<div>
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900">
        <!-- Header -->
        <div class="bg-white dark:bg-gray-800 shadow-sm border-b border-gray-200 dark:border-gray-700">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
                    <div class="flex-1 min-w-0">

                        <nav class="flex items-center space-x-2 text-sm text-gray-500 dark:text-gray-400 mb-4">
                            <a href="{{ route('teachers.assignments.index') }}"
                               class="hover:text-gray-700 dark:hover:text-gray-300">Assignments</a>
                            <span>/</span>
                            <a href="{{ route('teachers.assignments.show', ['assignment' => $assignment]) }}"

                               class="hover:text-gray-700 dark:hover:text-gray-300">{{ $assignment->title }}</a>
                            <span>/</span>
                            <span class="font-medium">{{ $student->user->name }}'s Submission</span>
                        </nav>

                        <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                            Submission Review: {{ $assignment->title }}
                        </h1>
                        <p class="text-gray-600 dark:text-gray-400 mt-2">
                            Student: {{ $student->user->name }} • {{ $assignment->academicSubject->name }}
                        </p>
                    </div>

                    <!-- Status and Score -->
                    <div class="mt-4 lg:mt-0 flex flex-col items-end space-y-2">
                        <div class="flex items-center space-x-4">
                            <!-- Status -->
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                                {{ $submission->status === 'graded' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300' :
                                   ($submission->status === 'submitted' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300' :
                                   'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300') }}">
                                {{ ucfirst(str_replace('_', ' ', $submission->status)) }}
                            </span>

                            <!-- Score -->
                            <div class="text-right">
                                <div class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                                    {{ $this->totalScore }}/{{ $this->maxScore }}
                                </div>
                                <div class="text-sm text-gray-500 dark:text-gray-400">
                                    {{ $this->percentage }}% • Grade: {{ $this->grade }}
                                </div>
                            </div>
                        </div>

                        @if($needsGrading)
                            <div class="text-sm text-orange-600 dark:text-orange-400">
                                {{ $this->progress }}% graded • Essay questions pending
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
                <!-- Main Content -->
                <div class="lg:col-span-3">
                    <!-- Submission Details -->
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 mb-6">
                        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                            <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Submission Details</h2>
                        </div>
                        <div class="p-6">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Started At</dt>
                                    <dd class="text-sm text-gray-900 dark:text-gray-100 mt-1">
                                        {{ $submission->started_at ? $submission->started_at->format('M j, Y g:i A') : 'Not started' }}
                                    </dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Submitted At</dt>
                                    <dd class="text-sm text-gray-900 dark:text-gray-100 mt-1">
                                        {{ $submission->submitted_at ? $submission->submitted_at->format('M j, Y g:i A') : 'Not submitted' }}
                                    </dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Time Spent</dt>
                                    <dd class="text-sm text-gray-900 dark:text-gray-100 mt-1">
                                        {{ $submission->time_spent_minutes }} minutes
                                    </dd>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Questions and Answers -->
                    <div class="space-y-6">
                        @foreach($questions as $index => $question)
                            @php
                                $grading = $gradingData[$index] ?? [];
                                $studentAnswer = $grading['student_answer'] ?? null;
                                $isCorrect = $grading['is_correct'] ?? null;
                                $needsManualGrading = $grading['needs_manual_grading'] ?? false;
                            @endphp

                            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
                                <!-- Question Header -->
                                <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                                    <div class="flex items-start justify-between">
                                        <div class="flex-1">
                                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
                                                Question {{ $index + 1 }}
                                                <span class="text-sm text-gray-500 dark:text-gray-400 font-normal ml-2">
                                                    ({{ $question['points'] }} {{ $question['points'] == 1 ? 'point' : 'points' }})
                                                </span>
                                            </h3>

                                            <!-- Question Text -->
                                            <div class="prose prose-gray max-w-none dark:prose-invert bg-gray-50 dark:bg-gray-700 rounded-lg p-4 mb-4">
                                                <x-form.markdown-with-math :content="$question['question']"/>
                                            </div>
                                        </div>
                                        <div class="ml-4 flex flex-col items-end space-y-2">
                                            <!-- Question Type Badge -->
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                {{ $question['type'] === 'multiple_choice_question' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300' :
                                                   ($question['type'] === 'true_or_false_question' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300' :
                                                   'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-300') }}">
                                                {{ ucfirst(str_replace('_', ' ', $question['type'])) }}
                                            </span>

                                            <!-- Points -->
                                            <span class="text-sm text-gray-500 dark:text-gray-400">
                                                {{ $grading['points_earned'] ?? 0 }}/{{ $question['points'] }} pts
                                            </span>

                                            <!-- Grading Status -->
                                            @if($needsManualGrading && !($grading['is_graded'] ?? false))
                                                <button
                                                    wire:click="openGradingPanel({{ $index }})"
                                                    class="inline-flex items-center px-3 py-1 text-xs font-medium text-orange-600 dark:text-orange-400 border border-orange-300 dark:border-orange-600 rounded hover:bg-orange-50 dark:hover:bg-orange-900/20">
                                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                    </svg>
                                                    Grade
                                                </button>
                                            @elseif($isCorrect === true)
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300">
                                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                                    </svg>
                                                    Correct
                                                </span>
                                            @elseif($isCorrect === false)
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300">
                                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                                    </svg>
                                                    Incorrect
                                                </span>
                                            @elseif($needsManualGrading && ($grading['is_graded'] ?? false))
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300">
                                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                                    </svg>
                                                    Graded
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <!-- Question Content -->
                                <div class="p-6">
                                    @if($question['type'] === 'multiple_choice_question')
                                        <div class="space-y-3 mb-6">
                                            <h4 class="font-medium text-gray-900 dark:text-gray-100 text-sm">Answer Options:</h4>
                                            @if($question['options'])
                                                @foreach($question['options'] as $optionKey => $optionValue)
                                                    @php
                                                        $isStudentChoice = strtoupper($studentAnswer ?? '') === strtoupper($optionKey);
                                                        $isCorrectChoice = strtoupper($question['answer'] ?? '') === strtoupper($optionKey);
                                                    @endphp

                                                    <div class="flex items-start p-4 rounded-lg border
                                                        {{ $isStudentChoice && $isCorrectChoice ? 'bg-green-50 border-green-200 dark:bg-green-900/20 dark:border-green-700' :
                                                           ($isStudentChoice && !$isCorrectChoice ? 'bg-red-50 border-red-200 dark:bg-red-900/20 dark:border-red-700' :
                                                           ($isCorrectChoice ? 'bg-green-50 border-green-200 dark:bg-green-900/20 dark:border-green-700' : 'bg-gray-50 border-gray-200 dark:bg-gray-700 dark:border-gray-600')) }}">

                                                        <span class="inline-flex items-center justify-center w-8 h-8 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-full text-sm font-medium text-gray-700 dark:text-gray-300 mr-4 flex-shrink-0">
                                                            {{ $optionKey }}
                                                        </span>

                                                        <div class="flex-1 min-w-0">
                                                            <div class="text-gray-900 dark:text-gray-100">
                                                                <x-form.markdown-with-math :content="$optionValue"/>
                                                            </div>
                                                        </div>

                                                        <div class="flex flex-col items-end space-y-1 ml-4">
                                                            @if($isStudentChoice)
                                                                <span class="inline-flex items-center px-2 py-1 text-xs font-medium rounded
                                                                    {{ $isCorrectChoice ? 'bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100' : 'bg-blue-100 text-blue-800 dark:bg-blue-800 dark:text-blue-100' }}">
                                                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                                        <path d="M10 12l-4-4 1.41-1.41L10 9.17l2.59-2.58L14 8l-4 4z"/>
                                                                    </svg>
                                                                    Student's Answer
                                                                </span>
                                                            @endif
                                                            @if($isCorrectChoice)
                                                                <span class="inline-flex items-center px-2 py-1 text-xs font-medium bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100 rounded">
                                                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                                                    </svg>
                                                                    Correct Answer
                                                                </span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                @endforeach
                                            @else
                                                <p class="text-gray-500 dark:text-gray-400 italic">No options available</p>
                                            @endif

                                            <!-- Student Answer Summary -->
                                            <div class="mt-4 p-3 bg-gray-100 dark:bg-gray-700 rounded-lg">
                                                <div class="text-sm">
                                                    <span class="font-medium text-gray-700 dark:text-gray-300">Student selected:</span>
                                                    <span class="ml-2 {{ $isCorrect ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                                                        @if($studentAnswer)
                                                            Option {{ strtoupper($studentAnswer) }}
                                                            @if($isCorrect)
                                                                (Correct ✓)
                                                            @else
                                                                (Incorrect ✗ - Correct answer was {{ strtoupper($question['answer']) }})
                                                            @endif
                                                        @else
                                                            <span class="text-gray-500 dark:text-gray-400">No answer provided</span>
                                                        @endif
                                                    </span>
                                                </div>
                                            </div>
                                        </div>

                                    @elseif($question['type'] === 'true_or_false_question')
                                        <div class="space-y-3 mb-6">
                                            <h4 class="font-medium text-gray-900 dark:text-gray-100 text-sm">Answer Options:</h4>
                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                @php
                                                    $trueSelected = $studentAnswer === 'true';
                                                    $falseSelected = $studentAnswer === 'false';
                                                    $correctAnswer = $question['answer'] === 'true';
                                                @endphp

                                                <div class="flex items-center justify-between p-4 rounded-lg border
                                                    {{ $trueSelected && $correctAnswer ? 'bg-green-50 border-green-200 dark:bg-green-900/20 dark:border-green-700' :
                                                       ($trueSelected && !$correctAnswer ? 'bg-red-50 border-red-200 dark:bg-red-900/20 dark:border-red-700' :
                                                       ($correctAnswer ? 'bg-green-50 border-green-200 dark:bg-green-900/20 dark:border-green-700' : 'bg-gray-50 border-gray-200 dark:bg-gray-700 dark:border-gray-600')) }}">
                                                    <span class="text-lg font-semibold text-green-700 dark:text-green-300">True</span>
                                                    <div class="flex space-x-2">
                                                        @if($trueSelected)
                                                            <span class="inline-flex items-center px-2 py-1 text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-800 dark:text-blue-100 rounded">
                                                                Student's Answer
                                                            </span>
                                                        @endif
                                                        @if($correctAnswer)
                                                            <span class="inline-flex items-center px-2 py-1 text-xs font-medium bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100 rounded">
                                                                Correct Answer
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>

                                                <div class="flex items-center justify-between p-4 rounded-lg border
                                                    {{ $falseSelected && !$correctAnswer ? 'bg-green-50 border-green-200 dark:bg-green-900/20 dark:border-green-700' :
                                                       ($falseSelected && $correctAnswer ? 'bg-red-50 border-red-200 dark:bg-red-900/20 dark:border-red-700' :
                                                       (!$correctAnswer ? 'bg-green-50 border-green-200 dark:bg-green-900/20 dark:border-green-700' : 'bg-gray-50 border-gray-200 dark:bg-gray-700 dark:border-gray-600')) }}">
                                                    <span class="text-lg font-semibold text-red-700 dark:text-red-300">False</span>
                                                    <div class="flex space-x-2">
                                                        @if($falseSelected)
                                                            <span class="inline-flex items-center px-2 py-1 text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-800 dark:text-blue-100 rounded">
                                                                Student's Answer
                                                            </span>
                                                        @endif
                                                        @if(!$correctAnswer)
                                                            <span class="inline-flex items-center px-2 py-1 text-xs font-medium bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100 rounded">
                                                                Correct Answer
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Student Answer Summary -->
                                            <div class="mt-4 p-3 bg-gray-100 dark:bg-gray-700 rounded-lg">
                                                <div class="text-sm">
                                                    <span class="font-medium text-gray-700 dark:text-gray-300">Student answered:</span>
                                                    <span class="ml-2 {{ $isCorrect ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                                                        @if($studentAnswer !== null)
                                                            {{ ucfirst($studentAnswer) }}
                                                            @if($isCorrect)
                                                                (Correct ✓)
                                                            @else
                                                                (Incorrect ✗ - Correct answer was {{ $correctAnswer ? 'True' : 'False' }})
                                                            @endif
                                                        @else
                                                            <span class="text-gray-500 dark:text-gray-400">No answer provided</span>
                                                        @endif
                                                    </span>
                                                </div>
                                            </div>
                                        </div>

                                    @elseif($question['type'] === 'essay_question')
                                        <div class="mb-4">
                                            <h4 class="font-medium text-gray-900 dark:text-gray-100 mb-3">Student's Answer:</h4>
                                            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 border max-h-64 overflow-y-auto">
                                                @if($studentAnswer)
                                                    <p class="text-gray-900 dark:text-gray-100 whitespace-pre-wrap">{{ $studentAnswer }}</p>
                                                    <div class="mt-3 text-xs text-gray-500 dark:text-gray-400 border-t border-gray-200 dark:border-gray-600 pt-3">
                                                        <div class="flex justify-between">
                                                            <span>Word count: {{ str_word_count($studentAnswer) }}</span>
                                                            <span>Character count: {{ strlen($studentAnswer) }}</span>
                                                        </div>
                                                    </div>
                                                @else
                                                    <p class="text-gray-500 dark:text-gray-400 italic">No answer provided</p>
                                                @endif
                                            </div>

                                            @if($grading['teacher_feedback'] ?? false)
                                                <div class="mt-4">
                                                    <h5 class="font-medium text-gray-900 dark:text-gray-100 mb-2">Teacher Feedback:</h5>
                                                    <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-4 border border-blue-200 dark:border-blue-700">
                                                        <p class="text-blue-900 dark:text-blue-100">{{ $grading['teacher_feedback'] }}</p>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="lg:col-span-1">
                    <!-- Summary -->
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 mb-6">
                        <div class="p-4 border-b border-gray-200 dark:border-gray-700">
                            <h3 class="font-semibold text-gray-900 dark:text-gray-100">Summary</h3>
                        </div>
                        <div class="p-4 space-y-3">
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-500 dark:text-gray-400">Total Questions</span>
                                <span class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ count($questions) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-500 dark:text-gray-400">Answered</span>
                                <span class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ count(array_filter($answers)) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-500 dark:text-gray-400">Score</span>
                                <span class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $this->totalScore }}/{{ $this->maxScore }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-500 dark:text-gray-400">Percentage</span>
                                <span class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $this->percentage }}%</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-500 dark:text-gray-400">Grade</span>
                                <span class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $this->grade }}</span>
                            </div>
                        </div>
                    </div>

                    @if($needsGrading)
                        <!-- Grading Progress -->
                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
                            <div class="p-4 border-b border-gray-200 dark:border-gray-700">
                                <h3 class="font-semibold text-gray-900 dark:text-gray-100">Grading Progress</h3>
                            </div>
                            <div class="p-4">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-sm text-gray-600 dark:text-gray-300">Progress</span>
                                    <span class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $this->progress }}%</span>
                                </div>
                                <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                    <div class="bg-gradient-to-r from-blue-500 to-purple-600 h-2 rounded-full transition-all duration-300" style="width: {{ $this->progress }}%"></div>
                                </div>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">
                                    {{ collect($gradingData)->where('needs_manual_grading', true)->where('is_graded', false)->count() }} essay questions need grading
                                </p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Essay Grading Modal -->
        @if($showGradingPanel && $currentEssayIndex !== null)
            <div class="fixed inset-0 z-50 overflow-y-auto" x-data="{ show: @entangle('showGradingPanel') }" x-show="show">
                <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                    <div class="fixed inset-0 transition-opacity bg-gray-900 bg-opacity-50 backdrop-blur-sm" @click="$wire.closeGradingPanel()"></div>

                    <div class="inline-block w-full max-w-4xl my-8 overflow-hidden text-left align-middle transition-all transform bg-white dark:bg-gray-800 shadow-2xl rounded-xl">
                        <!-- Modal Header -->
                        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                            <div class="flex items-center justify-between">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                                    Grade Essay Question {{ $currentEssayIndex + 1 }}
                                </h3>
                                <button @click="$wire.closeGradingPanel()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <!-- Modal Content -->
                        <div class="px-6 py-4">
                            @if($currentEssayIndex !== null && isset($questions[$currentEssayIndex]))
                                <!-- Question -->
                                <div class="mb-6">
                                    <h4 class="font-medium text-gray-900 dark:text-gray-100 mb-3">Question:</h4>
                                    <div class="prose prose-gray max-w-none dark:prose-invert bg-gray-50 dark:bg-gray-700 rounded-lg p-4 border">
                                        <x-form.markdown-with-math :content="$questions[$currentEssayIndex]['question']"/>
                                    </div>
                                </div>

                                <!-- Student Answer -->
                                <div class="mb-6">
                                    <h4 class="font-medium text-gray-900 dark:text-gray-100 mb-3">Student's Answer:</h4>
                                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 border max-h-64 overflow-y-auto">
                                        @if($gradingData[$currentEssayIndex]['student_answer'] ?? false)
                                            <p class="text-gray-900 dark:text-gray-100 whitespace-pre-wrap">{{ $gradingData[$currentEssayIndex]['student_answer'] }}</p>
                                            <div class="mt-3 text-xs text-gray-500 dark:text-gray-400 border-t border-gray-200 dark:border-gray-600 pt-3">
                                                <div class="flex justify-between">
                                                    <span>Words: {{ str_word_count($gradingData[$currentEssayIndex]['student_answer']) }}</span>
                                                    <span>Characters: {{ strlen($gradingData[$currentEssayIndex]['student_answer']) }}</span>
                                                </div>
                                            </div>
                                        @else
                                            <p class="text-gray-500 dark:text-gray-400 italic">No answer provided</p>
                                        @endif
                                    </div>
                                </div>

                                <!-- Grading Form -->
                                <form wire:submit.prevent="gradeEssayQuestion">
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                                        <div>
                                            <label for="essayGrade" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                                Points Earned (0 - {{ $questions[$currentEssayIndex]['points'] }})
                                            </label>
                                            <input type="number"
                                                   wire:model="essayGrade"
                                                   id="essayGrade"
                                                   min="0"
                                                   max="{{ $questions[$currentEssayIndex]['points'] }}"
                                                   step="0.5"
                                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-gray-300"
                                                   placeholder="Enter points earned">
                                            @error('essayGrade') <p class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p> @enderror
                                        </div>

                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                                Quick Grade Options
                                            </label>
                                            <div class="flex space-x-2">
                                                <button type="button"
                                                        wire:click="$set('essayGrade', {{ $questions[$currentEssayIndex]['points'] }})"
                                                        class="px-3 py-1 text-xs bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300 rounded hover:bg-green-200 dark:hover:bg-green-800">
                                                    Full ({{ $questions[$currentEssayIndex]['points'] }})
                                                </button>
                                                <button type="button"
                                                        wire:click="$set('essayGrade', {{ round($questions[$currentEssayIndex]['points'] * 0.75, 1) }})"
                                                        class="px-3 py-1 text-xs bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300 rounded hover:bg-yellow-200 dark:hover:bg-yellow-800">
                                                    75% ({{ round($questions[$currentEssayIndex]['points'] * 0.75, 1) }})
                                                </button>
                                                <button type="button"
                                                        wire:click="$set('essayGrade', {{ round($questions[$currentEssayIndex]['points'] * 0.5, 1) }})"
                                                        class="px-3 py-1 text-xs bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-300 rounded hover:bg-orange-200 dark:hover:bg-orange-800">
                                                    50% ({{ round($questions[$currentEssayIndex]['points'] * 0.5, 1) }})
                                                </button>
                                                <button type="button"
                                                        wire:click="$set('essayGrade', 0)"
                                                        class="px-3 py-1 text-xs bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300 rounded hover:bg-red-200 dark:hover:bg-red-800">
                                                    0
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mb-6">
                                        <label for="essayFeedback" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                            Feedback for Student (Optional)
                                        </label>
                                        <textarea wire:model="essayFeedback"
                                                  id="essayFeedback"
                                                  rows="4"
                                                  class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-gray-300 resize-none"
                                                  placeholder="Provide constructive feedback to help the student improve..."></textarea>
                                        @error('essayFeedback') <p class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p> @enderror
                                    </div>

                                    <div class="flex justify-end space-x-3">
                                        <button type="button"
                                                @click="$wire.closeGradingPanel()"
                                                class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-600">
                                            Cancel
                                        </button>
                                        <button type="submit"
                                                class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                            Save Grade & Feedback
                                        </button>
                                    </div>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
