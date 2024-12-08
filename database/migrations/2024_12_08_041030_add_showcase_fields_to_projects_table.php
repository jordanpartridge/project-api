<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->string('status')->default('active');
            $table->string('featured_image')->nullable();
            $table->string('demo_url')->nullable();
            $table->text('long_description')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->integer('display_order')->nullable();
            $table->json('meta_data')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn('status');
            $table->dropColumn('featured_image');
            $table->dropColumn('demo_url');
            $table->dropColumn('long_description');
            $table->dropColumn('is_featured');
            $table->dropColumn('display_order');
            $table->dropColumn('meta_data');
        });
    }
};
