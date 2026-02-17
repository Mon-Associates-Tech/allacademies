<div class="py-6">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-6">
            <a href="{{ route('teachers.public-assignments.index') }}" class="text-sm text-indigo-600 hover:text-indigo-700 dark:text-indigo-400 flex items-center gap-1 mb-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                Back to Assignments
            </a>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Create Public Assignment</h1>
        </div>

        <!-- Progress Steps -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                @foreach([1 => 'Basic Info', 2 => 'Time & Config', 3 => 'Settings', 4 => 'Questions', 5 => 'Review'] as $step => $label)
                    <div class="flex items-center {{ $step < 5 ? 'flex-1' : '' }}">
                        <button wire:click="goToStep({{ $step }})"
                                class="flex items-center justify-center w-10 h-10 rounded-full text-sm font-medium transition-all
                                    {{ $currentStep >= $step ? 'bg-indigo-600 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-500' }}
                                    {{ $currentStep === $step ? 'ring-4 ring-indigo-200 dark:ring-indigo-900' : '' }}">
                            {{ $step }}
                        </button>
                        @if($step < 5)
                            <div class="flex-1 h-1 mx-2 {{ $currentStep > $step ? 'bg-indigo-600' : 'bg-gray-200 dark:bg-gray-700' }}"></div>
                        @endif
                    </div>
                @endforeach
            </div>
            <div class="flex justify-between mt-2 text-xs text-gray-500 dark:text-gray-400">
                <span>Basic Info</span>
                <span>Time</span>
                <span>Settings</span>
                <span>Questions</span>
                <span>Review</span>
            </div>
        </div>

        <!-- Flash Messages -->
        @if(session('success'))
            <div class="mb-6 p-4 bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800 rounded-lg text-green-700 dark:text-green-300">
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="mb-6 p-4 bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 rounded-lg text-red-700 dark:text-red-300">
                {{ session('error') }}
            </div>
        @endif

        <!-- Step Content -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6">
            <!-- Step 1: Basic Information -->
            @if($currentStep === 1)
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-6">Basic Information</h2>
                <div class="space-y-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Title *</label>
                        <input type="text" wire:model="title" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white" placeholder="e.g., Midterm Exam - Chapter 5">
                        @error('title') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Description</label>
                        <textarea wire:model="description" rows="3" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white" placeholder="Brief description of the assignment..."></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Type *</label>
                        <select wire:model="type" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white">
                            <option value="quiz">Quiz</option>
                            <option value="examination">Examination</option>
                            <option value="practice">Practice</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Instructions</label>
                        <textarea wire:model="instructions" rows="4" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white" placeholder="Instructions for participants..."></textarea>
                    </div>
                </div>
            @endif

            <!-- Step 2: Time & Configuration -->
            @if($currentStep === 2)
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-6">Time & Configuration</h2>
                <div class="space-y-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Duration (minutes)</label>
                        <input type="number" wire:model="duration_in_minutes" min="5" max="480" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white" placeholder="60">
                        <p class="mt-1 text-sm text-gray-500">Leave empty for no time limit</p>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Start Date/Time</label>
                            <input type="datetime-local" wire:model="starts_at" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">End Date/Time</label>
                            <input type="datetime-local" wire:model="ends_at" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white">
                            @error('ends_at') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Maximum Attempts</label>
                        <input type="number" wire:model="max_attempts" min="1" max="10" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white">
                    </div>
                    <div class="flex items-center gap-3">
                        <input type="checkbox" wire:model="is_randomized" id="is_randomized" class="w-4 h-4 text-indigo-600 rounded">
                        <label for="is_randomized" class="text-sm text-gray-700 dark:text-gray-300">Randomize question order</label>
                    </div>
                </div>
            @endif

            <!-- Step 3: Result & Proctoring Settings -->
            @if($currentStep === 3)
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-6">Result & Proctoring Settings</h2>
                <div class="space-y-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Result Visibility</label>
                        <select wire:model="result_visibility" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white">
                            <option value="immediate">Immediately after submission</option>
                            <option value="after_due_date">After due date</option>
                            <option value="manual_release">Manual release</option>
                        </select>
                    </div>
                    <div class="space-y-3">
                        <div class="flex items-center gap-3">
                            <input type="checkbox" wire:model="show_correct_answers" id="show_correct_answers" class="w-4 h-4 text-indigo-600 rounded">
                            <label for="show_correct_answers" class="text-sm text-gray-700 dark:text-gray-300">Show correct answers in results</label>
                        </div>
                        <div class="flex items-center gap-3">
                            <input type="checkbox" wire:model="show_score_breakdown" id="show_score_breakdown" class="w-4 h-4 text-indigo-600 rounded">
                            <label for="show_score_breakdown" class="text-sm text-gray-700 dark:text-gray-300">Show score breakdown</label>
                        </div>
                    </div>

                    <hr class="border-gray-200 dark:border-gray-700">

                    <h3 class="font-medium text-gray-900 dark:text-white">Proctoring Options</h3>
                    <div class="space-y-3">
                        <div class="flex items-center gap-3">
                            <input type="checkbox" wire:model="proctoring_enabled" id="proctoring_enabled" class="w-4 h-4 text-indigo-600 rounded">
                            <label for="proctoring_enabled" class="text-sm text-gray-700 dark:text-gray-300">Enable proctoring</label>
                        </div>
                        @if($proctoring_enabled)
                            <div class="ml-7 space-y-3 p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                                <div class="flex items-center gap-3">
                                    <input type="checkbox" wire:model="restrict_navigation" id="restrict_navigation" class="w-4 h-4 text-indigo-600 rounded">
                                    <label for="restrict_navigation" class="text-sm text-gray-700 dark:text-gray-300">Restrict tab switching</label>
                                </div>
                                @if($restrict_navigation)
                                    <div class="ml-7">
                                        <label class="block text-sm text-gray-600 dark:text-gray-400 mb-1">Max tab switches allowed</label>
                                        <input type="number" wire:model="max_tab_switches" min="1" max="10" class="w-24 px-3 py-1 border border-gray-300 dark:border-gray-600 rounded dark:bg-gray-700 dark:text-white">
                                    </div>
                                @endif
                                <div class="flex items-center gap-3">
                                    <input type="checkbox" wire:model="require_fullscreen" id="require_fullscreen" class="w-4 h-4 text-indigo-600 rounded">
                                    <label for="require_fullscreen" class="text-sm text-gray-700 dark:text-gray-300">Require fullscreen mode</label>
                                </div>
                                <div class="flex items-center gap-3">
                                    <input type="checkbox" wire:model="auto_submit_on_violation" id="auto_submit_on_violation" class="w-4 h-4 text-indigo-600 rounded">
                                    <label for="auto_submit_on_violation" class="text-sm text-gray-700 dark:text-gray-300">Auto-submit on violation</label>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            <!-- Step 4: Questions -->
            @if($currentStep === 4)
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Questions</h2>
                    <div class="text-sm text-gray-500 dark:text-gray-400">
                        {{ $totalQuestions }} questions • {{ $totalMarks }} marks
                    </div>
                </div>

                @error('questions') <p class="mb-4 text-sm text-red-600">{{ $message }}</p> @enderror

                <!-- AI Generation Section -->
                <div class="mb-6 p-4 bg-indigo-50 dark:bg-indigo-900/30 border border-indigo-200 dark:border-indigo-800 rounded-xl">
                    <h3 class="font-medium text-indigo-800 dark:text-indigo-300 mb-3">🤖 AI Question Generation</h3>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm text-indigo-700 dark:text-indigo-400 mb-1">Upload Document</label>
                            <input type="file" wire:model="uploadedFile" accept=".pdf,.doc,.docx,.txt" class="w-full text-sm">
                            @if($documentContent)
                                <p class="mt-1 text-sm text-green-600">{{ $documentContent }}</p>
                            @endif
                        </div>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                            <div>
                                <label class="block text-xs text-indigo-600 dark:text-indigo-400">Multiple Choice</label>
                                <input type="number" wire:model="aiQuestionTypes.multiple_choice" min="0" max="20" class="w-full px-2 py-1 text-sm border rounded dark:bg-gray-700 dark:border-gray-600">
                            </div>
                            <div>
                                <label class="block text-xs text-indigo-600 dark:text-indigo-400">True/False</label>
                                <input type="number" wire:model="aiQuestionTypes.true_false" min="0" max="20" class="w-full px-2 py-1 text-sm border rounded dark:bg-gray-700 dark:border-gray-600">
                            </div>
                            <div>
                                <label class="block text-xs text-indigo-600 dark:text-indigo-400">Short Answer</label>
                                <input type="number" wire:model="aiQuestionTypes.short_answer" min="0" max="20" class="w-full px-2 py-1 text-sm border rounded dark:bg-gray-700 dark:border-gray-600">
                            </div>
                            <div>
                                <label class="block text-xs text-indigo-600 dark:text-indigo-400">Essay</label>
                                <input type="number" wire:model="aiQuestionTypes.essay" min="0" max="10" class="w-full px-2 py-1 text-sm border rounded dark:bg-gray-700 dark:border-gray-600">
                            </div>
                        </div>
                        <button wire:click="generateQuestions" wire:loading.attr="disabled" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm rounded-lg disabled:opacity-50">
                            <span wire:loading.remove wire:target="generateQuestions">Generate Questions</span>
                            <span wire:loading wire:target="generateQuestions">Generating...</span>
                        </button>
                    </div>
                </div>

                <!-- Database Question Generation Section -->
                <div class="mb-6 p-4 bg-emerald-50 dark:bg-emerald-900/30 border border-emerald-200 dark:border-emerald-800 rounded-xl">
                    <h3 class="font-medium text-emerald-800 dark:text-emerald-300 mb-3">📚 Generate from Question Bank</h3>
                    <p class="text-sm text-emerald-600 dark:text-emerald-400 mb-4">Select academic criteria to pull questions from the existing question bank.</p>

                    @error('academicSelection') <p class="mb-3 text-sm text-red-600">{{ $message }}</p> @enderror
                    @error('generation') <p class="mb-3 text-sm text-red-600">{{ $message }}</p> @enderror

                    <!-- Academic Hierarchy Cascading Dropdowns -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-4">
                        <!-- Academic Group -->
                        <div>
                            <label class="block text-sm font-medium text-emerald-700 dark:text-emerald-400 mb-1">Academic Group *</label>
                            <select wire:model.live="selectedAcademicGroupId" class="w-full px-3 py-2 text-sm border border-emerald-300 dark:border-emerald-700 rounded-lg dark:bg-gray-700 dark:text-white focus:ring-emerald-500 focus:border-emerald-500">
                                <option value="">Select Group...</option>
                                @foreach($this->academicGroups as $group)
                                    <option value="{{ $group->id }}">{{ $group->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Academic Level -->
                        <div>
                            <label class="block text-sm font-medium text-emerald-700 dark:text-emerald-400 mb-1">Academic Level *</label>
                            <select wire:model.live="selectedAcademicLevelId" class="w-full px-3 py-2 text-sm border border-emerald-300 dark:border-emerald-700 rounded-lg dark:bg-gray-700 dark:text-white focus:ring-emerald-500 focus:border-emerald-500" {{ !$selectedAcademicGroupId ? 'disabled' : '' }}>
                                <option value="">Select Level...</option>
                                @foreach($this->academicLevels as $level)
                                    <option value="{{ $level->id }}">{{ $level->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Academic Subject -->
                        <div>
                            <label class="block text-sm font-medium text-emerald-700 dark:text-emerald-400 mb-1">Subject *</label>
                            <select wire:model.live="selectedAcademicSubjectId" class="w-full px-3 py-2 text-sm border border-emerald-300 dark:border-emerald-700 rounded-lg dark:bg-gray-700 dark:text-white focus:ring-emerald-500 focus:border-emerald-500" {{ !$selectedAcademicLevelId ? 'disabled' : '' }}>
                                <option value="">Select Subject...</option>
                                @foreach($this->academicSubjects as $subject)
                                    <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Academic Topic (Optional) -->
                        <div>
                            <label class="block text-sm font-medium text-emerald-700 dark:text-emerald-400 mb-1">Topic <span class="text-gray-400">(Optional)</span></label>
                            <select wire:model.live="selectedAcademicTopicId" class="w-full px-3 py-2 text-sm border border-emerald-300 dark:border-emerald-700 rounded-lg dark:bg-gray-700 dark:text-white focus:ring-emerald-500 focus:border-emerald-500" {{ !$selectedAcademicSubjectId ? 'disabled' : '' }}>
                                <option value="">All Topics</option>
                                @foreach($this->academicTopics as $topic)
                                    <option value="{{ $topic->id }}">{{ $topic->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Academic Subtopic (Optional) -->
                        <div>
                            <label class="block text-sm font-medium text-emerald-700 dark:text-emerald-400 mb-1">Subtopic <span class="text-gray-400">(Optional)</span></label>
                            <select wire:model.live="selectedAcademicSubtopicId" class="w-full px-3 py-2 text-sm border border-emerald-300 dark:border-emerald-700 rounded-lg dark:bg-gray-700 dark:text-white focus:ring-emerald-500 focus:border-emerald-500" {{ !$selectedAcademicTopicId ? 'disabled' : '' }}>
                                <option value="">All Subtopics</option>
                                @foreach($this->academicSubtopics as $subtopic)
                                    <option value="{{ $subtopic->id }}">{{ $subtopic->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Available Questions Summary -->
                    @if($selectedAcademicSubjectId)
                        <div class="mb-4 p-3 bg-white dark:bg-gray-800 rounded-lg border border-emerald-200 dark:border-emerald-700">
                            <p class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Available Questions:</p>
                            <div class="flex flex-wrap gap-3 text-sm">
                                <span class="px-2 py-1 bg-blue-100 dark:bg-blue-900/50 text-blue-700 dark:text-blue-300 rounded">
                                    Multiple Choice: <strong>{{ $availableQuestionsCount['multiple_choice'] }}</strong>
                                </span>
                                <span class="px-2 py-1 bg-purple-100 dark:bg-purple-900/50 text-purple-700 dark:text-purple-300 rounded">
                                    True/False: <strong>{{ $availableQuestionsCount['true_false'] }}</strong>
                                </span>
                                <span class="px-2 py-1 bg-amber-100 dark:bg-amber-900/50 text-amber-700 dark:text-amber-300 rounded">
                                    Essay: <strong>{{ $availableQuestionsCount['essay'] }}</strong>
                                </span>
                            </div>
                        </div>
                    @endif

                    <!-- Question Type Counts -->
                    <div class="mb-4">
                        <p class="text-sm font-medium text-emerald-700 dark:text-emerald-400 mb-2">Number of Questions to Generate:</p>
                        <div class="grid grid-cols-3 gap-3">
                            <div>
                                <label class="block text-xs text-emerald-600 dark:text-emerald-400 mb-1">Multiple Choice</label>
                                <input type="number" wire:model="questionTypeCounts.multiple_choice" min="0" max="{{ $availableQuestionsCount['multiple_choice'] }}" class="w-full px-2 py-1 text-sm border border-emerald-300 dark:border-emerald-700 rounded dark:bg-gray-700 dark:text-white">
                            </div>
                            <div>
                                <label class="block text-xs text-emerald-600 dark:text-emerald-400 mb-1">True/False</label>
                                <input type="number" wire:model="questionTypeCounts.true_false" min="0" max="{{ $availableQuestionsCount['true_false'] }}" class="w-full px-2 py-1 text-sm border border-emerald-300 dark:border-emerald-700 rounded dark:bg-gray-700 dark:text-white">
                            </div>
                            <div>
                                <label class="block text-xs text-emerald-600 dark:text-emerald-400 mb-1">Essay</label>
                                <input type="number" wire:model="questionTypeCounts.essay" min="0" max="{{ $availableQuestionsCount['essay'] }}" class="w-full px-2 py-1 text-sm border border-emerald-300 dark:border-emerald-700 rounded dark:bg-gray-700 dark:text-white">
                            </div>
                        </div>
                    </div>

                    <!-- Generate Button -->
                    <button wire:click="generateQuestionsFromDatabase" wire:loading.attr="disabled"
                            class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm rounded-lg disabled:opacity-50 transition-colors"
                            {{ !$selectedAcademicSubjectId ? 'disabled' : '' }}>
                        <span wire:loading.remove wire:target="generateQuestionsFromDatabase">
                            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                            Pull Questions from Bank
                        </span>
                        <span wire:loading wire:target="generateQuestionsFromDatabase">
                            <svg class="w-4 h-4 inline mr-1 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                            Fetching...
                        </span>
                    </button>
                </div>

                <!-- Questions List -->
                <div class="space-y-4">
                    @forelse($questions as $index => $question)
                        <div class="p-4 border border-gray-200 dark:border-gray-700 rounded-xl">
                            <div class="flex items-start justify-between gap-4">
                                <div class="flex-1">
                                    <div class="flex items-center gap-2 mb-2">
                                        <span class="text-sm font-medium text-gray-500">Q{{ $index + 1 }}</span>
                                        <span class="px-2 py-0.5 text-xs bg-gray-100 dark:bg-gray-700 rounded">{{ ucfirst(str_replace('_', ' ', $question['type'])) }}</span>
                                        <span class="text-xs text-gray-500">{{ $question['marks'] }} marks</span>
                                    </div>
                                    <p class="text-gray-900 dark:text-white">
                                        <x-form.markdown-editor :content="Str::limit($question['question'], 150)"/>
{{--                                        {{ Str::limit($question['question'], 150) }}</p>--}}
                                </div>
                                <div class="flex items-center gap-1">
                                    <button wire:click="moveQuestionUp({{ $index }})" class="p-1 text-gray-400 hover:text-gray-600" @if($index === 0) disabled @endif>
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path></svg>
                                    </button>
                                    <button wire:click="moveQuestionDown({{ $index }})" class="p-1 text-gray-400 hover:text-gray-600" @if($index === count($questions) - 1) disabled @endif>
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                    </button>
                                    <button wire:click="editQuestion({{ $index }})" class="p-1 text-indigo-600 hover:text-indigo-700">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                    </button>
                                    <button wire:click="removeQuestion({{ $index }})" class="p-1 text-red-600 hover:text-red-700">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-8 text-gray-500 dark:text-gray-400">
                            <p>No questions added yet. Add questions manually or use AI generation.</p>
                        </div>
                    @endforelse
                </div>

                <button wire:click="addQuestion" class="mt-4 w-full py-3 border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-xl text-gray-600 dark:text-gray-400 hover:border-indigo-500 hover:text-indigo-600 transition-colors">
                    + Add Question
                </button>
            @endif

            <!-- Step 5: Review -->
            @if($currentStep === 5)
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-6">Review & Create</h2>
                <div class="space-y-6">
                    <div class="grid grid-cols-2 gap-4">
                        <div class="p-4 bg-gray-50 dark:bg-gray-700/50 rounded-xl">
                            <div class="text-sm text-gray-500 dark:text-gray-400">Title</div>
                            <div class="font-medium text-gray-900 dark:text-white">{{ $title ?: 'Not set' }}</div>
                        </div>
                        <div class="p-4 bg-gray-50 dark:bg-gray-700/50 rounded-xl">
                            <div class="text-sm text-gray-500 dark:text-gray-400">Type</div>
                            <div class="font-medium text-gray-900 dark:text-white">{{ ucfirst($type) }}</div>
                        </div>
                        <div class="p-4 bg-gray-50 dark:bg-gray-700/50 rounded-xl">
                            <div class="text-sm text-gray-500 dark:text-gray-400">Duration</div>
                            <div class="font-medium text-gray-900 dark:text-white">{{ $duration_in_minutes ? $duration_in_minutes . ' minutes' : 'No limit' }}</div>
                        </div>
                        <div class="p-4 bg-gray-50 dark:bg-gray-700/50 rounded-xl">
                            <div class="text-sm text-gray-500 dark:text-gray-400">Questions</div>
                            <div class="font-medium text-gray-900 dark:text-white">{{ $totalQuestions }} ({{ $totalMarks }} marks)</div>
                        </div>
                        <div class="p-4 bg-gray-50 dark:bg-gray-700/50 rounded-xl">
                            <div class="text-sm text-gray-500 dark:text-gray-400">Max Attempts</div>
                            <div class="font-medium text-gray-900 dark:text-white">{{ $max_attempts }}</div>
                        </div>
                        <div class="p-4 bg-gray-50 dark:bg-gray-700/50 rounded-xl">
                            <div class="text-sm text-gray-500 dark:text-gray-400">Proctoring</div>
                            <div class="font-medium text-gray-900 dark:text-white">{{ $proctoring_enabled ? 'Enabled' : 'Disabled' }}</div>
                        </div>
                    </div>

                    @if($totalQuestions === 0)
                        <div class="p-4 bg-amber-50 dark:bg-amber-900/30 border border-amber-200 dark:border-amber-800 rounded-xl text-amber-700 dark:text-amber-300">
                            ⚠️ No questions added. Please go back and add at least one question.
                        </div>
                    @endif
                </div>
            @endif

            <!-- Navigation Buttons -->
            <div class="flex items-center justify-between mt-8 pt-6 border-t border-gray-200 dark:border-gray-700">
                @if($currentStep > 1)
                    <button wire:click="previousStep" class="px-6 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">
                        ← Previous
                    </button>
                @else
                    <div></div>
                @endif

                @if($currentStep < 5)
                    <button wire:click="nextStep" class="px-6 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg">
                        Next →
                    </button>
                @else
                    <div class="flex gap-3">
                        <button wire:click="saveAsDraft" class="px-6 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">
                            Save as Draft
                        </button>
                        <button wire:click="publishAssignment" class="px-6 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg" @if($totalQuestions === 0) disabled @endif>
                            Create & Publish
                        </button>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Edit Question Modal -->
    @if($editingQuestionIndex !== null)
        <div class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex items-center justify-center min-h-screen p-4">
                <div class="fixed inset-0 bg-black/50" wire:click="cancelEditing"></div>
                <div class="relative bg-white dark:bg-gray-800 rounded-2xl shadow-xl max-w-2xl w-full p-6 max-h-[90vh] overflow-y-auto">
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4">Edit Question</h2>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Question Type</label>
                            <select wire:model="editingQuestion.type" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white">
                                <option value="multiple_choice">Multiple Choice</option>
                                <option value="true_false">True/False</option>
                                <option value="short_answer">Short Answer</option>
                                <option value="essay">Essay</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Question</label>
                            <textarea wire:model="editingQuestion.question" rows="3" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white"></textarea>
                        </div>
                        @if(($editingQuestion['type'] ?? '') === 'multiple_choice')
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Options</label>
                                @foreach(['A', 'B', 'C', 'D'] as $key)
                                    <div class="flex items-center gap-2 mb-2">
                                        <span class="w-6 text-sm font-medium">{{ $key }}.</span>
                                        <input type="text" wire:model="editingQuestion.options.{{ $key }}" class="flex-1 px-3 py-1 border border-gray-300 dark:border-gray-600 rounded dark:bg-gray-700 dark:text-white">
                                    </div>
                                @endforeach
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Correct Answer</label>
                                <select wire:model="editingQuestion.correct_answer" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white">
                                    @foreach(['A', 'B', 'C', 'D'] as $key)
                                        <option value="{{ $key }}">{{ $key }}</option>
                                    @endforeach
                                </select>
                            </div>
                        @elseif(($editingQuestion['type'] ?? '') === 'true_false')
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Correct Answer</label>
                                <select wire:model="editingQuestion.correct_answer" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white">
                                    <option value="true">True</option>
                                    <option value="false">False</option>
                                </select>
                            </div>
                        @endif
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Marks</label>
                                <input type="number" wire:model="editingQuestion.marks" min="1" max="100" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Difficulty</label>
                                <select wire:model="editingQuestion.difficulty" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white">
                                    <option value="easy">Easy</option>
                                    <option value="medium">Medium</option>
                                    <option value="hard">Hard</option>
                                </select>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Explanation (shown after submission)</label>
                            <textarea wire:model="editingQuestion.explanation" rows="2" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white"></textarea>
                        </div>
                    </div>
                    <div class="flex gap-3 mt-6">
                        <button wire:click="cancelEditing" class="flex-1 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-300">
                            Cancel
                        </button>
                        <button wire:click="saveQuestion" class="flex-1 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg">
                            Save Question
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
