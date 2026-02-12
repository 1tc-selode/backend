<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Task_assigment;
use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;

class TaskAssignmentController extends Controller
{
    /**
     * Display a listing of task assignments (Admin only)
     */
    public function index()
    {
        $assignments = Task_assigment::with(['user', 'task'])->paginate(15);
        return response()->json($assignments);
    }

    /**
     * Store a newly created task assignment (Admin only)
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'task_id' => 'required|exists:tasks,id',
            'assigned_at' => 'sometimes|date',
        ]);

        $validated['assigned_at'] = $validated['assigned_at'] ?? now();

        $assignment = Task_assigment::create($validated);

        return response()->json([
            'message' => 'Task assigned successfully',
            'assignment' => $assignment->load(['user', 'task']),
        ], 201);
    }

    /**
     * Display the specified task assignment
     */
    public function show(string $id)
    {
        $assignment = Task_assigment::with(['user', 'task'])->findOrFail($id);
        return response()->json($assignment);
    }

    /**
     * Update the specified task assignment (Admin only)
     */
    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'user_id' => 'sometimes|exists:users,id',
            'task_id' => 'sometimes|exists:tasks,id',
            'assigned_at' => 'sometimes|date',
            'completed_at' => 'nullable|date',
        ]);

        $assignment = Task_assigment::findOrFail($id);
        $assignment->update($validated);

        return response()->json([
            'message' => 'Task assignment updated successfully',
            'assignment' => $assignment->load(['user', 'task']),
        ]);
    }

    /**
     * Remove the specified task assignment (Admin only)
     */
    public function destroy(string $id)
    {
        $assignment = Task_assigment::findOrFail($id);
        $assignment->delete();

        return response()->json([
            'message' => 'Task assignment deleted successfully',
        ]);
    }

    /**
     * Assign a task to a user (Admin only)
     */
    public function assignToUser(Request $request, $taskId)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $task = Task::findOrFail($taskId);
        $user = User::findOrFail($validated['user_id']);

        // Check if already assigned
        $exists = Task_assigment::where('task_id', $taskId)
            ->where('user_id', $validated['user_id'])
            ->exists();

        if ($exists) {
            return response()->json([
                'message' => 'User is already assigned to this task',
            ], 422);
        }

        $assignment = Task_assigment::create([
            'task_id' => $taskId,
            'user_id' => $validated['user_id'],
            'assigned_at' => now(),
        ]);

        return response()->json([
            'message' => 'User assigned to task successfully',
            'assignment' => $assignment->load(['user', 'task']),
        ], 201);
    }

    /**
     * Unassign a task from a user (Admin only)
     */
    public function unassignFromUser($taskId, $userId)
    {
        $assignment = Task_assigment::where('task_id', $taskId)
            ->where('user_id', $userId)
            ->firstOrFail();

        $assignment->delete();

        return response()->json([
            'message' => 'User unassigned from task successfully',
        ]);
    }

    /**
     * Get all assignments for a specific task (Admin only)
     */
    public function byTask($taskId)
    {
        $task = Task::findOrFail($taskId);
        $assignments = Task_assigment::where('task_id', $taskId)
            ->with('user')
            ->get();

        return response()->json([
            'task' => $task,
            'assignments' => $assignments,
        ]);
    }

    /**
     * Get all assignments for a specific user (Admin only)
     */
    public function byUser($userId)
    {
        $user = User::findOrFail($userId);
        $assignments = Task_assigment::where('user_id', $userId)
            ->with('task')
            ->get();

        return response()->json([
            'user' => $user,
            'assignments' => $assignments,
        ]);
    }
}
