<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Http\Requests\SignUpRequest;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SignUpController extends Controller
{
    public function create()
    {
        return view('sign-up');
    }

    public function store(SignUpRequest $request)
    {
        $user = DB::transaction(function () use ($request) {
            /** @var \App\Models\User $user */
            $user = User::query()->create([
                ...$request->validated(),
                'password' => bcrypt($request->validated('password')),
                'role' => UserRole::SUBSCRIBER,
            ]);

            $team = $user->ownedTeams()->create(['name' => "{$user->name}'s Team", 'is_personal' => true]);

            $user->currentTeam()->associate($team)->save();

            return $user;
        });

        event(new Registered($user));

        Auth::login($user);

        return to_route('dashboard');
    }
}
