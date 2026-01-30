<div class="max-w-4xl mx-auto p-6">
    <div class="bg-white rounded-lg shadow-md">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-900">Edit Assignment</h2>
        </div>

        <form wire:submit.prevent="updateAssignment" class="p-6 space-y-6">
            <!-- Basic Information -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Title *</label>
                    <input type="text" id="title" wire:model="title"
                           class="mt-1 block w-full border-gray-300 dark:border-gray-600 rounded-md shadow-sm dark:bg-gray-700 dark:text-white focus:ring-blue-500 focus:border-blue-500">
                    @error('title') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label for="type" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Type *</label>
                    <select id="type" wire:model.live="type"
                            class="mt-1 block w-full border-gray-300 dark:border-gray-600 rounded-md shadow-sm dark:bg-gray-700 dark:text-white focus:ring-blue-500 focus:border-blue-500">
                        <option value="quiz">Quiz</option>
                        <option value="examination">Examination</option>
                    </select>
                    @error('type') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
            </div>

            <!-- Subject Selection -->
            <div>
                <label for="academic_subject_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Subject *</label>
                <select id="academic_subject_id" wire:model.live="academic_subject_id"
                        class="mt-1 block w-full border-gray-300 dark:border-gray-600 rounded-md shadow-sm dark:bg-gray-700 dark:text-white focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Select a subject</option>
                    @foreach($availableSubjects as $subject)
                        <option value="{{ $subject['id'] }}">{{ $subject['name'] }} ({{ $subject['code'] }})</option>
                    @endforeach
                </select>
                @error('academic_subject_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <!-- Description -->
            <div>
                <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Description</label>
                <textarea id="description" wire:model="description" rows="3"
                          class="mt-1 block w-full border-gray-300 dark:border-gray-600 rounded-md shadow-sm dark:bg-gray-700 dark:text-white focus:ring-blue-500 focus:border-blue-500"></textarea>
                @error('description') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <!-- Instructions -->
            <div>
                <label for="instructions" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Instructions</label>
                <textarea id="instructions" wire:model="instructions" rows="3"
                          class="mt-1 block w-full border-gray-300 dark:border-gray-600 rounded-md shadow-sm dark:bg-gray-700 dark:text-white focus:ring-blue-500 focus:border-blue-500"></textarea>
                @error('instructions') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <!-- Time Settings -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label for="duration_in_minutes" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Duration (minutes) *</label>
                    <input type="number" id="duration_in_minutes" wire:model="duration_in_minutes" min="5" max="480"
                           class="mt-1 block w-full border-gray-300 dark:border-gray-600 rounded-md shadow-sm dark:bg-gray-700 dark:text-white focus:ring-blue-500 focus:border-blue-500">
                    @error('duration_in_minutes') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label for="starts_at" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Start Date & Time *</label>
                    <input type="datetime-local" id="starts_at" wire:model="starts_at"
                           class="mt-1 block w-full border-gray-300 dark:border-gray-600 rounded-md shadow-sm dark:bg-gray-700 dark:text-white focus:ring-blue-500 focus:border-blue-500">
                    @error('starts_at') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label for="ends_at" class="block text-sm font-medium text-gray-700 dark:text-gray-300">End Date & Time *</label>
                    <input type="datetime-local" id="ends_at" wire:model="ends_at"
                           class="mt-1 block w-full border-gray-300 dark:border-gray-600 rounded-md shadow-sm dark:bg-gray-700 dark:text-white focus:ring-blue-500 focus:border-blue-500">
                    @error('ends_at') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
            </div>

            <!-- Settings -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="total_marks" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Total Marks *</label>
                    <input type="number" id="total_marks" wire:model="total_marks" min="1"
                           class="mt-1 block w-full border-gray-300 dark:border-gray-600 rounded-md shadow-sm dark:bg-gray-700 dark:text-white focus:ring-blue-500 focus:border-blue-500">
                    @error('total_marks') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <div class="flex items-center">
                    <input type="checkbox" id="is_randomized" wire:model="is_randomized"
                           class="h-4 w-4 text-blue-600 dark:text-blue-500 focus:ring-blue-500 dark:focus:ring-blue-400 border-gray-300 dark:border-gray-600 dark:bg-gray-700 rounded">
                    <label for="is_randomized" class="ml-2 block text-sm text-gray-900">
                        Randomize questions for each student
                    </label>
                </div>
            </div>

            <!-- Question Configuration Section -->
            @if($showQuestionSelection)
                <div class="border-t pt-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Question Configuration</h3>

                    <!-- Topic/Subtopic Selection -->
                    @if(!empty($availableTopics))
                        <div class="mb-6">
                            <h4 class="text-md font-medium text-gray-800 mb-3">Content Scope (Optional)</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Topics -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Topics</label>
                                    <div class="space-y-2 max-h-32 overflow-y-auto border border-gray-300 dark:border-gray-600 rounded-md p-2 dark:bg-gray-800">
                                        @foreach($availableTopics as $topic)
                                            <label class="flex items-center">
                                                <input type="checkbox" wire:model.live="selectedTopics" value="{{ $topic['id'] }}"
                                                       class="h-4 w-4 text-blue-600 dark:text-blue-500 focus:ring-blue-500 dark:focus:ring-blue-400 border-gray-300 dark:border-gray-600 dark:bg-gray-700 rounded">
                                                <span class="ml-2 text-sm text-gray-900">{{ $topic['name'] }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                    <p class="text-xs text-gray-500 mt-1">Leave empty to include all topics from the subject</p>
                                </div>

                                <!-- Subtopics -->
                                @if(!empty($availableSubtopics))
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Subtopics</label>
                                        <div class="space-y-2 max-h-32 overflow-y-auto border border-gray-300 dark:border-gray-600 rounded-md p-2 dark:bg-gray-800">
                                            @foreach($availableSubtopics as $subtopic)
                                                <label class="flex items-center">
                                                    <input type="checkbox" wire:model.live="selectedSubtopics" value="{{ $subtopic['id'] }}"
                                                           class="h-4 w-4 text-blue-600 dark:text-blue-500 focus:ring-blue-500 dark:focus:ring-blue-400 border-gray-300 dark:border-gray-600 dark:bg-gray-700 rounded">
                                                    <span class="ml-2 text-sm text-gray-900">{{ $subtopic['name'] }}</span>
                                                </label>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif

                    <!-- Question Types -->
                    <div class="space-y-4">
                        <h4 class="text-md font-medium text-gray-800">Question Types</h4>

                        <!-- Multiple Choice Questions -->
                        <div class="border rounded-lg p-4">
                            <div class="flex items-center mb-3">
                                <input type="checkbox" id="mcq_enabled" wire:model.live="questionTypes.multiple_choice_question.enabled"
                                       class="h-4 w-4 text-blue-600 dark:text-blue-500 focus:ring-blue-500 dark:focus:ring-blue-400 border-gray-300 dark:border-gray-600 dark:bg-gray-700 rounded">
                                <label for="mcq_enabled" class="ml-2 text-sm font-medium text-gray-900">
                                    Multiple Choice Questions
                                </label>
                            </div>
                            @if($questionTypes['multiple_choice_question']['enabled'])
                                <div class="grid grid-cols-2 gap-4 ml-6">
                                    <div>
                                        <label class="block text-sm text-gray-700">Number of Questions</label>
                                        <input type="number" wire:model.live="questionTypes.multiple_choice_question.count" min="1" max="50"
                                               class="mt-1 block w-full border-gray-300 dark:border-gray-600 rounded-md shadow-sm dark:bg-gray-700 dark:text-white focus:ring-blue-500 focus:border-blue-500 text-sm">
                                    </div>
                                    <div>
                                        <label class="block text-sm text-gray-700">Difficulty</label>
                                        <select wire:model.live="questionTypes.multiple_choice_question.difficulty"
                                                class="mt-1 block w-full border-gray-300 dark:border-gray-600 rounded-md shadow-sm dark:bg-gray-700 dark:text-white focus:ring-blue-500 focus:border-blue-500 text-sm">
                                            <option value="all">All Difficulties</option>
                                            <option value="easy">Easy</option>
                                            <option value="medium">Medium</option>
                                            <option value="hard">Hard</option>
                                        </select>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <!-- True/False Questions -->
                        <div class="border rounded-lg p-4">
                            <div class="flex items-center mb-3">
                                <input type="checkbox" id="tf_enabled" wire:model.live="questionTypes.true_or_false_question.enabled"
                                       class="h-4 w-4 text-blue-600 dark:text-blue-500 focus:ring-blue-500 dark:focus:ring-blue-400 border-gray-300 dark:border-gray-600 dark:bg-gray-700 rounded">
                                <label for="tf_enabled" class="ml-2 text-sm font-medium text-gray-900">
                                    True/False Questions
                                </label>
                            </div>
                            @if($questionTypes['true_or_false_question']['enabled'])
                                <div class="grid grid-cols-2 gap-4 ml-6">
                                    <div>
                                        <label class="block text-sm text-gray-700">Number of Questions</label>
                                        <input type="number" wire:model.live="questionTypes.true_or_false_question.count" min="1" max="50"
                                               class="mt-1 block w-full border-gray-300 dark:border-gray-600 rounded-md shadow-sm dark:bg-gray-700 dark:text-white focus:ring-blue-500 focus:border-blue-500 text-sm">
                                    </div>
                                    <div>
                                        <label class="block text-sm text-gray-700">Difficulty</label>
                                        <select wire:model.live="questionTypes.true_or_false_question.difficulty"
                                                class="mt-1 block w-full border-gray-300 dark:border-gray-600 rounded-md shadow-sm dark:bg-gray-700 dark:text-white focus:ring-blue-500 focus:border-blue-500 text-sm">
                                            <option value="all">All Difficulties</option>
                                            <option value="easy">Easy</option>
                                            <option value="medium">Medium</option>
                                            <option value="hard">Hard</option>
                                        </select>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <!-- Essay Questions -->
                        <div class="border rounded-lg p-4">
                            <div class="flex items-center mb-3">
                                <input type="checkbox" id="essay_enabled" wire:model.live="questionTypes.essay_question.enabled"
                                       class="h-4 w-4 text-blue-600 dark:text-blue-500 focus:ring-blue-500 dark:focus:ring-blue-400 border-gray-300 dark:border-gray-600 dark:bg-gray-700 rounded">
                                <label for="essay_enabled" class="ml-2 text-sm font-medium text-gray-900">
                                    Essay Questions
                                </label>
                            </div>
                            @if($questionTypes['essay_question']['enabled'])
                                <div class="grid grid-cols-2 gap-4 ml-6">
                                    <div>
                                        <label class="block text-sm text-gray-700">Number of Questions</label>
                                        <input type="number" wire:model.live="questionTypes.essay_question.count" min="1" max="20"
                                               class="mt-1 block w-full border-gray-300 dark:border-gray-600 rounded-md shadow-sm dark:bg-gray-700 dark:text-white focus:ring-blue-500 focus:border-blue-500 text-sm">
                                    </div>
                                    <div>
                                        <label class="block text-sm text-gray-700">Difficulty</label>
                                        <select wire:model.live="questionTypes.essay_question.difficulty"
                                                class="mt-1 block w-full border-gray-300 dark:border-gray-600 rounded-md shadow-sm dark:bg-gray-700 dark:text-white focus:ring-blue-500 focus:border-blue-500 text-sm">
                                            <option value="all">All Difficulties</option>
                                            <option value="easy">Easy</option>
                                            <option value="medium">Medium</option>
                                            <option value="hard">Hard</option>
                                        </select>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Question Summary -->
                    @if($this->totalQuestions > 0)
                        <div class="mt-4 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                            <p class="text-sm text-blue-800">
                                <strong>Total Questions:</strong> {{ $this->totalQuestions }}
                                @if($is_randomized)
                                    (Students will receive randomized questions from the available pool)
                                @else
                                    (All students will receive the same questions)
                                @endif
                            </p>
                        </div>
                    @endif
                </div>
            @else
                <div class="border-t pt-6">
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.99-.833-2.76 0L4.054 15.5c-.77.833.192 2.5 1.732 2.5z"/>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-yellow-800">Select a Subject</h3>
                                <div class="mt-2 text-sm text-yellow-700">
                                    <p>Please select a subject to configure questions for this assignment.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Assignment Targets -->
            <div class="border-t pt-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Assignment Targets</h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Academic Groups -->
                    @if(!empty($availableAcademicGroups))
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Academic Groups</label>
                            <div class="space-y-2 max-h-32 overflow-y-auto border border-gray-300 dark:border-gray-600 rounded-md p-2 dark:bg-gray-800">
                                @foreach($availableAcademicGroups as $group)
                                    <label class="flex items-center">
                                        <input type="checkbox" wire:model.live="selectedAcademicGroups" value="{{ $group['id'] }}"
                                               class="h-4 w-4 text-blue-600 dark:text-blue-500 focus:ring-blue-500 dark:focus:ring-blue-400 border-gray-300 dark:border-gray-600 dark:bg-gray-700 rounded">
                                        <span class="ml-2 text-sm text-gray-900">{{ $group['name'] }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Academic Levels -->
                    @if(!empty($availableAcademicLevels))
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Academic Levels</label>
                            <div class="space-y-2 max-h-32 overflow-y-auto border border-gray-300 dark:border-gray-600 rounded-md p-2 dark:bg-gray-800">
                                @foreach($availableAcademicLevels as $level)
                                    <label class="flex items-center">
                                        <input type="checkbox" wire:model.live="selectedAcademicLevels" value="{{ $level['id'] }}"
                                               class="h-4 w-4 text-blue-600 dark:text-blue-500 focus:ring-blue-500 dark:focus:ring-blue-400 border-gray-300 dark:border-gray-600 dark:bg-gray-700 rounded">
                                        <span class="ml-2 text-sm text-gray-900">{{ $level['name'] }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Student Groups -->
                    @if(!empty($availableStudentGroups))
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Student Groups</label>
                            <div class="space-y-2 max-h-32 overflow-y-auto border border-gray-300 dark:border-gray-600 rounded-md p-2 dark:bg-gray-800">
                                @foreach($availableStudentGroups as $group)
                                    <label class="flex items-center">
                                        <input type="checkbox" wire:model.live="selectedStudentGroups" value="{{ $group['id'] }}"
                                               class="h-4 w-4 text-blue-600 dark:text-blue-500 focus:ring-blue-500 dark:focus:ring-blue-400 border-gray-300 dark:border-gray-600 dark:bg-gray-700 rounded">
                                        <span class="ml-2 text-sm text-gray-900">{{ $group['name'] }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Individual Students -->
                    @if(!empty($availableStudents))
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Individual Students</label>
                            <div class="space-y-2 max-h-32 overflow-y-auto border border-gray-300 dark:border-gray-600 rounded-md p-2 dark:bg-gray-800">
                                @foreach($availableStudents as $student)
                                    <label class="flex items-center">
                                        <input type="checkbox" wire:model.live="selectedStudents" value="{{ $student['id'] }}"
                                               class="h-4 w-4 text-blue-600 dark:text-blue-500 focus:ring-blue-500 dark:focus:ring-blue-400 border-gray-300 dark:border-gray-600 dark:bg-gray-700 rounded">
                                        <span class="ml-2 text-sm text-gray-900">{{ $student['name'] }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Submit Button -->
            <div class="border-t pt-6">
                <div class="flex justify-end space-x-3">
                    <a href="{{ route('teachers.assignments.index') }}"
                       class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Cancel
                    </a>
                    <button type="submit"
                            class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Update Assignment
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>