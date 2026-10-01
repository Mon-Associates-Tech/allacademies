<?php

namespace Database\Seeders;

use App\MockExam\Models\MockExamPricingTier;
use Illuminate\Database\Seeder;

class MockExamPricingTierSeeder extends Seeder
{
    /**
     * Pricing tiers per user group.
     *
     * Print flat rate = price per subject per exam cycle.
     * Price per student = online rate per participant.
     *
     * Rates decrease as subject count increases (bulk discount).
     * Schools pay less than individuals; governments/orgs get the best rates.
     */
    public function run(): void
    {
        $groups = [
            'individual' => [
                // subject_count => [price_per_student, print_flat_rate]
                1  => [12.00, 100.00],
                2  => [10.50,  90.00],
                3  => [9.50,   82.00],
                4  => [9.00,   76.00],
                5  => [8.50,   70.00],
                6  => [8.00,   65.00],
                7  => [7.75,   62.00],
                8  => [7.50,   58.00],
                9  => [7.25,   55.00],
                10 => [7.00,   52.00],
            ],
            'school' => [
                1  => [9.00,  75.00],
                2  => [8.00,  68.00],
                3  => [7.25,  62.00],
                4  => [6.75,  57.00],
                5  => [6.25,  53.00],
                6  => [6.00,  49.00],
                7  => [5.75,  46.00],
                8  => [5.50,  43.00],
                9  => [5.25,  41.00],
                10 => [5.00,  38.00],
            ],
            'local_government' => [
                1  => [7.00,  60.00],
                2  => [6.25,  54.00],
                3  => [5.75,  49.00],
                4  => [5.25,  45.00],
                5  => [5.00,  42.00],
                6  => [4.75,  39.00],
                7  => [4.50,  37.00],
                8  => [4.25,  35.00],
                9  => [4.00,  33.00],
                10 => [3.75,  31.00],
            ],
            'organization' => [
                1  => [8.00,  68.00],
                2  => [7.25,  61.00],
                3  => [6.50,  55.00],
                4  => [6.00,  51.00],
                5  => [5.75,  47.00],
                6  => [5.50,  44.00],
                7  => [5.25,  41.00],
                8  => [5.00,  39.00],
                9  => [4.75,  37.00],
                10 => [4.50,  35.00],
            ],
        ];

        foreach ($groups as $group => $tiers) {
            foreach ($tiers as $subjectCount => [$pricePerStudent, $printFlatRate]) {
                $label = match ($group) {
                    'individual'      => 'Individual',
                    'school'          => 'School',
                    'local_government'=> 'Local Government',
                    'organization'    => 'Organization',
                };

                MockExamPricingTier::updateOrCreate(
                    [
                        'user_group'    => $group,
                        'subject_count' => $subjectCount,
                    ],
                    [
                        'name'              => "{$label} – {$subjectCount} Subject" . ($subjectCount > 1 ? 's' : ''),
                        'price_per_student' => $pricePerStudent,
                        'print_flat_rate'   => $printFlatRate,
                        'is_active'         => true,
                    ]
                );
            }
        }
    }
}
