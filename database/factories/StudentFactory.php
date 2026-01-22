<?php

namespace Database\Factories;

use App\Models\Student;
use App\Models\StudentGroup;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class StudentFactory extends Factory
{
    protected $model = Student::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'student_group_id' => StudentGroup::factory(),
        ];
    }
}
