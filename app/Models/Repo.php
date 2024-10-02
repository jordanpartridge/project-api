<?php

namespace App\Models;

use Glhd\Bits\Database\HasSnowflakes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Repo extends Model
{
    use HasFactory;
    use HasSnowflakes;
    use LogsActivity;
    use SoftDeletes;

    protected $fillable = [
        'github_id',
        'full_name',
        'name',
        'description',
        'url',
        'language',
        'private',
        'project_id',
        'stars_count',
        'forks_count',
        'open_issues_count',
        'default_branch',
        'last_push_at',
        'topics',
        'license',
    ];

    protected $casts = [
        'private' => 'boolean',
        'stars_count' => 'integer',
        'forks_count' => 'integer',
        'open_issues_count' => 'integer',
        'last_push_at' => 'datetime',
        'topics' => 'array',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logFillable();
    }

    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }
}
