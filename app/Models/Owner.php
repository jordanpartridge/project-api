<?php

namespace App\Models;

use Database\Factories\OwnerFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Owner extends Model
{
    /** @use HasFactory<OwnerFactory> */
    use HasFactory;

    use SoftDeletes;

    protected $fillable = [
        'login',
        'type',
        'avatar_url',
        'html_url',
    ];
}
