<?php

namespace App\Models;

use App\Enums\UserRole;
use App\Models\Chat\OpenAiTokenUsageLog;
use App\Models\Chat\UserTokenSubscription;
use App\Models\Media\MediaFile;
use App\Support\TokenSubscriptionStatus;
use App\Traits\HasAvatar;
use App\Traits\HasMultipleSubAccounts;
use App\Traits\HasRoles;
use App\Traits\HasSubscriptionCycles;
use App\Traits\HasTeams;
use App\Traits\Trackable;
use Exception;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;
use Lab404\Impersonate\Models\Impersonate;
use Laravel\Sanctum\HasApiTokens;
use Log;

/**
 * @property mixed $school
 */
class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens;
    use HasAvatar;
    use HasFactory;
    use HasMultipleSubAccounts;
    use HasRoles;
    use HasSubscriptionCycles;
    use HasTeams;
    use Impersonate;
    use Notifiable;
    use Trackable;

    protected $fillable = [
        'school_id', 'name', 'first_name', 'last_name', 'other_names', 'email', 'password', 'role', 'avatar', 'role_id',
        'phone', 'profile_image_url', 'status', 'is_online', 'last_seen_at',
        'two_factor_code', 'two_factor_expires_at', 'is_active',
        'suspension_reason', 'suspended_at', 'suspended_by',
        'country_code', 'country', 'region', 'city', 'gender', 'cover_image',
    ];

    protected $hidden = [
        'password', 'remember_token', 'two_factor_code',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_online' => 'boolean',
        'last_seen_at' => 'datetime',
        'two_factor_expires_at' => 'datetime',
        'is_active' => 'boolean',
        'role' => UserRole::class,
    ];

    protected $with = [
        'subscriptionCycles',
    ];

    public static function generateNameFromParts(?string $firstName, ?string $lastName, ?string $otherNames = null): string
    {
        $parts = array_filter([$firstName, $otherNames, $lastName]);

        return implode(' ', $parts);
    }

    // ==================== ACCESSORS ====================

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

        static::observe(new class
        {
            public function verified(User $user): void
            {
                $user->createFreeTrialSubscription();
            }
        });
    }

    public function handleRoleChange(): void
    {
        $roleModels = [
            'student' => Student::class,
            'teacher' => Teacher::class,
            'author' => Author::class,
            'librarian' => Librarian::class,
            'parent' => StudentParent::class,
        ];

        $role = $this->role instanceof UserRole ? $this->role->value : $this->role;

        if (! isset($roleModels[$role])) {
            return;
        }

        $modelClass = $roleModels[$role];
        $data = ['user_id' => $this->id];

        if (in_array($role, ['student', 'teacher', 'librarian', 'author', 'parent']) && $this->school_id) {
            $data['school_id'] = $this->school_id;
            $data = array_merge($data, $this->getRoleSpecificData($role, $modelClass));
        }

        $this->ensureRequiredFields($data, $role);

        try {
            $modelClass::firstOrCreate(['user_id' => $this->id], $data);
        } catch (Exception $e) {
            \Log::error('Failed to create role model', [
                'user_id' => $this->id,
                'role' => $role,
                'error' => $e->getMessage(),
            ]);
            $modelClass::firstOrCreate(['user_id' => $this->id]);
        }
    }

    private function getRoleSpecificData(string $role, string $modelClass): array
    {
        $data = [];

        switch ($role) {
            case 'teacher':
                $data['employee_id'] = method_exists($modelClass, 'generateEmployeeId')
                    ? $modelClass::generateEmployeeId($this->school_id)
                    : 'EMP'.time().rand(100, 999);
                $data['hire_date'] = now();
                $data['employment_type'] = 'full_time';
                $data['status'] = 'active';
                break;

            case 'librarian':
                $data['employee_id'] = method_exists($modelClass, 'generateEmployeeId')
                    ? $modelClass::generateEmployeeId($this->school_id)
                    : 'LIB'.time().rand(100, 999);
                $data['hire_date'] = now();
                $data['status'] = 'active';
                break;

            case 'student':
                $data['student_id'] = method_exists($modelClass, 'generateStudentId')
                    ? $modelClass::generateStudentId($this->school_id)
                    : 'STU'.time().rand(100, 999);
                $data['admission_date'] = now();
                $data['status'] = 'active';

                // Activate basic tier for students
                if ($this->hasVerifiedEmail() && ! $this->hasActiveSubscriptionCycle()) {
                    $this->activateBasicTier();
                }
                break;
        }

        return $data;
    }

    public function activateBasicTier(): void
    {
        $basicTier = \App\Models\Chat\PricingTier::where('name', 'Basic')
            ->where('is_active', true)
            ->first();

        if (! $basicTier) {
            Log::warning('Basic tier not found for user', ['user_id' => $this->id]);

            return;
        }

        // Check if user already has an active cycle
        if ($this->subscriptionCycles()->where('status', 'active')->exists()) {
            return;
        }

        \App\Models\Chat\SubscriptionCycle::create([
            'user_id' => $this->id,
            'pricing_tier_id' => $basicTier->id,
            'cycle_number' => 1,
            'cycle_start_date' => now(),
            'cycle_end_date' => now()->addDays(7),
            'tokens_allocated' => $basicTier->monthly_token_limit / 2,
            'topup_tokens_allocated' => 0,
            'tokens_used' => 0,
            'current_price' => 0,
            'status' => 'active',
            'is_topup' => false,
            'is_trial' => true,
        ]);
    }

    private function ensureRequiredFields(array &$data, string $role): void
    {
        if ($role === 'student' && empty($data['student_id'])) {
            $data['student_id'] = 'STU'.$this->id.time();
        }

        if (in_array($role, ['teacher', 'librarian']) && empty($data['employee_id'])) {
            $data['employee_id'] = strtoupper(substr($role, 0, 3)).$this->id.time();
        }
    }

    // ==================== SUBSCRIPTION CYCLE MANAGEMENT ====================

    public function createFreeTrialSubscription(bool $force = false): void
    {
        if (! $force && ! $this->hasVerifiedEmail()) {
            return;
        }

        if ($this->hasActiveSubscriptionCycle()) {
            return;
        }

        $this->activateBasicTier();
    }

    public function getNameAttribute($value): string
    {
        if ($this->first_name && $this->last_name) {
            $name = trim($this->first_name.' '.$this->last_name);
            if ($this->other_names) {
                $name = trim($this->first_name.' '.$this->other_names.' '.$this->last_name);
            }

            return $name;
        }

        return $value ?? '';
    }

    public function tokenSubscriptions(): HasMany
    {
        return $this->hasMany(UserTokenSubscription::class);
    }

    // ==================== RELATIONSHIPS ====================

    // Core Relationships
    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    // Role-specific Relationships
    public function student(): HasOne
    {
        return $this->hasOne(Student::class, 'user_id');
    }

    public function teacher(): HasOne
    {
        return $this->hasOne(Teacher::class, 'user_id');
    }

    public function author(): HasOne
    {
        return $this->hasOne(Author::class, 'user_id');
    }

    public function librarian(): HasOne
    {
        return $this->hasOne(Librarian::class, 'user_id');
    }

    public function accountant(): HasOne
    {
        return $this->hasOne(Accountant::class);
    }

    public function parent(): HasOne
    {
        return $this->hasOne(StudentParent::class, 'user_id');
    }

    // Activity & Content Relationships
    public function loginActivities(): HasMany
    {
        return $this->hasMany(LoginActivity::class);
    }

    public function worksheets(): HasMany
    {
        return $this->hasMany(Worksheet::class);
    }

    public function quizSessions(): HasMany
    {
        return $this->hasMany(QuizSession::class);
    }

    public function notes(): HasMany
    {
        return $this->hasMany(Note::class);
    }

    public function sharedNotes()
    {
        return $this->belongsToMany(Note::class, 'note_shares', 'shared_with_user_id', 'note_id')
            ->withPivot('can_edit')
            ->withTimestamps();
    }

    // Library Relationships
    public function borrowedBooks(): HasMany
    {
        return $this->hasMany(BookBorrowing::class);
    }

    public function bookSubscriptions(): HasMany
    {
        return $this->hasMany(BookSubscription::class);
    }

    // Subscription Relationships
    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class, 'subscriber_id');
    }

    // Media Relationships
    public function uploadedMedia(): HasMany
    {
        return $this->hasMany(MediaFile::class, 'uploaded_by');
    }

    // User Preferences
    public function preferences(): HasMany
    {
        return $this->hasMany(UserPreference::class);
    }

    // Suspension Relationship
    public function suspendedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'suspended_by');
    }

    // ==================== ROLE CHECKING ====================

    public function isOwner(): bool
    {
        return $this->hasRole('owner');
    }

    public function isSchoolAdmin(): bool
    {
        return $this->school_id && $this->hasRole('admin');
    }

    public function hasSchoolBoundRole(): bool
    {
        return $this->student || $this->teacher || $this->admin ||
            $this->librarian || $this->accountant || $this->parent;
    }

    public function getPrimarySchoolRole(): ?string
    {
        $roles = [
            'student' => $this->student,
            'teacher' => $this->teacher,
            'admin' => $this->admin,
            'librarian' => $this->librarian,
            'accountant' => $this->accountant,
            'parent' => $this->parent,
        ];

        foreach ($roles as $role => $exists) {
            if ($exists) {
                return $role;
            }
        }

        return null;
    }

    public function impersonateUser($userId)
    {
        $user = self::findOrFail($userId);

        if (! Auth::user()->canImpersonate()) {
            session()->flash('error', 'You do not have permission to impersonate users.');

            return;
        }

        if (! $user->canBeImpersonated()) {
            session()->flash('error', 'This user cannot be impersonated.');

            return;
        }

        session()->put('impersonate_redirect_to', route('dashboard'));

        return redirect()->route('impersonate', $userId);
    }

    // ==================== IMPERSONATION ====================

    public function canImpersonate(): bool
    {
        return $this->isSuperAdmin() ||
            $this->role === UserRole::OWNER ||
            $this->role === UserRole::ADMIN;
    }

    public function isSuperAdmin(): bool
    {
        return $this->hasRole('superadmin');
    }

    public function canBeImpersonated(): bool
    {
        return ! $this->isSuperAdmin() &&
            ! in_array($this->role->value, ['owner', 'admin', 'administrator', 'superadmin']) &&
            ($this->is_active ?? true);
    }

    // ==================== SCHOOL ACCESS & MULTI-TENANCY ====================

    public function needsSchoolOnboarding(): bool
    {
        if ($this->hasRole('super_admin')) {
            return false;
        }

        return ! $this->school_id;
    }

    public function canAccessSchool($schoolId): bool
    {
        return $this->canAccessCrossSchool() || $this->school_id == $schoolId;
    }

    public function canAccessCrossSchool(): bool
    {
        return $this->isSuperAdmin() || $this->hasRole('owner');
    }

    public function getCurrentSchool()
    {
        if ($this->canAccessCrossSchool()) {
            return app('current_school') ?: $this->school;
        }

        return $this->school;
    }

    // ==================== QUERY SCOPES ====================

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

        if (! $user) {
            return $query->whereRaw('0=1');
        }

        if ($user->canAccessCrossSchool()) {
            $schoolId = session('current_school_id');

            return $schoolId !== null ? $query->where('school_id', $schoolId) : $query;
        }

        return $query->where('school_id', $user->school_id);
    }

    public function scopeForSchool($query, $schoolId = null)
    {
        if ($schoolId) {
            return $query->where('school_id', $schoolId);
        }

        $user = auth()->user();
        if (! $user) {
            return $query->whereRaw('0=1');
        }

        if ($user->canAccessCrossSchool()) {
            $currentSchoolId = session('current_school_id', $user->school_id);

            return $currentSchoolId ? $query->where('school_id', $currentSchoolId) : $query;
        }

        return $query->where('school_id', $user->school_id);
    }

    // ==================== USER STATUS MANAGEMENT ====================

    public function suspend($reason = null): void
    {
        $this->update([
            'status' => 'suspended',
            'suspension_reason' => $reason,
            'suspended_at' => now(),
            'suspended_by' => auth()->id(),
        ]);
    }

    public function unsuspend(): void
    {
        $this->update([
            'status' => 'active',
            'suspension_reason' => null,
            'suspended_at' => null,
            'suspended_by' => null,
        ]);
    }

    public function isSuspended(): bool
    {
        return $this->status === 'suspended';
    }

    // ==================== TOKEN SUBSCRIPTION RELATIONSHIPS ====================

    public function activeTokenSubscription(): HasOne
    {
        return $this->hasOne(UserTokenSubscription::class)
            ->where('status', TokenSubscriptionStatus::ACTIVE->value)
            ->latest('activated_at');
    }

    public function tokenPurchases(): HasMany
    {
        return $this->hasMany(UserTokenSubscription::class)
            ->whereNotNull('purchased_at')
            ->orderBy('purchased_at', 'desc');
    }

    public function subscriptionHistory(): HasMany
    {
        return $this->hasMany(UserTokenSubscription::class)
            ->whereIn('status', [
                TokenSubscriptionStatus::EXPIRED->value,
                TokenSubscriptionStatus::DEPLETED->value,
                TokenSubscriptionStatus::REPLACED->value,
            ])
            ->orderBy('created_at', 'desc');
    }

    public function tokenUsageLogs(): HasMany
    {
        return $this->hasMany(OpenAiTokenUsageLog::class);
    }

    // ==================== TOKEN MANAGEMENT ====================

    public function hasOpenAiTokens(int $requiredTokens = 1): bool
    {
        $cycle = $this->subscriptionCycles()->where('status', TokenSubscriptionStatus::ACTIVE)->first();

        if (! $cycle) {
            return false;
        }

        if ($cycle->isExpired()) {
            $cycle->update(['status' => 'expired']);

            return false;
        }

        return $cycle->hasTokens($requiredTokens);
    }

    public function needsTokenUpgrade(): bool
    {
        $cycle = $this->getCurrentActiveCycle();

        if (! $cycle) {
            return true;
        }

        return $cycle->isExpired() || $cycle->isNearingDepletion();
    }

    public function getOpenAiModel(): string
    {
        if ($this->role !== UserRole::GUEST) {
            return config('openai.openai.premium_model', 'gpt-4');
        }

        $cycle = $this->getCurrentActiveCycle();

        if (! $cycle || ! $cycle->pricingTier) {
            return config('openai.openai.default_model', 'gpt-3.5-turbo');
        }

        if ($cycle->pricingTier->model) {
            return $cycle->pricingTier->model;
        }

        return ($cycle->current_price == 0)
            ? config('openai.openai.default_model', 'gpt-3.5-turbo')
            : config('openai.openai.premium_model', 'gpt-4');
    }

    public function shouldTrackTokenUsage(): bool
    {
        return in_array($this->role, [
            UserRole::GUEST,
            UserRole::ADMIN,
            UserRole::STUDENT,
            UserRole::TEACHER,
            UserRole::AUTHOR,
            UserRole::LIBRARIAN,
        ]);
    }
}
