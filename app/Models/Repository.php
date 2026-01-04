<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Repository extends Model
{
    use SoftDeletes;
    
    protected $fillable = [
        'github_id',
        'owner_id',
        'language_id',
        'name',
        'full_name',
        'description',
        'url',
        'private',
        'is_fork',
        'stargazers_count',
        'watchers_count',
        'forks_count',
        'last_push_at',
    ];

    protected $casts = [
        'private' => 'boolean',
        'is_fork' => 'boolean',
        'stargazers_count' => 'integer',
        'watchers_count' => 'integer',
        'forks_count' => 'integer',
        'last_push_at' => 'datetime',
    ];

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
    
    public function metrics(): HasMany
    {
        return $this->hasMany(RepositoryMetric::class);
    }

    public function getMainLanguageAttribute(): ?string
    {
        return $this->language?->name;
    }

    public function getOwnerNameAttribute(): ?string
    {
        return $this->owner?->name ?? $this->owner?->login;
    }

    public function getLatestCommitAttribute()
    {
        return $this->commits()->latest('committed_at')->first();
    }
}