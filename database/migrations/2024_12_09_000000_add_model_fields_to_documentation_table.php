<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('documentation', function (Blueprint $table) {
            $table->string('model_name')->nullable()->after('category');
            $table->boolean('auto_generated')->default(false)->after('is_published');
            $table->index(['model_name', 'category']);
        });
    }

    public function down()
    {
        Schema::table('documentation', function (Blueprint $table) {
            $table->dropColumn(['model_name', 'auto_generated']);
        });
    }
};
