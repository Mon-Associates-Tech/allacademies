<?php

namespace Tests\Unit;

use App\Models\AcademicSubject;
use App\Models\AcademicTopic;
use App\Models\Assessment;
use App\Models\MultipleChoiceQuestion;
use App\Models\Question;
use App\Models\Student;
use App\Models\TrueOrFalseQuestion;
use App\Models\User;
use Tests\TestCase;

class SelfAssessmentTest extends TestCase
{
    public function testSaveResponseMultipleChoiceCorrect(): void
    {

        $user = User::factory()->create();
        $student = Student::factory()->create(['user_id' => $user->id]);
        $this->actingAs($user);

        $multipleChoice = MultipleChoiceQuestion::factory()->create([
            'question' => ['up' => 'What is the capital of France?', 'down' => 'What is the capital of France?'],
            'option_a' => ['up' => 'Paris', 'down' => 'Paris'],
            'option_b' => ['up' => 'London', 'down' => 'London'],
            'option_c' => ['up' => 'Berlin', 'down' => 'Berlin'],
            'option_d' => ['up' => 'Madrid', 'down' => 'Madrid'],
            'option_e' => ['up' => 'Rome', 'down' => 'Rome'],
            'answer' => 'A',
            'score' => 5,
            'difficulty_level' => 'easy',
            'academic_subtopic_id' => 1,
            'academic_topic_id' => AcademicTopic::factory(),
        ]);

        $question = Question::factory()->create([
            'questionable_type' => MultipleChoiceQuestion::class,
            'questionable_id' => $multipleChoice->id,
            'points' => 5,

        ]);



        $component = new \App\Livewire\Assessment\SelfAssessment();
        $component->questions = [$question];
        $component->responses = [
            0 => [
                'response' => null,
                'is_answered' => false,
            ],
        ];

        $component->saveResponse(0, 'A');

        $this->assertTrue($component->responses[0]['is_correct']);
        $this->assertEquals(5, $component->responses[0]['score']);
    }

    public function testSaveResponseTrueFalseCorrect(): void
    {
        $user = User::factory()->create();
        $student = Student::factory()->create(['user_id' => $user->id]);
        $this->actingAs($user);

        $trueFalse = TrueOrFalseQuestion::factory()->create([
            'answer' => true,
            'question' => ['up' => 'What is the capital of France?', 'down' => 'What is the capital of France?'],
            'academic_topic_id' => AcademicTopic::factory(),

        ]);
        $question = Question::factory()->create([
            'questionable_type' => TrueOrFalseQuestion::class,
            'questionable_id' => $trueFalse->id,
            'points' => 3,
        ]);



        $component = new \App\Livewire\Assessment\SelfAssessment();
        $component->questions = [$question];
        $component->responses = [
            0 => [
                'response' => null,
                'is_answered' => false,
            ],
        ];

        $component->saveResponse(0, 'true');

        $this->assertTrue($component->responses[0]['is_correct']);
        $this->assertEquals(3, $component->responses[0]['score']);
    }

    public function testCompleteAssessment(): void
    {
        $user = User::factory()->create();
        $student = Student::factory()->create(['user_id' => $user->id]);
        $this->actingAs($user);
        $questions = [
            Question::factory()->create([
                'questionable_type' => MultipleChoiceQuestion::class,
                'questionable_id' => 1,
                'points' => 5,
            ]),
            Question::factory()->create([
                'questionable_type' => TrueOrFalseQuestion::class,
                'questionable_id' => 2,
                'points' => 3,
            ]),
        ];

        $component = new \App\Livewire\Assessment\SelfAssessment();
        $component->questions = $questions;
        $component->responses = [
            0 => [
                'response' => 'A',
                'is_answered' => true,
                'is_correct' => true,
                'score' => 5,
            ],
            1 => [
                'response' => 'true',
                'is_answered' => true,
                'is_correct' => true,
                'score' => 3,
            ],
        ];
        $component->assessment = Assessment::factory()->create([
            'topic_id' => AcademicTopic::factory(),
            'title' => 'Test Assessment',
            'subject_id' => AcademicSubject::factory(),
            'student_id' => Student::factory()->create(['user_id' => User::factory()->create()->id])->id,
        ]);

        $component->completeAssessment();

        $this->assertEquals(8, $component->result['total_score']);
        $this->assertEquals(8, $component->result['max_score']);
        $this->assertEquals(100, $component->result['percentage_score']);
        $this->assertEquals(1, $component->result['byType']['multiple_choice']['correct_count']);
        $this->assertEquals(1, $component->result['byType']['true_false']['correct_count']);
    }
}
