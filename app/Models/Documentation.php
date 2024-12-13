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

    /**
     * @return array
     */
    public static  function categories(): array
    {
        return ['Laravel', 'PHP', 'JavaScript', 'Vue', 'React', 'Node', 'Laravel', 'PHP', 'JavaScript', 'Vue', 'React', 'Node'];
    }

    /**
     * @param $query
     * @return mixed
     */
    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }
}
