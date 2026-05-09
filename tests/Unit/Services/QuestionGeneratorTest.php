<?php

namespace Tests\Unit\Services;

use App\Exceptions\NotEnoughQuestionsException;
use App\Models\AcademicSubject;
use App\Models\AcademicSubtopic;
use App\Models\AcademicTopic;
use App\Models\EssayQuestion;
use App\Models\MultipleChoiceQuestion;
use App\Models\School;
use App\Models\TrueOrFalseQuestion;
use App\Services\QuestionGenerator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QuestionGeneratorTest extends TestCase
{
    use RefreshDatabase;

    private School $school;
    private AcademicSubject $subject;
    private AcademicTopic $topic;
    private AcademicSubtopic $subtopic1;
    private AcademicSubtopic $subtopic2;

    protected function setUp(): void
    {
        parent::setUp();

        $this->school = School::factory()->create();

        $this->subject = AcademicSubject::factory()->create();

        $this->topic = AcademicTopic::create([
            'name' => 'Test Topic',
            'academic_subject_id' => $this->subject->id,
        ]);

        $this->subtopic1 = AcademicSubtopic::create([
            'name' => 'Subtopic 1',
            'academic_topic_id' => $this->topic->id,
        ]);

        $this->subtopic2 = AcademicSubtopic::create([
            'name' => 'Subtopic 2',
            'academic_topic_id' => $this->topic->id,
        ]);
    }

    public function test_can_select_questions_from_topic_without_subtopics(): void
    {
        // Create 10 questions at topic level (no subtopic)
        MultipleChoiceQuestion::factory()->count(10)->create([
            'academic_topic_id' => $this->topic->id,
            'academic_subtopic_id' => null,
        ]);

        $generator = new QuestionGenerator();
        $reflection = new \ReflectionClass($generator);
        $method = $reflection->getMethod('selectQuestionsForSection');
        $method->setAccessible(true);

        $questions = $method->invoke(
            $generator,
            'multiple_choice_questions',
            [$this->topic->id],
            [],
            5,
            []
        );

        $this->assertCount(5, $questions);
    }

    public function test_can_select_questions_from_subtopics(): void
    {
        // Create questions in subtopics
        MultipleChoiceQuestion::factory()->count(5)->create([
            'academic_topic_id' => $this->topic->id,
            'academic_subtopic_id' => $this->subtopic1->id,
        ]);

        MultipleChoiceQuestion::factory()->count(5)->create([
            'academic_topic_id' => $this->topic->id,
            'academic_subtopic_id' => $this->subtopic2->id,
        ]);

        $generator = new QuestionGenerator();
        $reflection = new \ReflectionClass($generator);
        $method = $reflection->getMethod('selectQuestionsForSection');
        $method->setAccessible(true);

        $subtopics = [
            ['id' => $this->subtopic1->id, 'count' => 3],
            ['id' => $this->subtopic2->id, 'count' => 2],
        ];

        $questions = $method->invoke(
            $generator,
            'multiple_choice_questions',
            [$this->topic->id],
            $subtopics,
            5,
            []
        );

        $this->assertCount(5, $questions);
    }

    public function test_can_select_questions_from_both_topic_and_subtopics(): void
    {
        // Create questions at topic level
        MultipleChoiceQuestion::factory()->count(10)->create([
            'academic_topic_id' => $this->topic->id,
            'academic_subtopic_id' => null,
        ]);

        // Create questions in subtopic
        MultipleChoiceQuestion::factory()->count(5)->create([
            'academic_topic_id' => $this->topic->id,
            'academic_subtopic_id' => $this->subtopic1->id,
        ]);

        $generator = new QuestionGenerator();
        $reflection = new \ReflectionClass($generator);
        $method = $reflection->getMethod('selectQuestionsForSection');
        $method->setAccessible(true);

        // Request 3 from subtopic, rest from topic
        $subtopics = [
            ['id' => $this->subtopic1->id, 'count' => 3],
        ];

        $questions = $method->invoke(
            $generator,
            'multiple_choice_questions',
            [$this->topic->id],
            $subtopics,
            8, // Total needed
            []
        );

        // Should get 3 from subtopic + 5 from topic = 8 total
        $this->assertCount(8, $questions);
    }

    public function test_throws_exception_when_insufficient_questions(): void
    {
        // Create only 2 questions
        MultipleChoiceQuestion::factory()->count(2)->create([
            'academic_topic_id' => $this->topic->id,
            'academic_subtopic_id' => null,
        ]);

        $generator = new QuestionGenerator();
        $reflection = new \ReflectionClass($generator);
        $method = $reflection->getMethod('selectQuestionsForSection');
        $method->setAccessible(true);

        $this->expectException(NotEnoughQuestionsException::class);

        $method->invoke(
            $generator,
            'multiple_choice_questions',
            [$this->topic->id],
            [],
            10, // Request more than available
            []
        );
    }

    public function test_does_not_reuse_questions_across_sections(): void
    {
        // Create 10 questions
        MultipleChoiceQuestion::factory()->count(10)->create([
            'academic_topic_id' => $this->topic->id,
            'academic_subtopic_id' => null,
        ]);

        $generator = new QuestionGenerator();
        $reflection = new \ReflectionClass($generator);
        $method = $reflection->getMethod('selectQuestionsForSection');
        $method->setAccessible(true);

        // First section gets 5 questions
        $section1Questions = $method->invoke(
            $generator,
            'multiple_choice_questions',
            [$this->topic->id],
            [],
            5,
            []
        );

        // Second section should get different 5 questions
        $section2Questions = $method->invoke(
            $generator,
            'multiple_choice_questions',
            [$this->topic->id],
            [],
            5,
            $section1Questions // Pass used questions
        );

        $this->assertCount(5, $section1Questions);
        $this->assertCount(5, $section2Questions);
        $this->assertEmpty(array_intersect($section1Questions, $section2Questions));
    }

    public function test_can_select_essay_questions(): void
    {
        EssayQuestion::factory()->count(10)->create([
            'academic_topic_id' => $this->topic->id,
            'academic_subtopic_id' => null,
        ]);

        $generator = new QuestionGenerator();
        $reflection = new \ReflectionClass($generator);
        $method = $reflection->getMethod('selectQuestionsForSection');
        $method->setAccessible(true);

        $questions = $method->invoke(
            $generator,
            'essay_questions',
            [$this->topic->id],
            [],
            5,
            []
        );

        $this->assertCount(5, $questions);
    }

    public function test_can_select_true_or_false_questions(): void
    {
        TrueOrFalseQuestion::factory()->count(10)->create([
            'academic_topic_id' => $this->topic->id,
            'academic_subtopic_id' => null,
        ]);

        $generator = new QuestionGenerator();
        $reflection = new \ReflectionClass($generator);
        $method = $reflection->getMethod('selectQuestionsForSection');
        $method->setAccessible(true);

        $questions = $method->invoke(
            $generator,
            'true_or_false_questions',
            [$this->topic->id],
            [],
            5,
            []
        );

        $this->assertCount(5, $questions);
    }

    public function test_handles_duplicate_subtopic_ids_correctly(): void
    {
        // Create questions in subtopic
        MultipleChoiceQuestion::factory()->count(10)->create([
            'academic_topic_id' => $this->topic->id,
            'academic_subtopic_id' => $this->subtopic1->id,
        ]);

        $generator = new QuestionGenerator();
        $reflection = new \ReflectionClass($generator);
        $method = $reflection->getMethod('selectQuestionsForSection');
        $method->setAccessible(true);

        // Pass duplicate subtopic IDs with different counts
        $subtopics = [
            ['id' => $this->subtopic1->id, 'count' => 3],
            ['id' => $this->subtopic1->id, 'count' => 5], // Duplicate, should take max
        ];

        $questions = $method->invoke(
            $generator,
            'multiple_choice_questions',
            [$this->topic->id],
            $subtopics,
            5,
            []
        );

        // Should get 5 questions (the max count)
        $this->assertCount(5, $questions);
    }

    public function test_skips_invalid_subtopic_ids(): void
    {
        // Create questions in valid subtopic
        MultipleChoiceQuestion::factory()->count(10)->create([
            'academic_topic_id' => $this->topic->id,
            'academic_subtopic_id' => $this->subtopic1->id,
        ]);

        $generator = new QuestionGenerator();
        $reflection = new \ReflectionClass($generator);
        $method = $reflection->getMethod('selectQuestionsForSection');
        $method->setAccessible(true);

        // Include invalid subtopic ID
        $subtopics = [
            ['id' => 99999, 'count' => 5], // Invalid ID
            ['id' => $this->subtopic1->id, 'count' => 5], // Valid ID
        ];

        $questions = $method->invoke(
            $generator,
            'multiple_choice_questions',
            [$this->topic->id],
            $subtopics,
            5,
            []
        );

        // Should still get 5 questions from valid subtopic
        $this->assertCount(5, $questions);
    }

    public function test_returns_unique_question_ids(): void
    {
        // Create questions
        MultipleChoiceQuestion::factory()->count(10)->create([
            'academic_topic_id' => $this->topic->id,
            'academic_subtopic_id' => null,
        ]);

        $generator = new QuestionGenerator();
        $reflection = new \ReflectionClass($generator);
        $method = $reflection->getMethod('selectQuestionsForSection');
        $method->setAccessible(true);

        $questions = $method->invoke(
            $generator,
            'multiple_choice_questions',
            [$this->topic->id],
            [],
            5,
            []
        );

        // Check all IDs are unique
        $this->assertCount(5, $questions);
        $this->assertCount(5, array_unique($questions));
    }
}
