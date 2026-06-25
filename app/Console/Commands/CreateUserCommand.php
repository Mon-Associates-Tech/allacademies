<?php

namespace App\Console\Commands;

use App\Enums\UserRole;
use App\Models\School;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class CreateUserCommand extends Command
{
    protected $signature = 'users:create
                            {email : The email address for the new user}
                            {account_type : The account type/role (e.g. student, teacher, admin)}
                            {--first_name= : Optional first name}
                            {--last_name= : Optional last name}
                            {--school_id= : Optional school id to associate the user with}
                            {--password= : Optional custom password (otherwise uses defaultpass)}';

    protected $description = 'Create a new user with given email and account type. Optional --first_name and --last_name';

    public function handle(): int
    {
        $email = (string) $this->argument('email');
        $accountType = (string) $this->argument('account_type');
        $firstName = $this->option('first_name');
        $lastName = $this->option('last_name');
        $schoolId = $this->option('school_id');
        $customPassword = $this->option('password');

        // Interactive prompts for missing optional values
        if (empty($firstName)) {
            $firstName = $this->ask('First name (optional)', null);
            $firstName = $firstName ?: null;
        }

        if (empty($lastName)) {
            $lastName = $this->ask('Last name (optional)', null);
            $lastName = $lastName ?: null;
        }

        if (empty($schoolId)) {
            $schoolId = $this->ask('School ID to associate (leave blank for none)', null);
            $schoolId = $schoolId ?: null;
        }

        if (empty($customPassword)) {
            $secret = $this->secret('Password (leave blank to use defaultpass)');
            $customPassword = $secret ?: null;
        }

        $validator = Validator::make([
            'email' => $email,
            'account_type' => $accountType,
        ], [
            'email' => ['required', 'email'],
            'account_type' => ['required', 'string'],
        ]);

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $this->error($error);
            }

            return self::FAILURE;
        }

        // Ensure account type is a valid UserRole value
        $allowed = array_map(fn ($r) => $r->value, UserRole::getAll());
        if (! in_array($accountType, $allowed, true)) {
            $this->error('Invalid account type. Allowed types: '.implode(', ', $allowed));

            return self::FAILURE;
        }

        if (User::where('email', $email)->exists()) {
            $this->error('A user with that email already exists.');

            return self::FAILURE;
        }

        $name = trim(User::generateNameFromParts($firstName, $lastName));
        if ($name === '') {
            $name = Str::before($email, '@');
        }

        $data = [
            'email' => $email,
            'first_name' => $firstName ?: null,
            'last_name' => $lastName ?: null,
            'name' => $name,
            // store role as a string value that matches the UserRole enum
            'role' => $accountType,
            // password; User model casts 'password' => 'hashed' so store plain here
            'password' => $customPassword ?: 'defaultpass',
            'status' => 'active',
            'is_active' => true,
        ];

        if ($schoolId) {
            $school = School::find($schoolId);
            if (! $school) {
                $this->error('School with id '.$schoolId.' not found.');

                return self::FAILURE;
            }

            $data['school_id'] = $school->id;
        }

        try {
            $user = User::create($data);

            $this->info("User created: ID={$user->id}, email={$user->email}, role={$user->role}");

            return self::SUCCESS;
        } catch (\Exception $e) {
            $this->error('Failed to create user: '.$e->getMessage());

            return self::FAILURE;
        }
    }
}
