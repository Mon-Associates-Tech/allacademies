<x-layouts.app title="{{ $user->name }} - User Details">
    <x-slot name="breadcrumb">
        <x-breadcrumb :paths="[
            'Users' => route('users.index'),
            $user->name => null
        ]" />
    </x-slot>

    <!-- User Header Section -->
    <div class="bg-white shadow-sm rounded-lg overflow-hidden">
        <div class="px-6 py-8">
            <div class="flex flex-col sm:flex-row items-start gap-6">
                <!-- Avatar Section -->
                <div class="flex-shrink-0">
                    <div class="relative">
                        <x-avatar name="{{$user->name}}" radius="rounded" avatar="{{$user->avatar}}" class="w-24 h-24" />
                        <!-- Online Status Indicator -->
                        @if($user->is_online)
                            <div class="absolute bottom-2 right-2 w-6 h-6 bg-green-400 border-4 border-white rounded-full"></div>
                        @endif
                    </div>
                </div>

                <!-- User Info Section -->
                <div class="flex-1 min-w-0">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900">{{ $user->name }}</h1>
                            <p class="text-gray-500">{{ $user->email }}</p>

                            <!-- Role Badge -->
                            <div class="mt-1">
                                <span class="inline-flex items-center rounded-md px-3 py-1 text-sm font-medium capitalize
                                    {{ $user->role === 'admin' ? 'bg-red-100 text-red-700' : '' }}
                                    {{ $user->role === 'teacher' ? 'bg-blue-100 text-blue-700' : '' }}
                                    {{ $user->role === 'student' ? 'bg-green-100 text-green-700' : '' }}
                                    {{ $user->role === 'librarian' ? 'bg-purple-100 text-purple-700' : '' }}
                                    {{ !in_array($user->role, ['admin', 'teacher', 'student', 'librarian']) ? 'bg-gray-100 text-gray-700' : '' }}">
                                    {{ $user->role ?? 'User' }}
                                </span>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="mt-4 sm:mt-0 flex flex-col sm:flex-row gap-2">
                            @if(!$user->email_verified_at)
                                <button class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                    </svg>
                                    Send Verification Email
                                </button>
                            @endif

                            <button class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
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
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-blue-500 rounded-md flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Subscriptions</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ $user->subscriptions_count }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <!-- Owned Teams Card -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-green-500 rounded-md flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Owned Teams</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ $user->owned_teams_count }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <!-- Joined Teams Card -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-purple-500 rounded-md flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Team Memberships</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ $user->joined_teams_count }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <!-- Worksheets Card -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-yellow-500 rounded-md flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Worksheets</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ $user->worksheets_count }}</dd>
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
            <div class="bg-white shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Account Information</h3>
                </div>
                <div class="px-6 py-4">
                    <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Full Name</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $user->name }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Email Address</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $user->email }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Role</dt>
                            <dd class="mt-1 text-sm text-gray-900 capitalize">{{ $user->role ?? 'User' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Email Verification</dt>
                            <dd class="mt-1">
                                @if($user->email_verified_at)
                                    <span class="inline-flex items-center text-sm text-green-600">
                                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                        </svg>
                                        Verified on {{ $user->email_verified_at->format('M j, Y') }}
                                    </span>
                                @else
                                    <span class="inline-flex items-center text-sm text-yellow-600">
                                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                        </svg>
                                        Not Verified
                                    </span>
                                @endif
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Account Created</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $user->created_at->format('F j, Y') }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Last Activity</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                @if($user->is_online)
                                    <span class="text-green-600 font-medium">Currently Online</span>
                                @else
                                    {{ $user->last_seen_at ? $user->last_seen_at->diffForHumans() : 'Never logged in' }}
                                @endif
                            </dd>
                        </div>
                    </dl>
                </div>
            </div>

            <!-- Recent Subscriptions -->
            @if($user->subscriptions->count() > 0)
                <div class="bg-white shadow rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Recent Subscriptions</h3>
                    </div>
                    <div class="divide-y divide-gray-200">
                        @foreach($user->subscriptions as $subscription)
                            <div class="px-6 py-4">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">
                                            Subscription #{{ $subscription->id }}
                                        </p>
                                        <p class="text-sm text-gray-500">
                                            Created {{ $subscription->created_at->diffForHumans() }}
                                        </p>
                                    </div>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        Active
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    @if($user->subscriptions_count > 5)
                        <div class="px-6 py-3 bg-gray-50 text-center">
                            <span class="text-sm text-gray-500">
                                And {{ $user->subscriptions_count - 5 }} more subscriptions
                            </span>
                        </div>
                    @endif
                </div>
            @endif
        </div>

        <!-- Right Column - Quick Actions & Teams -->
        <div class="space-y-8">
            <!-- Quick Actions -->
            <div class="bg-white shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Quick Actions</h3>
                </div>
                <div class="px-6 py-4 space-y-3">
                    @if(!$user->email_verified_at)
                        <button class="w-full inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                            Send Verification Email
                        </button>
                    @endif

                    <button class="w-full inline-flex items-center justify-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                        </svg>
                        Reset Password
                    </button>

                    <button class="w-full inline-flex items-center justify-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                        </svg>
                        Send Message
                    </button>

                    <button class="w-full inline-flex items-center justify-center px-4 py-2 border border-red-300 text-sm font-medium rounded-md text-red-700 bg-white hover:bg-red-50">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636m12.728 12.728L18 12M6 6l12 12"/>
                        </svg>
                        Suspend Account
                    </button>
                </div>
            </div>

            <!-- Current Team -->
            @if($user->currentTeam)
                <div class="bg-white shadow rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Current Team</h3>
                    </div>
                    <div class="px-6 py-4">
                        <div class="flex items-center">
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-900">{{ $user->currentTeam->name }}</p>
                                <p class="text-sm text-gray-500">Active team</p>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Team Memberships -->
            @if($user->joinedTeams->count() > 0)
                <div class="bg-white shadow rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Team Memberships</h3>
                    </div>
                    <div class="divide-y divide-gray-200">
                        @foreach($user->joinedTeams as $team)
                            <div class="px-6 py-3">
                                <p class="text-sm font-medium text-gray-900">{{ $team->name }}</p>
                                <p class="text-sm text-gray-500">Member</p>
                            </div>
                        @endforeach
                    </div>
                    @if($user->joined_teams_count > 3)
                        <div class="px-6 py-3 bg-gray-50 text-center">
                            <span class="text-sm text-gray-500">
                                And {{ $user->joined_teams_count - 3 }} more teams
                            </span>
                        </div>
                    @endif
                </div>
            @endif
        </div>
    </div>
</x-layouts.app>
