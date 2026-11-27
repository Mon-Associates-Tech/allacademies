<?php

namespace App\Livewire\Administrators;

use App\Enums\UserRole;
use App\Models\Role;
use App\Models\School;
use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;

class OverseerSchoolManagement extends Component
{
    use WithPagination;

    public $selectedUserId = '';

    public string $userSearch = '';

    public string $schoolSearch = '';

    public array $selectedSchoolIds = [];

    protected string $paginationTheme = 'tailwind';

    public function boot(): void
    {
        $this->authorizeManagement();
    }

    public function selectUser(?int $userId): void
    {
        $this->authorizeManagement();

        if (! $userId) {
            $this->selectedUserId = '';
            $this->userSearch = '';
            $this->schoolSearch = '';
            $this->selectedSchoolIds = [];

            return;
        }

        $selectedUser = User::findOrFail($userId);
        $this->selectedUserId = (string) $selectedUser->id;
        $this->userSearch = '';
        $this->schoolSearch = '';
        $this->selectedSchoolIds = $selectedUser->isOverseer()
            ? $selectedUser->overseenSchools()
                ->pluck('schools.id')
                ->map(fn ($schoolId) => (string) $schoolId)
                ->all()
            : [];
    }

    public function assignOverseerRole(): void
    {
        $this->authorizeManagement();

        $validated = $this->validate([
            'selectedUserId' => ['required', 'integer', 'exists:users,id'],
        ]);

        $user = User::findOrFail($validated['selectedUserId']);
        $role = Role::firstOrCreate(
            ['name' => UserRole::OVERSEER->value],
            ['slug' => UserRole::OVERSEER->value, 'description' => 'Can oversee multiple schools']
        );

        $currentRole = $user->role instanceof UserRole ? $user->role->value : $user->role;

        if (! $user->hasAnyRole([UserRole::OWNER->value, UserRole::ADMIN->value, UserRole::SUPER_ADMIN->value])) {
            if ($currentRole && $currentRole !== UserRole::OVERSEER->value) {
                $previousRole = Role::firstOrCreate(
                    ['name' => $currentRole],
                    ['slug' => $currentRole]
                );
                $user->assignRole($previousRole->name);
            }

            $user->role = UserRole::OVERSEER;
            $user->role_id = $role->id;
            $user->save();
        }

        $user->assignRole($role->name);
        $this->selectedSchoolIds = $user->overseenSchools()
            ->pluck('schools.id')
            ->map(fn ($schoolId) => (string) $schoolId)
            ->all();

        session()->flash('success', "Overseer role assigned to {$user->name}.");
    }

    public function save(): void
    {
        $this->authorizeSchoolAssignments();

        $validated = $this->validate([
            'selectedUserId' => ['required', 'integer', 'exists:users,id'],
            'selectedSchoolIds' => ['array'],
            'selectedSchoolIds.*' => ['integer', 'distinct', 'exists:schools,id'],
        ]);

        $overseer = $this->findOverseer($validated['selectedUserId']);
        $overseer->overseenSchools()->sync(array_map('intval', $validated['selectedSchoolIds']));

        session()->flash('success', "School assignments updated for {$overseer->name}.");
    }

    public function toggleSchool(int $schoolId): void
    {
        $this->authorizeSchoolAssignments();

        $schoolId = (string) $schoolId;

        if (in_array($schoolId, $this->selectedSchoolIds, true)) {
            $this->selectedSchoolIds = array_values(array_filter(
                $this->selectedSchoolIds,
                fn ($selectedSchoolId) => (string) $selectedSchoolId !== $schoolId
            ));

            return;
        }

        $this->selectedSchoolIds[] = $schoolId;
    }

    public function render()
    {
        $user = auth()->user();
        $canManageSchoolAssignments = $user->isOwner() || $user->isSuperAdmin();
        $search = trim($this->userSearch);
        $schoolSearch = trim($this->schoolSearch);
        $users = strlen($search) >= 2
            ? User::query()
                ->where(function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                })
                ->orderBy('name')
                ->limit(10)
                ->get()
            : collect();
        $schools = $canManageSchoolAssignments && strlen($schoolSearch) >= 2
            ? School::query()
                ->where(function ($query) use ($schoolSearch) {
                    $query->where('name', 'like', "%{$schoolSearch}%")
                        ->orWhere('city', 'like', "%{$schoolSearch}%");
                })
                ->orderBy('name')
                ->limit(10)
                ->get()
            : collect();
        $selectedSchools = $canManageSchoolAssignments && $this->selectedSchoolIds
            ? School::query()->whereIn('id', $this->selectedSchoolIds)->orderBy('name')->get()
            : collect();
        $overseers = User::query()
            ->where(function ($query) {
                $query->where('role', UserRole::OVERSEER->value)
                    ->orWhereHas('roles', fn ($roles) => $roles->where('name', UserRole::OVERSEER->value))
                    ->orWhereHas('primaryRole', fn ($role) => $role->where('name', UserRole::OVERSEER->value));
            })
            ->with('overseenSchools')
            ->orderBy('name')
            ->paginate(10, ['*'], 'overseersPage');

        return view('livewire.administrators.overseer-school-management', [
            'users' => $users,
            'selectedUser' => $this->selectedUserId ? User::find($this->selectedUserId) : null,
            'overseers' => $overseers,
            'canManageSchoolAssignments' => $canManageSchoolAssignments,
            'schools' => $schools,
            'selectedSchools' => $selectedSchools,
        ]);
    }

    private function findOverseer($userId): User
    {
        $user = User::findOrFail($userId);

        abort_unless($user->isOverseer(), 404);

        return $user;
    }

    private function authorizeManagement(): void
    {
        $user = auth()->user();

        abort_unless($user && ($user->isOwner() || $user->isSuperAdmin() || $user->hasRole(UserRole::ADMIN->value)), 403);
    }

    private function authorizeSchoolAssignments(): void
    {
        $user = auth()->user();

        abort_unless($user && ($user->isOwner() || $user->isSuperAdmin()), 403);
    }
}
