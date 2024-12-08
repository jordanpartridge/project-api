<?php

namespace App\Models;

use Spatie\Activitylog\LogOptions;

/** @use HasFactory<\Database\Factories\OwnerFactory> */
class Owner extends DataModel
{
    protected $fillable = [
        'login',
        'type',
        'avatar_url',
        'html_url',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'login',
                'type',
                'avatar_url',
            ])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}
