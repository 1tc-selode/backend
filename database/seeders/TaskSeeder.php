<?php

namespace Database\Seeders;

use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TaskSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create 10 tasks with different statuses
        Task::factory()->pending()->create();
        Task::factory()->pending()->create();
        Task::factory()->inProgress()->create();
        Task::factory()->inProgress()->create();
        Task::factory()->inProgress()->highPriority()->create();
        Task::factory()->completed()->create();
        Task::factory()->completed()->create();
        Task::factory()->completed()->create();
        Task::factory()->cancelled()->create();
        Task::factory()->pending()->highPriority()->create();
    }
}
