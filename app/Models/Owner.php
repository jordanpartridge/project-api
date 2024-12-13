<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Owner extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'github_id',
        'login',
        'type',
        'avatar_url',
        'html_url',
    ];

    public function repos(): HasMany
    {
        return $this->hasMany(Repo::class);
    }

    public function issues(): HasMany
    {
        return $this->hasMany(Issue::class, 'author_id');
    }

    public function pullRequests(): HasMany
    {
        return $this->hasMany(PullRequest::class, 'author_id');
    }
}
