<?php

namespace App\Models;

use Glhd\Bits\Database\HasSnowflakes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Language extends Model
{
    use HasFactory;
    use HasSnowflakes;
    use LogsActivity;

    protected $fillable = [
        'name',
    ];

    public function getRouteKeyName()
    {
        return 'name';
    }

    public function repos(): HasMany
    {
        return $this->hasMany(Repo::class);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable();
    }
}
