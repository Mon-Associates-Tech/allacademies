<?php

namespace App\Http\Controllers;

use App\Models\Team;
use App\Enums\TeamStatus;
use App\Notifications\ApproveTeam;
use App\Notifications\DeclineTeam;
use App\Http\Requests\ManageTeamsRequest;

class ManageTeamsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->authorize('administrate');

        $pendingTeams = Team::query()->orderBy('updated_at', 'asc')->where('status', TeamStatus::PENDING)->paginate();

        return view('manage-teams.index', [
            'pendingTeams' => $pendingTeams,
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

        return to_route('manage-teams.index')
            ->with('success', __('status.resource.approved', ['name' => $team->name]));
    }

    /**
     * Show the form for decining the specified resource.
     *
     * @param  \App\Models\Team  $team
     * @return \Illuminate\Http\Response
     */
    public function edit(Team $team)
    {
        $this->authorize('administrate');

        return view('manage-teams.edit', [
            'team' => $team,
        ]);
    }

    /**
     * decline the specified resource.
     *
     * @param  \App\Models\Team  $team
     * @return \Illuminate\Http\Response
     */
    public function decline(ManageTeamsRequest $request, Team $team)
    {
        $team->update($request->validated());

        $user = auth()->user();
        $message =  $team->name . " has been declined.";
        $reason = $request->validated('reason');
        $user->notify(new DeclineTeam($message, $reason));

        return to_route('manage-teams.index')
            ->with('success', __('status.resource.declined', ['name' => $team->name]));
    }
}
