# GitHub Projects Integration

## Overview
The GitHub Projects integration allows seamless synchronization between your Project API instance and GitHub Projects. This feature enables project managers to leverage GitHub's project management tools while maintaining centralized control through the Project API.

## Features
- Two-way sync with GitHub Projects
- GitHub Projects visibility management
- Automatic sync scheduling
- Custom field mapping
- Filament admin panel integration

## Configuration

### Environment Variables
```env
GITHUB_TOKEN=your_github_token
GITHUB_SYNC_INTERVAL=60 # in minutes
```

### GitHub Token Permissions
Your GitHub token needs the following permissions:
- `project:read`
- `project:write`

## Usage

### Through Filament Admin
1. Navigate to Projects in the admin panel
2. Create or edit a project
3. Fill in the GitHub Project Number
4. Set visibility and sync preferences
5. Save the project

### Via API

```php
// Sync a project manually
$project = Project::find($id);
$service = app(GitHubProjectService::class);
$service->syncProject($project);

// Check sync status
$needsSync = $project->needsSync();

// Get GitHub Project data
$githubSettings = $project->github_project_settings;
```

### API Endpoints

#### Get Project with GitHub Data
```http
GET /api/v1/projects/{id}
```

Response:
```json
{
    "id": 1,
    "name": "My Project",
    "github_project_number": "123",
    "github_project_visibility": "private",
    "github_project_settings": {
        "url": "https://github.com/users/owner/projects/123",
        "items": [...]
    },
    "last_synced_at": "2024-12-10T12:00:00Z"
}
```

#### Update GitHub Project Settings
```http
PATCH /api/v1/projects/{id}/github-settings
```

Request Body:
```json
{
    "github_project_number": "123",
    "github_project_visibility": "private",
    "sync_enabled": true
}
```

## Error Handling

The integration handles various error cases:

1. Invalid GitHub Project Number
```json
{
    "error": "github_project_not_found",
    "message": "GitHub Project #123 not found"
}
```

2. Sync Failures
```json
{
    "error": "sync_failed",
    "message": "Failed to sync with GitHub Project",
    "details": {...}
}
```

## Best Practices

1. **Sync Frequency**
   - Don't set sync intervals too low to avoid API rate limits
   - Consider using webhooks for real-time updates

2. **Error Handling**
   - Implement retry logic for failed syncs
   - Log all sync attempts and failures

3. **Data Integrity**
   - Always validate GitHub Project numbers
   - Keep local copies of important data

## Command Reference

### Console Commands
```bash
# Sync all projects
php artisan projects:sync-github

# Sync specific project
php artisan projects:sync-github --project=123

# Check sync status
php artisan projects:github-status
```

## Troubleshooting

### Common Issues

1. **Sync Not Working**
   - Verify GitHub token permissions
   - Check GitHub API rate limits
   - Ensure project number is correct

2. **Missing Data**
   - Verify GitHub Project visibility settings
   - Check field mapping configuration
   - Review sync logs for errors

3. **Performance Issues**
   - Adjust sync interval
   - Implement caching where appropriate
   - Monitor API usage

## Contributing

When contributing to the GitHub Projects integration:

1. Add tests for any new features
2. Update documentation for changes
3. Follow existing code style
4. Use provided service classes for GitHub interactions
