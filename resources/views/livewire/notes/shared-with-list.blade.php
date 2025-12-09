<div class="mt-8 pt-8 border-t border-gray-200 dark:border-gray-700">
    {{-- Header with Count --}}
    <div class="flex items-center justify-between mb-4">
        <h4 class="text-sm font-semibold text-gray-900 dark:text-white flex items-center gap-2">
            <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
            </svg>
            <span>Shared With</span>
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300">
                {{ $this->uniqueRecipientsCount }} {{ \Str::plural('recipient', $this->uniqueRecipientsCount) }}
            </span>
        </h4>

        {{-- Filter Toggle --}}
        <button x-data="{ filtersOpen: @entangle('filtersOpen') }"
                x-on:click="$wire.set('filtersOpen', !$wire.get('filtersOpen'))"
                class="inline-flex items-center px-3 py-1.5 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
            </svg>
            Filters
            @if($this->activeFiltersCount > 0)
                <span class="ml-1.5 inline-flex items-center px-1.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300">
            {{ $this->activeFiltersCount }}
        </span>
            @endif
        </button>
    </div>


    {{-- Filters Section --}}
    <div x-data="{ filtersOpen: @entangle('filtersOpen') }"
         x-show="filtersOpen"
         x-collapse
         class="mb-4 p-4 bg-gray-50 dark:bg-gray-900/50 rounded-xl border border-gray-200 dark:border-gray-700">

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            {{-- Search --}}
            <div class="sm:col-span-3">
                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                    Search
                </label>
                <div class="relative">
                    <input type="text"
                           wire:model.live.debounce.300ms="searchTerm"
                           placeholder="Search by name or email..."
                           class="w-full pl-9 pr-4 py-2 text-sm border-gray-300 dark:border-gray-600 rounded-lg focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-800 dark:text-gray-300">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                </div>
            </div>

            {{-- Share Type Filter --}}
            <div>
                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                    Share Type
                </label>
                <select wire:model.live="filterShareType"
                        class="w-full text-sm border-gray-300 dark:border-gray-600 rounded-lg focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-800 dark:text-gray-300">
                    <option value="all">All Types</option>
                    <option value="individual">Individual Users</option>
                    <option value="academic_group">Academic Groups</option>
                    <option value="academic_level">Academic Levels</option>
                    <option value="student_group">Student Groups</option>
                    <option value="school_wide">School Wide</option>
                </select>
            </div>

            {{-- Role Filter --}}
            <div>
                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                    User Role
                </label>
                <select wire:model.live="filterRole"
                        class="w-full text-sm border-gray-300 dark:border-gray-600 rounded-lg focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-800 dark:text-gray-300">
                    <option value="all">All Roles</option>
                    <option value="student">Students</option>
                    <option value="teacher">Teachers</option>
                    <option value="admin">Admins</option>
                    <option value="parent">Parents</option>
                    <option value="librarian">Librarians</option>
                </select>
            </div>

            {{-- Per Page --}}
            <div>
                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                    Per Page
                </label>
                <select wire:model.live="perPage"
                        class="w-full text-sm border-gray-300 dark:border-gray-600 rounded-lg focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-800 dark:text-gray-300">
                    <option value="5">5</option>
                    <option value="10">10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                </select>
            </div>

            {{-- Academic Group Filter --}}
            <div>
                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                    Academic Group
                </label>
                <select wire:model.live="filterAcademicGroup"
                        class="w-full text-sm border-gray-300 dark:border-gray-600 rounded-lg focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-800 dark:text-gray-300">
                    <option value="">All Groups</option>
                    @foreach($this->academicGroups as $group)
                        <option value="{{ $group->id }}">{{ $group->name }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Academic Level Filter --}}
            <div>
                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                    Academic Level
                </label>
                <select wire:model.live="filterAcademicLevel"
                        class="w-full text-sm border-gray-300 dark:border-gray-600 rounded-lg focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-800 dark:text-gray-300">
                    <option value="">All Levels</option>
                    @foreach($this->academicLevels as $level)
                        <option value="{{ $level->id }}">{{ $level->name }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Student Group Filter --}}
            <div>
                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                    Student Group
                </label>
                <select wire:model.live="filterStudentGroup"
                        class="w-full text-sm border-gray-300 dark:border-gray-600 rounded-lg focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-800 dark:text-gray-300">
                    <option value="">All Student Groups</option>
                    @foreach($this->studentGroups as $group)
                        <option value="{{ $group->id }}">{{ $group->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        {{-- Clear Filters Button --}}
        @if($this->activeFiltersCount > 0)
            <div class="mt-4 flex justify-end">
                <button wire:click="clearFilters"
                        class="inline-flex items-center px-3 py-1.5 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    Clear Filters
                </button>
            </div>
        @endif
    </div>

    @if($shares->count() > 0)
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
            <div class="divide-y divide-gray-100 dark:divide-gray-700">
                @foreach($shares as $index => $share)
                    <div class="p-4 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                        <div class="flex items-start gap-4">
                            {{-- Index Number --}}
                            <div class="flex-shrink-0 w-8 h-8 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center">
                                <span class="text-sm font-semibold text-gray-600 dark:text-gray-400">
                                    {{ $shares->firstItem() + $index }}
                                </span>
                            </div>

                            @if($share->share_type === 'individual' && $share->sharedWithUser)
                                {{-- Individual User --}}
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-start gap-3">
                                        <x-avatar class="h-12 w-12 flex-shrink-0"
                                                  :name="$share->sharedWithUser->name"
                                                  :avatar="$share->sharedWithUser->avatar" />

                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center gap-2 flex-wrap">
                                                <h5 class="text-sm font-semibold text-gray-900 dark:text-white truncate">
                                                    {{ $share->sharedWithUser->name }}
                                                </h5>

                                                {{-- Role Badge --}}
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium
                                                    {{ $share->sharedWithUser->role->value === 'student' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300' : '' }}
                                                    {{ $share->sharedWithUser->role->value === 'teacher' ? 'bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-300' : '' }}
                                                    {{ $share->sharedWithUser->role->value === 'admin' ? 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300' : '' }}
                                                    {{ !in_array($share->sharedWithUser->role->value, ['student', 'teacher', 'admin']) ? 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300' : '' }}">
                                                    {{ ucfirst($share->sharedWithUser->role->value) }}
                                                </span>

                                                {{-- Permission Badge --}}
                                                @if($share->can_edit)
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300">
                                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                                        </svg>
                                                        Can Edit
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300">
                                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                        </svg>
                                                        View Only
                                                    </span>
                                                @endif
                                            </div>

                                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                                {{ $share->sharedWithUser->email }}
                                            </p>

                                            <div class="flex items-center gap-4 mt-2 text-xs text-gray-500 dark:text-gray-400">
                                                <span class="flex items-center gap-1">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                    </svg>
                                                    Shared {{ $share->created_at->diffForHumans() }}
                                                </span>

                                                @if($share->notification_sent)
                                                    <span class="flex items-center gap-1 text-green-600 dark:text-green-400">
                                                        <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                                                            <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z" />
                                                            <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z" />
                                                        </svg>
                                                        Email sent
                                                    </span>
                                                @endif
                                            </div>
                                        </div>

                                        {{-- Remove Button --}}
                                        <button type="button"
                                                wire:click="removeShare({{ $share->id }}, 'individual', {{ $share->sharedWithUser->id }})"
                                                wire:confirm="Are you sure you want to remove access for {{ $share->sharedWithUser->name }}?"
                                                class="flex-shrink-0 p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors"
                                                title="Remove access">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            @else
                                {{-- Group Share --}}
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-start gap-3">
                                        <div class="h-12 w-12 rounded-lg bg-gradient-to-br from-purple-500 to-indigo-600 flex items-center justify-center text-white flex-shrink-0">
                                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z" />
                                            </svg>
                                        </div>

                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center gap-2 flex-wrap">
                                                <h5 class="text-sm font-semibold text-gray-900 dark:text-white">
                                                    {{ $share->shareable?->name ?? 'Unknown Group' }}
                                                </h5>

                                                {{-- Share Type Badge --}}
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-300">
                                                    {{ ucfirst(str_replace('_', ' ', $share->share_type)) }}
                                                </span>

                                                {{-- Permission Badge --}}
                                                @if($share->can_edit)
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300">
                                                        Can Edit
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300">
                                                        View Only
                                                    </span>
                                                @endif
                                            </div>

                                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                                Group share - All members have access
                                            </p>

                                            <div class="flex items-center gap-4 mt-2 text-xs text-gray-500 dark:text-gray-400">
                                                <span class="flex items-center gap-1">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                    </svg>
                                                    Shared {{ $share->created_at->diffForHumans() }}
                                                </span>
                                            </div>
                                        </div>

                                        {{-- Remove Button --}}
                                        <button type="button"
                                                wire:click="removeShare({{ $share->id }}, '{{ $share->share_type }}', {{ $share->shareable_id }})"
                                                wire:confirm="Are you sure you want to remove this group share?"
                                                class="flex-shrink-0 p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors"
                                                title="Remove share">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Pagination --}}
        <div class="mt-4">
            {{ $shares->links() }}
        </div>
    @else
        <div class="text-center py-12 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">
                @if($this->activeFiltersCount > 0)
                    No shares match your filters
                @else
                    No shares yet
                @endif
            </h3>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                @if($this->activeFiltersCount > 0)
                    Try adjusting your filters to see more results.
                @else
                    Get started by sharing this note above.
                @endif
            </p>
            @if($this->activeFiltersCount > 0)
                <button wire:click="clearFilters"
                        class="mt-3 inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                    Clear Filters
                </button>
            @endif
        </div>
    @endif
</div>
