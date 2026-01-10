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
        return view('register');
    }

    public function store(SignUpRequest $request)
    {
        $user = DB::transaction(static function () use ($request) {
            $validated = $request->validated();

            $isAuthor = $request->boolean('author'); // Check if user selected 'author'
            $isNewSchool = $request->boolean('newschool'); // Check if user wants to onboard a school

            // Determine which role to assign
            $roleName = $isAuthor ? 'author' : 'subscriber';
            $userRoleEnum = $isAuthor ? UserRole::AUTHOR : UserRole::SUBSCRIBER;

            // Find the appropriate role from the roles table
            $role = Role::where('name', $roleName)
                ->orWhere('slug', $roleName)
                ->first();

            // Fallback to subscriber if role missing
            if (!$role) {
                $role = Role::where('name', 'subscriber')
                    ->orWhere('slug', 'subscriber')
                    ->first();
                $userRoleEnum = UserRole::SUBSCRIBER;
            }

            /** @var User $user */
            $user = User::query()->create([
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'],
                'other_names' => $validated['other_names'] ?? null,
                'name' => User::generateNameFromParts(
                    $validated['first_name'],
                    $validated['last_name'],
                    $validated['other_names'] ?? null
                ),
                'email' => $validated['email'],
                'password' => bcrypt($validated['password']),
                'gender' => $validated['gender'] ?? null,
                'country_code' => $validated['country_code'] ?? null,
                'country' => $validated['country'],
                'region' => ($validated['region'] ?? null) ?: ($validated['region_manual'] ?? null),
                'city' => ($validated['city'] ?? null) ?: ($validated['city_manual'] ?? null),
                'phone' => $validated['phone'] ?? null,
                'role' => $userRoleEnum,
                'role_id' => $role?->id,
            ]);

            // Attach role relationship
            if ($role) {
                $user->roles()->attach($role->id);
            }

            // Create personal team
            $team = $user->ownedTeams()->create([
                'name' => "{$user->name}'s Team",
                'is_personal' => true,
            ]);

            $user->currentTeam()->associate($team)->save();

            // Give basic tier subscription cycle
            $user->createFreeTrialSubscription();

            return $user;
        });

        event(new Registered($user));

        // Store verification email in session
        $request->session()->put('verification_email', $user->email);
        if ($request->boolean('newschool')) {
            $request->session()->put('redirect_after_verification', 'onboarding');
            //dd($request->session()->all());
        }

        // Build success message
        $roleMessage = $request->boolean('author') ? 'author' : 'subscriber';
        $successMessage = "Registration successful as {$roleMessage}! Please check your email to verify your account before signing in.";

        // ✅ Check if the "Onboard a new school" checkbox was selected


        // Default redirect to verification notice
        return redirect()->route('verification.notice')
            ->with('success', $successMessage);
    }
}
