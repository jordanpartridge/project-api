<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class File extends Model
{
    use HasFactory;

    protected $fillable = [
        'repo_id',
        'filename',
        'content',
        'sha',
        'additions',
        'changes',
        'deletions',
        'size',
        'status',
    ];

    public function repo(): BelongsTo
    {
        return $this->belongsTo(Repo::class);
    }
}
