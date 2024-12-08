<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            ShieldSeeder::class, // Run this first to create roles and permissions
            UserSeeder::class,
        ]);
    }
}
