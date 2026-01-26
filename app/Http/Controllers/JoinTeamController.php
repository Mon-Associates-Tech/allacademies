<?php

namespace App\Http\Controllers;

use App\Models\Team;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class JoinTeamController extends Controller
{
    /**
     * Generate member joining code
     *
     * @return \Illuminate\Http\Response
     */
    public function generate(Team $team)
    {
        Gate::allowIf(fn ($user) => $user->id === $team->owner_id);

        retry(5, function () use ($team) {
            $team->update(['joining_code' => Str::random(8)]);
        });

        return back()->with('success', __('status.resource.generate_code', ['name' => $team->name]));
    }

    /**
     * Remove member joining code
     *
     * @return \Illuminate\Http\Response
     */
    public function remove(Team $team)
    {
        Gate::allowIf(fn ($user) => $user->id === $team->owner_id);

        $team->joining_code = null;
        $team->save();

        return back()
            ->with('success', __('status.resource.remove_code', ['name' => $team->name]));
    }

    /**
     * Show the form for joining a team
     *
     * @return \Illuminate\Http\Response
     */
    public function joining()
    {
        return view('teams.joining');
    }

    /**
     * Add member to a team
     *
     * @return \Illuminate\Http\Response
     */
    public function join(Request $request)
    {
        $this->validate($request, [
            'code' => ['required', 'string', 'size:8', 'regex:/^[A-Z0-9]+$/'],
        ], [
            'code.required' => 'Please enter a team code.',
            'code.size' => 'Team code must be exactly 8 characters.',
            'code.regex' => 'Team code can only contain letters and numbers.',
        ]);

        $user = Auth::user();
        $code = strtoupper(trim($request->code));

        try {
            DB::beginTransaction();

            $team = Team::where('joining_code', $code)
                ->firstOr(function () {
                    throw ValidationException::withMessages([
                        'code' => 'Invalid team code. Please check the code and try again.',
                    ]);
                });

            // Check if code is still active (optional: add expiration logic)
            if (empty($team->joining_code)) {
                throw ValidationException::withMessages([
                    'code' => 'This team code is no longer active. Please contact the team owner for a new code.',
                ]);
            }

            // Check if user is already a member
            if ($team->members->contains($user)) {
                throw ValidationException::withMessages([
                    'code' => 'You are already a member of this team.',
                ]);
            }

            // Check if user is the owner
            if ($team->owner->is($user)) {
                throw ValidationException::withMessages([
                    'code' => 'You cannot join your own team as you are the owner.',
                ]);
            }

            // Check team member limit (if applicable)
            $memberLimit = config('teams.max_members', 50); // Add this to config
            if ($team->members()->count() >= $memberLimit) {
                throw ValidationException::withMessages([
                    'code' => 'This team has reached its maximum member limit.',
                ]);
            }

            // Add user to team
            $team->members()->attach($user, [
                'joined_at' => now(),
                'role' => 'member', // Default role
            ]);

            DB::commit();

            // Log the successful join
            Log::info('User joined team', [
                'user_id' => $user->id,
                'team_id' => $team->id,
                'team_name' => $team->name,
            ]);

            return to_route('teams.index')
                ->with('success', __('status.resource.joined_team', ['name' => $team->name]));

        } catch (ValidationException $e) {
            DB::rollBack();
            throw $e;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error joining team', [
                'user_id' => $user->id,
                'code' => $code,
                'error' => $e->getMessage(),
            ]);

            throw ValidationException::withMessages([
                'code' => 'An error occurred while joining the team. Please try again.',
            ]);
        }
    }

    /**
     * Show team preview before joining (optional enhancement)
     *
     * @return \Illuminate\Http\Response
     */
    public function preview(Request $request)
    {
        $this->validate($request, [
            'code' => ['required', 'string', 'size:8'],
        ]);

        $code = strtoupper(trim($request->code));
        $team = Team::where('joining_code', $code)->first();

        if (! $team) {
            return response()->json([
                'error' => 'Invalid team code',
            ], 404);
        }

        return response()->json([
            'team' => [
                'name' => $team->name,
                'description' => $team->description,
                'member_count' => $team->members()->count(),
                'owner' => $team->owner->name,
                'created_at' => $team->created_at->format('M Y'),
            ],
        ]);
    }
}
