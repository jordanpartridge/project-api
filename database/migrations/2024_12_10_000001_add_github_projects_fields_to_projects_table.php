<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->string('github_project_number')->nullable()->after('github_id');
            $table->json('github_project_settings')->nullable()->after('github_project_number');
            $table->string('github_project_visibility')->nullable()->after('github_project_settings');
            $table->timestamp('last_synced_at')->nullable()->after('github_project_visibility');
        });
    }

    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn([
                'github_project_number',
                'github_project_settings',
                'github_project_visibility',
                'last_synced_at',
            ]);
        });
    }
};