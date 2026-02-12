<?php

use App\Http\Controllers\Web\AuthWebController;
use App\Http\Controllers\Web\UserWebController;
use App\Http\Controllers\Web\TaskWebController;
use App\Http\Controllers\Web\TaskAssignmentWebController;
use Illuminate\Support\Facades\Route;

// Redirect root to login
Route::get('/', function () {
    return redirect()->route('login');
});

// Authentication Routes
Route::get('login', [AuthWebController::class, 'showLoginForm'])->name('login');
Route::post('login', [AuthWebController::class, 'login'])->name('login.post');
Route::post('logout', [AuthWebController::class, 'logout'])->name('logout');

// Admin Web Routes - Protected by auth and admin check
Route::prefix('admin')->name('admin.')->middleware(['auth', 'web'])->group(function () {
    
    // Users Management
    Route::resource('users', UserWebController::class);
    Route::post('users/{id}/restore', [UserWebController::class, 'restore'])->name('users.restore');
    Route::delete('users/{id}/force', [UserWebController::class, 'forceDelete'])->name('users.force-delete');
    
    // Tasks Management
    Route::resource('tasks', TaskWebController::class);
    Route::post('tasks/{id}/restore', [TaskWebController::class, 'restore'])->name('tasks.restore');
    Route::delete('tasks/{id}/force', [TaskWebController::class, 'forceDelete'])->name('tasks.force-delete');
    
    // Task Assignments Management
    Route::resource('assignments', TaskAssignmentWebController::class);
    Route::post('assignments/{id}/restore', [TaskAssignmentWebController::class, 'restore'])->name('assignments.restore');
    Route::delete('assignments/{id}/force', [TaskAssignmentWebController::class, 'forceDelete'])->name('assignments.force-delete');
});
