# Project Model Documentation

## 1. Model Overview
The Project model represents projects in the application. It includes support for slugs, factory creation, and handles various project-related attributes including featured status and metadata.

## 2. Database Schema
```sql
CREATE TABLE projects (
    id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
    name varchar(255) NOT NULL,
    slug varchar(255) NOT NULL,
    description text,
    long_description text,
    status varchar(255),
    featured_image varchar(255),
    demo_url varchar(255),
    is_featured boolean DEFAULT false,
    display_order integer,
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
    'name',
    'slug',
    'description',
    'long_description',
    'status',
    'featured_image',
    'demo_url',
    'is_featured',
    'display_order',
    'meta_data'
];
```

### Type Casting
```php
protected $casts = [
    'is_featured' => 'boolean',
    'meta_data' => 'array',
    'deleted_at' => 'datetime'
];
```

## 4. Relationships

### Has One
- `repo()` - Associated repository relationship

### Morph Many
- `activities()` - Morphed activities relationship

## 5. Methods

The model includes standard Eloquent model methods and uses the following traits:
- `HasFactory` - For factory pattern support
- `HasSlug` - For automatic slug generation

## 6. Security Considerations

1. Input Validation
   - Ensure proper validation of all fillable fields
   - Sanitize meta_data before storage
   - Validate featured_image and demo_url as valid URLs

2. Access Control
   - Implement proper authorization for project creation/modification
   - Control access to featured project status changes
   - Protect sensitive meta_data information

## 7. Best Practices

1. Slug Generation
   - Use unique slugs for SEO-friendly URLs
   - Implement proper slug validation

2. Data Management
   - Use soft deletes for project removal
   - Maintain proper ordering with display_order
   - Keep meta_data structured and documented

3. Performance
   - Eager load relationships when needed
   - Index frequently queried columns
   - Cache frequently accessed projects

## 8. Code Examples

### Creating a New Project
```php
$project = Project::create([
    'name' => 'My New Project',
    'description' => 'Project description',
    'status' => 'active',
    'is_featured' => false,
    'meta_data' => [
        'category' => 'web',
        'technologies' => ['Laravel', 'Vue.js']
    ]
]);
```

### Querying Featured Projects
```php
$featuredProjects = Project::where('is_featured', true)
    ->orderBy('display_order')
    ->with('repo')
    ->get();
```

### Updating Project Status
```php
$project->update([
    'status' => 'completed',
    'meta_data' => array_merge($project->meta_data, [
        'completion_date' => now()
    ])
]);
```

### Soft Delete Project
```php
$project->delete(); // Uses soft delete
```

This documentation provides a comprehensive overview of the Project model's functionality and usage within the application. For specific implementation details or custom requirements, please refer to the application's specific business logic and requirements.