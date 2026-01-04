<?php

namespace App\Models;

use App\Models\Traits\HasStandardBehavior;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Documentation Model
 *
 * @property int $id
 * @property string $title
 * @property string $content
 * @property string $category
 * @property int $order
 * @property bool $is_published
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 *
 * @method static Builder|Documentation published()
 */
class Documentation extends DataModel
{
    use HasStandardBehavior;

    protected $table = 'documentation';

    protected $fillable = [
        'title',
        'content',
        'category',
        'order',
        'is_published',
    ];

    protected $casts = [
        'title' => 'string',
        'content' => 'string',
        'category' => 'string',
        'order' => 'integer',
        'is_published' => 'boolean',
    ];

    /**
     * Get the predefined categories for documentation.
     *
     * @return array<string>
     */
    public static function categories(): array
    {
        return [
            'Laravel',
            'PHP',
            'JavaScript',
            'Vue',
            'React',
            'Node',
        ];
    }

    /**
     * Scope query to only include published documentation.
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true);
    }
}