<?php

namespace Tests\Feature\Center;

use App\ExaminationHub\Models\Center;
use App\ExaminationHub\Models\ExamPackage;
use App\ExaminationHub\Models\ExamSyncState;
use App\ExaminationHub\Models\SyncRun;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CenterReadOnlyTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function center_model_creates_with_uuid()
    {
        $center = Center::create([
            'uuid' => (string) \Illuminate\Support\Str::ulid(),
            'code' => 'TEST01',
            'name' => 'Test Center',
        ]);

        $this->assertNotNull($center->uuid);
        $this->assertEquals('TEST01', $center->code);
    }

    /** @test */
    public function center_token_hashing_works()
    {
        $token = Center::generateToken();
        $hash = Center::hashToken($token);

        $center = Center::create([
            'uuid' => (string) \Illuminate\Support\Str::ulid(),
            'code' => 'TOKEN01',
            'name' => 'Token Test',
            'token_hash' => $hash,
        ]);

        $this->assertTrue($center->verifyToken($token));
        $this->assertFalse($center->verifyToken('wrong-token'));
    }

    /** @test */
    public function center_read_only_middleware_allows_get_on_center()
    {
        config(['center.role' => 'center']);

        $response = $this->get('/examinations/dashboard');

        // GET requests should pass through (may redirect to login, but not 403 from middleware)
        $this->assertTrue($response->getStatusCode() !== 403 || $response->isRedirect());
    }

    /** @test */
    public function center_read_only_middleware_blocks_post_on_center()
    {
        config(['center.role' => 'center']);

        $response = $this->post('/examinations/create/preview', []);

        $this->assertJson([
            'error' => 'Not allowed on center instance. Content management is only available on the central server.',
        ]);
        $this->assertEquals(403, $response->getStatusCode());
    }

    /** @test */
    public function center_read_only_middleware_allows_all_on_central()
    {
        config(['center.role' => 'central']);

        $response = $this->post('/examinations/create/preview', []);

        // Should not get 403 from CenterReadOnly middleware
        $this->assertNotEquals(403, $response->getStatusCode());
    }

    /** @test */
    public function exam_sync_state_model_works()
    {
        $state = ExamSyncState::create([
            'exam_uuid' => '01ARZ3NDEKTSV4RRFFQ69G5FAV',
            'applied_revision' => 1,
            'applied_state_version' => 1,
        ]);

        $this->assertFalse($state->needsRevision(1));
        $this->assertTrue($state->needsRevision(2));
        $this->assertFalse($state->needsStateUpdate(1));
        $this->assertTrue($state->needsStateUpdate(2));
    }

    /** @test */
    public function sync_run_model_works()
    {
        $run = SyncRun::start('pull');
        $this->assertEquals('running', $run->status);

        $run->markSuccess(10, 10);
        $this->assertEquals('success', $run->status);

        $failed = SyncRun::start('push');
        $failed->markFailed('Connection timeout', 5, 2);
        $this->assertEquals('failed', $failed->status);
        $this->assertEquals('Connection timeout', $failed->error);
    }
}
