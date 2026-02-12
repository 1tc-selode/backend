<?php

namespace Database\Seeders;

use App\Models\Task_assigment;
use App\Models\User;
use App\Models\Task;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TaskAssignmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();
        $tasks = Task::all();

        // Assign tasks to users - each task gets 1-3 users assigned
        foreach ($tasks as $task) {
            $numberOfAssignments = rand(1, 3);
            $assignedUsers = $users->random($numberOfAssignments);

            foreach ($assignedUsers as $user) {
                // Create task assignment
                $assignment = Task_assigment::factory()
                    ->forUser($user)
                    ->forTask($task)
                    ->create();

                // If task is completed, mark assignment as completed
                if ($task->status === 'completed') {
                    $assignment->update([
                        'completed_at' => fake()->dateTimeBetween($assignment->assigned_at, 'now')
                    ]);
                }
            }
        }
    }
}
