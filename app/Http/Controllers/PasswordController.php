<?php

namespace App\Http\Controllers;

use App\Events\PasswordChangedEvent;
use App\Http\Requests\ChangePasswordRequest;
use App\Http\Requests\ForgotPasswordRequest;
use App\Http\Requests\ResetPasswordRequest;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class PasswordController extends Controller
{
    public function forgotForm()
    {
        return view('password.forgot');
    }

    public function forgot(ForgotPasswordRequest $request)
    {
        $status = Password::sendResetLink($request->validated());

        return Password::RESET_LINK_SENT === $status
            ? back()->with(['status' => __($status)])
            : back()->withErrors(['email' => __($status)]);
    }

    public function resetForm(Request $request, $token)
    {
        return view('password.reset', [
            'token' => $token,
            'email' => $request->query('email'),
        ]);
    }

    public function reset(ResetPasswordRequest $request)
    {
        $status = Password::reset($request->validated(), function (User $user, $password) {
            $user->forceFill(['password' => bcrypt($password)])
                ->setRememberToken(Str::random(60))
            ;

            $user->save();

            event(new PasswordReset($user));
        });

        return Password::PASSWORD_RESET === $status
            ? redirect()->route('sign-in')->with('status', __($status))
            : back()->withErrors(['email' => __($status)]);
    }

    public function changeForm()
    {
        return view('password.change');
    }

    /** @param \App\Models\User $user */
    public function change(Authenticatable $user, ChangePasswordRequest $request)
    {
        $user->forceFill(['password' => bcrypt($request->post('password'))])->save();

        event(new PasswordChangedEvent($user));

        return back()->with('status', __('status.password.changed'));
    }
}
