<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('repos', function (Blueprint $table) {
            $table->foreignId('owner_id')->constrained('owners')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('repos', function (Blueprint $table) {
            $table->dropForeign('owner_id');
        });
    }
};
