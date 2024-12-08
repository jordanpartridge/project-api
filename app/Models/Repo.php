<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\LogOptions;

/** @use HasFactory<\Database\Factories\RepoFactory> */
class Repo extends DataModel
{
    protected $fillable = [
        'project_id',
        'owner_id',
        'language_id',
        'name',
        'full_name',
        'description',
        'private',
        'fork',
        'pushed_at',
    ];

    protected $casts = [
        'private' => 'boolean',
        'fork' => 'boolean',
        'pushed_at' => 'datetime',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(Owner::class);
    }

    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class);
    }

    public function commits(): HasMany
    {
        return $this->hasMany(Commit::class);
    }

    public function files(): HasMany
    {
        return $this->hasMany(File::class);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'name',
                'full_name',
                'description',
                'private',
                'pushed_at',
                'project_id',
                'owner_id',
                'language_id',
            ])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    public function getMainLanguageAttribute(): ?string
    {
        return $this->language?->name;
    }

    public function getOwnerNameAttribute(): ?string
    {
        return $this->owner?->name;
    }

    public function getLatestCommitAttribute()
    {
        return $this->commits()->latest('committed_at')->first();
    }
}
