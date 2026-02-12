<?php

namespace Tests\Feature;

use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class TaskApiTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function admin_can_view_all_tasks()
    {
        // Létrehozunk egy admin usert és task-okat
        $admin = User::factory()->create(['is_admin' => true]);
        Task::factory()->count(5)->create();

        // Admin userként autentikálunk
        Sanctum::actingAs($admin);

        // Kérés az összes task listázására
        $response = $this->getJson('/api/admin/tasks');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'title', 'description', 'priority', 'status', 'due_date']
                ]
            ]);
    }

    /** @test */
    public function non_admin_cannot_view_all_tasks()
    {
        // Létrehozunk egy sima usert
        $user = User::factory()->create(['is_admin' => false]);
        Task::factory()->count(3)->create();

        // Sima userként autentikálunk
        Sanctum::actingAs($user);

        // Megpróbáljuk elérni az admin endpointot
        $response = $this->getJson('/api/admin/tasks');

        // 403 Forbidden választ várunk
        $response->assertStatus(403)
            ->assertJson([
                'message' => 'Unauthorized. Admin access required.'
            ]);
    }

    /** @test */
    public function admin_can_create_task()
    {
        $admin = User::factory()->create(['is_admin' => true]);
        Sanctum::actingAs($admin);

        $taskData = [
            'title' => 'New Test Task',
            'description' => 'This is a test task description',
            'priority' => 'high',
            'status' => 'pending',
            'due_date' => '2026-03-15'
        ];

        $response = $this->postJson('/api/admin/tasks', $taskData);

        $response->assertStatus(201)
            ->assertJsonPath('task.title', 'New Test Task')
            ->assertJsonPath('task.priority', 'high');

        // Ellenőrizzük, hogy tényleg létrejött az adatbázisban
        $this->assertDatabaseHas('tasks', [
            'title' => 'New Test Task',
            'priority' => 'high'
        ]);
    }

    /** @test */
    public function non_admin_cannot_create_task()
    {
        $user = User::factory()->create(['is_admin' => false]);
        Sanctum::actingAs($user);

        $taskData = [
            'title' => 'Unauthorized Task',
            'description' => 'This should not be created',
            'priority' => 'low',
            'status' => 'pending',
            'due_date' => '2026-03-15'
        ];

        $response = $this->postJson('/api/admin/tasks', $taskData);

        $response->assertStatus(403);

        // Ellenőrizzük, hogy nem jött létre az adatbázisban
        $this->assertDatabaseMissing('tasks', [
            'title' => 'Unauthorized Task'
        ]);
    }

    /** @test */
    public function user_can_view_their_own_tasks()
    {
        $user = User::factory()->create(['is_admin' => false]);
        
        // Létrehozunk néhány task-ot és hozzárendeljük a userhez
        $myTask1 = Task::factory()->create(['title' => 'My First Task']);
        $myTask2 = Task::factory()->create(['title' => 'My Second Task']);
        
        $user->taskAssignments()->createMany([
            ['task_id' => $myTask1->id, 'assigned_at' => now()],
            ['task_id' => $myTask2->id, 'assigned_at' => now()],
        ]);

        // Létrehozunk egy task-ot, ami nincs hozzárendelve
        Task::factory()->create(['title' => 'Someone Elses Task']);

        Sanctum::actingAs($user);

        $response = $this->getJson('/api/my-tasks');

        $response->assertStatus(200)
            ->assertJsonCount(2)
            ->assertJsonFragment(['title' => 'My First Task'])
            ->assertJsonFragment(['title' => 'My Second Task'])
            ->assertJsonMissing(['title' => 'Someone Elses Task']);
    }

    /** @test */
    public function user_can_update_their_assigned_task_status()
    {
        $user = User::factory()->create(['is_admin' => false]);
        $task = Task::factory()->create(['status' => 'pending']);
        
        $user->taskAssignments()->create([
            'task_id' => $task->id,
            'assigned_at' => now()
        ]);

        Sanctum::actingAs($user);

        $response = $this->patchJson("/api/tasks/{$task->id}/status", [
            'status' => 'in_progress'
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('task.status', 'in_progress');

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'status' => 'in_progress'
        ]);
    }

    /** @test */
    public function user_cannot_update_unassigned_task_status()
    {
        $user = User::factory()->create(['is_admin' => false]);
        $task = Task::factory()->create(['status' => 'pending']);
        
        // Nem rendeljük hozzá a task-ot a userhez

        Sanctum::actingAs($user);

        $response = $this->patchJson("/api/tasks/{$task->id}/status", [
            'status' => 'in_progress'
        ]);

        $response->assertStatus(403)
            ->assertJson([
                'message' => 'You are not assigned to this task'
            ]);

        // Ellenőrizzük, hogy nem változott az állapot
        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'status' => 'pending'
        ]);
    }

    /** @test */
    public function unauthenticated_user_cannot_access_tasks()
    {
        // Nem autentikálunk senkit

        $response = $this->getJson('/api/my-tasks');

        $response->assertStatus(401)
            ->assertJson([
                'message' => 'Unauthenticated.'
            ]);
    }

    /** @test */
    public function admin_can_delete_task()
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $task = Task::factory()->create(['title' => 'Task to Delete']);

        Sanctum::actingAs($admin);

        $response = $this->deleteJson("/api/admin/tasks/{$task->id}");

        $response->assertStatus(200);

        // Soft delete-et használunk, szóval még létezik, de deleted_at van kitöltve
        $this->assertSoftDeleted('tasks', [
            'id' => $task->id
        ]);
    }

    /** @test */
    public function task_validation_fails_with_invalid_data()
    {
        $admin = User::factory()->create(['is_admin' => true]);
        Sanctum::actingAs($admin);

        $invalidData = [
            'title' => '', // Üres title
            'priority' => 'invalid_priority', // Érvénytelen priority
            'status' => 'wrong_status', // Érvénytelen status
        ];

        $response = $this->postJson('/api/admin/tasks', $invalidData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['title', 'priority', 'status']);
    }
}
