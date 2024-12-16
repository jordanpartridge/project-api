<?php

namespace App\Models\GitHub;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Issue extends Model
{
    use HasFactory;

    protected $fillable = [
        'repo_id', 'github_id', 'number', 'title', 'body', 
        'state', 'author_id', 'assignee_ids', 'labels', 
        'comments_count', 'closed_at'
    ];

    protected $casts = [
        'repo_id' => 'integer',
        'github_id' => 'integer',
        'number' => 'integer',
        'state' => 'string',
        'author_id' => 'integer',
        'assignee_ids' => 'array',
        'labels' => 'array',
        'comments_count' => 'integer',
        'closed_at' => 'datetime'
    ];

    public function repository()
    {
        return $this->belongsTo(Repository::class, 'repo_id');
    }

    public function author()
    {
        return $this->belongsTo(Owner::class, 'author_id');
    }

    public function assignees()
    {
        return $this->hasManyThrough(Owner::class, 'assignee_ids', 'id', 'id');
    }
}
