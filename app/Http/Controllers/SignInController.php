<?php

namespace App\Http\Controllers;

use App\Http\Requests\SignInRequest;

class SignInController extends Controller
{
    public function create()
    {
        return view('sign-in');
    }

    public function store(SignInRequest $request)
    {
        if (auth()->attempt($request->only('email', 'password'), $request->boolean('remember'))) {
            $request->session()->regenerate();

            return redirect()->intended('dashboard');
        }

        return back()->withErrors([
            'email' => trans('auth.failed'),
        ])->onlyInput('email');
    }
}
