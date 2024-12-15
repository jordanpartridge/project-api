<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Project extends Model
{
    protected $fillable = [
        'name',
        'description',
        'github_id',
        'github_project_number',
        'github_project_settings',
        'github_project_visibility',
        'status',
    ];

    protected $casts = [
        'github_project_settings' => 'json',
        'last_synced_at' => 'datetime',
    ];

    public function issues(): HasMany
    {
        return $this->hasMany(Issue::class);
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(Owner::class);
    }

    public function scopeWithGitHubProject($query)
    {
        return $query->whereNotNull('github_project_number');
    }

    public function needsSync(): bool
    {
        return $this->last_synced_at === null ||
            $this->last_synced_at->diffInHours(now()) > 1;
    }
}