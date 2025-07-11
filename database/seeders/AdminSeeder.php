<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Admin;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        // Create admin user
        $adminUser = User::firstOrCreate([
            'username' => 'admin'
        ], [
            'password_hash' => Hash::make('admin'),
            'role' => 'admin',
        ]);

        // Create admin profile
        Admin::firstOrCreate([
            'admin_id' => 1001
        ], [
            'user_id' => $adminUser->user_id,
            'first_name' => 'School',
            'last_name' => 'Administrator',
            'position' => 'School Administrator',
        ]);

        $this->command->info('Admin user created successfully!');
        $this->command->info('Username: admin');
        $this->command->info('Password: admin');
    }
} 