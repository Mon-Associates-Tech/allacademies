<?php

namespace Database\Factories;

use App\Models\GroupBookSubscription;
use App\Models\StudentGroup;
use App\Models\Book;
use Illuminate\Database\Eloquent\Factories\Factory;

class GroupBookSubscriptionFactory extends Factory
{
    protected $model = GroupBookSubscription::class;

    public function definition(): array
    {
        $startDate = $this->faker->dateTimeBetween('-6 months', 'now');
        $endDate = $this->faker->dateTimeBetween($startDate, '+1 year');
        
        return [
            'student_group_id' => StudentGroup::factory(),
            'book_id' => Book::factory()->state(function (array $attributes) {
                return ['has_softcopy' => true];
            }),
            'start_date' => $startDate,
            'end_date' => $endDate,
            'status' => $this->faker->randomElement(['active', 'cancelled', 'expired']),
        ];
    }
}