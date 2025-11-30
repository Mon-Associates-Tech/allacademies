<?php

namespace Tests\Feature\Api;

use App\Models\AcademicGroup;
use App\Models\AcademicLevel;
use App\Models\AcademicSubject;
use App\Models\AcademicTopic;
use App\Models\AcademicSubtopic;
use App\Models\MultipleChoiceQuestion;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ExaminationGenerationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Setup authorized user
        $user = User::factory()->create();
        Sanctum::actingAs($user);
    }

    /** @test */
    public function it_can_retrieve_academic_hierarchy()
    {
        $group = AcademicGroup::create(['name' => 'Group A']);
        $level = AcademicLevel::create(['name' => 'Level 1', 'academic_group_id' => $group->id]);
        $subject = AcademicSubject::create(['name' => 'Math', 'code' => 'MAT101', 'academic_level_id' => $level->id]);

        $this->getJson('/api/academic-groups')
            ->assertOk()
            ->assertJsonFragment(['name' => 'Group A']);

        $this->getJson("/api/academic-groups/{$group->id}/levels")
            ->assertOk()
            ->assertJsonFragment(['name' => 'Level 1']);

        $this->getJson("/api/academic-levels/{$level->id}/subjects")
            ->assertOk()
            ->assertJsonFragment(['code' => 'MAT101']);
    }

    /** @test */
    public function it_falls_back_to_topic_questions_when_subtopic_has_insufficient_questions()
    {
        // 1. Setup Data
        $subject = AcademicSubject::create(['name' => 'Science', 'code' => 'SCI101']);
        $topic = AcademicTopic::create(['name' => 'Physics', 'academic_subject_id' => $subject->id]);
        $subtopic = AcademicSubtopic::create(['name' => 'Motion', 'academic_topic_id' => $topic->id]);

        // Create 2 questions in Subtopic
        MultipleChoiceQuestion::factory()->count(2)->create([
            'academic_topic_id' => $topic->id,
            'academic_subtopic_id' => $subtopic->id,
        ]);

        // Create 5 questions in Topic (no subtopic)
        MultipleChoiceQuestion::factory()->count(5)->create([
            'academic_topic_id' => $topic->id,
            'academic_subtopic_id' => null,
        ]);

        // 2. Define Request Payload
        // Requesting 5 questions in total:
        // - Priority: From Subtopic 'Motion'
        // - Fallback: From Topic 'Physics'
        $payload = [
            'heading' => [
                'title' => 'Physics Quiz',
                'duration' => 60,
                'instructions' => 'Answer all questions',
            ],
            'sections' => [
                [
                    'name' => 'Section A',
                    'type' => 'multiple_choice_questions',
                    'count' => 5, // We want 5 total
                    'topics' => [$topic->id],
                    'subtopics' => [
                        ['id' => $subtopic->id, 'count' => 5] // Try to get 5 from subtopic
                    ],
                    'instructions' => 'Select correct option',
                ]
            ]
        ];

        // 3. Make Request
        $response = $this->postJson('/api/questions/generate', $payload);

        // 4. Assertions
        $response->assertOk();

        $generatedQuestions = $response->json('data.sections.0.questions');

        // We should get exactly 5 questions
        $this->assertCount(5, $generatedQuestions);

        // Verify composition: 2 from subtopic, 3 from topic
        $subtopicQuestionsCount = collect($generatedQuestions)
            ->where('academic_subtopic_id', $subtopic->id)
            ->count();

        $topicQuestionsCount = collect($generatedQuestions)
            ->whereNull('academic_subtopic_id')
            ->count();

        $this->assertEquals(2, $subtopicQuestionsCount, 'Should have retrieved all 2 available subtopic questions');
        $this->assertEquals(3, $topicQuestionsCount, 'Should have retrieved 3 fallback questions from topic');
    }
}
