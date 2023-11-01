<?php

namespace App\Http\Controllers;

use App\Models\Team;
use App\Enums\TeamStatus;
use App\Notifications\TeamApprovedNotification;
use App\Notifications\TeamDeclinedNotification;
use App\Support\Wordy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;

class AuditTeamController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->authorize('administrate');

        $auditTeams = Team::query()->where('status', TeamStatus::PENDING)->with('owner')->oldest('updated_at')->paginate();

        return view('audit-teams.index', [
            'auditTeams' => $auditTeams,
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Team  $auditTeam
     * @return \Illuminate\Http\Response
     */
    public function show(Team $auditTeam)
    {
        $this->authorize('administrate');

        $audits = array_map(function ($current, $incoming, $heading) use ($auditTeam) {
            return [
                $heading,
                $current = data_get($auditTeam->meta, $current, []),
                $incoming = data_get($auditTeam->meta, $incoming, []),
                Wordy::changes($current, $incoming),
            ];
        }, ['past', 'present'], ['present', 'future'], ['Past Changes', 'Present Changes']);

        return view('audit-teams.show', [
            'auditTeam' => $auditTeam,
            'audits' => $audits
        ]);
    }


    /**
     * approve the specified resource.
     *
     * @param  \App\Models\Team  $auditTeam
     * @return \Illuminate\Http\Response
     */
    public function approve(Team $auditTeam)
    {
        $this->authorize('administrate');

        abort_unless($auditTeam->status === TeamStatus::PENDING, 403);

        $auditTeam->update([
            'status' => TeamStatus::APPROVED,
            'meta' => [
                'past' => $auditTeam->meta['present'] ?? [],
                'present' => $auditTeam->meta['future'] ?? [],
            ]
        ]);

        Notification::send(auth()->user(), new TeamApprovedNotification($auditTeam));

        return to_route('audit-teams.index')
            ->with('success', __('status.resource.approved', ['name' => $auditTeam->name]));
    }

    /**
     * Show the form for declining the specified resource.
     *
     * @param  \App\Models\Team  $auditTeam
     * @return \Illuminate\Http\Response
     */
    public function reason(Team $auditTeam)
    {
        $this->authorize('administrate');

        return view('audit-teams.decline', [
            'auditTeam' => $auditTeam,
        ]);
    }

    /**
     * decline the specified resource.
     *
     * @param  \App\Models\Team  $auditTeam
     * @return \Illuminate\Http\Response
     */
    public function decline(Request $request, Team $auditTeam)
    {
        $this->authorize('administrate');

        $validated = $request->validate([
            'reason' => ['required', 'string', 'min:2', 'max:1023'],
        ]);

        abort_unless($auditTeam->status === TeamStatus::PENDING, 403);

        $reason = $validated['reason'];
        $meta = $auditTeam->meta;

        unset($meta['future']);

        $auditTeam->update([
            'meta' => $meta,
            'status' => TeamStatus::DECLINED,
            'declined_reason' => $reason
        ]);

        Notification::send(auth()->user(), new TeamDeclinedNotification($auditTeam));

        return to_route('audit-teams.index')
            ->with('success', __('status.resource.declined', ['name' => $auditTeam->name]));
    }
}
