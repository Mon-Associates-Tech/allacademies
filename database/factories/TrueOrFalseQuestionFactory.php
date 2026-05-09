<?php

namespace Database\Factories;

use App\Models\AcademicTopic;
use App\Models\AcademicSubtopic;
use App\Support\Mark;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TrueOrFalseQuestion>
 */
class TrueOrFalseQuestionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $questionText = $this->faker->sentence() . ' ' . $this->faker->sentence();

        return [
            'question' => [
                'up' => $questionText,
                'down' => '<p>' . $questionText . '</p>',
            ],
            'answer' => $this->faker->boolean(),
            'score' => $this->faker->numberBetween(1, 3),
            'difficulty_level' => $this->faker->randomElement(['easy', 'medium', 'hard']),
            'academic_topic_id' => AcademicTopic::factory(),
            'academic_subtopic_id' => null,
        ];
    }
}
