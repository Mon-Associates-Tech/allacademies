<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class MockExamSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            MockExamSubscriptionPlanSeeder::class,
            MockExamPricingTierSeeder::class,
        ]);
    }
}
