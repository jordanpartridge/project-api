<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::dropIfExists('files');
        Schema::dropIfExists('repos');
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
            $table->integer('stars');
            $table->integer('forks');
            $table->integer('open_issues_count');
            $table->enum('visibility', ["public", "private", "internal"]);
            $table->string('owner_id');
            $table->string('issues');
            $table->string('pull_requests');
            $table->timestamp('created_at');
            $table->timestamp('updated_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('repos');
    }
};
