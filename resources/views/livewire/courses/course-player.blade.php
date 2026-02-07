<div class="min-h-screen bg-gray-50 dark:bg-gray-900">
    {{-- Top Navigation Bar --}}
    <div class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 sticky top-0 z-40">
        <div class="flex items-center justify-between h-16 px-4">
            <div class="flex items-center gap-4">
                {{-- Back Button --}}
                <a href="{{ route('lms.courses.show', $course->slug) }}" class="flex items-center gap-2 text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    <span class="hidden sm:inline">Back to Course</span>
                </a>

                {{-- Course Title --}}
                <div class="hidden md:block border-l border-gray-200 dark:border-gray-700 pl-4">
                    <h1 class="text-sm font-medium text-gray-900 dark:text-white truncate max-w-md">{{ $course->title }}</h1>
                </div>
            </div>

            <div class="flex items-center gap-4">
                {{-- Progress --}}
                <div class="hidden sm:flex items-center gap-3">
                    <span class="text-sm text-gray-600 dark:text-gray-400">{{ number_format($enrollment->progress_percentage, 0) }}% complete</span>
                    <div class="w-32 h-2 bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden">
                        <div class="h-full bg-indigo-600 rounded-full transition-all duration-300" style="width: {{ $enrollment->progress_percentage }}%"></div>
                    </div>
                </div>

                {{-- Toggle Sidebar --}}
                <button wire:click="toggleSidebar" class="p-2 rounded-lg text-gray-500 hover:text-gray-700 hover:bg-gray-100 dark:text-gray-400 dark:hover:text-gray-200 dark:hover:bg-gray-700 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <div class="flex">
        {{-- Sidebar - Course Structure --}}
        <div class="transition-all duration-300 {{ $sidebarOpen ? 'w-80' : 'w-0' }} flex-shrink-0 overflow-hidden">
            <div class="w-80 h-[calc(100vh-4rem)] bg-white dark:bg-gray-800 border-r border-gray-200 dark:border-gray-700 overflow-y-auto">
                <div class="p-4">
                    <h2 class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-4">
                        Course Content
                    </h2>

                    {{-- Chapters Accordion --}}
                    <div class="space-y-2">
                        @foreach($chapters as $chapter)
                            <div class="border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden" wire:key="sidebar-chapter-{{ $chapter->id }}">
                                {{-- Chapter Header --}}
                                <button
                                    wire:click="toggleChapter({{ $chapter->id }})"
                                    class="w-full flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors"
                                >
                                    <span class="text-sm font-medium text-gray-900 dark:text-white text-left truncate pr-2">
                                        {{ $chapter->title }}
                                    </span>
                                    <svg class="w-4 h-4 text-gray-500 flex-shrink-0 transform transition-transform {{ $selectedChapterId === $chapter->id ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                    </svg>
                                </button>

                                {{-- Sections List --}}
                                @if($selectedChapterId === $chapter->id)
                                    <div class="bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700">
                                        @foreach($chapter->sections()->whereNull('parent_section_id')->orderBy('order')->get() as $section)
                                            @include('livewire.courses.partials.section-item', ['section' => $section, 'depth' => 0])
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        {{-- Main Content Area --}}
        <div class="flex-1 overflow-y-auto h-[calc(100vh-4rem)]">
            @if($currentContent)
                <div class="max-w-4xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
                    {{-- Content Header --}}
                    <div class="mb-6">
                        <div class="flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400 mb-2">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-medium
                                @if($currentContent->type === 'video') bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-300
                                @elseif($currentContent->type === 'audio') bg-purple-100 text-purple-700 dark:bg-purple-900 dark:text-purple-300
                                @elseif($currentContent->type === 'text') bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-300
                                @elseif($currentContent->type === 'quiz') bg-emerald-100 text-emerald-700 dark:bg-emerald-900 dark:text-emerald-300
                                @else bg-amber-100 text-amber-700 dark:bg-amber-900 dark:text-amber-300
                                @endif">
                                {{ ucfirst($currentContent->type) }}
                            </span>
                            @if($currentContent->is_required)
                                <span class="text-red-500">* Required</span>
                            @endif
                        </div>
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">
                            {{ $currentContent->title }}
                        </h2>
                    </div>

                    {{-- Content Display --}}
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden mb-6">
                        @switch($currentContent->type)
                            @case('video')
                                <div class="aspect-video bg-gray-900">
                                    @if($currentContent->getMediaUrl())
                                        <video
                                            id="video-player"
                                            class="w-full h-full"
                                            controls
                                            x-data
                                            x-on:timeupdate="$wire.updateVideoProgress(Math.floor($event.target.currentTime))"
                                        >
                                            <source src="{{ $currentContent->getMediaUrl() }}" type="video/mp4">
                                            Your browser does not support the video tag.
                                        </video>
                                    @else
                                        <div class="w-full h-full flex items-center justify-center">
                                            <p class="text-gray-400">Video not available</p>
                                        </div>
                                    @endif
                                </div>
                                @break

                            @case('audio')
                                <div class="p-8">
                                    <div class="flex items-center justify-center mb-6">
                                        <div class="w-24 h-24 rounded-full bg-purple-100 dark:bg-purple-900 flex items-center justify-center">
                                            <svg class="w-12 h-12 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3"/>
                                            </svg>
                                        </div>
                                    </div>
                                    @if($currentContent->getMediaUrl())
                                        <audio
                                            id="audio-player"
                                            class="w-full"
                                            controls
                                            x-data
                                            x-on:timeupdate="$wire.updateAudioProgress(Math.floor($event.target.currentTime))"
                                        >
                                            <source src="{{ $currentContent->getMediaUrl() }}" type="audio/mpeg">
                                            Your browser does not support the audio tag.
                                        </audio>
                                    @else
                                        <p class="text-center text-gray-400">Audio not available</p>
                                    @endif
                                </div>
                                @break

                            @case('text')
                                <div class="p-6 prose prose-gray dark:prose-invert max-w-none">
                                    {!! nl2br(e($currentContent->content)) !!}
                                </div>
                                <div class="px-6 pb-6">
                                    <button
                                        wire:click="markTextAsRead"
                                        class="w-full flex items-center justify-center gap-2 px-4 py-3 bg-indigo-600 text-white font-medium rounded-lg hover:bg-indigo-700 transition-colors"
                                    >
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                        </svg>
                                        Mark as Read
                                    </button>
                                </div>
                                @break

                            @case('quiz')
                                <div class="p-6">
                                    <div class="text-center py-8">
                                        <div class="w-16 h-16 mx-auto mb-4 bg-emerald-100 dark:bg-emerald-900 rounded-full flex items-center justify-center">
                                            <svg class="w-8 h-8 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                                            </svg>
                                        </div>
                                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Quiz Time!</h3>
                                        <p class="text-gray-600 dark:text-gray-400 mb-6">Test your knowledge with this quiz</p>
                                        <button class="px-6 py-3 bg-emerald-600 text-white font-medium rounded-lg hover:bg-emerald-700 transition-colors">
                                            Start Quiz
                                        </button>
                                    </div>
                                </div>
                                @break

                            @case('feedback')
                                <div class="p-6">
                                    <div class="text-center py-8">
                                        <div class="w-16 h-16 mx-auto mb-4 bg-amber-100 dark:bg-amber-900 rounded-full flex items-center justify-center">
                                            <svg class="w-8 h-8 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                                            </svg>
                                        </div>
                                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Share Your Feedback</h3>
                                        <p class="text-gray-600 dark:text-gray-400 mb-6">We'd love to hear your thoughts on this section</p>
                                        <textarea
                                            class="w-full p-4 border border-gray-200 dark:border-gray-700 rounded-lg bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                            rows="4"
                                            placeholder="Enter your feedback..."
                                        ></textarea>
                                        <button
                                            wire:click="submitFeedback"
                                            class="mt-4 px-6 py-3 bg-amber-600 text-white font-medium rounded-lg hover:bg-amber-700 transition-colors"
                                        >
                                            Submit Feedback
                                        </button>
                                    </div>
                                </div>
                                @break
                        @endswitch
                    </div>

                    {{-- AI Summary (if available) --}}
                    @if($currentContent->ai_summary)
                        <div class="bg-indigo-50 dark:bg-indigo-900/30 rounded-xl border border-indigo-200 dark:border-indigo-800 p-6 mb-6">
                            <div class="flex items-center gap-2 mb-3">
                                <svg class="w-5 h-5 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                                </svg>
                                <h3 class="font-semibold text-indigo-900 dark:text-indigo-300">AI Summary</h3>
                            </div>
                            <p class="text-indigo-800 dark:text-indigo-200 text-sm">
                                {{ is_array($currentContent->ai_summary) ? ($currentContent->ai_summary['summary'] ?? '') : $currentContent->ai_summary }}
                            </p>
                        </div>
                    @endif

                    {{-- Navigation Buttons --}}
                    <div class="flex items-center justify-between">
                        @if($previousContent)
                            <button
                                wire:click="selectContent({{ $previousContent->id }})"
                                class="flex items-center gap-2 px-4 py-2 text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition-colors"
                            >
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                                </svg>
                                Previous
                            </button>
                        @else
                            <div></div>
                        @endif

                        <button
                            wire:click="markContentComplete"
                            class="flex items-center gap-2 px-6 py-2 bg-emerald-600 text-white font-medium rounded-lg hover:bg-emerald-700 transition-colors"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Mark Complete
                        </button>

                        @if($nextContent)
                            <button
                                wire:click="selectContent({{ $nextContent->id }})"
                                class="flex items-center gap-2 px-4 py-2 text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition-colors"
                            >
                                Next
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </button>
                        @else
                            <div></div>
                        @endif
                    </div>
                </div>
            @else
                {{-- No Content Selected --}}
                <div class="flex items-center justify-center h-full">
                    <div class="text-center py-12">
                        <div class="w-16 h-16 mx-auto mb-4 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center">
                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Select a lesson to begin</h3>
                        <p class="text-gray-600 dark:text-gray-400">Choose a lesson from the sidebar to start learning</p>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
