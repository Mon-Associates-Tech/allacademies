<?php

namespace Database\Factories;

use App\Models\AcademicSubject;
use Illuminate\Database\Eloquent\Factories\Factory;

class SubjectFactory extends Factory
{
    protected $model = AcademicSubject::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->randomElement([
                'Mathematics', 'Physics', 'Chemistry', 'Biology', 'Computer Science',
                'English', 'Literature', 'History', 'Geography', 'Economics',
                'Art', 'Music', 'Physical Education', 'Psychology', 'Sociology',
                'Philosophy', 'Political Science', 'French', 'Spanish', 'German'
            ]),
            'description' => $this->faker->sentence(),
        ];
    }
}
