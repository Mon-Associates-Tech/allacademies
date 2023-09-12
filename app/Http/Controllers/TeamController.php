<?php

namespace App\Http\Controllers;

use App\Http\Requests\TeamRequest;
use App\Models\Team;
use App\Models\MetaData;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

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

        return view('teams.edit', [
            'team' => $team,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Team  $team
     * @return \Illuminate\Http\Response
     */
    public function update(TeamRequest $request,  Team $team)
    {
        abort_unless($team->owner_id === auth()->id(), 403, 'You can not edit this team');

        $team->update($request->validated());

        //create or update meta data
        $path = is_null($team->metaData) ? null : $team->metaData->meta['logo'] ?? '';
        if($request->hasFile('logo')){
            $path = $request->file('logo')->store('images', 'public');
        }

        $meta = [
            'school' => $request->school,
            'department' => $request->department,
            'logo' => $path
        ];
  
        MetaData::updateOrCreate(
            ['team_id' => $team->id],
            ['meta' => $meta]
        );
    
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
