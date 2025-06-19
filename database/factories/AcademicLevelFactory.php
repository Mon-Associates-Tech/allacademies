<?php

namespace Database\Factories;

use App\Models\AcademicLevel;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AcademicLevel>
 */
class AcademicLevelFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->word(),
            'label' => $this->faker->unique()->word(),
            'academic_group_id' => AcademicGroupFactory::new()->create()->id,
        ];
    }
}
