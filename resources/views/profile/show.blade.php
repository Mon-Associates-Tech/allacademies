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
