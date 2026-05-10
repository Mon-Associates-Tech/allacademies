<x-layouts.exam>
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="bg-white dark:bg-gray-800 shadow-xl rounded-lg overflow-hidden">
            <div class="bg-gradient-to-r from-indigo-600 to-blue-600 px-8 py-6">
                <h1 class="text-3xl font-bold text-white">{{ $exam->title }}</h1>
                <p class="text-indigo-100 mt-2">{{ $exam->description }}</p>
            </div>

            <div class="p-8 space-y-6">
                <div class="grid md:grid-cols-3 gap-4">
                    <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
                        <div class="flex items-center">
                            <svg class="h-8 w-8 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <div class="ml-3">
                                <p class="text-sm text-gray-600 dark:text-gray-400">Duration</p>
                                <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ $exam->duration_in_minutes ?? 'Unlimited' }} min</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-4">
                        <div class="flex items-center">
                            <svg class="h-8 w-8 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <div class="ml-3">
                                <p class="text-sm text-gray-600 dark:text-gray-400">Sections</p>
                                <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ $exam->sections->count() }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-purple-50 dark:bg-purple-900/20 border border-purple-200 dark:border-purple-800 rounded-lg p-4">
                        <div class="flex items-center">
                            <svg class="h-8 w-8 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <div class="ml-3">
                                <p class="text-sm text-gray-600 dark:text-gray-400">Total Questions</p>
                                <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ $exam->sections->sum('question_count') }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                @if($exam->instructions)
                    <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-6">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-3 flex items-center">
                            <svg class="h-5 w-5 mr-2 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Important Instructions
                        </h2>
                        <div class="text-sm text-gray-700 dark:text-gray-300 whitespace-pre-line">{{ $exam->instructions }}</div>
                    </div>
                @endif

                <div>
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Examination Sections</h2>
                    <div class="space-y-3">
                        @foreach($exam->sections as $index => $section)
                            <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                <div class="flex items-center justify-between">
                                    <div class="flex-1">
                                        <h3 class="font-semibold text-gray-900 dark:text-white">{{ $index + 1 }}. {{ $section->title }}</h3>
                                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">{{ $section->description }}</p>
                                        <div class="flex items-center gap-4 mt-2 text-xs text-gray-500 dark:text-gray-400">
                                            <span>{{ $section->question_count }} questions</span>
                                            @if($section->time_limit_minutes)
                                                <span>• {{ $section->time_limit_minutes }} minutes</span>
                                            @endif
                                        </div>
                                    </div>
                                    <a href="{{ route('examinations-hub.take.section', [$exam, $index]) }}" class="ml-4 px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 text-sm font-medium">
                                        Start Section
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4">
                    <p class="text-sm text-red-700 dark:text-red-400">
                        <strong>Note:</strong> Once you start, the timer will begin. Make sure you're ready before proceeding.
                    </p>
                </div>

                <div class="flex justify-between items-center pt-4">
                    <a href="{{ route('examinations-hub.take.join') }}" class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white">
                        ← Exit
                    </a>
                    <a href="{{ route('examinations-hub.take.section', [$exam, 0]) }}" class="px-6 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 font-medium shadow-lg">
                        Begin Examination
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-layouts.exam>
