<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('commit_file', function (Blueprint $table) {
            $table->id();
            $table->foreignId('commit_id')->constrained()->cascadeOnDelete();
            $table->foreignId('file_id')->constrained()->cascadeOnDelete();
            $table->integer('additions')->default(0); // Lines added
            $table->integer('deletions')->default(0); // Lines removed
            $table->integer('changes')->default(0);   // Total changes (additions + deletions)
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('commit_file');
    }
};
