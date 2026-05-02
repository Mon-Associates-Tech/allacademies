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
        $plans = [
            [
                'name' => 'Online — One-Time',
                'type' => 'online',
                'max_subjects' => 10,
                'max_exams' => 1,
                'max_participants' => null,
                'duration_type' => 'one_time',
                'duration_value' => null,
                'base_price' => 0,
                'description' => 'Single online exam session. Price calculated per student × subject tier.',
            ],
            [
                'name' => 'Online — 5 Exams',
                'type' => 'online',
                'max_subjects' => 10,
                'max_exams' => 5,
                'max_participants' => null,
                'duration_type' => 'fixed_count',
                'duration_value' => 5,
                'base_price' => 0,
                'description' => 'Bundle of 5 online exams. Shared participant pool across all exams.',
            ],
            [
                'name' => 'Online — Monthly',
                'type' => 'online',
                'max_subjects' => 10,
                'max_exams' => null,
                'max_participants' => null,
                'duration_type' => 'period',
                'duration_value' => 30,
                'base_price' => 0,
                'description' => 'Unlimited online exams for 30 days. Participant slots shared across all exams.',
            ],
            [
                'name' => 'Print — One-Time',
                'type' => 'print',
                'max_subjects' => 10,
                'max_exams' => 1,
                'max_participants' => null,
                'duration_type' => 'one_time',
                'duration_value' => null,
                'base_price' => 0,
                'description' => 'Single print exam. Answer sheet generated alongside question paper. Flat rate per subject.',
            ],
            [
                'name' => 'Print — 5 Exams',
                'type' => 'print',
                'max_subjects' => 10,
                'max_exams' => 5,
                'max_participants' => null,
                'duration_type' => 'fixed_count',
                'duration_value' => 5,
                'base_price' => 0,
                'description' => 'Bundle of 5 print exams.',
            ],
        ];

        foreach ($plans as $plan) {
            GeneralExamSubscriptionPlan::updateOrCreate(
                ['name' => $plan['name']],
                array_merge($plan, ['is_active' => true])
            );
        }
    }
}
