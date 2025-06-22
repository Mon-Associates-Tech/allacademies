<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Http\Requests\SignUpRequest;
use App\Models\User;
use App\Models\Role;
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
        $user = DB::transaction(static function () use ($request) {
            // Find the subscriber role from the roles table
            $subscriberRole = Role::where('name', 'subscriber')
                ->orWhere('slug', 'subscriber')
                ->first();

            /** @var \App\Models\User $user */
            $user = User::query()->create([
                ...$request->validated(),
                'password' => bcrypt($request->validated('password')),
                'role' => UserRole::SUBSCRIBER, // Keep the enum for backward compatibility
                'role_id' => $subscriberRole?->id, // Set the role_id from the roles table
            ]);

            // Also attach the role via many-to-many relationship for consistency
            if ($subscriberRole) {
                $user->roles()->attach($subscriberRole->id);
            }

            $team = $user->ownedTeams()->create(['name' => "{$user->name}'s Team", 'is_personal' => true]);

            $user->currentTeam()->associate($team)->save();

            return $user;
        });

        event(new Registered($user));

        // Store the user's email in session for the verification notice page
        $request->session()->put('verification_email', $user->email);

        // Redirect to email verification notice
        return redirect()->route('verification.notice')
            ->with('success', 'Registration successful! Please check your email to verify your account before signing in.');
    }
}
