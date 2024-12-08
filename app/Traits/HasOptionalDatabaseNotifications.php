<?php

namespace App\Traits;

use Filament\Panel;
use Illuminate\Support\Facades\Schema;

trait HasOptionalDatabaseNotifications
{
    protected function configureDatabaseNotifications(Panel $panel): Panel
    {
        if (Schema::hasTable('notifications')) {
            $panel->databaseNotifications();
        }

        return $panel;
    }
}
