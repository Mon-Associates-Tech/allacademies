<section>
    <div class="academic-hierarchy">
        <div class="container mx-auto px-4 py-6">
            <div class="mb-6 flex justify-between">
                <h1 class="text-2xl font-bold">Academic Structure</h1>
                <a href="{{ route('academic-groups.index') }}"
                   class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 text-sm font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Back to Academic Groups
                </a>
            </div>


            <div class="bg-white rounded-lg shadow overflow-hidden">
                <!-- Academic Groups -->
                @foreach($academicGroups as $group)
                    <div class="border-b border-gray-200">
                        <div class="flex items-center justify-between p-4 bg-gray-50 cursor-pointer hover:bg-gray-100"
                             wire:click="toggleGroup({{ $group->id }})">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 mr-2 text-gray-500 transform transition-transform duration-200
                                     {{ in_array($group->id, $expandedGroups) ? 'rotate-90' : '' }}"
                                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                                <span class="font-medium">{{ $group->name }}</span>
                            </div>
                            <span class="text-sm text-gray-500">{{ $group->academicLevels->count() }} levels</span>
                        </div>

                        <!-- Academic Levels -->
                        @if(in_array($group->id, $expandedGroups))
                            <div class="pl-6 bg-white">
                                @foreach($group->academicLevels as $level)
                                    <div class="border-b border-gray-100">
                                        <div class="flex items-center justify-between p-3 cursor-pointer hover:bg-gray-50"
                                             wire:click="toggleLevel({{ $level->id }})">
                                            <div class="flex items-center">
                                                <svg class="w-4 h-4 mr-2 text-gray-500 transform transition-transform duration-200
                                                     {{ in_array($level->id, $expandedLevels) ? 'rotate-90' : '' }}"
                                                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                                </svg>
                                                <span>{{ $level->name }}</span>
                                            </div>
                                            <span class="text-xs text-gray-500">{{ $level->academicSubjects->count() }} subjects</span>
                                        </div>

                                        <!-- Academic Subjects -->
                                        @if(in_array($level->id, $expandedLevels))
                                            <div class="pl-6 bg-gray-50">
                                                @foreach($level->academicSubjects as $subject)
                                                    <div class="border-b border-gray-100">
                                                        <div class="flex items-center justify-between p-3 cursor-pointer hover:bg-white"
                                                             wire:click="toggleSubject({{ $subject->id }})">
                                                            <div class="flex items-center">
                                                                <svg class="w-4 h-4 mr-2 text-gray-500 transform transition-transform duration-200
                                                                     {{ in_array($subject->id, $expandedSubjects) ? 'rotate-90' : '' }}"
                                                                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                                                </svg>
                                                                <span>{{ $subject->name }}</span>
                                                            </div>
                                                            <span class="text-xs text-gray-500">{{ $subject->academicTopics->count() }} topics</span>
                                                        </div>

                                                        <!-- Academic Topics -->
                                                        @if(in_array($subject->id, $expandedSubjects))
                                                            <div class="pl-6 bg-white">
                                                                @foreach($subject->academicTopics as $topic)
                                                                    <div class="border-b border-gray-100">
                                                                        <div class="flex items-center justify-between p-3 cursor-pointer hover:bg-gray-50"
                                                                             wire:click="toggleTopic({{ $topic->id }})">
                                                                            <div class="flex items-center">
                                                                                <svg class="w-4 h-4 mr-2 text-gray-500 transform transition-transform duration-200
                                                                                     {{ in_array($topic->id, $expandedTopics) ? 'rotate-90' : '' }}"
                                                                                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                                                                </svg>
                                                                                <span>{{ $topic->name }}</span>
                                                                            </div>
                                                                            <div class="flex items-center">
                                                                                <button class="text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded mr-2"
                                                                                        wire:click.stop="selectTopic({{ $topic->id }})">
                                                                                    View Questions
                                                                                </button>
                                                                                <span class="text-xs text-gray-500">{{ $topic->subtopics->count() }} subtopics</span>
                                                                            </div>
                                                                        </div>

                                                                        <!-- Questions Section for Topic (if selected) -->
                                                                        @if($questionsTopicId == $topic->id && !$questionsSubtopicId)
                                                                            <div class="px-6 py-4 bg-blue-50 border-t border-b border-blue-100">
                                                                                <div class="flex justify-between items-center mb-3">
                                                                                    <h3 class="text-lg font-medium text-blue-800">
                                                                                        Questions for Topic: {{ $topic->name }}
                                                                                    </h3>
                                                                                    <button wire:click="closeQuestions" class="text-gray-500 hover:text-gray-700">
                                                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                                                        </svg>
                                                                                    </button>
                                                                                </div>

                                                                                <div class="border-b border-gray-200 mb-4">
                                                                                    <nav class="flex -mb-px">
                                                                                        <button
                                                                                            class="py-2 px-4 text-center border-b-2 font-medium text-sm {{ $activeTab === 'mcq' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-500' }}"
                                                                                            wire:click="setTab('mcq')">
                                                                                            Multiple Choice
                                                                                        </button>
                                                                                        <button
                                                                                            class="py-2 px-4 text-center border-b-2 font-medium text-sm {{ $activeTab === 'true_false' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-500' }}"
                                                                                            wire:click="setTab('true_false')">
                                                                                            True/False
                                                                                        </button>
                                                                                        <button
                                                                                            class="py-2 px-4 text-center border-b-2 font-medium text-sm {{ $activeTab === 'essay' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-500' }}"
                                                                                            wire:click="setTab('essay')">
                                                                                            Essay
                                                                                        </button>
                                                                                    </nav>
                                                                                </div>

                                                                                <div class="overflow-x-auto">
                                                                                    @if($this->questions->count() > 0)
                                                                                        <table class="min-w-full divide-y divide-gray-200">
                                                                                            <thead class="bg-gray-50">
                                                                                                <tr>
                                                                                                    <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Question</th>
                                                                                                    <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Difficulty</th>
                                                                                                    <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Score</th>
                                                                                                </tr>
                                                                                            </thead>
                                                                                            <tbody class="bg-white divide-y divide-gray-200">
                                                                                                @foreach($this->questions as $question)
                                                                                                    <tr>
                                                                                                        <td class="px-4 py-2 whitespace-normal text-sm text-gray-900">
                                                                                                            <span class="font-medium">[{{$this->getStartingIndex() + $loop->index + 1 }}]</span>
                                                                                                            <x-form.markdown-with-math content="{{ $question->question->down ?? $question->question }}" />
                                                                                                        </td>
                                                                                                        <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-500">
                                                                                                            {{ $question->difficulty_level ?? 'N/A' }}
                                                                                                        </td>
                                                                                                        <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-500">
                                                                                                            {{ $question->score ?? 'N/A' }}
                                                                                                        </td>
                                                                                                    </tr>
                                                                                                @endforeach
                                                                                            </tbody>
                                                                                        </table>

                                                                                        <!-- Pagination -->
                                                                                        <div class="mt-4">
                                                                                            {{ $this->questions->links() }}
                                                                                        </div>
                                                                                    @else
                                                                                        <div class="text-center py-4 text-gray-500">
                                                                                            No questions found for this topic.
                                                                                        </div>
                                                                                    @endif
                                                                                </div>
                                                                            </div>
                                                                        @endif

                                                                        <!-- Academic Subtopics -->
                                                                        @if(in_array($topic->id, $expandedTopics))
                                                                            <div class="pl-6 bg-gray-50">
                                                                                @if($topic->subtopics->count() > 0)
                                                                                    @foreach($topic->subtopics as $subtopic)
                                                                                        <div class="border-b border-gray-100 flex items-center justify-between p-3 hover:bg-white">
                                                                                            <div class="flex items-center">
                                                                                                <span class="ml-6 mr-2">•</span>
                                                                                                <span>{{ $subtopic->name }}</span>
                                                                                            </div>
                                                                                            <button class="text-xs bg-green-100 text-green-800 px-2 py-1 rounded"
                                                                                                    wire:click.stop="toggleSubtopic({{ $subtopic->id }}, {{ $topic->id }})">
                                                                                                View Questions
                                                                                            </button>
                                                                                        </div>

                                                                                        <!-- Questions Section for Subtopic (if selected) -->
                                                                                        @if($questionsSubtopicId == $subtopic->id)
                                                                                            <div class="px-8 py-4 bg-green-50 border-t border-b border-green-100">
                                                                                                <div class="flex justify-between items-center mb-3">
                                                                                                    <h3 class="text-lg font-medium text-green-800">
                                                                                                        Questions for Subtopic: {{ $subtopic->name }}
                                                                                                    </h3>
                                                                                                    <button wire:click="closeQuestions" class="text-gray-500 hover:text-gray-700">
                                                                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                                                                        </svg>
                                                                                                    </button>
                                                                                                </div>

                                                                                                <div class="border-b border-gray-200 mb-4">
                                                                                                    <nav class="flex -mb-px">
                                                                                                        <button
                                                                                                            class="py-2 px-4 text-center border-b-2 font-medium text-sm {{ $activeTab === 'mcq' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-500' }}"
                                                                                                            wire:click="setTab('mcq')">
                                                                                                            Multiple Choice
                                                                                                        </button>
                                                                                                        <button
                                                                                                            class="py-2 px-4 text-center border-b-2 font-medium text-sm {{ $activeTab === 'true_false' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-500' }}"
                                                                                                            wire:click="setTab('true_false')">
                                                                                                            True/False
                                                                                                        </button>
                                                                                                        <button
                                                                                                            class="py-2 px-4 text-center border-b-2 font-medium text-sm {{ $activeTab === 'essay' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-500' }}"
                                                                                                            wire:click="setTab('essay')">
                                                                                                            Essay
                                                                                                        </button>
                                                                                                    </nav>
                                                                                                </div>

                                                                                                <div class="overflow-x-auto">
                                                                                                    @if($this->questions->count() > 0)
                                                                                                        <table class="min-w-full divide-y divide-gray-200">
                                                                                                            <thead class="bg-gray-50">
                                                                                                                <tr>
                                                                                                                    <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Question</th>
                                                                                                                    <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Difficulty</th>
                                                                                                                    <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Score</th>
                                                                                                                </tr>
                                                                                                            </thead>
                                                                                                            <tbody class="bg-white divide-y divide-gray-200">
                                                                                                                @foreach($this->questions as $question)
                                                                                                                    <tr>
                                                                                                                        <td class="px-4 py-2 whitespace-normal text-sm text-gray-900">
                                                                                                                            <div class="flex">
                                                                                                                                <span class="font-medium">[{{$this->getStartingIndex() + $loop->index + 1 }}]</span>
                                                                                                                                <x-form.markdown-with-math content="{{ $question->question->down ?? $question->question }}" />
                                                                                                                            </div>
                                                                                                                        </td>
                                                                                                                        <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-500">
                                                                                                                            {{ $question->difficulty_level ?? 'N/A' }}
                                                                                                                        </td>
                                                                                                                        <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-500">
                                                                                                                            {{ $question->score ?? 'N/A' }}
                                                                                                                        </td>
                                                                                                                    </tr>
                                                                                                                @endforeach
                                                                                                            </tbody>
                                                                                                        </table>

                                                                                                        <!-- Pagination -->
                                                                                                        <div class="mt-4">
                                                                                                            {{ $this->questions->links() }}
                                                                                                        </div>
                                                                                                    @else
                                                                                                        <div class="text-center py-4 text-gray-500">
                                                                                                            No questions found for this subtopic.
                                                                                                        </div>
                                                                                                    @endif
                                                                                                </div>
                                                                                            </div>
                                                                                        @endif
                                                                                    @endforeach
                                                                                @else
                                                                                    <div class="p-3 text-gray-500 text-sm italic">
                                                                                        No subtopics available
                                                                                    </div>
                                                                                @endif
                                                                            </div>
                                                                        @endif
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                        @endif
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    <style>
        .academic-hierarchy {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .academic-hierarchy .cursor-pointer {
            cursor: pointer;
        }

        .academic-hierarchy .hover\:bg-gray-50:hover {
            background-color: #f9fafb;
        }

        .academic-hierarchy .hover\:bg-gray-100:hover {
            background-color: #f3f4f6;
        }

        .academic-hierarchy .hover\:text-gray-700:hover {
            color: #374151;
        }

        @media (max-width: 768px) {
            .academic-hierarchy .pl-6 {
                padding-left: 1rem;
            }

            .academic-hierarchy .pl-8 {
                padding-left: 1.5rem;
            }

            .academic-hierarchy .px-6 {
                padding-left: 0.5rem;
                padding-right: 0.5rem;
            }

            .academic-hierarchy .px-8 {
                padding-left: 1rem;
                padding-right: 1rem;
            }

            .academic-hierarchy .py-4 {
                padding-top: 0.5rem;
                padding-bottom: 0.5rem;
            }
        }
    </style>
</section>
