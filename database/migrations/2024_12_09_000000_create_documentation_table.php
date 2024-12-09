<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('documentation', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('content');
            $table->string('category');
            $table->string('model_name')->nullable();
            $table->integer('order')->default(0);
            $table->boolean('is_published')->default(false);
            $table->boolean('auto_generated')->default(false);
            $table->timestamps();

            $table->index(['category', 'is_published']);
            $table->index(['model_name', 'category']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('documentation');
    }
};
