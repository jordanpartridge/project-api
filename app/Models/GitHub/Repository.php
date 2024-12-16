<?php

namespace App\Models\GitHub;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Repository extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'github_id', 'owner_id', 'language_id', 'name', 'full_name', 
        'description', 'private', 'fork', 'url', 'html_url', 
        'clone_url', 'ssh_url', 'homepage', 'size', 'stars_count', 
        'watchers_count', 'forks_count', 'open_issues_count', 
        'default_branch', 'topics', 'archived', 'disabled', 'visibility'
    ];

    protected $casts = [
        'github_id' => 'integer',
        'owner_id' => 'integer', 
        'language_id' => 'integer',
        'private' => 'boolean',
        'fork' => 'boolean',
        'size' => 'integer',
        'stars_count' => 'integer',
        'watchers_count' => 'integer',
        'forks_count' => 'integer',
        'open_issues_count' => 'integer',
        'topics' => 'array',
        'archived' => 'boolean',
        'disabled' => 'boolean'
    ];

    public function owner()
    {
        return $this->belongsTo(Owner::class, 'owner_id');
    }

    public function language()
    {
        return $this->belongsTo(Language::class, 'language_id');
    }

    public function commits()
    {
        return $this->hasMany(Commit::class, 'repo_id');
    }

    public function issues()
    {
        return $this->hasMany(Issue::class, 'repo_id');
    }
}
