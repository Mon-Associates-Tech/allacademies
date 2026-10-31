<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class CenterSyncUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * Creates the "Central Sync" system user used for exam package
     * creation and sync operations on the central server.
     */
    public function run(): void
    {
        $email = 'sync@allacademies.system';

        $user = User::firstOrCreate(
            ['email' => $email],
            [
                'name' => 'Central Sync',
                'username' => 'central_sync',
                'password' => bcrypt(bin2hex(random_bytes(32))),
                'role' => 'admin',
                'email_verified_at' => now(),
                'is_active' => true,
            ]
        );

        $this->command->info("Central Sync user created/updated: {$user->email}");
    }
}
