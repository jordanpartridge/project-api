<?php

namespace App\Models;

use Glhd\Bits\Database\HasSnowflakes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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

    public function files()
    {
        return $this->belongsToMany(File::class)->withPivot('status', 'additions', 'deletions', 'changes');
    }
}
