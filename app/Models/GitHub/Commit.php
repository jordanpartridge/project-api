<?php

namespace App\Models\GitHub;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Commit extends Model
{
    use HasFactory;

    protected $fillable = [
        'repo_id', 'sha', 'message', 'author_id', 'committer_id', 
        'tree_sha', 'parent_shas', 'html_url', 'verified', 
        'additions', 'deletions', 'total_changes', 'committed_at'
    ];

    protected $casts = [
        'repo_id' => 'integer',
        'author_id' => 'integer',
        'committer_id' => 'integer',
        'parent_shas' => 'array',
        'verified' => 'boolean',
        'additions' => 'integer',
        'deletions' => 'integer',
        'total_changes' => 'integer',
        'committed_at' => 'datetime'
    ];

    public function repository()
    {
        return $this->belongsTo(Repository::class, 'repo_id');
    }

    public function author()
    {
        return $this->belongsTo(Owner::class, 'author_id');
    }

    public function committer()
    {
        return $this->belongsTo(Owner::class, 'committer_id');
    }
}
