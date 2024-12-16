<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Activity log table
        if (!Schema::hasTable('activity_log')) {
            Schema::create('activity_log', function (Blueprint $table) {
                $table->id();
                $table->string('log_name')->nullable();
                $table->text('description');
                $table->nullableMorphs('subject');
                $table->nullableMorphs('causer');
                $table->json('properties')->nullable();
                $table->uuid('batch_uuid')->nullable();
                $table->string('event')->nullable();
                $table->timestamps();
                $table->index('log_name');
            });
        }

        // Add any missing columns to notifications table
        if (Schema::hasTable('notifications')) {
            if (!Schema::hasColumn('notifications', 'data')) {
                Schema::table('notifications', function (Blueprint $table) {
                    $table->text('data');
                });
            }
            if (!Schema::hasColumn('notifications', 'read_at')) {
                Schema::table('notifications', function (Blueprint $table) {
                    $table->timestamp('read_at')->nullable();
                });
            }
        } else {
            Schema::create('notifications', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->string('type');
                $table->morphs('notifiable');
                $table->text('data');
                $table->timestamp('read_at')->nullable();
                $table->timestamps();
            });
        }

        // Add missing columns to roles table if it exists
        if (Schema::hasTable('roles')) {
            if (!Schema::hasColumn('roles', 'guard_name')) {
                Schema::table('roles', function (Blueprint $table) {
                    $table->string('guard_name');
                });
            }
        }
    }

    public function down()
    {
        Schema::dropIfExists('activity_log');
        // We won't drop other tables in down() to preserve existing data
    }
};
