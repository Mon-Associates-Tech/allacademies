<?php

namespace Database\Factories;

use App\Models\AcademicTopic;
use App\Models\AcademicSubtopic;
use App\Support\Mark;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\EssayQuestion>
 */
class EssayQuestionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $questionText = $this->faker->paragraph(3);
        $answerText = $this->faker->paragraph(5);

        return [
            'question' => [
                'up' => $questionText,
                'down' => '<p>' . $questionText . '</p>',
            ],
            'answer' => [
                'up' => $answerText,
                'down' => '<p>' . $answerText . '</p>',
            ],
            'score' => $this->faker->numberBetween(5, 20),
            'difficulty_level' => $this->faker->randomElement(['easy', 'medium', 'hard']),
            'academic_topic_id' => AcademicTopic::factory(),
            'academic_subtopic_id' => null,
        ];
    }
}
