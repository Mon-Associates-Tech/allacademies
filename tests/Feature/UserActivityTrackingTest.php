<?php

namespace Tests\Feature;

use App\Facades\ActivityLogger;
use App\Models\Book;
use App\Models\Quiz;
use App\Models\User;
use App\Models\UserActivity;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserActivityTrackingTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->actingAs($this->user);
    }

    // ============================================================================
    // Test ActivityLogger Facade
    // ============================================================================

    public function test_can_log_custom_activity()
    {
        ActivityLogger::log(
            'upload',
            'Document Uploaded',
            'document',
            null,
            ['file_size' => '2.5MB']
        );

        $this->assertDatabaseHas('user_activities', [
            'user_id' => $this->user->id,
            'activity_type' => 'upload',
            'activity_name' => 'Document Uploaded',
            'category' => 'document',
        ]);
    }

    public function test_can_log_login_activity()
    {
        ActivityLogger::logLogin($this->user);

        $this->assertDatabaseHas('user_activities', [
            'user_id' => $this->user->id,
            'activity_type' => 'login',
            'activity_name' => 'User Login',
            'category' => 'authentication',
        ]);
    }

    public function test_can_log_quiz_start()
    {
        $quiz = Quiz::factory()->create();

        ActivityLogger::logQuizStart($this->user, $quiz, [
            'total_questions' => 10,
        ]);

        $activity = UserActivity::latest()->first();

        $this->assertEquals('start', $activity->activity_type);
        $this->assertEquals('Quiz Started', $activity->activity_name);
        $this->assertEquals('academic', $activity->category);
        $this->assertEquals($quiz->id, $activity->subject_id);
    }

    public function test_can_log_quiz_submission_with_score()
    {
        $quiz = Quiz::factory()->create();

        ActivityLogger::logQuizSubmit($this->user, $quiz, [
            'score' => 95,
            'duration' => 45,
            'questions_correct' => 19,
        ]);

        $activity = UserActivity::latest()->first();

        $this->assertEquals('submit', $activity->activity_type);
        $this->assertEquals('Quiz Submitted', $activity->activity_name);
        $this->assertEquals(95, $activity->metadata['score']);
        $this->assertEquals(45, $activity->metadata['duration']);
    }

    public function test_can_log_book_added_to_reading_list()
    {
        $book = Book::factory()->create();

        ActivityLogger::logBookAddedToReadingList($this->user, $book);

        $activity = UserActivity::latest()->first();

        $this->assertEquals('favorite', $activity->activity_type);
        $this->assertEquals('Book Added to Reading List', $activity->activity_name);
        $this->assertEquals('library', $activity->category);
        $this->assertEquals($book->id, $activity->subject_id);
    }

    public function test_can_log_messenger_token_purchase()
    {
        ActivityLogger::logMessengerTokenPurchase($this->user, [
            'tokens' => 1000,
            'amount' => 50,
            'currency' => 'USD',
        ]);

        $activity = UserActivity::latest()->first();

        $this->assertEquals('purchase', $activity->activity_type);
        $this->assertEquals('Messenger Tokens Purchased', $activity->activity_name);
        $this->assertEquals('payment', $activity->category);
        $this->assertEquals(1000, $activity->metadata['tokens']);
        $this->assertEquals(50, $activity->metadata['amount']);
    }

    // ============================================================================
    // Test ActivityLoggable Trait
    // ============================================================================

    public function test_book_creation_is_logged_automatically()
    {
        $book = Book::factory()->create();

        $activity = UserActivity::latest()->first();

        $this->assertEquals('create', $activity->activity_type);
        $this->assertEquals('Book Created', $activity->activity_name);
        $this->assertNotNull($activity->subject_id);
    }

    public function test_book_update_is_logged_automatically()
    {
        $book = Book::factory()->create();
        UserActivity::truncate();

        $book->update(['title' => 'Updated Title']);

        $activity = UserActivity::latest()->first();

        $this->assertEquals('update', $activity->activity_type);
        $this->assertEquals('Book Updated', $activity->activity_name);
    }

    public function test_book_deletion_is_logged_automatically()
    {
        $book = Book::factory()->create();
        UserActivity::truncate();

        $book->delete();

        $activity = UserActivity::latest()->first();

        $this->assertEquals('delete', $activity->activity_type);
        $this->assertEquals('Book Deleted', $activity->activity_name);
    }

    public function test_can_skip_activity_logging()
    {
        $book = Book::factory()->create();
        UserActivity::truncate();

        $book->skipActivityLogging()->update(['title' => 'New Title']);

        $this->assertDatabaseMissing('user_activities', [
            'subject_id' => $book->id,
            'activity_type' => 'update',
        ]);
    }

    public function test_can_manually_log_activity_on_model()
    {
        $book = Book::factory()->create();
        UserActivity::truncate();

        $book->logActivity(
            'favorite',
            'Added to Reading List',
            'library'
        );

        $activity = UserActivity::latest()->first();

        $this->assertEquals('favorite', $activity->activity_type);
        $this->assertEquals('Added to Reading List', $activity->activity_name);
    }

    // ============================================================================
    // Test Activity Retrieval and Filtering
    // ============================================================================

    public function test_can_retrieve_user_activities()
    {
        ActivityLogger::logLogin($this->user);
        ActivityLogger::logLogout($this->user);

        $activities = $this->user->activities;

        $this->assertCount(2, $activities);
    }

    public function test_can_filter_activities_by_category()
    {
        ActivityLogger::logLogin($this->user);
        $quiz = Quiz::factory()->create();
        ActivityLogger::logQuizStart($this->user, $quiz);

        $academicActivities = UserActivity::byCategory('academic')
            ->byUser($this->user->id)
            ->get();

        $this->assertCount(1, $academicActivities);
        $this->assertEquals('academic', $academicActivities[0]->category);
    }

    public function test_can_filter_activities_by_type()
    {
        ActivityLogger::logLogin($this->user);
        $quiz = Quiz::factory()->create();
        ActivityLogger::logQuizStart($this->user, $quiz);

        $startActivities = UserActivity::byActivityType('start')
            ->byUser($this->user->id)
            ->get();

        $this->assertCount(1, $startActivities);
        $this->assertEquals('start', $startActivities[0]->activity_type);
    }

    public function test_can_get_recent_activities()
    {
        ActivityLogger::logLogin($this->user);
        ActivityLogger::logLogout($this->user);

        $recent = UserActivity::byUser($this->user->id)
            ->recent(days: 7)
            ->get();

        $this->assertCount(2, $recent);
    }

    public function test_can_get_activity_statistics()
    {
        ActivityLogger::logLogin($this->user);
        $quiz = Quiz::factory()->create();
        ActivityLogger::logQuizStart($this->user, $quiz);
        ActivityLogger::logQuizSubmit($this->user, $quiz);

        $stats = ActivityLogger::getUserActivityStatistics($this->user->id);

        $this->assertEquals(3, $stats['total_activities']);
        $this->assertArrayHasKey('by_category', $stats);
        $this->assertArrayHasKey('by_activity_type', $stats);
        $this->assertNotNull($stats['last_activity']);
    }

    public function test_can_paginate_activities()
    {
        for ($i = 0; $i < 25; $i++) {
            ActivityLogger::logLogin($this->user);
        }

        $paginated = ActivityLogger::getUserActivities(
            userId: $this->user->id,
            limit: 10
        );

        $this->assertEquals(10, count($paginated->items()));
        $this->assertEquals(3, $paginated->lastPage());
    }

    // ============================================================================
    // Test Metadata and Additional Data
    // ============================================================================

    public function test_activity_includes_ip_address()
    {
        ActivityLogger::logLogin($this->user);

        $activity = UserActivity::latest()->first();

        $this->assertNotNull($activity->ip_address);
    }

    public function test_activity_includes_user_agent()
    {
        ActivityLogger::logLogin($this->user);

        $activity = UserActivity::latest()->first();

        $this->assertNotNull($activity->user_agent);
    }

    public function test_metadata_is_stored_as_json()
    {
        ActivityLogger::log(
            'submit',
            'Quiz Completed',
            'academic',
            null,
            [
                'score' => 95,
                'duration' => 45,
                'details' => ['questions' => 20],
            ]
        );

        $activity = UserActivity::latest()->first();

        $this->assertIsArray($activity->metadata);
        $this->assertEquals(95, $activity->metadata['score']);
        $this->assertEquals(['questions' => 20], $activity->metadata['details']);
    }

    // ============================================================================
    // Test Edge Cases
    // ============================================================================

    public function test_activity_not_logged_when_not_authenticated()
    {
        auth()->logout();

        ActivityLogger::log(
            'view',
            'Viewed Page',
            'content'
        );

        $this->assertDatabaseMissing('user_activities', [
            'activity_type' => 'view',
        ]);
    }

    public function test_can_log_activity_for_specific_user()
    {
        $otherUser = User::factory()->create();

        ActivityLogger::log(
            'view',
            'Viewed Page',
            'content',
            null,
            [],
            null,
            null,
            $otherUser->id
        );

        $activity = UserActivity::where('user_id', $otherUser->id)->first();

        $this->assertNotNull($activity);
        $this->assertEquals($otherUser->id, $activity->user_id);
    }

    public function test_polymorphic_relationship_works()
    {
        $book = Book::factory()->create();
        ActivityLogger::logBookAddedToReadingList($this->user, $book);

        $activity = UserActivity::latest()->first();
        $subject = $activity->subject;

        $this->assertInstanceOf(Book::class, $subject);
        $this->assertEquals($book->id, $subject->id);
    }

    public function test_description_is_generated_when_not_provided()
    {
        ActivityLogger::log(
            'view',
            'Viewed Page',
            'content'
        );

        $activity = UserActivity::latest()->first();

        $this->assertNotNull($activity->description);
    }
}
