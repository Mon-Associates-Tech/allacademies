<?php

namespace Database\Factories;

use App\Models\AcademicSubject;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AcademicTopic>
 */
class AcademicTopicFactory extends Factory
{
    protected $model = \App\Models\AcademicTopic::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->word(),
            'academic_subject_id' => AcademicSubject::factory(),
        ];
    }
}
