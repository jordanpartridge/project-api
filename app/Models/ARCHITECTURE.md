# Model Architecture

## Base Models

### BaseModel
Base model that all domain models extend. Includes:
- HasFactory: Factory pattern support
- HasSnowflakes: Snowflake ID generation
- LogsActivity: Activity logging

### DataModel
For domain entities. Extends BaseModel and adds:
- SoftDeletes: Soft deletion support
Use for models that represent core business entities.

### JoinModel
For pivot/join tables. Extends Model directly and includes:
- HasFactory: Factory pattern support
Use only for intermediate tables that connect domain entities.

## Usage Guidelines

### DataModel Usage
Use for models that:
- Represent core domain entities
- Need unique IDs across systems
- Require activity tracking
- Have soft delete requirements

Example models:
- Project
- Repo
- Language
- Owner
- File
- Commit

### JoinModel Usage
Use for models that:
- Connect two or more domain entities
- Don't need their own IDs
- Don't require activity tracking

Example models:
- FileVersion (connects File and Commit)