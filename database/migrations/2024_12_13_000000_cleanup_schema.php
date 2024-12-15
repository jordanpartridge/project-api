<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // First, drop tables in correct order to avoid foreign key constraints
        Schema::dropIfExists('project_repository');
        Schema::dropIfExists('pull_requests');
        Schema::dropIfExists('issues');
        Schema::dropIfExists('documentation');
        Schema::dropIfExists('commit_file');
        Schema::dropIfExists('file_versions');
        Schema::dropIfExists('files');
        Schema::dropIfExists('commits');
        Schema::dropIfExists('repos');
        Schema::dropIfExists('owners');

        // Recreate owners table (base table)
        Schema::create('owners', function (Blueprint $table) {
            $table->id();
            $table->string('github_id')->unique();
            $table->string('login')->unique();
            $table->enum('type', ['User', 'Organization']);
            $table->string('avatar_url')->nullable();
            $table->string('html_url');
            $table->timestamps();
            $table->softDeletes();
        });

        // Create repos table with proper relationships
        Schema::create('repos', function (Blueprint $table) {
            $table->id();
            $table->string('github_id')->unique();
            $table->string('name');
            $table->string('full_name');
            $table->text('description')->nullable();
            $table->string('url');
            $table->string('html_url');
            $table->string('clone_url');
            $table->string('ssh_url')->nullable();
            $table->string('language')->nullable();
            $table->integer('stars')->default(0);
            $table->integer('forks')->default(0);
            $table->integer('open_issues_count')->default(0);
            $table->enum('visibility', ['public', 'private', 'internal']);
            $table->foreignId('owner_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });

        // Create issues table
        Schema::create('issues', function (Blueprint $table) {
            $table->id();
            $table->string('github_id')->unique();
            $table->string('title');
            $table->text('body')->nullable();
            $table->enum('state', ['open', 'closed']);
            $table->foreignId('repo_id')->constrained()->cascadeOnDelete();
            $table->foreignId('author_id')->constrained('owners');
            $table->foreignId('assignee_id')->nullable()->constrained('owners');
            $table->timestamp('closed_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // Create pull requests table
        Schema::create('pull_requests', function (Blueprint $table) {
            $table->id();
            $table->string('github_id')->unique();
            $table->string('title');
            $table->text('body')->nullable();
            $table->enum('state', ['open', 'closed', 'merged']);
            $table->foreignId('repo_id')->constrained()->cascadeOnDelete();
            $table->foreignId('author_id')->constrained('owners');
            $table->foreignId('merged_by_id')->nullable()->constrained('owners');
            $table->timestamp('closed_at')->nullable();
            $table->timestamp('merged_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // Create documentation table
        Schema::create('documentation', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('content');
            $table->string('category');
            $table->integer('order')->default(0);
            $table->boolean('is_published')->default(true);
            $table->json('meta_data')->nullable();
            $table->foreignId('repo_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });

        // Create project_repository pivot table
        Schema::create('project_repository', function (Blueprint $table) {
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->foreignId('repository_id')->constrained('repos')->cascadeOnDelete();
            $table->timestamps();
            $table->primary(['project_id', 'repository_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_repository');
        Schema::dropIfExists('documentation');
        Schema::dropIfExists('pull_requests');
        Schema::dropIfExists('issues');
        Schema::dropIfExists('repos');
        Schema::dropIfExists('owners');
    }
};
