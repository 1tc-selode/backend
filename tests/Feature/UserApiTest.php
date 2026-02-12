<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class UserApiTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function admin_can_view_all_users()
    {
        $admin = User::factory()->create(['is_admin' => true]);
        User::factory()->count(5)->create(['is_admin' => false]);

        Sanctum::actingAs($admin);

        $response = $this->getJson('/api/admin/users');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'name', 'email', 'department', 'phone', 'is_admin']
                ]
            ]);
    }

    /** @test */
    public function non_admin_cannot_view_all_users()
    {
        $user = User::factory()->create(['is_admin' => false]);
        User::factory()->count(3)->create();

        Sanctum::actingAs($user);

        $response = $this->getJson('/api/admin/users');

        $response->assertStatus(403)
            ->assertJson([
                'message' => 'Unauthorized. Admin access required.'
            ]);
    }

    /** @test */
    public function admin_can_create_new_user()
    {
        $admin = User::factory()->create(['is_admin' => true]);
        Sanctum::actingAs($admin);

        $userData = [
            'name' => 'New Test User',
            'email' => 'newuser@test.com',
            'password' => 'password123',
            'department' => 'IT',
            'phone' => '+36 1 234 5678',
            'is_admin' => false
        ];

        $response = $this->postJson('/api/admin/users', $userData);

        $response->assertStatus(201)
            ->assertJsonPath('user.name', 'New Test User')
            ->assertJsonPath('user.email', 'newuser@test.com');

        $this->assertDatabaseHas('users', [
            'email' => 'newuser@test.com',
            'department' => 'IT'
        ]);
    }

    /** @test */
    public function admin_can_update_user()
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $user = User::factory()->create([
            'name' => 'Old Name',
            'department' => 'Sales'
        ]);

        Sanctum::actingAs($admin);

        $response = $this->putJson("/api/admin/users/{$user->id}", [
            'name' => 'Updated Name',
            'department' => 'Marketing'
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('user.name', 'Updated Name')
            ->assertJsonPath('user.department', 'Marketing');

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Updated Name',
            'department' => 'Marketing'
        ]);
    }

    /** @test */
    public function non_admin_cannot_update_other_users()
    {
        $user = User::factory()->create(['is_admin' => false]);
        $otherUser = User::factory()->create(['name' => 'Other User']);

        Sanctum::actingAs($user);

        $response = $this->putJson("/api/admin/users/{$otherUser->id}", [
            'name' => 'Hacked Name'
        ]);

        $response->assertStatus(403);

        $this->assertDatabaseHas('users', [
            'id' => $otherUser->id,
            'name' => 'Other User' // Nem változott
        ]);
    }

    /** @test */
    public function admin_can_delete_user()
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $user = User::factory()->create();

        Sanctum::actingAs($admin);

        $response = $this->deleteJson("/api/admin/users/{$user->id}");

        $response->assertStatus(200);

        $this->assertSoftDeleted('users', [
            'id' => $user->id
        ]);
    }

    /** @test */
    public function authenticated_user_can_view_own_profile()
    {
        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com'
        ]);

        Sanctum::actingAs($user);

        $response = $this->getJson('/api/profile');

        $response->assertStatus(200)
            ->assertJson([
                'name' => 'Test User',
                'email' => 'test@example.com'
            ]);
    }

    /** @test */
    public function user_can_update_own_profile()
    {
        $user = User::factory()->create([
            'name' => 'Old Name',
            'department' => 'Sales',
            'phone' => '+36 1 111 1111'
        ]);

        Sanctum::actingAs($user);

        $response = $this->putJson('/api/profile', [
            'name' => 'New Name',
            'department' => 'Marketing',
            'phone' => '+36 1 999 9999'
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('user.name', 'New Name')
            ->assertJsonPath('user.department', 'Marketing')
            ->assertJsonPath('user.phone', '+36 1 999 9999');

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'New Name',
            'department' => 'Marketing'
        ]);
    }
}
