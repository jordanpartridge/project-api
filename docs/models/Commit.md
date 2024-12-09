# Commit Model Documentation

## 1. Model Overview
The Commit model represents repository commits in the application. It's used to track version control commits with their associated metadata like SHA hashes, commit messages, author information, and timestamps.

## 2. Database Schema
```sql
CREATE TABLE commits (
    id bigint PRIMARY KEY,
    sha varchar(255) NOT NULL,
    message text,
    author json,
    committed_at timestamp,
    deleted_at timestamp NULL,
    created_at timestamp,
    updated_at timestamp,
    repo_id bigint FOREIGN KEY
);
```

## 3. Properties & Fields

### Fillable Attributes
```php
protected $fillable = [
    'sha',
    'message',
    'author',
    'committed_at'
];
```

### Attribute Casting
```php
protected $casts = [
    'committed_at' => 'datetime',
    'author' => 'array',
    'deleted_at' => 'datetime'
];
```

## 4. Relationships

### BelongsTo
- `repo()` - Associates the commit with its repository

### BelongsToMany
- `files()` - Manages the relationship between commits and affected files

### MorphMany
- `activities()` - Tracks activities related to the commit

## 5. Methods
The model inherits standard Eloquent model methods and includes relationship methods:
```php
public function repo()
{
    return $this->belongsTo(Repo::class);
}

public function files()
{
    return $this->belongsToMany(File::class);
}

public function activities()
{
    return $this->morphMany(Activity::class, 'subject');
}
```

## 6. Security Considerations
- Ensure proper validation of SHA hashes
- Validate and sanitize commit messages
- Implement proper access control for commit-related operations
- Handle author information securely as it contains sensitive data
- Use soft deletes for maintaining audit trails

## 7. Best Practices
1. Always validate commit data before saving
2. Use type-hinting for method parameters
3. Implement proper error handling
4. Use model events for logging changes
5. Maintain indexes on frequently queried fields
6. Use eager loading when accessing relationships to avoid N+1 queries

## 8. Code Examples

### Creating a New Commit
```php
$commit = Commit::create([
    'sha' => '8f7d3b2e1a...',
    'message' => 'Initial commit',
    'author' => [
        'name' => 'John Doe',
        'email' => 'john@example.com'
    ],
    'committed_at' => now()
]);
```

### Querying Commits
```php
// Find commits by SHA
$commit = Commit::where('sha', $sha)->first();

// Get commits with related data
$commits = Commit::with(['repo', 'files'])
    ->whereDate('committed_at', '>=', $startDate)
    ->get();

// Get commits by author
$commits = Commit::whereJsonContains('author->email', 'john@example.com')
    ->get();
```

### Updating a Commit
```php
$commit->update([
    'message' => 'Updated commit message'
]);
```

### Working with Relationships
```php
// Add files to a commit
$commit->files()->attach($fileIds);

// Get all activities for a commit
$activities = $commit->activities;

// Get repository information
$repository = $commit->repo;
```

Note: This documentation assumes standard Laravel conventions and may need to be adjusted based on specific application requirements and customizations.