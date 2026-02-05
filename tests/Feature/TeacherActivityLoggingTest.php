<?php

namespace Tests\Feature;

use App\Models\Teacher;
use App\Models\User;
use App\Models\UserActivity;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TeacherActivityLoggingTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->actingAs($this->user);
    }

    /**
     * Test that teacher creation is logged automatically
     */
    public function test_teacher_creation_is_logged_automatically()
    {
        // Create a teacher
        $teacher = Teacher::factory()->create();

        // Verify activity was logged
        $activity = UserActivity::where('subject_id', $teacher->id)
            ->where('activity_type', 'create')
            ->first();

        $this->assertNotNull($activity);
        $this->assertEquals('create', $activity->activity_type);
        $this->assertEquals('Created Teacher', $activity->activity_name);
    }

    /**
     * Test that teacher update is logged automatically
     */
    public function test_teacher_update_is_logged_automatically()
    {
        // Create a teacher
        $teacher = Teacher::factory()->create();

        // Clear previous activity logs
        UserActivity::truncate();

        // Update the teacher
        $teacher->update([
            'specialization' => 'Mathematics',
            'department' => 'Science Department',
        ]);

        // Verify activity was logged
        $activity = UserActivity::where('subject_id', $teacher->id)
            ->where('activity_type', 'update')
            ->first();

        $this->assertNotNull($activity);
        $this->assertEquals('update', $activity->activity_type);
        $this->assertEquals('Updated Teacher', $activity->activity_name);

        // Verify changes are captured in metadata
        $this->assertArrayHasKey('changes', $activity->metadata);
        $this->assertTrue(isset($activity->metadata['changes']['specialization']) || isset($activity->metadata['changes']['department']));
    }

    /**
     * Test that teacher deletion is logged automatically
     */
    public function test_teacher_deletion_is_logged_automatically()
    {
        // Create a teacher
        $teacher = Teacher::factory()->create();
        $teacherId = $teacher->id;

        // Clear previous activity logs
        UserActivity::truncate();

        // Delete the teacher
        $teacher->delete();

        // Verify activity was logged
        $activity = UserActivity::where('subject_id', $teacherId)
            ->where('activity_type', 'delete')
            ->first();

        $this->assertNotNull($activity);
        $this->assertEquals('delete', $activity->activity_type);
        $this->assertEquals('Deleted Teacher', $activity->activity_name);
    }

    /**
     * Test that teacher update activity includes change metadata
     */
    public function test_teacher_update_includes_change_metadata()
    {
        // Create a teacher with initial specialization
        $teacher = Teacher::factory()->create(['specialization' => 'English']);

        // Clear previous activity logs
        UserActivity::truncate();

        // Update the teacher's specialization
        $originalSpecialization = $teacher->specialization;
        $newSpecialization = 'Mathematics';

        $teacher->update(['specialization' => $newSpecialization]);

        // Get the activity log
        $activity = UserActivity::where('subject_id', $teacher->id)
            ->where('activity_type', 'update')
            ->first();

        // Verify metadata contains changes and original values
        $this->assertNotNull($activity->metadata);
        $this->assertArrayHasKey('changes', $activity->metadata);
        $this->assertArrayHasKey('original', $activity->metadata);

        // Verify the specific change
        if (isset($activity->metadata['changes']['specialization'])) {
            $this->assertEquals($newSpecialization, $activity->metadata['changes']['specialization']);
        }
    }
}
