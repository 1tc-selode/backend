<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Get authenticated user profile
     */
    public function profile(Request $request)
    {
        $user = $request->user()->load('taskAssignments.task');
        return response()->json($user);
    }

    /**
     * Display a listing of users (Admin only)
     */
    public function index()
    {
        $users = User::withCount('taskAssignments')->paginate(15);
        return response()->json($users);
    }

    /**
     * Store a newly created user (Admin only)
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'department' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:255',
            'is_admin' => 'sometimes|boolean',
        ]);

        $validated['password'] = Hash::make($validated['password']);

        $user = User::create($validated);

        return response()->json([
            'message' => 'User created successfully',
            'user' => $user,
        ], 201);
    }

    /**
     * Display the specified user (Admin only)
     */
    public function show(string $id)
    {
        $user = User::with('taskAssignments.task')->findOrFail($id);
        return response()->json($user);
    }

    /**
     * Update the specified user (Admin only)
     */
    public function update(Request $request, string $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|string|email|max:255|unique:users,email,' . $id,
            'password' => 'sometimes|string|min:8',
            'department' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:255',
            'is_admin' => 'sometimes|boolean',
        ]);

        if (isset($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        }

        $user->update($validated);

        return response()->json([
            'message' => 'User updated successfully',
            'user' => $user,
        ]);
    }

    /**
     * Soft delete the specified user (Admin only)
     */
    public function destroy(Request $request, string $id)
    {
        $user = User::findOrFail($id);
        
        // Prevent self-deletion
        if ($user->id === $request->user()->id) {
            return response()->json([
                'message' => 'You cannot delete yourself',
            ], 403);
        }

        $user->delete();

        return response()->json([
            'message' => 'User deleted successfully',
        ]);
    }

    /**
     * Force delete the specified user (Admin only)
     */
    public function forceDelete(Request $request, $id)
    {
        $user = User::withTrashed()->findOrFail($id);
        
        if ($user->id === $request->user()->id) {
            return response()->json([
                'message' => 'You cannot delete yourself',
            ], 403);
        }

        $user->forceDelete();

        return response()->json([
            'message' => 'User permanently deleted',
        ]);
    }

    /**
     * Restore a soft deleted user (Admin only)
     */
    public function restore($id)
    {
        $user = User::withTrashed()->findOrFail($id);
        $user->restore();

        return response()->json([
            'message' => 'User restored successfully',
            'user' => $user,
        ]);
    }
}
