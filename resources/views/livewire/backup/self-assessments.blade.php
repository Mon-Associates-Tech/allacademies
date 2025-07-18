<div>
    @if($currentView === 'selection')
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
                            <h1 class="text-4xl font-bold">Assessment Center</h1>
                            <p class="text-indigo-100 mt-2 text-lg">Choose your learning path</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Assessment Mode Selection -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="p-8 space-y-10">
                    <div class="space-y-6">
                        <div class="flex items-center space-x-3">
                            <div class="p-3 bg-emerald-100 dark:bg-emerald-900/30 rounded-xl">
                                <svg class="w-6 h-6 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                                </svg>
                            </div>
                            <div>
                                <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Choose Assessment Type</h2>
                                <p class="text-gray-600 dark:text-gray-400">Select how you want to practice and learn</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <button wire:click="switchToSelfAssessment"
                                    class="p-6 border-2 rounded-xl text-left transition-all transform hover:scale-105 border-gray-300 hover:border-blue-400 hover:shadow-lg">
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

                            <button wire:click="switchToAssignmentPractice"
                                    class="p-6 border-2 rounded-xl text-left transition-all transform hover:scale-105 border-gray-300 hover:border-green-400 hover:shadow-lg">
                                <div class="flex items-center space-x-4">
                                    <div class="p-3 bg-green-100 dark:bg-green-900/30 rounded-xl">
                                        <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                                        </svg>
                                    </div>
                                    <div class="flex-1">
                                        <h4 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Assignment Practice</h4>
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
                </div>
            </div>
        </div>
    @elseif($currentView === 'self-assessment')
        <div class="mb-6">
            <button wire:click="backToSelection" class="flex items-center space-x-2 text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                <span>Back to Selection</span>
            </button>
        </div>
        @livewire('students.self-assessment-configuration')
    @elseif($currentView === 'assignment-practice')
        <div class="mb-6">
            <button wire:click="backToSelection" class="flex items-center space-x-2 text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                <span>Back to Selection</span>
            </button>
        </div>
        @livewire('students.assignments')
    @endif
</div>
