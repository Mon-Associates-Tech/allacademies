<div class="max-w-6xl mx-auto p-6">
    <div class="bg-white rounded-lg shadow-lg p-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-8">Random Question Generator Test</h1>

        <!-- Flash Messages -->
        @if (session()->has('success'))
            <div class="mb-6 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg">
                {{ session('success') }}
            </div>
        @endif

        @if (session()->has('error'))
            <div class="mb-6 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">
                {{ session('error') }}
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Left Column: Configuration -->
            <div class="space-y-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Configuration</h2>

                <!-- Subject Selection -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Select Subject *
                    </label>
                    <select
                        wire:model.live="selectedSubject"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    >
                        <option value="">Choose a subject...</option>
                        @foreach($subjects as $subject)
                            <option value="{{ $subject->id }}">
                                {{ $subject->name }}
                                @if($subject->academicLevel)
                                    ({{ $subject->academicLevel->name }})
                                @endif
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Topic Selection -->
                @if($selectedSubject && count($topics) > 0)
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Select Topic (Optional)
                        </label>
                        <select
                            wire:model.live="selectedTopic"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        >
                            <option value="">All topics</option>
                            @foreach($topics as $topic)
                                <option value="{{ $topic->id }}">{{ $topic->name }}</option>
                            @endforeach
                        </select>
                    </div>
                @endif

                <!-- Subtopic Selection -->
                @if($selectedTopic && count($subtopics) > 0)
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Select Subtopic (Optional)
                        </label>
                        <select
                            wire:model.live="selectedSubtopic"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        >
                            <option value="">All subtopics</option>
                            @foreach($subtopics as $subtopic)
                                <option value="{{ $subtopic->id }}">{{ $subtopic->name }}</option>
                            @endforeach
                        </select>
                    </div>
                @endif

                <!-- Question Types -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-3">
                        Question Types *
                    </label>
                    <div class="space-y-2">
                        <label class="flex items-center">
                            <input
                                type="checkbox"
                                wire:model.live="questionTypes.multiple_choice_question"
                                class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500"
                            >
                            <span class="ml-2 text-sm text-gray-700">Multiple Choice Questions</span>
                        </label>
                        <label class="flex items-center">
                            <input
                                type="checkbox"
                                wire:model.live="questionTypes.true_or_false_question"
                                class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500"
                            >
                            <span class="ml-2 text-sm text-gray-700">True/False Questions</span>
                        </label>
                        <label class="flex items-center">
                            <input
                                type="checkbox"
                                wire:model.live="questionTypes.essay_question"
                                class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500"
                            >
                            <span class="ml-2 text-sm text-gray-700">Essay Questions</span>
                        </label>
                    </div>
                </div>

                <!-- Question Count -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Number of Questions *
                    </label>
                    <input
                        type="number"
                        wire:model.live="questionCount"
                        min="1"
                        max="50"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    >
                </div>

                <!-- Difficulty Level -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Difficulty Level
                    </label>
                    <select
                        wire:model.live="difficulty"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    >
                        <option value="all">All Levels</option>
                        <option value="easy">Easy</option>
                        <option value="medium">Medium</option>
                        <option value="hard">Hard</option>
                    </select>
                </div>

                <!-- Balanced Distribution -->
                <div>
                    <label class="flex items-center">
                        <input
                            type="checkbox"
                            wire:model.live="balancedDistribution"
                            class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500"
                        >
                        <span class="ml-2 text-sm text-gray-700">Balanced difficulty distribution</span>
                    </label>
                </div>

                <!-- Generate Button -->
                <div class="flex space-x-4">
                    <button
                        wire:click="generateQuestions"
                        class="flex-1 bg-blue-600 text-white py-3 px-6 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 font-medium"
                        @disabled(!$selectedSubject)
                    >
                        Generate Questions
                    </button>

                    <button
                        wire:click="debugData"
                        class="bg-gray-600 text-white py-3 px-6 rounded-md hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 font-medium"
                        @disabled(!$selectedSubject)
                    >
                        Debug
                    </button>
                </div>
            </div>

            <!-- Right Column: Statistics and Results -->
            <div class="space-y-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Statistics & Results</h2>

                <!-- Available Questions -->
                @if(!empty($questionCounts))
                    <div class="bg-blue-50 p-4 rounded-lg">
                        <h3 class="text-lg font-medium text-gray-900 mb-3">Available Questions</h3>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="text-center">
                                <div class="text-2xl font-bold text-blue-600">{{ $questionCounts['multiple_choice_question'] ?? 0 }}</div>
                                <div class="text-sm text-gray-600">Multiple Choice</div>
                            </div>
                            <div class="text-center">
                                <div class="text-2xl font-bold text-green-600">{{ $questionCounts['true_or_false_question'] ?? 0 }}</div>
                                <div class="text-sm text-gray-600">True/False</div>
                            </div>
                            <div class="text-center">
                                <div class="text-2xl font-bold text-purple-600">{{ $questionCounts['essay_question'] ?? 0 }}</div>
                                <div class="text-sm text-gray-600">Essay</div>
                            </div>
                            <div class="text-center">
                                <div class="text-2xl font-bold text-gray-900">{{ $questionCounts['total'] ?? 0 }}</div>
                                <div class="text-sm text-gray-600">Total</div>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Question Distribution by Difficulty -->
                @if(!empty($questionDistribution))
                    <div class="bg-green-50 p-4 rounded-lg">
                        <h3 class="text-lg font-medium text-gray-900 mb-3">Distribution by Difficulty</h3>
                        <div class="space-y-3">
                            @foreach($questionDistribution as $type => $difficulties)
                                <div class="border-b border-gray-200 pb-2">
                                    <h4 class="font-medium text-gray-800 mb-1">{{ ucfirst(str_replace('_', ' ', $type)) }}</h4>
                                    <div class="grid grid-cols-4 gap-2 text-sm">
                                        <div class="text-center">
                                            <div class="font-bold text-green-600">{{ $difficulties['easy'] }}</div>
                                            <div class="text-gray-600">Easy</div>
                                        </div>
                                        <div class="text-center">
                                            <div class="font-bold text-yellow-600">{{ $difficulties['medium'] }}</div>
                                            <div class="text-gray-600">Medium</div>
                                        </div>
                                        <div class="text-center">
                                            <div class="font-bold text-red-600">{{ $difficulties['hard'] }}</div>
                                            <div class="text-gray-600">Hard</div>
                                        </div>
                                        <div class="text-center">
                                            <div class="font-bold text-gray-900">{{ $difficulties['total'] }}</div>
                                            <div class="text-gray-600">Total</div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Generated Questions Summary -->
                @if(!empty($generatedQuestions))
                    <div class="bg-yellow-50 p-4 rounded-lg">
                        <h3 class="text-lg font-medium text-gray-900 mb-3">Generated Questions</h3>
                        <div class="space-y-2">
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-600">Total Generated:</span>
                                <span class="text-sm font-medium text-gray-900">{{ count($generatedQuestions) }}</span>
                            </div>
                            @php
                                $typeCount = collect($generatedQuestions)->groupBy('type')->map->count();
                            @endphp
                            @foreach($typeCount as $type => $count)
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600">{{ ucfirst(str_replace('_', ' ', $type)) }}:</span>
                                    <span class="text-sm font-medium text-gray-900">{{ $count }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Generated Questions Display -->
        @if(!empty($generatedQuestions))
            <div class="mt-8 border-t pt-8">
                <h2 class="text-xl font-semibold text-gray-800 mb-6">Generated Questions Preview</h2>
                <div class="space-y-6">
                    @foreach($generatedQuestions as $question)
                        <div class="bg-gray-50 p-6 rounded-lg">
                            <div class="flex items-start justify-between mb-4">
                                <div class="flex items-center space-x-3">
                                    <span class="bg-blue-100 text-blue-800 text-xs font-semibold px-2.5 py-0.5 rounded">
                                        Q{{ $question['index'] }}
                                    </span>
                                    <span class="bg-gray-100 text-gray-800 text-xs font-semibold px-2.5 py-0.5 rounded">
                                        {{ ucfirst(str_replace('_', ' ', $question['type'])) }}
                                    </span>
                                    <span class="bg-{{ $question['difficulty'] === 'easy' ? 'green' : ($question['difficulty'] === 'medium' ? 'yellow' : 'red') }}-100 text-{{ $question['difficulty'] === 'easy' ? 'green' : ($question['difficulty'] === 'medium' ? 'yellow' : 'red') }}-800 text-xs font-semibold px-2.5 py-0.5 rounded">
                                        {{ ucfirst($question['difficulty']) }}
                                    </span>
                                </div>
                                <span class="text-sm text-gray-500">{{ $question['points'] }} points</span>
                            </div>

                            <div class="mb-4">
                                <div class="text-gray-900 font-medium mb-2">
                                    {!! $question['question'] !!}
                                </div>

                                @if($question['type'] === 'multiple_choice_question' && !empty($question['options']))
                                    <div class="ml-4 space-y-1">
                                        @foreach($question['options'] as $option)
                                            <div class="text-sm text-gray-700">{{ $option }}</div>
                                        @endforeach
                                    </div>
                                @endif

                                @if($question['type'] === 'true_or_false_question')
                                    <div class="ml-4 space-y-1">
                                        <div class="text-sm text-gray-700">A) True</div>
                                        <div class="text-sm text-gray-700">B) False</div>
                                    </div>
                                @endif
                            </div>

                            <div class="text-xs text-gray-500 space-y-1">
                                <div>Question ID: {{ $question['id'] }}</div>
                                <div>Subject ID: {{ $question['subject_id'] }}</div>
                                @if($question['topic_id'])
                                    <div>Topic ID: {{ $question['topic_id'] }}</div>
                                @endif
                                @if($question['subtopic_id'])
                                    <div>Subtopic ID: {{ $question['subtopic_id'] }}</div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Debug Information -->
        <div class="mt-8 p-4 bg-gray-100 rounded-lg">
            <h4 class="text-sm font-medium text-gray-700 mb-2">Debug Information</h4>
            <div class="text-xs text-gray-600 space-y-1">
                <div>Selected Subject ID: {{ $selectedSubject ?? 'None' }}</div>
                <div>Selected Topic ID: {{ $selectedTopic ?? 'None' }}</div>
                <div>Selected Subtopic ID: {{ $selectedSubtopic ?? 'None' }}</div>
                <div>Question Types: {{ json_encode($questionTypes) }}</div>
                <div>Question Count: {{ $questionCount }}</div>
                <div>Difficulty: {{ $difficulty }}</div>
                <div>Balanced Distribution: {{ $balancedDistribution ? 'Yes' : 'No' }}</div>
                <div>Available Subjects: {{ count($subjects) }}</div>
                <div>Available Topics: {{ count($topics) }}</div>
                <div>Available Subtopics: {{ count($subtopics) }}</div>
            </div>
        </div>
    </div>
</div>
