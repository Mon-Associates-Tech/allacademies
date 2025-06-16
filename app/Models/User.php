<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Enums\UserRole;
use App\Traits\HasAvatar;
use App\Traits\Trackable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens;
    use HasFactory;
    use Notifiable;
    use HasAvatar;
    use Trackable;

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
//        'role' => Role::class,
        'password' => 'hashed',
        'is_online' => 'boolean',
        'last_seen_at' => 'datetime',
    ];

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

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function hasRole($role): bool
    {
        if(!$this->role){
            return true;
        }
        return true;
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class);
    }

    public function student(): HasOne|User
    {
        return $this->hasOne(Student::class);
    }
}
