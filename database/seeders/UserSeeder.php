<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Create Super Admin
        User::create([
            'name' => 'Super Admin',
            'email' => 'super@example.com',
            'password' => Hash::make('password'),
        ])->assignRole('super_admin');

        // Create Admin
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
        ])->assignRole('admin');

        // Create GitHub User
        User::create([
            'name' => 'GitHub User',
            'email' => 'github@example.com',
            'password' => Hash::make('password'),
        ])->assignRole('github_user');

        // Create Developer (has both admin and github access)
        User::create([
            'name' => 'Developer',
            'email' => 'dev@example.com',
            'password' => Hash::make('password'),
        ])->syncRoles(['admin', 'github_user']);
    }
}
