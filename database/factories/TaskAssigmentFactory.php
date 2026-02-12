<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Task;
use App\Models\Task_assigment;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Task_assigment>
 */
class TaskAssigmentFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<\Illuminate\Database\Eloquent\Model>
     */
    protected $model = Task_assigment::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::inRandomOrder()->first()->id ?? User::factory(),
            'task_id' => Task::inRandomOrder()->first()->id ?? Task::factory(),
            'assigned_at' => fake()->dateTimeBetween('-30 days', 'now'),
            'completed_at' => fake()->optional(0.4)->dateTimeBetween('-15 days', 'now'),
        ];
    }

    /**
     * Indicate that the task assignment is completed.
     */
    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'completed_at' => fake()->dateTimeBetween('-15 days', 'now'),
        ]);
    }

    /**
     * Indicate that the task assignment is not yet completed.
     */
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'completed_at' => null,
        ]);
    }

    /**
     * Indicate that the task assignment is for a specific user.
     */
    public function forUser(User $user): static
    {
        return $this->state(fn (array $attributes) => [
            'user_id' => $user->id,
        ]);
    }

    /**
     * Indicate that the task assignment is for a specific task.
     */
    public function forTask(Task $task): static
    {
        return $this->state(fn (array $attributes) => [
            'task_id' => $task->id,
        ]);
    }
}
