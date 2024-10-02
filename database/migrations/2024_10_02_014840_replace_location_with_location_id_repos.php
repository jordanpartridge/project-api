<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('repos', function (Blueprint $table) {
            $table->dropColumn('language');
            $table->foreignId('language_id')->after('project_id')->nullable()->constrained('languages')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('repos', function (Blueprint $table) {
            $table->dropColumn('language_id');
            $table->string('language')->nullable();
        });
    }
};
