<?php

namespace App\Http\Controllers\Auth;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterSchoolRequest;
use App\Models\Role;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\DB;

class RegisterSchoolController extends Controller
{
    public function create()
    {
        return view('auth.register-school');
    }

    public function store(RegisterSchoolRequest $request)
    {
        $user = DB::transaction(static function () use ($request) {
            $validated = $request->validated();

            // Find the admin role for school administrators
            $role = Role::where('name', 'admin')
                ->orWhere('slug', 'admin')
                ->first();

            // Fallback to guest if admin role not found
            if (! $role) {
                $role = Role::where('name', 'guest')
                    ->orWhere('slug', 'guest')
                    ->first();
            }

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
                'role' => UserRole::ADMIN,
                'role_id' => $role?->id,
            ]);

            // Attach role relationship
            if ($role) {
                $user->roles()->attach($role->id);
            }

            // Create school team instead of personal team
            $team = $user->ownedTeams()->create([
                'name' => $validated['school_name'],
                'is_personal' => false,
            ]);

            $user->currentTeam()->associate($team)->save();

            // Give basic tier subscription cycle
            $user->createFreeTrialSubscription();

            return $user;
        });

        event(new Registered($user));

        // Store verification email and onboarding flag in session
        $request->session()->put('verification_email', $user->email);
        $request->session()->put('redirect_after_verification', 'onboarding');

        return redirect()->route('verification.notice')
            ->with('success', 'Welcome to school onboarding! Please verify your email to begin setting up your school.');
    }
}
