<?php

namespace App\Http\Controllers;

use App\Http\Requests\MemberRequest;
use App\Models\Team;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class MemberController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Team $team)
    {
        return view('members.create', [
            'team' => $team,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Team $team, MemberRequest $request)
    {
        $team->load('owner', 'members');

        $user = User::query()->where('email', $request->validated('email'))->firstOrFail();

        if ($team->owner->is($user) || $team->members->contains($user)) {
            throw ValidationException::withMessages([
                'email' => 'User already in team',
            ]);
        }

        $team->members()->attach($user);

        return to_route('teams.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Team  $team
     * @param  User  $member
     * @return \Illuminate\Http\Response
     */
    public function destroy(Team $team, User $member)
    {
        $team->members()->detach($member);

        return to_route('teams.index');
    }
}
