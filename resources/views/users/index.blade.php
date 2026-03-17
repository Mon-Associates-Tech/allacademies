@php
    $activeFilterCount = collect([
        request('verified'),
        request('unverified'),
        request('role'),
        request('online'),
        request('gender'),
        request('academic_group'),
        request('academic_level'),
        request('subject'),
        request('status'),
    ])->filter()->count();
@endphp

<x-layouts.app page-name="Users" :showTitleArea="false">
    <!-- Success/Error Messages -->
    @if (session()->has('success'))
        <div class="mb-6 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-300 px-4 py-3 rounded-lg shadow-sm">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                {{ session('success') }}
            </div>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="mb-6 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-300 px-4 py-3 rounded-lg shadow-sm">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                {{ session('error') }}
            </div>
        </div>
    @endif

    <!-- Professional Header Section -->
    <div class=" mb-6">
        <!-- Header Card -->
        <div class="bg-white dark:bg-gray-800 shadow-sm ring-1 ring-gray-900/5 dark:ring-gray-700/50 rounded-lg overflow-hidden">
            <!-- Title Row -->
            <div class="px-4 py-4 sm:px-6 border-b border-gray-100 dark:border-gray-700/50">
                <div class="flex items-center justify-between gap-3">
                    <div class="flex items-center gap-3 min-w-0">
                        <div class="flex-shrink-0 w-8 h-8 sm:w-10 sm:h-10 bg-gradient-to-br from-slate-500 to-slate-600 rounded-lg flex items-center justify-center shadow-lg shadow-slate-500/20">
                            <svg class="w-4 h-4 sm:w-5 sm:h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                        </div>
                        <div class="min-w-0">
                            <h1 class="text-base sm:text-lg font-semibold text-gray-900 dark:text-gray-100 truncate">Users</h1>
                            <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400 truncate">
                                {{ $users->total() }} {{ Str::plural('user', $users->total()) }} found
                                @if(request('search'))
                                    <span class="text-gray-400 dark:text-gray-500 hidden sm:inline">·</span>
                                    <span class="text-slate-600 dark:text-slate-400 hidden sm:inline">searching "{{ request('search') }}"</span>
                                @endif
                            </p>
                        </div>
                    </div>
                    <div class="flex-shrink-0">
                        <x-button.primary variant="primary" size="sm" type="button"
                                          onclick="window.Modal.open('add-user-form')">
                            <svg class="h-4 w-4 sm:mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            <span class="hidden sm:inline">Add User</span>
                        </x-button.primary>
                    </div>
                </div>
            </div>

            <!-- Search and Filter Row -->
            <div class="px-4 py-3 sm:px-6 bg-gray-50/50 dark:bg-gray-800/50" x-data="{ showFilters: {{ $activeFilterCount > 0 ? 'true' : 'false' }} }">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <!-- Search Form -->
                    <div class="flex-1 max-w-md">
                        <form method="GET" action="{{ route('users.index') }}">
                            @foreach(request()->except(['search', 'page']) as $key => $value)
                                @if(is_array($value))
                                    @foreach($value as $item)
                                        <input type="hidden" name="{{ $key }}[]" value="{{ $item }}">
                                    @endforeach
                                @else
                                    <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                                @endif
                            @endforeach

                            <div class="relative">
                                <input type="text"
                                       name="search"
                                       value="{{ request('search') }}"
                                       placeholder="Search by name or email..."
                                       class="w-full rounded-lg border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 dark:text-white dark:placeholder-gray-400 pl-10 pr-4 py-2 text-sm focus:border-slate-500 focus:ring-slate-500 shadow-sm">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-4 w-4 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                    </svg>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- Advanced Filters Component -->
                    <div class="relative flex-shrink-0">
                        <x-UserFilterPanel
                            :academicGroups="$academicGroups"
                            :academicLevels="$academicLevels"
                            :subjects="$subjects"
                        />
                    </div>
                </div>

                <!-- Active Filters Display (inside the card) -->
                @if(request()->hasAny(['gender', 'academic_group', 'academic_level', 'subject', 'status', 'role', 'verified', 'unverified', 'online']))
                    <div class="mt-3 pt-3 border-t border-gray-200/50 dark:border-gray-700/50 flex flex-wrap items-center gap-2">
                        <span class="text-xs font-medium text-gray-500 dark:text-gray-400">Active:</span>

                        @if(request('role'))
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 text-xs font-medium bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 rounded-full">
                                Role: {{ ucfirst(request('role')) }}
                                <a href="{{ route('users.index', array_diff_key(request()->all(), ['role' => ''])) }}" class="hover:text-slate-900 dark:hover:text-slate-100">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                </a>
                            </span>
                        @endif

                        @if(request('gender'))
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 text-xs font-medium bg-pink-100 dark:bg-pink-900/50 text-pink-700 dark:text-pink-300 rounded-full">
                                {{ ucfirst(request('gender')) }}
                                <a href="{{ route('users.index', array_diff_key(request()->all(), ['gender' => ''])) }}" class="hover:text-pink-900 dark:hover:text-pink-100">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                </a>
                            </span>
                        @endif

                        @if(request('academic_group'))
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 text-xs font-medium bg-indigo-100 dark:bg-indigo-900/50 text-indigo-700 dark:text-indigo-300 rounded-full">
                                {{ $academicGroups->find(request('academic_group'))->name ?? 'Group' }}
                                <a href="{{ route('users.index', array_diff_key(request()->all(), ['academic_group' => ''])) }}" class="hover:text-indigo-900 dark:hover:text-indigo-100">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                </a>
                            </span>
                        @endif

                        @if(request('academic_level'))
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 text-xs font-medium bg-blue-100 dark:bg-blue-900/50 text-blue-700 dark:text-blue-300 rounded-full">
                                {{ $academicLevels->find(request('academic_level'))->name ?? 'Level' }}
                                <a href="{{ route('users.index', array_diff_key(request()->all(), ['academic_level' => ''])) }}" class="hover:text-blue-900 dark:hover:text-blue-100">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                </a>
                            </span>
                        @endif

                        @if(request('subject'))
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 text-xs font-medium bg-purple-100 dark:bg-purple-900/50 text-purple-700 dark:text-purple-300 rounded-full">
                                {{ $subjects->find(request('subject'))->name ?? 'Subject' }}
                                <a href="{{ route('users.index', array_diff_key(request()->all(), ['subject' => ''])) }}" class="hover:text-purple-900 dark:hover:text-purple-100">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                </a>
                            </span>
                        @endif

                        @if(request('status'))
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-full">
                                {{ ucfirst(request('status')) }}
                                <a href="{{ route('users.index', array_diff_key(request()->all(), ['status' => ''])) }}" class="hover:text-gray-900 dark:hover:text-gray-100">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                </a>
                            </span>
                        @endif

                        @if(request('verified'))
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 text-xs font-medium bg-green-100 dark:bg-green-900/50 text-green-700 dark:text-green-300 rounded-full">
                                Verified
                                <a href="{{ route('users.index', array_diff_key(request()->all(), ['verified' => ''])) }}" class="hover:text-green-900 dark:hover:text-green-100">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                </a>
                            </span>
                        @endif

                        @if(request('unverified'))
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 text-xs font-medium bg-yellow-100 dark:bg-yellow-900/50 text-yellow-700 dark:text-yellow-300 rounded-full">
                                Unverified
                                <a href="{{ route('users.index', array_diff_key(request()->all(), ['unverified' => ''])) }}" class="hover:text-yellow-900 dark:hover:text-yellow-100">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                </a>
                            </span>
                        @endif

                        @if(request('online'))
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 text-xs font-medium bg-emerald-100 dark:bg-emerald-900/50 text-emerald-700 dark:text-emerald-300 rounded-full">
                                <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full"></span>
                                Online
                                <a href="{{ route('users.index', array_diff_key(request()->all(), ['online' => ''])) }}" class="hover:text-emerald-900 dark:hover:text-emerald-100">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                </a>
                            </span>
                        @endif

                        <a href="{{ route('users.index') }}" class="ml-auto text-xs text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 transition-colors">
                            Clear all
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>

    @if ($users->count())
        <!-- Mobile Card View -->
        <div class="md:hidden space-y-4 px-4">
            @foreach ($users as $user)
                @php
                    $roleValue = $user->role instanceof App\Enums\UserRole ? $user->role->value : $user->role;
                    $roleColors = [
                        'admin' => 'text-red-600 dark:text-red-400',
                        'teacher' => 'text-blue-600 dark:text-blue-400',
                        'student' => 'text-green-600 dark:text-green-400',
                        'librarian' => 'text-purple-600 dark:text-purple-400',
                        'author' => 'text-yellow-600 dark:text-yellow-400',
                        'parent' => 'text-pink-600 dark:text-pink-400',
                        'guest' => 'text-indigo-600 dark:text-indigo-400',
                        'moderator' => 'text-orange-600 dark:text-orange-400',
                        'owner' => 'text-violet-600 dark:text-violet-400',
                    ];
                    $roleColorClass = $roleColors[$roleValue] ?? 'text-gray-600 dark:text-gray-400';
                @endphp
                <div class="bg-white dark:bg-gray-800 shadow-sm ring-1 ring-gray-900/5 dark:ring-gray-700/50 rounded-lg p-4">
                    <!-- User Header -->
                    <div class="flex items-start gap-3">
                        <div class="relative flex-shrink-0">
                            <x-avatar avatar="{{$user->avatar}}" class="h-12 w-12" name="{{ $user->name }}"/>
                            @if($user->is_online)
                                <div class="absolute -bottom-0.5 -right-0.5 h-3 w-3 rounded-full bg-green-400 border-2 border-white dark:border-gray-800"></div>
                            @endif
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="font-medium text-gray-900 dark:text-gray-100 truncate">{{ $user->name }}</p>
                            <p class="text-sm text-gray-500 dark:text-gray-400 truncate">{{ $user->email }}</p>
                            <div class="flex items-center gap-2 mt-1">
                                <span class="text-xs font-medium capitalize {{ $roleColorClass }}">{{ $roleValue ?? 'User' }}</span>
                                <span class="text-gray-300 dark:text-gray-600">•</span>
                                @if($user->email_verified_at)
                                    <span class="text-xs text-green-600 dark:text-green-400">Verified</span>
                                @else
                                    <span class="text-xs text-yellow-600 dark:text-yellow-400">Unverified</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <!-- User Meta -->
                    <div class="mt-3 flex items-center justify-between text-xs text-gray-500 dark:text-gray-400 border-t border-gray-100 dark:border-gray-700 pt-3">
                        <span>Joined {{ $user->created_at->format('M j, Y') }}</span>
                        @if($user->is_online)
                            <span class="inline-flex items-center text-green-600 dark:text-green-400">
                                <div class="h-2 w-2 rounded-full bg-green-400 mr-1"></div>Online
                            </span>
                        @else
                            <span>{{ $user->last_seen_at ? $user->last_seen_at->diffForHumans() : 'Never active' }}</span>
                        @endif
                    </div>
                    <!-- Actions -->
                    <div class="mt-3 flex flex-wrap gap-2 border-t border-gray-100 dark:border-gray-700 pt-3">
                        <a href="{{ route('users.show', ['user' => $user]) }}" class="inline-flex items-center px-3 py-1.5 text-xs font-medium rounded-md text-blue-700 dark:text-blue-400 bg-blue-50 dark:bg-blue-900/20">
                            <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            View
                        </a>
                        @can('own')
                            @if($roleValue !== 'owner')
                                <button onclick="window.Modal.open('change-role-form', { userName: '{{$user->name}}', email: '{{$user->email}}', role: '{{$user->role}}', id: '{{$user->id}}' })" class="inline-flex items-center px-3 py-1.5 text-xs font-medium rounded-md text-purple-700 dark:text-purple-400 bg-purple-50 dark:bg-purple-900/20">
                                    <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    Role
                                </button>
                            @endif
                        @endcan
                        @if(!$user->email_verified_at)
                            <form method="POST" action="{{ route('users.mark-as-verified', $user) }}" class="inline">
                                @csrf
                                <button type="submit" 
                                        onclick="return confirm('Are you sure you want to mark this user as verified?')"
                                        class="inline-flex items-center px-3 py-1.5 text-xs font-medium rounded-md text-green-700 dark:text-green-400 bg-green-50 dark:bg-green-900/20 hover:bg-green-100 dark:hover:bg-green-900/40 transition-colors">
                                    <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    Verify
                                </button>
                            </form>
                        @endif
                        @if($user->canBeImpersonated())
                            <a href="{{ route('impersonate', $user->id) }}" class="inline-flex items-center px-3 py-1.5 text-xs font-medium rounded-md text-amber-700 dark:text-amber-400 bg-amber-50 dark:bg-amber-900/20">
                                <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path></svg>
                                Troubleshoot
                            </a>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Desktop Table View -->
        <div class="hidden md:block bg-white dark:bg-gray-800 shadow-sm ring-1 ring-gray-900/5 dark:ring-gray-700/50 rounded-lg overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-900">
                    <tr>
                        @php
                            $currentSort = request('sort_by', 'name');
                            $currentDirection = request('sort_direction', 'asc');
                        @endphp
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            <a href="{{ route('users.index', array_merge(request()->except(['sort_by', 'sort_direction', 'page']), ['sort_by' => 'name', 'sort_direction' => ($currentSort === 'name' && $currentDirection === 'asc') ? 'desc' : 'asc'])) }}"
                               class="group inline-flex items-center gap-1 hover:text-gray-700 dark:hover:text-gray-200 transition-colors">
                                User
                                <span class="flex flex-col">
                                    <svg class="w-3 h-3 {{ $currentSort === 'name' && $currentDirection === 'asc' ? 'text-blue-600 dark:text-blue-400' : 'text-gray-300 dark:text-gray-600 group-hover:text-gray-400' }}" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M5 12l5-5 5 5H5z"/>
                                    </svg>
                                    <svg class="w-3 h-3 -mt-1 {{ $currentSort === 'name' && $currentDirection === 'desc' ? 'text-blue-600 dark:text-blue-400' : 'text-gray-300 dark:text-gray-600 group-hover:text-gray-400' }}" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M5 8l5 5 5-5H5z"/>
                                    </svg>
                                </span>
                            </a>
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            <a href="{{ route('users.index', array_merge(request()->except(['sort_by', 'sort_direction', 'page']), ['sort_by' => 'last_seen_at', 'sort_direction' => ($currentSort === 'last_seen_at' && $currentDirection === 'asc') ? 'desc' : 'asc'])) }}"
                               class="group inline-flex items-center gap-1 hover:text-gray-700 dark:hover:text-gray-200 transition-colors">
                                Activity
                                <span class="flex flex-col">
                                    <svg class="w-3 h-3 {{ $currentSort === 'last_seen_at' && $currentDirection === 'asc' ? 'text-blue-600 dark:text-blue-400' : 'text-gray-300 dark:text-gray-600 group-hover:text-gray-400' }}" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M5 12l5-5 5 5H5z"/>
                                    </svg>
                                    <svg class="w-3 h-3 -mt-1 {{ $currentSort === 'last_seen_at' && $currentDirection === 'desc' ? 'text-blue-600 dark:text-blue-400' : 'text-gray-300 dark:text-gray-600 group-hover:text-gray-400' }}" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M5 8l5 5 5-5H5z"/>
                                    </svg>
                                </span>
                            </a>
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            <a href="{{ route('users.index', array_merge(request()->except(['sort_by', 'sort_direction', 'page']), ['sort_by' => 'created_at', 'sort_direction' => ($currentSort === 'created_at' && $currentDirection === 'asc') ? 'desc' : 'asc'])) }}"
                               class="group inline-flex items-center gap-1 hover:text-gray-700 dark:hover:text-gray-200 transition-colors">
                                Joined
                                <span class="flex flex-col">
                                    <svg class="w-3 h-3 {{ $currentSort === 'created_at' && $currentDirection === 'asc' ? 'text-blue-600 dark:text-blue-400' : 'text-gray-300 dark:text-gray-600 group-hover:text-gray-400' }}" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M5 12l5-5 5 5H5z"/>
                                    </svg>
                                    <svg class="w-3 h-3 -mt-1 {{ $currentSort === 'created_at' && $currentDirection === 'desc' ? 'text-blue-600 dark:text-blue-400' : 'text-gray-300 dark:text-gray-600 group-hover:text-gray-400' }}" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M5 8l5 5 5-5H5z"/>
                                    </svg>
                                </span>
                            </a>
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            <span class="sr-only">Actions</span>
                        </th>
                    </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach ($users as $user)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $roleValue = $user->role instanceof App\Enums\UserRole ? $user->role->value : $user->role;
                                    $roleColors = [
                                        'admin' => 'text-red-600 dark:text-red-400',
                                        'teacher' => 'text-blue-600 dark:text-blue-400',
                                        'student' => 'text-green-600 dark:text-green-400',
                                        'librarian' => 'text-purple-600 dark:text-purple-400',
                                        'author' => 'text-yellow-600 dark:text-yellow-400',
                                        'parent' => 'text-pink-600 dark:text-pink-400',
                                        'guest' => 'text-indigo-600 dark:text-indigo-400',
                                        'moderator' => 'text-orange-600 dark:text-orange-400',
                                        'owner' => 'text-violet-600 dark:text-violet-400',
                                    ];
                                    $roleColorClass = $roleColors[$roleValue] ?? 'text-gray-600 dark:text-gray-400';
                                @endphp
                                <div class="flex items-center space-x-3">
                                    <div class="relative">
                                        <x-avatar avatar="{{$user->avatar}}" class="h-10 w-10"
                                                  name="{{ $user->name }}"/>
                                        @if($user->is_online)
                                            <div
                                                class="absolute -bottom-0.5 -right-0.5 h-3 w-3 rounded-full bg-green-400 border-2 border-white dark:border-gray-800"></div>
                                        @endif
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <p class="font-medium text-gray-900 dark:text-gray-100 truncate">{{ $user->name }}</p>
                                        <p class="text-sm text-gray-500 dark:text-gray-400 truncate">{{ $user->email }}</p>
                                        <div class="flex items-center gap-2 mt-0.5">
                                            <span class="text-xs font-medium capitalize {{ $roleColorClass }}">{{ $roleValue ?? 'User' }}</span>
                                            <span class="text-gray-300 dark:text-gray-600">•</span>
                                            @if($user->email_verified_at)
                                                <span class="text-xs text-green-600 dark:text-green-400">Verified</span>
                                            @else
                                                <span class="text-xs text-yellow-600 dark:text-yellow-400">Unverified</span>
                                            @endif
                                        </div>
                                        @if(Auth::user()->hasRole('owner'))
                                            <p class="text-xs text-gray-400 dark:text-gray-500 truncate mt-0.5">{{ $user->school?->name }}</p>
                                        @endif
                                    </div>
                                </div>
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($user->is_online)
                                    <span class="inline-flex items-center text-xs text-green-600 dark:text-green-400">
                        <div class="h-2 w-2 rounded-full bg-green-400 mr-1"></div>
                        Online
                    </span>
                                @else
                                    <span class="text-xs text-gray-500 dark:text-gray-400">
                        {{ $user->last_seen_at ? $user->last_seen_at->diffForHumans() : 'Never' }}
                    </span>
                                @endif
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap">
                                <span
                                    class="text-sm text-gray-500 dark:text-gray-400">{{ $user->created_at->format('M j, Y') }}</span>
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex items-center justify-end space-x-1">
                                    <!-- View Button -->
                                    <a href="{{ route('users.show', ['user' => $user]) }}"
                                       class="inline-flex items-center px-2.5 py-1.5 text-xs font-medium rounded-md text-blue-700 dark:text-blue-400 bg-blue-50 dark:bg-blue-900/20 hover:bg-blue-100 dark:hover:bg-blue-900/40 transition-colors"
                                       title="View Details">
                                        <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                        <span class="hidden sm:inline">View</span>
                                    </a>

                                    <!-- Change Role Button (for owners only) -->
                                    @can('own')
                                        @if($roleValue !== 'owner')
                                            <button
                                                onclick="window.Modal.open('change-role-form', { userName: '{{$user->name}}', email: '{{$user->email}}', role: '{{$user->role}}', id: '{{$user->id}}' })"
                                                class="inline-flex items-center px-2.5 py-1.5 text-xs font-medium rounded-md text-purple-700 dark:text-purple-400 bg-purple-50 dark:bg-purple-900/20 hover:bg-purple-100 dark:hover:bg-purple-900/40 transition-colors"
                                                title="Change Role">
                                                <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>
                                                <span class="hidden sm:inline">Role</span>
                                            </button>
                                        @endif
                                    @endcan

                                    <!-- Mark as Verified Button -->
                                    @if(!$user->email_verified_at)
                                        <form method="POST" action="{{ route('users.mark-as-verified', $user) }}" class="inline">
                                            @csrf
                                            <button type="submit" 
                                                    onclick="return confirm('Are you sure you want to mark this user as verified?')"
                                                    class="inline-flex items-center px-2.5 py-1.5 text-xs font-medium rounded-md text-green-700 dark:text-green-400 bg-green-50 dark:bg-green-900/20 hover:bg-green-100 dark:hover:bg-green-900/40 transition-colors"
                                                    title="Mark as Verified">
                                                <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>
                                                <span class="hidden sm:inline">Verify</span>
                                            </button>
                                        </form>
                                    @endif

                                    <!-- Impersonate/Troubleshoot Button -->
                                    @if($user->canBeImpersonated())
                                        <a href="{{ route('impersonate', $user->id) }}"
                                           class="inline-flex items-center px-2.5 py-1.5 text-xs font-medium rounded-md text-amber-700 dark:text-amber-400 bg-amber-50 dark:bg-amber-900/20 hover:bg-amber-100 dark:hover:bg-amber-900/40 transition-colors"
                                           title="Login as this user to troubleshoot">
                                            <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                                            </svg>
                                            <span class="hidden lg:inline">Troubleshoot</span>
                                        </a>
                                    @endif

                                    <!-- More Actions Dropdown -->
                                    <x-dropdown>
                                        <button
                                            class="inline-flex items-center p-1.5 text-gray-400 dark:text-gray-500 hover:text-gray-600 dark:hover:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-md transition-colors"
                                            title="More actions">
                                            <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z"/>
                                            </svg>
                                        </button>

                                        <x-slot name="content">
                                            <x-dropdown.item :href="route('users.show', ['user' => $user])">
                                                <x-slot name="icon">
                                                    <svg class="mr-3 h-4 w-4 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                    </svg>
                                                </x-slot>
                                                View Full Profile
                                            </x-dropdown.item>

                                            <x-dropdown.item click="$dispatch('open-delete-modal', {{ $user->id }})">
                                                <x-slot name="icon">
                                                    <svg class="w-4 h-4 mr-2 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                    </svg>
                                                </x-slot>
                                                <span class="text-red-600 dark:text-red-400">Delete User</span>
                                            </x-dropdown.item>
                                        </x-slot>
                                    </x-dropdown>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pagination -->
        <div class="mt-6">
            {{ $users->links() }}
        </div>

    @else
        <!-- Empty state -->
        <div class="text-center py-12 bg-white dark:bg-gray-800 rounded-lg">
            <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor"
                 viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">No users found</h3>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                Try adjusting your search or filters to find what you're looking for.
            </p>
        </div>
    @endif

    <x-modal-component name="change-role-form">
        <x-slot:header>
            <div class="pb-4">
                <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100" id="modal-title">
                    Change User Role
                </h3>
            </div>
        </x-slot:header>
        <form method="POST" action="{{ route('users.change-role') }}" id="change-role-form">
            @csrf
            <input type="hidden" name="user_id" x-model="modalData.id">

            <div>
                <div
                    class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-blue-100 dark:bg-blue-900">
                    <svg class="h-6 w-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor"
                         viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="mt-3 text-center sm:mt-5">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100" id="modal-title">
                        Change User Role
                    </h3>
                    <div class="mt-2">
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            You are about to change the role for the following user:
                        </p>
                    </div>
                </div>
            </div>

            <div class="mt-5 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">User Name</label>
                    <input type="text"
                           x-model="modalData.userName"
                           readonly
                           class="mt-1 block w-full border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-gray-50 dark:bg-gray-700 text-gray-500 dark:text-gray-400">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Email</label>
                    <input type="email"
                           name="email"
                           x-model="modalData.email"
                           readonly
                           class="mt-1 block w-full border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-gray-50 dark:bg-gray-700 text-gray-500 dark:text-gray-400">
                </div>

                <div>
                    <label for="role" class="block text-sm font-medium text-gray-700 dark:text-gray-300">New
                        Role</label>
                    <select name="role"
                            id="role"
                            x-model="modalData.role"
                            class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        <option value="guest">Guest</option>
                        <option value="student">Student</option>
                        <option value="teacher">Teacher</option>
                        <option value="librarian">Librarian</option>
                        <option value="author">Author</option>
                        <option value="parent">Parent</option>
                        <option value="moderator">Moderator</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>
            </div>
        </form>

        <x-slot:footer>
            <div class="mt-5 sm:mt-6 sm:grid sm:grid-cols-2 sm:gap-3 sm:grid-flow-row-dense">
                <x-button.white type="button"
                                onclick="window.Modal.close('change-role-form')"
                                class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 dark:border-gray-600 shadow-sm px-4 py-2 bg-white dark:bg-gray-700 text-base font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:col-start-1 sm:text-sm">
                    Cancel
                </x-button.white>
                <x-button.primary type="submit" form="change-role-form"
                                  class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:col-start-2 sm:text-sm">
                    Change Role
                </x-button.primary>

            </div>
        </x-slot:footer>
    </x-modal-component>
    <x-modal-component name="add-user-form" height="h-96">
        <x-slot:header>
            <div class="">
                <h3 class="text-lg font-medium pb-4 text-gray-900 dark:text-gray-100">Add New User</h3>
            </div>
        </x-slot:header>

        <form method="POST" id="user-add-form" action="{{ route('users.store') }}">
            @csrf

            <!-- Name Field -->
            <div class="mb-4">
                <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Name</label>
                <input type="text"
                       id="name"
                       name="name"
                       required
                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder-gray-400 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                       placeholder="Enter user's full name">
            </div>

            <!-- Email Field -->
            <div class="mb-4">
                <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Email</label>
                <input type="email"
                       id="email"
                       name="email"
                       required
                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder-gray-400 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                       placeholder="Enter user's email address">
            </div>

            <!-- Password Field -->
            <div class="mb-4">
                <label for="password"
                       class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Password</label>
                <input type="password"
                       id="password"
                       name="password"
                       required
                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder-gray-400 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                       placeholder="Enter a secure password">
            </div>

            <!-- Role Field -->
            <div class="mb-6">
                <label for="role" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Role</label>
                <select id="role"
                        name="role"
                        required
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Select a role</option>
                    <option value="admin">Admin</option>
                    <option value="teacher">Teacher</option>
                    <option value="student">Student</option>
                    <option value="librarian">Librarian</option>
                    <option value="moderator">Moderator</option>
                    <option value="author">Author</option>
                    <option value="parent">Parent</option>
                    <option value="guest">Guest</option>
                </select>
            </div>
        </form>

        <x-slot:footer>
            <div class="flex items-center justify-end gap-3">
                <button type="button"
                        onclick="window.Modal.close('add-user-form')"
                        class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md hover:bg-gray-200 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-500">
                    Cancel
                </button>
                <x-button.primary size="md" type="submit" form="user-add-form"
                                  class="px-4  text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    Create User
                </x-button.primary>
            </div>
        </x-slot:footer>
    </x-modal-component>

    @livewire('users.delete-user-modal')

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            window.addEventListener('open-delete-modal', function (event) {
                // Use the correct Livewire dispatch method
                Livewire.dispatch('openDeleteModal', {userId: event.detail});
            });
        });
    </script>
</x-layouts.app>
