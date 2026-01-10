# Development Guidelines

## Code Quality Standards

### PHP Code Style

- **PSR-12 Compliance**: Follow PSR-12 coding standards for PHP
- **Laravel Pint**: Use Laravel Pint for automatic code formatting (`php artisan pint`)
- **Type Declarations**: Use strict types and return type declarations
- **Enum Usage**: Prefer backed enums over constants for fixed value sets
- **Null Safety**: Use null coalescing operators (`??`) and optional chaining (`?->`)

### Naming Conventions

- **Classes**: PascalCase (e.g., `UserTokenSubscription`, `TokenSubscriptionService`)
- **Methods**: camelCase (e.g., `createFreeTrialSubscription`, `hasOpenAiTokens`)
- **Variables**: camelCase (e.g., `$currentSubscription`, `$tokenLimit`)
- **Constants**: SCREAMING_SNAKE_CASE (e.g., `SUPER_ADMIN`, `ACTIVE`)
- **Database Tables**: snake_case plural (e.g., `user_token_subscriptions`, `academic_groups`)
- **Database Columns**: snake_case (e.g., `school_id`, `tokens_remaining`)
- **Routes**: kebab-case (e.g., `/academic-groups`, `/book-subscriptions`)

### Documentation Standards

- **PHPDoc Blocks**: Document all public methods with parameter types and return types
- **Inline Comments**: Use section separators with `// ==================== SECTION NAME ====================`
- **Method Documentation**: Include `@param`, `@return`, `@throws` annotations
- **Complex Logic**: Add explanatory comments for business logic

### File Organization

- **Model Organization**: Group related methods with section comments (Relationships, Scopes, Accessors, etc.)
- **Service Classes**: One service per business domain (e.g., `TokenSubscriptionService`, `AssessmentService`)
- **Trait Usage**: Extract reusable behaviors into traits (e.g., `BelongsToSchool`, `HasRoles`)
- **Helper Functions**: Global helpers in `helpers.php` with `function_exists()` checks

## Architectural Patterns

### Multi-Tenancy Implementation

```php
// Use BelongsToSchool trait for school-scoped models
trait BelongsToSchool
{
    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }
    
    protected static function bootBelongsToSchool(): void
    {
        static::creating(function ($model) {
            if (!$model->school_id && auth()->user()?->school_id) {
                $model->school_id = auth()->user()->school_id;
            }
        });
    }
}

// Use scopes for querying across schools
public function scopeForSchool(Builder $query, $schoolId)
{
    return $query->where('school_id', $schoolId);
}

// Check school context with helper functions
$schoolId = getSchoolId();
$school = getCurrentSchoolContext();
```

### Enum-Based Type Safety

```php
// Define backed enums for fixed value sets
enum UserRole: string
{
    case OWNER = 'owner';
    case ADMIN = 'admin';
    case TEACHER = 'teacher';
    case STUDENT = 'student';
    
    public static function getAll(): array
    {
        return [self::OWNER, self::ADMIN, self::TEACHER, self::STUDENT];
    }
}

// Use enums in model casts
protected $casts = [
    'role' => UserRole::class,
    'status' => TokenSubscriptionStatus::class,
];

// Compare enums properly (enum to enum, not string)
if ($subscription->status === TokenSubscriptionStatus::ACTIVE) {
    // Correct: comparing enum to enum
}
```

### Service Layer Pattern

```php
// Encapsulate business logic in service classes
class TokenSubscriptionService
{
    public function changeSubscription(User $user, OpenAiTokenPackage $package, bool $isTopUp = true): UserTokenSubscription
    {
        return DB::transaction(function () use ($user, $package, $isTopUp) {
            // Business logic here
        });
    }
    
    public function activateSubscription(UserTokenSubscription $subscription): void
    {
        DB::transaction(function () use ($subscription) {
            // Activation logic
        });
    }
}

// Use services in controllers
public function store(Request $request, TokenSubscriptionService $service)
{
    $subscription = $service->changeSubscription(auth()->user(), $package);
    return redirect()->back()->with('success', 'Subscription updated');
}
```

### Model Observers & Lifecycle Hooks

```php
// Use model events for automatic actions
protected static function booted(): void
{
    parent::booted();
    
    static::created(static function ($user) {
        $user->handleRoleChange();
        $user->createFreeTrialSubscription();
    });
    
    static::updated(static function ($user) {
        if ($user->isDirty('role')) {
            $user->handleRoleChange();
        }
    });
}

// Use anonymous observers for simple cases
static::observe(new class {
    public function verified(User $user): void
    {
        $user->createFreeTrialSubscription();
    }
});
```

### Database Transactions

```php
// Wrap critical operations in transactions
return DB::transaction(function () use ($user, $data) {
    $subscription = UserTokenSubscription::create($data);
    $user->update(['status' => 'active']);
    return $subscription;
});

// Use transactions in services for data consistency
public function replaceSubscription(User $user, OpenAiTokenPackage $package): UserTokenSubscription
{
    return DB::transaction(function () use ($user, $package) {
        $current = $user->activeSubscriptionCycle;
        if ($current) {
            $current->deactivate(TokenSubscriptionStatus::REPLACED);
        }
        return UserTokenSubscription::create([...]);
    });
}
```

## Common Implementation Patterns

### Relationship Definitions

```php
// Use explicit relationship methods with type hints
public function school(): BelongsTo
{
    return $this->belongsTo(School::class);
}

public function tokenSubscriptions(): HasMany
{
    return $this->hasMany(UserTokenSubscription::class);
}

public function activeTokenSubscription(): HasOne
{
    return $this->hasOne(UserTokenSubscription::class)
        ->where('status', TokenSubscriptionStatus::ACTIVE->value)
        ->latest('activated_at');
}

// Use polymorphic relationships for flexible associations
public function questions(): MorphMany
{
    return $this->morphMany(Question::class, 'questionable');
}
```

