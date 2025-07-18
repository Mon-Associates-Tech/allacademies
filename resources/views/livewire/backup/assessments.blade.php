<div>
    @if($step === 'setup')
        <div class="max-w-5xl mx-auto space-y-8">
            <!-- Enhanced Header Section -->
            <div class="bg-gradient-to-r from-blue-500 via-indigo-600 to-purple-600 rounded-xl p-8 text-white shadow-lg relative overflow-hidden">
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
                            <h1 class="text-4xl font-bold">Self Assessment</h1>
                            <p class="text-blue-100 mt-2 text-lg">Create your personalized learning assessment</p>
                        </div>
                    </div>

                    <!-- Progress Indicator -->
                    <div class="hidden lg:block text-center bg-white/10 backdrop-blur-sm rounded-xl p-4">
                        <div class="text-2xl font-bold text-yellow-300">Step 1</div>
                        <div class="text-sm text-blue-200">Setup</div>
                    </div>
                </div>
            </div>

            <!-- Enhanced Form Container -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
                <!-- Progress Bar -->
                <div class="h-2 bg-gray-200 dark:bg-gray-700">
                    <div class="h-2 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full" style="width: 33%"></div>
                </div>

                <div class="p-8 space-y-10">
                    <!-- Subject Selection -->
                    <div class="space-y-6">
                        <div class="flex items-center space-x-3">
                            <div class="p-3 bg-blue-100 dark:bg-blue-900/30 rounded-xl">
                                <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                </svg>
                            </div>
                            <div>
                                <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Subject & Topic</h2>
                                <p class="text-gray-600 dark:text-gray-400">Choose your subject and specific topics</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <!-- Subject Selection -->
                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Subject</label>
                                <select wire:model.live="selectedSubject" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                                    <option value="">Select Subject</option>
                                    @foreach($subjects as $subject)
                                        <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Topic Selection -->
                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Topic (Optional)</label>
                                <select wire:model.live="selectedTopic" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white" @disabled(!$selectedSubject)>
                                    <option value="">All Topics</option>
                                    @foreach($topics as $topic)
                                        <option value="{{ $topic->id }}">{{ $topic->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Subtopic Selection -->
                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Subtopic (Optional)</label>
                                <select wire:model.live="selectedSubtopic" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white" @disabled(!$selectedTopic)>
                                    <option value="">All Subtopics</option>
                                    @foreach($subtopics as $subtopic)
                                        <option value="{{ $subtopic->id }}">{{ $subtopic->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Question Types -->
                    <div class="space-y-6">
                        <div class="flex items-center space-x-3">
                            <div class="p-3 bg-green-100 dark:bg-green-900/30 rounded-xl">
                                <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <div>
                                <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Question Types</h2>
                                <p class="text-gray-600 dark:text-gray-400">Select the types of questions you want to include</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <!-- Multiple Choice -->
                            <label class="flex items-center p-4 border-2 rounded-xl cursor-pointer transition-all {{ $questionTypes['multiple_choice_question'] ? 'border-blue-500 bg-blue-50 dark:bg-blue-900/20' : 'border-gray-300 hover:border-gray-400' }}">
                                <input type="checkbox" wire:model.live="questionTypes.multiple_choice_question" class="sr-only">
                                <div class="flex items-center space-x-3">
                                    <div class="p-2 bg-blue-100 dark:bg-blue-900/30 rounded-lg">
                                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <div class="font-medium">Multiple Choice</div>
                                        <div class="text-sm text-gray-600 dark:text-gray-400">Choose from options</div>
                                    </div>
                                </div>
                            </label>

                            <!-- True/False -->
                            <label class="flex items-center p-4 border-2 rounded-xl cursor-pointer transition-all {{ $questionTypes['true_or_false_question'] ? 'border-blue-500 bg-blue-50 dark:bg-blue-900/20' : 'border-gray-300 hover:border-gray-400' }}">
                                <input type="checkbox" wire:model.live="questionTypes.true_or_false_question" class="sr-only">
                                <div class="flex items-center space-x-3">
                                    <div class="p-2 bg-green-100 dark:bg-green-900/30 rounded-lg">
                                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <div class="font-medium">True/False</div>
                                        <div class="text-sm text-gray-600 dark:text-gray-400">Simple yes/no questions</div>
                                    </div>
                                </div>
                            </label>

                            <!-- Essay -->
                            <label class="flex items-center p-4 border-2 rounded-xl cursor-pointer transition-all {{ $questionTypes['essay_question'] ? 'border-blue-500 bg-blue-50 dark:bg-blue-900/20' : 'border-gray-300 hover:border-gray-400' }}">
                                <input type="checkbox" wire:model.live="questionTypes.essay_question" class="sr-only">
                                <div class="flex items-center space-x-3">
                                    <div class="p-2 bg-purple-100 dark:bg-purple-900/30 rounded-lg">
                                        <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <div class="font-medium">Essay</div>
                                        <div class="text-sm text-gray-600 dark:text-gray-400">Written responses</div>
                                    </div>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Assessment Configuration -->
                    <div class="space-y-6">
                        <div class="flex items-center space-x-3">
                            <div class="p-3 bg-orange-100 dark:bg-orange-900/30 rounded-xl">
                                <svg class="w-6 h-6 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <div>
                                <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Assessment Settings</h2>
                                <p class="text-gray-600 dark:text-gray-400">Configure your assessment parameters</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <!-- Question Count -->
                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Number of Questions</label>
                                <select wire:model.live="questionCount" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                                    @for($i = 5; $i <= 50; $i += 5)
                                        <option value="{{ $i }}">{{ $i }} Questions</option>
                                    @endfor
                                </select>
                            </div>

                            <!-- Difficulty Level -->
                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Difficulty Level</label>
                                <select wire:model.live="difficulty" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                                    <option value="all">All Levels</option>
                                    <option value="easy">Easy</option>
                                    <option value="medium">Medium</option>
                                    <option value="hard">Hard</option>
                                </select>
                            </div>

                            <!-- Time Limit -->
                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Time Limit (Optional)</label>
                                <select wire:model.live="timeLimitMinutes" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                                    <option value="">No Time Limit</option>
                                    <option value="10">10 Minutes</option>
                                    <option value="15">15 Minutes</option>
                                    <option value="30">30 Minutes</option>
                                    <option value="45">45 Minutes</option>
                                    <option value="60">1 Hour</option>
                                    <option value="90">1.5 Hours</option>
                                    <option value="120">2 Hours</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex justify-end space-x-4 pt-6 border-t border-gray-200 dark:border-gray-700">
                        <button wire:click="startAssessment" class="px-8 py-3 bg-gradient-to-r from-blue-500 to-purple-600 text-white rounded-xl font-semibold hover:from-blue-600 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-blue-500 transform hover:scale-105 transition-all duration-200 shadow-lg">
                            Start Assessment
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @elseif($step === 'assessment')
        @include('livewire.students.partials.assessment-interface')
    @elseif($step === 'results')
        @include('livewire.students.partials.assessment-results')
    @endif
</div>
