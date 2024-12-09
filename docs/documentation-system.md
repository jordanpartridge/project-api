# Documentation System

## Overview

The documentation system provides a flexible way to manage and display documentation content within the project-api application. It uses Laravel Livewire for dynamic interactions and Laravel Folio for routing.

## Features

- Category-based organization
- Markdown content support with safe rendering
- Dynamic filtering
- Pagination support
- Secure content handling

## Components

### 1. Documentation Model

Located in `app/Models/Documentation.php`

```php
use App\Models\Documentation;

// Available categories
Documentation::CATEGORY_GUIDES    // 'guides'
Documentation::CATEGORY_TUTORIALS // 'tutorials'
Documentation::CATEGORY_API       // 'api'
Documentation::CATEGORY_EXAMPLES  // 'examples'

// Get all categories
$categories = Documentation::categories();
```

#### Properties
- `title` - Document title (unique)
- `slug` - URL-friendly identifier (auto-generated)
- `content` - Markdown content
- `category` - Document category
- `order` - Display order
- `is_published` - Publication status
- `meta_data` - Additional JSON data

### 2. Markdown Service

Located in `app/Services/MarkdownService.php`

Handles secure markdown conversion with the following features:
- HTML sanitization
- Unsafe link prevention
- CommonMark compliance

Usage:
```php
$markdownService = new MarkdownService();
$html = $markdownService->convertToHtml($markdownContent);
```

### 3. Livewire Components

#### Documentation Settings (`app/Livewire/Pages/Docs/Settings.php`)
Handles documentation creation and editing with:
- Validation rules
- Secure content processing
- Category management

#### Documentation Index (`app/Livewire/Pages/Docs/Index.php`)
Manages documentation display with:
- Category filtering
- Pagination
- Query string support

## Validation Rules

### Documentation Creation/Update
- Title: Required, unique, max 255 characters
- Content: Required, markdown string
- Category: Must be one of the predefined categories
- Order: Required integer, minimum 0
- Is Published: Boolean
- Meta Data: Optional array

## Usage Examples

### Creating Documentation
```php
use App\Models\Documentation;

Documentation::create([
    'title' => 'Getting Started',
    'content' => '# Welcome to Project API...',
    'category' => Documentation::CATEGORY_GUIDES,
    'order' => 1,
    'is_published' => true
]);
```

### Filtering Documentation
```php
use App\Models\Documentation;

// Get published docs in a category
$docs = Documentation::published()
    ->where('category', Documentation::CATEGORY_TUTORIALS)
    ->orderBy('order')
    ->paginate(10);
```

## Security Considerations

1. Markdown Processing
   - All markdown is sanitized before rendering
   - HTML input is stripped
   - Unsafe links are disabled

2. Input Validation
   - Category values are strictly controlled
   - Title uniqueness is enforced
   - Content is sanitized before storage

## Testing

Key test scenarios are available in `tests/Feature/DocumentationTest.php`:
- Document creation validation
- Category filtering
- Pagination behavior
- Markdown rendering security

## Future Considerations

1. Category Management
   - Dynamic category creation
   - Category hierarchies
   - Category-specific templates

2. Content Features
   - Version history
   - Collaborative editing
   - Change tracking

3. Search Capabilities
   - Full-text search
   - Tag-based search
   - Related content suggestions