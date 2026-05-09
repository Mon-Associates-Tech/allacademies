<?php

namespace Database\Factories;

use App\Models\AcademicTopic;
use App\Models\AcademicSubtopic;
use App\Support\Mark;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\MultipleChoiceQuestion>
 */
class MultipleChoiceQuestionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $questionText = $this->faker->sentence() . ' ' . $this->faker->sentence();
        $options = [
            $this->faker->sentence(),
            $this->faker->sentence(),
            $this->faker->sentence(),
            $this->faker->sentence(),
            $this->faker->sentence(),
        ];

        return [
            'question' => [
                'up' => $questionText,
                'down' => '<p>' . $questionText . '</p>',
            ],
            'option_a' => [
                'up' => $options[0],
                'down' => '<p>' . $options[0] . '</p>',
            ],
            'option_b' => [
                'up' => $options[1],
                'down' => '<p>' . $options[1] . '</p>',
            ],
            'option_c' => [
                'up' => $options[2],
                'down' => '<p>' . $options[2] . '</p>',
            ],
            'option_d' => [
                'up' => $options[3],
                'down' => '<p>' . $options[3] . '</p>',
            ],
            'option_e' => [
                'up' => $options[4],
                'down' => '<p>' . $options[4] . '</p>',
            ],
            'answer' => $this->faker->randomElement(['a', 'b', 'c', 'd', 'e']),
            'score' => $this->faker->numberBetween(1, 5),
            'difficulty_level' => $this->faker->randomElement(['easy', 'medium', 'hard']),
            'academic_topic_id' => AcademicTopic::factory(),
            'academic_subtopic_id' => null,
        ];
    }
}
