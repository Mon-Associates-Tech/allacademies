<?php

namespace Database\Factories;

use App\Models\AcademicSubject;
use App\Models\AcademicTopic;
use Illuminate\Database\Eloquent\Factories\Factory;

class TopicFactory extends Factory
{
    protected $model = AcademicTopic::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->sentence(3),
            'subject_id' => AcademicSubject::factory(),
            'description' => $this->faker->paragraph(),
        ];
    }
}
