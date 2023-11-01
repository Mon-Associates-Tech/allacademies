<?php

namespace App\Models;

use App\Traits\Trackable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Enums\TeamStatus;
use Illuminate\Support\Facades\Auth;

class Team extends Model
{
    use HasFactory;
    use Trackable;

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'is_personal',
        'status',
        'reason',
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'status' => TeamStatus::class,
    ];

    public function owner()
    {
        return $this->belongsTo(User::class);
    }

    public function members()
    {
        return $this->belongsToMany(User::class);
    }

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    public function metaData()
    {
        return $this->hasOne(MetaData::class);
    }

    public static function getUserTeams()
    {
        //get user teams
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $user->currentTeam->loadCount('subscriptions');

        $ownedTeams = $user->ownedTeams()
            ->withCount('subscriptions')
            ->get()
            ->each(function (Team $team) use ($user) {
                $team->setRelation('owner', $user);
            });

        $joinedTeams = $user->joinedTeams()
            ->with('owner')
            ->withCount('subscriptions')
            ->get();

        $teams = $ownedTeams->merge($joinedTeams);
        $teams->sort();

        return $teams;
    }
}
