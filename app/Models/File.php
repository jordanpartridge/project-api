<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class File extends Model
{
    use HasFactory;

    protected $fillable = [
        'repo_id',
        'filename',
        'content',
        'sha',
        'status',
    ];

    public function commits(): belongsToMany
    {
        return $this->belongsToMany(Commit::class, 'file_versions');
    }

    public function repo(): BelongsTo
    {
        return $this->belongsTo(Repo::class);
    }
}