### Query Scopes

```php
// Define reusable query scopes
public function scopeActive($query)
{
    return $query->where('status', 'active')->where('is_active', true);
}

public function scopeByRole($query, $role)
{
    $roleValue = $role instanceof UserRole ? $role->value : $role;
    return $query->where('role', $roleValue);
}

public function scopeForCurrentSchool($query)
{
    $user = auth()->user();
    if (!$user) {
        return $query->whereRaw('0=1');
    }
    
    if ($user->canAccessCrossSchool()) {
        $schoolId = session('current_school_id');
        return $schoolId !== null ? $query->where('school_id', $schoolId) : $query;
    }
    
    return $query->where('school_id', $user->school_id);
}

// Use scopes in queries
$students = Student::active()->forCurrentSchool()->get();
$teachers = User::byRole(UserRole::TEACHER)->get();
```

### Helper Function Patterns

```php
// Always check function existence
if (!function_exists('school_setting')) {
    function school_setting($key, $default = null)
    {
        return SchoolSetting::get($key, $default);
    }
}

// Use type hints and return types
function getTimeRemaining($futureTimestamp): string
{
    $futureDate = Carbon::parse($futureTimestamp);
    $now = Carbon::now();
    
    if ($now->greaterThan($futureDate)) {
        return 'Expired';
    }
    
    // Implementation...
}

// Provide flexible parameters with defaults
function getStudent($user_id = null, $student_id = null, $school_id = null, $withoutScopes = false)
{
    $query = $withoutScopes 
        ? Student::withoutGlobalScopes() 
        : Student::query();
    
    // Implementation...
}
```

### Logging Patterns

```php
// Use structured logging with context
Log::info('changeSubscription called', [
    'user_id' => $user->id,
    'new_package_id' => $newPackage->id,
    'is_top_up' => $isTopUp,
    'current_subscription_id' => $currentSubscription?->id,
]);

// Create helper functions for consistent logging
function logInfo(string $message, array $context = []): void
{
    $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2)[1];
    $context = array_merge([
        'class' => $trace['class'],
        'method' => $trace['function'],
    ], $context);
    
    Log::info($message, $context);
}

// Log errors with full context
Log::error('Failed to create role model', [
    'user_id' => $this->id,
    'role' => $role,
    'error' => $e->getMessage(),
]);
```

### Authorization Patterns

```php
// Use role checking methods on User model
public function isSuperAdmin(): bool
{
    return $this->hasRole('superadmin');
}

public function canAccessSchool($schoolId): bool
{
    return $this->canAccessCrossSchool() || $this->school_id == $schoolId;
}

public function canImpersonate(): bool
{
    return $this->isSuperAdmin() || 
           $this->role === UserRole::OWNER || 
           $this->role === UserRole::ADMIN;
}

// Use policies for model-level authorization
class BookPolicy
{
    public function view(User $user, Book $book): bool
    {
        return $user->canAccessSchool($book->school_id);
    }
    
    public function update(User $user, Book $book): bool
    {
        return $user->id === $book->author_id || 
               $user->hasRole('admin');
    }
}
```

### Polymorphic Morph Map

```php
// Register morph map in AppServiceProvider
Relation::enforceMorphMap([
    'user' => User::class,
    'student' => Student::class,
    'teacher' => Teacher::class,
    'book' => Book::class,
    'assessment' => Assessment::class,
    'essay_question' => EssayQuestion::class,
    'multiple_choice_question' => MultipleChoiceQuestion::class,
]);

// Use consistent morph names across the application
```

## Best Practices

### Error Handling

- Use try-catch blocks for operations that may fail
- Log errors with full context before handling
- Provide fallback behavior when appropriate
- Use database transactions for critical operations

### Performance Optimization

- Use eager loading to prevent N+1 queries: `with(['school', 'user'])`
- Add database indexes for frequently queried columns
- Cache expensive operations with appropriate TTL
- Use queue jobs for heavy background processing

### Security Practices

- Validate and sanitize all user input
- Use Laravel's built-in CSRF protection
- Implement proper authorization checks with policies
- Never expose sensitive data in logs or responses
- Use parameterized queries (Eloquent does this automatically)

### Testing Considerations

- Write feature tests for critical user flows
- Use factories for test data generation
- Mock external services (OpenAI, Paystack, etc.)
- Test multi-tenancy isolation
- Verify authorization rules with policy tests

### Code Reusability

- Extract common logic into traits
- Create service classes for complex business logic
- Use helper functions for frequently used operations
- Define reusable query scopes on models
- Leverage Laravel's built-in features before creating custom solutions

### Livewire Component Patterns

- Keep components focused on single responsibility
- Use component properties for reactive state
- Emit events for component communication
- Validate input in component methods
- Use computed properties for derived data

### API Development

- Use API resources for consistent response formatting
- Implement proper HTTP status codes
- Version APIs when making breaking changes
- Document endpoints with clear examples
- Use Sanctum for API authentication

### Database Migrations

- Never modify existing migrations in production
- Use descriptive migration names
- Add indexes for foreign keys and frequently queried columns
- Use `up()` and `down()` methods for reversibility
- Test migrations on a copy of production data

### Configuration Management

- Store environment-specific values in `.env`
- Use config files for application-wide settings
- Never commit sensitive credentials to version control
- Document required environment variables
- Provide sensible defaults in config files
