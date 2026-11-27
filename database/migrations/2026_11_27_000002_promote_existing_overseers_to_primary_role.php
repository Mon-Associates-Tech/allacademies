<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('roles') || ! Schema::hasTable('role_user') || ! Schema::hasColumn('users', 'role_id')) {
            return;
        }

        $overseerRoleId = DB::table('roles')->where('name', 'overseer')->value('id');

        if (! $overseerRoleId) {
            return;
        }

        DB::table('users')
            ->whereNotIn('role', ['owner', 'admin', 'superadmin'])
            ->whereExists(function ($query) use ($overseerRoleId) {
                $query->selectRaw('1')
                    ->from('role_user')
                    ->whereColumn('role_user.user_id', 'users.id')
                    ->where('role_user.role_id', $overseerRoleId);
            })
            ->orderBy('id')
            ->chunkById(100, function ($users) use ($overseerRoleId) {
                foreach ($users as $user) {
                    if ($user->role && $user->role !== 'overseer') {
                        $previousRoleId = DB::table('roles')->where('name', $user->role)->value('id');

                        if (! $previousRoleId) {
                            $slug = $user->role;
                            $suffix = 1;

                            while (DB::table('roles')->where('slug', $slug)->exists()) {
                                $slug = $user->role.'-'.$suffix++;
                            }

                            $previousRoleId = DB::table('roles')->insertGetId([
                                'name' => $user->role,
                                'slug' => $slug,
                                'created_at' => now(),
                                'updated_at' => now(),
                            ]);
                        }

                        if (! DB::table('role_user')
                            ->where('user_id', $user->id)
                            ->where('role_id', $previousRoleId)
                            ->exists()) {
                            DB::table('role_user')->insert([
                                'user_id' => $user->id,
                                'role_id' => $previousRoleId,
                                'created_at' => now(),
                                'updated_at' => now(),
                            ]);
                        }
                    }

                    DB::table('users')
                        ->where('id', $user->id)
                        ->update([
                            'role' => 'overseer',
                            'role_id' => $overseerRoleId,
                        ]);
                }
            });
    }

    public function down(): void
    {
        // Existing overseer assignments cannot be safely reverted to a single primary role.
    }
};
