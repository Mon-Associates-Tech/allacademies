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

    $total = $users->total();
    $headerSub = $total . ' ' . Str::plural('user', $total) . ' found';
    if (request('search')) {
        $headerSub .= ' · searching "' . request('search') . '"';
    }
@endphp

<x-layouts.app page-name="Users" :showTitleArea="false">
    <x-exam.page variant="compact">

        <!-- Flash Messages -->
        <x-exam.flash type="success" />
        <x-exam.flash type="error" />

        <!-- Header -->
        <x-exam.header
            variant="compact"
            title="Users"
            eyebrow="USER MANAGEMENT"
            :sub="$headerSub"
            color="ok"
        >
            <x-slot:actions>
                <x-exam.button variant="primary" type="button" onclick="window.Modal.open('add-user-form')">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Add User
                </x-exam.button>
            </x-slot:actions>
        </x-exam.header>

        <!-- Search and Filters Card -->
        <x-exam.card class="mb-6" :padded="true" x-data="{ showFilters: {{ $activeFilterCount > 0 ? 'true' : 'false' }} }">
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
                            <x-exam.input
                                type="text"
                                name="search"
                                :value="request('search')"
                                placeholder="Search by name or email..."
                            />
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-4 w-4 text-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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

            <!-- Active Filters Display -->
            @if(request()->hasAny(['gender', 'academic_group', 'academic_level', 'subject', 'status', 'role', 'verified', 'unverified', 'online']))
                <div class="mt-3 pt-3 border-t border-line flex flex-wrap items-center gap-2">
                    <span class="font-mono text-[10px] uppercase tracking-[0.14em] text-muted">Active:</span>

                    @if(request('role'))
                        <x-exam.badge tone="primary">
                            Role: {{ ucfirst(request('role')) }}
                            <a href="{{ route('users.index', array_diff_key(request()->all(), ['role' => ''])) }}" class="ml-1 hover:text-accent">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </a>
                        </x-exam.badge>
                    @endif

                    @if(request('gender'))
                        <x-exam.badge tone="primary">
                            {{ ucfirst(request('gender')) }}
                            <a href="{{ route('users.index', array_diff_key(request()->all(), ['gender' => ''])) }}" class="ml-1 hover:text-accent">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </a>
                        </x-exam.badge>
                    @endif

                    @if(request('academic_group'))
                        <x-exam.badge tone="primary">
                            {{ $academicGroups->find(request('academic_group'))->name ?? 'Group' }}
                            <a href="{{ route('users.index', array_diff_key(request()->all(), ['academic_group' => ''])) }}" class="ml-1 hover:text-accent">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </a>
                        </x-exam.badge>
                    @endif

                    @if(request('academic_level'))
                        <x-exam.badge tone="primary">
                            {{ $academicLevels->find(request('academic_level'))->name ?? 'Level' }}
                            <a href="{{ route('users.index', array_diff_key(request()->all(), ['academic_level' => ''])) }}" class="ml-1 hover:text-accent">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </a>
                        </x-exam.badge>
                    @endif

                    @if(request('subject'))
                        <x-exam.badge tone="primary">
                            {{ $subjects->find(request('subject'))->name ?? 'Subject' }}
                            <a href="{{ route('users.index', array_diff_key(request()->all(), ['subject' => ''])) }}" class="ml-1 hover:text-accent">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </a>
                        </x-exam.badge>
                    @endif

                    @if(request('status'))
                        <x-exam.badge tone="muted">
                            {{ ucfirst(request('status')) }}
                            <a href="{{ route('users.index', array_diff_key(request()->all(), ['status' => ''])) }}" class="ml-1 hover:text-accent">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </a>
                        </x-exam.badge>
                    @endif

                    @if(request('verified'))
                        <x-exam.badge tone="ok">
                            Verified
                            <a href="{{ route('users.index', array_diff_key(request()->all(), ['verified' => ''])) }}" class="ml-1 hover:text-accent">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </a>
                        </x-exam.badge>
                    @endif

                    @if(request('unverified'))
                        <x-exam.badge tone="warn">
                            Unverified
                            <a href="{{ route('users.index', array_diff_key(request()->all(), ['unverified' => ''])) }}" class="ml-1 hover:text-accent">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </a>
                        </x-exam.badge>
                    @endif

                    @if(request('online'))
                        <x-exam.badge tone="ok">
                            <span class="h-1.5 w-1.5 rounded-full bg-ok"></span>
                            Online
                            <a href="{{ route('users.index', array_diff_key(request()->all(), ['online' => ''])) }}" class="ml-1 hover:text-accent">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </a>
                        </x-exam.badge>
                    @endif

                    <a href="{{ route('users.index') }}" class="whitespace-nowrap border-b border-dashed border-transparent font-mono text-[11px] font-semibold uppercase tracking-wider transition hover:border-current text-muted hover:text-ink focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary">
                        Clear all
                    </a>
                </div>
            @endif
        </x-exam.card>

    @if ($users->count())
        <!-- Mobile Card View -->
        <div class="md:hidden space-y-4">
            @foreach ($users as $user)
                @php
                    $roleValue = $user->role instanceof App\Enums\UserRole ? $user->role->value : $user->role;
                    $roleColors = [
                        'admin' => 'text-accent',
                        'teacher' => 'text-primary',
                        'student' => 'text-ok',
                        'librarian' => 'text-primary',
                        'accountant' => 'text-primary',
                        'author' => 'text-warn',
                        'parent' => 'text-primary',
                        'guest' => 'text-muted',
                        'moderator' => 'text-warn',
                        'owner' => 'text-primary',
                    ];
                    $roleColorClass = $roleColors[$roleValue] ?? 'text-muted';
                @endphp
                <x-exam.card>
                    <!-- User Header -->
                    <div class="flex items-start gap-3">
                        <div class="relative flex-shrink-0">
                            <x-avatar avatar="{{$user->avatar}}" class="h-12 w-12" name="{{ $user->name }}"/>
                            @if($user->is_online)
                                <div class="absolute -bottom-0.5 -right-0.5 h-3 w-3 rounded-full bg-ok border-2 border-surface"></div>
                            @endif
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="font-medium text-ink truncate">{{ $user->name }}</p>
                            <p class="text-sm text-ink-soft truncate">{{ $user->email }}</p>
                            <div class="flex items-center gap-2 mt-1">
                                <span class="text-xs font-medium capitalize {{ $roleColorClass }}">{{ $roleValue ?? 'User' }}</span>
                                <span class="text-line-strong">•</span>
                                @if($user->email_verified_at)
                                    <x-exam.badge tone="ok">Verified</x-exam.badge>
                                @else
                                    <x-exam.badge tone="warn">Unverified</x-exam.badge>
                                @endif
                            </div>
                        </div>
                    </div>
                    <!-- User Meta -->
                    <div class="mt-3 flex items-center justify-between text-xs text-ink-soft border-t border-line pt-3">
                        <span>Joined {{ $user->created_at->format('M j, Y') }}</span>
                        @if($user->is_online)
                            <span class="inline-flex items-center text-ok">
                                <div class="h-2 w-2 rounded-full bg-ok mr-1"></div>Online
                            </span>
                        @else
                            <span>{{ $user->last_seen_at ? $user->last_seen_at->diffForHumans() : 'Never active' }}</span>
                        @endif
                    </div>
                    <!-- Actions -->
                    <div class="mt-4 flex flex-wrap items-center gap-4 border-t border-line pt-4">
                        <a href="{{ route('users.show', ['user' => $user]) }}" class="inline-flex items-center whitespace-nowrap border-b border-dashed border-transparent font-mono text-[11px] font-semibold uppercase tracking-wider transition hover:border-current text-primary hover:text-primary-hi focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary">
                            <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            View
                        </a>
                        @can('own')
                            @if($roleValue !== 'owner')
                                <x-exam.link-button onclick="window.Modal.open('change-role-form', { userName: '{{$user->name}}', email: '{{$user->email}}', role: '{{$user->role}}', id: '{{$user->id}}' })">
                                    <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    Role
                                </x-exam.link-button>
                            @endif
                        @endcan
                        @if(!$user->email_verified_at)
                            <form method="POST" action="{{ route('users.mark-as-verified', $user) }}" class="inline">
                                @csrf
                                <x-exam.link-button onclick="return confirm('Are you sure you want to mark this user as verified?')">
                                    <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    Verify
                                </x-exam.link-button>
                            </form>
                        @endif
                        @if($user->canBeImpersonated())
                            <a href="{{ route('impersonate', $user->id) }}" class="inline-flex items-center whitespace-nowrap border-b border-dashed border-transparent font-mono text-[11px] font-semibold uppercase tracking-wider transition hover:border-current text-warn hover:text-warn focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-warn">
                                <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path></svg>
                                Troubleshoot
                            </a>
                        @endif
                    </div>
                </x-exam.card>
            @endforeach
        </div>

        <!-- Desktop Table View -->
        <x-exam.table class="hidden md:block">
            <x-slot:head>
                <tr class="border-b border-line bg-surface-2">
                    @php
                        $currentSort = request('sort_by', 'name');
                        $currentDirection = request('sort_direction', 'asc');
                    @endphp
                    <th class="whitespace-nowrap px-4 py-3 text-left">
                        <a href="{{ route('users.index', array_merge(request()->except(['sort_by', 'sort_direction', 'page']), ['sort_by' => 'name', 'sort_direction' => ($currentSort === 'name' && $currentDirection === 'asc') ? 'desc' : 'asc'])) }}"
                           class="inline-flex items-center gap-1.5 font-mono text-[10px] font-semibold uppercase tracking-[0.14em] transition hover:text-ink {{ $currentSort === 'name' ? 'text-ink' : 'text-muted' }}">
                            User
                            <span class="flex flex-col items-center gap-px leading-none" aria-hidden="true">
                                <svg class="h-1.5 w-1.5 {{ $currentSort === 'name' && $currentDirection === 'asc' ? 'text-accent' : 'text-line-strong' }}" viewBox="0 0 8 6" fill="currentColor"><path d="M4 0l4 6H0z"/></svg>
                                <svg class="h-1.5 w-1.5 {{ $currentSort === 'name' && $currentDirection === 'desc' ? 'text-accent' : 'text-line-strong' }}" viewBox="0 0 8 6" fill="currentColor"><path d="M4 6L0 0h8z"/></svg>
                            </span>
                        </a>
                    </th>
                    <th class="whitespace-nowrap px-4 py-3 text-left">
                        <a href="{{ route('users.index', array_merge(request()->except(['sort_by', 'sort_direction', 'page']), ['sort_by' => 'last_seen_at', 'sort_direction' => ($currentSort === 'last_seen_at' && $currentDirection === 'asc') ? 'desc' : 'asc'])) }}"
                           class="inline-flex items-center gap-1.5 font-mono text-[10px] font-semibold uppercase tracking-[0.14em] transition hover:text-ink {{ $currentSort === 'last_seen_at' ? 'text-ink' : 'text-muted' }}">
                            Activity
                            <span class="flex flex-col items-center gap-px leading-none" aria-hidden="true">
                                <svg class="h-1.5 w-1.5 {{ $currentSort === 'last_seen_at' && $currentDirection === 'asc' ? 'text-accent' : 'text-line-strong' }}" viewBox="0 0 8 6" fill="currentColor"><path d="M4 0l4 6H0z"/></svg>
                                <svg class="h-1.5 w-1.5 {{ $currentSort === 'last_seen_at' && $currentDirection === 'desc' ? 'text-accent' : 'text-line-strong' }}" viewBox="0 0 8 6" fill="currentColor"><path d="M4 6L0 0h8z"/></svg>
                            </span>
                        </a>
                    </th>
                    <th class="whitespace-nowrap px-4 py-3 text-left">
                        <a href="{{ route('users.index', array_merge(request()->except(['sort_by', 'sort_direction', 'page']), ['sort_by' => 'created_at', 'sort_direction' => ($currentSort === 'created_at' && $currentDirection === 'asc') ? 'desc' : 'asc'])) }}"
                           class="inline-flex items-center gap-1.5 font-mono text-[10px] font-semibold uppercase tracking-[0.14em] transition hover:text-ink {{ $currentSort === 'created_at' ? 'text-ink' : 'text-muted' }}">
                            Joined
                            <span class="flex flex-col items-center gap-px leading-none" aria-hidden="true">
                                <svg class="h-1.5 w-1.5 {{ $currentSort === 'created_at' && $currentDirection === 'asc' ? 'text-accent' : 'text-line-strong' }}" viewBox="0 0 8 6" fill="currentColor"><path d="M4 0l4 6H0z"/></svg>
                                <svg class="h-1.5 w-1.5 {{ $currentSort === 'created_at' && $currentDirection === 'desc' ? 'text-accent' : 'text-line-strong' }}" viewBox="0 0 8 6" fill="currentColor"><path d="M4 6L0 0h8z"/></svg>
                            </span>
                        </a>
                    </th>
                    <th class="whitespace-nowrap px-4 py-3 text-right font-mono text-[10px] font-semibold uppercase tracking-[0.14em] text-muted">
                        <span class="sr-only">Actions</span>
                    </th>
                </tr>
            </x-slot:head>

            @foreach ($users as $user)
                @php
                    $roleValue = $user->role instanceof App\Enums\UserRole ? $user->role->value : $user->role;
                    $roleColors = [
                        'admin' => 'text-accent',
                        'teacher' => 'text-primary',
                        'student' => 'text-ok',
                        'librarian' => 'text-primary',
                        'accountant' => 'text-primary',
                        'author' => 'text-warn',
                        'parent' => 'text-primary',
                        'guest' => 'text-muted',
                        'moderator' => 'text-warn',
                        'owner' => 'text-primary',
                    ];
                    $roleColorClass = $roleColors[$roleValue] ?? 'text-muted';
                @endphp
                <tr class="transition hover:bg-surface-2/60">
                    <td class="px-4 py-3 whitespace-nowrap">
                        <div class="flex items-center gap-3">
                            <div class="relative">
                                <x-avatar avatar="{{$user->avatar}}" class="h-10 w-10"
                                          name="{{ $user->name }}"/>
                                @if($user->is_online)
                                    <div class="absolute -bottom-0.5 -right-0.5 h-3 w-3 rounded-full bg-ok border-2 border-surface"></div>
                                @endif
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="font-medium text-ink truncate">{{ $user->name }}</p>
                                <p class="text-sm text-ink-soft truncate">{{ $user->email }}</p>
                                <div class="flex items-center gap-2 mt-0.5">
                                    <span class="text-xs font-medium capitalize {{ $roleColorClass }}">{{ $roleValue ?? 'User' }}</span>
                                    <span class="text-line-strong">•</span>
                                    @if($user->email_verified_at)
                                        <x-exam.badge tone="ok">Verified</x-exam.badge>
                                    @else
                                        <x-exam.badge tone="warn">Unverified</x-exam.badge>
                                    @endif
                                </div>
                                @if(Auth::user()->hasRole('owner'))
                                    <p class="text-xs text-muted truncate mt-0.5">{{ $user->school?->name }}</p>
                                @endif
                            </div>
                        </div>
                    </td>

                    <td class="px-4 py-3 whitespace-nowrap">
                        @if($user->is_online)
                            <span class="inline-flex items-center text-xs text-ok">
                                <div class="h-2 w-2 rounded-full bg-ok mr-1"></div>Online
                            </span>
                        @else
                            <span class="text-xs text-ink-soft">
                                {{ $user->last_seen_at ? $user->last_seen_at->diffForHumans() : 'Never' }}
                            </span>
                        @endif
                    </td>

                    <td class="px-4 py-3 whitespace-nowrap">
                        <span class="text-sm text-ink-soft">{{ $user->created_at->format('M j, Y') }}</span>
                    </td>

                    <td class="px-4 py-3 whitespace-nowrap text-right text-sm">
                        <div class="flex items-center justify-end gap-3">
                            <!-- View Button -->
                            <a href="{{ route('users.show', ['user' => $user]) }}" class="inline-flex items-center whitespace-nowrap border-b border-dashed border-transparent font-mono text-[11px] font-semibold uppercase tracking-wider transition hover:border-current text-primary hover:text-primary-hi focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary" title="View Details">
                                <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                                <span class="hidden sm:inline">View</span>
                            </a>

                            <!-- Change Role Button (for owners only) -->
                            @can('own')
                                @if($roleValue !== 'owner')
                                    <x-exam.link-button onclick="window.Modal.open('change-role-form', { userName: '{{$user->name}}', email: '{{$user->email}}', role: '{{$user->role}}', id: '{{$user->id}}' })" title="Change Role">
                                        <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        <span class="hidden sm:inline">Role</span>
                                    </x-exam.link-button>
                                @endif
                            @endcan

                            <!-- Mark as Verified Button -->
                            @if(!$user->email_verified_at)
                                <form method="POST" action="{{ route('users.mark-as-verified', $user) }}" class="inline">
                                    @csrf
                                    <x-exam.link-button onclick="return confirm('Are you sure you want to mark this user as verified?')" title="Mark as Verified">
                                        <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        <span class="hidden sm:inline">Verify</span>
                                    </x-exam.link-button>
                                </form>
                            @endif

                            <!-- Impersonate/Troubleshoot Button -->
                            @if($user->canBeImpersonated())
                                <a href="{{ route('impersonate', $user->id) }}" class="inline-flex items-center whitespace-nowrap border-b border-dashed border-transparent font-mono text-[11px] font-semibold uppercase tracking-wider transition hover:border-current text-warn hover:text-warn focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-warn" title="Login as this user to troubleshoot">
                                    <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                                    </svg>
                                    <span class="hidden lg:inline">Troubleshoot</span>
                                </a>
                            @endif

                            <!-- More Actions Dropdown -->
                            <x-dropdown>
                                <button
                                    class="inline-flex items-center p-1.5 text-muted hover:text-ink hover:bg-surface-2 rounded-md transition-colors"
                                    title="More actions">
                                    <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z"/>
                                    </svg>
                                </button>

                                <x-slot name="content">
                                    <x-dropdown.item :href="route('users.show', ['user' => $user])">
                                        <x-slot name="icon">
                                            <svg class="mr-3 h-4 w-4 text-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                        </x-slot>
                                        View Full Profile
                                    </x-dropdown.item>

                                    <x-dropdown.item click="$dispatch('open-delete-modal', {{ $user->id }})">
                                        <x-slot name="icon">
                                            <svg class="w-4 h-4 mr-2 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </x-slot>
                                        <span class="text-accent">Delete User</span>
                                    </x-dropdown.item>
                                </x-slot>
                            </x-dropdown>
                        </div>
                    </td>
                </tr>
            @endforeach

            @if($users->hasPages())
                <x-slot:footer>
                    <div class="flex justify-center">
                        {{ $users->links() }}
                    </div>
                </x-slot:footer>
            @endif
        </x-exam.table>

        <!-- Mobile Pagination -->
        <div class="md:hidden mt-6">
            {{ $users->links() }}
        </div>

    @else
        <!-- Empty state -->
        <x-exam.empty title="No users found" description="Try adjusting your search or filters to find what you're looking for." />
    @endif

    <!-- Change Role Modal -->
    <x-modal-component name="change-role-form">
        <x-slot:header>
            <div class="pb-4">
                <h3 class="text-lg leading-6 font-medium text-ink" id="modal-title">
                    Change User Role
                </h3>
            </div>
        </x-slot:header>
        <form method="POST" action="{{ route('users.change-role') }}" id="change-role-form">
            @csrf
            <input type="hidden" name="user_id" x-model="modalData.id">

            <div>
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-primary/10">
                    <svg class="h-6 w-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="mt-3 text-center sm:mt-5">
                    <h3 class="text-lg leading-6 font-medium text-ink" id="modal-title">
                        Change User Role
                    </h3>
                    <div class="mt-2">
                        <p class="text-sm text-ink-soft">
                            You are about to change the role for the following user:
                        </p>
                    </div>
                </div>
            </div>

            <div class="mt-5 space-y-4">
                <x-exam.field bare label="User Name">
                    <x-exam.input type="text" x-model="modalData.userName" readonly class="bg-surface-2 text-ink-soft" />
                </x-exam.field>

                <x-exam.field bare label="Email">
                    <input type="email" name="email" x-model="modalData.email" readonly
                           class="w-full rounded border border-line-strong bg-surface-2 px-3 py-2.5 text-sm text-ink-soft" />
                </x-exam.field>

                <x-exam.field bare label="New Role" :required="true">
                    <x-exam.select name="role" id="role" x-model="modalData.role">
                        <option value="guest">Guest</option>
                        <option value="student">Student</option>
                        <option value="teacher">Teacher</option>
                        <option value="librarian">Librarian</option>
                        <option value="accountant">Accountant</option>
                        <option value="author">Author</option>
                        <option value="parent">Parent</option>
                        <option value="moderator">Moderator</option>
                        <option value="admin">Admin</option>
                    </x-exam.select>
                </x-exam.field>
            </div>
        </form>

        <x-slot:footer>
            <div class="flex items-center justify-end gap-3">
                <x-exam.button variant="ghost" type="button" onclick="window.Modal.close('change-role-form')">
                    Cancel
                </x-exam.button>
                <x-exam.button variant="primary" type="submit" form="change-role-form">
                    Change Role
                </x-exam.button>
            </div>
        </x-slot:footer>
    </x-modal-component>

    <!-- Add User Modal -->
    <x-modal-component name="add-user-form" height="h-96">
        <x-slot:header>
            <div class="">
                <h3 class="text-lg font-medium pb-4 text-ink">Add New User</h3>
            </div>
        </x-slot:header>

        <form method="POST" id="user-add-form" action="{{ route('users.store') }}">
            @csrf

            <x-exam.field bare label="Name" :required="true">
                <x-exam.input type="text" id="name" name="name" required placeholder="Enter user's full name" />
            </x-exam.field>

            <x-exam.field bare label="Email" :required="true">
                <x-exam.input type="email" id="email" name="email" required placeholder="Enter user's email address" />
            </x-exam.field>

            <x-exam.field bare label="Password" :required="true">
                <x-exam.input type="password" id="password" name="password" required placeholder="Enter a secure password" />
            </x-exam.field>

            <x-exam.field bare label="Role" :required="true">
                <x-exam.select id="role" name="role" required placeholder="Select a role">
                    <option value="admin">Admin</option>
                    <option value="teacher">Teacher</option>
                    <option value="student">Student</option>
                    <option value="librarian">Librarian</option>
                    <option value="accountant">Accountant</option>
                    <option value="moderator">Moderator</option>
                    <option value="author">Author</option>
                    <option value="parent">Parent</option>
                    <option value="guest">Guest</option>
                </x-exam.select>
            </x-exam.field>
        </form>

        <x-slot:footer>
            <div class="flex items-center justify-end gap-3">
                <x-exam.button variant="ghost" type="button" onclick="window.Modal.close('add-user-form')">
                    Cancel
                </x-exam.button>
                <x-exam.button variant="primary" type="submit" form="user-add-form">
                    Create User
                </x-exam.button>
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

    </x-exam.page>
</x-layouts.app>
