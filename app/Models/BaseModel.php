<?php

namespace App\Models;

use Glhd\Bits\Database\HasSnowflakes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

abstract class BaseModel extends Model
{
    use HasFactory;
    use HasSnowflakes;
    use LogsActivity;

    /**
     * Get the activity log options for the model.
     */
    abstract public function getActivitylogOptions(): LogOptions;
}
