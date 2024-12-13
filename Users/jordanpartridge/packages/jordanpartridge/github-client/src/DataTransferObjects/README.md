# GitHub Client Data Transfer Objects (DTOs)

## Overview

This package provides a comprehensive set of Data Transfer Objects (DTOs) for interacting with the GitHub API. The DTOs are designed to:

- Provide type-safe representations of GitHub resources
- Offer consistent data transformation
- Support both REST and GraphQL API responses
- Implement validation and transformation interfaces

## Key Features

- Consistent naming conventions
- Immutable data structures
- Built-in API response transformation
- Comprehensive validation
- Nested, composable DTOs

## Directory Structure

- `Commits/`: Commit-related DTOs
- `PullRequests/`: Pull Request DTOs
- `Repositories/`: Repository-related DTOs
- `Users/`: User and Git User DTOs
- `Workflows/`: GitHub Actions workflow DTOs
- `Shared/`: Common utility DTOs
- `Contracts/`: Interfaces for data transformation

## Usage Example

```php
// Transform API response to DTO
$user = UserDto::fromApiResponse($apiResponse);

// Access type-safe properties
echo $user->name;
echo $user->email;
```

## Contributing

1. Follow existing DTO structure
2. Implement `DataTransformableInterface`
3. Use Spatie LaravelData attributes
4. Provide comprehensive validation
5. Create meaningful transformation methods
