<div>
    @if($step === 'setup')
        <div class="max-w-5xl mx-auto space-y-8">
            <!-- Enhanced Header Section -->
            <div class="bg-gradient-to-r from-green-500 via-emerald-600 to-teal-600 rounded-xl p-8 text-white shadow-lg relative overflow-hidden">
                <div class="absolute top-0 right-0 -mt-4 -mr-4 w-24 h-24 bg-white/10 rounded-full blur-2xl"></div>
                <div class="absolute bottom-0 left-0 -mb-4 -ml-4 w-32 h-32 bg-white/10 rounded-full blur-2xl"></div>

                <div class="relative flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <div class="p-4 bg-white/20 backdrop-blur-sm rounded-full">
                            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                            </svg>
                        </div>
                        <div>
                            <h1 class="text-4xl font-bold">Assignment Practice</h1>
                            <p class="text-green-100 mt-2 text-lg">Practice with teacher-created assignments</p>
                        </div>
                    </div>

                    <!-- Progress Indicator -->
                    <div class="hidden lg:block text-center bg-white/10 backdrop-blur-sm rounded-xl p-4">
                        <div class="text-2xl font-bold text-yellow-300">Step 1</div>
                        <div class="text-sm text-green-200">Setup</div>
                    </div>
                </div>
            </div>

            <!-- Enhanced Form Container -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
                <!-- Progress Bar -->
                <div class="h-2 bg-gray-200 dark:bg-gray-700">
                    <div class="h-2 bg-gradient-to-r from-green-500 to-teal-600 rounded-full" style="width: 33%"></div>
                </div>

                <div class="p-8 space-y-10">
                    <!-- Assignment Selection -->
                    <div class="space-y-6">
                        <div class="flex items-center space-x-3">
                            <div class="p-3 bg-green-100 dark:bg-green-900/30 rounded-xl">
                                <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                                </svg>
                            </div>
                            <div>
                                <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Select Assignment</h2>
                                <p class="text-gray-600 dark:text-gray-400">Choose an assignment to practice with</p>
                            </div>
                        </div>

                        @if($availableAssignments->isEmpty())
                            <div class="text-center py-12">
                                <div class="mx-auto w-24 h-24 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mb-4">
                                    <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                                    </svg>
                                </div>
                                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-2">No Assignments Available</h3>
                                <p class="text-gray-600 dark:text-gray-400">There are no assignments available for practice at this time.</p>
                            </div>
                        @else
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                @foreach($availableAssignments as $assignment)
                                    <div wire:click="$set('selectedAssignment', {{ $assignment->id }})"
                                         class="cursor-pointer p-6 border-2 rounded-xl transition-all transform hover:scale-105 {{ $selectedAssignment == $assignment->id ? 'border-green-500 bg-green-50 dark:bg-green-900/20 shadow-lg' : 'border-gray-300 hover:border-gray-400 hover:shadow-md' }}">
                                        <div class="flex items-start space-x-4">
                                            <div class="p-3 bg-green-100 dark:bg-green-900/30 rounded-xl">
                                                <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                                </svg>
                                            </div>
                                            <div class="flex-1">
                                                <h4 class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $assignment->title }}</h4>
                                                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">{{ $assignment->academicSubject->title }}</p>
                                                @if($assignment->description)
                                                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">{{ Str::limit($assignment->description, 100) }}</p>
                                                @endif
                                                <div class="flex items-center space-x-4 mt-3">
                                                    @if($assignment->duration_in_minutes)
                                                        <span class="px-2 py-1 bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 text-xs rounded-full">
                                                            {{ $assignment->duration_in_minutes }} min
                                                        </span>
                                                    @endif
                                                    <span class="px-2 py-1 bg-gray-100 dark:bg-gray-900 text-gray-800 dark:text-gray-200 text-xs rounded-full">
                                                        {{ ucfirst($assignment->type) }}
                                                    </span>
                                                    @if($assignment->total_marks)
                                                        <span class="px-2 py-1 bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200 text-xs rounded-full">
                                                            {{ $assignment->total_marks }} marks
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    <!-- Action Buttons -->
                    @if(!$availableAssignments->isEmpty())
                        <div class="flex justify-end space-x-4 pt-6 border-t border-gray-200 dark:border-gray-700">
                            <button wire:click="startAssessment"
                                    @disabled(!$selectedAssignment)
                                    class="px-8 py-3 bg-gradient-to-r from-green-500 to-teal-600 text-white rounded-xl font-semibold hover:from-green-600 hover:to-teal-700 focus:outline-none focus:ring-2 focus:ring-green-500 transform hover:scale-105 transition-all duration-200 shadow-lg disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none">
                                Start Practice
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @elseif($step === 'assessment')
        @include('livewire.students.partials.assessment-interface')
    @elseif($step === 'results')
        @include('livewire.students.partials.assessment-results')
    @endif
</div>
