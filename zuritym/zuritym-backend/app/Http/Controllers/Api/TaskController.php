<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\UserTask;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class TaskController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $user  = $request->user();
        $tasks = Task::where('is_active', true)
                     ->when($request->type, fn($q) => $q->where('type', $request->type))
                     ->orderBy('sort_order')
                     ->get()
                     ->map(fn($task) => $this->taskResource($task, $user));

        return $this->success($tasks);
    }

    public function show(Request $request, Task $task): JsonResponse
    {
        if (!$task->is_active) return $this->error('Task not available.', 404);
        return $this->success($this->taskResource($task, $request->user()));
    }

    public function start(Request $request, Task $task): JsonResponse
    {
        $user = $request->user();
        if (!$task->is_active) return $this->error('Task not available.', 404);

        $todayCount = UserTask::where('user_id', $user->id)
                              ->where('task_id', $task->id)
                              ->whereDate('created_at', today())
                              ->count();

        if ($todayCount >= $task->daily_limit) {
            return $this->error("You've reached today's limit for this task.", 429);
        }

        $totalCount = $task->completionsByUser($user->id);
        if ($task->completion_limit && $totalCount >= $task->completion_limit) {
            return $this->error('You have already completed this task the maximum number of times.', 409);
        }

        $userTask = UserTask::create([
            'user_id'    => $user->id,
            'task_id'    => $task->id,
            'status'     => 'pending',
            'ip_address' => $request->ip(),
            'device_id'  => $user->device_id,
        ]);

        return $this->success([
            'user_task_id' => $userTask->id,
            'timer'        => $task->timer_seconds,
            'action_url'   => $task->action_url,
        ], 'Task started!');
    }

    public function complete(Request $request, int $userTaskId): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'screenshot' => 'nullable|image|max:5120',
            'proof_url'  => 'nullable|url',
        ]);
        if ($validator->fails()) return $this->error($validator->errors()->first(), 422);

        $user     = $request->user();
        $userTask = UserTask::where('id', $userTaskId)
                            ->where('user_id', $user->id)
                            ->where('status', 'pending')
                            ->with('task')
                            ->firstOrFail();

        $task = $userTask->task;

        if ($task->requires_screenshot && !$request->hasFile('screenshot') && !$request->proof_url) {
            return $this->error('Screenshot or proof is required for this task.', 422);
        }

        DB::transaction(function () use ($user, $userTask, $task, $request) {
            $screenshotPath = null;
            if ($request->hasFile('screenshot')) {
                $screenshotPath = $request->file('screenshot')->store('task_screenshots', 'public');
            }

            $status = $task->is_verified ? 'pending' : 'completed';
            $pointsEarned = $status === 'completed' ? $task->reward_points : 0;

            $userTask->update([
                'status'        => $status,
                'earned_points' => $pointsEarned,
                'screenshot'    => $screenshotPath ? basename($screenshotPath) : null,
                'proof_url'     => $request->proof_url,
            ]);

            if ($status === 'completed') {
                $user->creditWallet($task->reward_points, 'task', "Task completed: {$task->title}");
                $task->increment('completion_count');
            }
        });

        $msg = $task->is_verified
            ? 'Task submitted for review. You will be rewarded once verified.'
            : "Task completed! You earned {$task->reward_points} points.";

        return $this->success([
            'status'        => $task->is_verified ? 'pending' : 'completed',
            'points_earned' => $task->is_verified ? 0 : $task->reward_points,
            'new_balance'   => $user->fresh()->wallet->balance,
        ], $msg);
    }

    private function taskResource(Task $task, $user): array
    {
        $todayCompleted = UserTask::where('user_id', $user->id)
                                  ->where('task_id', $task->id)
                                  ->whereDate('created_at', today())
                                  ->where('status', 'completed')
                                  ->count();
        return [
            'id'                 => $task->id,
            'title'              => $task->title,
            'description'        => $task->description,
            'type'               => $task->type,
            'reward_points'      => $task->reward_points,
            'action_url'         => $task->action_url,
            'timer_seconds'      => $task->timer_seconds,
            'daily_limit'        => $task->daily_limit,
            'today_completed'    => $todayCompleted,
            'is_completable'     => $todayCompleted < $task->daily_limit,
            'requires_screenshot' => $task->requires_screenshot,
            'icon_url'           => $task->icon ? asset('storage/tasks/' . $task->icon) : null,
            'banner_url'         => $task->banner ? asset('storage/tasks/' . $task->banner) : null,
        ];
    }

    private function success($data, string $msg = 'OK', int $code = 200): JsonResponse
    { return response()->json(['success'=>true,'message'=>$msg,'data'=>$data],$code); }
    private function error(string $msg, int $code = 400): JsonResponse
    { return response()->json(['success'=>false,'message'=>$msg,'data'=>null],$code); }
}
