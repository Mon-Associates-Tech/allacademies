<?php

namespace Tests\Feature;

use App\Models\School;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAttendanceOverviewTest extends TestCase
{
    use RefreshDatabase;

    public function test_school_administrator_can_open_read_only_attendance_overview(): void
    {
        $school = School::factory()->create();
        $administrator = User::factory()->create([
            'role' => 'admin',
            'school_id' => $school->id,
        ]);

        $this->actingAs($administrator)
            ->get(route('admin.attendance.index'))
            ->assertOk()
            ->assertSee('Attendance Overview')
            ->assertSee('Read-only attendance records');
    }

    public function test_owner_can_open_attendance_overview_without_a_selected_school(): void
    {
        $owner = User::factory()->create([
            'role' => 'owner',
            'school_id' => null,
        ]);

        $this->actingAs($owner)
            ->get(route('admin.attendance.index'))
            ->assertOk()
            ->assertSee('Attendance Overview');
    }

    public function test_student_cannot_open_administrator_attendance_overview(): void
    {
        $school = School::factory()->create();
        $student = User::factory()->create([
            'role' => 'student',
            'school_id' => $school->id,
        ]);

        $this->actingAs($student)
            ->get(route('admin.attendance.index'))
            ->assertForbidden();
    }
}
