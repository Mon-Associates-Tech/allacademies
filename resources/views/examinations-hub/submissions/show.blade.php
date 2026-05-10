<x-layouts.app>
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <div class="mb-6">
            <a href="{{ route('examinations-hub.submissions.index', $exam) }}" class="text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 text-sm">
                ← Back to Submissions
            </a>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white mt-2">Submission Details</h1>
            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">{{ $exam->title }}</p>
        </div>

        <div class="grid md:grid-cols-2 gap-6 mb-6">
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Participant Information</h2>
                <dl class="space-y-3">
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Name</dt>
                        <dd class="text-base text-gray-900 dark:text-white mt-1">{{ $submission->participant_name ?? $submission->getParticipantName() }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Email</dt>
                        <dd class="text-base text-gray-900 dark:text-white mt-1">{{ $submission->participant_email ?? $submission->getParticipantEmail() }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Participant Type</dt>
                        <dd class="text-base text-gray-900 dark:text-white mt-1">{{ ucfirst($submission->participant_type) }}</dd>
                    </div>
                </dl>
            </div>

            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Performance Summary</h2>
                <dl class="space-y-3">
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Score</dt>
                        <dd class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ $submission->score ?? 0 }}/{{ $submission->total_marks ?? 0 }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Percentage</dt>
                        <dd class="text-2xl font-bold text-indigo-600 dark:text-indigo-400 mt-1">{{ number_format($submission->percentage ?? 0, 1) }}%</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Grade</dt>
                        <dd class="mt-1">
                            <span class="inline-flex items-center px-3 py-1 text-lg font-semibold rounded-full
                                {{ in_array($submission->grade, ['A+', 'A']) ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : '' }}
                                {{ in_array($submission->grade, ['B', 'C']) ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200' : '' }}
                                {{ in_array($submission->grade, ['D', 'F']) ? 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' : '' }}
                            ">
                                {{ $submission->grade ?? 'N/A' }}
                            </span>
                        </dd>
                    </div>
                </dl>
            </div>
        </div>

        <div class="grid md:grid-cols-3 gap-4 mb-6">
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                <p class="text-sm text-gray-600 dark:text-gray-400">Time Taken</p>
                <p class="text-xl font-bold text-gray-900 dark:text-white mt-1">{{ $submission->time_taken_minutes ?? 0 }} min</p>
            </div>
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                <p class="text-sm text-gray-600 dark:text-gray-400">Status</p>
                <p class="text-xl font-bold text-gray-900 dark:text-white mt-1">{{ ucfirst($submission->status ?? 'unknown') }}</p>
            </div>
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                <p class="text-sm text-gray-600 dark:text-gray-400">Submitted At</p>
                <p class="text-xl font-bold text-gray-900 dark:text-white mt-1">{{ optional($submission->submitted_at)?->format('M d, H:i') ?? 'N/A' }}</p>
            </div>
        </div>

        @php
            $exam->load('questions.section');
            $responses = $submission->responses ?? [];
            $questionsBySection = $exam->questions->groupBy(fn($q) => $q->section?->title ?? 'Unsectioned');
        @endphp

        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-6">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-6">Detailed Responses</h2>
            
            @foreach($questionsBySection as $sectionTitle => $questions)
                <div class="mb-8">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 pb-2 border-b border-gray-200 dark:border-gray-700">
                        {{ $sectionTitle }}
                    </h3>
                    
                    <div class="space-y-6">
                        @foreach($questions as $index => $question)
                            @php
                                $response = $responses[$question->id] ?? null;
                                $isCorrect = $response['is_correct'] ?? null;
                                $studentAnswer = $response['response'] ?? null;
                                $pointsEarned = $response['points_earned'] ?? 0;
                            @endphp
                            
                            <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4
                                {{ $isCorrect === true ? 'bg-green-50 dark:bg-green-900/10 border-green-200 dark:border-green-800' : '' }}
                                {{ $isCorrect === false ? 'bg-red-50 dark:bg-red-900/10 border-red-200 dark:border-red-800' : '' }}
                            ">
                                <div class="flex items-start justify-between mb-3">
                                    <div class="flex-1">
                                        <p class="font-medium text-gray-900 dark:text-white">Question {{ $loop->parent->iteration }}.{{ $loop->iteration }}</p>
                                        <div class="text-gray-700 dark:text-gray-300 mt-2">
                                            <x-form.markdown-with-math :content="$question->getFormattedQuestion()" class="prose dark:prose-invert max-w-none" />
                                        </div>
                                    </div>
                                    <div class="ml-4 flex items-center gap-2">
                                        @if($isCorrect === true)
                                            <span class="inline-flex items-center px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                                ✓ Correct
                                            </span>
                                        @elseif($isCorrect === false)
                                            <span class="inline-flex items-center px-2 py-1 text-xs font-medium rounded-full bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
                                                ✗ Incorrect
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200">
                                                Pending Review
                                            </span>
                                        @endif
                                        <span class="text-sm font-semibold text-gray-700 dark:text-gray-300">{{ $pointsEarned }}/{{ $question->marks }}</span>
                                    </div>
                                </div>

                                @if($question->isMultipleChoice() || $question->isTrueFalse())
                                    <div class="mt-3 space-y-2">
                                        @if($question->isMultipleChoice())
                                            @foreach($question->getOptionsForDisplay() as $optionKey => $optionText)
                                                @php
                                                    $isStudentAnswer = $studentAnswer === $optionKey;
                                                    $isCorrectAnswer = $question->correct_answer === $optionKey;
                                                @endphp
                                                <div class="flex items-center gap-2 p-2 rounded
                                                    {{ $isStudentAnswer && $isCorrectAnswer ? 'bg-green-100 dark:bg-green-900/20' : '' }}
                                                    {{ $isStudentAnswer && !$isCorrectAnswer ? 'bg-red-100 dark:bg-red-900/20' : '' }}
                                                    {{ !$isStudentAnswer && $isCorrectAnswer ? 'bg-blue-50 dark:bg-blue-900/10' : '' }}
                                                ">
                                                    <span class="font-medium text-gray-700 dark:text-gray-300">{{ $optionKey }}.</span>
                                                    <x-form.markdown-with-math :content="$optionText" class="inline text-gray-700 dark:text-gray-300" />
                                                    @if($isStudentAnswer)
                                                        <span class="ml-auto text-xs text-gray-600 dark:text-gray-400">(Your answer)</span>
                                                    @endif
                                                    @if($isCorrectAnswer)
                                                        <span class="ml-auto text-xs text-green-600 dark:text-green-400">✓ Correct</span>
                                                    @endif
                                                </div>
                                            @endforeach
                                        @else
                                            <div class="flex items-center gap-2 p-2 rounded {{ $studentAnswer === 'True' ? 'bg-blue-50 dark:bg-blue-900/10' : '' }}">
                                                <span class="font-medium text-gray-700 dark:text-gray-300">True</span>
                                                @if($studentAnswer === 'True')
                                                    <span class="ml-auto text-xs text-gray-600 dark:text-gray-400">(Your answer)</span>
                                                @endif
                                                @if($question->correct_answer === 'True' || $question->correct_answer === '1')
                                                    <span class="ml-auto text-xs text-green-600 dark:text-green-400">✓ Correct</span>
                                                @endif
                                            </div>
                                            <div class="flex items-center gap-2 p-2 rounded {{ $studentAnswer === 'False' ? 'bg-blue-50 dark:bg-blue-900/10' : '' }}">
                                                <span class="font-medium text-gray-700 dark:text-gray-300">False</span>
                                                @if($studentAnswer === 'False')
                                                    <span class="ml-auto text-xs text-gray-600 dark:text-gray-400">(Your answer)</span>
                                                @endif
                                                @if($question->correct_answer === 'False' || $question->correct_answer === '0')
                                                    <span class="ml-auto text-xs text-green-600 dark:text-green-400">✓ Correct</span>
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                @else
                                    <div class="mt-3">
                                        <p class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Student Answer:</p>
                                        <div class="bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded p-3">
                                            <p class="text-gray-700 dark:text-gray-300 whitespace-pre-wrap">{{ $studentAnswer ?? 'No answer provided' }}</p>
                                        </div>
                                    </div>
                                @endif

                                @if(!empty($response['feedback']))
                                    <div class="mt-3 p-3 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded">
                                        <p class="text-sm font-medium text-blue-900 dark:text-blue-100">Feedback:</p>
                                        <p class="text-sm text-blue-800 dark:text-blue-200 mt-1">{{ $response['feedback'] }}</p>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</x-layouts.app>

