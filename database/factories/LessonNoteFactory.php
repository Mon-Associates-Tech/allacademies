<?php

namespace Database\Factories;

use App\Models\AcademicSubject;
use App\Models\AcademicTopic;
use App\Models\Lesson;
use App\Models\LessonNote;
use App\Models\Teacher;
use Illuminate\Database\Eloquent\Factories\Factory;

class LessonNoteFactory extends Factory
{
    protected $model = LessonNote::class;

    public function definition(): array
    {
        return [
            'teacher_id' => Teacher::factory(),
            'lesson_id' => Lesson::factory(),
            'subject_id' => AcademicSubject::factory(),
            'topic_id' => AcademicTopic::factory(),
            'title' => $this->faker->sentence(),
            'content' => $this->faker->paragraphs(3, true),
            'file_path' => null,
        ];
    }
}
