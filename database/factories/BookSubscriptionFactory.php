<?php

namespace Database\Factories;

use App\Models\BookSubscription;
use App\Models\Student;
use App\Models\Book;
use Illuminate\Database\Eloquent\Factories\Factory;

class BookSubscriptionFactory extends Factory
{
    protected $model = BookSubscription::class;

    public function definition(): array
    {
        $startDate = $this->faker->dateTimeBetween('-6 months', 'now');
        $endDate = $this->faker->dateTimeBetween($startDate, '+1 year');
        
        return [
            'student_id' => Student::factory(),
            'book_id' => Book::factory()->state(function (array $attributes) {
                return ['has_softcopy' => true];
            }),
            'start_date' => $startDate,
            'end_date' => $endDate,
            'status' => $this->faker->randomElement(['active', 'cancelled', 'expired']),
        ];
    }

    public function active(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'active',
            ];
        });
    }

    public function cancelled(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'cancelled',
            ];
        });
    }
}