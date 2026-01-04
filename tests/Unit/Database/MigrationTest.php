<?php

namespace Tests\Unit\Database;

use Tests\TestCase;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class MigrationTest extends TestCase
{
    public function test_migrations_can_run_without_errors()
    {
        // Ensure we're using the testing database
        $this->assertEquals('testing', config('database.default'));
        
        // Get all migrations
        $migrations = DB::table('migrations')->get();
        
        // Assert migrations table exists and has records
        $this->assertTrue(Schema::hasTable('migrations'));
        $this->assertNotEmpty($migrations);
    }
    
    public function test_projects_table_has_correct_columns()
    {
        $this->assertTrue(Schema::hasTable('projects'));
        
        $columns = Schema::getColumnListing('projects');
        
        $expectedColumns = [
            'id',
            'name',
            'slug',
            'description',
            'status',
            'featured_image',
            'demo_url',
            'long_description',
            'is_featured',
            'display_order',
            'meta_data',
            'created_at',
            'updated_at'
        ];
        
        foreach ($expectedColumns as $column) {
            $this->assertContains($column, $columns, "Missing expected column: {$column}");
        }
    }
    
    public function test_no_duplicate_columns()
    {
        $tables = Schema::getAllTables();
        
        foreach ($tables as $table) {
            $tableName = array_values((array)$table)[0];
            $columns = Schema::getColumnListing($tableName);
            $duplicates = array_diff_assoc($columns, array_unique($columns));
            
            $this->assertEmpty(
                $duplicates,
                "Found duplicate columns in table {$tableName}: " . implode(', ', $duplicates)
            );
        }
    }
}