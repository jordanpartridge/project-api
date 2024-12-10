# Project-API Context

This context file provides instructions for Claude to operate in project-api mode.

## Project Overview
- Laravel API for managing software projects
- Version control integration
- Sophisticated data management
- Filament admin interface

## Knowledge Access Instructions
1. To access shared patterns:
   ```javascript
   const patterns = await window.fs.readFile('/Sites/claude-knowledge/patterns/laravel-patterns.json', { encoding: 'utf8' });
   ```

2. To access project-specific knowledge:
   ```javascript
   const projectKnowledge = await window.fs.readFile('/Sites/project-backups/project-api/knowledge/current-state.json', { encoding: 'utf8' });
   ```

## Core Patterns and Practices
Refer to /Sites/claude-knowledge/patterns/laravel-patterns.json for:
- Service pattern implementation
- Error handling approaches
- Database interaction patterns
- Testing strategies

## Project-Specific Details
Refer to /Sites/project-backups/project-api/knowledge/current-state.json for:
- Current implementation details
- Active features
- Recent changes
- Known issues

## Working Directory
Always operate relative to: /Users/jordanpartridge/Sites/project-api

## File Access Pattern
When accessing project files:
```php
// Example: Reading a model file
const modelContent = await window.fs.readFile(${config.project.base_path}/app/Models/Project.php);
```

Remember to maintain:
1. Consistent use of established patterns
2. Reference to stored knowledge
3. Project-specific context awareness