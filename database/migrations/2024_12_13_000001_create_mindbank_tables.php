<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('entities', function (Blueprint $table) {
            $table->id();
            $table->string('name')->index();
            $table->string('type')->index();
            $table->timestamps();
        });

        Schema::create('observations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('entity_id')->constrained()->cascadeOnDelete();
            $table->text('content');
            $table->string('confidence')->default('high');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('observations');
        Schema::dropIfExists('entities');
    }
};