<?php

namespace App\Models;

use App\Enums\UserRole;
use App\Models\Chat\OpenAiTokenPackage;
use App\Models\Chat\OpenAiTokenUsageLog;
use App\Models\Chat\UserTokenSubscription;
use App\Models\Media\MediaFile;
use App\Support\TokenSubscriptionStatus;
use App\Traits\HasAvatar;
use App\Traits\HasRoles;
use App\Traits\HasTeams;
use App\Traits\Trackable;
use Exception;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphOne;
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
    use HasFactory;
    use Notifiable;
    use HasAvatar;
    use Trackable;
    use Impersonate;
    use HasRoles;
    use HasTeams;


    protected $fillable = [
        'school_id', 'name', 'email', 'password', 'role', 'avatar', 'role_id',
        'phone', 'profile_image_url', 'status', 'is_online', 'last_seen_at',
        'two_factor_code', 'two_factor_expires_at', 'is_active',
        'suspension_reason', 'suspended_at', 'suspended_by',
        'country_code', 'gender', 'cover_image'
    ];

    protected $hidden = [
        'password', 'remember_token', 'two_factor_code'
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

    ];


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

        static::observe(new class {
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


        if (isset($roleModels[$role])) {
            $modelClass = $roleModels[$role];
            $data = ['user_id' => $this->id];

            // Add school_id for school-specific roles
            if (in_array($role, ['student', 'teacher', 'librarian', 'author', 'parent']) && $this->school_id) {
                $data['school_id'] = $this->school_id;

                // Generate IDs and set defaults based on role
                switch ($role) {
                    case 'teacher':
                        if (method_exists($modelClass, 'generateEmployeeId')) {
                            $data['employee_id'] = $modelClass::generateEmployeeId($this->school_id);
                        } else {
                            $data['employee_id'] = 'EMP' . time() . rand(100, 999);
                        }
                        $data['hire_date'] = now();
                        $data['employment_type'] = 'full_time';
                        $data['status'] = 'active';
                        break;

                    case 'librarian':
                        if (method_exists($modelClass, 'generateEmployeeId')) {
                            $data['employee_id'] = $modelClass::generateEmployeeId($this->school_id);
                        } else {
                            $data['employee_id'] = 'LIB' . time() . rand(100, 999);
                        }
                        $data['hire_date'] = now();
                        $data['status'] = 'active';
                        break;

                    case 'student':
                        if (method_exists($modelClass, 'generateStudentId')) {
                            $data['student_id'] = $modelClass::generateStudentId($this->school_id);
                        } else {
                            $data['student_id'] = 'STU' . time() . rand(100, 999);
                        }
                        $data['admission_date'] = now();
                        $data['status'] = 'active';
                        break;
                }
            }

            // Ensure required fields have values
            if ($role === 'student' && empty($data['student_id'])) {
                $data['student_id'] = 'STU' . $this->id . time();
            }

            if (in_array($role, ['teacher', 'librarian']) && empty($data['employee_id'])) {
                $data['employee_id'] = strtoupper(substr($role, 0, 3)) . $this->id . time();
            }

            try {
                $modelClass::firstOrCreate(['user_id' => $this->id], $data);
            } catch (Exception $e) {
                Log::error('Failed to create role model', [
                    'user_id' => $this->id,
                    'role' => $role,
                    'data' => $data,
                    'error' => $e->getMessage()
                ]);

                // Fallback: try without the additional data
                $modelClass::firstOrCreate(['user_id' => $this->id]);
            }
        }
    }

    /**
     * Create a free trial subscription for new users
     *
     */
    public function createFreeTrialSubscription(bool $force = false): void
    {

        // Only check email verification if not forcing creation
        if (!$force && !$this->hasVerifiedEmail()) {
            return;
        }

        // Check if user has ever had a trial subscription
        if ($this->hasEverHadTrial()) {
            return;
        }

        // Check if user already has any subscription
        if ($this->tokenSubscriptions()->count() > 0) {
            return;
        }

        // Get free trial package
        $freePackage = OpenAiTokenPackage::where('is_free', true)
            ->where('is_active', true)
            ->first();

        if (!$freePackage) {
            Log::warning('No free trial package available for user', ['user_id' => $this->id]);
            return;
        }

        // Create a free trial subscription
        UserTokenSubscription::create([
            'user_id' => $this->id,
            'package_id' => $freePackage->id,
            'tokens_purchased' => $freePackage->token_limit,
            'tokens_used' => 0,
            'tokens_remaining' => $freePackage->token_limit,
            'purchased_at' => now(),
            'activated_at' => now(),
            'expires_at' => now()->addWeek(), // 7 days trial
            'status' => TokenSubscriptionStatus::ACTIVE,
            'action_type' => 'trial',
        ]);
    }

    /**
     * Check if user has ever had a trial subscription
     */
    public function hasEverHadTrial(): bool
    {
        return $this->tokenSubscriptions()
            ->where('action_type', 'trial')
            ->exists();
    }

    public function tokenSubscriptions(): HasMany
    {
        return $this->hasMany(UserTokenSubscription::class);
    }

    // Multi-tenant role checking

    public function subscriptions(): User|HasMany
    {
        return $this->hasMany(Subscription::class, 'subscriber_id');
    }

    public function borrowedBooks(): HasMany
    {
        return $this->hasMany(BookBorrowing::class);
    }

    public function bookSubscriptions(): HasMany
    {
        return $this->hasMany(BookSubscription::class);
    }

    // Impersonation methods

    public function worksheets(): HasMany
    {
        return $this->hasMany(Worksheet::class);
    }

    public function student(): HasOne
    {
        return $this->hasOne(Student::class, 'user_id');
    }

    public function impersonateUser($userId)
    {
        $user = self::findOrFail($userId);

        // Check if current user can impersonate
        if (!Auth::user()->canImpersonate()) {
            session()->flash('error', 'You do not have permission to impersonate users.');
            return;
        }

        // Check if target user can be impersonated
        if (!$user->canBeImpersonated()) {
            session()->flash('error', 'This user cannot be impersonated.');
            return;
        }

        // Store the current user ID before impersonation
        session()->put('impersonate_redirect_to', route('dashboard'));

        return redirect()->route('impersonate', $userId);
    }

    // Get user's primary role in current school context

    public function canImpersonate(): bool
    {
        return $this->isSuperAdmin() ||
            $this->role === UserRole::OWNER ||
            $this->role === UserRole::ADMIN;
    }

    public function isSuperAdmin(): User|bool
    {
        return $this->hasRole('superadmin');
    }

    public function canBeImpersonated(): bool
    {
        return !$this->isSuperAdmin() &&
            !in_array($this->role->value, ['owner', 'admin', 'administrator', 'superadmin']) &&
            ($this->is_active ?? true);
    }

    public function hasSchoolBoundRole(): bool
    {
        return $this->student || $this->teacher || $this->admin ||
            $this->librarian || $this->accountant || $this->parent;
    }

    public function getPrimarySchoolRole(): ?string
    {
        if ($this->student) return 'student';
        if ($this->teacher) return 'teacher';
        if ($this->admin) return 'admin';
        if ($this->librarian) return 'librarian';
        if ($this->accountant) return 'accountant';
        if ($this->parent) return 'parent';

        return null;
    }

    public function hasRoleInSchool($role, $schoolId)
    {
        return $this->hasRole($role, $schoolId);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active')->where('is_active', true);
    }

    public function scopeByRole($query, $role)
    {
        if ($role instanceof UserRole) {
            return $query->where('role', $role->value);
        }
        return $query->where('role', $role);
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

    public function parent(): HasOne
    {
        return $this->hasOne(StudentParent::class, 'user_id');
    }

    public function loginActivities(): HasMany
    {
        return $this->hasMany(LoginActivity::class);
    }

    public function preferences(): User|HasMany
    {
        return $this->hasMany(UserPreference::class);
    }

    public function uploadedMedia()
    {
        return $this->hasMany(MediaFile::class, 'uploaded_by');
    }

    /**
     * Check if user needs onboarding
     */
    public function needsSchoolOnboarding(): bool
    {
        // Super admin doesn't need school onboarding
        if ($this->hasRole('super_admin')) {
            return false;
        }

        return !$this->school_id;
    }

    /**
     * Check if user is admin of their school
     */
    public function isSchoolAdmin(): bool
    {
        return $this->school_id && $this->hasRole('admin');
    }

    public function isOwner(): bool
    {
        return $this->hasRole('owner');
    }

    public function canAccessSchool($schoolId): bool
    {
        if ($this->canAccessCrossSchool()) {
            return true;
        }

        return $this->school_id == $schoolId;
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

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function accountant(): HasOne
    {
        return $this->hasOne(Accountant::class);
    }

    public function scopeForCurrentSchool($query)
    {
        $user = auth()->user();

        if (!$user) {
            return $query->whereRaw('0=1'); // No results for unauthenticated users
        }

        // Super admins and owners with cross-school access
        if ($user->isSuperAdmin() || $user->hasRole('owner')) {
            // Check if they're in a specific school context
            if (session()->has('current_school_id')) {
                $schoolId = session('current_school_id');
                if ($schoolId !== null) { // Explicitly check for null
                    return $query->where('school_id', $schoolId);
                }
                // If current_school_id is explicitly set to null, they want to see all schools
                return $query;
            }

            // If no session context, check if they want to see all schools or their own
            // By default, super admins and owners can see all schools
            // unless they've specifically selected a school
            return $query;
        }

        // Regular users and admins only see their own school
        return $query->where('school_id', $user->school_id);
    }

    public function scopeForSchool($query, $schoolId = null)
    {
        if ($schoolId) {
            return $query->where('school_id', $schoolId);
        }

        $user = auth()->user();
        if (!$user) {
            return $query->whereRaw('0=1'); // No results for unauthenticated users
        }

        // Super admins and owners might see all users or just their school
        if ($user->isSuperAdmin() || $user->hasRole('owner')) {
            // Check if they're in a specific school context
            $currentSchoolId = session('current_school_id', $user->school_id);
            if ($currentSchoolId) {
                return $query->where('school_id', $currentSchoolId);
            }
            // If in "all schools" view, don't apply scoping
            return $query;
        }

        // Regular users only see users from their school
        return $query->where('school_id', $user->school_id);
    }

    public function suspend($reason = null)
    {
        $this->update([
            'status' => 'suspended',
            'suspension_reason' => $reason,
            'suspended_at' => now(),
            'suspended_by' => auth()->id(),
        ]);
    }

    public function unsuspend()
    {
        $this->update([
            'status' => 'active',
            'suspension_reason' => null,
            'suspended_at' => null,
            'suspended_by' => null,
        ]);
    }

    public function isSuspended()
    {
        return $this->status === 'suspended';
    }

    public function suspendedBy()
    {
        return $this->belongsTo(User::class, 'suspended_by');
    }

    public function quizSessions()
    {
        return $this->hasMany(QuizSession::class);
    }

    public function activeTokenSubscription()
    {
        return $this->hasOne(UserTokenSubscription::class)
            ->where('status', TokenSubscriptionStatus::ACTIVE->value)
            ->latest('activated_at');
    }

    /**
     * Get all token purchases for this user (including top-ups)
     */
    public function tokenPurchases()
    {
        return $this->hasMany(UserTokenSubscription::class)
            ->whereNotNull('purchased_at')
            ->orderBy('purchased_at', 'desc');
    }

    public function subscriptionHistory()
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


    /**
     * Check if a user has sufficient tokens
     * Only applies to users with 'subscriber' role
     */
    public function hasOpenAiTokens(int $requiredTokens = 1): bool
    {

        $subscription = $this->activeTokenSubscription;

        if (!$subscription) {
            return false;
        }

        // Check if expired
        if ($subscription->isExpired()) {
            $subscription->deactivate(TokenSubscriptionStatus::EXPIRED);
            return false;
        }

        return $subscription->hasTokens($requiredTokens);
    }

    /**
     * Check if user needs to upgrade
     * Only applies to users with 'subscriber' role
     */
    public function needsTokenUpgrade(): bool
    {
        $subscription = $this->activeTokenSubscription;

        if (!$subscription) {
            return true;
        }

        return $subscription->isExpired() ||
            $subscription->status === TokenSubscriptionStatus::DEPLETED ||
            $subscription->isNearingDepletion();
    }

    /**
     * Get the OpenAI model based on user's role and subscription
     * Subscribers use token-based packages, others get premium by default
     */
    public function getOpenAiModel(): string
    {
        // Non-subscribers always get premium model - use enum comparison
        if ($this->role !== UserRole::SUBSCRIBER) {
            return config('openai.openai.premium_model', 'gpt-4');
        }

        // For subscribers, check their subscription package
        $subscription = $this->activeTokenSubscription;

        if (!$subscription || !$subscription->package) {
            return config('openai.openai.default_model', 'gpt-3.5-turbo');
        }

        // Get model from package
        $packageModel = $subscription->package->model;

        if ($packageModel) {
            return $packageModel;
        }

        // Fallback: determine by package price or is_free flag
        if ($subscription->package->is_free || $subscription->package->price == 0) {
            return config('openai.openai.default_model', 'gpt-3.5-turbo');
        }

        return config('openai.openai.premium_model', 'gpt-4');
    }

    /**
     * Check if a user should be tracked for token usage
     */
    public function shouldTrackTokenUsage(): bool
    {
        return in_array($this->role, [
            UserRole::SUBSCRIBER,
            UserRole::ADMIN,
            UserRole::STUDENT,
            UserRole::TEACHER,
            UserRole::AUTHOR,
            UserRole::LIBRARIAN,
        ]);
    }

    /**
     * Get the user's subaccount (for direct user payments)
     */
    public function subaccount(): MorphOne
    {
        return $this->morphOne(Subaccount::class, 'subaccountable');
    }

    public function notes()
    {
        return $this->hasMany(Note::class);
    }

    public function sharedNotes()
    {
        return $this->belongsToMany(Note::class, 'note_shares', 'shared_with_user_id', 'note_id')
            ->withPivot('can_edit')
            ->withTimestamps();
    }

    /**
     * ============================================
     * SPONSORSHIP RELATIONSHIPS
     * ============================================
     */

    /**
     * Get the sponsorship programs created by this user (as benefactor)
     */
    public function sponsorshipPrograms(): HasMany
    {
        return $this->hasMany(\App\Models\SponsorshipProgram::class);
    }

    /**
     * Get active sponsorship programs created by this user
     */
    public function activeSponsorshipPrograms(): HasMany
    {
        return $this->hasMany(\App\Models\SponsorshipProgram::class)
            ->where('status', 'active');
    }

    /**
     * Get the sponsor offers created by this user (as sponsor)
     */
    public function sponsorOffers(): HasMany
    {
        return $this->hasMany(\App\Models\SponsorOffer::class);
    }

    /**
     * Get active sponsor offers created by this user
     */
    public function activeSponsorOffers(): HasMany
    {
        return $this->hasMany(\App\Models\SponsorOffer::class)
            ->where('status', 'open');
    }

    /**
     * Get the bids submitted by this user (as benefactor)
     */
    public function sponsorshipBids(): HasMany
    {
        return $this->hasMany(\App\Models\SponsorshipBid::class);
    }

    /**
     * Get pending bids submitted by this user
     */
    public function pendingSponsorshipBids(): HasMany
    {
        return $this->hasMany(\App\Models\SponsorshipBid::class)
            ->where('status', 'pending');
    }

    /**
     * Get the contributions made by this user (as donor/sponsor)
     */
    public function sponsorshipContributions(): HasMany
    {
        return $this->hasMany(\App\Models\SponsorshipContribution::class);
    }

    /**
     * Get successful contributions made by this user
     */
    public function successfulSponsorshipContributions(): HasMany
    {
        return $this->hasMany(\App\Models\SponsorshipContribution::class)
            ->where('status', 'completed');
    }

    /**
     * Get programs verified by this user (as reviewer)
     */
    public function verifiedPrograms(): HasMany
    {
        return $this->hasMany(\App\Models\SponsorshipProgram::class, 'verified_by');
    }

    /**
     * Check if user is a benefactor
     */
    public function isBenefactor(): bool
    {
        return $this->role === \App\Enums\UserRole::BENEFACTOR || $this->hasRole('benefactor');
    }

    /**
     * Check if user is a sponsor
     */
    public function isSponsor(): bool
    {
        return $this->role === \App\Enums\UserRole::SPONSOR || $this->hasRole('sponsor');
    }

    /**
     * Check if user is a reviewer
     */
    public function isReviewer(): bool
    {
        return $this->role === \App\Enums\UserRole::REVIEWER || $this->hasRole('reviewer') || $this->hasRole('owner');
    }

    /**
     * Get total amount raised across all user's programs
     */
    public function getTotalAmountRaisedAttribute(): float
    {
        return $this->sponsorshipPrograms()
            ->sum('amount_raised');
    }

    /**
     * Get total amount contributed by this user
     */
    public function getTotalAmountContributedAttribute(): float
    {
        return $this->successfulSponsorshipContributions()
            ->sum('amount');
    }

    /**
     * Get total sponsorship offers value
     */
    public function getTotalOffersValueAttribute(): float
    {
        return $this->sponsorOffers()
            ->sum('amount_offered');
    }
}
