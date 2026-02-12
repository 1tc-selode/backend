@extends('layouts.admin')

@section('title', 'Task Assignment Details')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="mb-6 flex justify-between items-center">
        <h1 class="text-3xl font-bold text-gray-800">
            <i class="fas fa-user-check"></i> Task Assignment Details
        </h1>
        <a href="{{ route('admin.assignments.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
            <i class="fas fa-arrow-left"></i> Back to List
        </a>
    </div>

    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <div class="bg-blue-600 text-white px-6 py-4">
            <h2 class="text-2xl font-bold">Assignment #{{ $assignment->id }}</h2>
        </div>

        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h3 class="text-sm font-semibold text-gray-600 uppercase">ID</h3>
                    <p class="mt-1 text-lg text-gray-900">{{ $assignment->id }}</p>
                </div>

                <div>
                    <h3 class="text-sm font-semibold text-gray-600 uppercase">State</h3>
                    <p class="mt-1 text-lg">
                        @if($assignment->deleted_at)
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                                <i class="fas fa-trash mr-1"></i> Deleted
                            </span>
                        @else
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                <i class="fas fa-check-circle mr-1"></i> Active
                            </span>
                        @endif
                    </p>
                </div>

                <div class="md:col-span-2 bg-gray-50 p-4 rounded-lg">
                    <h3 class="text-sm font-semibold text-gray-600 uppercase mb-2">User Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-600">Name</p>
                            <p class="text-lg font-medium text-gray-900">{{ $assignment->user->name }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Email</p>
                            <p class="text-lg font-medium text-gray-900">{{ $assignment->user->email }}</p>
                        </div>
                        @if($assignment->user->department)
                        <div>
                            <p class="text-sm text-gray-600">Department</p>
                            <p class="text-lg font-medium text-gray-900">{{ $assignment->user->department }}</p>
                        </div>
                        @endif
                        <div>
                            <p class="text-sm text-gray-600">Role</p>
                            <p class="text-lg font-medium">
                                @if($assignment->user->is_admin)
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                        <i class="fas fa-user-shield mr-1"></i> Admin
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                        <i class="fas fa-user mr-1"></i> User
                                    </span>
                                @endif
                            </p>
                        </div>
                    </div>
                </div>

                <div class="md:col-span-2 bg-blue-50 p-4 rounded-lg">
                    <h3 class="text-sm font-semibold text-gray-600 uppercase mb-2">Task Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-600">Title</p>
                            <p class="text-lg font-medium text-gray-900">{{ $assignment->task->title }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Priority</p>
                            <p class="text-lg">
                                @if($assignment->task->priority === 'low')
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        <i class="fas fa-arrow-down mr-1"></i> Low
                                    </span>
                                @elseif($assignment->task->priority === 'medium')
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                        <i class="fas fa-minus mr-1"></i> Medium
                                    </span>
                                @elseif($assignment->task->priority === 'high')
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        <i class="fas fa-arrow-up mr-1"></i> High
                                    </span>
                                @endif
                            </p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Status</p>
                            <p class="text-lg">
                                @if($assignment->task->status === 'pending')
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                        <i class="fas fa-clock mr-1"></i> Pending
                                    </span>
                                @elseif($assignment->task->status === 'in-progress')
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        <i class="fas fa-spinner mr-1"></i> In Progress
                                    </span>
                                @elseif($assignment->task->status === 'completed')
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        <i class="fas fa-check mr-1"></i> Completed
                                    </span>
                                @endif
                            </p>
                        </div>
                        @if($assignment->task->due_date)
                        <div>
                            <p class="text-sm text-gray-600">Due Date</p>
                            <p class="text-lg font-medium text-gray-900">{{ $assignment->task->due_date }}</p>
                        </div>
                        @endif
                        @if($assignment->task->description)
                        <div class="md:col-span-2">
                            <p class="text-sm text-gray-600">Description</p>
                            <p class="text-base text-gray-900 mt-1">{{ $assignment->task->description }}</p>
                        </div>
                        @endif
                    </div>
                </div>

                <div>
                    <h3 class="text-sm font-semibold text-gray-600 uppercase">Assigned At</h3>
                    <p class="mt-1 text-lg text-gray-900">
                        {{ $assignment->assigned_at ? $assignment->assigned_at->format('Y-m-d H:i:s') : 'N/A' }}
                    </p>
                </div>

                <div>
                    <h3 class="text-sm font-semibold text-gray-600 uppercase">Completed At</h3>
                    <p class="mt-1 text-lg">
                        @if($assignment->completed_at)
                            <span class="text-green-600 font-medium">
                                <i class="fas fa-check-circle mr-1"></i> {{ $assignment->completed_at->format('Y-m-d H:i:s') }}
                            </span>
                        @else
                            <span class="text-gray-400">Not completed yet</span>
                        @endif
                    </p>
                </div>

                <div>
                    <h3 class="text-sm font-semibold text-gray-600 uppercase">Created At</h3>
                    <p class="mt-1 text-lg text-gray-900">{{ $assignment->created_at->format('Y-m-d H:i:s') }}</p>
                </div>

                <div>
                    <h3 class="text-sm font-semibold text-gray-600 uppercase">Last Updated</h3>
                    <p class="mt-1 text-lg text-gray-900">{{ $assignment->updated_at->format('Y-m-d H:i:s') }}</p>
                </div>

                @if($assignment->deleted_at)
                <div>
                    <h3 class="text-sm font-semibold text-gray-600 uppercase">Deleted At</h3>
                    <p class="mt-1 text-lg text-gray-900">{{ $assignment->deleted_at->format('Y-m-d H:i:s') }}</p>
                </div>
                @endif
            </div>
        </div>

        <div class="bg-gray-100 px-6 py-4 flex justify-end space-x-3">
            @if($assignment->deleted_at)
                <form action="{{ route('admin.assignments.restore', $assignment->id) }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                        <i class="fas fa-trash-restore"></i> Restore
                    </button>
                </form>
                <form action="{{ route('admin.assignments.force-delete', $assignment->id) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to permanently delete this assignment? This action cannot be undone!')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="bg-red-700 hover:bg-red-800 text-white font-bold py-2 px-4 rounded">
                        <i class="fas fa-trash-alt"></i> Delete Permanently
                    </button>
                </form>
            @else
                <a href="{{ route('admin.assignments.edit', $assignment->id) }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    <i class="fas fa-edit"></i> Edit
                </a>
                <form action="{{ route('admin.assignments.destroy', $assignment->id) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this assignment?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                        <i class="fas fa-trash"></i> Delete
                    </button>
                </form>
            @endif
        </div>
    </div>
</div>
@endsection
