<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Task;
use Illuminate\Http\Request;

class TaskWebController extends Controller
{
    public function index()
    {
        $tasks = Task::withTrashed()->paginate(15);
        return view('admin.tasks.index', compact('tasks'));
    }

    public function create()
    {
        return view('admin.tasks.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'priority' => 'required|in:low,medium,high',
            'due_date' => 'nullable|date',
            'status' => 'required|in:pending,in_progress,completed,cancelled'
        ]);

        Task::create($validated);

        return redirect()->route('admin.tasks.index')
            ->with('success', 'Task created successfully!');
    }

    public function show(string $id)
    {
        $task = Task::withTrashed()->with(['taskAssignments.user'])->findOrFail($id);
        return view('admin.tasks.show', compact('task'));
    }

    public function edit(string $id)
    {
        $task = Task::withTrashed()->findOrFail($id);
        return view('admin.tasks.edit', compact('task'));
    }

    public function update(Request $request, string $id)
    {
        $task = Task::withTrashed()->findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'priority' => 'required|in:low,medium,high',
            'due_date' => 'nullable|date',
            'status' => 'required|in:pending,in_progress,completed,cancelled'
        ]);

        $task->update($validated);

        return redirect()->route('admin.tasks.index')
            ->with('success', 'Task updated successfully!');
    }

    public function destroy(string $id)
    {
        $task = Task::findOrFail($id);
        $task->delete();

        return redirect()->route('admin.tasks.index')
            ->with('success', 'Task deleted successfully!');
    }

    public function restore(string $id)
    {
        $task = Task::withTrashed()->findOrFail($id);
        $task->restore();

        return redirect()->route('admin.tasks.index')
            ->with('success', 'Task restored successfully!');
    }

    public function forceDelete(string $id)
    {
        $task = Task::withTrashed()->findOrFail($id);
        $task->forceDelete();

        return redirect()->route('admin.tasks.index')
            ->with('success', 'Task permanently deleted!');
    }
}
