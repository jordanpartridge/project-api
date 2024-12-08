<?php

namespace App\Models;

/** @use HasFactory<\Database\Factories\FileVersionFactory> */
class FileVersion extends JoinModel
{
    protected $table = 'file_versions';

    protected $fillable = [
        'commit_id',
        'file_id',
        'status',
        'additions',
        'deletions',
        'changes',
    ];

    protected $casts = [
        'additions' => 'integer',
        'deletions' => 'integer',
        'changes' => 'integer',
    ];
}
