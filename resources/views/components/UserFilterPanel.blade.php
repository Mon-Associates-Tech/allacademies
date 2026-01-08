@props(['academicGroups', 'academicLevels', 'subjects'])

<div x-data="{
    filtersOpen: false,
    activeFilters: {{ request()->hasAny(['role', 'gender', 'academic_group', 'academic_level', 'subject', 'verified', 'unverified', 'online', 'status']) ? 'true' : 'false' }}
}">
    <!-- Filter Toggle Button -->
    <button @click="filtersOpen = !filtersOpen"
            type="button"
            class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium rounded-lg border transition-colors {{ request()->hasAny(['role', 'gender', 'academic_group', 'academic_level', 'subject', 'status']) ? 'bg-indigo-50 dark:bg-indigo-900/20 border-indigo-200 dark:border-indigo-800 text-indigo-700 dark:text-indigo-300' : 'bg-white dark:bg-gray-800 border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700' }}">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
        </svg>
        <span>Advanced Filters</span>
        @if(request()->hasAny(['role', 'gender', 'academic_group', 'academic_level', 'subject', 'status']))
            <span class="inline-flex items-center justify-center w-5 h-5 text-xs font-bold text-white bg-indigo-600 rounded-full">
                {{ collect(request()->only(['role', 'gender', 'academic_group', 'academic_level', 'subject', 'status']))->filter()->count() }}
            </span>
        @endif
        <svg class="w-4 h-4 transition-transform" :class="filtersOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
        </svg>
    </button>

    <!-- Filters Panel -->
    <div x-show="filtersOpen"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 -translate-y-2"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 -translate-y-2"
         style="display: none;"
         class="absolute right-0 top-full mt-2 w-96 bg-white dark:bg-gray-800 rounded-lg shadow-xl border border-gray-200 dark:border-gray-700 z-50">

        <form method="GET" action="{{ route('users.index') }}">
            <!-- Preserve search term -->
            @if(request('search'))
                <input type="hidden" name="search" value="{{ request('search') }}">
            @endif

            <!-- Header -->
            <div class="p-4 pb-3 border-b border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Filter Users</h3>
                    @if(request()->hasAny(['role', 'gender', 'academic_group', 'academic_level', 'subject', 'status', 'verified', 'unverified', 'online']))
                        <a href="{{ route('users.index') }}"
                           class="text-xs text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 dark:hover:text-indigo-300">
                            Clear All
                        </a>
                    @endif
                </div>
            </div>

            <!-- Scrollable Content with fixed height -->
            <div class="p-4 space-y-4 overflow-y-auto thin-scrollbar" style="max-height: 450px;">
                <!-- Role Filter -->
                <div class="space-y-2">
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300">Role</label>
                    <select name="role"
                            class="w-full text-sm rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">All Roles</option>
                        <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                        <option value="teacher" {{ request('role') === 'teacher' ? 'selected' : '' }}>Teacher</option>
                        <option value="student" {{ request('role') === 'student' ? 'selected' : '' }}>Student</option>
                        <option value="librarian" {{ request('role') === 'librarian' ? 'selected' : '' }}>Librarian</option>
                        <option value="moderator" {{ request('role') === 'moderator' ? 'selected' : '' }}>Moderator</option>
                        <option value="owner" {{ request('role') === 'owner' ? 'selected' : '' }}>Owner</option>
                        <option value="author" {{ request('role') === 'author' ? 'selected' : '' }}>Author</option>
                        <option value="subscriber" {{ request('role') === 'subscriber' ? 'selected' : '' }}>Subscriber</option>
                        <option value="parent" {{ request('role') === 'parent' ? 'selected' : '' }}>Parent</option>
                    </select>
                </div>

                <!-- Gender Filter -->
                <div class="space-y-2">
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300">Gender</label>
                    <select name="gender"
                            class="w-full text-sm rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">All Genders</option>
                        <option value="male" {{ request('gender') === 'male' ? 'selected' : '' }}>Male</option>
                        <option value="female" {{ request('gender') === 'female' ? 'selected' : '' }}>Female</option>
                        <option value="other" {{ request('gender') === 'other' ? 'selected' : '' }}>Other</option>
                    </select>
                </div>

                <!-- Academic Group Filter -->
                <div class="space-y-2">
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300">Academic Group</label>
                    <select name="academic_group"
                            class="w-full text-sm rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">All Groups</option>
                        @foreach($academicGroups as $group)
                            <option value="{{ $group->id }}" {{ request('academic_group') == $group->id ? 'selected' : '' }}>
                                {{ $group->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Academic Level Filter -->
                <div class="space-y-2">
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300">Academic Level</label>
                    <select name="academic_level"
                            class="w-full text-sm rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">All Levels</option>
                        @foreach($academicLevels as $level)
                            <option value="{{ $level->id }}" {{ request('academic_level') == $level->id ? 'selected' : '' }}>
                                {{ $level->name }} @if($level->academicGroup) ({{ $level->academicGroup->name }}) @endif
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Subject Filter (For Teachers) -->
                <div class="space-y-2">
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300">Assigned Subject</label>
                    <select name="subject"
                            class="w-full text-sm rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">All Subjects</option>
                        @foreach($subjects as $subject)
                            <option value="{{ $subject->id }}" {{ request('subject') == $subject->id ? 'selected' : '' }}>
                                {{ $subject->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Status Checkboxes -->
                <div class="space-y-2">
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300">Status</label>
                    <div class="space-y-2">
                        <label class="flex items-center space-x-2">
                            <input type="checkbox" name="verified" value="1" {{ request('verified') ? 'checked' : '' }}
                            class="rounded border-gray-300 dark:border-gray-600 text-indigo-600 focus:ring-indigo-500">
                            <span class="text-sm text-gray-700 dark:text-gray-300">Email Verified</span>
                        </label>
                        <label class="flex items-center space-x-2">
                            <input type="checkbox" name="online" value="1" {{ request('online') ? 'checked' : '' }}
                            class="rounded border-gray-300 dark:border-gray-600 text-indigo-600 focus:ring-indigo-500">
                            <span class="text-sm text-gray-700 dark:text-gray-300">Currently Online</span>
                        </label>
                    </div>
                </div>

                <!-- Account Status -->
                <div class="space-y-2">
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300">Account Status</label>
                    <select name="status"
                            class="w-full text-sm rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">All Status</option>
                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>

                <div class="p-4 bg-gray-50 dark:bg-gray-900 border-t border-gray-200 dark:border-gray-700 rounded-b-lg">
                    <div class="flex items-center gap-2">
                        <button type="submit"
                                class="flex-1 px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                            Apply Filters
                        </button>
                        <a href="{{ route('users.index') }}"
                           class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors">
                            Reset
                        </a>
                    </div>
                </div>
            </div>

            <!-- Sticky Footer with Buttons -->

        </form>
    </div>
</div>
