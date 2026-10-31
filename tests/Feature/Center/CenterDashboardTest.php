<?php

namespace Tests\Feature\Center;

use App\ExaminationHub\Models\Center;
use App\ExaminationHub\Models\GeneralExam;
use App\ExaminationHub\Models\GeneralExamSubmission;
use App\ExaminationHub\Models\SyncRun;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CenterDashboardTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
    }

    public function test_admin_sees_all_centers(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $center1 = Center::create([
            'uuid' => (string) \Illuminate\Support\Str::ulid(),
            'code' => 'C1',
            'name' => 'Center 1',
            'is_active' => true,
        ]);

        $center2 = Center::create([
            'uuid' => (string) \Illuminate\Support\Str::ulid(),
            'code' => 'C2',
            'name' => 'Center 2',
            'is_active' => true,
        ]);

        $response = $this->actingAs($admin)->get('/center/dashboard');

        $response->assertStatus(200);
        $response->assertSee('Center 1');
        $response->assertSee('Center 2');
    }

    public function test_center_user_sees_only_own_center(): void
    {
        $center = Center::create([
            'uuid' => (string) \Illuminate\Support\Str::ulid(),
            'code' => 'C1',
            'name' => 'Center 1',
            'is_active' => true,
        ]);

        $user = User::factory()->create();
        $user->assignRole('teacher');
        $user->update(['center_id' => $center->id]);

        $otherCenter = Center::create([
            'uuid' => (string) \Illuminate\Support\Str::ulid(),
            'code' => 'C2',
            'name' => 'Center 2',
            'is_active' => true,
        ]);

        $response = $this->actingAs($user)->get('/center/dashboard');

        $response->assertStatus(200);
        $response->assertSee('Center 1');
        $response->assertDontSee('Center 2');
    }

    public function test_center_user_cannot_view_other_center_details(): void
    {
        $center = Center::create([
            'uuid' => (string) \Illuminate\Support\Str::ulid(),
            'code' => 'C1',
            'name' => 'Center 1',
            'is_active' => true,
        ]);

        $user = User::factory()->create();
        $user->assignRole('teacher');
        $user->update(['center_id' => $center->id]);

        $otherCenter = Center::create([
            'uuid' => (string) \Illuminate\Support\Str::ulid(),
            'code' => 'C2',
            'name' => 'Center 2',
            'is_active' => true,
        ]);

        $response = $this->actingAs($user)->get("/center/{$otherCenter->id}");

        $response->assertStatus(403);
    }

    public function test_dashboard_shows_parity_alerts(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $center = Center::create([
            'uuid' => (string) \Illuminate\Support\Str::ulid(),
            'code' => 'C1',
            'name' => 'Center 1',
            'is_active' => true,
        ]);

        $exam = GeneralExam::create([
            'uuid' => (string) \Illuminate\Support\Str::ulid(),
            'user_id' => 1,
            'title' => 'Test Exam',
            'access_code' => 'TEST123',
            'starts_at' => now()->subDay(),
            'ends_at' => now()->addDay(),
            'duration_in_minutes' => 60,
            'max_attempts' => 1,
            'participant_mode' => 'configured',
            'status' => 'published',
            'revision' => 1,
        ]);

        $submission = GeneralExamSubmission::create([
            'uuid' => (string) \Illuminate\Support\Str::ulid(),
            'general_exam_id' => $exam->id,
            'center_id' => $center->id,
            'participant_name' => 'Test Candidate',
            'status' => GeneralExamSubmission::STATUS_AUTO_GRADED,
            'score' => 5.0,
            'central_score' => 6.0,
            'parity_ok' => false,
            'parity_checked_at' => now(),
        ]);

        $response = $this->actingAs($admin)->get('/center/dashboard');

        $response->assertStatus(200);
        $response->assertSee('Parity Alerts');
    }

    public function test_dashboard_shows_data_freshness(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $center = Center::create([
            'uuid' => (string) \Illuminate\Support\Str::ulid(),
            'code' => 'C1',
            'name' => 'Center 1',
            'is_active' => true,
        ]);

        $syncRun = SyncRun::create([
            'direction' => 'push',
            'started_at' => now()->subMinutes(10),
            'finished_at' => now()->subMinutes(9),
            'status' => 'success',
            'items_total' => 5,
            'items_ok' => 5,
        ]);

        $response = $this->actingAs($admin)->get('/center/dashboard');

        $response->assertStatus(200);
        $response->assertSee('Last synced');
    }

    public function test_sync_page_shows_status(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $response = $this->actingAs($admin)->get('/center/sync');

        $response->assertStatus(200);
        $response->assertSee('Sync Status');
    }
}
