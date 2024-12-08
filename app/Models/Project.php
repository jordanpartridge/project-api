<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\LogOptions;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

/**
 * @property string $name
 * @property string $slug
 * @property string|null $description
 * @property string|null $long_description
 * @property string $status
 * @property string|null $featured_image
 * @property string|null $demo_url
 * @property bool $is_featured
 * @property int|null $display_order
 * @property array|null $meta_data
 *
 * @method static \Database\Factories\ProjectFactory factory()
 */
class Project extends DataModel
{
    use HasFactory;
    use HasSlug;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'long_description',
        'status',
        'featured_image',
        'demo_url',
        'is_featured',
        'display_order',
        'meta_data',
    ];

    protected $casts = [
        'is_featured' => 'boolean',
        'meta_data' => 'array',
    ];

    /**
     * Get the options for generating the slug.
     */
    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug');
    }

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'name',
                'slug',
                'description',
                'status',
                'is_featured',
            ])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    /**
     * Get the repositories associated with the project.
     */
    public function repos(): HasMany
    {
        return $this->hasMany(Repo::class);
    }
}
