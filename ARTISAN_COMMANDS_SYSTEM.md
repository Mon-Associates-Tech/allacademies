# Artisan Commands Management System

## Overview
A comprehensive system for managing and executing artisan commands and jobs from the web interface with namespace control and full audit logging.

## Features

### 1. Automatic Namespace Detection
- **Auto-Discovery**: Automatically detects all available command namespaces
- **Smart Categorization**: Distinguishes between user-defined and Laravel core commands
- **One-Click Sync**: "Sync Namespaces" button to refresh available commands
- **User-Defined Commands**: Enabled by default (e.g., `users:*`, `books:*`, `students:*`)
- **Laravel Core Commands**: Disabled by default for safety
- **Destructive Commands**: Clearly marked (e.g., `db:*`, `migrate:*`, `schema:*`)
- **Toggle Control**: Enable/disable entire namespaces with one click

### 2. Command Execution
- Browse all enabled commands grouped by namespace
- Auto-detect command arguments and options
- Dynamic input fields for all parameters
- Real-time output display
- Error handling and reporting

### 3. Job Dispatching
- List all jobs from `app/Jobs` directory
- Dispatch jobs without constructor parameters
- Warning for jobs requiring parameters
- Execution tracking

### 4. Audit Logging
Every command execution is logged with:
- User who executed the command
- Command name and arguments
- Execution output
- Success/failure status
- Error messages (if any)
- IP address
- User agent
- Timestamp

### 5. Security
- Only superadmins and owners can access
- Gate-based authorization
- Namespace filtering prevents dangerous commands
- Full audit trail for accountability

## Database Tables

### `artisan_command_logs`
Stores execution history of all commands and jobs.

**Columns:**
- `id`: Primary key
- `user_id`: User who executed the command
- `command`: Command name
- `arguments`: JSON of command arguments
- `output`: Command output
- `status`: success/failed
- `error_message`: Error details if failed
- `ip_address`: User's IP address
- `user_agent`: User's browser/client
- `executed_at`: Execution timestamp

### `artisan_command_namespaces`
Controls which command namespaces are available.

**Columns:**
- `id`: Primary key
- `namespace`: Command namespace (e.g., 'users', 'cache', 'db')
- `label`: Human-readable label
- `description`: Description of the namespace
- `is_enabled`: Whether namespace is enabled
- `is_laravel_core`: Whether it's a Laravel core namespace
- `sort_order`: Display order

## Default Namespaces

### User-Defined (Enabled by Default)
- `users` - User Management
- `app` - Application-specific commands

### Laravel Core (Disabled by Default)
- `cache` - Cache management
- `config` - Configuration cache
- `route` - Route management
- `view` - View cache
- `queue` - Queue management
- `migrate` - Database migrations
- `db` - Database commands (DESTRUCTIVE)
- `optimize` - Application optimization
- `storage` - Storage management
- `schedule` - Task scheduling

## Usage

### Accessing the System
1. Navigate to `/artisan-commands`
2. Or click "System Commands" in the sidebar (Owner Tools section)

### Managing Namespaces
1. Click "Manage Namespaces" button
2. Toggle namespaces on/off as needed
3. User-defined commands are separated from Laravel core
4. Destructive commands are clearly marked

### Running Commands
1. Select a command from the dropdown
2. Fill in any required arguments/options
3. Click "Run Command"
4. View output in terminal-style display

### Dispatching Jobs
1. Switch to "Queue Jobs" tab
2. Select a job from the dropdown
3. Click "Dispatch Job"
4. View confirmation message

### Viewing Audit Log
1. Click "View Audit Log" button
2. See all command executions with details
3. Filter by user, status, or date
4. Paginated for easy browsing

## Models

### ArtisanCommandLog
```php
// Relationships
$log->user; // User who executed

// Accessors
$log->formatted_arguments; // Human-readable arguments
```

### ArtisanCommandNamespace
```php
// Scopes
ArtisanCommandNamespace::enabled(); // Only enabled namespaces
ArtisanCommandNamespace::userDefined(); // User-defined only
ArtisanCommandNamespace::laravelCore(); // Laravel core only

// Static Methods
ArtisanCommandNamespace::getEnabledNamespaces(); // Array of enabled namespace names
```

## Seeding

To seed default namespaces:
```bash
php artisan db:seed --class=ArtisanCommandNamespaceSeeder
```

## Security Considerations

1. **Access Control**: Only superadmins and owners can access
2. **Namespace Filtering**: Dangerous commands disabled by default
3. **Audit Trail**: Every execution is logged
4. **IP Tracking**: Know where commands were executed from
5. **Error Logging**: Failed commands are tracked

## Adding New Namespaces

To add a new namespace programmatically:
```php
ArtisanCommandNamespace::create([
    'namespace' => 'custom',
    'label' => 'Custom Commands',
    'description' => 'Custom application commands',
    'is_enabled' => true,
    'is_laravel_core' => false,
    'sort_order' => 100,
]);
```

## Best Practices

1. **Enable Carefully**: Only enable namespaces you understand
2. **Review Logs**: Regularly check audit logs for suspicious activity
3. **Test First**: Test commands in development before production
4. **Backup Data**: Always backup before running destructive commands
5. **Document Changes**: Document why you enabled specific namespaces

## Future Enhancements

- Command scheduling from UI
- Command favorites/bookmarks
- Export audit logs
- Email notifications for failed commands
- Command execution limits/throttling
- Role-based namespace permissions
