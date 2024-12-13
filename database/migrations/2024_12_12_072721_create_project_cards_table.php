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
        Schema::create('project_cards', function (Blueprint $table) {
            $table->id();
            $table->string('github_id')->unique();
            $table->text('note')->nullable();
            $table->string('issue_id')->nullable();
            $table->string('pull_request_id')->nullable();
            $table->string('column_id');
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_cards');
    }
};
