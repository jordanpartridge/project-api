<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Fix project_id on repos table
        Schema::table('repos', function (Blueprint $table) {
            if (!Schema::hasColumn('repos', 'project_id')) {
                $table->foreignId('project_id')->nullable()->constrained('projects')->nullOnDelete();
            }
        });

        // Create repos table if it doesn't exist
        if (!Schema::hasTable('repos')) {
            Schema::create('repos', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('full_name')->unique();
                $table->foreignId('language_id')->nullable()->constrained('languages')->nullOnDelete();
                $table->string('url');
                $table->foreignId('owner_id')->constrained('owners')->cascadeOnDelete();
                $table->foreignId('project_id')->nullable()->constrained('projects')->nullOnDelete();
                $table->text('description')->nullable();
                $table->boolean('private')->default(false);
                $table->timestamps();
                $table->softDeletes();
            });
        }
    }

    public function down(): void
    {
        Schema::table('repos', function (Blueprint $table) {
            $table->dropConstrainedForeignId('project_id');
        });
    }
};