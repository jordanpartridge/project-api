<?php

namespace App\Models\GitHub;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Language extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'color', 'type'
    ];

    public function repositories()
    {
        return $this->hasMany(Repository::class, 'language_id');
    }
}
