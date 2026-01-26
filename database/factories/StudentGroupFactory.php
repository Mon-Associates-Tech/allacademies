<?php

namespace Database\Factories;

use App\Models\StudentGroup;
use App\Models\Teacher;
use Illuminate\Database\Eloquent\Factories\Factory;

class StudentGroupFactory extends Factory
{
    protected $model = StudentGroup::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->randomElement([
                'Grade 1', 'Grade 2', 'Grade 3', 'Grade 4', 'Grade 5',
                'Grade 6', 'Grade 7', 'Grade 8', 'Grade 9', 'Grade 10',
                'Grade 11', 'Grade 12', 'Class A', 'Class B', 'Class C',
                'Class D', 'Class E', 'Advanced Group', 'Beginners Group', 'Intermediate Group',
            ]),
            'teacher_id' => Teacher::factory(),
            'description' => $this->faker->sentence(),
        ];
    }
}
