<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create admin user
        User::factory()->admin()->create([
            'name' => 'Admin',
            'email' => 'admin@taskmanager.hu',
            'password' => bcrypt('admin123'),
            'department' => 'Management',
            'phone' => '+36 1 234 5678',
        ]);

        // Create 9 regular users with password: Jelszo12
        User::factory(9)->create();
    }
}
