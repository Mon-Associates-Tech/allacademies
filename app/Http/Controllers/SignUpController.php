<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Http\Requests\SignUpRequest;
use App\Models\User;
use App\Models\Role;
use Illuminate\Auth\Events\Registered;
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
            $validated = $request->validated();
            $isAuthor = $request->boolean('author'); // Get the author checkbox value
            // Determine which role to assign based on checkbox
            $roleName = $isAuthor ? 'author' : 'subscriber';
            $userRoleEnum = $isAuthor ? UserRole::AUTHOR : UserRole::SUBSCRIBER;

            // Find the appropriate role from the roles table
            $role = Role::where('name', $roleName)
                ->orWhere('slug', $roleName)
                ->first();

            // If the role doesn't exist, fall back to subscriber
            if (!$role) {
                $role = Role::where('name', 'subscriber')
                    ->orWhere('slug', 'subscriber')
                    ->first();
                $userRoleEnum = UserRole::SUBSCRIBER;
            }

            /** @var User $user */
            $user = User::query()->create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => bcrypt($validated['password']),
                'role' => $userRoleEnum, // Keep the enum for backward compatibility
                'role_id' => $role?->id, // Set the role_id from the roles table
            ]);

            // Attach the role via many-to-many relationship for consistency
            if ($role) {
                $user->roles()->attach($role->id);
            }

            // Create personal team
            $team = $user->ownedTeams()->create([
                'name' => "{$user->name}'s Team",
                'is_personal' => true
            ]);

            $user->currentTeam()->associate($team)->save();

            return $user;
        });

        event(new Registered($user));

        // Store the user's email in session for the verification notice page
        $request->session()->put('verification_email', $user->email);

        // Create success message based on role
        $roleMessage = $request->boolean('author') ? 'author' : 'subscriber';
        $successMessage = "Registration successful as {$roleMessage}! Please check your email to verify your account before signing in.";

        // Redirect to email verification notice
        return redirect()->route('verification.notice')
            ->with('success', $successMessage);
    }
}
