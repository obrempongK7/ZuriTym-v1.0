<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Models\SpinHistory;
use App\Models\SpinReward;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SpinController extends Controller
{
    public function getWheelConfig(): JsonResponse
    {
        $segments = SpinReward::where('is_active', true)
                              ->orderBy('sort_order')
                              ->get()
                              ->map(fn($s) => [
                                  'id'          => $s->id,
                                  'label'       => $s->label,
                                  'points'      => $s->points,
                                  'type'        => $s->type,
                                  'color'       => $s->color,
                                  'probability' => $s->probability,
                              ]);

        return $this->success([
            'segments'    => $segments,
            'daily_limit' => (int) Setting::get('spin_daily_limit', 3),
        ]);
    }

    public function spin(Request $request): JsonResponse
    {
        $user       = $request->user();
        $dailyLimit = (int) Setting::get('spin_daily_limit', 3);

        if ($user->wallet->is_locked) {
            return $this->error('Your wallet is locked.', 403);
        }

        $todaySpins = SpinHistory::where('user_id', $user->id)
                                 ->whereDate('created_at', today())
                                 ->count();

        if ($todaySpins >= $dailyLimit) {
            return $this->error("You've used all {$dailyLimit} spins for today. Come back tomorrow!", 429);
        }

        // Weighted random selection
        $segments       = SpinReward::where('is_active', true)->get();
        $totalWeight    = $segments->sum('probability');
        $rand           = mt_rand(1, $totalWeight);
        $cumulative     = 0;
        $selectedReward = $segments->first();

        foreach ($segments as $segment) {
            $cumulative += $segment->probability;
            if ($rand <= $cumulative) {
                $selectedReward = $segment;
                break;
            }
        }

        $pointsWon = 0;
        DB::transaction(function () use ($user, $selectedReward, &$pointsWon) {
            SpinHistory::create([
                'user_id'       => $user->id,
                'spin_reward_id' => $selectedReward->id,
                'points_won'    => $selectedReward->points,
            ]);

            if ($selectedReward->type !== 'empty' && $selectedReward->points > 0) {
                $user->creditWallet(
                    $selectedReward->points,
                    'spin',
                    "Spin & Earn reward: {$selectedReward->label}"
                );
                $pointsWon = $selectedReward->points;
            }
        });

        $remaining = $dailyLimit - $todaySpins - 1;

        return $this->success([
            'reward'           => [
                'id'     => $selectedReward->id,
                'label'  => $selectedReward->label,
                'points' => $selectedReward->points,
                'type'   => $selectedReward->type,
                'color'  => $selectedReward->color,
            ],
            'points_won'       => $pointsWon,
            'spins_remaining'  => max(0, $remaining),
            'new_balance'      => $user->fresh()->wallet->balance,
        ], $pointsWon > 0 ? "🎉 You won {$pointsWon} points!" : "Better luck next time!");
    }

    public function history(Request $request): JsonResponse
    {
        $history = SpinHistory::where('user_id', $request->user()->id)
                              ->with('spinReward')
                              ->latest()
                              ->take(50)
                              ->get()
                              ->map(fn($h) => [
                                  'label'      => $h->spinReward->label,
                                  'points_won' => $h->points_won,
                                  'date'       => $h->created_at->toISOString(),
                              ]);

        return $this->success($history);
    }

    private function success($data, string $msg = 'OK', int $code = 200): JsonResponse
    { return response()->json(['success'=>true,'message'=>$msg,'data'=>$data],$code); }
    private function error(string $msg, int $code = 400): JsonResponse
    { return response()->json(['success'=>false,'message'=>$msg,'data'=>null],$code); }
}
