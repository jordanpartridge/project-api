# Documentation Model Documentation

## 1. Model Overview
The Documentation model is a Laravel Eloquent model that manages documentation entries in the application. It includes features for sluggable content, soft deletes, and activity logging. The model is designed to handle documentation content with categorization, ordering, and publishing capabilities.

## 2. Database Schema
```sql
CREATE TABLE documentation (
    id bigint unsigned NOT NULL AUTO_INCREMENT,
    title varchar(255) NOT NULL,
    slug varchar(255) NOT NULL,
    content text,
    category varchar(255),
    order integer,
    is_published boolean DEFAULT false,
    meta_data json,
    created_at timestamp NULL DEFAULT NULL,
    updated_at timestamp NULL DEFAULT NULL,
    deleted_at timestamp NULL DEFAULT NULL,
    PRIMARY KEY (id)
);
```

## 3. Properties & Fields
### Fillable Properties
```php
protected $fillable = [
    'title',
    'slug',
    'content',
    'category',
    'order',
    'is_published',
    'meta_data'
];
```

### Type Casting
```php
protected $casts = [
    'is_published' => 'boolean',
    'meta_data' => 'array',
    'order' => 'integer',
    'deleted_at' => 'datetime'
];
```

## 4. Relationships
- `activities`: MorphMany relationship for activity logging
```php
public function activities()
{
    return $this->morphMany(Activity::class, 'subject');
}
```

## 5. Methods
The model inherits methods from the following traits:
- `HasSlug`: Provides slug generation functionality
- `SoftDeletes`: Enables soft deletion of records

## 6. Security Considerations
1. Input Validation
   - Validate all input data before saving
   - Sanitize content to prevent XSS attacks
   - Ensure proper access control for published/unpublished content

2. Data Protection
   - Use proper authentication and authorization
   - Implement rate limiting for API endpoints
   - Validate meta_data JSON structure

## 7. Best Practices
1. Content Management
   - Always generate unique slugs for documentation entries
   - Maintain proper ordering within categories
   - Use meta_data for additional, flexible attributes

2. Query Optimization
   - Index frequently queried columns (slug, category, is_published)
   - Use eager loading when accessing relationships
   - Implement caching for frequently accessed documentation

## 8. Code Examples

### Creating Documentation
```php
$doc = Documentation::create([
    'title' => 'Getting Started',
    'content' => 'Your content here...',
    'category' => 'user-guide',
    'order' => 1,
    'is_published' => true,
    'meta_data' => [
        'author' => 'John Doe',
        'version' => '1.0'
    ]
]);
```

### Querying Documentation
```php
// Get all published documentation
$published = Documentation::where('is_published', true)->get();

// Get documentation by category
$categoryDocs = Documentation::where('category', 'user-guide')
    ->orderBy('order')
    ->get();

// Find by slug
$doc = Documentation::where('slug', 'getting-started')->first();
```

### Updating Documentation
```php
$doc = Documentation::find(1);
$doc->update([
    'content' => 'Updated content...',
    'is_published' => true
]);
```

### Soft Delete
```php
// Soft delete
$doc->delete();

// Restore soft deleted documentation
$doc->restore();

// Include soft deleted items in query
Documentation::withTrashed()->get();
```

This documentation provides a comprehensive overview of the Documentation model's functionality and usage. For specific implementation details or custom requirements, please refer to the application's business logic and requirements.