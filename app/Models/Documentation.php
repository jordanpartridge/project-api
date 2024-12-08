<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Documentation extends DataModel
{
    use HasSlug;
    use SoftDeletes;

    protected $table = 'documentation';

    protected $fillable = [
        'title',
        'slug',
        'content',
        'category',
        'order',
        'is_published',
        'meta_data',
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'meta_data' => 'array',
        'order' => 'integer',
    ];

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('title')
            ->saveSlugsTo('slug');
    }

    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function getUrlAttribute(): string
    {
        return route('docs.show', ['slug' => $this->slug]);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'title',
                'slug',
                'content',
                'category',
                'is_published',
                'order',
            ])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}
