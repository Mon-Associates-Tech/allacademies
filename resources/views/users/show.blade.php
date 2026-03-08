<x-layouts.app title="{{ $user->name }} - User Details">
    <x-slot name="breadcrumb">
        <x-breadcrumb :paths="[
            'Users' => route('users.index'),
            $user->name => null
        ]" />
    </x-slot>

    <!-- Relationship Manager Component (handles deletion events and flash messages) -->
    @livewire('users.user-relationship-manager', ['userId' => $user->id])

    <!-- User Header Section -->
    <div class="bg-white dark:bg-gray-800 shadow-sm ring-1 ring-gray-900/5 dark:ring-gray-700/50 rounded-lg overflow-hidden">
        <div class="px-6 py-8">
            <div class="flex flex-col sm:flex-row items-start gap-6">
                <!-- Avatar Section -->
                <div class="flex-shrink-0">
                    <div class="relative">
                        <x-avatar name="{{$user->name}}" radius="rounded" avatar="{{$user->avatar}}" class="w-24 h-24" />
                        <!-- Online Status Indicator -->
                        @if($user->is_online)
                            <div class="absolute bottom-2 right-2 w-6 h-6 bg-green-400 border-4 border-white dark:border-gray-800 rounded-full"></div>
                        @endif
                    </div>
                </div>

                <!-- User Info Section -->
                <div class="flex-1 min-w-0">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $user->name }}</h1>
                            <p class="text-gray-500 dark:text-gray-400">{{ $user->email }}</p>

                            <!-- Role Badge -->
                            <div class="mt-2">
                                @php
                                    $roleValue = $user->role->value ?? 'user';
                                    $roleColors = [
                                        'admin' => 'bg-red-100 dark:bg-red-900 text-red-700 dark:text-red-200',
                                        'teacher' => 'bg-blue-100 dark:bg-blue-900 text-blue-700 dark:text-blue-200',
                                        'student' => 'bg-green-100 dark:bg-green-900 text-green-700 dark:text-green-200',
                                        'librarian' => 'bg-purple-100 dark:bg-purple-900 text-purple-700 dark:text-purple-200',
                                        'author' => 'bg-yellow-100 dark:bg-yellow-900 text-yellow-700 dark:text-yellow-200',
                                        'parent' => 'bg-pink-100 dark:bg-pink-900 text-pink-700 dark:text-pink-200',
                                        'guest' => 'bg-indigo-100 dark:bg-indigo-900 text-indigo-700 dark:text-indigo-200',
                                        'moderator' => 'bg-orange-100 dark:bg-orange-900 text-orange-700 dark:text-orange-200',
                                        'owner' => 'bg-violet-100 dark:bg-violet-900 text-violet-700 dark:text-violet-200',
                                    ];
                                    $colorClass = $roleColors[$roleValue] ?? 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200';
                                @endphp
                                <span class="inline-flex items-center rounded-md px-3 py-1 text-sm font-medium capitalize {{ $colorClass }}">
                                    {{ $roleValue }}
                                </span>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="mt-4 sm:mt-0 flex flex-col sm:flex-row gap-2">
                            @if(!$user->email_verified_at)
                                <button class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:focus:ring-offset-gray-800">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                    </svg>
                                    Send Verification Email
                                </button>
                            @endif

                            @if($user->canBeImpersonated())
                                <a href="{{ route('impersonate', $user->id) }}"
                                   class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-amber-600 hover:bg-amber-700 dark:bg-amber-500 dark:hover:bg-amber-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500 dark:focus:ring-offset-gray-800"
                                   title="Login as this user to troubleshoot">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                                    </svg>
                                    Troubleshoot
                                </a>
                            @endif

                            <button
                                x-on:click="$dispatch('open-modal', { name: 'reset-user-password' })"
                                class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 text-sm font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:focus:ring-offset-gray-800">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                                </svg>
                                Reset Password
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="mt-8 grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
        <!-- Subscriptions Card -->
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm ring-1 ring-gray-900/5 dark:ring-gray-700/50 rounded-lg transition-all duration-200 hover:shadow-md">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg flex items-center justify-center shadow-lg shadow-blue-500/30">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Subscriptions</dt>
                            <dd class="text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ $user->subscriptions_count }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <!-- Owned Teams Card -->
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm ring-1 ring-gray-900/5 dark:ring-gray-700/50 rounded-lg transition-all duration-200 hover:shadow-md">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-gradient-to-br from-green-500 to-green-600 rounded-lg flex items-center justify-center shadow-lg shadow-green-500/30">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Owned Teams</dt>
                            <dd class="text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ $user->owned_teams_count }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <!-- Joined Teams Card -->
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm ring-1 ring-gray-900/5 dark:ring-gray-700/50 rounded-lg transition-all duration-200 hover:shadow-md">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-purple-600 rounded-lg flex items-center justify-center shadow-lg shadow-purple-500/30">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Team Memberships</dt>
                            <dd class="text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ $user->joined_teams_count }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <!-- Worksheets Card -->
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm ring-1 ring-gray-900/5 dark:ring-gray-700/50 rounded-lg transition-all duration-200 hover:shadow-md">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-gradient-to-br from-amber-500 to-amber-600 rounded-lg flex items-center justify-center shadow-lg shadow-amber-500/30">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Worksheets</dt>
                            <dd class="text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ $user->worksheets_count }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="mt-8 grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Left Column - User Details -->
        <div class="lg:col-span-2 space-y-8">
            <!-- Account Information -->
            <div class="bg-white dark:bg-gray-800 shadow-sm ring-1 ring-gray-900/5 dark:ring-gray-700/50 rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Account Information</h3>
                </div>
                <div class="px-6 py-5">
                    <dl class="grid grid-cols-1 gap-x-6 gap-y-6 sm:grid-cols-2 lg:grid-cols-3">
                        <!-- Basic Information -->
                        <div class="sm:col-span-1">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Full Name</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $user->name }}</dd>
                        </div>
                        <div class="sm:col-span-1">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">First Name</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $user->first_name ?? '—' }}</dd>
                        </div>
                        <div class="sm:col-span-1">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Last Name</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $user->last_name ?? '—' }}</dd>
                        </div>
                        @if($user->other_names)
                            <div class="sm:col-span-1">
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Other Names</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $user->other_names }}</dd>
                            </div>
                        @endif
                        <div class="sm:col-span-1">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Email Address</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $user->email }}</dd>
                        </div>
                        <div class="sm:col-span-1">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Phone Number</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $user->phone ?? '—' }}</dd>
                        </div>
                        <div class="sm:col-span-1">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Gender</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100 capitalize">{{ $user->gender ?? '—' }}</dd>
                        </div>
                        <div class="sm:col-span-1">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Role</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100 capitalize">{{ $user->role->value ?? 'User' }}</dd>
                        </div>

                        <!-- Location Information -->
                        <div class="sm:col-span-1">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Country</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                @if($user->country)
                                    {{ $user->country }}@if($user->country_code) ({{ $user->country_code }})@endif
                                @else
                                    —
                                @endif
                            </dd>
                        </div>
                        <div class="sm:col-span-1">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Region</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $user->region ?? '—' }}</dd>
                        </div>
                        <div class="sm:col-span-1">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">City</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $user->city ?? '—' }}</dd>
                        </div>

                        <!-- School Information -->
                        <div class="sm:col-span-1">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">School</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $user->school?->name ?? '—' }}</dd>
                        </div>

                        <!-- Account Status -->
                        <div class="sm:col-span-1">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Account Status</dt>
                            <dd class="mt-1">
                                @php
                                    $statusColors = [
                                        'active' => 'bg-green-100 dark:bg-green-900 text-green-700 dark:text-green-200',
                                        'inactive' => 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200',
                                        'suspended' => 'bg-red-100 dark:bg-red-900 text-red-700 dark:text-red-200',
                                        'pending' => 'bg-yellow-100 dark:bg-yellow-900 text-yellow-700 dark:text-yellow-200',
                                    ];
                                    $statusClass = $statusColors[$user->status ?? 'active'] ?? $statusColors['active'];
                                @endphp
                                <span class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium capitalize {{ $statusClass }}">
                                    {{ $user->status ?? 'Active' }}
                                </span>
                            </dd>
                        </div>
                        <div class="sm:col-span-1">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Is Active</dt>
                            <dd class="mt-1">
                                @if($user->is_active)
                                    <span class="inline-flex items-center text-sm text-green-600 dark:text-green-400">
                                        <svg class="w-4 h-4 mr-1.5" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                        </svg>
                                        Yes
                                    </span>
                                @else
                                    <span class="inline-flex items-center text-sm text-red-600 dark:text-red-400">
                                        <svg class="w-4 h-4 mr-1.5" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                        </svg>
                                        No
                                    </span>
                                @endif
                            </dd>
                        </div>

                        <!-- Email Verification -->
                        <div class="sm:col-span-1">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Email Verification</dt>
                            <dd class="mt-1">
                                @if($user->email_verified_at)
                                    <span class="inline-flex items-center text-sm text-green-600 dark:text-green-400">
                                        <svg class="w-4 h-4 mr-1.5" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                        </svg>
                                        Verified on {{ $user->email_verified_at->format('M j, Y') }}
                                    </span>
                                @else
                                    <span class="inline-flex items-center text-sm text-yellow-600 dark:text-yellow-400">
                                        <svg class="w-4 h-4 mr-1.5" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                        </svg>
                                        Not Verified
                                    </span>
                                @endif
                            </dd>
                        </div>

                        <!-- Timestamps -->
                        <div class="sm:col-span-1">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Account Created</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $user->created_at->format('F j, Y \a\t g:i A') }}</dd>
                        </div>
                        <div class="sm:col-span-1">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Last Updated</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $user->updated_at->format('F j, Y \a\t g:i A') }}</dd>
                        </div>
                        <div class="sm:col-span-1">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Last Activity</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                @if($user->is_online)
                                    <span class="inline-flex items-center text-green-600 dark:text-green-400 font-medium">
                                        <span class="w-2 h-2 bg-green-500 rounded-full mr-2 animate-pulse"></span>
                                        Currently Online
                                    </span>
                                @else
                                    {{ $user->last_seen_at ? $user->last_seen_at->diffForHumans() : 'Never logged in' }}
                                @endif
                            </dd>
                        </div>
                    </dl>
                </div>
            </div>

            <!-- Suspension Information (if suspended) -->
            @if($user->status === 'suspended' || $user->suspended_at)
                <div class="bg-red-50 dark:bg-red-900/20 shadow-sm ring-1 ring-red-200 dark:ring-red-800 rounded-lg">
                    <div class="px-6 py-4 border-b border-red-200 dark:border-red-800">
                        <h3 class="text-lg font-semibold text-red-800 dark:text-red-200 flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                            Account Suspended
                        </h3>
                    </div>
                    <div class="px-6 py-5">
                        <dl class="grid grid-cols-1 gap-x-6 gap-y-4 sm:grid-cols-2">
                            <div class="sm:col-span-1">
                                <dt class="text-sm font-medium text-red-700 dark:text-red-300">Suspended At</dt>
                                <dd class="mt-1 text-sm text-red-900 dark:text-red-100">{{ $user->suspended_at ? $user->suspended_at->format('F j, Y \a\t g:i A') : '—' }}</dd>
                            </div>
                            <div class="sm:col-span-1">
                                <dt class="text-sm font-medium text-red-700 dark:text-red-300">Suspended By</dt>
                                <dd class="mt-1 text-sm text-red-900 dark:text-red-100">{{ $user->suspendedBy?->name ?? '—' }}</dd>
                            </div>
                            @if($user->suspension_reason)
                                <div class="sm:col-span-2">
                                    <dt class="text-sm font-medium text-red-700 dark:text-red-300">Suspension Reason</dt>
                                    <dd class="mt-1 text-sm text-red-900 dark:text-red-100">{{ $user->suspension_reason }}</dd>
                                </div>
                            @endif
                        </dl>
                    </div>
                </div>
            @endif

            <!-- Recent Subscriptions -->
            @if($user->subscriptions->count() > 0)
                <div class="bg-white dark:bg-gray-800 shadow-sm ring-1 ring-gray-900/5 dark:ring-gray-700/50 rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Recent Subscriptions</h3>
                    </div>
                    <div class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($user->subscriptions as $subscription)
                            <div class="px-6 py-4 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors duration-150">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                            Subscription #{{ $subscription->id }}
                                        </p>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">
                                            Created {{ $subscription->created_at->diffForHumans() }}
                                        </p>
                                    </div>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200">
                                        Active
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    @if($user->subscriptions_count > 10)
                        <div class="px-6 py-3 bg-gray-50 dark:bg-gray-700/50 text-center border-t border-gray-200 dark:border-gray-700">
                            <span class="text-sm text-gray-500 dark:text-gray-400">
                                And {{ $user->subscriptions_count - 10 }} more subscriptions
                            </span>
                        </div>
                    @endif
                </div>
            @endif

            <!-- User Relationships Section -->
            <div class="bg-white dark:bg-gray-800 shadow-sm ring-1 ring-gray-900/5 dark:ring-gray-700/50 rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">User Relationships & Data</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">All related data associated with this user account</p>
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
                                                    <button type="button"
                                                            wire:click="$dispatch('deleteRelationship', { userId: {{ $user->id }}, relation: 'student', itemId: {{ $user->student->id }} })"
                                                            class="p-2 text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors"
                                                            onclick="return confirm('Are you sure you want to delete this student profile?')">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                        </svg>
                                                    </button>
                                                </div>
                                            @elseif($rel['relation'] === 'teacher' && $user->teacher)
                                                <div class="flex items-center justify-between p-3 bg-white dark:bg-gray-800 rounded-lg shadow-sm">
                                                    <div>
                                                        <p class="text-sm font-medium text-gray-900 dark:text-gray-100">Employee ID: {{ $user->teacher->employee_id ?? 'N/A' }}</p>
                                                        <p class="text-xs text-gray-500 dark:text-gray-400">Status: {{ $user->teacher->status ?? 'Active' }}</p>
                                                    </div>
                                                    <button type="button"
                                                            wire:click="$dispatch('deleteRelationship', { userId: {{ $user->id }}, relation: 'teacher', itemId: {{ $user->teacher->id }} })"
                                                            class="p-2 text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors"
                                                            onclick="return confirm('Are you sure you want to delete this teacher profile?')">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                        </svg>
                                                    </button>
                                                </div>
                                            @elseif($rel['relation'] === 'author' && $user->author)
                                                <div class="flex items-center justify-between p-3 bg-white dark:bg-gray-800 rounded-lg shadow-sm">
                                                    <div>
                                                        <p class="text-sm font-medium text-gray-900 dark:text-gray-100">Author Profile</p>
                                                        <p class="text-xs text-gray-500 dark:text-gray-400">Created: {{ $user->author->created_at?->format('M j, Y') ?? 'N/A' }}</p>
                                                    </div>
                                                    <button type="button"
                                                            wire:click="$dispatch('deleteRelationship', { userId: {{ $user->id }}, relation: 'author', itemId: {{ $user->author->id }} })"
                                                            class="p-2 text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors"
                                                            onclick="return confirm('Are you sure you want to delete this author profile?')">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                        </svg>
                                                    </button>
                                                </div>
                                            @elseif($rel['relation'] === 'librarian' && $user->librarian)
                                                <div class="flex items-center justify-between p-3 bg-white dark:bg-gray-800 rounded-lg shadow-sm">
                                                    <div>
                                                        <p class="text-sm font-medium text-gray-900 dark:text-gray-100">Employee ID: {{ $user->librarian->employee_id ?? 'N/A' }}</p>
                                                        <p class="text-xs text-gray-500 dark:text-gray-400">Status: {{ $user->librarian->status ?? 'Active' }}</p>
                                                    </div>
                                                    <button type="button"
                                                            wire:click="$dispatch('deleteRelationship', { userId: {{ $user->id }}, relation: 'librarian', itemId: {{ $user->librarian->id }} })"
                                                            class="p-2 text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors"
                                                            onclick="return confirm('Are you sure you want to delete this librarian profile?')">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                        </svg>
                                                    </button>
                                                </div>
                                            @elseif($rel['relation'] === 'parent' && $user->parent)
                                                <div class="flex items-center justify-between p-3 bg-white dark:bg-gray-800 rounded-lg shadow-sm">
                                                    <div>
                                                        <p class="text-sm font-medium text-gray-900 dark:text-gray-100">Parent Profile</p>
                                                        <p class="text-xs text-gray-500 dark:text-gray-400">Created: {{ $user->parent->created_at?->format('M j, Y') ?? 'N/A' }}</p>
                                                    </div>
                                                    <button type="button"
                                                            wire:click="$dispatch('deleteRelationship', { userId: {{ $user->id }}, relation: 'parent', itemId: {{ $user->parent->id }} })"
                                                            class="p-2 text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors"
                                                            onclick="return confirm('Are you sure you want to delete this parent profile?')">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                        </svg>
                                                    </button>
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
                                                        @if(!in_array($rel['relation'], ['loginActivities', 'tokenUsageLogs']))
                                                            <button type="button"
                                                                    wire:click="$dispatch('deleteRelationship', { userId: {{ $user->id }}, relation: '{{ $rel['relation'] }}', itemId: {{ $item->id }} })"
                                                                    class="ml-3 p-2 text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors"
                                                                    onclick="return confirm('Are you sure you want to delete this item?')">
                                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                                </svg>
                                                            </button>
                                                        @endif
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
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">This user has no associated relationships or data.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Right Column - Quick Actions & Teams -->
        <div class="space-y-8">
            <!-- Quick Actions -->
            <div class="bg-white dark:bg-gray-800 shadow-sm ring-1 ring-gray-900/5 dark:ring-gray-700/50 rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Quick Actions</h3>
                </div>
                <div class="px-6 py-4 space-y-3">
                    @if(!$user->email_verified_at)
                        <button class="w-full inline-flex items-center justify-center px-4 py-2.5 border border-transparent text-sm font-medium rounded-lg text-white bg-blue-600 hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:focus:ring-offset-gray-800 transition-colors duration-150">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                            Send Verification Email
                        </button>
                    @endif

                    <!-- Reset Password Button -->
                    <button type="button"
                            x-data=""
                            x-on:click="$dispatch('open-modal', { name: 'reset-user-password' })"
                            class="w-full inline-flex items-center justify-center px-4 py-2.5 border border-gray-300 dark:border-gray-600 text-sm font-medium rounded-lg text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:focus:ring-offset-gray-800 transition-colors duration-150">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                        </svg>
                        Reset Password
                    </button>

                    <button type="button"
                            x-data=""
                            x-on:click="$dispatch('open-modal', { name: 'send-message-to-user' })"
                            class="w-full inline-flex items-center justify-center px-4 py-2.5 border border-gray-300 dark:border-gray-600 text-sm font-medium rounded-lg text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:focus:ring-offset-gray-800 transition-colors duration-150">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                        </svg>
                        Send Message
                    </button>

                    <x-modal-component
                        name="send-message-to-user"
                        title="Send Message to {{ $user->name }}"
                        size="lg"
                        :show="$errors->isNotEmpty()">
                        @livewire('common.messages.send-message-to-user', ['userId' => $user->id, 'userName' => $user->name])
                    </x-modal-component>

                    <x-modal-component
                        name="reset-user-password"
                        title="Reset Password for {{ $user->name }}"
                        size="md"
                        :show="$errors->isNotEmpty()">
                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                            Please enter a new password for this user.
                        </p>
                        <div class="mt-4">
                            @livewire('common.users.reset-user-password', ['userId' => $user->id, 'userName' => $user->name])
                        </div>
                    </x-modal-component>

                    <!-- Suspend/Unsuspend Account Buttons -->
                    @if($user->isSuspended())
                        <!-- Unsuspend Account Button (shown when user is suspended) -->
                        <button type="button"
                                x-data=""
                                x-on:click="$dispatch('open-modal', { name: 'unsuspend-user' })"
                                class="w-full inline-flex items-center justify-center px-4 py-2.5 border border-green-300 dark:border-green-700 text-sm font-medium rounded-lg text-green-700 dark:text-green-400 bg-white dark:bg-gray-700 hover:bg-green-50 dark:hover:bg-green-900/20 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 dark:focus:ring-offset-gray-800 transition-colors duration-150">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Unsuspend Account
                        </button>

                        <!-- Unsuspend User Modal -->
                        <x-modal-component
                            name="unsuspend-user"
                            title="Unsuspend User Account"
                            size="md"
                            :show="$errors->isNotEmpty()">
                            @livewire('common.users.unsuspend-user', ['userId' => $user->id, 'userName' => $user->name])
                        </x-modal-component>
                    @else
                        <!-- Suspend Account Button (shown when user is not suspended) -->
                        <button type="button"
                                x-data=""
                                x-on:click="$dispatch('open-modal', { name: 'suspend-user' })"
                                class="w-full inline-flex items-center justify-center px-4 py-2.5 border border-red-300 dark:border-red-700 text-sm font-medium rounded-lg text-red-700 dark:text-red-400 bg-white dark:bg-gray-700 hover:bg-red-50 dark:hover:bg-red-900/20 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 dark:focus:ring-offset-gray-800 transition-colors duration-150">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636m12.728 12.728L18 12M6 6l12 12"/>
                            </svg>
                            Suspend Account
                        </button>

                        <!-- Suspend User Modal -->
                        <x-modal-component
                            name="suspend-user"
                            title="Suspend User Account"
                            size="md"
                            :show="$errors->isNotEmpty()">
                            @livewire('common.users.suspend-user', ['userId' => $user->id, 'userName' => $user->name])
                        </x-modal-component>
                    @endif

                    <button
                        type="button"
                        x-data="{}"
                        @click="$dispatch('open-delete-modal', {{ $user->id }})"
                        class="w-full inline-flex items-center justify-center px-4 py-2.5 border border-red-300 dark:border-red-700 text-sm font-medium rounded-lg text-red-700 dark:text-red-400 bg-white dark:bg-gray-700 hover:bg-red-50 dark:hover:bg-red-900/20 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 dark:focus:ring-offset-gray-800 transition-colors duration-150">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                        Delete Account
                    </button>
                </div>
            </div>

            <!-- Current Team -->
            @if($user->currentTeam)
                <div class="bg-white dark:bg-gray-800 shadow-sm ring-1 ring-gray-900/5 dark:ring-gray-700/50 rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Current Team</h3>
                    </div>
                    <div class="px-6 py-4">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-lg flex items-center justify-center shadow-lg shadow-indigo-500/30 mr-4">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $user->currentTeam->name }}</p>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Active team</p>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Team Memberships -->
            @if($user->joinedTeams->count() > 0)
                <div class="bg-white dark:bg-gray-800 shadow-sm ring-1 ring-gray-900/5 dark:ring-gray-700/50 rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Team Memberships</h3>
                    </div>
                    <div class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($user->joinedTeams as $team)
                            <div class="px-6 py-3 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors duration-150">
                                <p class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $team->name }}</p>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Member</p>
                            </div>
                        @endforeach
                    </div>
                    @if($user->joined_teams_count > 3)
                        <div class="px-6 py-3 bg-gray-50 dark:bg-gray-700/50 text-center border-t border-gray-200 dark:border-gray-700">
                            <span class="text-sm text-gray-500 dark:text-gray-400">
                                And {{ $user->joined_teams_count - 3 }} more teams
                            </span>
                        </div>
                    @endif
                </div>
            @endif
        </div>
    </div>

    @livewire('users.delete-user-modal')

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            window.addEventListener('open-delete-modal', function(event) {
                // Use the correct Livewire dispatch method
                Livewire.dispatch('openDeleteModal', { userId: event.detail });
            });
        });
    </script>

</x-layouts.app>
