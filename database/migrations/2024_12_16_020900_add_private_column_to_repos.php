<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('repos', function (Blueprint $table) {
            if (!Schema::hasColumn('repos', 'private')) {
                $table->boolean('private')->default(false)->after('description');
            }
        });
    }

    public function down()
    {
        Schema::table('repos', function (Blueprint $table) {
            $table->dropColumn('private');
        });
    }
};
