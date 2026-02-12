@extends('layouts.admin')

@section('title', 'Task Assignments')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <h1 class="text-3xl font-bold text-gray-800">
        <i class="fas fa-user-check"></i> Task Assignments
    </h1>
    <a href="{{ route('admin.assignments.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
        <i class="fas fa-plus"></i> Create Assignment
    </a>
</div>

@if($assignments->isEmpty())
    <div class="bg-white shadow-md rounded-lg p-6 text-center">
        <i class="fas fa-inbox text-gray-400 text-5xl mb-4"></i>
        <p class="text-gray-600 text-lg">No task assignments found.</p>
        <a href="{{ route('admin.assignments.create') }}" class="mt-4 inline-block bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
            <i class="fas fa-plus"></i> Create First Assignment
        </a>
    </div>
@else
    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Task</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Assigned At</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Completed At</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">State</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($assignments as $assignment)
                    <tr class="{{ $assignment->deleted_at ? 'bg-red-50' : '' }}">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $assignment->id }}</td>
                        <td class="px-6 py-4 text-sm text-gray-900">
                            <div class="font-medium">{{ $assignment->user->name }}</div>
                            <div class="text-gray-500 text-xs">{{ $assignment->user->email }}</div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-900">
                            <div class="font-medium">{{ $assignment->task->title }}</div>
                            @if($assignment->task->priority)
                                @if($assignment->task->priority === 'low')
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 mt-1">
                                        Low
                                    </span>
                                @elseif($assignment->task->priority === 'medium')
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800 mt-1">
                                        Medium
                                    </span>
                                @elseif($assignment->task->priority === 'high')
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800 mt-1">
                                        High
                                    </span>
                                @endif
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $assignment->assigned_at ? $assignment->assigned_at->format('Y-m-d H:i') : 'N/A' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            @if($assignment->completed_at)
                                <span class="text-green-600">
                                    <i class="fas fa-check-circle"></i> {{ $assignment->completed_at->format('Y-m-d H:i') }}
                                </span>
                            @else
                                <span class="text-gray-400">Not completed</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($assignment->deleted_at)
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                    <i class="fas fa-trash mr-1"></i> Deleted
                                </span>
                            @else
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                    <i class="fas fa-check-circle mr-1"></i> Active
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <a href="{{ route('admin.assignments.show', $assignment->id) }}" class="text-blue-600 hover:text-blue-900 mr-3">
                                <i class="fas fa-eye"></i>
                            </a>
                            @if($assignment->deleted_at)
                                <form action="{{ route('admin.assignments.restore', $assignment->id) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="text-green-600 hover:text-green-900 mr-3">
                                        <i class="fas fa-trash-restore"></i>
                                    </button>
                                </form>
                                <form action="{{ route('admin.assignments.force-delete', $assignment->id) }}" method="POST" class="inline" onsubmit="return confirm('Permanently delete this assignment?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-700 hover:text-red-900">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                            @else
                                <a href="{{ route('admin.assignments.edit', $assignment->id) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.assignments.destroy', $assignment->id) }}" method="POST" class="inline" onsubmit="return confirm('Delete this assignment?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @if($assignments->hasPages())
        <div class="mt-4">
            {{ $assignments->links() }}
        </div>
    @endif
@endif
@endsection
