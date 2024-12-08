<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\Activitylog\LogOptions;

/** @use HasFactory<\Database\Factories\FileFactory> */
class File extends DataModel
{
    protected $fillable = [
        'repo_id',
        'filename',
        'content',
        'sha',
        'status',
    ];

    public function commits(): BelongsToMany
    {
        return $this->belongsToMany(Commit::class, 'file_versions')->withPivot('status', 'additions', 'deletions', 'changes');
    }

    public function repo(): BelongsTo
    {
        return $this->belongsTo(Repo::class);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'filename',
                'status',
                'sha',
                'repo_id',
            ])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}
