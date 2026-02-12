<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Task extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'description',
        'priority',
        'due_date',
        'status',
    ];

    protected $casts = [
        'due_date' => 'date',
        'deleted_at' => 'datetime',
    ];

    /**
     * Get the task assignments for the task.
     */
    public function taskAssignments(): HasMany
    {
        return $this->hasMany(Task_assigment::class);
    }

    /**
     * Get the users assigned to this task.
     */
    public function users()
    {
        return $this->hasManyThrough(
            User::class,
            Task_assigment::class,
            'task_id',
            'id',
            'id',
            'user_id'
        );
    }
}
