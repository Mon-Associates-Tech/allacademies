<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateProfileRequest;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Arr;

class ProfileController extends Controller
{
    public function show(Authenticatable $user)
    {
        return view('profile.show', [
            'user' => $user,
        ]);
    }

    public function edit(Authenticatable $user)
    {
        return view('profile.edit', [
            'user' => $user,
        ]);
    }

    /** @param \App\Models\User $user */
    public function update(Authenticatable $user, UpdateProfileRequest $request)
    {
        $user->update(Arr::except($request->validated(), ['avatar', 'force_update_avatar']));

        if ($user->wasChanged('email')) {
            $user->forceFill(['email_verified_at' => null])->save();
            $user->sendEmailVerificationNotification();
        }

        $user->updateAvatar($request->file('avatar'), $request->post('force_update_avatar'));

        return redirect()->route('profile.show')->with('success', __('status.profile.updated'));
    }
}
