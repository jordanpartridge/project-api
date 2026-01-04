<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Owner extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'github_id',
        'type',
        'login',
        'name',
        'email',
        'avatar_url',
    ];

    public function repositories(): HasMany
    {
        return $this->hasMany(Repository::class);
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