<div class="min-h-screen bg-gray-50 dark:bg-gray-900">
    <div class="px-4 py-8 mx-auto max-w-4xl sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <a href="{{ route('teachers.classroom.show', $session) }}"
               class="inline-flex items-center text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">
                <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Back to session details
            </a>

            <h1 class="mt-4 text-3xl font-bold text-gray-900 dark:text-white">
                {{ $session->title }}
            </h1>
            <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                {{ $session->scheduled_start->format('l, F j, Y \a\t g:i A') }}
            </p>
        </div>

        <!-- Error Alert -->
        @if($error)
            <div class="p-4 mb-6 text-red-800 bg-red-100 border border-red-200 rounded-lg dark:bg-red-900/20 dark:border-red-900 dark:text-red-400">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                    </svg>
                    <span>{{ $error }}</span>
                </div>
            </div>
        @endif

        <!-- Main Content Card -->
        <div class="overflow-hidden bg-white rounded-lg shadow dark:bg-gray-800">
            <div class="p-8">
                @if(!$session->isLive() && !$joinUrl)
                    <!-- Pre-start State -->
                    <div class="text-center">
                        <div class="flex items-center justify-center w-20 h-20 mx-auto mb-6 bg-violet-100 rounded-full dark:bg-violet-900/20">
                            <svg class="w-10 h-10 text-violet-600 dark:text-violet-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                            </svg>
                        </div>

                        <h2 class="mb-2 text-2xl font-bold text-gray-900 dark:text-white">
                            Ready to start your session?
                        </h2>
                        <p class="mb-8 text-gray-600 dark:text-gray-400">
                            Click the button below to create the meeting room and join as moderator.
                        </p>

                        <!-- Session Info -->
                        <div class="p-4 mb-8 text-left border border-gray-200 rounded-lg bg-gray-50 dark:bg-gray-700 dark:border-gray-600">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Participants</p>
                                    <p class="text-lg font-semibold text-gray-900 dark:text-white">
                                        {{ $session->participants->count() }}
                                    </p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Duration</p>
                                    <p class="text-lg font-semibold text-gray-900 dark:text-white">
                                        {{ $session->duration_minutes }} min
                                    </p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Recording</p>
                                    <p class="text-lg font-semibold text-gray-900 dark:text-white">
                                        {{ $session->auto_record ? 'On' : 'Off' }}
                                    </p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Max Participants</p>
                                    <p class="text-lg font-semibold text-gray-900 dark:text-white">
                                        {{ $session->max_participants }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <button wire:click="startMeeting"
                                wire:loading.attr="disabled"
                                class="inline-flex items-center px-8 py-4 text-lg font-medium text-white bg-violet-600 rounded-lg hover:bg-violet-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-violet-500 disabled:opacity-50 disabled:cursor-not-allowed">
                            <span wire:loading.remove wire:target="startMeeting">
                                <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                Start Meeting
                            </span>
                            <span wire:loading wire:target="startMeeting">
                                <svg class="w-6 h-6 mr-2 animate-spin" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Creating meeting...
                            </span>
                        </button>
                    </div>
                @else
                    <!-- Live State -->
                    <div class="text-center">
                        <div class="relative inline-flex items-center justify-center w-20 h-20 mx-auto mb-6">
                            <span class="absolute inline-flex w-full h-full bg-red-400 rounded-full opacity-75 animate-ping"></span>
                            <div class="relative flex items-center justify-center w-20 h-20 bg-red-500 rounded-full">
                                <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                </svg>
                            </div>
                        </div>

                        <h2 class="mb-2 text-2xl font-bold text-gray-900 dark:text-white">
                            Session is Live!
                        </h2>
                        <p class="mb-8 text-gray-600 dark:text-gray-400">
                            Your meeting room is ready. Click below to join.
                        </p>

                        <!-- Live Meeting Info -->
                        @if($meetingInfo)
                            <div class="p-4 mb-6 border border-gray-200 rounded-lg bg-gray-50 dark:bg-gray-700 dark:border-gray-600">
                                <div class="grid grid-cols-3 gap-4 text-left">
                                    <div>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">Participants</p>
                                        <p class="text-lg font-semibold text-gray-900 dark:text-white">
                                            {{ $meetingInfo['participant_count'] ?? 0 }}
                                        </p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">Moderators</p>
                                        <p class="text-lg font-semibold text-gray-900 dark:text-white">
                                            {{ $meetingInfo['moderator_count'] ?? 0 }}
                                        </p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">Recording</p>
                                        <p class="text-lg font-semibold text-gray-900 dark:text-white">
                                            {{ ($meetingInfo['is_recording'] ?? false) ? 'Active' : 'Off' }}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-6">
                                <button wire:click="refreshMeetingInfo"
                                        class="text-sm text-violet-600 hover:text-violet-700 dark:text-violet-400 dark:hover:text-violet-300">
                                    <svg class="inline-block w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                    </svg>
                                    Refresh Info
                                </button>
                            </div>
                        @endif

                        <div class="space-y-4">
                            <button wire:click="joinMeeting"
                                    class="inline-flex items-center px-8 py-4 text-lg font-medium text-white bg-green-600 rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                                </svg>
                                Join Meeting Room
                            </button>

                            <button wire:click="endMeeting"
                                    wire:confirm="Are you sure you want to end this meeting? All participants will be removed."
                                    class="block px-6 py-3 mx-auto text-sm font-medium text-red-700 bg-white border border-red-300 rounded-lg hover:bg-red-50 dark:bg-gray-700 dark:text-red-400 dark:border-red-600 dark:hover:bg-gray-600">
                                <svg class="inline-block w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                                End Meeting
                            </button>
                        </div>

                        <!-- Meeting Info -->
                        <div class="p-4 mt-8 text-left border border-gray-200 rounded-lg bg-gray-50 dark:bg-gray-700 dark:border-gray-600">
                            <p class="mb-2 text-sm font-medium text-gray-900 dark:text-white">Meeting Information</p>
                            <div class="space-y-2 text-sm text-gray-600 dark:text-gray-400">
                                <p><span class="font-medium">Meeting ID:</span> {{ $session->meeting_id }}</p>
                                <p><span class="font-medium">Started:</span> {{ $session->actual_start?->diffForHumans() }}</p>
                                @if($session->actual_start)
                                    <p><span class="font-medium">Duration:</span> {{ $session->actual_start->diffInMinutes(now()) }} minutes</p>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Tips Card -->
        <div class="p-6 mt-6 bg-blue-50 border border-blue-200 rounded-lg dark:bg-blue-900/20 dark:border-blue-900">
            <h3 class="mb-3 text-sm font-semibold text-blue-900 dark:text-blue-300">Quick Tips</h3>
            <ul class="space-y-2 text-sm text-blue-800 dark:text-blue-400">
                <li class="flex items-start">
                    <svg class="w-5 h-5 mr-2 text-blue-600 dark:text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    <span>Test your audio and video before starting the session</span>
                </li>
                <li class="flex items-start">
                    <svg class="w-5 h-5 mr-2 text-blue-600 dark:text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    <span>As moderator, you have full control over the session</span>
                </li>
                <li class="flex items-start">
                    <svg class="w-5 h-5 mr-2 text-blue-600 dark:text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    <span>Students can join once the meeting is live</span>
                </li>
                @if($session->auto_record)
                    <li class="flex items-start">
                        <svg class="w-5 h-5 mr-2 text-blue-600 dark:text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <span>This session will be automatically recorded</span>
                    </li>
                @endif
                <li class="flex items-start">
                    <svg class="w-5 h-5 mr-2 text-blue-600 dark:text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    <span>Use the share screen feature to present materials</span>
                </li>
            </ul>
        </div>
    </div>

    @script
    <script>
        // Auto-refresh meeting info every 30 seconds when live
        let refreshInterval = null;

        $wire.on('meeting-started', () => {
            refreshInterval = setInterval(() => {
                $wire.refreshMeetingInfo();
            }, 30000); // 30 seconds
        });

        // Clean up interval when component is destroyed
        window.addEventListener('beforeunload', () => {
            if (refreshInterval) {
                clearInterval(refreshInterval);
            }
        });
    </script>
    @endscript
</div>
