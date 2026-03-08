<?php

namespace App\Http\Controllers;

use App\Enums\TeamStatus;
use App\Http\Requests\TeamRequest;
use App\Models\Team;
use App\Models\User;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class TeamController extends Controller
{
    public function activate(Team $team)
    {
        /** @var User $user */
        $user = Auth::user();

        // Check if user can switch to this team
        if (! $user->canSwitchToTeam($team)) {
            return redirect()->back()->withErrors(['error' => 'You cannot switch to this team.']);
        }

        $user->setCurrentTeam($team);

        return to_route('teams.index')->with('success', __('status.team.activate', ['name' => $team->name]));
    }

    /**
     * Display a listing of the resource.
     *
     * @return Application|Factory|\Illuminate\View\View|object|View
     */
    public function index()
    {
        /** @var User $user */
        $user = Auth::user();

        // Ensure user has a team
        $user->ensureUserHasTeam();

        $user->load('currentTeam');

        // Get all accessible teams using the unified method
        $teams = $user->getAllUserTeams();

        // Load additional data for display
        $teams->load(['owner', 'members']);
        $teams->loadCount('subscriptions', 'members');

        // Apply search filter
        if (request('search')) {
            $search = request('search');
            $teams = $teams->filter(function ($team) use ($search) {
                return stripos($team->name, $search) !== false;
            });
        }

        // Apply specific filters
        if (request('owned')) {
            $teams = $teams->where('owner_id', $user->id);
        } elseif (request('joined')) {
            $teams = $teams->where('owner_id', '!=', $user->id);
        }

        // Apply personal filter
        if (request('personal')) {
            $teams = $teams->where('is_personal', true);
        }

        return view('teams.index', [
            'teams' => $teams->values(), // Reset collection keys
            'user' => $user,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Factory|View|Application|\Illuminate\View\View|object
     */
    public function create()
    {
        return view('teams.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @throws \Throwable
     */
    public function store(Request $request): ?RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'min:2', 'max:100'],
            'description' => ['nullable', 'string', 'max:500'],
            'type' => ['required', 'in:academic,professional,personal'],
            'privacy' => ['required', 'in:private,public'],
            'allow_member_switching' => ['boolean'],
            'generate_code' => ['boolean'],
            'auto_activate' => ['boolean'],
        ], [
            'name.required' => 'Team name is required.',
            'name.min' => 'Team name must be at least 2 characters.',
            'name.max' => 'Team name cannot exceed 100 characters.',
            'description.max' => 'Description cannot exceed 500 characters.',
            'type.required' => 'Please select a team type.',
            'type.in' => 'Invalid team type selected.',
            'privacy.required' => 'Please select privacy settings.',
            'privacy.in' => 'Invalid privacy setting selected.',
        ]);

        try {
            DB::beginTransaction();

            $team = Team::create([
                'name' => $request->name,
                'description' => $request->description,
                'type' => $request->type,
                'privacy' => $request->privacy,
                'owner_id' => auth()->id(),
                'is_active' => true,
                'allow_member_switching' => $request->boolean('allow_member_switching', true),
                'is_personal' => $request->type === 'personal',
                'joining_code' => $request->boolean('generate_code', true) ? Str::random(8) : null,
            ]);

            // Log team creation
            Log::info('Team created', [
                'team_id' => $team->id,
                'owner_id' => auth()->id(),
                'team_name' => $team->name,
                'type' => $team->type,
            ]);

            // Auto-activate if requested
            if ($request->boolean('auto_activate', true)) {
                auth()->user()->setCurrentTeam($team);
            }

            DB::commit();

            $message = __('status.resource.created', ['name' => $team->name]);

            if ($team->joining_code) {
                $message .= ' '.__('Your team code is: :code', ['code' => $team->joining_code]);
            }

            return redirect()->route('teams.index')->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating team', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id(),
            ]);

            return back()
                ->withInput()
                ->withErrors(['error' => 'An error occurred while creating the team. Please try again.']);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
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
     * @return \Illuminate\Http\Response
     */
    public function update(TeamRequest $request, Team $team)
    {
        Gate::allowIf(fn ($user) => $user->id === $team->owner_id);

        $attributes = $request->validated();

        if (! $team->is_personal) {
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

                if ($path === false) {
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

    public function join(Request $request): RedirectResponse
    {
        $request->validate([
            'joining_code' => ['required', 'string', 'size:8'],
        ]);

        $team = Team::where('joining_code', $request->joining_code)->first();

        if (! $team) {
            return back()->withErrors(['joining_code' => 'Invalid joining code.']);
        }

        $user = Auth::user();

        if (! $team->addMember($user)) {
            return back()->withErrors(['error' => 'Unable to join this team.']);
        }

        return redirect()->route('teams.index')
            ->with('success', __('Successfully joined team: :name', ['name' => $team->name]));
    }

    /**
     * Leave a team
     */
    public function leave(Team $team): RedirectResponse
    {
        $user = Auth::user();

        if ($team->owner_id === $user->id) {
            return back()->withErrors(['error' => 'You cannot leave a team you own.']);
        }

        if (! $team->removeMember($user)) {
            return back()->withErrors(['error' => 'Unable to leave this team.']);
        }

        return redirect()->route('teams.index')
            ->with('success', __('Successfully left team: :name', ['name' => $team->name]));
    }
}
