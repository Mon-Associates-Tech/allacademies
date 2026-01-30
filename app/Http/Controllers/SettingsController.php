<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Http\Requests\RoleRequest;
use App\Models\User;
use Illuminate\Validation\ValidationException;

class SettingsController extends Controller
{
    public function index()
    {
        $this->authorize('own');

        return view('settings.index');
    }

    public function role(RoleRequest $request)
    {
        $this->authorize('own');

        if ($request->isMethod('POST')) {
            $user = User::query()->where('email', $request->validated('email'))->firstOrFail();

            if ($user->role === UserRole::OWNER) {
                throw ValidationException::withMessages([
                    'email' => "You can not change this user's role",
                ]);
            }

            $user->role = UserRole::from($request->validated('role'));

            $user->save();

            return to_route('settings.index')->with('success', __('status.settings.role', [
                'name' => $user->name,
                'role' => $user->role->value,
            ]));
        }

        return view('settings.role');
    }
}
