<?php

namespace Tests\Feature;

use App\Livewire\Administrators\AdminNavigation;
use App\Livewire\Administrators\OverseerSchoolManagement;
use App\Models\Role;
use App\Models\School;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\TestCase;

class OverseerSchoolManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_overseers_can_access_only_assigned_schools(): void
    {
        $overseer = User::factory()->create(['role' => 'overseer']);
        $assignedSchool = School::factory()->create();
        $unassignedSchool = School::factory()->create();
        $overseer->overseenSchools()->attach($assignedSchool);

        $this->assertTrue($overseer->canAccessSchool($assignedSchool->id));
        $this->assertFalse($overseer->canAccessSchool($unassignedSchool->id));
    }

    public function test_overseer_can_view_school_admin_navigation_without_owner_tools(): void
    {
        $overseer = User::factory()->create(['role' => 'overseer']);

        Livewire::actingAs($overseer)
            ->test(AdminNavigation::class)
            ->assertSee('School Switcher')
            ->assertSee('Students')
            ->assertSee('Student Groups')
            ->assertDontSee('System Commands');
    }

    public function test_overseer_cannot_perform_unapproved_livewire_actions(): void
    {
        $overseer = User::factory()->create(['role' => 'overseer']);

        $this->expectException(HttpException::class);

        Livewire::actingAs($overseer)
            ->test(AdminNavigation::class)
            ->call('save');
    }

    public function test_overseer_cannot_post_to_admin_routes_or_open_owner_tools(): void
    {
        $overseer = User::factory()->create(['role' => 'overseer']);

        $this->actingAs($overseer)
            ->post(route('admin.import', ['role' => 'student']))
            ->assertForbidden();

        $this->actingAs($overseer)
            ->get(route('admin.artisan-commands'))
            ->assertForbidden();
    }

    public function test_admin_accounts_with_an_overseer_role_keep_their_admin_privileges(): void
    {
        $administrator = User::factory()->create(['role' => 'admin']);
        Role::firstOrCreate(['name' => 'overseer'], ['slug' => 'overseer']);
        $administrator->assignRole('overseer');

        $this->assertFalse($administrator->isReadOnlyOverseer());
    }

    public function test_school_context_helpers_use_the_overseers_selected_assigned_school(): void
    {
        $overseer = User::factory()->create(['role' => 'overseer']);
        $assignedSchool = School::factory()->create();
        $unassignedSchool = School::factory()->create();
        $overseer->overseenSchools()->attach($assignedSchool);

        $this->actingAs($overseer);
        session(['current_school_id' => $assignedSchool->id]);

        $this->assertSame($assignedSchool->id, getSchoolId());
        $this->assertTrue(getCurrentSchoolContext()->is($assignedSchool));

        session(['current_school_id' => $unassignedSchool->id]);

        $this->assertNull(getSchoolId());
        $this->assertNull(getCurrentSchoolContext());
    }

    public function test_overseer_user_list_and_details_are_limited_to_the_selected_school(): void
    {
        $overseer = User::factory()->create(['role' => 'overseer']);
        $assignedSchool = School::factory()->create();
        $unassignedSchool = School::factory()->create();
        $overseer->overseenSchools()->attach($assignedSchool);
        $assignedUser = User::factory()->create([
            'name' => 'Assigned School User',
            'school_id' => $assignedSchool->id,
        ]);
        $unassignedUser = User::factory()->create([
            'name' => 'Unassigned School User',
            'school_id' => $unassignedSchool->id,
        ]);

        $this->actingAs($overseer);
        session(['current_school_id' => $assignedSchool->id]);

        $this->get(route('users.index'))
            ->assertOk()
            ->assertSee('Assigned School User')
            ->assertDontSee('Unassigned School User');

        $this->get(route('users.show', $assignedUser))
            ->assertOk();

        $this->get(route('users.show', $unassignedUser))
            ->assertForbidden();
    }

    public function test_owner_can_assign_schools_to_an_overseer(): void
    {
        $owner = User::factory()->create(['role' => 'owner']);
        $overseer = User::factory()->create(['role' => 'overseer']);
        $schools = School::factory()->count(2)->create();

        Livewire::actingAs($owner)
            ->test(OverseerSchoolManagement::class)
            ->set('selectedUserId', $overseer->id)
            ->set('selectedSchoolIds', $schools->modelKeys())
            ->call('save')
            ->assertHasNoErrors();

        $this->assertEqualsCanonicalizing(
            $schools->modelKeys(),
            $overseer->fresh()->overseenSchools()->pluck('schools.id')->all()
        );
    }

    public function test_owner_sees_existing_school_assignments_when_opening_management_page(): void
    {
        $owner = User::factory()->create(['role' => 'owner']);
        $overseer = User::factory()->create(['role' => 'overseer']);
        $school = School::factory()->create(['name' => 'Existing Assigned School']);
        $overseer->overseenSchools()->attach($school);

        Livewire::actingAs($owner)
            ->test(OverseerSchoolManagement::class)
            ->assertSee($overseer->name)
            ->assertSee('Existing Assigned School');
    }

    public function test_school_search_limits_results_and_allows_adding_a_school(): void
    {
        $owner = User::factory()->create(['role' => 'owner']);
        $overseer = User::factory()->create(['role' => 'overseer']);
        $schools = collect(range(1, 11))->map(fn ($number) => School::factory()->create([
            'name' => sprintf('Searchable School %02d', $number),
        ]));

        Livewire::actingAs($owner)
            ->test(OverseerSchoolManagement::class)
            ->call('selectUser', $overseer->id)
            ->set('schoolSearch', 'Searchable School')
            ->assertSee('Searchable School 01')
            ->assertSee('Searchable School 10')
            ->assertDontSee('Searchable School 11')
            ->call('toggleSchool', $schools->first()->id)
            ->assertSet('selectedSchoolIds', [(string) $schools->first()->id]);
    }

    public function test_owner_can_assign_overseer_role_to_an_existing_user_without_removing_roles(): void
    {
        $owner = User::factory()->create(['role' => 'owner']);
        $user = User::factory()->create(['role' => 'teacher']);

        Livewire::actingAs($owner)
            ->test(OverseerSchoolManagement::class)
            ->set('userSearch', $user->email)
            ->assertSee($user->email)
            ->call('selectUser', $user->id)
            ->call('assignOverseerRole')
            ->assertHasNoErrors();

        $this->assertTrue($user->fresh()->isOverseer());
        $this->assertTrue($user->fresh()->hasRole('teacher'));
    }

    public function test_admin_can_assign_overseer_role_to_an_existing_user(): void
    {
        $administrator = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create(['role' => 'teacher']);

        Livewire::actingAs($administrator)
            ->test(OverseerSchoolManagement::class)
            ->set('selectedUserId', $user->id)
            ->call('assignOverseerRole')
            ->assertHasNoErrors();

        $this->assertTrue($user->fresh()->isOverseer());
    }

    public function test_assigning_overseer_role_promotes_a_guest_to_the_overseer_primary_role(): void
    {
        $owner = User::factory()->create(['role' => 'owner']);
        $user = User::factory()->create(['role' => 'guest']);

        Livewire::actingAs($owner)
            ->test(OverseerSchoolManagement::class)
            ->set('selectedUserId', $user->id)
            ->call('assignOverseerRole')
            ->assertHasNoErrors();

        $user = $user->fresh();

        $this->assertSame('overseer', $user->role->value);
        $this->assertSame('overseer', $user->primaryRole->name);
        $this->assertTrue($user->canBeImpersonated());
        $this->assertTrue($user->hasRole('guest'));
        $this->assertTrue($user->isOverseer());
    }

    public function test_non_manager_cannot_open_the_overseer_assignment_page(): void
    {
        $teacher = User::factory()->create(['role' => 'teacher']);

        $this->actingAs($teacher)
            ->get('/admin/overseer-schools')
            ->assertForbidden();
    }
}
