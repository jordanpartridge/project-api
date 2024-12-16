<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PrivateGPTAnalysis extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'prompt',
        'response',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];
}
