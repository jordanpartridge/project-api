<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('issues', function (Blueprint $table) {
            $table->id();
            $table->string('github_id')->unique();
            $table->string('title');
            $table->text('body')->nullable();
            $table->enum('state', ["open","closed"]);
            $table->string('repo_id');
            $table->string('author_id');
            $table->string('assignee_id')->nullable();
            $table->string('project_card_id')->nullable();
            $table->timestamp('closed_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('issues');
    }
};
