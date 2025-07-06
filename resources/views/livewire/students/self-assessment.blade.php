<div>
    @if($step === 'setup')
        <div class="max-w-5xl mx-auto space-y-8">
            <!-- Enhanced Header Section -->
            <div class="bg-gradient-to-r from-indigo-500 via-purple-600 to-pink-600 rounded-xl p-8 text-white shadow-lg relative overflow-hidden">
                <div class="absolute top-0 right-0 -mt-4 -mr-4 w-24 h-24 bg-white/10 rounded-full blur-2xl"></div>
                <div class="absolute bottom-0 left-0 -mb-4 -ml-4 w-32 h-32 bg-white/10 rounded-full blur-2xl"></div>

                <div class="relative flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <div class="p-4 bg-white/20 backdrop-blur-sm rounded-full">
                            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <div>
                            <h1 class="text-4xl font-bold">Create Assessment</h1>
                            <p class="text-indigo-100 mt-2 text-lg">Design your personalized learning assessment</p>
                        </div>
                    </div>

                    <!-- Progress Indicator -->
                    <div class="hidden lg:block text-center bg-white/10 backdrop-blur-sm rounded-xl p-4">
                        <div class="text-2xl font-bold text-yellow-300">Step 1</div>
                        <div class="text-sm text-indigo-200">Setup</div>
                    </div>
                </div>
            </div>

            <!-- Enhanced Form Container -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
                <!-- Progress Bar -->
                <div class="h-2 bg-gray-200 dark:bg-gray-700">
                    <div class="h-2 bg-gradient-to-r from-indigo-500 to-purple-600 rounded-full" style="width: 33%"></div>
                </div>

                <div class="p-8 space-y-10">
                    <!-- Assessment Mode Selection -->
                    <div class="space-y-6">
                        <div class="flex items-center space-x-3">
                            <div class="p-3 bg-emerald-100 dark:bg-emerald-900/30 rounded-xl">
                                <svg class="w-6 h-6 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                                </svg>
                            </div>
                            <div>
                                <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Assessment Mode</h2>
                                <p class="text-gray-600 dark:text-gray-400">Choose how you want to create your assessment</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <button wire:click="switchAssessmentMode('self')"
                                    class="p-6 border-2 rounded-xl text-left transition-all transform hover:scale-105
                                           {{ $assessmentMode === 'self' ? 'border-indigo-500 bg-indigo-50 dark:bg-indigo-900/20 shadow-lg' : 'border-gray-300 hover:border-gray-400 hover:shadow-md' }}">
                                <div class="flex items-center space-x-4">
                                    <div class="p-3 bg-blue-100 dark:bg-blue-900/30 rounded-xl">
                                        <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                    </div>
                                    <div class="flex-1">
                                        <h4 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Self Assessment</h4>
                                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Create your own custom assessment with personalized settings</p>
                                        <div class="flex items-center space-x-2 mt-2">
                                            <span class="px-2 py-1 bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 text-xs rounded-full">Custom</span>
                                            <span class="px-2 py-1 bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200 text-xs rounded-full">Flexible</span>
                                        </div>
                                    </div>
                                </div>
                            </button>

                            <button wire:click="switchAssessmentMode('assignment')"
                                    class="p-6 border-2 rounded-xl text-left transition-all transform hover:scale-105
                                           {{ $assessmentMode === 'assignment' ? 'border-indigo-500 bg-indigo-50 dark:bg-indigo-900/20 shadow-lg' : 'border-gray-300 hover:border-gray-400 hover:shadow-md' }}">
                                <div class="flex items-center space-x-4">
                                    <div class="p-3 bg-green-100 dark:bg-green-900/30 rounded-xl">
                                        <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                                        </svg>
                                    </div>
                                    <div class="flex-1">
                                        <h4 class="text-lg font-semibold text-gray-900 dark:text-gray-100">From Assignment</h4>
                                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Practice with teacher-created assignments and structured content</p>
                                        <div class="flex items-center space-x-2 mt-2">
                                            <span class="px-2 py-1 bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200 text-xs rounded-full">Structured</span>
                                            <span class="px-2 py-1 bg-purple-100 dark:bg-purple-900 text-purple-800 dark:text-purple-200 text-xs rounded-full">Teacher-Created</span>
                                        </div>
                                    </div>
                                </div>
                            </button>
                        </div>
                    </div>

                    @if($assessmentMode === 'assignment')
                        <!-- Assignment Selection -->
                        <div class="space-y-6">
                            <div class="flex items-center space-x-3">
                                <div class="p-3 bg-purple-100 dark:bg-purple-900/30 rounded-xl">
                                    <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                    </svg>
                                </div>
                                <div>
                                    <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Select Assignment</h2>
                                    <p class="text-gray-600 dark:text-gray-400">Choose from available assignments to practice</p>
                                </div>
                            </div>

                            @if($availableAssignments && $availableAssignments->count() > 0)
                                <div class="grid gap-4 max-h-96 overflow-y-auto">
                                    @foreach($availableAssignments as $assignment)
                                        <label class="flex items-start p-6 border-2 rounded-xl cursor-pointer transition-all hover:shadow-md
                                                      {{ $selectedAssignment == $assignment->id ? 'border-indigo-500 bg-indigo-50 dark:bg-indigo-900/20' : 'border-gray-300 hover:border-gray-400' }}">
                                            <input type="radio" wire:model="selectedAssignment" value="{{ $assignment->id }}" class="mt-1 mr-4 text-indigo-600 focus:ring-indigo-500">
                                            <div class="flex-1">
                                                <div class="flex items-start justify-between">
                                                    <div class="flex-1">
                                                        <h4 class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $assignment->title }}</h4>
                                                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">{{ $assignment->description ?? 'No description available' }}</p>
                                                    </div>
                                                    <div class="ml-4 flex-shrink-0">
                                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium
                                                                    {{ $assignment->type === 'quiz' ? 'bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200' : 'bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200' }}">
                                                            {{ ucfirst($assignment->type) }}
                                                        </span>
                                                    </div>
                                                </div>

                                                <div class="mt-3 grid grid-cols-2 sm:grid-cols-4 gap-4 text-sm">
                                                    <div class="flex items-center space-x-2">
                                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                                        </svg>
                                                        <span class="text-gray-600 dark:text-gray-400">{{ $assignment->academicSubject->name ?? 'Unknown Subject' }}</span>
                                                    </div>
                                                    <div class="flex items-center space-x-2">
                                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                        </svg>
                                                        <span class="text-gray-600 dark:text-gray-400">{{ $assignment->duration_in_minutes }} min</span>
                                                    </div>
                                                    <div class="flex items-center space-x-2">
                                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                                                        </svg>
                                                        <span class="text-gray-600 dark:text-gray-400">{{ $assignment->total_marks }} marks</span>
                                                    </div>
                                                    <div class="flex items-center space-x-2">
                                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                                        </svg>
                                                        <span class="text-gray-600 dark:text-gray-400">Due: {{ $assignment->ends_at->format('M j, g:i A') }}</span>
                                                    </div>
                                                </div>

                                                @if($assignment->instructions)
                                                    <div class="mt-3 p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                                                        <p class="text-sm text-gray-600 dark:text-gray-400">
                                                            <strong>Instructions:</strong> {{ Str::limit($assignment->instructions, 150) }}
                                                        </p>
                                                    </div>
                                                @endif
                                            </div>
                                        </label>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-12">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                    </svg>
                                    <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">No assignments available</h3>
                                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">There are no assignments available for practice at the moment.</p>
                                </div>
                            @endif
                        </div>
                    @else
                        <!-- Self Assessment Configuration -->
                        <div class="space-y-8">
                            <!-- Content Selection Section -->
                            <div class="space-y-6">
                                <div class="flex items-center space-x-3">
                                    <div class="p-3 bg-blue-100 dark:bg-blue-900/30 rounded-xl">
                                        <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Content Selection</h2>
                                        <p class="text-gray-600 dark:text-gray-400">Choose what you want to be assessed on</p>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                                    <!-- Subject Selection -->
                                    <div class="space-y-3">
                                        <label class="flex items-center space-x-2 text-sm font-semibold text-gray-700 dark:text-gray-300">
                                            <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                            </svg>
                                            <span>Subject <span class="text-red-500">*</span></span>
                                        </label>
                                        <div class="relative">
                                            <select id="subject" wire:model.live="selectedSubject"
                                                    class="w-full pl-4 pr-10 py-4 rounded-xl border-2 border-gray-200 dark:border-gray-600 dark:bg-gray-700 bg-gray-50 dark:bg-gray-700/50 text-gray-900 dark:text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 dark:focus:ring-indigo-600 focus:ring-opacity-50 transition-all duration-200">
                                                <option value="">📚 Choose a subject</option>
                                                @foreach($subjects as $subject)
                                                    <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                                                @endforeach
                                            </select>
                                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3">
                                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                                </svg>
                                            </div>
                                        </div>
                                        @error('selectedSubject')
                                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <!-- Topic Selection -->
                                    <div class="space-y-3">
                                        <label class="flex items-center space-x-2 text-sm font-semibold text-gray-700 dark:text-gray-300">
                                            <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707L13.414 3.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                            </svg>
                                            <span>Topic (Optional)</span>
                                        </label>
                                        <div class="relative">
                                            <select id="topic" wire:model.live="selectedTopic"
                                                    class="w-full pl-4 pr-10 py-4 rounded-xl border-2 border-gray-200 dark:border-gray-600 dark:bg-gray-700 bg-gray-50 dark:bg-gray-700/50 text-gray-900 dark:text-gray-100 shadow-sm focus:border-green-500 focus:ring-2 focus:ring-green-200 dark:focus:ring-green-600 focus:ring-opacity-50 transition-all duration-200 {{ !$selectedSubject ? 'opacity-50 cursor-not-allowed' : '' }}"
                                                {{ !$selectedSubject ? 'disabled' : '' }}>
                                                <option value="">🔍 Choose a topic (optional)</option>
                                                @if(isset($topics))
                                                    @foreach($topics as $topic)
                                                        <option value="{{ $topic->id }}">{{ $topic->name }}</option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        </div>
                                    </div>

                                    <!-- Subtopic Selection -->
                                    <div class="space-y-3">
                                        <label class="flex items-center space-x-2 text-sm font-semibold text-gray-700 dark:text-gray-300">
                                            <svg class="w-4 h-4 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                            </svg>
                                            <span>Subtopic (Optional)</span>
                                        </label>
                                        <div class="relative">
                                            <select id="subtopic" wire:model.live="selectedSubtopic"
                                                    class="w-full pl-4 pr-10 py-4 rounded-xl border-2 border-gray-200 dark:border-gray-600 dark:bg-gray-700 bg-gray-50 dark:bg-gray-700/50 text-gray-900 dark:text-gray-100 shadow-sm focus:border-purple-500 focus:ring-2 focus:ring-purple-200 dark:focus:ring-purple-600 focus:ring-opacity-50 transition-all duration-200 {{ !$selectedTopic ? 'opacity-50 cursor-not-allowed' : '' }}"
                                                {{ !$selectedTopic ? 'disabled' : '' }}>
                                                <option value="">🎯 Choose a subtopic (optional)</option>
                                                @if(isset($subtopics))
                                                    @foreach($subtopics as $subtopic)
                                                        <option value="{{ $subtopic->id }}">{{ $subtopic->name }}</option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Assessment Configuration Section -->
                            <div class="space-y-6">
                                <div class="flex items-center space-x-3">
                                    <div class="p-3 bg-orange-100 dark:bg-orange-900/30 rounded-xl">
                                        <svg class="w-6 h-6 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Assessment Configuration</h2>
                                        <p class="text-gray-600 dark:text-gray-400">Customize your assessment parameters</p>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                                    <!-- Question Types -->
                                    <div class="space-y-4">
                                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Question Types</h3>
                                        <div class="space-y-3">
                                            <label class="flex items-center p-4 bg-gray-50 dark:bg-gray-700/50 rounded-xl border border-gray-200 dark:border-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors cursor-pointer">
                                                <input type="checkbox" wire:model="questionTypes.multiple_choice_question"
                                                       class="h-5 w-5 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                                                <div class="ml-4 flex-1">
                                                    <div class="flex items-center space-x-2">
                                                        <span class="text-2xl">🔘</span>
                                                        <span class="font-medium text-gray-900 dark:text-gray-100">Multiple Choice</span>
                                                    </div>
                                                    <p class="text-sm text-gray-600 dark:text-gray-400">Questions with multiple answer options</p>
                                                </div>
                                            </label>

                                            <label class="flex items-center p-4 bg-gray-50 dark:bg-gray-700/50 rounded-xl border border-gray-200 dark:border-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors cursor-pointer">
                                                <input type="checkbox" wire:model="questionTypes.true_or_false_question"
                                                       class="h-5 w-5 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                                                <div class="ml-4 flex-1">
                                                    <div class="flex items-center space-x-2">
                                                        <span class="text-2xl">✅</span>
                                                        <span class="font-medium text-gray-900 dark:text-gray-100">True or False</span>
                                                    </div>
                                                    <p class="text-sm text-gray-600 dark:text-gray-400">Simple true/false questions</p>
                                                </div>
                                            </label>

                                            <label class="flex items-center p-4 bg-gray-50 dark:bg-gray-700/50 rounded-xl border border-gray-200 dark:border-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors cursor-pointer">
                                                <input type="checkbox" wire:model="questionTypes.essay_question"
                                                       class="h-5 w-5 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                                                <div class="ml-4 flex-1">
                                                    <div class="flex items-center space-x-2">
                                                        <span class="text-2xl">📝</span>
                                                        <span class="font-medium text-gray-900 dark:text-gray-100">Essay Questions</span>
                                                    </div>
                                                    <p class="text-sm text-gray-600 dark:text-gray-400">Open-ended written responses</p>
                                                </div>
                                            </label>
                                        </div>
                                    </div>

                                    <!-- Assessment Parameters -->
                                    <div class="space-y-4">
                                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Parameters</h3>
                                        <div class="space-y-4">
                                            <!-- Number of Questions -->
                                            <div>
                                                <label for="questionCount" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                                    Number of Questions
                                                </label>
                                                <div class="relative">
                                                    <input type="number" id="questionCount" wire:model="questionCount" min="1" max="50"
                                                           class="w-full pl-4 pr-16 py-3 rounded-xl border-2 border-gray-200 dark:border-gray-600 dark:bg-gray-700 bg-gray-50 dark:bg-gray-700/50 text-gray-900 dark:text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 dark:focus:ring-indigo-600 focus:ring-opacity-50 transition-all duration-200">
                                                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                                        <span class="text-gray-400 text-sm">questions</span>
                                                    </div>
                                                </div>
                                                @error('questionCount')
                                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                                @enderror
                                            </div>

                                            <!-- Difficulty Level -->
                                            <div>
                                                <label for="difficulty" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                                    Difficulty Level
                                                </label>
                                                <select id="difficulty" wire:model="difficulty"
                                                        class="w-full pl-4 pr-10 py-3 rounded-xl border-2 border-gray-200 dark:border-gray-600 dark:bg-gray-700 bg-gray-50 dark:bg-gray-700/50 text-gray-900 dark:text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 dark:focus:ring-indigo-600 focus:ring-opacity-50 transition-all duration-200">
                                                    <option value="all">🎯 All Levels</option>
                                                    <option value="easy">🟢 Easy</option>
                                                    <option value="medium">🟡 Medium</option>
                                                    <option value="hard">🔴 Hard</option>
                                                </select>
                                            </div>

                                            <!-- Time Limit -->
                                            <div>
                                                <label for="timeLimitMinutes" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                                    Time Limit (Optional)
                                                </label>
                                                <div class="relative">
                                                    <input type="number" id="timeLimitMinutes" wire:model="timeLimitMinutes" min="5" max="180"
                                                           class="w-full pl-4 pr-16 py-3 rounded-xl border-2 border-gray-200 dark:border-gray-600 dark:bg-gray-700 bg-gray-50 dark:bg-gray-700/50 text-gray-900 dark:text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 dark:focus:ring-indigo-600 focus:ring-opacity-50 transition-all duration-200"
                                                           placeholder="No limit">
                                                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                                        <span class="text-gray-400 text-sm">minutes</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Action Buttons -->
                    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center space-y-4 sm:space-y-0 pt-6 border-t border-gray-200 dark:border-gray-700">
                        <div class="text-sm text-gray-600 dark:text-gray-400">
                            @if($assessmentMode === 'self')
                                <span class="font-medium">Note:</span> Select at least one question type to proceed
                            @else
                                <span class="font-medium">Note:</span> Select an assignment to start practicing
                            @endif
                        </div>

                        <div class="flex space-x-4">
                            <button type="button" wire:click="resetForm"
                                    class="inline-flex items-center px-6 py-3 border border-gray-300 dark:border-gray-600 shadow-sm text-sm font-medium rounded-xl text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                </svg>
                                Reset
                            </button>

                            <button wire:click="startAssessment"
                                    class="inline-flex items-center px-8 py-3 border border-transparent text-sm font-medium rounded-xl text-white bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 shadow-lg transform hover:scale-105 transition-all duration-200">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                </svg>
                                Start Assessment
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if($step === 'assessment')
        <div class="max-w-5xl mx-auto space-y-6">
            <!-- Assessment Header -->
            <div class="bg-gradient-to-r from-blue-500 via-indigo-600 to-purple-600 rounded-xl p-6 text-white shadow-lg">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <div class="p-3 bg-white/20 backdrop-blur-sm rounded-full">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <div>
                            <h1 class="text-2xl font-bold">Assessment in Progress</h1>
                            <p class="text-blue-100">Question {{ $currentQuestionIndex + 1 }} of {{ count($questions) }}</p>
                        </div>
                    </div>

                    @if($timeLimitSeconds > 0)
                        <div class="text-center bg-white/10 backdrop-blur-sm rounded-xl p-4"
                             x-data="{ time: {{ $timeRemaining }}, intervalId: null }"
                             x-init="
                                 if (time > 0) {
                                     intervalId = setInterval(() => {
                                         if (time > 0) {
                                             time--;
                                         } else {
                                             clearInterval(intervalId);
                                             @this.completeAssessment();
                                         }
                                     }, 1000);
                                 }
                             ">
                            <div class="text-lg font-bold text-yellow-300" x-text="Math.floor(time / 60) + ':' + (time % 60).toString().padStart(2, '0')"></div>
                            <div class="text-xs text-blue-200">Time Remaining</div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Progress Bar -->
            <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-lg">
                <div class="flex items-center justify-between mb-4">
                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Progress</span>
                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ round((($currentQuestionIndex + 1) / count($questions)) * 100) }}%</span>
                </div>
                <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-3">
                    <div class="bg-gradient-to-r from-blue-500 to-purple-600 h-3 rounded-full transition-all duration-300"
                         style="width: {{ (($currentQuestionIndex + 1) / count($questions)) * 100 }}%"></div>
                </div>
            </div>

            <!-- Current Question -->
            @if(isset($questions[$currentQuestionIndex]))
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden">
                    <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                        <div class="flex items-center justify-between mb-4">
                            <span class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                Question {{ $currentQuestionIndex + 1 }} of {{ count($questions) }}
                            </span>
                            @if(isset($questions[$currentQuestionIndex]['question_record']['questionable']->difficulty_level))
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium
                                    @if($questions[$currentQuestionIndex]['question_record']['questionable']->difficulty_level === 'easy')
                                        bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                    @elseif($questions[$currentQuestionIndex]['question_record']['questionable']->difficulty_level === 'medium')
                                        bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200
                                    @elseif($questions[$currentQuestionIndex]['question_record']['questionable']->difficulty_level === 'hard')
                                        bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200
                                    @endif">
                                    {{ ucfirst($questions[$currentQuestionIndex]['question_record']['questionable']->difficulty_level) }}
                                </span>
                            @endif
                        </div>
                        <div class="flex mb-3 flex-wrap gap-2">
                            @foreach($questions as $index => $question)
                                <button wire:click="jumpToQuestion({{ $index }})"
                                        class="w-10 h-10 flex items-center justify-center rounded-lg text-sm font-medium transition-all transform hover:scale-105
                                @if($currentQuestionIndex === $index)
                                    bg-indigo-600 text-white shadow-lg
                                @elseif(isset($responses[$index]) && $responses[$index] !== null && $responses[$index] !== '')
                                    bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200 hover:bg-green-200
                                @else
                                    bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200 hover:bg-gray-200 dark:hover:bg-gray-600
                                @endif">
                                    {{ $index + 1 }}
                                </button>
                            @endforeach
                        </div>

                        <h4 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-6">
                            {!! $questions[$currentQuestionIndex]['question_record']['questionable']->question->down !!}
                        </h4>

                        @php
                            $currentQuestion = $questions[$currentQuestionIndex]['question_record']['questionable'];
                            $type = class_basename(get_class($currentQuestion));
                        @endphp

                        <div class="space-y-4">
                            @if ($type === 'MultipleChoiceQuestion')
                                @php
                                    $options = [];
                                    foreach(['a', 'b', 'c', 'd', 'e'] as $letter) {
                                        if (!empty($currentQuestion->{'option_'.$letter})) {
                                            $options[] = ['label' => strtoupper($letter), 'value' => $currentQuestion->{'option_'.$letter}];
                                        }
                                    }
                                @endphp

                                @foreach ($options as $option)
                                    <label class="flex items-start p-4 border-2 rounded-xl cursor-pointer transition-all hover:shadow-md
                                                  {{ isset($responses[$currentQuestionIndex]) && $responses[$currentQuestionIndex] === $option['label'] ? 'border-indigo-500 bg-indigo-50 dark:bg-indigo-900/20' : 'border-gray-200 dark:border-gray-600 hover:border-gray-300' }}">
                                        <input type="radio"
                                               name="response_{{ $currentQuestionIndex }}"
                                               value="{{ $option['label'] }}"
                                               wire:key="response_{{ $currentQuestionIndex }}_{{ $option['label'] }}"
                                               wire:click="saveResponse({{ $currentQuestionIndex }}, '{{ $option['label'] }}')"
                                               @if (isset($responses[$currentQuestionIndex]) && $responses[$currentQuestionIndex] === $option['label']) checked @endif
                                               class="mt-1 mr-4 text-indigo-600 focus:ring-indigo-500">
                                        <div class="flex-1">
                                            <span class="font-medium text-gray-900 dark:text-gray-100">{{ $option['label'] }}.</span>
                                            <span class="ml-2 text-gray-700 dark:text-gray-300">{{ $option['value']->down }}</span>
                                        </div>
                                    </label>
                                @endforeach

                            @elseif ($type === 'TrueOrFalseQuestion')
                                <div class="space-y-3">
                                    <label class="flex items-center p-4 border-2 rounded-xl cursor-pointer transition-all hover:shadow-md
                                                  {{ isset($responses[$currentQuestionIndex]) && $responses[$currentQuestionIndex] === 'true' ? 'border-indigo-500 bg-indigo-50 dark:bg-indigo-900/20' : 'border-gray-200 dark:border-gray-600 hover:border-gray-300' }}">
                                        <input type="radio"
                                               name="response_{{ $currentQuestionIndex }}"
                                               value="true"
                                               wire:key="response_{{ $currentQuestionIndex }}_true"
                                               wire:click="saveResponse({{ $currentQuestionIndex }}, 'true')"
                                               @if (isset($responses[$currentQuestionIndex]) && $responses[$currentQuestionIndex] === 'true') checked @endif
                                               class="mr-4 text-indigo-600 focus:ring-indigo-500">
                                        <span class="text-lg font-medium text-gray-900 dark:text-gray-100">✅ True</span>
                                    </label>

                                    <label class="flex items-center p-4 border-2 rounded-xl cursor-pointer transition-all hover:shadow-md
                                                  {{ isset($responses[$currentQuestionIndex]) && $responses[$currentQuestionIndex] === 'false' ? 'border-indigo-500 bg-indigo-50 dark:bg-indigo-900/20' : 'border-gray-200 dark:border-gray-600 hover:border-gray-300' }}">
                                        <input type="radio"
                                               name="response_{{ $currentQuestionIndex }}"
                                               value="false"
                                               wire:key="response_{{ $currentQuestionIndex }}_false"
                                               wire:click="saveResponse({{ $currentQuestionIndex }}, 'false')"
                                               @if (isset($responses[$currentQuestionIndex]) && $responses[$currentQuestionIndex] === 'false') checked @endif
                                               class="mr-4 text-indigo-600 focus:ring-indigo-500">
                                        <span class="text-lg font-medium text-gray-900 dark:text-gray-100">❌ False</span>
                                    </label>
                                </div>

                            @elseif ($type === 'EssayQuestion')
                                <div class="space-y-4">
                                    <textarea
                                        wire:model.lazy="responses.{{ $currentQuestionIndex }}"
                                        class="w-full h-40 p-4 rounded-xl border-2 border-gray-200 dark:border-gray-600 dark:bg-gray-700 bg-gray-50 text-gray-900 dark:text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 dark:focus:ring-indigo-600 focus:ring-opacity-50 transition-all duration-200 resize-none"
                                        placeholder="Write your detailed answer here..."
                                        wire:key="response_{{ $currentQuestionIndex }}_essay"
                                        rows="8">{{ $responses[$currentQuestionIndex] ?? '' }}</textarea>
                                    <div class="text-sm text-gray-500 dark:text-gray-400">
                                        💡 Take your time to provide a comprehensive answer. This will be manually reviewed.
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Navigation Footer -->
                    <div class="px-6 py-4 bg-gray-50 dark:bg-gray-700/50 flex justify-between items-center">
                        <button wire:click="previousQuestion"
                                class="inline-flex items-center px-6 py-3 border border-gray-300 dark:border-gray-600 shadow-sm text-sm font-medium rounded-xl text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors {{ $currentQuestionIndex === 0 ? 'opacity-50 cursor-not-allowed' : '' }}"
                            {{ $currentQuestionIndex === 0 ? 'disabled' : '' }}>
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                            </svg>
                            Previous
                        </button>

                        <div class="flex items-center space-x-2 text-sm text-gray-500 dark:text-gray-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span>Your progress is automatically saved</span>
                        </div>

                        <button wire:click="{{ $currentQuestionIndex < count($questions) - 1 ? 'nextQuestion' : 'completeAssessment' }}"
                                class="inline-flex items-center px-6 py-3 border border-transparent text-sm font-medium rounded-xl text-white
                                       {{ $currentQuestionIndex < count($questions) - 1 ? 'bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700' : 'bg-gradient-to-r from-green-600 to-emerald-600 hover:from-green-700 hover:to-emerald-700' }}
                                       focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 shadow-lg transform hover:scale-105 transition-all duration-200">
                            {{ $currentQuestionIndex < count($questions) - 1 ? 'Next Question' : 'Complete Assessment' }}
                            <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                @if($currentQuestionIndex < count($questions) - 1)
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                @else
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                @endif
                            </svg>
                        </button>
                    </div>
                </div>
            @endif
        </div>
    @endif

    @if($step === 'results')
        <div class="max-w-5xl mx-auto space-y-8">
            <!-- Results Header -->
            <div class="bg-gradient-to-r from-green-500 via-emerald-600 to-teal-600 rounded-xl p-8 text-white shadow-lg relative overflow-hidden">
                <div class="absolute top-0 right-0 -mt-4 -mr-4 w-24 h-24 bg-white/10 rounded-full blur-2xl"></div>
                <div class="absolute bottom-0 left-0 -mb-4 -ml-4 w-32 h-32 bg-white/10 rounded-full blur-2xl"></div>

                <div class="relative text-center">
                    <div class="p-4 bg-white/20 backdrop-blur-sm rounded-full w-20 h-20 mx-auto mb-4 flex items-center justify-center">
                        <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                        </svg>
                    </div>
                    <h1 class="text-4xl font-bold mb-2">Assessment Complete!</h1>
                    <p class="text-green-100 text-lg">Congratulations on completing your assessment</p>
                </div>
            </div>

            @if($result)
                <!-- Score Summary -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xl p-8">
                    <div class="text-center mb-8">
                        <h2 class="text-3xl font-bold text-gray-900 dark:text-gray-100 mb-4">Your Results</h2>
                        <div class="inline-flex items-center justify-center w-32 h-32 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full text-white text-3xl font-bold mb-4">
                            {{ $result['percentage_score'] }}%
                        </div>
                        <p class="text-xl text-gray-600 dark:text-gray-400">
                            You scored {{ $result['score'] }} out of {{ $result['total_score'] }} points
                        </p>
                    </div>

                    <!-- Performance Breakdown -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                        <div class="text-center p-6 bg-green-50 dark:bg-green-900/20 rounded-xl">
                            <div class="text-3xl font-bold text-green-600 dark:text-green-400">{{ $result['correct_answers'] }}</div>
                            <div class="text-sm text-green-700 dark:text-green-300">Correct Answers</div>
                        </div>
                        <div class="text-center p-6 bg-red-50 dark:bg-red-900/20 rounded-xl">
                            <div class="text-3xl font-bold text-red-600 dark:text-red-400">{{ $result['incorrect_answers'] }}</div>
                            <div class="text-sm text-red-700 dark:text-red-300">Incorrect Answers</div>
                        </div>
                        <div class="text-center p-6 bg-blue-50 dark:bg-blue-900/20 rounded-xl">
                            <div class="text-3xl font-bold text-blue-600 dark:text-blue-400">{{ $result['total_questions'] }}</div>
                            <div class="text-sm text-blue-700 dark:text-blue-300">Total Questions</div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex flex-col sm:flex-row gap-4 justify-center">
                        <button wire:click="resetAssessment"
                                class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-xl text-white bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 shadow-lg transform hover:scale-105 transition-all duration-200">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                            </svg>
                            Take Another Assessment
                        </button>
                        <button wire:click="reviewAnswers"
                                class="inline-flex items-center px-6 py-3 border border-gray-300 dark:border-gray-600 text-base font-medium rounded-xl text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 shadow-lg transition-colors">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            Review Answers
                        </button>
                    </div>
                </div>
            @endif
        </div>
    @endif

    <!-- Recent Assessments Section -->
    <div class="max-w-5xl mx-auto mt-12">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xl overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Recent Assessments</h3>
            </div>

            @if($this->recentAssessments && count($this->recentAssessments) > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-100 dark:bg-gray-700">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Subject</th>
                            <th scope="col" class="hidden md:table-cell px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Topic</th>
                            <th scope="col" class="hidden md:table-cell px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Date</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Score</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                        </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($this->recentAssessments as $assessment)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                        {{ $assessment->subject->name ?? 'N/A' }}
                                    </div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400 md:hidden">
                                        {{ $assessment->created_at->format('M j, Y') }}
                                    </div>
                                </td>
                                <td class="hidden md:table-cell px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                    {{ $assessment->topic->name ?? 'General' }}
                                </td>
                                <td class="hidden md:table-cell px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                    {{ $assessment->created_at->format('M j, Y g:i A') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($assessment->percentage_score)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                @if($assessment->percentage_score >= 80) bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                                @elseif($assessment->percentage_score >= 60) bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200
                                                @else bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200 @endif">
                                                {{ $assessment->percentage_score }}%
                                            </span>
                                    @else
                                        <span class="text-sm text-gray-500 dark:text-gray-400">N/A</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                            @if($assessment->status === 'completed') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                            @elseif($assessment->status === 'in_progress') bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200
                                            @else bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200 @endif">
                                            {{ ucfirst($assessment->status) }}
                                        </span>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>

                @if($this->recentAssessments->hasPages())
                    <div class="px-6 py-3 border-t border-gray-200 dark:border-gray-700">
                        {{ $this->recentAssessments->links() }}
                    </div>
                @endif
            @else
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">No assessments yet</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Start your first assessment to see your progress here.</p>
                </div>
            @endif
        </div>
    </div>
</div>
