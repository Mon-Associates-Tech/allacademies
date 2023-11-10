<?php

namespace App\Http\Controllers;

use App\Enums\TeamStatus;
use App\Models\Team;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\TeamRequest;
use App\Enums\SubscriptionStatus;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use App\Http\Requests\joinTeamRequest;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class TeamController extends Controller
{
    public function activate(Team $team)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $team->load('members', 'owner');

        Gate::allowIf($team->owner->is($user) || $team->members->contains($user));

        $user->currentTeam()->associate($team)->save();

        return to_route('teams.index')->with('success', __('status.team.activate', ['name' => $team->name]));
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $user->load('currentTeam');

        $ownedTeams = $user->ownedTeams()->withCount('subscriptions')->get();
        $ownedTeams->each(fn (Team $team) => $team->setRelation('owner', $user));

        $joinedTeams = $user->joinedTeams()->with('owner')->withCount('subscriptions')->get();

        $teams = $ownedTeams->merge($joinedTeams);
        unset($ownedTeams, $joinedTeams);
        $teams = $teams->sort();

        return view('teams.index', [
            'teams' => $teams,
            'user' => $user,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('teams.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(TeamRequest $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $team = new Team($request->validated());
        $team->owner()->associate($user);
        $team->save();

        return to_route('teams.index')
            ->with('success', __('status.resource.created', ['name' => $team->name]));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Team  $team
     * @return \Illuminate\Http\Response
     */
    public function edit(Team $team)
    {
        Gate::allowIf($team->owner_id === auth()->id());

        return view('teams.edit', [
            'team' => $team,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\TeamRequest  $request
     * @param  \App\Models\Team  $team
     * @return \Illuminate\Http\Response
     */
    public function update(TeamRequest $request, Team $team)
    {
        Gate::allowIf(fn ($user) => $user->id === $team->owner_id);

        $attributes = $request->validated();

        if (!$team->is_personal) {
            $validated = $request->validate([
                'type' => ['required', 'string', 'in:institution,department,faculty,college'],
                'institution' => ['required', 'string', 'min:2', 'max:255'],
                'department' => ['exclude_if:type,institution', 'required_unless:type,institution', 'string', 'min:2', 'max:255'],
                'faculty' => ['exclude_unless:type,faculty', 'required_if:type,faculty', 'string', 'min:2', 'max:255'],
                'school' => ['exclude_unless:type,college', 'required_if:type,college', 'string', 'min:2', 'max:255'],
                'college' => ['exclude_unless:type,college', 'required_if:type,college', 'string', 'min:2', 'max:255'],
                'logo' => ['nullable', 'image'],
            ], [
                'required_if' => 'The :attribute field is required.',
                'required_unless' => 'The :attribute field is required.',
            ], [
                'institution' => 'name',
            ]);

            $logo = Arr::pull($validated, 'logo');

            Arr::set($attributes, 'meta', [
                ...$team->meta,
                'future' => $validated,
            ]);

            if ($logo) {
                $path = $request->file('logo')->storePublicly('logos', 's3');

                if (false === $path) {
                    throw ValidationException::withMessages(['logo' => 'Logo upload failed.']);
                }

                Arr::set($attributes, 'meta.logo', $path);

                if ($logo = Arr::get($team->meta, 'logo')) {
                    Storage::disk('s3')->delete($logo);
                }
            }

            if (
                array_diff(
                    Arr::get($attributes, 'meta.future', []),
                    Arr::get($attributes, 'meta.present', [])
                )
            ) {
                Arr::set($attributes, 'status', TeamStatus::PENDING);
            }
        }

        $team->update($attributes);

        return to_route('teams.index')
            ->with('success', __('status.resource.updated', ['name' => $team->name]));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Team  $team
     * @return \Illuminate\Http\Response
     */
    public function destroy(Team $team)
    {
        Gate::denyIf($team->is_personal);
        Gate::allowIf(fn ($user) => $user->id === $team->owner_id);

        DB::transaction(function () use ($team) {
            $team->members()->detach();
            $team->delete();
        });

        return to_route('teams.index')
            ->with('success', __('status.resource.deleted', ['name' => $team->name]));
    }

    /**
     * Generate members joining code
     *
     * @param  \App\Models\Team  $team
     * @return \Illuminate\Http\Response
     */
    public function generateJoiningCode(Team $team)
    {
        Gate::allowIf(fn ($user) => $user->id === $team->owner_id);
        $membersJoiningCode = uniqid();
        $team->update(['joining_code' => $membersJoiningCode]);

        return view('teams.edit', [
            'team' => $team,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function joinTeam()
    {
        return view('teams.join-team');
    }

    /**
     * Add member to a team
     *  @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function addTeamMember(joinTeamRequest $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $code = $request->code;
        $team = Team::where('joining_code', $code)
            ->with(['subscriptions' => function ($query) {
                $query->where('expires_at', '>', now())
                    ->where('status', SubscriptionStatus::PAID);
            }])
            ->firstOr(callback: function () {
                throw ValidationException::withMessages([
                    'code' => 'No team found for the provided code.',
                ]);
            });

        $subscription = $team->subscriptions->first();
        $subscription ?: throw ValidationException::withMessages(['code' => 'No active subscription for this team.']);

        $beneficiaryCount = $subscription->beneficiaries;
        $teamMembersCount = $team->members()->count();
        $teamMembersCount < $beneficiaryCount ?: throw ValidationException::withMessages([
            'code' => 'This team has exceeded the allowed number of members.',
        ]);

        !$team->members->contains($user) ?: throw ValidationException::withMessages([
            'code' => 'You are already a member of this team.',
        ]);

        $user->joinedTeams()->attach($team);

        return to_route('teams.index')
            ->with('success', __('status.resource.joined_team', ['name' => $team->name]));
    }
}
