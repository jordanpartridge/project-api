<?php

namespace App\Models\GitHub;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Owner extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'github_id', 'login', 'type', 'name', 'avatar_url', 'html_url', 
        'company', 'blog', 'location', 'email', 'bio', 
        'public_repos', 'public_gists', 'followers', 'following'
    ];

    protected $casts = [
        'github_id' => 'integer',
        'public_repos' => 'integer',
        'public_gists' => 'integer',
        'followers' => 'integer',
        'following' => 'integer'
    ];

    public function repositories()
    {
        return $this->hasMany(Repository::class, 'owner_id');
    }

    public function authoredCommits()
    {
        return $this->hasMany(Commit::class, 'author_id');
    }

    public function committedCommits()
    {
        return $this->hasMany(Commit::class, 'committer_id');
    }

    public function issues()
    {
        return $this->hasMany(Issue::class, 'author_id');
    }
}
