<?php

namespace App\Http\Controllers;

use App\Models\Team;
use App\Enums\TeamStatus;
use App\Notifications\ApproveTeam;
use App\Notifications\DeclineTeam;
use App\Http\Requests\PendingTeamsRequest;

class PendingTeamsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->authorize('administrate');

        $pendingTeams = Team::orderBy('updated_at', 'asc')->where('status', TeamStatus::PENDING)->paginate();

        return view('pending-teams.index', [
            'pendingTeams' => $pendingTeams,
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Team  $team
     * @return \Illuminate\Http\Response
     */
    public function show(Team $pending_team)
    {
        $metaData = $pending_team->metaData()->pluck('meta')->toArray();

        return view('pending-teams.show', [
            'team' => $pending_team,
            'institutionDetails' => $metaData,
        ]);
    }


    /**
     * approve the specified resource.
     *
     * @param  \App\Models\Team  $team
     * @return \Illuminate\Http\Response
     */
    public function approve(Team $team)
    {
        $this->authorize('administrate');

        $team->update(['status' => 'approved']);

        $user = auth()->user();
        $message =  $team->name . " has been approved. You are all set to create examinations.";
        $user->notify(new ApproveTeam($message));

        return to_route('pending-teams.index')
            ->with('success', __('status.resource.approved', ['name' => $team->name]));
    }

    /**
     * Show the form for declining the specified resource.
     *
     * @param  \App\Models\Team  $team
     * @return \Illuminate\Http\Response
     */
    public function decline(Team $team)
    {
        $this->authorize('administrate');

        return view('pending-teams.decline', [
            'team' => $team,
        ]);
    }

    /**
     * decline the specified resource.
     *
     * @param  \App\Models\Team  $team
     * @return \Illuminate\Http\Response
     */
    public function declineTeam(PendingTeamsRequest $request, Team $pending_team)
    {
        $pending_team->update($request->validated());

        $user = auth()->user();
        $message =  $pending_team->name . " has been declined.";
        $reason = $request->validated('reason');
        $user->notify(new DeclineTeam($message, $reason));

        return to_route('pending-teams.index')
            ->with('success', __('status.resource.declined', ['name' => $pending_team->name]));
    }
}
