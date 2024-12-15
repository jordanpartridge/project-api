<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('documentation', function (Blueprint $table) {
            $table->id();
            $table->string('title', 255)->unique();
            $table->string('slug', 255)->unique();
            $table->string('category', 50);
            $table->longText('content');
            $table->integer('order')->default(0);
            $table->boolean('is_published')->default(false);
            $table->json('meta')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['category', 'order']);
            $table->index('is_published');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('documentation');
    }
};