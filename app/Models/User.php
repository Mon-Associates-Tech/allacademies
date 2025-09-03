<?php

namespace App\Models;

use App\Models\Media\MediaFile;
use App\Traits\HasAvatar;
use App\Traits\HasRoles;
use App\Traits\HasTeams;
use App\Traits\Trackable;
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
    ];

    protected $with = [
        'school',
    ];

    protected static function booted(): void
    {
        // Handle both created and updated events
        static::created(static function ($user) {
            self::handleRoleChange($user);
        });

        static::updated(static function ($user) {
            if ($user->isDirty('role')) {
                self::handleRoleChange($user);
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

        if (isset($roleModels[$this->role])) {
            $modelClass = $roleModels[$this->role];
            $data = ['user_id' => $this->id];

            // Add school_id for school-specific roles
            if (in_array($this->role, ['student', 'teacher', 'librarian', 'author', 'parent']) && $this->school_id) {
                $data['school_id'] = $this->school_id;

                // Generate IDs and set defaults based on role
                switch ($this->role) {
                    case 'teacher':
                        $data['employee_id'] = Teacher::generateEmployeeId($this->school_id);
                        $data['hire_date'] = now();
                        $data['employment_type'] = 'full_time';
                        $data['status'] = 'active';
                        break;

                    case 'librarian':
                        $data['employee_id'] = Librarian::generateEmployeeId($this->school_id);
                        $data['hire_date'] = now();
                        $data['status'] = 'active';
                        break;

                    case 'student':
                        $data['student_id'] = Student::generateStudentId($this->school_id);
                        $data['admission_date'] = now();
                        $data['status'] = 'active';
                        break;
                }
            }

            $modelClass::firstOrCreate(['user_id' => $this->id], $data);
        }
    }

    private static function handleRoleChangeDeprecated($user)
    {
        // Handle student role
        if ($user->role === 'student') {
            Student::firstOrCreate(
                ['user_id' => $user->id],
                [
                    'student_group_id' => null,
                    'academic_level_id' => null,
                    'academic_group_id' => null,
                    'school_id' => null,
                ]
            );
        }

        // Handle teacher role
        if ($user->role === 'teacher') {
            Teacher::firstOrCreate(
                ['user_id' => $user->id],
                [/* default teacher fields */]
            );
        }

        // Handle author role
        if ($user->role === 'author') {
            Author::firstOrCreate(
                ['user_id' => $user->id],
                [/* default author fields */]
            );
        }

        // Handle librarian role
        if ($user->role === 'librarian') {
            Librarian::firstOrCreate(
                ['user_id' => $user->id],
                [/* default librarian fields */]
            );
        }

        // Handle parent role
        if ($user->role === 'parent') {
            StudentParent::firstOrCreate(
                ['user_id' => $user->id],
                [
                    'relationship' => null,
                ]
            );
        }
    }

    /**
     * Check if the user can impersonate other users
     */
    public function canImpersonateDeprecated(): bool
    {
        // Only admins can impersonate other users
        return in_array($this->attributes['role'], ['owner', 'admin', 'administrator']);
    }

    /**
     * Check if the user can be impersonated
     */
    public function canBeImpersonatedDeprecated(): bool
    {
        // Don't allow impersonating other admins or inactive users
        return !in_array($this->attributes['role'], ['owner', 'admin', 'administrator']) &&
            ($this->is_active ?? true);
    }

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

    // Multi-tenant role checking

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

    // Impersonation methods

    public function canImpersonate(): bool
    {
        return $this->isSuperAdmin() ||
            in_array($this->role, ['owner', 'admin', 'administrator', 'superadmin']);
    }

    public function isSuperAdmin(): User|bool
    {
        return $this->hasRole('superadmin');
    }

    public function hasRole($role, $schoolId = null): bool
    {
        if ($role === 'superadmin') {
            return $this->roles()
                ->where('name', 'superadmin')
//                ->whereNull('roles.school_id')
                ->exists();
        }

        $query = $this->roles()->where('name', $role);

        if ($schoolId) {
            // $query->where('users.school_id', $schoolId);
        } elseif ($this->school_id) {
            // $query->where('users.school_id', $this->school_id);
        }

        return $query->exists();
    }

    public function hasSchoolBoundRole(): bool
    {
        return $this->student || $this->teacher || $this->admin ||
            $this->librarian || $this->accountant || $this->parent;
    }

    // Get user's primary role in current school context
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

    public function canBeImpersonated(): bool
    {
        return !$this->isSuperAdmin() &&
            !in_array($this->role, ['owner', 'admin', 'administrator', 'superadmin']) &&
            ($this->is_active ?? true);
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

        if (!$user || $user->canAccessCrossSchool()) {
            $schoolId = app()->has('current_school') ? app('current_school')->id : null;
            return $schoolId ? $query->where('school_id', $schoolId) : $query;
        }

        return $query->where('school_id', $user->school_id);
    }
}
