<?php

namespace Database\Factories;

use App\Models\Assessment;
use App\Models\Book;
use App\Models\Student;
use Illuminate\Database\Eloquent\Factories\Factory;

class AssessmentFactory extends Factory
{
    protected $model = Assessment::class;

    public function definition(): array
    {
        return [
            'student_id' => Student::factory(),
            'book_id' => Book::factory(),
            'score' => $this->faker->numberBetween(0, 100),
            'comments' => $this->faker->optional(0.8)->paragraph(),
        ];
    }
}
