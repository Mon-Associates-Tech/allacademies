<?php

namespace App\Http\Controllers;

use App\Models\Team;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class JoinTeamController extends Controller
{
    /**
     * Generate member joining code
     *
     * @param  \App\Models\Team  $team
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
     * remove member joining code
     *
     * @param  \App\Models\Team  $team
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
     *  @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function join(Request $request)
    {
        $this->validate($request, [
            'code' => ['required', 'string', 'size:8'],
        ]);

        $user = Auth::user();
        $code = $request->code;

        $team = Team::where('joining_code', $code)
            ->firstOr(callback: function () {
                throw ValidationException::withMessages([
                    'code' => 'Invalid joining code',
                ]);
            });

        !$team->members->contains($user) && !$team->owner->is($user) ?: throw ValidationException::withMessages([
            'code' => 'You are already a member of this team',
        ]);

        $team->members()->attach($user);

        return to_route('teams.index')
            ->with('success', __('status.resource.joined_team', ['name' => $team->name]));
    }
}
