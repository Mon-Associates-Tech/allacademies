<?php

namespace Database\Factories;

use App\Models\AcademicSubject;
use App\Models\Lesson;
use App\Models\StudentGroup;
use App\Models\Teacher;
use Illuminate\Database\Eloquent\Factories\Factory;

class LessonFactory extends Factory
{
    protected $model = Lesson::class;

    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(),
            'description' => $this->faker->paragraph(),
            'teacher_id' => Teacher::factory(),
            'subject_id' => AcademicSubject::factory(),
            'student_group_id' => StudentGroup::factory(),
            'date' => $this->faker->dateTimeBetween('-1 month', '+1 month'),
        ];
    }
}
