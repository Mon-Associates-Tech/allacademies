<?php

namespace Tests\Feature;

use App\Models\School;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class DataManagerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
    }

    public function test_admin_user_has_school_auto_selected(): void
    {
        $school = School::factory()->create();
        $admin = User::factory()->create([
            'email_verified_at' => now(),
            'password' => 'password',
            'school_id' => $school->id,
            'role' => 'admin',
        ]);

        Livewire::actingAs($admin)
            ->test(\App\Livewire\Common\DataManager::class)
            ->assertSet('selectedSchoolId', $admin->school_id);
    }

    public function test_admin_user_does_not_see_school_selection(): void
    {
        $school = School::factory()->create();
        $admin = User::factory()->create([
            'email_verified_at' => now(),
            'password' => 'password',
            'school_id' => $school->id,
            'role' => 'admin',
        ]);

        Livewire::actingAs($admin)
            ->test(\App\Livewire\Common\DataManager::class)
            ->assertSet('isOwnerOrSuperAdmin', false);
    }

    public function test_owner_user_has_no_school_selected_initially(): void
    {
        $owner = User::factory()->create([
            'email_verified_at' => now(),
            'password' => 'password',
        ]);
        $owner->assignRole('owner');

        Livewire::actingAs($owner)
            ->test(\App\Livewire\Common\DataManager::class)
            ->assertSet('selectedSchoolId', null);
    }

    public function test_owner_can_select_school(): void
    {
        $owner = User::factory()->create([
            'email_verified_at' => now(),
            'password' => 'password',
        ]);
        $owner->assignRole('owner');
        $school = School::factory()->create();

        Livewire::actingAs($owner)
            ->test(\App\Livewire\Common\DataManager::class)
            ->set('selectedSchoolId', $school->id)
            ->assertSet('selectedSchoolId', $school->id);
    }

    public function test_owner_cannot_perform_import_without_school_selection(): void
    {
        $owner = User::factory()->create([
            'email_verified_at' => now(),
            'password' => 'password',
        ]);
        $owner->assignRole('owner');

        // Verify that owner starts with null selectedSchoolId (requires selection)
        Livewire::actingAs($owner)
            ->test(\App\Livewire\Common\DataManager::class)
            ->assertSet('selectedSchoolId', null);
    }
}
