<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PullRequest extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'github_id',
        'title',
        'body',
        'state',
        'repo_id',
        'author_id',
        'merged_by_id',
        'project_card_id',
        'closed_at',
        'merged_at',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'closed_at' => 'timestamp',
        'merged_at' => 'timestamp',
    ];
}
