<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Repo extends Model
{
    protected $fillable = [
        'full_name',
        'url',
        'description',
        'project_id',
        'name',
        'updated_at',
        'created_at'
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }
}