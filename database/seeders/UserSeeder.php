<?php

namespace Database\Seeders;

use App\Models\School;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Get the first school or create a default one if none exists
        $school = School::first();

        if (!$school) {
            $this->command->info('No school found. Creating default school...');

            $school = School::create([
                'name' => 'Default Academy',
                'email' => 'contact@defaultacademy.com',
                'phone' => '555-0123',
                'website' => 'https://defaultacademy.com',
                'type' => 'secondary',
                'ownership' => 'private',
                'address' => '123 Education Lane',
                'city' => 'Accra', // Defaulting based on context (Paystack/Currency)
                'state' => 'Accra',
                'country' => 'Ghana',
                'status' => 'active',
                'postal_code' => '100001',
                'description' => 'A default school generated for seeding purposes.',
                'settings' => [
                    'currency' => 'GHS',
                    'timezone' => 'Africa/Accra',
                ]
            ]);
        }

        $roles = [
            'owner',
            'admin',
            'student',
            'parent',
            'teacher',
            'moderator',
            'author',
            'subscriber'
        ];

        // 2. Create 10 users for each role
        foreach ($roles as $role) {
            $this->command->info("Seeding 10 users for role: {$role}");

            // We use the factory to ensure all other user fields (profile_image, etc.) are populated
            User::factory()
                ->count(10)
                ->create([
                    'school_id' => $school->id,
                    'role' => $role,
                    'status' => 'active',
                    'is_active' => true,
                    // Optional: Ensure password is known for testing
                    'password' => Hash::make('password'),
                ]);
        }

        $this->command->info('User seeding completed successfully.');
    }
}
