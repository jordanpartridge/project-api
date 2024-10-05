<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('repos', function (Blueprint $table) {
            $table->snowflakeId();
            $table->unsignedBigInteger('github_id')->unique(); // New field for GitHub's repo ID
            $table->string('full_name')->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('url')->unique();
            $table->string('language')->nullable();
            $table->boolean('private')->default(false);
            $table->foreignId('project_id')->constrained('projects')->cascadeOnDelete();

            $table->unsignedInteger('stars_count')->default(0);
            $table->unsignedInteger('forks_count')->default(0);
            $table->unsignedInteger('open_issues_count')->default(0);
            $table->string('default_branch')->default('main');
            $table->timestamp('last_push_at')->nullable();
            $table->json('topics')->nullable();
            $table->string('license')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('repos');
    }
};
