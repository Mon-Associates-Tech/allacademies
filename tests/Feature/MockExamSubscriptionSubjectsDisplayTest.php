<?php

namespace Tests\Feature;

use App\Livewire\MockExam\Subscriptions\Dashboard;
use App\MockExam\Models\MockExam;
use App\MockExam\Models\MockExamSubscription;
use App\MockExam\Models\MockExamSubscriptionPlan;
use App\MockExam\Models\MockExamSubscriptionStatus;
use App\MockExam\Models\MockExamTemplate;
use App\Models\AcademicGroup;
use App\Models\AcademicLevel;
use App\Models\AcademicSubject;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Tests\TestCase;

class MockExamSubscriptionSubjectsDisplayTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function generated_mocks_are_linked_to_the_active_subscription_and_include_subject_details_for_the_dashboard(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        $group = AcademicGroup::create([
            'name' => 'Senior Secondary',
            'tag' => 'SS',
            'school_id' => 1,
        ]);

        $level = AcademicLevel::create([
            'name' => 'SSS 1',
            'academic_group_id' => $group->id,
        ]);

        $subject = AcademicSubject::create([
            'name' => 'Biology',
            'code' => 'BIO',
            'academic_level_id' => $level->id,
        ]);

        $plan = MockExamSubscriptionPlan::create([
            'name' => 'Print Access',
            'type' => 'print',
            'max_subjects' => 10,
            'max_exams' => 5,
            'max_participants' => 30,
            'duration_type' => 'month',
            'duration_value' => 1,
            'base_price' => 0,
            'is_active' => true,
            'description' => 'For tests',
        ]);

        $subscription = MockExamSubscription::create([
            'user_id' => $user->id,
            'mock_exam_subscription_plan_id' => $plan->id,
            'type' => 'print',
            'user_group' => 'individual',
            'status' => MockExamSubscriptionStatus::Active,
            'participant_slots' => 30,
            'participants_used' => 0,
            'exams_used' => 0,
            'max_exams' => 1,
            'amount_paid' => 0,
            'granted_by_owner' => false,
            'activated_at' => now(),
            'expires_at' => now()->addMonth(),
        ]);

        $subscription->subjects()->sync([$subject->id]);

        $template = MockExamTemplate::create([
            'user_id' => $user->id,
            'academic_group_id' => $group->id,
            'academic_level_id' => $level->id,
            'academic_subject_id' => $subject->id,
            'name' => 'Biology Mock Template',
            'description' => 'Template used for the test',
            'is_active' => true,
            'default_duration_minutes' => 60,
            'sections_config' => [
                [
                    'title' => 'Section 1',
                    'question_type' => 'mcq',
                    'question_count' => 2,
                    'marks_per_question' => 1,
                    'topic_ids' => [],
                    'subtopic_ids' => [],
                ],
            ],
        ]);

        $this->actingAs($user)
            ->post(route('mock-exams.generate.store', $template));

        $exam = MockExam::first();

        $this->assertNotNull($exam);
        $this->assertSame($subscription->id, $exam->mock_exam_subscription_id);
        $this->assertTrue($exam->subjectExams()->where('academic_subject_id', $subject->id)->exists());

        $dashboard = new Dashboard();
        $dashboard->expandedSubscriptionIds = [$subscription->id];

        $view = $dashboard->render();
        $data = $view->getData();

        $this->assertArrayHasKey($subscription->id, $data['subscriptionMocks']);
        $this->assertTrue(
            $data['subscriptionMocks'][$subscription->id]->first()->subjectExams
                ->pluck('academicSubject.name')
                ->contains('Biology')
        );
    }
}
