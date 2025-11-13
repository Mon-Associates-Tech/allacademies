<div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-100 py-8 px-4">
    <div class="max-w-5xl mx-auto">
        <!-- Header Section -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-r from-blue-600 to-indigo-600 rounded-2xl mb-4 shadow-lg">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
            <h1 class="text-4xl font-bold bg-gradient-to-r from-gray-900 to-gray-600 bg-clip-text text-transparent mb-2">
                Create New Assignment
            </h1>
            <p class="text-gray-600 text-lg">Design engaging quizzes and examinations for your students</p>
        </div>

        <!-- Main Form Card -->
        <div class="bg-white/80 backdrop-blur-sm rounded-3xl shadow-2xl border border-white/20 overflow-hidden">
            <form wire:submit.prevent="createAssignment" class="p-8 space-y-8">
                <!-- Basic Information Section -->
                <div class="relative">
                    <div class="flex items-center mb-6">
                        <div class="w-8 h-8 bg-gradient-to-r from-emerald-500 to-teal-600 rounded-lg flex items-center justify-center mr-3">
                            <span class="text-white font-bold text-sm">1</span>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900">Basic Information</h3>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="group">
                            <label for="title" class="block text-sm font-semibold text-gray-700 mb-2">Assignment Title *</label>
                            <div class="relative">
                                <input type="text" id="title" wire:model="title"
                                       class="w-full px-4 py-3 bg-white border-2 border-gray-200 rounded-xl focus:ring-4 focus:ring-blue-500/20 focus:border-blue-500 transition-all duration-200 placeholder-gray-400"
                                       placeholder="Enter assignment title...">
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                                    <svg class="w-5 h-5 text-gray-400 group-focus-within:text-blue-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                    </svg>
                                </div>
                            </div>
                            @error('title') <span class="text-red-500 text-sm mt-1 flex items-center"><svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>{{ $message }}</span> @enderror
                        </div>

                        <div class="group">
                            <label for="type" class="block text-sm font-semibold text-gray-700 mb-2">Assignment Type *</label>
                            <div class="relative">
                                <select id="type" wire:model.live="type"
                                        class="w-full px-4 py-3 bg-white border-2 border-gray-200 rounded-xl focus:ring-4 focus:ring-blue-500/20 focus:border-blue-500 transition-all duration-200 appearance-none">
                                    <option value="quiz">📝 Quiz</option>
                                    <option value="examination">🎓 Examination</option>
                                </select>
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                    </svg>
                                </div>
                            </div>
                            @error('type') <span class="text-red-500 text-sm mt-1 flex items-center"><svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <!-- Subject Selection -->
                    <div class="mt-6">
                        <label for="academic_subject_id" class="block text-sm font-semibold text-gray-700 mb-2">Subject *</label>
                        <div class="relative">
                            <select id="academic_subject_id" wire:model.live="academic_subject_id"
                                    class="w-full px-4 py-3 bg-white border-2 border-gray-200 rounded-xl focus:ring-4 focus:ring-blue-500/20 focus:border-blue-500 transition-all duration-200 appearance-none">
                                <option value="">Select a subject...</option>
                                @foreach($availableSubjects as $subject)
                                    <option value="{{ $subject['id'] }}">{{ $subject['name'] }} ({{ $subject['code'] }})</option>
                                @endforeach
                            </select>
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </div>
                        </div>
                        @error('academic_subject_id') <span class="text-red-500 text-sm mt-1 flex items-center"><svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>{{ $message }}</span> @enderror
                    </div>

                    <!-- Description & Instructions -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                        <div>
                            <label for="description" class="block text-sm font-semibold text-gray-700 mb-2">Description</label>
                            <textarea id="description" wire:model="description" rows="4"
                                      class="w-full px-4 py-3 bg-white border-2 border-gray-200 rounded-xl focus:ring-4 focus:ring-blue-500/20 focus:border-blue-500 transition-all duration-200 placeholder-gray-400 resize-none"
                                      placeholder="Provide a brief description of the assignment..."></textarea>
                            @error('description') <span class="text-red-500 text-sm mt-1 flex items-center"><svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label for="instructions" class="block text-sm font-semibold text-gray-700 mb-2">Instructions</label>
                            <textarea id="instructions" wire:model="instructions" rows="4"
                                      class="w-full px-4 py-3 bg-white border-2 border-gray-200 rounded-xl focus:ring-4 focus:ring-blue-500/20 focus:border-blue-500 transition-all duration-200 placeholder-gray-400 resize-none"
                                      placeholder="Enter specific instructions for students..."></textarea>
                            @error('instructions') <span class="text-red-500 text-sm mt-1 flex items-center"><svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>

                <!-- Time & Settings Section -->
                <div class="relative border-t border-gray-100 pt-8">
                    <div class="flex items-center mb-6">
                        <div class="w-8 h-8 bg-gradient-to-r from-purple-500 to-pink-600 rounded-lg flex items-center justify-center mr-3">
                            <span class="text-white font-bold text-sm">2</span>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900">Time & Configuration</h3>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                        <div>
                            <label for="duration_in_minutes" class="block text-sm font-semibold text-gray-700 mb-2">Duration (minutes) *</label>
                            <div class="relative">
                                <input type="number" id="duration_in_minutes" wire:model="duration_in_minutes" min="5" max="480"
                                       class="w-full px-4 py-3 bg-white border-2 border-gray-200 rounded-xl focus:ring-4 focus:ring-blue-500/20 focus:border-blue-500 transition-all duration-200"
                                       placeholder="60">
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                            </div>
                            @error('duration_in_minutes') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label for="starts_at" class="block text-sm font-semibold text-gray-700 mb-2">Start Date & Time *</label>
                            <input type="datetime-local" id="starts_at" wire:model="starts_at"
                                   class="w-full px-4 py-3 bg-white border-2 border-gray-200 rounded-xl focus:ring-4 focus:ring-blue-500/20 focus:border-blue-500 transition-all duration-200">
                            @error('starts_at') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label for="ends_at" class="block text-sm font-semibold text-gray-700 mb-2">End Date & Time *</label>
                            <input type="datetime-local" id="ends_at" wire:model="ends_at"
                                   class="w-full px-4 py-3 bg-white border-2 border-gray-200 rounded-xl focus:ring-4 focus:ring-blue-500/20 focus:border-blue-500 transition-all duration-200">
                            @error('ends_at') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label for="total_marks" class="block text-sm font-semibold text-gray-700 mb-2">Total Marks *</label>
                            <div class="relative">
                                <input type="number" id="total_marks" wire:model="total_marks" min="1"
                                       class="w-full px-4 py-3 bg-white border-2 border-gray-200 rounded-xl focus:ring-4 focus:ring-blue-500/20 focus:border-blue-500 transition-all duration-200"
                                       placeholder="100">
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                    </svg>
                                </div>
                            </div>
                            @error('total_marks') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <!-- Exam Security & Restrictions -->
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                        <h3 class="text-lg font-semibold mb-4">Exam Security & Restrictions</h3>

                        <div class="space-y-4">
                            <div class="flex items-center">
                                <input type="checkbox"
                                       wire:model="restrict_navigation"
                                       id="restrict_navigation"
                                       class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                <label for="restrict_navigation" class="ml-2 text-sm text-gray-700 dark:text-gray-300">
                                    Restrict navigation (prevent students from leaving the assignment page)
                                </label>
                            </div>

                            @if($restrict_navigation)
                                <div class="ml-6 space-y-4 border-l-2 border-blue-300 pl-4">
                                    <div>
                                        <label for="max_tab_switches" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                            Maximum Tab Switches Allowed
                                        </label>
                                        <input type="number"
                                               wire:model="max_tab_switches"
                                               id="max_tab_switches"
                                               min="0"
                                               max="10"
                                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                        <p class="mt-1 text-xs text-gray-500">Set to 0 for unlimited. Recommended: 2-3 switches.</p>
                                    </div>

                                    <div class="flex items-center">
                                        <input type="checkbox"
                                               wire:model="auto_submit_on_violation"
                                               id="auto_submit_on_violation"
                                               class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                        <label for="auto_submit_on_violation" class="ml-2 text-sm text-gray-700 dark:text-gray-300">
                                            Automatically cancel assignment when limit is exceeded
                                        </label>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Randomization Toggle -->
                    <div class="mt-6">
                        <label class="flex items-center p-4 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl border border-blue-100 cursor-pointer hover:from-blue-100 hover:to-indigo-100 transition-all duration-200">
                            <input type="checkbox" id="is_randomized" wire:model="is_randomized"
                                   class="w-5 h-5 text-blue-600 bg-white border-2 border-gray-300 rounded focus:ring-blue-500 focus:ring-4 focus:ring-blue-500/20">
                            <div class="ml-3">
                                <span class="text-sm font-semibold text-gray-900">🎲 Randomize Questions</span>
                                <p class="text-sm text-gray-600">Each student will receive questions in random order</p>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Question Configuration Section -->
                @if($showQuestionSelection)
                    <div class="relative border-t border-gray-100 pt-8">
                        <div class="flex items-center mb-6">
                            <div class="w-8 h-8 bg-gradient-to-r from-orange-500 to-red-600 rounded-lg flex items-center justify-center mr-3">
                                <span class="text-white font-bold text-sm">3</span>
                            </div>
                            <h3 class="text-xl font-semibold text-gray-900">Question Configuration</h3>
                        </div>

                        <!-- Topic/Subtopic Selection -->
                        @if(!empty($availableTopics))
                            <div class="mb-8">
                                <h4 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                                    <svg class="w-5 h-5 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                                    </svg>
                                    Content Scope (Optional)
                                </h4>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <!-- Topics -->
                                    <div class="bg-white p-6 rounded-2xl border-2 border-gray-100 hover:border-indigo-200 transition-all duration-200">
                                        <label class="block text-sm font-semibold text-gray-700 mb-3">📚 Topics</label>
                                        <div class="space-y-2 max-h-32 overflow-y-auto custom-scrollbar">
                                            @foreach($availableTopics as $topic)
                                                <label class="flex items-center p-2 rounded-lg hover:bg-gray-50 cursor-pointer transition-colors">
                                                    <input type="checkbox" wire:model.live="selectedTopics" value="{{ $topic['id'] }}"
                                                           class="w-4 h-4 text-indigo-600 bg-white border-2 border-gray-300 rounded focus:ring-indigo-500 focus:ring-2">
                                                    <span class="ml-3 text-sm text-gray-900">{{ $topic['name'] }}</span>
                                                </label>
                                            @endforeach
                                        </div>
                                        <p class="text-xs text-gray-500 mt-2">Leave empty to include all topics</p>
                                    </div>

                                    <!-- Subtopics -->
                                    @if(!empty($availableSubtopics))
                                        <div class="bg-white p-6 rounded-2xl border-2 border-gray-100 hover:border-indigo-200 transition-all duration-200">
                                            <label class="block text-sm font-semibold text-gray-700 mb-3">🔍 Subtopics</label>
                                            <div class="space-y-2 max-h-32 overflow-y-auto custom-scrollbar">
                                                @foreach($availableSubtopics as $subtopic)
                                                    <label class="flex items-center p-2 rounded-lg hover:bg-gray-50 cursor-pointer transition-colors">
                                                        <input type="checkbox" wire:model.live="selectedSubtopics" value="{{ $subtopic['id'] }}"
                                                               class="w-4 h-4 text-indigo-600 bg-white border-2 border-gray-300 rounded focus:ring-indigo-500 focus:ring-2">
                                                        <span class="ml-3 text-sm text-gray-900">{{ $subtopic['name'] }}</span>
                                                    </label>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endif

                        <!-- Question Types -->
                        <div class="space-y-6">
                            <h4 class="text-lg font-semibold text-gray-800 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                Question Types
                            </h4>

                            <!-- Multiple Choice Questions -->
                            <div class="bg-white rounded-2xl border-2 border-gray-100 overflow-hidden hover:shadow-lg transition-all duration-300">
                                <label class="flex items-center p-6 cursor-pointer bg-gradient-to-r from-emerald-50 to-teal-50 border-b border-gray-100">
                                    <input type="checkbox" id="mcq_enabled" wire:model.live="questionTypes.multiple_choice_question.enabled"
                                           class="w-5 h-5 text-emerald-600 bg-white border-2 border-gray-300 rounded focus:ring-emerald-500 focus:ring-2">
                                    <div class="ml-4">
                                        <span class="text-lg font-semibold text-gray-900 flex items-center">
                                            ✅ Multiple Choice Questions
                                        </span>
                                        <p class="text-sm text-gray-600">Students select from predefined options</p>
                                    </div>
                                </label>
                                @if($questionTypes['multiple_choice_question']['enabled'])
                                    <div class="p-6 grid grid-cols-2 gap-6 bg-white">
                                        <div>
                                            <label class="block text-sm font-semibold text-gray-700 mb-2">Number of Questions</label>
                                            <input type="number" wire:model.live="questionTypes.multiple_choice_question.count" min="1" max="50"
                                                   class="w-full px-4 py-3 bg-white border-2 border-gray-200 rounded-xl focus:ring-4 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all duration-200">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-semibold text-gray-700 mb-2">Difficulty Level</label>
                                            <select wire:model.live="questionTypes.multiple_choice_question.difficulty"
                                                    class="w-full px-4 py-3 bg-white border-2 border-gray-200 rounded-xl focus:ring-4 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all duration-200 appearance-none">
                                                <option value="all">🌟 All Difficulties</option>
                                                <option value="easy">🟢 Easy</option>
                                                <option value="medium">🟡 Medium</option>
                                                <option value="hard">🔴 Hard</option>
                                            </select>
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <!-- True/False Questions -->
                            <div class="bg-white rounded-2xl border-2 border-gray-100 overflow-hidden hover:shadow-lg transition-all duration-300">
                                <label class="flex items-center p-6 cursor-pointer bg-gradient-to-r from-blue-50 to-indigo-50 border-b border-gray-100">
                                    <input type="checkbox" id="tf_enabled" wire:model.live="questionTypes.true_or_false_question.enabled"
                                           class="w-5 h-5 text-blue-600 bg-white border-2 border-gray-300 rounded focus:ring-blue-500 focus:ring-2">
                                    <div class="ml-4">
                                        <span class="text-lg font-semibold text-gray-900 flex items-center">
                                            ⚖️ True/False Questions
                                        </span>
                                        <p class="text-sm text-gray-600">Binary choice questions with true or false answers</p>
                                    </div>
                                </label>
                                @if($questionTypes['true_or_false_question']['enabled'])
                                    <div class="p-6 grid grid-cols-2 gap-6 bg-white">
                                        <div>
                                            <label class="block text-sm font-semibold text-gray-700 mb-2">Number of Questions</label>
                                            <input type="number" wire:model.live="questionTypes.true_or_false_question.count" min="1" max="50"
                                                   class="w-full px-4 py-3 bg-white border-2 border-gray-200 rounded-xl focus:ring-4 focus:ring-blue-500/20 focus:border-blue-500 transition-all duration-200">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-semibold text-gray-700 mb-2">Difficulty Level</label>
                                            <select wire:model.live="questionTypes.true_or_false_question.difficulty"
                                                    class="w-full px-4 py-3 bg-white border-2 border-gray-200 rounded-xl focus:ring-4 focus:ring-blue-500/20 focus:border-blue-500 transition-all duration-200 appearance-none">
                                                <option value="all">🌟 All Difficulties</option>
                                                <option value="easy">🟢 Easy</option>
                                                <option value="medium">🟡 Medium</option>
                                                <option value="hard">🔴 Hard</option>
                                            </select>
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <!-- Essay Questions -->
                            <div class="bg-white rounded-2xl border-2 border-gray-100 overflow-hidden hover:shadow-lg transition-all duration-300">
                                <label class="flex items-center p-6 cursor-pointer bg-gradient-to-r from-purple-50 to-pink-50 border-b border-gray-100">
                                    <input type="checkbox" id="essay_enabled" wire:model.live="questionTypes.essay_question.enabled"
                                           class="w-5 h-5 text-purple-600 bg-white border-2 border-gray-300 rounded focus:ring-purple-500 focus:ring-2">
                                    <div class="ml-4">
                                        <span class="text-lg font-semibold text-gray-900 flex items-center">
                                            📝 Essay Questions
                                        </span>
                                        <p class="text-sm text-gray-600">Open-ended questions requiring detailed written responses</p>
                                    </div>
                                </label>
                                @if($questionTypes['essay_question']['enabled'])
                                    <div class="p-6 grid grid-cols-2 gap-6 bg-white">
                                        <div>
                                            <label class="block text-sm font-semibold text-gray-700 mb-2">Number of Questions</label>
                                            <input type="number" wire:model.live="questionTypes.essay_question.count" min="1" max="20"
                                                   class="w-full px-4 py-3 bg-white border-2 border-gray-200 rounded-xl focus:ring-4 focus:ring-purple-500/20 focus:border-purple-500 transition-all duration-200">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-semibold text-gray-700 mb-2">Difficulty Level</label>
                                            <select wire:model.live="questionTypes.essay_question.difficulty"
                                                    class="w-full px-4 py-3 bg-white border-2 border-gray-200 rounded-xl focus:ring-4 focus:ring-purple-500/20 focus:border-purple-500 transition-all duration-200 appearance-none">
                                                <option value="all">🌟 All Difficulties</option>
                                                <option value="easy">🟢 Easy</option>
                                                <option value="medium">🟡 Medium</option>
                                                <option value="hard">🔴 Hard</option>
                                            </select>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Question Summary -->
                        @if($this->totalQuestions > 0)
                            <div class="mt-6 p-6 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-2xl text-white">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0">
                                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                        </svg>
                                    </div>
                                    <div class="ml-4">
                                        <h4 class="text-lg font-semibold">Assignment Summary</h4>
                                        <p class="text-blue-100">
                                            <strong>Total Questions:</strong> {{ $this->totalQuestions }}
                                            @if($is_randomized)
                                                <span class="block text-sm mt-1">🎲 Students will receive randomized questions from the available pool</span>
                                            @else
                                                <span class="block text-sm mt-1">📋 All students will receive the same questions</span>
                                            @endif
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                @else
                    <div class="relative border-t border-gray-100 pt-8">
                        <div class="flex items-center mb-6">
                            <div class="w-8 h-8 bg-gradient-to-r from-orange-500 to-red-600 rounded-lg flex items-center justify-center mr-3">
                                <span class="text-white font-bold text-sm">3</span>
                            </div>
                            <h3 class="text-xl font-semibold text-gray-900">Question Configuration</h3>
                        </div>

                        <div class="bg-gradient-to-r from-amber-50 to-orange-50 border-2 border-amber-200 rounded-2xl p-6">
                            <div class="flex items-start">
                                <div class="flex-shrink-0">
                                    <svg class="w-8 h-8 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.99-.833-2.76 0L4.054 15.5c-.77.833.192 2.5 1.732 2.5z"/>
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <h3 class="text-lg font-semibold text-amber-800">📚 Subject Selection Required</h3>
                                    <p class="mt-2 text-amber-700">
                                        Please select a subject above to configure questions for this assignment. Once you've chosen a subject, you'll be able to customize question types, difficulty levels, and content scope.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Assignment Targets Section -->
                <div class="relative border-t border-gray-100 pt-8">
                    <div class="flex items-center mb-6">
                        <div class="w-8 h-8 bg-gradient-to-r from-teal-500 to-green-600 rounded-lg flex items-center justify-center mr-3">
                            <span class="text-white font-bold text-sm">4</span>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900">Assignment Targets</h3>
                        <div class="ml-auto">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                👥 Select Students
                            </span>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Academic Groups -->
                        @if(!empty($availableAcademicGroups))
                            <div class="bg-white p-6 rounded-2xl border-2 border-gray-100 hover:border-teal-200 hover:shadow-lg transition-all duration-300">
                                <label class="block text-lg font-semibold text-gray-700 mb-4 flex items-center">
                                    <svg class="w-5 h-5 mr-2 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                    </svg>
                                    🎯 Academic Groups
                                </label>
                                <div class="space-y-2 max-h-40 overflow-y-auto custom-scrollbar">
                                    @foreach($availableAcademicGroups as $group)
                                        <label class="flex items-center p-3 rounded-xl hover:bg-gray-50 cursor-pointer transition-all duration-200 group">
                                            <input type="checkbox" wire:model.live="selectedAcademicGroups" value="{{ $group['id'] }}"
                                                   class="w-4 h-4 text-teal-600 bg-white border-2 border-gray-300 rounded focus:ring-teal-500 focus:ring-2">
                                            <span class="ml-3 text-sm text-gray-900 group-hover:text-teal-700 transition-colors">{{ $group['name'] }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <!-- Academic Levels -->
                        @if(!empty($availableAcademicLevels))
                            <div class="bg-white p-6 rounded-2xl border-2 border-gray-100 hover:border-teal-200 hover:shadow-lg transition-all duration-300">
                                <label class="block text-lg font-semibold text-gray-700 mb-4 flex items-center">
                                    <svg class="w-5 h-5 mr-2 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                                    </svg>
                                    📊 Academic Levels
                                </label>
                                <div class="space-y-2 max-h-40 overflow-y-auto custom-scrollbar">
                                    @foreach($availableAcademicLevels as $level)
                                        <label class="flex items-center p-3 rounded-xl hover:bg-gray-50 cursor-pointer transition-all duration-200 group">
                                            <input type="checkbox" wire:model.live="selectedAcademicLevels" value="{{ $level['id'] }}"
                                                   class="w-4 h-4 text-teal-600 bg-white border-2 border-gray-300 rounded focus:ring-teal-500 focus:ring-2">
                                            <span class="ml-3 text-sm text-gray-900 group-hover:text-teal-700 transition-colors">{{ $level['name'] }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <!-- Student Groups -->
                        @if(!empty($availableStudentGroups))
                            <div class="bg-white p-6 rounded-2xl border-2 border-gray-100 hover:border-teal-200 hover:shadow-lg transition-all duration-300">
                                <label class="block text-lg font-semibold text-gray-700 mb-4 flex items-center">
                                    <svg class="w-5 h-5 mr-2 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z"/>
                                    </svg>
                                    👥 Student Groups
                                </label>
                                <div class="space-y-2 max-h-40 overflow-y-auto custom-scrollbar">
                                    @foreach($availableStudentGroups as $group)
                                        <label class="flex items-center p-3 rounded-xl hover:bg-gray-50 cursor-pointer transition-all duration-200 group">
                                            <input type="checkbox" wire:model.live="selectedStudentGroups" value="{{ $group['id'] }}"
                                                   class="w-4 h-4 text-teal-600 bg-white border-2 border-gray-300 rounded focus:ring-teal-500 focus:ring-2">
                                            <span class="ml-3 text-sm text-gray-900 group-hover:text-teal-700 transition-colors">{{ $group['name'] }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <!-- Individual Students -->
                        @if(!empty($availableStudents))
                            <div class="bg-white p-6 rounded-2xl border-2 border-gray-100 hover:border-teal-200 hover:shadow-lg transition-all duration-300">
                                <label class="block text-lg font-semibold text-gray-700 mb-4 flex items-center">
                                    <svg class="w-5 h-5 mr-2 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                    🎓 Individual Students
                                </label>
                                <div class="space-y-2 max-h-40 overflow-y-auto custom-scrollbar">
                                    @foreach($availableStudents as $student)
                                        <label class="flex items-center p-3 rounded-xl hover:bg-gray-50 cursor-pointer transition-all duration-200 group">
                                            <input type="checkbox" wire:model.live="selectedStudents" value="{{ $student['id'] }}"
                                                   class="w-4 h-4 text-teal-600 bg-white border-2 border-gray-300 rounded focus:ring-teal-500 focus:ring-2">
                                            <span class="ml-3 text-sm text-gray-900 group-hover:text-teal-700 transition-colors">{{ $student['name'] }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Submit Section -->
                <div class="border-t border-gray-100 pt-8">
                    <div class="flex flex-col sm:flex-row justify-between items-center space-y-4 sm:space-y-0 sm:space-x-4">
                        <div class="text-sm text-gray-500">
                            ⚡ All fields marked with * are required
                        </div>
                        <div class="flex space-x-4">
                            <a href="{{ route('teachers.assignments.index') }}"
                               class="px-6 py-3 border-2 border-gray-300 rounded-xl text-sm font-semibold text-gray-700 bg-white hover:bg-gray-50 hover:border-gray-400 focus:outline-none focus:ring-4 focus:ring-gray-500/20 transition-all duration-200">
                                ← Cancel
                            </a>
                            <button type="submit"
                                    class="px-8 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 border-2 border-transparent rounded-xl text-sm font-semibold text-white shadow-lg hover:shadow-xl focus:outline-none focus:ring-4 focus:ring-blue-500/20 transform hover:scale-105 transition-all duration-200">
                                ✨ Create Assignment
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Custom Styles -->
    <style>
        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 10px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 10px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
    </style>
</div>
