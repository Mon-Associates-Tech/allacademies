<?php

namespace Tests\Feature\Api;

use App\Models\AcademicGroup;
use App\Models\AcademicLevel;
use App\Models\AcademicSubject;
use App\Models\AcademicSubtopic;
use App\Models\AcademicTopic;
use App\Models\EssayQuestion;
use App\Models\MultipleChoiceQuestion;
use App\Models\School;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QuestionAvailabilityTest extends TestCase
{
    use RefreshDatabase;

    private AcademicGroup $group;
    private AcademicLevel $level;
    private AcademicSubject $subject;
    private AcademicTopic $topic1;
    private AcademicTopic $topic2;
    private AcademicSubtopic $subtopic1;
    private AcademicSubtopic $subtopic2;

    protected function setUp(): void
    {
        parent::setUp();

        // Create academic structure
        $this->group = AcademicGroup::factory()->create();
        $this->level = AcademicLevel::factory()->create([
            'academic_group_id' => $this->group->id,
        ]);
        $this->subject = AcademicSubject::factory()->create([
            'academic_level_id' => $this->level->id,
        ]);

        // Create topics
        $this->topic1 = AcademicTopic::create([
            'name' => 'Algebra',
            'academic_subject_id' => $this->subject->id,
        ]);

        $this->topic2 = AcademicTopic::create([
            'name' => 'Geometry',
            'academic_subject_id' => $this->subject->id,
        ]);

        // Create subtopics
        $this->subtopic1 = AcademicSubtopic::create([
            'name' => 'Linear Equations',
            'academic_topic_id' => $this->topic1->id,
        ]);

        $this->subtopic2 = AcademicSubtopic::create([
            'name' => 'Quadratic Equations',
            'academic_topic_id' => $this->topic1->id,
        ]);
    }

    public function test_check_availability_with_sufficient_questions(): void
    {
        // Create 10 multiple choice questions for topic1
        MultipleChoiceQuestion::factory()->count(10)->create([
            'academic_topic_id' => $this->topic1->id,
            'academic_subtopic_id' => null,
        ]);

        $response = $this->postJson('/api/questions/check-availability', [
            'academic_subject_id' => $this->subject->id,
            'question_type' => 'multiple_choice_questions',
            'required_count' => 5,
            'topic_ids' => [$this->topic1->id],
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'required_count' => 5,
                    'available_count' => 10,
                    'sufficient' => true,
                ],
            ]);
    }

    public function test_check_availability_with_insufficient_questions(): void
    {
        // Create only 3 essay questions for topic1
        EssayQuestion::factory()->count(3)->create([
            'academic_topic_id' => $this->topic1->id,
            'academic_subtopic_id' => null,
        ]);

        $response = $this->postJson('/api/questions/check-availability', [
            'academic_subject_id' => $this->subject->id,
            'question_type' => 'essay_questions',
            'required_count' => 10,
            'topic_ids' => [$this->topic1->id],
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'required_count' => 10,
                    'available_count' => 3,
                    'sufficient' => false,
                ],
            ]);
    }

    public function test_check_availability_with_subtopics(): void
    {
        // Create questions in subtopics
        MultipleChoiceQuestion::factory()->count(5)->create([
            'academic_topic_id' => $this->topic1->id,
            'academic_subtopic_id' => $this->subtopic1->id,
        ]);

        MultipleChoiceQuestion::factory()->count(7)->create([
            'academic_topic_id' => $this->topic1->id,
            'academic_subtopic_id' => $this->subtopic2->id,
        ]);

        $response = $this->postJson('/api/questions/check-availability', [
            'academic_subject_id' => $this->subject->id,
            'question_type' => 'multiple_choice_questions',
            'required_count' => 10,
            'subtopic_ids' => [$this->subtopic1->id, $this->subtopic2->id],
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'required_count' => 10,
                    'available_count' => 12,
                    'sufficient' => true,
                ],
            ])
            ->assertJsonStructure([
                'data' => [
                    'breakdown' => [
                        'by_subtopic' => [
                            '*' => ['id', 'name', 'available'],
                        ],
                    ],
                ],
            ]);
    }

    public function test_check_availability_with_multiple_topics(): void
    {
        // Create questions in multiple topics
        MultipleChoiceQuestion::factory()->count(8)->create([
            'academic_topic_id' => $this->topic1->id,
            'academic_subtopic_id' => null,
        ]);

        MultipleChoiceQuestion::factory()->count(6)->create([
            'academic_topic_id' => $this->topic2->id,
            'academic_subtopic_id' => null,
        ]);

        $response = $this->postJson('/api/questions/check-availability', [
            'academic_subject_id' => $this->subject->id,
            'question_type' => 'multiple_choice_questions',
            'required_count' => 12,
            'topic_ids' => [$this->topic1->id, $this->topic2->id],
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'required_count' => 12,
                    'available_count' => 14,
                    'sufficient' => true,
                ],
            ]);
    }

    public function test_check_availability_validation_errors(): void
    {
        $response = $this->postJson('/api/questions/check-availability', [
            'academic_subject_id' => 99999, // Non-existent
            'question_type' => 'multiple_choice_questions',
            'required_count' => 5,
        ]);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
                'message' => 'Validation failed',
            ]);
    }

    public function test_statistics_endpoint_returns_comprehensive_data(): void
    {
        // Create various questions
        MultipleChoiceQuestion::factory()->count(5)->create([
            'academic_topic_id' => $this->topic1->id,
            'academic_subtopic_id' => null,
        ]);

        EssayQuestion::factory()->count(3)->create([
            'academic_topic_id' => $this->topic1->id,
            'academic_subtopic_id' => $this->subtopic1->id,
        ]);

        $response = $this->getJson('/api/questions/statistics?academic_subject_id=' . $this->subject->id);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'subject' => ['id', 'name', 'code'],
                    'topics' => [
                        '*' => [
                            'id',
                            'name',
                            'essay_questions',
                            'multiple_choice_questions',
                            'true_or_false_questions',
                            'total_questions',
                            'subtopics',
                        ],
                    ],
                ],
            ]);
    }

    public function test_check_availability_with_group_and_level_validation(): void
    {
        MultipleChoiceQuestion::factory()->count(10)->create([
            'academic_topic_id' => $this->topic1->id,
        ]);

        $response = $this->postJson('/api/questions/check-availability', [
            'academic_subject_id' => $this->subject->id,
            'academic_group_id' => $this->group->id,
            'academic_level_id' => $this->level->id,
            'question_type' => 'multiple_choice_questions',
            'required_count' => 5,
            'topic_ids' => [$this->topic1->id],
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);
    }

    public function test_check_availability_with_wrong_level_returns_error(): void
    {
        $wrongLevel = AcademicLevel::factory()->create([
            'academic_group_id' => $this->group->id,
        ]);

        $response = $this->postJson('/api/questions/check-availability', [
            'academic_subject_id' => $this->subject->id,
            'academic_level_id' => $wrongLevel->id,
            'question_type' => 'multiple_choice_questions',
            'required_count' => 5,
        ]);

        $response->assertStatus(400)
            ->assertJson([
                'success' => false,
                'message' => 'Subject does not belong to the specified level',
            ]);
    }

    public function test_check_availability_defaults_to_all_topics_when_none_specified(): void
    {
        // Create questions in both topics
        MultipleChoiceQuestion::factory()->count(5)->create([
            'academic_topic_id' => $this->topic1->id,
        ]);

        MultipleChoiceQuestion::factory()->count(3)->create([
            'academic_topic_id' => $this->topic2->id,
        ]);

        $response = $this->postJson('/api/questions/check-availability', [
            'academic_subject_id' => $this->subject->id,
            'question_type' => 'multiple_choice_questions',
            'required_count' => 5,
            // No topic_ids or subtopic_ids specified
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'available_count' => 8,
                    'sufficient' => true,
                ],
            ]);
    }
}
