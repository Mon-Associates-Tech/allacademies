<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\BookShop\Models\Staff;
use App\BookShop\Enums\StaffRole;

class BookShopSuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Staff::updateOrCreate(
            ['email' => 'superadmin@bookshop.com'], // Search criteria
            [
                'name'                 => 'Super Admin',
                'password'             => Hash::make('password'),
                'role'                 => StaffRole::SUPERADMIN,
                'is_active'            => true,
                'must_change_password' => false,
            ]
        );

        $this->command->info('✅ Super Admin user created or updated successfully!');
        $this->command->warn('⚠️  Please remember to change the default password after first login.');
    }
}
