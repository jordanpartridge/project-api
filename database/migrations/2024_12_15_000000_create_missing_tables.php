<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Create commits table if it doesn't exist
        if (!Schema::hasTable('commits')) {
            Schema::create('commits', function (Blueprint $table) {
                $table->id();
                $table->foreignId('repo_id')->constrained('repos')->onDelete('cascade');
                $table->string('sha')->unique();
                $table->text('message');
                $table->json('author');
                $table->timestamps();
                $table->softDeletes();
            });
        }

        // Create languages table if it doesn't exist
        if (!Schema::hasTable('languages')) {
            Schema::create('languages', function (Blueprint $table) {
                $table->id();
                $table->string('name')->unique();
                $table->timestamps();
                $table->softDeletes();
            });
        }

        // Create project_repository table if it doesn't exist
        if (!Schema::hasTable('project_repository')) {
            Schema::create('project_repository', function (Blueprint $table) {
                $table->id();
                $table->foreignId('project_id')->constrained('projects')->onDelete('cascade');
                $table->foreignId('repo_id')->constrained('repos')->onDelete('cascade');
                $table->timestamps();
            });
        }

        // Add missing columns to repos table
        Schema::table('repos', function (Blueprint $table) {
            if (!Schema::hasColumn('repos', 'language_id')) {
                $table->foreignId('language_id')->nullable()->constrained('languages')->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('commits');
        Schema::dropIfExists('languages');
        Schema::dropIfExists('project_repository');
        
        Schema::table('repos', function (Blueprint $table) {
            $table->dropConstrainedForeignId('language_id');
        });
    }
};