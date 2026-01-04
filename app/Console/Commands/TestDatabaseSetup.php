<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class TestDatabaseSetup extends Command
{
    protected $signature = 'test:setup-database';
    protected $description = 'Set up the test database environment';

    public function handle()
    {
        $this->info('Setting up test database...');
        
        // Drop and recreate database
        DB::statement('DROP DATABASE IF EXISTS project_api_testing');
        DB::statement('CREATE DATABASE project_api_testing');
        
        // Switch to test database
        config(['database.connections.mysql.database' => 'project_api_testing']);
        DB::purge('mysql');
        DB::reconnect('mysql');
        
        // Run migrations from scratch
        $this->call('migrate:fresh', [
            '--database' => 'mysql',
            '--env' => 'testing',
            '--no-load' => true // Skip schema loading
        ]);
        
        $this->info('Test database setup complete!');
    }
}