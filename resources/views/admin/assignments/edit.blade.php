@extends('layouts.admin')

@section('title', 'Edit Task Assignment')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-800">
            <i class="fas fa-edit"></i> Edit Task Assignment #{{ $assignment->id }}
        </h1>
    </div>

    <div class="bg-white shadow-md rounded-lg p-6">
        <form action="{{ route('admin.assignments.update', $assignment->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-4">
                <label for="user_id" class="block text-gray-700 text-sm font-bold mb-2">User *</label>
                <select name="user_id" id="user_id" required
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('user_id') border-red-500 @enderror">
                    <option value="">Select User</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}" {{ old('user_id', $assignment->user_id) == $user->id ? 'selected' : '' }}>
                            {{ $user->name }} ({{ $user->email }})
                        </option>
                    @endforeach
                </select>
                @error('user_id')
                    <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="task_id" class="block text-gray-700 text-sm font-bold mb-2">Task *</label>
                <select name="task_id" id="task_id" required
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('task_id') border-red-500 @enderror">
                    <option value="">Select Task</option>
                    @foreach($tasks as $task)
                        <option value="{{ $task->id }}" {{ old('task_id', $assignment->task_id) == $task->id ? 'selected' : '' }}>
                            {{ $task->title }} (Priority: {{ ucfirst($task->priority) }})
                        </option>
                    @endforeach
                </select>
                @error('task_id')
                    <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="assigned_at" class="block text-gray-700 text-sm font-bold mb-2">Assigned At</label>
                <input type="datetime-local" name="assigned_at" id="assigned_at" 
                    value="{{ old('assigned_at', $assignment->assigned_at ? $assignment->assigned_at->format('Y-m-d\TH:i') : '') }}"
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            </div>

            <div class="mb-6">
                <label for="completed_at" class="block text-gray-700 text-sm font-bold mb-2">Completed At (Optional)</label>
                <input type="datetime-local" name="completed_at" id="completed_at" 
                    value="{{ old('completed_at', $assignment->completed_at ? $assignment->completed_at->format('Y-m-d\TH:i') : '') }}"
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                <p class="text-gray-500 text-xs mt-1">Leave empty if task is not completed yet</p>
            </div>

            <div class="flex items-center justify-between">
                <a href="{{ route('admin.assignments.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                    <i class="fas fa-arrow-left"></i> Cancel
                </a>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                    <i class="fas fa-save"></i> Update Assignment
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
