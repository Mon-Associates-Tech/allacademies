<div class="max-w-4xl mx-auto">
    <!-- Header -->
    <div class="bg-gradient-to-r from-indigo-600 to-purple-600 rounded-xl p-8 text-white mb-8 shadow-xl">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-4xl font-bold mb-2">Assessment Center</h1>
                <p class="text-indigo-100 text-lg">Choose your assessment mode and get started</p>
            </div>
            <div class="hidden md:block">
                <div class="w-20 h-20 bg-white/20 rounded-full flex items-center justify-center">
                    <svg class="w-10 h-10" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Mode Selection -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-8 mb-8">
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">Select Assessment Mode</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Self Assessment -->
            <div class="relative">
                <input type="radio" id="self-mode" wire:model.live="assessmentMode" value="self" class="sr-only">
                <label for="self-mode" class="block cursor-pointer">
                    <div class="p-6 rounded-xl border-2 transition-all duration-200 hover:shadow-lg
                        {{ $assessmentMode === 'self' ? 'border-indigo-500 bg-indigo-50 dark:bg-indigo-900/20' : 'border-gray-200 dark:border-gray-600 hover:border-indigo-300' }}">
                        <div class="flex items-center mb-4">
                            <div class="w-12 h-12 bg-indigo-100 dark:bg-indigo-800 rounded-lg flex items-center justify-center mr-4">
                                <svg class="w-6 h-6 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Self Assessment</h3>
                                <p class="text-sm text-gray-600 dark:text-gray-400">Create your own custom assessment</p>
                            </div>
                        </div>
                        <ul class="text-sm text-gray-600 dark:text-gray-400 space-y-1">
                            <li>• Choose subjects and topics</li>
                            <li>• Set question count and difficulty</li>
                            <li>• Optional time limits</li>
                            <li>• Immediate feedback</li>
                        </ul>
                    </div>
                </label>
            </div>

            <!-- Assignment Mode -->
            <div class="relative">
                <input type="radio" id="assignment-mode" wire:model.live="assessmentMode" value="assignment" class="sr-only">
                <label for="assignment-mode" class="block cursor-pointer">
                    <div class="p-6 rounded-xl border-2 transition-all duration-200 hover:shadow-lg
                        {{ $assessmentMode === 'assignment' ? 'border-purple-500 bg-purple-50 dark:bg-purple-900/20' : 'border-gray-200 dark:border-gray-600 hover:border-purple-300' }}">
                        <div class="flex items-center mb-4">
                            <div class="w-12 h-12 bg-purple-100 dark:bg-purple-800 rounded-lg flex items-center justify-center mr-4">
                                <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Assignment</h3>
                                <p class="text-sm text-gray-600 dark:text-gray-400">Complete teacher-assigned assessments</p>
                            </div>
                        </div>
                        <ul class="text-sm text-gray-600 dark:text-gray-400 space-y-1">
                            <li>• Pre-configured by teacher</li>
                            <li>• Structured sections</li>
                            <li>• Timed assessments</li>
                            <li>• Graded submissions</li>
                        </ul>
                    </div>
                </label>
            </div>
        </div>
    </div>

    <!-- Content Based on Mode -->
    @if($assessmentMode === 'self')
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-8">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">Select Subject</h2>
            
            @if($subjects->isEmpty())
                <div class="text-center py-8">
                    <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                    <p class="text-gray-600 dark:text-gray-400">No subjects available</p>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($subjects as $subject)
                        <div class="relative">
                            <input type="radio" id="subject-{{ $subject->id }}" wire:model.live="selectedSubject" value="{{ $subject->id }}" class="sr-only">
                            <label for="subject-{{ $subject->id }}" class="block cursor-pointer">
                                <div class="p-6 rounded-lg border-2 transition-all duration-200 hover:shadow-md
                                    {{ $selectedSubject == $subject->id ? 'border-indigo-500 bg-indigo-50 dark:bg-indigo-900/20' : 'border-gray-200 dark:border-gray-600 hover:border-indigo-300' }}">
                                    <h3 class="font-semibold text-gray-900 dark:text-white mb-2">{{ $subject->name }}</h3>
                                    @if($subject->academicLevel)
                                        <p class="text-sm text-gray-600 dark:text-gray-400">{{ $subject->academicLevel->name }}</p>
                                    @endif
                                </div>
                            </label>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    @else
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-8">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">Available Assignments</h2>
            
            @if($availableAssignments->isEmpty())
                <div class="text-center py-8">
                    <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <p class="text-gray-600 dark:text-gray-400">No assignments available</p>
                </div>
            @else
                <div class="space-y-4">
                    @foreach($availableAssignments as $assignment)
                        <div class="relative">
                            <input type="radio" id="assignment-{{ $assignment->id }}" wire:model.live="selectedAssignment" value="{{ $assignment->id }}" class="sr-only">
                            <label for="assignment-{{ $assignment->id }}" class="block cursor-pointer">
                                <div class="p-6 rounded-lg border-2 transition-all duration-200 hover:shadow-md
                                    {{ $selectedAssignment == $assignment->id ? 'border-purple-500 bg-purple-50 dark:bg-purple-900/20' : 'border-gray-200 dark:border-gray-600 hover:border-purple-300' }}">
                                    <div class="flex justify-between items-start">
                                        <div class="flex-1">
                                            <h3 class="font-semibold text-gray-900 dark:text-white mb-2">{{ $assignment->title }}</h3>
                                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">{{ $assignment->academicSubject->name }}</p>
                                            @if($assignment->description)
                                                <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">{{ Str::limit($assignment->description, 100) }}</p>
                                            @endif
                                            <div class="flex items-center space-x-4 text-sm text-gray-500 dark:text-gray-400">
                                                <span>{{ $assignment->teacher->user->name }}</span>
                                                @if($assignment->duration_in_minutes)
                                                    <span>• {{ $assignment->duration_in_minutes }} minutes</span>
                                                @endif
                                                <span>• Due: {{ $assignment->ends_at->format('M d, Y H:i') }}</span>
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400">
                                                Active
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </label>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    @endif

    <!-- Continue Button -->
    <div class="mt-8 flex justify-end">
        <button wire:click="proceedToConfiguration" 
                class="px-8 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-lg transition-colors duration-200 disabled:opacity-50 disabled:cursor-not-allowed"
                {{ ($assessmentMode === 'self' && !$selectedSubject) || ($assessmentMode === 'assignment' && !$selectedAssignment) ? 'disabled' : '' }}>
            Continue to Configuration
            <svg class="w-5 h-5 ml-2 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
            </svg>
        </button>
    </div>
</div>
