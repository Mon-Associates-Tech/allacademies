<?php

namespace App\Console\Commands;

use App\BookShop\Enums\StaffRole;
use App\BookShop\Models\Staff;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class CreateBookshopSuperAdminCommand extends Command
{
    protected $signature = 'bookshop:superadmin:create
                            {email : The email address for the new super admin}
                            {--name= : The full name of the super admin}
                            {--password= : Custom password (otherwise uses "password")}
                            {--is_active= : Whether the account is active (true/false)}
                            {--must_change_password= : Whether the user must change password on first login (true/false)}';

    protected $description = 'Create a new Super Admin for the Book Shop module with given email and options.';

    public function handle(): int
    {
        $email = (string) $this->argument('email');
        $name = $this->option('name');
        $password = $this->option('password');
        $isActiveOpt = $this->option('is_active');
        $mustChangePasswordOpt = $this->option('must_change_password');

        // Validate email format
        $validator = Validator::make([
            'email' => $email,
        ], [
            'email' => ['required', 'email'],
        ]);

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $this->error($error);
            }

            return self::FAILURE;
        }

        // Ensure email is unique
        if (Staff::where('email', $email)->exists()) {
            $this->error('A staff member with that email already exists.');

            return self::FAILURE;
        }

        // Interactive prompts for missing optional values
        if (empty($name)) {
            $name = $this->ask('Name (leave blank for "Super Admin")', 'Super Admin');
            $name = $name ?: 'Super Admin';
        }

        if (empty($password)) {
            $secret = $this->secret('Password (leave blank to use "password")');
            $password = $secret ?: 'password';
        }

        if (is_null($isActiveOpt)) {
            $isActive = $this->confirm('Is the account active?', true);
        } else {
            $isActive = filter_var($isActiveOpt, FILTER_VALIDATE_BOOLEAN);
        }

        if (is_null($mustChangePasswordOpt)) {
            $mustChangePassword = $this->confirm('Must change password on first login?', false);
        } else {
            $mustChangePassword = filter_var($mustChangePasswordOpt, FILTER_VALIDATE_BOOLEAN);
        }

        // Prepare data mapping to the fields defined in BookShopSuperAdminSeeder
        $data = [
            'email' => $email,
            'name' => $name,
            // We use Hash::make() directly here since the Seeder explicitly uses it.
            'password' => Hash::make($password),
            'role' => StaffRole::SUPERADMIN,
            'is_active' => $isActive,
            'must_change_password' => $mustChangePassword,
        ];

        try {
            $staff = Staff::create($data);

            // Safely get the string representation of the enum regardless of whether it's a Backed or Unit enum
            $roleName = $staff->role instanceof \BackedEnum ? $staff->role->value : $staff->role->name;
            
            $this->info("✅ Super Admin created: ID={$staff->id}, email={$staff->email}, role={$roleName}");

            if ($password === 'password' && ! $mustChangePassword) {
                $this->warn('⚠️  Please remember to change the default password after first login.');
            }

            return self::SUCCESS;
        } catch (\Exception $e) {
            $this->error('Failed to create super admin: '.$e->getMessage());

            return self::FAILURE;
        }
    }
}