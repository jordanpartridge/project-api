<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\Activitylog\LogOptions;

/** @use HasFactory<\Database\Factories\CommitFactory> */
class Commit extends DataModel
{
    protected $fillable = [
        'sha',
        'message',
        'author',
        'committed_at',
    ];

    protected $casts = [
        'committed_at' => 'datetime',
        'author' => 'array',
    ];

    public function repo(): BelongsTo
    {
        return $this->belongsTo(Repo::class);
    }

    public function files(): BelongsToMany
    {
        return $this->belongsToMany(File::class, 'file_versions')->withPivot('status', 'additions', 'deletions', 'changes');
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'sha',
                'message',
                'committed_at',
            ])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}
