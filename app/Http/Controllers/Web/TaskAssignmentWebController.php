<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Task_assigment;
use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;

class TaskAssignmentWebController extends Controller
{
    public function index()
    {
        $assignments = Task_assigment::withTrashed()
            ->with(['user', 'task'])
            ->paginate(15);
        return view('admin.assignments.index', compact('assignments'));
    }

    public function create()
    {
        $users = User::where('is_admin', false)->get();
        $tasks = Task::all();
        return view('admin.assignments.create', compact('users', 'tasks'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'task_id' => 'required|exists:tasks,id',
            'assigned_at' => 'nullable|date',
            'completed_at' => 'nullable|date|after_or_equal:assigned_at'
        ]);

        if (!isset($validated['assigned_at'])) {
            $validated['assigned_at'] = now();
        }

        Task_assigment::create($validated);

        return redirect()->route('admin.assignments.index')
            ->with('success', 'Assignment created successfully!');
    }

    public function show(string $id)
    {
        $assignment = Task_assigment::withTrashed()
            ->with(['user', 'task'])
            ->findOrFail($id);
        return view('admin.assignments.show', compact('assignment'));
    }

    public function edit(string $id)
    {
        $assignment = Task_assigment::withTrashed()->findOrFail($id);
        $users = User::where('is_admin', false)->get();
        $tasks = Task::all();
        return view('admin.assignments.edit', compact('assignment', 'users', 'tasks'));
    }

    public function update(Request $request, string $id)
    {
        $assignment = Task_assigment::withTrashed()->findOrFail($id);

        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'task_id' => 'required|exists:tasks,id',
            'assigned_at' => 'nullable|date',
            'completed_at' => 'nullable|date|after_or_equal:assigned_at'
        ]);

        $assignment->update($validated);

        return redirect()->route('admin.assignments.index')
            ->with('success', 'Assignment updated successfully!');
    }

    public function destroy(string $id)
    {
        $assignment = Task_assigment::findOrFail($id);
        $assignment->delete();

        return redirect()->route('admin.assignments.index')
            ->with('success', 'Assignment deleted successfully!');
    }

    public function restore(string $id)
    {
        $assignment = Task_assigment::withTrashed()->findOrFail($id);
        $assignment->restore();

        return redirect()->route('admin.assignments.index')
            ->with('success', 'Assignment restored successfully!');
    }

    public function forceDelete(string $id)
    {
        $assignment = Task_assigment::withTrashed()->findOrFail($id);
        $assignment->forceDelete();

        return redirect()->route('admin.assignments.index')
            ->with('success', 'Assignment permanently deleted!');
    }
}
