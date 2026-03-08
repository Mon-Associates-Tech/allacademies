<div class="min-h-screen bg-gray-50 dark:bg-gray-900">
    <div class="px-4 py-8 mx-auto max-w-7xl sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-start justify-between">
                <div class="flex-1">
                    <div class="flex items-center gap-3 mb-2">
                        <a href="{{ route('teachers.classroom.index') }}"
                           class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                            </svg>
                        </a>
                        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
                            {{ $session->title }}
                        </h1>

                        <!-- Status Badge -->
                        <span class="inline-flex items-center px-3 py-1 text-sm font-medium rounded-full
                            {{ $session->status === 'live' ? 'bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400' : '' }}
                            {{ $session->status === 'scheduled' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-400' : '' }}
                            {{ $session->status === 'ended' ? 'bg-gray-100 text-gray-800 dark:bg-gray-900/20 dark:text-gray-400' : '' }}
                            {{ $session->status === 'cancelled' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-400' : '' }}">
                            @if($session->status === 'live')
                                <span class="relative flex w-2 h-2 mr-2">
                                    <span class="absolute inline-flex w-full h-full bg-red-400 rounded-full opacity-75 animate-ping"></span>
                                    <span class="relative inline-flex w-2 h-2 bg-red-500 rounded-full"></span>
                                </span>
                            @endif
                            {{ ucfirst($session->status) }}
                        </span>
                    </div>

                    @if($session->description)
                        <p class="text-gray-600 dark:text-gray-400">{{ $session->description }}</p>
                    @endif
                </div>

                <!-- Actions -->
                <div class="flex gap-2 ml-4">
                    @if($session->status === 'scheduled' && $session->canStart())
                        <a href="{{ route('teachers.classroom.start', $session) }}"
                           class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-green-600 rounded-lg hover:bg-green-700">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/>
                            </svg>
                            Start Session
                        </a>
                    @endif

                    @if($session->status === 'live')
                        <a href="{{ route('teachers.classroom.start', $session) }}"
                           class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                            </svg>
                            Join Live
                        </a>
                    @endif

                    @if($session->status === 'scheduled')
                        <a href="{{ route('teachers.classroom.edit', $session) }}"
                           class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-600 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-600">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                            Edit
                        </a>
                    @endif

                    <button wire:click="deleteSession"
                            wire:confirm="Are you sure you want to delete this session?"
                            class="inline-flex items-center px-4 py-2 text-sm font-medium text-red-700 bg-white border border-red-300 rounded-lg hover:bg-red-50 dark:bg-gray-700 dark:text-red-400 dark:border-red-600 dark:hover:bg-gray-600">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                        Delete
                    </button>
                </div>
            </div>
        </div>

        <!-- Tabs -->
        <div class="mb-6 border-b border-gray-200 dark:border-gray-700">
            <nav class="flex -mb-px space-x-8">
                <button wire:click="setTab('overview')"
                        class="py-4 text-sm font-medium border-b-2 {{ $activeTab === 'overview' ? 'border-violet-600 text-violet-600' : 'border-transparent text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-500' }}">
                    Overview
                </button>
                <button wire:click="setTab('participants')"
                        class="py-4 text-sm font-medium border-b-2 {{ $activeTab === 'participants' ? 'border-violet-600 text-violet-600' : 'border-transparent text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-500' }}">
                    Participants ({{ $session->participants->count() }})
                </button>
                @if($session->hasRecordings())
                    <button wire:click="setTab('recordings')"
                            class="py-4 text-sm font-medium border-b-2 {{ $activeTab === 'recordings' ? 'border-violet-600 text-violet-600' : 'border-transparent text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-500' }}">
                        Recordings ({{ $session->recordings->where('status', 'published')->count() }})
                    </button>
                @endif
                <button wire:click="setTab('settings')"
                        class="py-4 text-sm font-medium border-b-2 {{ $activeTab === 'settings' ? 'border-violet-600 text-violet-600' : 'border-transparent text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-500' }}">
                    Settings
                </button>
            </nav>
        </div>

        <!-- Tab Content -->
        @if($activeTab === 'overview')
            <div class="space-y-6">
                <!-- Session Info Card -->
                <div class="bg-white rounded-lg shadow dark:bg-gray-800">
                    <div class="p-6">
                        <h2 class="mb-4 text-lg font-semibold text-gray-900 dark:text-white">Session Information</h2>

                        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                            <div>
                                <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Scheduled Start</label>
                                <p class="mt-1 text-sm text-gray-900 dark:text-white">
                                    {{ $session->scheduled_start->format('l, F j, Y \a\t g:i A') }}
                                </p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Duration</label>
                                <p class="mt-1 text-sm text-gray-900 dark:text-white">
                                    {{ $session->duration_minutes }} minutes
                                </p>
                            </div>

                            @if($session->academicLevel)
                                <div>
                                    <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Academic Level</label>
                                    <p class="mt-1 text-sm text-gray-900 dark:text-white">
                                        {{ $session->academicLevel->name }}
                                    </p>
                                </div>
                            @endif

                            @if($session->academicSubject)
                                <div>
                                    <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Subject</label>
                                    <p class="mt-1 text-sm text-gray-900 dark:text-white">
                                        {{ $session->academicSubject->name }}
                                    </p>
                                </div>
                            @endif

                            @if($session->actual_start)
                                <div>
                                    <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Actual Start</label>
                                    <p class="mt-1 text-sm text-gray-900 dark:text-white">
                                        {{ $session->actual_start->format('g:i A') }}
                                    </p>
                                </div>
                            @endif

                            @if($session->actual_end)
                                <div>
                                    <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Actual End</label>
                                    <p class="mt-1 text-sm text-gray-900 dark:text-white">
                                        {{ $session->actual_end->format('g:i A') }}
                                    </p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Quick Stats -->
                <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
                    <div class="bg-white rounded-lg shadow dark:bg-gray-800">
                        <div class="p-6">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <svg class="w-8 h-8 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <p class="text-2xl font-semibold text-gray-900 dark:text-white">
                                        {{ $session->participants->count() }}
                                    </p>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Total Participants</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-lg shadow dark:bg-gray-800">
                        <div class="p-6">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <p class="text-2xl font-semibold text-gray-900 dark:text-white">
                                        {{ $session->participants->where('has_joined', true)->count() }}
                                    </p>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Attended</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-lg shadow dark:bg-gray-800">
                        <div class="p-6">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <p class="text-2xl font-semibold text-gray-900 dark:text-white">
                                        {{ $session->recordings->where('status', 'published')->count() }}
                                    </p>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Recordings</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        @if($activeTab === 'participants')
            <livewire:teachers.virtual-classroom.virtual-session-participants :session="$session" :key="'participants-'.$session->id" />
        @endif

        @if($activeTab === 'recordings')
            <livewire:teachers.virtual-classroom.virtual-session-recordings :session="$session" :key="'recordings-'.$session->id" />
        @endif

        @if($activeTab === 'settings')
            <div class="bg-white rounded-lg shadow dark:bg-gray-800">
                <div class="p-6">
                    <h2 class="mb-4 text-lg font-semibold text-gray-900 dark:text-white">Session Settings</h2>

                    <div class="space-y-4">
                        <div class="flex items-center justify-between py-3 border-b border-gray-200 dark:border-gray-700">
                            <div>
                                <p class="text-sm font-medium text-gray-900 dark:text-white">Auto-record session</p>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Automatically record the session</p>
                            </div>
                            <span class="text-sm {{ $session->auto_record ? 'text-green-600' : 'text-gray-400' }}">
                                {{ $session->auto_record ? 'Enabled' : 'Disabled' }}
                            </span>
                        </div>

                        <div class="flex items-center justify-between py-3 border-b border-gray-200 dark:border-gray-700">
                            <div>
                                <p class="text-sm font-medium text-gray-900 dark:text-white">Mute on start</p>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Participants are muted when joining</p>
                            </div>
                            <span class="text-sm {{ $session->mute_on_start ? 'text-green-600' : 'text-gray-400' }}">
                                {{ $session->mute_on_start ? 'Enabled' : 'Disabled' }}
                            </span>
                        </div>

                        <div class="flex items-center justify-between py-3 border-b border-gray-200 dark:border-gray-700">
                            <div>
                                <p class="text-sm font-medium text-gray-900 dark:text-white">Allow guest login</p>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Non-registered users can join</p>
                            </div>
                            <span class="text-sm {{ $session->allow_guest_login ? 'text-green-600' : 'text-gray-400' }}">
                                {{ $session->allow_guest_login ? 'Enabled' : 'Disabled' }}
                            </span>
                        </div>

                        <div class="flex items-center justify-between py-3 border-b border-gray-200 dark:border-gray-700">
                            <div>
                                <p class="text-sm font-medium text-gray-900 dark:text-white">Guest policy</p>
                                <p class="text-sm text-gray-500 dark:text-gray-400">How to handle guest join requests</p>
                            </div>
                            <span class="text-sm text-gray-900 dark:text-white">
                                {{ str_replace('_', ' ', $session->guest_policy) }}
                            </span>
                        </div>

                        <div class="flex items-center justify-between py-3">
                            <div>
                                <p class="text-sm font-medium text-gray-900 dark:text-white">Maximum participants</p>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Limit on concurrent participants</p>
                            </div>
                            <span class="text-sm text-gray-900 dark:text-white">
                                {{ $session->max_participants }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>

    @script
    <script>
        $wire.on('link-copied', (message) => {
            // Show toast notification
            alert(message);
        });
    </script>
    @endscript
</div>
