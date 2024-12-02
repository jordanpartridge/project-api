<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('files', function (Blueprint $table) {
            $table->string('status')->after('path');
            $table->renameColumn('path', 'filename');
            $table->integer('additions')->after('filename');
            $table->integer('deletions')->after('additions');
            $table->integer('changes')->after('deletions');
            $table->string('size')->after('changes')->default('0');
        });
    }

    public function down(): void
    {
        Schema::table('files', function (Blueprint $table) {
            $table->dropColumn('status');
            $table->renameColumn('filename', 'path');
            $table->dropColumn('additions', 'deletions', 'changes', 'size');
        });
    }
};
