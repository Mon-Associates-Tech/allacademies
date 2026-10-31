<?php

namespace Tests\Feature\Center;

use App\ExaminationHub\Models\GeneralExam;
use App\ExaminationHub\Traits\HasUuid;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HasUuidTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function new_exam_gets_ulid_uuid()
    {
        $exam = GeneralExam::create([
            'title' => 'Test Exam',
            'user_id' => 1,
            'status' => 'draft',
        ]);

        $this->assertNotNull($exam->uuid);
        $this->assertEquals(26, strlen($exam->uuid));
        $this->assertMatchesRegularExpression('/^[0-9a-km-zA-HJ-NP-Z]+$/', $exam->uuid);
    }

    /** @test */
    public function uuid_is_unique()
    {
        $exam1 = GeneralExam::create([
            'title' => 'Exam 1',
            'user_id' => 1,
            'status' => 'draft',
        ]);

        $exam2 = GeneralExam::create([
            'title' => 'Exam 2',
            'user_id' => 1,
            'status' => 'draft',
        ]);

        $this->assertNotEquals($exam1->uuid, $exam2->uuid);
    }

    /** @test */
    public function find_by_uuid_works()
    {
        $exam = GeneralExam::create([
            'title' => 'Find Me',
            'user_id' => 1,
            'status' => 'draft',
        ]);

        $found = GeneralExam::findByUuid($exam->uuid);
        $this->assertEquals($exam->id, $found->id);
    }

    /** @test */
    public function find_by_uuid_returns_null_for_missing()
    {
        $found = GeneralExam::findByUuid('01ARZ3NDEKTSV4RRFFQ69G5FAV');
        $this->assertNull($found);
    }
}
