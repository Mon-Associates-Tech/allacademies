<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Auth\EmailVerificationRequest;

class EmailVerificationController extends Controller
{
    public function notice()
    {
        return view('verification-notice');
    }

    public function verify(EmailVerificationRequest $request)
    {
        $request->fulfill();

        return redirect()->route('dashboard');
    }

    /** @param \App\Models\User $user */
    public function send(Authenticatable $user)
    {
        $user->sendEmailVerificationNotification();

        return back()->with('success', __('status.verification.sent'));
    }
}
