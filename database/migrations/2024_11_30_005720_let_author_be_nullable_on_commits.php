<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('commits', function (Blueprint $table) {
            $table->json('author')->nullable()->change();
        });

    }

    public function down(): void
    {
        Schema::table('commits', function (Blueprint $table) {
            $table->json('author')->change();
        });
    }
};
