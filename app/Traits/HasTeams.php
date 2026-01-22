<?php

namespace App\Traits;

use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

trait HasTeams
{
    protected function initializeHasTeams(): void
    {
        $this->with[] = 'currentTeam';

        // Only run this after the model is fully loaded
        if ($this->exists && empty($this->current_team_id)) {
            try {
                $this->ensureUserHasTeam();
            } catch (\Exception $e) {
                // Log the error but don't break the application
                \Log::warning('Failed to ensure user has team', [
                    'user_id' => $this->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }

    /**
     * Ensure the user has at least one team and set it as current
     */
    public function ensureUserHasTeam(): void
    {
        // Check if user has any teams (owned or joined)
        $allTeams = $this->getAllUserTeams();

        if ($allTeams->isEmpty()) {
            // Create a personal team for the user
            $personalTeam = $this->createPersonalTeam();
            $this->setCurrentTeam($personalTeam);
        } else {
            // Set the first available team as current if none is set
            $currentTeam = $this->getCurrentTeamOrDefault();
            if ($currentTeam) {
                $this->setCurrentTeam($currentTeam);
            }
        }
    }

    /**
     * Get all teams the user has access to (owned + joined active teams)
     */
    public function getAllUserTeams()
    {
        $ownedTeams = $this->ownedTeams()->get();

        // Only get joined teams where the team owner allows switching
        $joinedTeams = $this->joinedTeams()
            ->where('is_active', true)
            ->whereHas('owner', function ($query) {
                // Add any conditions here for team owner permissions
                // For now, we'll allow all active teams
            })
            ->get();

        return $ownedTeams->merge($joinedTeams);
    }

    /**
     * Get current team or return the best default option
     */
    public function getCurrentTeamOrDefault()
    {
        if ($this->current_team_id) {
            $currentTeam = $this->currentTeam;
            if ($currentTeam && $this->canAccessTeam($currentTeam)) {
                return $currentTeam;
            }
        }

        // Find the best default team
        $allTeams = $this->getAllUserTeams();

        // Prioritize owned teams first
        $ownedTeam = $allTeams->where('owner_id', $this->id)->first();
        if ($ownedTeam) {
            return $ownedTeam;
        }

        // Then any accessible joined team
        return $allTeams->first();
    }

    /**
     * Check if user can access a specific team
     */
    public function canAccessTeam(Team $team): bool
    {
        // User owns the team
        if ($team->owner_id === $this->id) {
            return true;
        }

        // User is a member and team is active
        return $this->joinedTeams()
            ->where('teams.id', $team->id)
            ->where('teams.is_active', true)
            ->exists();
    }

    /**
     * Check if user can switch to a specific team
     */
    public function canSwitchToTeam(Team $team): bool
    {
        if (! $this->canAccessTeam($team)) {
            return false;
        }

        // If user owns the team, they can always switch
        if ($team->owner_id === $this->id) {
            return true;
        }

        // For joined teams, check if team owner allows switching
        // This could be controlled by a team setting in the future
        return $team->is_active && $team->allow_member_switching ?? true;
    }

    /**
     * Set current team for the user
     */
    public function setCurrentTeam(Team $team): bool
    {
        if (! $this->canSwitchToTeam($team)) {
            return false;
        }

        $this->current_team_id = $team->id;

        return $this->save();
    }

    /**
     * Create a personal team for the user
     */
    protected function createPersonalTeam(): Team
    {
        return DB::transaction(function () {
            return Team::create([
                'name' => $this->name."'s Personal Team",
                'owner_id' => $this->id,
                'is_personal' => true,
                'is_active' => true,
                'type' => 'personal',
                'privacy' => 'private',
            ]);
        });
    }

    /**
     * Relationships
     */
    public function joinedTeams(): BelongsToMany
    {
        return $this->belongsToMany(Team::class, 'team_user')->withPivot('role');
    }

    public function ownedTeams(): HasMany
    {
        return $this->hasMany(Team::class, 'owner_id');
    }

    public function currentTeam(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'current_team_id');
    }

    /**
     * Helper method to get all teams with proper access control
     */
    public function teams()
    {
        return $this->getAllUserTeams();
    }
}
