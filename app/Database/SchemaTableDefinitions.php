<?php

namespace App\Database;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Closure;

trait SchemaTableDefinitions 
{
    protected array $schemaTables = [
        'owners',
        'cache',
        'cache_locks',
        'repos',
        'private_g_p_t_documents'
    ];

    protected array $schemaColumns = [
        'repos' => ['owner_id', 'github_id', 'private'],
        'owners' => ['github_id', 'login', 'type', 'avatar_url', 'html_url']
    ];

    protected function createTableIfNeeded(string $table, Closure $callback)
    {
        if (!Schema::hasTable($table)) {
            Schema::create($table, $callback);
        }
    }

    protected function addColumnIfNeeded(string $table, string $column, Closure $callback)
    {
        if (!Schema::hasColumn($table, $column)) {
            Schema::table($table, $callback);
        }
    }

    protected function addForeignKeyIfNeeded(string $table, string $column, string $references, ?string $on = null)
    {
        // Check if foreign key already exists
        $foreignKeys = $this->getForeignKeys($table);
        if (!in_array($column, $foreignKeys)) {
            Schema::table($table, function (Blueprint $table) use ($column, $references, $on) {
                $constraint = $table->foreignId($column);
                if ($on) {
                    $constraint->references($on)->on($references);
                } else {
                    $constraint->constrained($references);
                }
            });
        }
    }

    protected function getForeignKeys(string $table): array
    {
        $foreignKeys = DB::select("
            SELECT COLUMN_NAME
            FROM information_schema.KEY_COLUMN_USAGE
            WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ? AND REFERENCED_TABLE_NAME IS NOT NULL
        ", [env('DB_DATABASE'), $table]);

        return array_map(fn($fk) => $fk->COLUMN_NAME, $foreignKeys);
    }

    protected function isSchemaTable(string $table): bool
    {
        return in_array($table, $this->schemaTables);
    }

    protected function hasSchemaColumn(string $table, string $column): bool
    {
        return isset($this->schemaColumns[$table]) && 
               in_array($column, $this->schemaColumns[$table]);
    }

    protected function assertSchemaTablesExist(): bool
    {
        foreach ($this->schemaTables as $table) {
            if (!Schema::hasTable($table)) {
                return false;
            }
        }
        return true;
    }
}
