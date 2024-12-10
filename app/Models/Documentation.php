<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Documentation extends Model
{
    use HasFactory;

    protected $table = 'documentation';

    protected $fillable = [
        'title',
        'content',
        'category',
        'order',
        'is_published',
    ];

    protected $casts = [
        'is_published' => 'boolean',
    ];
}
