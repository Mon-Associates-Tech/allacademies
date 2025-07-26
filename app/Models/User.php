<?php

namespace App\Models;

use App\Enums\UserRole;
use App\Traits\HasAvatar;
use App\Traits\HasRoles;
use App\Traits\Trackable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Lab404\Impersonate\Models\Impersonate;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens;
    use HasFactory;
    use Notifiable;
    use HasAvatar;
    use Trackable;
    use Impersonate;
    use HasRoles;

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'avatar',
        'role_id',
        'is_online',
        'last_seen_at',
        'two_factor_code',
        'two_factor_expires_at',
        'is_active',
    ];

    /**
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_online' => 'boolean',
        'last_seen_at' => 'datetime',
        'two_factor_expires_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    /**
     * Check if the user can impersonate other users
     */
    public function canImpersonate(): bool
    {
        // Only admins can impersonate other users
        return in_array($this->attributes['role'], ['owner', 'admin', 'administrator']);
    }

    /**
     * Check if the user can be impersonated
     */
    public function canBeImpersonated(): bool
    {
        // Don't allow impersonating other admins or inactive users
        return !in_array($this->attributes['role'], ['owner', 'admin', 'administrator']) &&
            ($this->is_active ?? true);
    }

    public function subscriptions(): User|HasMany
    {
        return $this->hasMany(Subscription::class, 'subscriber_id');
    }

    public function joinedTeams(): BelongsToMany
    {
        return $this->belongsToMany(Team::class);
    }

    public function ownedTeams(): User|HasMany
    {
        return $this->hasMany(Team::class, 'owner_id');
    }

    public function currentTeam(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function worksheets(): User|HasMany
    {
        return $this->hasMany(Worksheet::class);
    }


    public function student()
    {
        return $this->hasOne(Student::class);
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

    protected static function booted()
    {
        static::updated(static function ($user) {
            if ($user->isDirty('role')) {
                // Handle student role
                if ($user->hasRole('student')) {
                    Student::firstOrCreate(
                        ['user_id' => $user->id],
                        ['student_group_id' => null]
                    );
                }

                // Handle teacher role
                if ($user->hasRole('teacher')) {
                    Teacher::firstOrCreate(
                        ['user_id' => $user->id],
                        [/* default teacher fields */]
                    );
                }

                // Handle author role
                if ($user->hasRole('author')) {
                    Author::firstOrCreate(
                        ['user_id' => $user->id],
                        [/* default author fields */]
                    );
                }

                // Handle librarian role
                if ($user->hasRole('librarian')) {
                    Librarian::firstOrCreate(
                        ['user_id' => $user->id],
                        [/* default librarian fields */]
                    );
                }

                // Handle parent role
                if ($user->hasRole('parent')) {
                    StudentParent::firstOrCreate(
                        ['user_id' => $user->id],
                        [
                            'relationship' => null,
                        ]
                    );
                }
            }
        });
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
}
