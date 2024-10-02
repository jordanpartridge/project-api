<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        /**
         * Programming languages table this is a possilbe interesting dimension table for the project
         * Repos have a language and it would be neat for example to query repos by language
         * so my thought is:
         *
         * /api/languages/php/repos to get all repos that are php
         */
        Schema::create('languages', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('languages');
    }
};
