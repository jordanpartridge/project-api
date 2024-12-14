<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Observation extends Model
{
    protected $fillable = ['content', 'confidence'];

    public function entity()
    {
        return $this->belongsTo(Entity::class);
    }
}