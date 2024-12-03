<?php

namespace App\Models;

use App\Observers\CommitObserver;
use Glhd\Bits\Database\HasSnowflakes;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

#[ObservedBy([CommitObserver::class])]

class Commit extends Model
{
    use HasFactory;
    use HasSnowflakes;

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
}
