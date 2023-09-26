<?php

namespace App\Http\Controllers;

use App\Models\Team;
use App\Enums\SubscriptionStatus;
use App\Enums\SubscriptionPackage;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\TeamRequest;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\MetaDataRequest;
use App\Http\Controllers\MetaDataController;

class TeamController extends Controller
{
    public function activate(Team $team)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $team->load('members', 'owner');

        abort_unless($team->owner->is($user) || $team->members->contains($user), 403);

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

        $user->load(['currentTeam' => ['members', 'owner']]);
        $user->currentTeam->loadCount('subscriptions');

        $ownedTeams = $user->ownedTeams()->withCount('subscriptions')->with('metaData')->get();
        $ownedTeams->each(fn (Team $team) => $team->setRelation('owner', $user));

        $joinedTeams = $user->joinedTeams()->with('owner')->withCount('subscriptions')->with('metaData')->get();

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
        abort_unless($team->owner_id === auth()->id(), 403, 'You can not edit this team');
        $package = $team->subscriptions()->select('package')
            ->where('package', SubscriptionPackage::INSTITUTION_FULL)
            ->where('status', SubscriptionStatus::PAID)
            ->where('expires_at', '>', now())
            ->where('team_id', auth()->user()->current_team_id)
            ->get()->toArray();
        return view('teams.edit', [
            'team' => $team,
            'package' => $package,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Team  $team
     * @return \Illuminate\Http\Response
     */
    public function update(TeamRequest $request, MetaDataRequest $metarequest, Team $team)
    {
        abort_unless($team->owner_id === auth()->id(), 403, 'You can not edit this team');

        $team->update($request->validated());

        $metaData = (new MetaDataController)->updateOrCreate($metarequest, $team);

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
        abort_if($team->is_personal, 403, 'You can not delete a personal team');
        abort_unless($team->owner_id === auth()->id(), 403, "You can not delete another's team");

        DB::transaction(function () use ($team) {
            $team->members()->detach();
            $team->delete();
        });

        return to_route('teams.index')
            ->with('success', __('status.resource.deleted', ['name' => $team->name]));
    }
}
