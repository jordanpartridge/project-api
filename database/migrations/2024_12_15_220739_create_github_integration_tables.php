<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Only create owners table if it doesn't exist
        if (!Schema::hasTable('owners')) {
            Schema::create('owners', function (Blueprint $table) {
                $table->id();
                $table->bigInteger('github_id')->unique();
                $table->string('login');
                $table->enum('type', ['User', 'Organization']);
                $table->string('name')->nullable();
                $table->string('avatar_url')->nullable();
                $table->string('html_url')->nullable();
                $table->string('company')->nullable();
                $table->string('blog')->nullable();
                $table->string('location')->nullable();
                $table->string('email')->nullable();
                $table->text('bio')->nullable();
                $table->integer('public_repos')->default(0);
                $table->integer('public_gists')->default(0);
                $table->integer('followers')->default(0);
                $table->integer('following')->default(0);
                $table->timestamps();
                $table->softDeletes();
            });
        }

        // Update existing owners table if needed
        Schema::table('owners', function (Blueprint $table) {
            if (!Schema::hasColumn('owners', 'github_id')) {
                $table->bigInteger('github_id')->unique()->after('id');
            }
        });
    }

    public function down()
    {
        // Don't drop the owners table as it might be used by other parts of the application
    }
};
