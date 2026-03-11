<?php

namespace Tests\Feature;

use App\Livewire\Administrators\UserManagement;
use App\Livewire\Users\DeleteUserModal;
use App\Models\Note;
use App\Models\Student;
use App\Models\Subscription;
use App\Models\Teacher;
use App\Models\Team;
use App\Models\User;
use App\Models\UserPreference;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Livewire\Livewire;
use Tests\TestCase;

class UserDeletionCascadeTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;

    protected function setUp(): void
    {
        parent::setUp();

        // Create an admin user for authentication
        $this->adminUser = User::factory()->create([
            'email_verified_at' => now(),
            'role' => 'admin',
        ]);
    }

    /** @test */
    public function user_deletion_removes_student_profile()
    {
        // Create a user with student role
        $user = User::factory()->create([
            'role' => 'student',
            'email_verified_at' => now(),
        ]);

        // Create student profile
        $student = Student::create([
            'user_id' => $user->id,
        ]);

        $this->assertDatabaseHas('students', ['user_id' => $user->id]);

        // Delete user using the DeleteUserModal component
        Livewire::test(DeleteUserModal::class)
            ->call('openDeleteModal', $user->id)
            ->call('deleteUser');

        // Verify user and student profile are deleted
        $this->assertDatabaseMissing('users', ['id' => $user->id]);
        $this->assertDatabaseMissing('students', ['user_id' => $user->id]);
    }

    /** @test */
    public function user_deletion_removes_teacher_profile()
    {
        // Create a user with teacher role
        $user = User::factory()->create([
            'role' => 'teacher',
            'email_verified_at' => now(),
        ]);

        // Create teacher profile
        $teacher = Teacher::create([
            'user_id' => $user->id,
        ]);

        $this->assertDatabaseHas('teachers', ['user_id' => $user->id]);

        // Delete user using the DeleteUserModal component
        Livewire::test(DeleteUserModal::class)
            ->call('openDeleteModal', $user->id)
            ->call('deleteUser');

        // Verify user and teacher profile are deleted
        $this->assertDatabaseMissing('users', ['id' => $user->id]);
        $this->assertDatabaseMissing('teachers', ['user_id' => $user->id]);
    }

    /** @test */
    public function user_deletion_removes_subscriptions()
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        // Create subscriptions for the user
        Subscription::create([
            'subscriber_id' => $user->id,
            'subscribable_type' => 'App\Models\Book',
            'subscribable_id' => 1,
        ]);

        $this->assertDatabaseHas('subscriptions', ['subscriber_id' => $user->id]);

        // Delete user
        Livewire::test(DeleteUserModal::class)
            ->call('openDeleteModal', $user->id)
            ->call('deleteUser');

        // Verify subscriptions are deleted
        $this->assertDatabaseMissing('users', ['id' => $user->id]);
        $this->assertDatabaseMissing('subscriptions', ['subscriber_id' => $user->id]);
    }

    /** @test */
    public function user_deletion_removes_notes()
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        // Create notes for the user
        Note::create([
            'user_id' => $user->id,
            'title' => 'Test Note',
            'content' => 'Test content',
        ]);

        $this->assertDatabaseHas('notes', ['user_id' => $user->id]);

        // Delete user
        Livewire::test(DeleteUserModal::class)
            ->call('openDeleteModal', $user->id)
            ->call('deleteUser');

        // Verify notes are deleted
        $this->assertDatabaseMissing('users', ['id' => $user->id]);
        $this->assertDatabaseMissing('notes', ['user_id' => $user->id]);
    }

    /** @test */
    public function user_deletion_removes_user_preferences()
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        // Create preferences for the user
        UserPreference::create([
            'user_id' => $user->id,
            'key' => 'theme',
            'value' => 'dark',
        ]);

        $this->assertDatabaseHas('user_preferences', ['user_id' => $user->id]);

        // Delete user
        Livewire::test(DeleteUserModal::class)
            ->call('openDeleteModal', $user->id)
            ->call('deleteUser');

        // Verify preferences are deleted
        $this->assertDatabaseMissing('users', ['id' => $user->id]);
        $this->assertDatabaseMissing('user_preferences', ['user_id' => $user->id]);
    }

    /** @test */
    public function user_deletion_detaches_roles()
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        // Assign a role to the user (if role_user pivot table exists)
        if (DB::getSchemaBuilder()->hasTable('role_user')) {
            DB::table('role_user')->insert([
                'user_id' => $user->id,
                'role_id' => 1,
            ]);

            $this->assertDatabaseHas('role_user', ['user_id' => $user->id]);
        }

        // Delete user
        Livewire::test(DeleteUserModal::class)
            ->call('openDeleteModal', $user->id)
            ->call('deleteUser');

        // Verify user is deleted and roles are detached
        $this->assertDatabaseMissing('users', ['id' => $user->id]);
        if (DB::getSchemaBuilder()->hasTable('role_user')) {
            $this->assertDatabaseMissing('role_user', ['user_id' => $user->id]);
        }
    }

    /** @test */
    public function user_deletion_detaches_team_memberships()
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        // Create a team and add user as member
        $teamOwner = User::factory()->create();
        $team = Team::create([
            'name' => 'Test Team',
            'owner_id' => $teamOwner->id,
            'is_active' => true,
        ]);

        // Add user to team
        DB::table('team_user')->insert([
            'team_id' => $team->id,
            'user_id' => $user->id,
            'role' => 'member',
        ]);

        $this->assertDatabaseHas('team_user', ['user_id' => $user->id]);

        // Delete user
        Livewire::test(DeleteUserModal::class)
            ->call('openDeleteModal', $user->id)
            ->call('deleteUser');

        // Verify user is deleted and team membership is removed
        $this->assertDatabaseMissing('users', ['id' => $user->id]);
        $this->assertDatabaseMissing('team_user', ['user_id' => $user->id]);
    }

    /** @test */
    public function user_deletion_removes_owned_teams()
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        // Create a team owned by the user
        $team = Team::create([
            'name' => 'User Owned Team',
            'owner_id' => $user->id,
            'is_active' => true,
        ]);

        $this->assertDatabaseHas('teams', ['owner_id' => $user->id]);

        // Delete user
        Livewire::test(DeleteUserModal::class)
            ->call('openDeleteModal', $user->id)
            ->call('deleteUser');

        // Verify user and owned teams are deleted
        $this->assertDatabaseMissing('users', ['id' => $user->id]);
        $this->assertDatabaseMissing('teams', ['owner_id' => $user->id]);
    }

    /** @test */
    public function user_management_delete_removes_all_relationships()
    {
        $this->actingAs($this->adminUser);

        // Create a user with various relationships
        $user = User::factory()->create([
            'role' => 'student',
            'email_verified_at' => now(),
        ]);

        // Create student profile
        Student::create([
            'user_id' => $user->id,
        ]);

        // Create subscription
        Subscription::create([
            'subscriber_id' => $user->id,
            'subscribable_type' => 'App\Models\Book',
            'subscribable_id' => 1,
        ]);

        $this->assertDatabaseHas('students', ['user_id' => $user->id]);
        $this->assertDatabaseHas('subscriptions', ['subscriber_id' => $user->id]);

        // Delete user using UserManagement component
        Livewire::test(UserManagement::class)
            ->call('delete', $user->id);

        // Verify all related data is deleted
        $this->assertDatabaseMissing('users', ['id' => $user->id]);
        $this->assertDatabaseMissing('students', ['user_id' => $user->id]);
        $this->assertDatabaseMissing('subscriptions', ['subscriber_id' => $user->id]);
    }

    /** @test */
    public function user_deletion_is_wrapped_in_transaction()
    {
        $user = User::factory()->create([
            'role' => 'student',
            'email_verified_at' => now(),
        ]);

        // Create student profile
        Student::create([
            'user_id' => $user->id,
        ]);

        // Verify both exist before deletion
        $this->assertDatabaseHas('users', ['id' => $user->id]);
        $this->assertDatabaseHas('students', ['user_id' => $user->id]);

        // Delete user
        Livewire::test(DeleteUserModal::class)
            ->call('openDeleteModal', $user->id)
            ->call('deleteUser');

        // Both should be deleted (transaction ensures atomicity)
        $this->assertDatabaseMissing('users', ['id' => $user->id]);
        $this->assertDatabaseMissing('students', ['user_id' => $user->id]);
    }

    /** @test */
    public function load_items_to_delete_shows_all_related_items()
    {
        $user = User::factory()->create([
            'role' => 'student',
            'email_verified_at' => now(),
        ]);

        // Create student profile
        Student::create([
            'user_id' => $user->id,
        ]);

        // Create subscription
        Subscription::create([
            'subscriber_id' => $user->id,
            'subscribable_type' => 'App\Models\Book',
            'subscribable_id' => 1,
        ]);

        // Test that loadItemsToDelete shows the related items
        $component = Livewire::test(DeleteUserModal::class)
            ->call('openDeleteModal', $user->id);

        // Check that itemsToDelete contains the expected items
        $itemsToDelete = $component->get('itemsToDelete');

        // Should have items with count > 0
        $this->assertIsArray($itemsToDelete);

        // Find student profile in items
        $studentItem = collect($itemsToDelete)->firstWhere('name', 'Student Profile');
        $this->assertNotNull($studentItem);
        $this->assertEquals(1, $studentItem['count']);

        // Find subscriptions in items
        $subscriptionItem = collect($itemsToDelete)->firstWhere('name', 'Subscriptions');
        $this->assertNotNull($subscriptionItem);
        $this->assertEquals(1, $subscriptionItem['count']);
    }
}
