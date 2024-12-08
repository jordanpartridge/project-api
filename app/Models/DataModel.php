<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

abstract class DataModel extends BaseModel
{
    use SoftDeletes;
}
