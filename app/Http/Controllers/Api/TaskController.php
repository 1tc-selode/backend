<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    /**
     * Display a listing of all tasks (Admin only)
     */
    public function index()
    {
        $tasks = Task::with(['taskAssignments.user'])->paginate(15);
        return response()->json($tasks);
    }

    /**
     * Get authenticated user's tasks
     */
    public function myTasks(Request $request)
    {
        $user = $request->user();
        
        $tasks = Task::whereHas('taskAssignments', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })->with(['taskAssignments' => function ($query) use ($user) {
            $query->where('user_id', $user->id);
        }])->get();

        return response()->json($tasks);
    }

    /**
     * Update task status (User can update their own task status)
     */
    public function updateStatus(Request $request, $id)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,in_progress,completed,cancelled',
        ]);

        $user = $request->user();
        
        $task = Task::findOrFail($id);
        
        // Check if user is assigned to this task or is admin
        if (!$user->is_admin) {
            $isAssigned = $task->taskAssignments()
                ->where('user_id', $user->id)
                ->exists();
                
            if (!$isAssigned) {
                return response()->json([
                    'message' => 'You are not assigned to this task'
                ], 403);
            }
        }

        $task->update(['status' => $validated['status']]);

        // If completed, update assignment completed_at
        if ($validated['status'] === 'completed') {
            $task->taskAssignments()
                ->where('user_id', $user->id)
                ->update(['completed_at' => now()]);
        }

        return response()->json([
            'message' => 'Task status updated successfully',
            'task' => $task,
        ]);
    }

    /**
     * Store a newly created task (Admin only)
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'priority' => 'required|in:low,medium,high',
            'due_date' => 'nullable|date',
            'status' => 'sometimes|in:pending,in_progress,completed,cancelled',
        ]);

        $task = Task::create($validated);

        return response()->json([
            'message' => 'Task created successfully',
            'task' => $task,
        ], 201);
    }

    /**
     * Display the specified task
     */
    public function show(string $id)
    {
        $task = Task::with(['taskAssignments.user'])->findOrFail($id);
        return response()->json($task);
    }

    /**
     * Update the specified task (Admin only)
     */
    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'priority' => 'sometimes|in:low,medium,high',
            'due_date' => 'nullable|date',
            'status' => 'sometimes|in:pending,in_progress,completed,cancelled',
        ]);

        $task = Task::findOrFail($id);
        $task->update($validated);

        return response()->json([
            'message' => 'Task updated successfully',
            'task' => $task,
        ]);
    }

    /**
     * Soft delete the specified task (Admin only)
     */
    public function destroy(string $id)
    {
        $task = Task::findOrFail($id);
        $task->delete();

        return response()->json([
            'message' => 'Task deleted successfully',
        ]);
    }

    /**
     * Force delete the specified task (Admin only)
     */
    public function forceDelete($id)
    {
        $task = Task::withTrashed()->findOrFail($id);
        $task->forceDelete();

        return response()->json([
            'message' => 'Task permanently deleted',
        ]);
    }

    /**
     * Restore a soft deleted task (Admin only)
     */
    public function restore($id)
    {
        $task = Task::withTrashed()->findOrFail($id);
        $task->restore();

        return response()->json([
            'message' => 'Task restored successfully',
            'task' => $task,
        ]);
    }
}
