# User Model Documentation

## 1. Model Overview
The User model is a core model in the application that handles user management and authentication. It is located in the `App\Models` namespace and uses several traits for extended functionality including API tokens, factory support, role management, and notifications.

## 2. Database Schema
The model corresponds to the `users` table in the database with the following structure:

```sql
CREATE TABLE users (
    id bigint unsigned AUTO_INCREMENT PRIMARY KEY,
    name varchar(255),
    email varchar(255) UNIQUE,
    password varchar(255),
    email_verified_at timestamp NULL,
    created_at timestamp NULL,
    updated_at timestamp NULL
);
```

## 3. Properties & Fields

### Fillable Attributes
```php
protected $fillable = [
    'name',
    'email',
    'password'
];
```

### Type Casting
```php
protected $casts = [
    'id' => 'int',
    'email_verified_at' => 'datetime',
    'password' => 'hashed'
];
```

### Model Properties
- `incrementing`: Controls auto-incrementing IDs
- `timestamps`: Enables timestamp management
- `preventsLazyLoading`: Controls lazy loading prevention
- `usesUniqueIds`: Indicates unique ID usage
- `snakeAttributes`: Controls array key casing
- `exists`: Indicates model existence state
- `wasRecentlyCreated`: Tracks new model creation

## 4. Relationships

### Roles
```php
/**
 * A model may have multiple roles.
 */
public function roles(): BelongsToMany
```

### Permissions
```php
/**
 * A model may have multiple direct permissions.
 */
public function permissions(): BelongsToMany
```

## 5. Methods
The model inherits methods from its used traits:
- API token management (HasApiTokens)
- Factory creation (HasFactory)
- Role management (HasRoles)
- Notification handling (Notifiable)

## 6. Security Considerations
- Passwords are automatically hashed (via casting)
- Email verification support is built-in
- API tokens are managed securely through Sanctum
- Role-based access control through Spatie Permissions

## 7. Best Practices
1. Always use mass assignment protection (`$fillable` or `$guarded`)
2. Implement email verification when required
3. Use proper password hashing (automatically handled)
4. Utilize role-based permissions for access control
5. Enable proper API token management for API authentication

## 8. Code Examples

### Creating a New User
```php
$user = User::create([
    'name' => 'John Doe',
    'email' => 'john@example.com',
    'password' => bcrypt('password')
]);
```

### Assigning Roles
```php
$user->assignRole('admin');
```

### Checking Permissions
```php
if ($user->hasPermissionTo('edit articles')) {
    // Perform action
}
```

### API Token Creation
```php
$token = $user->createToken('api-token')->plainTextToken;
```

### Sending Notifications
```php
$user->notify(new WelcomeNotification());
```

This documentation provides a comprehensive overview of the User model's capabilities and usage within the application. For specific implementation details, consult the relevant trait documentation and Laravel's official documentation.