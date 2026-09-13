<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Super Admin
        $superAdmin = User::updateOrCreate(
            ['email' => 'superadmin@example.com'],
            [
                'name' => 'Lead Super Administrator',
                'password' => Hash::make('Password123!'),
                'is_admin' => true,
                'email_verified_at' => now(),
            ]
        );
        $superAdmin->syncRoles(['Super Admin']);

        // 2. Admin
        $admin = User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Library Administrator',
                'password' => Hash::make('Password123!'),
                'is_admin' => true,
                'email_verified_at' => now(),
            ]
        );
        $admin->syncRoles(['Admin']);

        // 3. Editor
        $editor = User::updateOrCreate(
            ['email' => 'editor@example.com'],
            [
                'name' => 'Content Editor',
                'password' => Hash::make('Password123!'),
                'is_admin' => false,
                'email_verified_at' => now(),
            ]
        );
        $editor->syncRoles(['Editor']);

        // 4. Moderator
        $moderator = User::updateOrCreate(
            ['email' => 'moderator@example.com'],
            [
                'name' => 'Community Moderator',
                'password' => Hash::make('Password123!'),
                'is_admin' => false,
                'email_verified_at' => now(),
            ]
        );
        $moderator->syncRoles(['Moderator']);
    }
}
