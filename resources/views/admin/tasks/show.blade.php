@extends('layouts.admin')

@section('title', 'Task Details')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="mb-6 flex justify-between items-center">
        <h1 class="text-3xl font-bold text-gray-800">
            <i class="fas fa-tasks"></i> Task Details
        </h1>
        <a href="{{ route('admin.tasks.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
            <i class="fas fa-arrow-left"></i> Back to List
        </a>
    </div>

    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <div class="bg-blue-600 text-white px-6 py-4">
            <h2 class="text-2xl font-bold">{{ $task->title }}</h2>
        </div>

        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h3 class="text-sm font-semibold text-gray-600 uppercase">ID</h3>
                    <p class="mt-1 text-lg text-gray-900">{{ $task->id }}</p>
                </div>

                <div>
                    <h3 class="text-sm font-semibold text-gray-600 uppercase">Priority</h3>
                    <p class="mt-1 text-lg">
                        @if($task->priority === 'low')
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                <i class="fas fa-arrow-down mr-1"></i> Low
                            </span>
                        @elseif($task->priority === 'medium')
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                                <i class="fas fa-minus mr-1"></i> Medium
                            </span>
                        @elseif($task->priority === 'high')
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                                <i class="fas fa-arrow-up mr-1"></i> High
                            </span>
                        @endif
                    </p>
                </div>

                <div>
                    <h3 class="text-sm font-semibold text-gray-600 uppercase">Status</h3>
                    <p class="mt-1 text-lg">
                        @if($task->status === 'pending')
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800">
                                <i class="fas fa-clock mr-1"></i> Pending
                            </span>
                        @elseif($task->status === 'in-progress')
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                                <i class="fas fa-spinner mr-1"></i> In Progress
                            </span>
                        @elseif($task->status === 'completed')
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                <i class="fas fa-check mr-1"></i> Completed
                            </span>
                        @endif
                    </p>
                </div>

                <div>
                    <h3 class="text-sm font-semibold text-gray-600 uppercase">State</h3>
                    <p class="mt-1 text-lg">
                        @if($task->deleted_at)
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

                <div class="md:col-span-2">
                    <h3 class="text-sm font-semibold text-gray-600 uppercase">Description</h3>
                    <p class="mt-1 text-lg text-gray-900">{{ $task->description ?? 'No description provided' }}</p>
                </div>

                <div>
                    <h3 class="text-sm font-semibold text-gray-600 uppercase">Due Date</h3>
                    <p class="mt-1 text-lg text-gray-900">{{ $task->due_date ?? 'N/A' }}</p>
                </div>

                <div>
                    <h3 class="text-sm font-semibold text-gray-600 uppercase">Created At</h3>
                    <p class="mt-1 text-lg text-gray-900">{{ $task->created_at->format('Y-m-d H:i:s') }}</p>
                </div>

                <div>
                    <h3 class="text-sm font-semibold text-gray-600 uppercase">Last Updated</h3>
                    <p class="mt-1 text-lg text-gray-900">{{ $task->updated_at->format('Y-m-d H:i:s') }}</p>
                </div>

                @if($task->deleted_at)
                <div>
                    <h3 class="text-sm font-semibold text-gray-600 uppercase">Deleted At</h3>
                    <p class="mt-1 text-lg text-gray-900">{{ $task->deleted_at->format('Y-m-d H:i:s') }}</p>
                </div>
                @endif
            </div>
        </div>

        <div class="bg-gray-100 px-6 py-4 flex justify-end space-x-3">
            @if($task->deleted_at)
                <form action="{{ route('admin.tasks.restore', $task->id) }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                        <i class="fas fa-trash-restore"></i> Restore
                    </button>
                </form>
                <form action="{{ route('admin.tasks.force-delete', $task->id) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to permanently delete this task? This action cannot be undone!')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="bg-red-700 hover:bg-red-800 text-white font-bold py-2 px-4 rounded">
                        <i class="fas fa-trash-alt"></i> Delete Permanently
                    </button>
                </form>
            @else
                <a href="{{ route('admin.tasks.edit', $task->id) }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    <i class="fas fa-edit"></i> Edit
                </a>
                <form action="{{ route('admin.tasks.destroy', $task->id) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this task?')">
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
