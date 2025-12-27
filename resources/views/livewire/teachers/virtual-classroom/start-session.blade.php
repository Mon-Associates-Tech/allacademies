<div class="min-h-screen bg-gradient-to-br from-gray-50 via-violet-50/30 to-gray-50 dark:from-gray-900 dark:via-violet-950/20 dark:to-gray-900">
    <div class="px-4 py-8 mx-auto max-w-5xl sm:px-6 lg:px-8">
        <!-- Enhanced Header -->
        <div class="mb-8">
            <a href="{{ route('teachers.classroom.show', $session) }}"
               class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-600 transition-all bg-white border border-gray-200 rounded-lg hover:bg-gray-50 hover:text-gray-900 hover:border-gray-300 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-700 dark:hover:bg-gray-700 dark:hover:text-gray-300">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Back to session details
            </a>

            <div class="flex items-start justify-between mt-6">
                <div>
                    <h1 class="text-4xl font-bold text-gray-900 dark:text-white">
                        {{ $session->title }}
                    </h1>
                    <div class="flex items-center gap-2 mt-3">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <p class="text-base text-gray-600 dark:text-gray-400">
                            {{ $session->scheduled_start->format('l, F j, Y \a\t g:i A') }}
                        </p>
                    </div>
                </div>
                @if($session->isLive())
                    <span class="flex items-center gap-2 px-4 py-2 text-sm font-semibold text-red-700 bg-red-100 rounded-full dark:bg-red-900/30 dark:text-red-400 animate-pulse">
                        <span class="relative flex w-3 h-3">
                            <span class="absolute inline-flex w-full h-full bg-red-400 rounded-full opacity-75 animate-ping"></span>
                            <span class="relative inline-flex w-3 h-3 bg-red-500 rounded-full"></span>
                        </span>
                        LIVE
                    </span>
                @endif
            </div>
        </div>

        <!-- Error Alert -->
        @if($error)
            <div class="p-4 mb-6 border border-red-200 rounded-xl bg-gradient-to-r from-red-50 to-red-100/50 dark:from-red-900/20 dark:to-red-900/10 dark:border-red-900/50">
                <div class="flex items-start gap-3">
                    <div class="flex-shrink-0 p-1 bg-red-100 rounded-lg dark:bg-red-900/40">
                        <svg class="w-5 h-5 text-red-600 dark:text-red-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <p class="font-medium text-red-900 dark:text-red-300">{{ $error }}</p>
                    </div>
                </div>
            </div>
        @endif

        <!-- Main Content Card -->
        <div class="overflow-hidden border border-gray-200 shadow-xl bg-white/80 backdrop-blur-sm rounded-2xl dark:bg-gray-800/80 dark:border-gray-700">
            @if(!$session->isLive() && !$joinUrl)
                <!-- Pre-start State -->
                <div class="p-12">
                    <div class="text-center">
                        <div class="relative inline-flex items-center justify-center mb-8">
                            <div class="absolute w-32 h-32 rounded-full bg-violet-200/50 dark:bg-violet-900/20 animate-pulse"></div>
                            <div class="relative flex items-center justify-center w-24 h-24 rounded-2xl bg-gradient-to-br from-violet-500 to-violet-600 shadow-lg shadow-violet-500/50">
                                <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                </svg>
                            </div>
                        </div>

                        <h2 class="mb-3 text-3xl font-bold text-gray-900 dark:text-white">
                            Ready to start your session?
                        </h2>
                        <p class="mb-10 text-lg text-gray-600 dark:text-gray-400">
                            Create the meeting room and join as moderator to begin your live session.
                        </p>

                        <!-- Enhanced Session Info Grid -->
                        <div class="grid grid-cols-2 gap-4 p-6 mb-10 border border-gray-200 md:grid-cols-4 rounded-xl bg-gradient-to-br from-gray-50 to-white dark:from-gray-700 dark:to-gray-800 dark:border-gray-600">
                            <div class="p-4 text-center transition-all bg-white border border-gray-100 rounded-lg hover:shadow-md dark:bg-gray-800 dark:border-gray-700">
                                <div class="flex justify-center mb-2">
                                    <div class="p-2 bg-blue-100 rounded-lg dark:bg-blue-900/30">
                                        <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                                        </svg>
                                    </div>
                                </div>
                                <p class="mb-1 text-sm font-medium text-gray-500 dark:text-gray-400">Participants</p>
                                <p class="text-2xl font-bold text-gray-900 dark:text-white">
                                    {{ $session->participants->count() }}
                                </p>
                            </div>
                            <div class="p-4 text-center transition-all bg-white border border-gray-100 rounded-lg hover:shadow-md dark:bg-gray-800 dark:border-gray-700">
                                <div class="flex justify-center mb-2">
                                    <div class="p-2 bg-green-100 rounded-lg dark:bg-green-900/30">
                                        <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                    </div>
                                </div>
                                <p class="mb-1 text-sm font-medium text-gray-500 dark:text-gray-400">Duration</p>
                                <p class="text-2xl font-bold text-gray-900 dark:text-white">
                                    {{ $session->duration_minutes }}<span class="text-sm text-gray-500"> min</span>
                                </p>
                            </div>
                            <div class="p-4 text-center transition-all bg-white border border-gray-100 rounded-lg hover:shadow-md dark:bg-gray-800 dark:border-gray-700">
                                <div class="flex justify-center mb-2">
                                    <div class="p-2 rounded-lg {{ $session->auto_record ? 'bg-red-100 dark:bg-red-900/30' : 'bg-gray-100 dark:bg-gray-700' }}">
                                        <svg class="w-5 h-5 {{ $session->auto_record ? 'text-red-600 dark:text-red-400' : 'text-gray-600 dark:text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                        </svg>
                                    </div>
                                </div>
                                <p class="mb-1 text-sm font-medium text-gray-500 dark:text-gray-400">Recording</p>
                                <p class="text-2xl font-bold text-gray-900 dark:text-white">
                                    {{ $session->auto_record ? 'On' : 'Off' }}
                                </p>
                            </div>
                            <div class="p-4 text-center transition-all bg-white border border-gray-100 rounded-lg hover:shadow-md dark:bg-gray-800 dark:border-gray-700">
                                <div class="flex justify-center mb-2">
                                    <div class="p-2 bg-purple-100 rounded-lg dark:bg-purple-900/30">
                                        <svg class="w-5 h-5 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                        </svg>
                                    </div>
                                </div>
                                <p class="mb-1 text-sm font-medium text-gray-500 dark:text-gray-400">Max Capacity</p>
                                <p class="text-2xl font-bold text-gray-900 dark:text-white">
                                    {{ $session->max_participants }}
                                </p>
                            </div>
                        </div>

                        <button wire:click="startMeeting"
                                wire:loading.attr="disabled"
                                class="inline-flex items-center gap-3 px-10 py-5 text-lg font-semibold text-white transition-all shadow-lg bg-gradient-to-r from-violet-600 to-violet-700 rounded-xl hover:from-violet-700 hover:to-violet-800 hover:shadow-xl hover:scale-105 focus:outline-none focus:ring-4 focus:ring-violet-500/50 disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:scale-100">
                            <span wire:loading.remove wire:target="startMeeting" class="flex items-center gap-3">
                                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                Start Meeting
                            </span>
                            <span wire:loading wire:target="startMeeting" class="flex items-center gap-3">
                                <svg class="w-6 h-6 animate-spin" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Creating meeting...
                            </span>
                        </button>
                    </div>
                </div>
            @else
                <!-- Live State -->
                <div class="p-12">
                    <div class="text-center">
                        <div class="relative inline-flex items-center justify-center mb-8">
                            <span class="absolute inline-flex w-32 h-32 bg-red-400 rounded-full opacity-30 animate-ping"></span>
                            <span class="absolute inline-flex w-28 h-28 bg-red-400 rounded-full opacity-40 animate-ping" style="animation-delay: 0.3s;"></span>
                            <div class="relative flex items-center justify-center w-24 h-24 rounded-2xl bg-gradient-to-br from-red-500 to-red-600 shadow-lg shadow-red-500/50">
                                <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                </svg>
                            </div>
                        </div>

                        <h2 class="mb-3 text-3xl font-bold text-gray-900 dark:text-white">
                            Session is Live!
                        </h2>
                        <p class="mb-10 text-lg text-gray-600 dark:text-gray-400">
                            Your meeting room is active and ready. Join now to start teaching.
                        </p>

                        <!-- Live Meeting Info -->
                        @if($meetingInfo)
                            <div class="p-6 mb-8 border border-gray-200 rounded-xl bg-gradient-to-br from-gray-50 to-white dark:from-gray-700 dark:to-gray-800 dark:border-gray-600">
                                <div class="grid grid-cols-3 gap-4">
                                    <div class="p-4 text-center bg-white border border-gray-100 rounded-lg dark:bg-gray-800 dark:border-gray-700">
                                        <div class="flex justify-center mb-2">
                                            <div class="p-2 bg-blue-100 rounded-lg dark:bg-blue-900/30">
                                                <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                                                </svg>
                                            </div>
                                        </div>
                                        <p class="mb-1 text-sm font-medium text-gray-500 dark:text-gray-400">Participants</p>
                                        <p class="text-2xl font-bold text-gray-900 dark:text-white">
                                            {{ $meetingInfo['participant_count'] ?? 0 }}
                                        </p>
                                    </div>
                                    <div class="p-4 text-center bg-white border border-gray-100 rounded-lg dark:bg-gray-800 dark:border-gray-700">
                                        <div class="flex justify-center mb-2">
                                            <div class="p-2 bg-violet-100 rounded-lg dark:bg-violet-900/30">
                                                <svg class="w-5 h-5 text-violet-600 dark:text-violet-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>
                                            </div>
                                        </div>
                                        <p class="mb-1 text-sm font-medium text-gray-500 dark:text-gray-400">Moderators</p>
                                        <p class="text-2xl font-bold text-gray-900 dark:text-white">
                                            {{ $meetingInfo['moderator_count'] ?? 0 }}
                                        </p>
                                    </div>
                                    <div class="p-4 text-center bg-white border border-gray-100 rounded-lg dark:bg-gray-800 dark:border-gray-700">
                                        <div class="flex justify-center mb-2">
                                            <div class="p-2 rounded-lg {{ ($meetingInfo['is_recording'] ?? false) ? 'bg-red-100 dark:bg-red-900/30' : 'bg-gray-100 dark:bg-gray-700' }}">
                                                <svg class="w-5 h-5 {{ ($meetingInfo['is_recording'] ?? false) ? 'text-red-600 dark:text-red-400' : 'text-gray-600 dark:text-gray-400' }}" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"/>
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm0-2a6 6 0 100-12 6 6 0 000 12z" clip-rule="evenodd"/>
                                                </svg>
                                            </div>
                                        </div>
                                        <p class="mb-1 text-sm font-medium text-gray-500 dark:text-gray-400">Recording</p>
                                        <p class="text-2xl font-bold text-gray-900 dark:text-white">
                                            {{ ($meetingInfo['is_recording'] ?? false) ? 'Active' : 'Off' }}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <button wire:click="refreshMeetingInfo"
                                    class="inline-flex items-center gap-2 px-4 py-2 mb-8 text-sm font-medium text-violet-700 transition-all bg-violet-50 border border-violet-200 rounded-lg hover:bg-violet-100 dark:bg-violet-900/20 dark:text-violet-400 dark:border-violet-800 dark:hover:bg-violet-900/30">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                </svg>
                                Refresh Info
                            </button>
                        @endif

                        <div class="space-y-4">
                            <button wire:click="joinMeeting"
                                    class="inline-flex items-center gap-3 px-10 py-5 text-lg font-semibold text-white transition-all shadow-lg bg-gradient-to-r from-green-600 to-green-700 rounded-xl hover:from-green-700 hover:to-green-800 hover:shadow-xl hover:scale-105 focus:outline-none focus:ring-4 focus:ring-green-500/50">
                                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                                </svg>
                                Join Meeting Room
                            </button>

                            <button wire:click="endMeeting"
                                    wire:confirm="Are you sure you want to end this meeting? All participants will be removed."
                                    class="inline-flex items-center gap-2 px-6 py-3 text-sm font-medium text-red-700 transition-all bg-white border-2 border-red-200 rounded-lg hover:bg-red-50 hover:border-red-300 dark:bg-gray-800 dark:text-red-400 dark:border-red-800 dark:hover:bg-gray-700">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                                End Meeting
                            </button>
                        </div>

                        <!-- Meeting Info -->
                        <div class="p-6 mt-10 text-left border border-gray-200 rounded-xl bg-gradient-to-br from-gray-50 to-white dark:from-gray-700 dark:to-gray-800 dark:border-gray-600">
                            <div class="flex items-center gap-2 mb-4">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <p class="text-sm font-semibold text-gray-900 dark:text-white">Meeting Information</p>
                            </div>
                            <div class="space-y-3 text-sm">
                                <div class="flex items-center justify-between p-3 bg-white border border-gray-100 rounded-lg dark:bg-gray-800 dark:border-gray-700">
                                    <span class="font-medium text-gray-500 dark:text-gray-400">Meeting ID</span>
                                    <span class="font-mono font-semibold text-gray-900 dark:text-white">{{ $session->meeting_id }}</span>
                                </div>
                                <div class="flex items-center justify-between p-3 bg-white border border-gray-100 rounded-lg dark:bg-gray-800 dark:border-gray-700">
                                    <span class="font-medium text-gray-500 dark:text-gray-400">Started</span>
                                    <span class="font-semibold text-gray-900 dark:text-white">{{ $session->actual_start?->diffForHumans() }}</span>
                                </div>
                                @if($session->actual_start)
                                    <div class="flex items-center justify-between p-3 bg-white border border-gray-100 rounded-lg dark:bg-gray-800 dark:border-gray-700">
                                        <span class="font-medium text-gray-500 dark:text-gray-400">Duration</span>
                                        <span class="font-semibold text-gray-900 dark:text-white">{{ $session->actual_start->diffInMinutes(now()) }} minutes</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <!-- Enhanced Tips Card -->
        <div class="p-8 mt-6 border border-blue-200 shadow-lg bg-gradient-to-br from-blue-50 via-blue-50 to-indigo-50 rounded-2xl dark:from-blue-900/20 dark:via-blue-900/10 dark:to-indigo-900/20 dark:border-blue-900/50">
            <div class="flex items-center gap-3 mb-5">
                <div class="p-2 bg-blue-100 rounded-lg dark:bg-blue-900/40">
                    <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-blue-900 dark:text-blue-300">Quick Tips for Success</h3>
            </div>
            <ul class="space-y-3">
                <li class="flex items-start gap-3 p-3 transition-all bg-white border border-blue-100 rounded-lg hover:shadow-md dark:bg-blue-900/10 dark:border-blue-800/50">
                    <div class="flex-shrink-0 p-1 mt-0.5 bg-blue-100 rounded dark:bg-blue-900/40">
                        <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <span class="text-sm font-medium text-blue-900 dark:text-blue-300">Test your audio and video before starting the session</span>
                </li>
                <li class="flex items-start gap-3 p-3 transition-all bg-white border border-blue-100 rounded-lg hover:shadow-md dark:bg-blue-900/10 dark:border-blue-800/50">
                    <div class="flex-shrink-0 p-1 mt-0.5 bg-blue-100 rounded dark:bg-blue-900/40">
                        <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <span class="text-sm font-medium text-blue-900 dark:text-blue-300">As moderator, you have full control over the session</span>
                </li>
                <li class="flex items-start gap-3 p-3 transition-all bg-white border border-blue-100 rounded-lg hover:shadow-md dark:bg-blue-900/10 dark:border-blue-800/50">
                    <div class="flex-shrink-0 p-1 mt-0.5 bg-blue-100 rounded dark:bg-blue-900/40">
                        <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <span class="text-sm font-medium text-blue-900 dark:text-blue-300">Students can join once the meeting is live</span>
                </li>
                @if($session->auto_record)
                    <li class="flex items-start gap-3 p-3 transition-all bg-white border border-blue-100 rounded-lg hover:shadow-md dark:bg-blue-900/10 dark:border-blue-800/50">
                        <div class="flex-shrink-0 p-1 mt-0.5 bg-blue-100 rounded dark:bg-blue-900/40">
                            <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <span class="text-sm font-medium text-blue-900 dark:text-blue-300">This session will be automatically recorded</span>
                    </li>
                @endif
                <li class="flex items-start gap-3 p-3 transition-all bg-white border border-blue-100 rounded-lg hover:shadow-md dark:bg-blue-900/10 dark:border-blue-800/50">
                    <div class="flex-shrink-0 p-1 mt-0.5 bg-blue-100 rounded dark:bg-blue-900/40">
                        <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <span class="text-sm font-medium text-blue-900 dark:text-blue-300">Use the share screen feature to present materials</span>
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
