<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Http\Requests\SignUpRequest;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SignUpController extends Controller
{
    public function create()
    {
        return view('sign-up');
    }

    public function store(SignUpRequest $request)
    {
        $user = User::query()->create([
            ...$request->validated(),
            'password' => bcrypt($request->validated('password')),
            'role' => UserRole::SUBSCRIBER,
        ]);

        event(new Registered($user));

        Auth::login($user);

        return to_route('dashboard');
    }
}
