<div class="min-h-screen bg-gray-50 dark:bg-gray-900">
    <div class="px-4 py-8 mx-auto max-w-7xl sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
                        Virtual Classroom Sessions
                    </h1>
                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                        Manage your live and recorded virtual sessions
                    </p>
                </div>
                <a href="{{ route('teachers.classroom.create') }}"
                   class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-violet-600 border border-transparent rounded-lg shadow-sm hover:bg-violet-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-violet-500">
                    <svg class="w-5 h-5 mr-2 -ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Create Session
                </a>
            </div>
        </div>

        <!-- Filters -->
        <div class="mb-6">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <!-- View Tabs -->
                <div class="inline-flex rounded-lg shadow-sm" role="group">
                    <button wire:click="$set('view', 'upcoming')"
                            type="button"
                            class="px-4 py-2 text-sm font-medium {{ $view === 'upcoming' ? 'bg-violet-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-50' }} border border-gray-200 rounded-l-lg focus:z-10 focus:ring-2 focus:ring-violet-500 dark:bg-gray-800 dark:border-gray-600 dark:text-white">
                        Upcoming
                    </button>
                    <button wire:click="$set('view', 'past')"
                            type="button"
                            class="px-4 py-2 text-sm font-medium {{ $view === 'past' ? 'bg-violet-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-50' }} border-t border-b border-gray-200 focus:z-10 focus:ring-2 focus:ring-violet-500 dark:bg-gray-800 dark:border-gray-600 dark:text-white">
                        Past
                    </button>
                    <button wire:click="$set('view', 'all')"
                            type="button"
                            class="px-4 py-2 text-sm font-medium {{ $view === 'all' ? 'bg-violet-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-50' }} border border-gray-200 rounded-r-lg focus:z-10 focus:ring-2 focus:ring-violet-500 dark:bg-gray-800 dark:border-gray-600 dark:text-white">
                        All
                    </button>
                </div>

                <!-- Search -->
                <div class="relative">
                    <input wire:model.live.debounce.300ms="search"
                           type="text"
                           placeholder="Search sessions..."
                           class="w-full px-4 py-2 pr-10 text-sm border border-gray-300 rounded-lg focus:ring-violet-500 focus:border-violet-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white sm:w-64">
                    <svg class="absolute w-5 h-5 text-gray-400 transform -translate-y-1/2 right-3 top-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Sessions List -->
        <div class="space-y-4">
            @forelse($sessions as $session)
                <div class="overflow-hidden bg-white rounded-lg shadow dark:bg-gray-800">
                    <div class="p-6">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <div class="flex items-center gap-3 mb-2">
                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                                        {{ $session->title }}
                                    </h3>

                                    <!-- Status Badge -->
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        {{ $session->status === 'live' ? 'bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400' : '' }}
                                        {{ $session->status === 'scheduled' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-400' : '' }}
                                        {{ $session->status === 'ended' ? 'bg-gray-100 text-gray-800 dark:bg-gray-900/20 dark:text-gray-400' : '' }}
                                        {{ $session->status === 'cancelled' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-400' : '' }}">
                                        {{ ucfirst($session->status) }}
                                    </span>

                                    @if($session->status === 'live')
                                        <span class="relative flex h-3 w-3">
                                            <span class="absolute inline-flex w-full h-full bg-red-400 rounded-full opacity-75 animate-ping"></span>
                                            <span class="relative inline-flex w-3 h-3 bg-red-500 rounded-full"></span>
                                        </span>
                                    @endif
                                </div>

                                @if($session->description)
                                    <p class="mb-3 text-sm text-gray-600 dark:text-gray-400">
                                        {{ Str::limit($session->description, 150) }}
                                    </p>
                                @endif

                                <div class="flex flex-wrap gap-4 text-sm text-gray-500 dark:text-gray-400">
                                    <div class="flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                        {{ $session->scheduled_start->format('M d, Y') }}
                                    </div>
                                    <div class="flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        {{ $session->scheduled_start->format('g:i A') }} - {{ $session->scheduled_end->format('g:i A') }}
                                    </div>
                                    @if($session->academicSubject)
                                        <div class="flex items-center">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                            </svg>
                                            {{ $session->academicSubject->name }}
                                        </div>
                                    @endif
                                    <div class="flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                        </svg>
                                        {{ $session->participants->count() }} Participants
                                    </div>
                                </div>
                            </div>

                            <!-- Actions -->
                            <div class="flex gap-2 ml-4">
                                @if($session->status === 'scheduled' && $session->canStart())
                                    <button wire:click="startSession({{ $session->id }})"
                                            class="inline-flex items-center px-3 py-2 text-sm font-medium text-white bg-green-600 rounded-lg hover:bg-green-700">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        Start
                                    </button>
                                @endif

                                @if($session->status === 'live')
                                    <a href="{{ route('teachers.classroom.start', $session) }}"
                                       class="inline-flex items-center px-3 py-2 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                        </svg>
                                        Join Live
                                    </a>
                                @endif

                                <a href="{{ route('teachers.classroom.show', $session) }}"
                                   class="inline-flex items-center px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-600">
                                    View
                                </a>

                                @if($session->status === 'scheduled')
                                    <button wire:click="cancelSession({{ $session->id }})"
                                            wire:confirm="Are you sure you want to cancel this session?"
                                            class="inline-flex items-center px-3 py-2 text-sm font-medium text-red-700 bg-white border border-red-300 rounded-lg hover:bg-red-50 dark:bg-gray-700 dark:text-red-400 dark:border-red-600 dark:hover:bg-gray-600">
                                        Cancel
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="py-12 text-center bg-white rounded-lg shadow dark:bg-gray-800">
                    <svg class="w-12 h-12 mx-auto text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                    </svg>
                    <h3 class="mt-4 text-lg font-medium text-gray-900 dark:text-white">No sessions found</h3>
                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                        @if($view === 'upcoming')
                            You don't have any upcoming sessions. Create one to get started!
                        @else
                            No sessions match your current filter.
                        @endif
                    </p>
                    @if($view === 'upcoming')
                        <div class="mt-6">
                            <a href="{{ route('teachers.classroom.create') }}"
                               class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-violet-600 border border-transparent rounded-lg hover:bg-violet-700">
                                <svg class="w-5 h-5 mr-2 -ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                </svg>
                                Create Session
                            </a>
                        </div>
                    @endif
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if($sessions->hasPages())
            <div class="mt-6">
                {{ $sessions->links() }}
            </div>
        @endif
    </div>
</div>
