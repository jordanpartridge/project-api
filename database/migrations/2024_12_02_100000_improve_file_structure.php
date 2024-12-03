<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // First, create the file_versions table (renamed from commit_file)
        Schema::rename('commit_file', 'file_versions');
        
        // Add new columns to file_versions
        Schema::table('file_versions', function (Blueprint $table) {
            $table->string('sha')->after('file_id');
            $table->string('previous_sha')->nullable()->after('sha');
            $table->string('content_type')->nullable()->after('previous_sha');
        });

        // Modify files table
        Schema::table('files', function (Blueprint $table) {
            // Add SHA to track current version
            $table->string('sha')->nullable()->after('filename');
            
            // Add path separate from filename
            $table->string('path')->after('filename');
            
            // Remove columns that should be in file_versions only
            $table->dropColumn(['additions', 'deletions', 'changes', 'size']);
            
            // Add unique constraint
            $table->unique(['repo_id', 'filename']);
        });
    }

    public function down(): void
    {
        // Restore files table to original state
        Schema::table('files', function (Blueprint $table) {
            $table->dropUnique(['repo_id', 'filename']);
            $table->dropColumn(['sha', 'path']);
            $table->integer('additions')->default(0);
            $table->integer('deletions')->default(0);
            $table->integer('changes')->default(0);
            $table->string('size')->default('0');
        });

        // Remove new columns from file_versions
        Schema::table('file_versions', function (Blueprint $table) {
            $table->dropColumn(['sha', 'previous_sha', 'content_type']);
        });

        // Rename back to original
        Schema::rename('file_versions', 'commit_file');
    }
};