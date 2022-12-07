<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        collect(UserRole::cases())->each(function (UserRole $userRole) {
            /** @var \App\Models\User $user */
            $user = User::query()->create([
                'name' => 'Real ' . ucfirst($userRole->value),
                'email' => $userRole->value . '@exams.com',
                'password' => Hash::make('secret.pass'),
                'role' => $userRole,
            ]);

            $team = $user->ownedTeams()->create(['name' => $user->name . "'s Team", 'is_personal' => true]);

            $user->currentTeam()->associate($team)->save();
        });
    }
}
