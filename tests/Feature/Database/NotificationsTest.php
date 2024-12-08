<?php

namespace Tests\Feature\Database;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class NotificationsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function notifications_table_exists(): void
    {
        $this->assertTrue(
            Schema::hasTable('notifications'),
            'The notifications table does not exist. Run php artisan migrate to create it.',
        );
    }

    /** @test */
    public function notifications_table_has_required_columns(): void
    {
        $this->assertTrue(Schema::hasColumns('notifications', [
            'id',
            'type',
            'notifiable_type',
            'notifiable_id',
            'data',
            'read_at',
            'created_at',
            'updated_at',
        ]));
    }
}
