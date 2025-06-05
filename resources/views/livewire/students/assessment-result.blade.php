<x-app-layout>
    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow sm:rounded-lg p-6">

                <!-- Header -->
                <h2 class="text-2xl font-bold mb-6">Assessment Results</h2>

                <!-- Summary -->
                <div class="grid grid-cols-2 gap-4 mb-6">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Total Score</p>
                        <p class="text-xl font-semibold">{{ $result['total_score'] }} / {{ $result['max_score'] }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Percentage</p>
                        <p class="text-xl font-semibold">{{ $result['percentage_score'] }}%</p>
                    </div>
                    @if($result['needs_grading'])
                        <div class="col-span-2 mt-4">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">
                                Needs Grading (Essay Questions)
                            </span>
                        </div>
                    @endif
                </div>

                <!-- Question List -->
                <div class="mt-8 space-y-8">
                    @foreach ($result['questions'] as $question)
                        <div class="border-b border-gray-200 dark:border-gray-700 pb-6">
                            <div class="flex justify-between mb-2">
                                <span class="text-sm font-medium px-2 py-1 rounded bg-gray-100 dark:bg-gray-700">
                                    {{ ucfirst(str_replace('_', ' ', $question['question_type'])) }}
                                </span>
                                <span class="text-sm font-medium px-2 py-1 rounded
                                    @if($question['is_correct']) bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                    @else bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200
                                    @endif">
                                    {{ $question['score'] }} / {{ $question['max_score'] }}
                                </span>
                            </div>

                            <h3 class="text-lg font-medium mb-4">{{ $question['question_text'] }}</h3>

                            <!-- Options -->
                            @if(count($question['options']))
                                <ul class="mb-4 space-y-2">
                                    @foreach ($question['options'] as $option)
                                        <li class="flex items-center">
                                            <span class="font-mono mr-2">{{ $option['label'] }}.</span>
                                            <span>{{ $option['value'] }}</span>
                                        </li>
                                    @endforeach
                                </ul>
                            @endif

                            <!-- User Answer -->
                            <div class="mb-2">
                                <strong>Your Answer:</strong> {{ $question['user_answer'] ?: 'Not answered' }}
                            </div>

                            <!-- Correct Answer -->
                            @if(isset($question['correct_answer']))
                                <div class="mb-2">
                                    <strong>Correct Answer:</strong> {{ $question['correct_answer'] }}
                                </div>
                            @endif

                            <!-- Feedback -->
                            <div class="@if($question['is_correct']) text-green-600 dark:text-green-400 @else text-red-600 dark:text-red-400 @endif font-semibold">
                                {{ $question['is_correct'] ? 'Correct ✅' : 'Incorrect ❌' }}
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Export Button -->
                <div class="mt-8 flex justify-end">
                    <a href="{{ route('student.assessment.export', ['id' => $this->assessment->id]) }}"
                       class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-md shadow-sm text-sm font-medium">
                        Export Results
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
