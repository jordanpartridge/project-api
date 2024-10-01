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
            $table->string('name')->unique();
            $table->text('description')->nullable();
            $table->string('url')->unique();
            $table->string('language')->nullable();
            $table->foreignId('project_id')->constrained('projects')->cascadeOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('repos');
    }
};
