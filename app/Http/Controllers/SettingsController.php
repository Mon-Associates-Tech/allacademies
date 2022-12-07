<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Http\Requests\RoleRequest;
use App\Models\User;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function index()
    {
        $this->authorize('preside');

        return view('settings.index');
    }

    public function role(RoleRequest $request)
    {
        $this->authorize('preside');

        if ($request->isMethod('POST')) {
            $user = User::query()->where('email', $request->validated('email'))->firstOrFail();

            $user->role = UserRole::from($request->validated('role'));

            $user->save();

            return to_route('settings.index');
        }

        return view('settings.role');
    }
}
