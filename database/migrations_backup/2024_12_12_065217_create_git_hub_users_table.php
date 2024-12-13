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
        Schema::create('git_hub_users', function (Blueprint $table) {
            $table->id();
            $table->string('github_id')->unique();
            $table->string('username')->unique();
            $table->string('avatar_url')->nullable();
            $table->string('profile_url');
            $table->enum('type', ["User","Organization"]);
            $table->string('repos');
            $table->timestamp('created_at');
            $table->timestamp('updated_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('git_hub_users');
    }
};
