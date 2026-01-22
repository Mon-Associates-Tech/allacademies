<?php

namespace App\Console\Commands;

use App\Enums\UserRole;
use App\Models\Role;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class MigrateUserRoles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:migrate-roles {--dry-run : Show what would be updated without making changes} {--to-many-to-many : Migrate to many-to-many instead of single role}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate users from string role field to role relationships';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $dryRun = $this->option('dry-run');
        $toManyToMany = $this->option('to-many-to-many');

        $this->info('Starting user role migration...');

        if ($dryRun) {
            $this->warn('Running in DRY RUN mode - no changes will be made');
        }

        if ($toManyToMany) {
            $this->info('Migrating to many-to-many relationships via role_user table');
        } else {
            $this->info('Migrating to single role relationship via role_id field');
        }

        // Get all role mappings from UserRole enum cases
        $enumCases = UserRole::cases();
        $roleMapping = [];

        foreach ($enumCases as $case) {
            $roleMapping[$case->value] = $case->value;
        }

        $this->info('Found '.count($roleMapping).' roles in UserRole enum: '.implode(', ', array_keys($roleMapping)));

        // Create or get existing roles
        $roles = [];
        foreach ($roleMapping as $name => $value) {
            if (! $dryRun) {
                $role = Role::firstOrCreate(
                    ['name' => $name],
                    [
                        'name' => $name,
                        'description' => ucfirst($name).' role',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]
                );
                $roles[$name] = $role;
                $this->line("✓ Role '{$name}' ready (ID: {$role->id})");
            } else {
                // For dry run, just check if role exists
                $role = Role::where('name', $name)->first();
                if ($role) {
                    $roles[$name] = $role;
                    $this->line("✓ Role '{$name}' exists (ID: {$role->id})");
                } else {
                    $roles[$name] = 'would_create';
                    $this->line("+ Role '{$name}' would be created");
                }
            }
        }

        // Get users that need migration
        $whereCondition = $toManyToMany ?
            // For many-to-many, find users with string roles that don't have any roles in pivot table
            function ($query) {
                $query->whereNotNull('role')
                    ->where('role', '!=', '')
                    ->whereDoesntHave('roles');
            } :
            // For single role, find users with string roles that don't have role_id set
            function ($query) {
                $query->whereNotNull('role')
                    ->where('role', '!=', '')
                    ->whereNull('role_id');
            };

        $users = User::where($whereCondition)->get();

        $this->info("Found {$users->count()} users to migrate");

        $migrated = 0;
        $skipped = 0;
        $errors = 0;

        foreach ($users as $user) {
            $oldRole = $user->role;

            if (! isset($roles[$oldRole])) {
                $this->error("Unknown role '{$oldRole}' for user {$user->id} ({$user->email})");
                $this->line('Available roles: '.implode(', ', array_keys($roles)));
                $errors++;

                continue;
            }

            $role = $roles[$oldRole];

            if ($dryRun) {
                if ($role === 'would_create') {
                    $this->line("User {$user->id} ({$user->email}): '{$oldRole}' -> would create role and assign");
                } else {
                    $target = $toManyToMany ? 'many-to-many relationship' : "role_id {$role->id}";
                    $this->line("User {$user->id} ({$user->email}): '{$oldRole}' -> {$target}");
                }
                $migrated++;
            } else {
                try {
                    DB::transaction(function () use ($user, $role, $toManyToMany) {
                        if ($toManyToMany) {
                            // Add to many-to-many relationship
                            if (! $user->roles()->where('role_id', $role->id)->exists()) {
                                $user->roles()->attach($role->id);
                            }
                        } else {
                            // Set single role_id
                            $user->update(['role_id' => $role->id]);
                        }
                    });

                    $target = $toManyToMany ? 'many-to-many' : "role_id {$role->id}";
                    $this->line("✓ Migrated user {$user->id} ({$user->email}): '{$oldRole}' -> {$target}");
                    $migrated++;
                } catch (\Exception $e) {
                    $this->error("Failed to migrate user {$user->id}: ".$e->getMessage());
                    $errors++;
                }
            }
        }

        // Summary
        $this->newLine();
        $this->info('Migration Summary:');
        $this->line("Migrated: {$migrated}");
        $this->line("Skipped: {$skipped}");
        $this->line("Errors: {$errors}");

        if ($dryRun) {
            $this->newLine();
            $this->warn('This was a DRY RUN. To actually perform the migration, run:');
            $command = 'php artisan users:migrate-roles';
            if ($toManyToMany) {
                $command .= ' --to-many-to-many';
            }
            $this->line($command);
        } else {
            $this->newLine();
            $this->info('Migration completed!');

            if ($migrated > 0) {
                $this->warn('Consider updating your code to use the role relationships instead of the role field.');
                $this->line('You may want to remove the role field from the fillable array after testing.');
            }
        }

        return $errors > 0 ? 1 : 0;
    }
}
