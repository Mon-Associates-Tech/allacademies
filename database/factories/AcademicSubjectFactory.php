<?php

namespace Database\Factories;

use App\Models\AcademicLevel;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AcademicSubject>
 */
class AcademicSubjectFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'name' => $this->faker->unique()->word(),
            'code' => $this->faker->unique()->word(),
            'academic_level_id' => AcademicLevel::factory(1)->create()->id,
        ];
    }
}
