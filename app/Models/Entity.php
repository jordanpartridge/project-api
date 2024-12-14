<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Entity extends Model
{
    protected $fillable = ['name', 'type'];

    public function observations()
    {
        return $this->hasMany(Observation::class);
    }
}