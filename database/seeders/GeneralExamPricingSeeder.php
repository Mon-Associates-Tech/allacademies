<?php

namespace Database\Seeders;

use App\Models\GeneralExamPricingTier;
use App\Models\GeneralExamSubscriptionPlan;
use Illuminate\Database\Seeder;

class GeneralExamPricingSeeder extends Seeder
{
    public function run(): void
    {
        // ==================== PRICING TIERS ====================
        // Online: price per student decreases as subject count increases (bulk discount)
        // Print: flat rate per subject (also discounted at scale)
        $tiers = [
            ['subject_count' => 1,  'price_per_student' => 10.00, 'print_flat_rate' => 80.00],
            ['subject_count' => 2,  'price_per_student' => 8.50,  'print_flat_rate' => 70.00],
            ['subject_count' => 3,  'price_per_student' => 7.50,  'print_flat_rate' => 65.00],
            ['subject_count' => 4,  'price_per_student' => 7.00,  'print_flat_rate' => 60.00],
            ['subject_count' => 5,  'price_per_student' => 6.50,  'print_flat_rate' => 55.00],
            ['subject_count' => 6,  'price_per_student' => 6.00,  'print_flat_rate' => 50.00],
            ['subject_count' => 7,  'price_per_student' => 5.75,  'print_flat_rate' => 48.00],
            ['subject_count' => 8,  'price_per_student' => 5.50,  'print_flat_rate' => 45.00],
            ['subject_count' => 9,  'price_per_student' => 5.25,  'print_flat_rate' => 43.00],
            ['subject_count' => 10, 'price_per_student' => 5.00,  'print_flat_rate' => 40.00],
        ];

        foreach ($tiers as $tier) {
            GeneralExamPricingTier::updateOrCreate(
                ['subject_count' => $tier['subject_count']],
                array_merge($tier, ['is_active' => true])
            );
        }

        // ==================== SUBSCRIPTION PLANS ====================
        // Keep only two plan types. "max_exams" is now treated as cycles per subject on subscription instances.
        $plans = [
            [
                'name' => 'Online',
                'type' => 'online',
                'max_subjects' => 10,
                'max_exams' => 1,
                'max_participants' => null,
                'duration_type' => 'one_time',
                'duration_value' => null,
                'base_price' => 0,
                'description' => 'Online exam delivery. Price = per-student tier by selected subject count. Exam cycles per subject are chosen at subscription purchase.',
            ],
            [
                'name' => 'Print',
                'type' => 'print',
                'max_subjects' => 10,
                'max_exams' => 1,
                'max_participants' => null,
                'duration_type' => 'one_time',
                'duration_value' => null,
                'base_price' => 0,
                'description' => 'Print exam delivery. Price = print flat-rate tier by selected subject count. Exam cycles per subject are chosen at subscription purchase.',
            ],
        ];

        $activeNames = collect($plans)->pluck('name');

        GeneralExamSubscriptionPlan::query()
            ->whereNotIn('name', $activeNames)
            ->update(['is_active' => false]);

        foreach ($plans as $plan) {
            GeneralExamSubscriptionPlan::updateOrCreate(
                ['name' => $plan['name']],
                array_merge($plan, ['is_active' => true])
            );
        }
    }
}
