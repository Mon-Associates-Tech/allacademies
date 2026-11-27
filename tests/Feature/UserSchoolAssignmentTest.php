<?php

namespace Tests\Feature;

use App\Models\School;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserSchoolAssignmentTest extends TestCase
{
    use RefreshDatabase;

    public function test_owner_can_change_a_user_school_and_their_student_profile_school(): void
    {
        $sourceSchool = School::factory()->create();
        $destinationSchool = School::factory()->create();
        $owner = User::factory()->create(['role' => 'owner']);
        $user = User::factory()->create([
            'role' => 'student',
            'school_id' => $sourceSchool->id,
        ]);
        Student::create([
            'user_id' => $user->id,
            'school_id' => $sourceSchool->id,
            'student_id' => 'STU-001',
        ]);

        $this->actingAs($owner)
            ->post(route('users.change-school'), [
                'user_id' => $user->id,
                'new_school_id' => $destinationSchool->id,
            ])
            ->assertRedirect(route('users.index'));

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'school_id' => $destinationSchool->id,
        ]);
        $this->assertDatabaseHas('students', [
            'user_id' => $user->id,
            'school_id' => $destinationSchool->id,
        ]);
    }

    public function test_superadmin_can_view_school_actions_and_change_a_user_school(): void
    {
        $destinationSchool = School::factory()->create();
        $superadmin = User::factory()->create(['role' => 'superadmin']);
        $user = User::factory()->create(['role' => 'guest']);

        $this->actingAs($superadmin)
            ->get(route('users.index'))
            ->assertOk()
            ->assertSee('name="school"')
            ->assertSee('Change School');

        $this->post(route('users.change-school'), [
            'user_id' => $user->id,
            'new_school_id' => $destinationSchool->id,
        ])->assertRedirect(route('users.index'));

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'school_id' => $destinationSchool->id,
        ]);
    }

    public function test_owner_can_filter_users_by_school(): void
    {
        $selectedSchool = School::factory()->create(['name' => 'Selected School']);
        $otherSchool = School::factory()->create(['name' => 'Other School']);
        $owner = User::factory()->create(['role' => 'owner']);
        User::factory()->create([
            'name' => 'Selected School User',
            'role' => 'guest',
            'school_id' => $selectedSchool->id,
        ]);
        User::factory()->create([
            'name' => 'Other School User',
            'role' => 'guest',
            'school_id' => $otherSchool->id,
        ]);

        $this->actingAs($owner)
            ->get(route('users.index', ['school' => $selectedSchool->id]))
            ->assertOk()
            ->assertSee('Selected School User')
            ->assertDontSee('Other School User')
            ->assertSee('name="school"')
            ->assertSee('Selected School');
    }

    public function test_overseer_can_filter_users_by_an_accessible_school_only(): void
    {
        $accessibleSchool = School::factory()->create(['name' => 'Accessible School']);
        $otherSchool = School::factory()->create(['name' => 'Other School']);
        $overseer = User::factory()->create(['role' => 'overseer']);
        $overseer->overseenSchools()->attach($accessibleSchool);
        User::factory()->create([
            'name' => 'Accessible School User',
            'role' => 'guest',
            'school_id' => $accessibleSchool->id,
        ]);
        User::factory()->create([
            'name' => 'Other School User',
            'role' => 'guest',
            'school_id' => $otherSchool->id,
        ]);

        $this->actingAs($overseer)
            ->get(route('users.index', ['school' => $accessibleSchool->id]))
            ->assertOk()
            ->assertSee('Accessible School User')
            ->assertDontSee('Other School User')
            ->assertSee('name="school"')
            ->assertSee('Accessible School')
            ->assertDontSee('Other School');
    }

    public function test_admin_cannot_change_a_user_school(): void
    {
        $school = School::factory()->create();
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create(['role' => 'guest']);

        $this->actingAs($admin)
            ->get(route('users.index'))
            ->assertOk()
            ->assertDontSee('name="school"');

        $this->actingAs($admin)
            ->post(route('users.change-school'), [
                'user_id' => $user->id,
                'new_school_id' => $school->id,
            ])
            ->assertForbidden();
    }

    public function test_owner_accounts_cannot_be_moved_to_another_school(): void
    {
        $school = School::factory()->create();
        $owner = User::factory()->create(['role' => 'owner']);
        $anotherOwner = User::factory()->create(['role' => 'owner']);

        $this->actingAs($owner)
            ->post(route('users.change-school'), [
                'user_id' => $anotherOwner->id,
                'new_school_id' => $school->id,
            ])
            ->assertForbidden();

        $this->assertDatabaseHas('users', [
            'id' => $anotherOwner->id,
            'school_id' => null,
        ]);
    }
}
