<?php

namespace App\Http\Controllers;

use App\Http\Requests\SignInRequest;

class SignInController extends Controller
{
    public function new()
    {
        return view('sign-in');
    }

    public function store(SignInRequest $request)
    {
        if (auth()->attempt($request->validated())) {
            $request->session()->regenerate();

            return redirect()->intended('dashboard');
        }

        return back()->withErrors([
            'email' => trans('auth.failed'),
        ])->onlyInput('email');
    }
}
