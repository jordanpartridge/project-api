# Project API Documentation

## Overview

This API provides access to programming language information and their associated repositories. The API follows RESTful principles and uses Laravel Sanctum for authentication.

## Base URL

```
/api/v1
```

All endpoints are prefixed with this base URL.

## Authentication

The API uses Laravel Sanctum for authentication. All endpoints require authentication via a bearer token.

```http
Authorization: Bearer <your-token>
```

## Endpoints

### User Information

#### Get Authenticated User
```http
GET /users
```

Returns information about the currently authenticated user.

**Response Format:**
```json
{
    "data": {
        // User information
    }
}
```

### Languages

#### List All Languages
```http
GET /languages
```

Returns a list of all programming languages.

**Response Format:**
```json
{
    "data": [
        {
            "name": "string"
        }
    ]
}
```

#### Get Single Language
```http
GET /languages/{language}
```

Returns information about a specific programming language.

**Parameters:**
- `language` (string, required): The name of the language (used as route key)

**Response Format:**
```json
{
    "data": {
        "name": "string"
    }
}
```

### Repositories

#### List Language Repositories
```http
GET /languages/{language}/repos
```

Returns a list of repositories for a specific programming language.

**Parameters:**
- `language` (string, optional): The name of the language to filter repositories

**Response Format:**
```json
{
    "data": [
        {
            // Repository information
        }
    ]
}
```

## Models

### Language Model

The Language model represents a programming language and has the following properties:

**Fields:**
- `name` (string): The name of the programming language

**Relationships:**
- `repos`: HasMany relationship with Repository model

**Features:**
- Uses route model binding with the `name` field
- Implements activity logging using Spatie's Activity Log package
- Logs changes to the `name` field only when modified

## Error Handling

The API returns standard HTTP status codes and JSON error responses:

- 200: Success
- 401: Unauthorized
- 403: Forbidden
- 404: Not Found
- 422: Validation Error
- 500: Server Error

Error responses follow this format:
```json
{
    "message": "Error description",
    "errors": {
        // Detailed error information (for validation errors)
    }
}
```

## Rate Limiting

The API implements Laravel's default rate limiting. Specific limits should be defined in the project configuration.

## Data Models

### Language

```php
Language {
    // Properties
    name: string

    // Relationships
    repos: HasMany<Repo>
}
```

## Versioning

The API is versioned using URL prefixing (v1). Breaking changes will be introduced in new versions while maintaining backward compatibility in existing versions.

## Activity Logging

The API uses Spatie's Activity Log package to track changes to resources. For the Language model, the following actions are logged:
- Changes to the `name` field
- Only dirty (changed) attributes are logged
- Empty logs are not submitted

## Testing

The API includes comprehensive tests for all endpoints. Tests can be run using PHPUnit:

```bash
php artisan test
```

## Development Setup with Laravel Herd

### Prerequisites
1. Install [Laravel Herd](https://herd.laravel.com) for your operating system
2. Ensure Herd is running (check the menu bar icon)

### Initial Setup
1. Clone the repository
2. Install dependencies: `composer install` (this will automatically create your .env file)
3. Generate application key: `php artisan key:generate`
4. Run migrations: `php artisan migrate`

### Configuring Herd

#### Using herd.yml
This project includes a `herd.yml` configuration file that Herd will automatically detect. The basic configuration looks like:

```yaml
sites:
    - domain: project-api.test
      path: /Users/YourUsername/Sites/project-api/public
```

#### Manual Site Setup
If you need to manually configure the site:

1. Open Laravel Herd
2. Click "Park a Site" or use the menu bar icon
3. Configure your site:
   - Domain: `project-api.test`
   - Site Path: Select the `public` folder in your project directory
4. Click "Park Site"

#### Accessing the Site
- Your API will be available at `http://project-api.test`
- If you need HTTPS, Herd automatically provides secure certificates
- Access via HTTPS: `https://project-api.test`

#### Troubleshooting Herd
1. **Site Not Found**
   - Verify Herd is running (check menu bar icon)
   - Ensure the path in `herd.yml` matches your actual project path
   - Try re-parking the site

2. **SSL Issues**
   - Open Herd preferences
   - Click "Install CA Certificate" if not already installed
   - Restart your browser

3. **Port Conflicts**
   - Check if other services (like Valet, MAMP, etc.) are using ports 80/443
   - Quit conflicting services

4. **DNS Resolution**
   - Herd automatically configures `.test` domains
   - If not working, try:
     ```bash
     sudo killall -HUP mDNSResponder
     ```

### Database Access
- Herd includes MySQL by default
- Default credentials:
  - Host: 127.0.0.1
  - Port: 3306
  - Username: root
  - Password: (blank)

### PHP Version
- Herd comes with PHP 8.2 by default
- You can switch PHP versions through the Herd menu bar icon if needed

## Additional Resources

- [Laravel Documentation](https://laravel.com/docs)
- [Laravel Sanctum Documentation](https://laravel.com/docs/sanctum)
- [Spatie Activity Log Package](https://spatie.be/docs/laravel-activitylog)