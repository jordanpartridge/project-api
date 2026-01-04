<?php

namespace App\Models\Traits;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

trait HasStandardBehavior
{
    use HasFactory;
    use LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly($this->getFillable())
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            static::validateStructure($model);
        });

        static::updating(function ($model) {
            static::validateStructure($model);
        });
    }

    protected static function validateStructure($model): void
    {
        if (!isset($model->fillable)) {
            throw new \Exception(class_basename($model) . ' must define $fillable property');
        }

        if (!isset($model->casts)) {
            throw new \Exception(class_basename($model) . ' must define $casts property');
        }

        foreach ($model->fillable as $field) {
            if (!array_key_exists($field, $model->casts) && !in_array($field, ['id', 'created_at', 'updated_at'])) {
                throw new \Exception("Field '$field' in " . class_basename($model) . " must have a cast type defined");
            }
        }
    }
}