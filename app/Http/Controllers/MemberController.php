<?php

namespace App\Http\Controllers;

use App\Http\Requests\MemberRequest;
use App\Models\Team;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;

class MemberController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Team $team, Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Load team relationships
        $team->load(['members' => function ($query) use ($request) {
            // Apply search filter
            if ($request->filled('search')) {
                $search = $request->input('search');
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'LIKE', "%{$search}%")
                        ->orWhere('email', 'LIKE', "%{$search}%");
                });
            }

            // Apply role filter
            if ($request->filled('role') && $request->input('role') !== 'owner') {
                $query->where('team_user.role', $request->input('role'));
            }
        }, 'owner']);

        // Create members collection including owner
        $members = collect([$team->owner])->merge($team->members);

        // Apply owner filter if specified
        if ($request->filled('role') && $request->input('role') === 'owner') {
            $members = collect([$team->owner]);
        } elseif ($request->filled('role') && $request->input('role') !== 'owner') {
            $members = $team->members;
        }

        // Apply search filter to owner if needed
        if ($request->filled('search') && $members->contains($team->owner)) {
            $search = strtolower($request->input('search'));
            if (! (str_contains(strtolower($team->owner->name), $search) ||
                  str_contains(strtolower($team->owner->email), $search))) {
                $members = $members->reject(function ($member) use ($team) {
                    return $member->is($team->owner);
                });
            }
        }

        // Sort members: owner first, then by name
        $members = $members->sortBy(function ($member) use ($team) {
            return $team->owner->is($member) ? '0'.$member->name : '1'.$member->name;
        });

        return view('members.index', [
            'team' => $team,
            'user' => $user,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Team $team)
    {
        Gate::allowIf($team->owner_id === auth()->id() && ! $team->is_personal);

        return view('members.create', [
            'team' => $team,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Team $team, MemberRequest $request)
    {
        if ($team->is_personal) {
            throw ValidationException::withMessages([
                'email' => 'You can not add members to this team',
            ]);
        }

        $team->load('owner', 'members');

        if ($team->owner->isNot(auth()->user())) {
            throw ValidationException::withMessages([
                'email' => 'You did not create this team',
            ]);
        }

        $user = User::query()->where('email', $request->validated('email'))->firstOrFail();

        if ($team->owner->is($user) || $team->members->contains($user)) {
            throw ValidationException::withMessages([
                'email' => 'User already in team',
            ]);
        }

        $team->members()->attach($user, [
            'role' => 'member',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return to_route('teams.members.index', ['team' => $team])
            ->with('success', __('status.member.added', [
                'member' => $user->name,
                'team' => $team->name,
            ]));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Team $team, User $member)
    {
        Gate::allowIf($team->owner_id === auth()->id());

        if ($team->owner->is($member)) {
            throw ValidationException::withMessages([
                'member' => 'Cannot remove team owner',
            ]);
        }

        $member->load('currentTeam');

        if ($member->currentTeam && $member->currentTeam->is($team)) {
            $personalTeam = $member->ownedTeams()->where('is_personal', true)->first();
            if ($personalTeam) {
                $member->currentTeam()->associate($personalTeam)->save();
            }
        }

        $team->members()->detach($member);

        return to_route('teams.members.index', ['team' => $team])
            ->with('success', __('status.member.removed', [
                'member' => $member->name,
                'team' => $team->name,
            ]));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(Team $team, User $member)
    {
        Gate::allowIf($team->owner_id === auth()->id());

        $memberData = $team->members()->where('user_id', $member->id)->firstOrFail();

        return view('members.edit', [
            'team' => $team,
            'member' => $memberData,
        ]);
    }

    /**
     * Change team member role from member to admin and the vice versa
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Team $team, User $member)
    {
        Gate::allowIf($team->owner_id === auth()->id());

        $request->validate([
            'role' => 'required|in:member,admin',
        ]);

        if ($team->owner->is($member)) {
            throw ValidationException::withMessages([
                'role' => 'Cannot change owner role',
            ]);
        }

        $team->members()->updateExistingPivot($member->id, [
            'role' => $request->role,
            'updated_at' => now(),
        ]);

        return to_route('teams.members.index', ['team' => $team])
            ->with('success', __('status.member.role_changed', [
                'member' => $member->name,
                'new_role' => $request->role,
                'team' => $team->name,
            ]));
    }
}
