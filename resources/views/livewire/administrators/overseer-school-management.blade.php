<div>
    <x-ui.page-shell
        title="Overseer Management"
        subtitle="Assign existing users the overseer role and manage their school access."
        headerGradient="info"
    >
        @if (session()->has('success'))
            <div class="border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800 dark:border-emerald-800 dark:bg-emerald-900/20 dark:text-emerald-300">
                {{ session('success') }}
            </div>
        @endif

        <x-ui.card class="p-6 space-y-6">
            <section class="space-y-4">
                <div>
                    <h2 class="text-sm font-semibold text-slate-800 dark:text-white">Current overseers</h2>
                    <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Review existing school access or select an overseer to update their assignments.</p>
                </div>

                <div class="space-y-3">
                    @forelse ($overseers as $overseer)
                        <div wire:key="overseer-assignment-{{ $overseer->id }}" class="border border-slate-200 px-4 py-3 dark:border-slate-700">
                            <div class="flex flex-wrap items-center justify-between gap-3">
                                <div>
                                    <p class="text-sm font-medium text-slate-800 dark:text-white">{{ $overseer->name }}</p>
                                    <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">{{ $overseer->email }}</p>
                                </div>
                                @if ($canManageSchoolAssignments)
                                    <x-ui.button wire:click="selectUser({{ $overseer->id }})" variant="secondary">Manage schools</x-ui.button>
                                @else
                                    <x-ui.badge variant="info">Overseer</x-ui.badge>
                                @endif
                            </div>

                            @if ($canManageSchoolAssignments)
                                <div class="mt-3">
                                    <p class="mb-2 text-xs font-medium uppercase tracking-wider text-slate-500 dark:text-slate-400">
                                        Assigned schools · {{ $overseer->overseenSchools->count() }}
                                    </p>
                                    @if ($overseer->overseenSchools->isEmpty())
                                        <p class="text-xs text-slate-500 dark:text-slate-400">No schools assigned.</p>
                                    @else
                                        <div class="max-h-24 space-y-1 overflow-y-auto border-l-2 border-violet-200 pl-3 dark:border-violet-800">
                                            @foreach ($overseer->overseenSchools as $school)
                                                <p class="text-xs text-slate-600 dark:text-slate-300">{{ $school->name }}</p>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            @endif
                        </div>
                    @empty
                        <p class="rounded-sm border border-dashed border-slate-300 px-5 py-6 text-center text-sm text-slate-500 dark:border-slate-700 dark:text-slate-400">
                            No users currently have the overseer role.
                        </p>
                    @endforelse
                </div>

                {{ $overseers->links() }}
            </section>

            <div class="border-t border-slate-200 dark:border-slate-700"></div>

            <div class="max-w-xl space-y-3">
                <label for="user-search" class="mb-2 block text-xs font-medium uppercase tracking-wider text-slate-500 dark:text-slate-400">
                    Find an existing user
                </label>
                <input
                    id="user-search"
                    type="search"
                    wire:model.live.debounce.300ms="userSearch"
                    placeholder="Search by name or email"
                    class="w-full rounded-none border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-800 focus:border-amber-500 focus:outline-none focus:ring-2 focus:ring-amber-500/20 dark:border-slate-700 dark:bg-slate-800 dark:text-white"
                    style="border-radius: var(--radius-sm);"
                >
                @if (strlen(trim($userSearch)) < 2)
                    <p class="text-xs text-slate-500 dark:text-slate-400">Enter at least two characters to search.</p>
                @elseif ($users->isEmpty())
                    <p class="text-xs text-slate-500 dark:text-slate-400">No matching users found.</p>
                @else
                    <div class="divide-y divide-slate-200 border border-slate-200 dark:divide-slate-700 dark:border-slate-700">
                        @foreach ($users as $user)
                            <button
                                type="button"
                                wire:key="user-result-{{ $user->id }}"
                                wire:click="selectUser({{ $user->id }})"
                                class="block w-full px-4 py-3 text-left transition hover:bg-slate-50 dark:hover:bg-slate-800"
                            >
                                <span class="block text-sm font-medium text-slate-800 dark:text-white">{{ $user->name }}</span>
                                <span class="mt-1 block text-xs text-slate-500 dark:text-slate-400">{{ $user->email }}</span>
                            </button>
                        @endforeach
                    </div>
                @endif
            </div>

            @if ($selectedUser)
                <div class="flex items-center justify-between gap-4 border border-slate-200 px-4 py-3 dark:border-slate-700">
                    <div>
                        <p class="text-xs font-medium uppercase tracking-wider text-slate-500 dark:text-slate-400">Selected user</p>
                        <p class="mt-1 text-sm font-semibold text-slate-800 dark:text-white">{{ $selectedUser->name }} · {{ $selectedUser->email }}</p>
                    </div>
                    <x-ui.button wire:click="selectUser(null)" variant="secondary">Change user</x-ui.button>
                </div>

                @if (! $selectedUser->isOverseer())
                    <div class="flex items-center justify-between gap-4 border border-dashed border-slate-300 p-5 dark:border-slate-700">
                        <div>
                            <h2 class="text-sm font-semibold text-slate-800 dark:text-white">Assign the overseer role</h2>
                            <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">This adds overseer access without removing the user's existing roles.</p>
                        </div>
                        <x-ui.button wire:click="assignOverseerRole" icon="check">Assign role</x-ui.button>
                    </div>
                @elseif ($canManageSchoolAssignments)
                    <div class="space-y-4">
                        <div class="flex items-center justify-between gap-4">
                            <div>
                                <h2 class="text-sm font-semibold text-slate-800 dark:text-white">Schools this overseer can access</h2>
                                <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Only selected schools will be available in their school switcher. Search to add or remove schools.</p>
                            </div>
                            <x-ui.badge variant="info">{{ count($selectedSchoolIds) }} selected</x-ui.badge>
                        </div>

                        @error('selectedSchoolIds.*')
                            <p class="text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror

                        <div>
                            <h3 class="mb-2 text-xs font-medium uppercase tracking-wider text-slate-500 dark:text-slate-400">Assigned schools</h3>
                            @if ($selectedSchools->isEmpty())
                                <p class="mb-3 text-sm text-slate-500 dark:text-slate-400">No schools assigned yet.</p>
                            @else
                                <div class="mb-4 max-h-40 space-y-2 overflow-y-auto">
                                    @foreach ($selectedSchools as $school)
                                        <div wire:key="selected-school-{{ $school->id }}" class="flex items-center justify-between gap-3 border border-slate-200 px-3 py-2 dark:border-slate-700">
                                            <span class="min-w-0 truncate text-sm text-slate-800 dark:text-white">{{ $school->name }}</span>
                                            <button type="button" wire:click="toggleSchool({{ $school->id }})" class="shrink-0 text-xs font-medium text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300">
                                                Remove
                                            </button>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>

                        <div class="space-y-3">
                            <label for="school-search" class="block text-xs font-medium uppercase tracking-wider text-slate-500 dark:text-slate-400">
                                Find schools
                            </label>
                            <input
                                id="school-search"
                                type="search"
                                wire:model.live.debounce.300ms="schoolSearch"
                                placeholder="Search by school name or city"
                                class="w-full rounded-none border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-800 focus:border-amber-500 focus:outline-none focus:ring-2 focus:ring-amber-500/20 dark:border-slate-700 dark:bg-slate-800 dark:text-white"
                                style="border-radius: var(--radius-sm);"
                            >
                            @if (strlen(trim($schoolSearch)) < 2)
                                <p class="text-xs text-slate-500 dark:text-slate-400">Enter at least two characters to search.</p>
                            @elseif ($schools->isEmpty())
                                <p class="text-xs text-slate-500 dark:text-slate-400">No matching schools found.</p>
                            @else
                                <div class="max-h-64 divide-y divide-slate-200 overflow-y-auto border border-slate-200 dark:divide-slate-700 dark:border-slate-700">
                                    @foreach ($schools as $school)
                                        @php($isSelectedSchool = in_array((string) $school->id, array_map('strval', $selectedSchoolIds), true))
                                        <button
                                            type="button"
                                            wire:key="school-result-{{ $school->id }}"
                                            wire:click="toggleSchool({{ $school->id }})"
                                            class="flex w-full items-center justify-between gap-3 px-4 py-3 text-left transition hover:bg-slate-50 dark:hover:bg-slate-800"
                                        >
                                            <span class="min-w-0">
                                                <span class="block truncate text-sm font-medium text-slate-800 dark:text-white">{{ $school->name }}</span>
                                                <span class="mt-1 block text-xs text-slate-500 dark:text-slate-400">{{ $school->city ?: 'Location not set' }}</span>
                                            </span>
                                            <span class="shrink-0 text-xs font-medium {{ $isSelectedSchool ? 'text-red-600 dark:text-red-400' : 'text-violet-600 dark:text-violet-400' }}">
                                                {{ $isSelectedSchool ? 'Remove' : 'Add' }}
                                            </span>
                                        </button>
                                    @endforeach
                                </div>
                            @endif
                        </div>

                        <div class="flex justify-end border-t border-slate-200 pt-5 dark:border-slate-700">
                            <x-ui.button wire:click="save" icon="check">Save assignments</x-ui.button>
                        </div>
                    </div>
                @else
                    <div class="rounded-sm border border-slate-200 px-5 py-4 text-sm text-slate-600 dark:border-slate-700 dark:text-slate-300">
                        The overseer role is assigned. School access assignments are managed by an owner or superadmin.
                    </div>
                @endif
            @else
                <div class="rounded-sm border border-dashed border-slate-300 px-5 py-8 text-center text-sm text-slate-500 dark:border-slate-700 dark:text-slate-400">
                    Select a user to assign the overseer role or manage their school access.
                </div>
            @endif
        </x-ui.card>
    </x-ui.page-shell>
</div>