<?php

namespace App\Http\Controllers\Auth;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterGuestRequest;
use App\Models\Role;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\DB;

class RegisterGuestController extends Controller
{
    public function create()
    {
        return view('auth.register-guest');
    }

    public function store(RegisterGuestRequest $request)
    {
        $user = DB::transaction(static function () use ($request) {
            $validated = $request->validated();

            // Find the guest role
            $role = Role::where('name', 'guest')
                ->orWhere('slug', 'guest')
                ->first();

            // Prepare city and region - use manual input if 'other' was selected
            $city = $validated['city'] === 'other' ? $validated['city_manual'] : $validated['city'];
            $region = $validated['region'] === 'other' ? $validated['region_manual'] : $validated['region'];

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
                'country' => $validated['country'],
                'region' => $region,
                'city' => $city,
                'date_of_birth' => $validated['date_of_birth'] ?? null,
                'gender' => $validated['gender'] ?? null,
                'phone' => $validated['phone'] ?? null,
                'country_code' => $validated['country_code'] ?? null,
                'role' => UserRole::GUEST,
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

        return redirect()->route('verification.notice')
            ->with('success', 'Registration successful! Please check your email to verify your account before signing in.');
    }
}
