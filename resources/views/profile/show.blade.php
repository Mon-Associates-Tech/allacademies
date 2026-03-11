<x-layouts.app title="Profile" title-align-center>
    <!-- Header Section with Cover Background -->
    <div class="relative rounded-2xl mb-8 overflow-hidden shadow-xl dark:shadow-gray-900/50">
        <!-- Cover Image or Gradient -->
        @if($user->cover_image_url)
            <div class="absolute inset-0">
                <img src="{{ $user->cover_image_url }}" alt="Cover" class="w-full h-full object-cover">
                <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/30 to-transparent"></div>
            </div>
        @else
            <div class="absolute inset-0 bg-gradient-to-br from-indigo-600 via-purple-600 to-pink-500 dark:from-indigo-800 dark:via-purple-800 dark:to-pink-700"></div>
            <div class="absolute inset-0 bg-black/20"></div>
        @endif

        <div class="relative px-6 py-8 sm:py-10">
            <div class="flex flex-col items-center text-center">
                <!-- Avatar -->
                <div class="relative mb-4">
                    @if($user->profile_avatar_url)
                        <img class="w-20 h-20 sm:w-24 sm:h-24 object-cover rounded-full shadow-2xl border-4 border-white dark:border-gray-200 ring-4 ring-white/30"
                             src="{{ $user->profile_avatar_url }}"
                             alt="{{ $user->name }}">
                    @else
                        <div class="w-20 h-20 sm:w-24 sm:h-24 rounded-full bg-white/20 backdrop-blur-md flex items-center justify-center shadow-2xl border-4 border-white dark:border-gray-200 ring-4 ring-white/30">
                            <span class="text-white font-bold text-2xl sm:text-3xl">{{ strtoupper(substr($user->name, 0, 2)) }}</span>
                        </div>
                    @endif
                    <div class="absolute bottom-0 right-0 w-6 h-6 bg-green-500 border-2 border-white rounded-full flex items-center justify-center shadow-lg">
                        <div class="w-2.5 h-2.5 bg-green-400 rounded-full animate-pulse"></div>
                    </div>
                </div>

                <!-- User Info -->
                <h1 class="text-3xl sm:text-4xl font-bold text-white mb-2 drop-shadow-lg">{{ $user->name }}</h1>
                <p class="text-white/80 text-lg mb-4">{{ $user->email }}</p>

                <!-- Role & Academic Level Badges -->
                <div class="flex flex-wrap justify-center gap-3 mb-6">
                    <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium bg-white/20 backdrop-blur-md text-white border border-white/30 shadow-lg">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        {{ ucfirst($user->role->value ?? 'Student') }}
                    </span>
                    @if($user->preferredAcademicLevel)
                        <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium bg-white/20 backdrop-blur-md text-white border border-white/30 shadow-lg">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                            </svg>
                            {{ $user->preferredAcademicLevel->name }}
                        </span>
                    @endif
                </div>

                <!-- Action Button -->
                <x-link.primary :to="route('profile.edit')" class="bg-white text-indigo-600 hover:bg-gray-100 shadow-xl hover:shadow-2xl transition-all duration-200">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    Edit Profile
                </x-link.primary>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6 mb-8">
        <!-- Member Since -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-5 hover:shadow-md transition-shadow duration-200">
            <div class="flex items-center">
                <div class="p-3 rounded-xl bg-blue-100 dark:bg-blue-900/30">
                    <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Member Since</p>
                    <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ $user->created_at->format('M Y') }}</p>
                </div>
            </div>
        </div>

        <!-- Subscription Status -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-5 hover:shadow-md transition-shadow duration-200">
            <div class="flex items-center">
                <div class="p-3 rounded-xl bg-green-100 dark:bg-green-900/30">
                    <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Subscription</p>
                    <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ $user->activeSubscriptionCycle->package->name ?? 'Basic' }}</p>
                </div>
            </div>
        </div>

        <!-- Total Days Active -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-5 hover:shadow-md transition-shadow duration-200">
            <div class="flex items-center">
                <div class="p-3 rounded-xl bg-purple-100 dark:bg-purple-900/30">
                    <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Days Active</p>
                    <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ floor($user->created_at->diffInDays(now())) }} days</p>
                </div>
            </div>
        </div>

        <!-- Academic Level -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-5 hover:shadow-md transition-shadow duration-200">
            <div class="flex items-center">
                <div class="p-3 rounded-xl bg-amber-100 dark:bg-amber-900/30">
                    <svg class="w-6 h-6 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Academic Level</p>
                    <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ $user->preferredAcademicLevel->name ?? 'Not Set' }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6 lg:gap-8">
        <!-- Left Column - Profile Details -->
        <div class="xl:col-span-2 space-y-6">
            <!-- Personal Information -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                        <svg class="w-5 h-5 mr-2 text-indigo-500 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        Personal Information
                    </h3>
                </div>
                <div class="p-6">
                    <dl class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                        <div class="p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">First Name</dt>
                            <dd class="mt-1 text-base font-medium text-gray-900 dark:text-white">{{ $user->first_name ?: 'Not provided' }}</dd>
                        </div>
                        <div class="p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Last Name</dt>
                            <dd class="mt-1 text-base font-medium text-gray-900 dark:text-white">{{ $user->last_name ?: 'Not provided' }}</dd>
                        </div>
                        <div class="p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Other Names</dt>
                            <dd class="mt-1 text-base font-medium text-gray-900 dark:text-white">{{ $user->other_names ?: 'Not provided' }}</dd>
                        </div>
                        <div class="p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Email Address</dt>
                            <dd class="mt-1 text-base font-medium text-gray-900 dark:text-white">{{ $user->email }}</dd>
                        </div>
                        <div class="p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Phone</dt>
                            <dd class="mt-1 text-base font-medium text-gray-900 dark:text-white">{{ $user->phone ?: 'Not provided' }}</dd>
                        </div>
                        <div class="p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Gender</dt>
                            <dd class="mt-1 text-base font-medium text-gray-900 dark:text-white">{{ $user->gender ? ucfirst($user->gender) : 'Not provided' }}</dd>
                        </div>
                        <div class="p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Country</dt>
                            <dd class="mt-1 text-base font-medium text-gray-900 dark:text-white">{{ $user->country ?: 'Not provided' }}</dd>
                        </div>
                        <div class="p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Region/State</dt>
                            <dd class="mt-1 text-base font-medium text-gray-900 dark:text-white">{{ $user->region ?: 'Not provided' }}</dd>
                        </div>
                        <div class="p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">City</dt>
                            <dd class="mt-1 text-base font-medium text-gray-900 dark:text-white">{{ $user->city ?: 'Not provided' }}</dd>
                        </div>
                        <div class="p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Account Type</dt>
                            <dd class="mt-2">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-indigo-100 text-indigo-800 dark:bg-indigo-900/50 dark:text-indigo-300">
                                    {{ ucfirst($user->role->value ?? 'Student') }}
                                </span>
                            </dd>
                        </div>
                        <div class="p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Email Status</dt>
                            <dd class="mt-2">
                                @if($user->email_verified_at)
                                    <span class="inline-flex items-center text-sm font-medium text-green-600 dark:text-green-400">
                                        <svg class="w-4 h-4 mr-1.5" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                        </svg>
                                        Verified
                                    </span>
                                @else
                                    <span class="inline-flex items-center text-sm font-medium text-amber-600 dark:text-amber-400">
                                        <svg class="w-4 h-4 mr-1.5" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                        </svg>
                                        Unverified
                                    </span>
                                @endif
                            </dd>
                        </div>
                        <div class="p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Account Status</dt>
                            <dd class="mt-2">
                                @if($user->is_active)
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800 dark:bg-green-900/50 dark:text-green-300">
                                        Active
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-800 dark:bg-red-900/50 dark:text-red-300">
                                        Inactive
                                    </span>
                                @endif
                            </dd>
                        </div>
                    </dl>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                        <svg class="w-5 h-5 mr-2 text-purple-500 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                        Recent Activity
                    </h3>
                </div>
                <div class="p-6">
                    <div class="flow-root">
                        <ul role="list" class="-mb-8">
                            <li>
                                <div class="relative pb-8">
                                    <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200 dark:bg-gray-600" aria-hidden="true"></span>
                                    <div class="relative flex space-x-3">
                                        <div>
                                            <span class="h-8 w-8 rounded-full bg-blue-500 dark:bg-blue-600 flex items-center justify-center ring-4 ring-white dark:ring-gray-800">
                                                <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                                </svg>
                                            </span>
                                        </div>
                                        <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                                            <div>
                                                <p class="text-sm font-medium text-gray-700 dark:text-gray-300">Profile updated</p>
                                            </div>
                                            <div class="text-right text-sm whitespace-nowrap text-gray-500 dark:text-gray-400">
                                                <time>{{ $user->updated_at->diffForHumans() }}</time>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </li>
                            <li>
                                <div class="relative pb-8">
                                    <div class="relative flex space-x-3">
                                        <div>
                                            <span class="h-8 w-8 rounded-full bg-green-500 dark:bg-green-600 flex items-center justify-center ring-4 ring-white dark:ring-gray-800">
                                                <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>
                                            </span>
                                        </div>
                                        <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                                            <div>
                                                <p class="text-sm font-medium text-gray-700 dark:text-gray-300">Account created</p>
                                            </div>
                                            <div class="text-right text-sm whitespace-nowrap text-gray-500 dark:text-gray-400">
                                                <time>{{ $user->created_at->diffForHumans() }}</time>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- User Relationships & Data Section -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                        <svg class="w-5 h-5 mr-2 text-blue-500 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                        </svg>
                        Your Data & Relationships
                    </h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">All related data associated with your account</p>
                </div>
                <div class="divide-y divide-gray-200 dark:divide-gray-700">
                    @php
                        $relationships = [
                            // Role-specific profiles
                            ['name' => 'Student Profile', 'relation' => 'student', 'count' => $user->student ? 1 : 0, 'type' => 'profile', 'icon' => 'M12 14l9-5-9-5-9 5 9 5z M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z'],
                            ['name' => 'Teacher Profile', 'relation' => 'teacher', 'count' => $user->teacher ? 1 : 0, 'type' => 'profile', 'icon' => 'M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z'],
                            ['name' => 'Author Profile', 'relation' => 'author', 'count' => $user->author ? 1 : 0, 'type' => 'profile', 'icon' => 'M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z'],
                            ['name' => 'Librarian Profile', 'relation' => 'librarian', 'count' => $user->librarian ? 1 : 0, 'type' => 'profile', 'icon' => 'M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z'],
                            ['name' => 'Parent Profile', 'relation' => 'parent', 'count' => $user->parent ? 1 : 0, 'type' => 'profile', 'icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z'],
                            // Content relationships
                            ['name' => 'Notes', 'relation' => 'notes', 'count' => $user->notes_count ?? 0, 'type' => 'content', 'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
                            ['name' => 'Worksheets', 'relation' => 'worksheets', 'count' => $user->worksheets_count ?? 0, 'type' => 'content', 'icon' => 'M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
                            ['name' => 'Quiz Sessions', 'relation' => 'quizSessions', 'count' => $user->quiz_sessions_count ?? 0, 'type' => 'content', 'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4'],
                            // Library relationships
                            ['name' => 'Borrowed Books', 'relation' => 'borrowedBooks', 'count' => $user->borrowed_books_count ?? 0, 'type' => 'library', 'icon' => 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253'],
                            ['name' => 'Book Subscriptions', 'relation' => 'bookSubscriptions', 'count' => $user->book_subscriptions_count ?? 0, 'type' => 'library', 'icon' => 'M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z'],
                            // Subscription relationships
                            ['name' => 'Token Subscriptions', 'relation' => 'tokenSubscriptions', 'count' => $user->token_subscriptions_count ?? 0, 'type' => 'subscription', 'icon' => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
                            ['name' => 'Subscription Cycles', 'relation' => 'subscriptionCycles', 'count' => $user->subscription_cycles_count ?? 0, 'type' => 'subscription', 'icon' => 'M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15'],
                            // Activity relationships
                            ['name' => 'Login Activities', 'relation' => 'loginActivities', 'count' => $user->login_activities_count ?? 0, 'type' => 'activity', 'icon' => 'M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1'],
                            ['name' => 'Token Usage Logs', 'relation' => 'tokenUsageLogs', 'count' => $user->token_usage_logs_count ?? 0, 'type' => 'activity', 'icon' => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z'],
                            // Media relationships
                            ['name' => 'Uploaded Media', 'relation' => 'uploadedMedia', 'count' => $user->uploaded_media_count ?? 0, 'type' => 'media', 'icon' => 'M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z'],
                            // Team relationships
                            ['name' => 'Owned Teams', 'relation' => 'ownedTeams', 'count' => $user->owned_teams_count ?? 0, 'type' => 'team', 'icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z'],
                            ['name' => 'Team Memberships', 'relation' => 'joinedTeams', 'count' => $user->joined_teams_count ?? 0, 'type' => 'team', 'icon' => 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z'],
                            // Other relationships
                            ['name' => 'User Preferences', 'relation' => 'preferences', 'count' => $user->preferences_count ?? 0, 'type' => 'other', 'icon' => 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z'],
                            ['name' => 'Roles', 'relation' => 'roles', 'count' => $user->roles_count ?? 0, 'type' => 'other', 'icon' => 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z'],
                        ];

                        $typeColors = [
                            'profile' => 'from-indigo-500 to-indigo-600',
                            'content' => 'from-blue-500 to-blue-600',
                            'library' => 'from-green-500 to-green-600',
                            'subscription' => 'from-purple-500 to-purple-600',
                            'activity' => 'from-amber-500 to-amber-600',
                            'media' => 'from-pink-500 to-pink-600',
                            'team' => 'from-teal-500 to-teal-600',
                            'other' => 'from-gray-500 to-gray-600',
                        ];
                    @endphp

                    @foreach($relationships as $rel)
                        @if($rel['count'] > 0)
                            <div x-data="{ expanded: false }" class="border-b border-gray-200 dark:border-gray-700 last:border-b-0">
                                <button @click="expanded = !expanded" class="w-full px-6 py-4 flex items-center justify-between hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors duration-150">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 bg-gradient-to-br {{ $typeColors[$rel['type']] }} rounded-lg flex items-center justify-center shadow-lg mr-4">
                                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $rel['icon'] }}"/>
                                            </svg>
                                        </div>
                                        <div class="text-left">
                                            <p class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $rel['name'] }}</p>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ $rel['count'] }} {{ Str::plural('item', $rel['count']) }}</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200 mr-3">
                                            {{ $rel['count'] }}
                                        </span>
                                        <svg class="w-5 h-5 text-gray-400 dark:text-gray-500 transition-transform duration-200" :class="{ 'rotate-180': expanded }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                        </svg>
                                    </div>
                                </button>

                                <div x-show="expanded" x-collapse class="bg-gray-50 dark:bg-gray-900/50">
                                    <div class="px-6 py-4">
                                        @if($rel['type'] === 'profile')
                                            {{-- Profile items --}}
                                            @if($rel['relation'] === 'student' && $user->student)
                                                <div class="flex items-center justify-between p-3 bg-white dark:bg-gray-800 rounded-lg shadow-sm">
                                                    <div>
                                                        <p class="text-sm font-medium text-gray-900 dark:text-gray-100">Student ID: {{ $user->student->student_id ?? 'N/A' }}</p>
                                                        <p class="text-xs text-gray-500 dark:text-gray-400">Status: {{ $user->student->status ?? 'Active' }}</p>
                                                    </div>
                                                </div>
                                            @elseif($rel['relation'] === 'teacher' && $user->teacher)
                                                <div class="flex items-center justify-between p-3 bg-white dark:bg-gray-800 rounded-lg shadow-sm">
                                                    <div>
                                                        <p class="text-sm font-medium text-gray-900 dark:text-gray-100">Employee ID: {{ $user->teacher->employee_id ?? 'N/A' }}</p>
                                                        <p class="text-xs text-gray-500 dark:text-gray-400">Status: {{ $user->teacher->status ?? 'Active' }}</p>
                                                    </div>
                                                </div>
                                            @elseif($rel['relation'] === 'author' && $user->author)
                                                <div class="flex items-center justify-between p-3 bg-white dark:bg-gray-800 rounded-lg shadow-sm">
                                                    <div>
                                                        <p class="text-sm font-medium text-gray-900 dark:text-gray-100">Author Profile</p>
                                                        <p class="text-xs text-gray-500 dark:text-gray-400">Created: {{ $user->author->created_at?->format('M j, Y') ?? 'N/A' }}</p>
                                                    </div>
                                                </div>
                                            @elseif($rel['relation'] === 'librarian' && $user->librarian)
                                                <div class="flex items-center justify-between p-3 bg-white dark:bg-gray-800 rounded-lg shadow-sm">
                                                    <div>
                                                        <p class="text-sm font-medium text-gray-900 dark:text-gray-100">Employee ID: {{ $user->librarian->employee_id ?? 'N/A' }}</p>
                                                        <p class="text-xs text-gray-500 dark:text-gray-400">Status: {{ $user->librarian->status ?? 'Active' }}</p>
                                                    </div>
                                                </div>
                                            @elseif($rel['relation'] === 'parent' && $user->parent)
                                                <div class="flex items-center justify-between p-3 bg-white dark:bg-gray-800 rounded-lg shadow-sm">
                                                    <div>
                                                        <p class="text-sm font-medium text-gray-900 dark:text-gray-100">Parent Profile</p>
                                                        <p class="text-xs text-gray-500 dark:text-gray-400">Created: {{ $user->parent->created_at?->format('M j, Y') ?? 'N/A' }}</p>
                                                    </div>
                                                </div>
                                            @endif
                                        @else
                                            {{-- Collection items --}}
                                            <div class="space-y-2 max-h-64 overflow-y-auto">
                                                @php
                                                    $items = $user->{$rel['relation']} ?? collect();
                                                @endphp
                                                @forelse($items as $item)
                                                    <div class="flex items-center justify-between p-3 bg-white dark:bg-gray-800 rounded-lg shadow-sm">
                                                        <div class="flex-1 min-w-0">
                                                            <p class="text-sm font-medium text-gray-900 dark:text-gray-100 truncate">
                                                                @if($rel['relation'] === 'notes')
                                                                    {{ $item->title ?? 'Untitled Note' }}
                                                                @elseif($rel['relation'] === 'worksheets')
                                                                    {{ $item->title ?? 'Untitled Worksheet' }}
                                                                @elseif($rel['relation'] === 'quizSessions')
                                                                    Quiz Session #{{ $item->id }}
                                                                @elseif($rel['relation'] === 'borrowedBooks')
                                                                    {{ $item->book?->title ?? 'Book #' . $item->book_id }}
                                                                @elseif($rel['relation'] === 'bookSubscriptions')
                                                                    {{ $item->book?->title ?? 'Book Subscription #' . $item->id }}
                                                                @elseif($rel['relation'] === 'tokenSubscriptions')
                                                                    Token Subscription #{{ $item->id }}
                                                                @elseif($rel['relation'] === 'subscriptionCycles')
                                                                    Cycle #{{ $item->cycle_number ?? $item->id }}
                                                                @elseif($rel['relation'] === 'loginActivities')
                                                                    Login from {{ $item->ip_address ?? 'Unknown IP' }}
                                                                @elseif($rel['relation'] === 'ownedTeams' || $rel['relation'] === 'joinedTeams')
                                                                    {{ $item->name ?? 'Team #' . $item->id }}
                                                                @elseif($rel['relation'] === 'preferences')
                                                                    {{ $item->key ?? 'Preference #' . $item->id }}
                                                                @elseif($rel['relation'] === 'roles')
                                                                    {{ $item->name ?? 'Role #' . $item->id }}
                                                                @else
                                                                    Item #{{ $item->id }}
                                                                @endif
                                                            </p>
                                                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                                                @if($item->created_at)
                                                                    Created {{ $item->created_at->diffForHumans() }}
                                                                @else
                                                                    ID: {{ $item->id }}
                                                                @endif
                                                            </p>
                                                        </div>
                                                    </div>
                                                @empty
                                                    <p class="text-sm text-gray-500 dark:text-gray-400 text-center py-4">No items to display</p>
                                                @endforelse
                                            </div>
                                            @if($rel['count'] > 10)
                                                <p class="mt-3 text-xs text-gray-500 dark:text-gray-400 text-center">
                                                    Showing 10 of {{ $rel['count'] }} items
                                                </p>
                                            @endif
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endforeach

                    {{-- Empty state if no relationships --}}
                    @php
                        $hasAnyRelationship = collect($relationships)->sum('count') > 0;
                    @endphp
                    @if(!$hasAnyRelationship)
                        <div class="px-6 py-8 text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">No related data</h3>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">You don't have any associated relationships or data yet.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Right Column - Quick Actions & Progress -->
        <div class="space-y-6">
            <!-- Quick Actions -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                        <svg class="w-5 h-5 mr-2 text-green-500 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                        Quick Actions
                    </h3>
                </div>
                <div class="p-6 space-y-3">
                    <x-link.primary :to="route('profile.edit')" class="w-full justify-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        Edit Profile
                    </x-link.primary>

                    <a href="{{ route('password.change') }}"
                       class="w-full inline-flex justify-center items-center px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800 transition-colors duration-200">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                        </svg>
                        Change Password
                    </a>

                    <a href="{{ route('security') }}"
                       class="w-full inline-flex justify-center items-center px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800 transition-colors duration-200">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                        Security Settings
                    </a>
                </div>
            </div>

            <!-- Profile Completion -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                        <svg class="w-5 h-5 mr-2 text-amber-500 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                        Profile Completion
                    </h3>
                </div>
                <div class="p-6">
                    <div class="mb-5">
                        <div class="flex justify-between text-sm mb-2">
                            <span class="text-gray-600 dark:text-gray-400">Profile Progress</span>
                            <span class="text-gray-900 dark:text-white font-semibold">{{ $profileCompletion['percentage'] }}%</span>
                        </div>
                        <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2.5">
                            <div class="bg-gradient-to-r from-indigo-500 to-purple-500 h-2.5 rounded-full transition-all duration-500" style="width: {{ $profileCompletion['percentage'] }}%"></div>
                        </div>
                    </div>
                    <div class="space-y-3">
                        @foreach($profileCompletion['items'] as $item)
                            @if($item['completed'])
                                <div class="flex items-center text-sm p-3 bg-green-50 dark:bg-green-900/20 rounded-lg">
                                    <svg class="w-5 h-5 text-green-500 dark:text-green-400 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                    <span class="text-green-700 dark:text-green-300 font-medium">{{ $item['label'] }}</span>
                                </div>
                            @else
                                <div class="flex items-center text-sm p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                                    <svg class="w-5 h-5 text-gray-400 dark:text-gray-500 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V7z" clip-rule="evenodd"/>
                                    </svg>
                                    <span class="text-gray-500 dark:text-gray-400">{{ $item['label'] }}</span>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
