<?php

namespace App\Http\Controllers;

use App\Enums\TeamStatus;
use App\Models\Team;
use App\Notifications\TeamApprovedNotification;
use App\Notifications\TeamDeclinedNotification;
use App\Support\Wordy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Notification;

class AuditTeamController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->authorize('administrate');

        $auditTeams = Team::query()
            ->where('status', TeamStatus::PENDING)
            ->with(['owner', 'members'])
            ->withCount('members')
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->input('search');
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'LIKE', "%{$search}%")
                        ->orWhereHas('owner', function ($ownerQuery) use ($search) {
                            $ownerQuery->where('name', 'LIKE', "%{$search}%")
                                ->orWhere('email', 'LIKE', "%{$search}%");
                        });
                });
            })
            ->when($request->filled('sort'), function ($query) use ($request) {
                $sort = $request->input('sort');
                switch ($sort) {
                    case 'name_asc':
                        $query->orderBy('name', 'asc');
                        break;
                    case 'name_desc':
                        $query->orderBy('name', 'desc');
                        break;
                    case 'owner_asc':
                        $query->join('users', 'teams.owner_id', '=', 'users.id')
                            ->orderBy('users.name', 'asc')
                            ->select('teams.*');
                        break;
                    case 'owner_desc':
                        $query->join('users', 'teams.owner_id', '=', 'users.id')
                            ->orderBy('users.name', 'desc')
                            ->select('teams.*');
                        break;
                    case 'members_asc':
                        $query->orderBy('members_count', 'asc');
                        break;
                    case 'members_desc':
                        $query->orderBy('members_count', 'desc');
                        break;
                    case 'newest':
                        $query->latest('created_at');
                        break;
                    case 'oldest_updated':
                    default:
                        $query->oldest('updated_at');
                        break;
                }
            }, function ($query) {
                // Default sorting
                $query->oldest('updated_at');
            })
            ->paginate(15)
            ->withQueryString();

        $stats = [
            'total' => Team::where('status', TeamStatus::PENDING)->count(),
            'this_week' => Team::where('status', TeamStatus::PENDING)
                ->where('created_at', '>=', now()->startOfWeek())
                ->count(),
            'this_month' => Team::where('status', TeamStatus::PENDING)
                ->where('created_at', '>=', now()->startOfMonth())
                ->count(),
        ];

        return view('audit-teams.index', [
            'auditTeams' => $auditTeams,
            'stats' => $stats,
        ]);
    }

    /**
     * Display the specified resource.
     *
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
            'audits' => $audits,
        ]);
    }

    /**
     * approve the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function approve(Team $auditTeam)
    {
        $this->authorize('administrate');

        Gate::allowIf($auditTeam->status === TeamStatus::PENDING);

        $auditTeam->load('owner');

        $auditTeam->update([
            'status' => TeamStatus::APPROVED,
            'meta' => [
                'past' => $auditTeam->meta['present'] ?? [],
                'present' => $auditTeam->meta['future'] ?? [],
            ],
        ]);

        Notification::send($auditTeam->owner, new TeamApprovedNotification($auditTeam));

        return to_route('audit-teams.index')
            ->with('success', __('status.resource.approved', ['name' => $auditTeam->name]));
    }

    /**
     * Show the form for declining the specified resource.
     *
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
     * @return \Illuminate\Http\Response
     */
    public function decline(Request $request, Team $auditTeam)
    {
        $this->authorize('administrate');

        $validated = $request->validate([
            'reason' => ['required', 'string', 'min:2', 'max:1023'],
        ]);

        Gate::allowIf($auditTeam->status === TeamStatus::PENDING);

        $auditTeam->load('owner');

        $reason = $validated['reason'];
        $meta = $auditTeam->meta;

        unset($meta['future']);

        $auditTeam->update([
            'meta' => $meta,
            'status' => TeamStatus::DECLINED,
            'declined_reason' => $reason,
        ]);

        Notification::send($auditTeam->owner, new TeamDeclinedNotification($auditTeam));

        return to_route('audit-teams.index')
            ->with('success', __('status.resource.declined', ['name' => $auditTeam->name]));
    }

    /**
     * Bulk approve multiple teams
     *
     * @return \Illuminate\Http\Response
     */
    public function bulkApprove(Request $request)
    {
        $this->authorize('administrate');

        $validated = $request->validate([
            'team_ids' => ['required', 'array'],
            'team_ids.*' => ['exists:teams,id'],
        ]);

        $teams = Team::whereIn('id', $validated['team_ids'])
            ->where('status', TeamStatus::PENDING)
            ->with('owner')
            ->get();

        $approvedCount = 0;
        foreach ($teams as $team) {
            $team->update([
                'status' => TeamStatus::APPROVED,
                'meta' => [
                    'past' => $team->meta['present'] ?? [],
                    'present' => $team->meta['future'] ?? [],
                ],
            ]);

            Notification::send($team->owner, new TeamApprovedNotification($team));
            $approvedCount++;
        }

        return to_route('audit-teams.index')
            ->with('success', "Successfully approved {$approvedCount} team(s).");
    }
}
