# Data Architecture

## Entity Relationship Diagram

```mermaid
erDiagram
    Project ||--o| Repo : has
    Repo ||--|{ Commit : contains
    Repo ||--|{ File : contains
    Repo ||--|| Owner : belongs_to
    Repo ||--|| Language : written_in
    User ||--o{ Project : manages

    Project {
        snowflake id PK
        string name
        string slug
        string description
        timestamp created_at
        timestamp updated_at
    }

    Repo {
        snowflake id PK
        snowflake project_id FK
        snowflake owner_id FK
        snowflake language_id FK
        string name
        string full_name
        string description
        boolean private
        boolean fork
        timestamp pushed_at
        timestamp created_at
        timestamp updated_at
    }

    Commit {
        snowflake id PK
        snowflake repo_id FK
        string sha
        string message
        timestamp committed_at
        string author_name
        string author_email
        timestamp created_at
        timestamp updated_at
    }

    File {
        snowflake id PK
        snowflake repo_id FK
        string name
        string path
        string sha
        integer size
        string type
        timestamp created_at
        timestamp updated_at
    }

    Owner {
        snowflake id PK
        string name
        string type
        timestamp created_at
        timestamp updated_at
    }

    Language {
        snowflake id PK
        string name
        timestamp created_at
        timestamp updated_at
    }

    User {
        snowflake id PK
        string name
        string email
        timestamp email_verified_at
        timestamp created_at
        timestamp updated_at
    }
```

## Model Relationships

### Project
- Has one optional repository (`hasOne(Repo::class)`)
- Belongs to many users (`belongsToMany(User::class)`)

### Repo
- Belongs to one project (`belongsTo(Project::class)`)
- Has many commits (`hasMany(Commit::class)`)
- Has many files (`hasMany(File::class)`)
- Belongs to one owner (`belongsTo(Owner::class)`)
- Belongs to one language (`belongsTo(Language::class)`)

### Commit
- Belongs to one repository (`belongsTo(Repo::class)`)

### File
- Belongs to one repository (`belongsTo(Repo::class)`)

### Owner
- Has many repositories (`hasMany(Repo::class)`)

### Language
- Has many repositories (`hasMany(Repo::class)`)

### User
- Has many projects (`hasMany(Project::class)`)

## Key Design Decisions

### Identifiers
- Using Snowflake IDs for distributed scalability
- All models use timestamps for tracking creation and updates

### Project Organization
- Projects can exist without repositories (optional relationship)
- Repositories are tied to a single project
- Files and commits are always associated with a repository

### Activity Tracking
- All models implement activity logging via Spatie's Activity Log
- Projects use slug-based routing for clean URLs

## Common Queries

### Project with Repository Info
```php
Project::with('repo')->get();
```

### Repository with All Related Data
```php
Repo::with(['project', 'commits', 'files', 'owner', 'language'])->get();
```

### User's Projects with Repositories
```php
User::with('projects.repo')->get();
```