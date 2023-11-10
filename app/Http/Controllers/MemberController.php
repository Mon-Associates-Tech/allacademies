<?php

namespace App\Http\Controllers;

use App\Models\Team;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Requests\MemberRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class MemberController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Team $team)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $team->load(['members' => function ($query) {
            $query->withPivot('role');
        }, 'owner']);
        $team->members->push($team->owner);
        $team->members = $team->members->sort();

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

        $team->members()->attach($user);

        return to_route('teams.index')
            ->with('success', __('status.member.added', [
                'member' => $user->name,
                'team' => $team->name,
            ]));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Team  $team
     * @param  User  $member
     * @return \Illuminate\Http\Response
     */
    public function destroy(Team $team, User $member)
    {
        $member->load('currentTeam');

        if ($member->currentTeam->is($team)) {
            $member->currentTeam()->associate($member->ownedTeams()->where('is_personal', true)->first())->save();
        }

        $team->members()->detach($member);

        return to_route('teams.index')->with('success', __('status.member.removed', ['member' => $member->name, 'team' => $team->name]));
    }

    /**
     * Change team member role from member to admin and the vice versa
     * @param  Team  $team
     * @param  User  $member 
     * @return \Illuminate\Http\Response
     */
    public function changeMemberRole(Team $team, User $user)
    {
        /** @var \App\Models\User $user */
        $relationship = $team->members()->where('user_id', $user->id)->first();

        $relationship->pivot->role = ($relationship->pivot->role === 'member') ? 'admin' : 'member';

        $relationship->pivot->save();

        return to_route('teams.members.index', ['team' => $team])->with('success', __('status.member.role_changed', [
            'member' => $user->name,
            'new_role' => $relationship->pivot->role,
            'team' => $team->name,
        ]));
    }
}
