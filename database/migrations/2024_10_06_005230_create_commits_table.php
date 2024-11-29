<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('commits', function (Blueprint $table) {
            $table->snowflakeId();
            $table->foreignId('repo_id')->constrained()->cascadeOnDelete();
            $table->string('sha')->nullable();
            $table->text('message');
            $table->json('author');
            $table->timestamps();
            $table->timestamp('committed_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('commits');
    }
};
