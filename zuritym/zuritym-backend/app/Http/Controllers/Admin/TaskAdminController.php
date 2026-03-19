<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Task;
use Illuminate\Http\Request;

class TaskAdminController extends Controller {
    public function index() {
        $tasks = Task::withTrashed()->orderBy('sort_order')->paginate(20);
        return view('admin.tasks.index', compact('tasks'));
    }
    public function create() { return view('admin.tasks.form', ['task'=>new Task()]); }
    public function store(Request $request) {
        $request->validate([
            'title'=>'required|string|max:200',
            'type'=>'required|string',
            'reward_points'=>'required|numeric|min:0',
            'action_url'=>'nullable|url',
            'timer_seconds'=>'nullable|integer|min:0',
            'daily_limit'=>'nullable|integer|min:1',
            'sort_order'=>'nullable|integer',
        ]);
        $data = $request->except(['_token','icon','banner']);
        $data['is_active'] = $request->boolean('is_active');
        $data['requires_screenshot'] = $request->boolean('requires_screenshot');
        $data['is_verified'] = $request->boolean('is_verified');
        if ($request->hasFile('icon')) $data['icon'] = $request->file('icon')->store('tasks','public');
        Task::create($data);
        return redirect()->route('admin.tasks.index')->with('success','Task created!');
    }
    public function edit(Task $task) { return view('admin.tasks.form', compact('task')); }
    public function update(Request $request, Task $task) {
        $data = $request->except(['_token','_method','icon','banner']);
        $data['is_active'] = $request->boolean('is_active');
        $data['requires_screenshot'] = $request->boolean('requires_screenshot');
        $data['is_verified'] = $request->boolean('is_verified');
        if ($request->hasFile('icon')) $data['icon'] = $request->file('icon')->store('tasks','public');
        $task->update($data);
        return redirect()->route('admin.tasks.index')->with('success','Task updated!');
    }
    public function destroy(Task $task) { $task->delete(); return back()->with('success','Task deleted.'); }
}
