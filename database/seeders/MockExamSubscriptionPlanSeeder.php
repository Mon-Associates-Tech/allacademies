<?php

namespace Database\Seeders;

use App\MockExam\Models\MockExamSubscriptionPlan;
use Illuminate\Database\Seeder;

class MockExamSubscriptionPlanSeeder extends Seeder
{
    public function run(): void
    {
        $plans = [
            [
                'name'          => 'Print',
                'type'          => 'print',
                'max_subjects'  => 10,
                'max_exams'     => 1,
                'max_participants' => null,
                'duration_type' => 'one_time',
                'duration_value' => null,
                'base_price'    => 0,
                'description'   => 'Print exam delivery. Price is calculated from the pricing tier matching the subscriber\'s user group and subject count. Exam cycles per subject are chosen at purchase.',
            ],
            [
                'name'          => 'Online',
                'type'          => 'online',
                'max_subjects'  => 10,
                'max_exams'     => 1,
                'max_participants' => null,
                'duration_type' => 'one_time',
                'duration_value' => null,
                'base_price'    => 0,
                'description'   => 'Online exam delivery. Price = per-student tier rate × participant count, based on user group and subject count.',
            ],
        ];

        $activeNames = collect($plans)->pluck('name');

        MockExamSubscriptionPlan::whereNotIn('name', $activeNames)->update(['is_active' => false]);

        foreach ($plans as $plan) {
            MockExamSubscriptionPlan::updateOrCreate(
                ['name' => $plan['name']],
                array_merge($plan, ['is_active' => true])
            );
        }
    }
}
