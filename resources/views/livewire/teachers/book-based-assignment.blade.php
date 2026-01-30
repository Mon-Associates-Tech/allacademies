<div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Create Book-Based Assignment</h1>
        <p class="mt-2 text-gray-600">Generate questions from books or uploaded content and assign to your students</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="lg:col-span-2">
            <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Assignment Configuration</h3>
                </div>
                <div class="px-4 py-5 sm:p-6">
                    <form wire:submit.prevent="createAssignment">
                        <!-- Assignment Details -->
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Assignment Title *</label>
                            <input type="text" class="w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm dark:bg-gray-700 dark:text-white focus:border-indigo-500 focus:ring-indigo-500"
                                   wire:model="title" placeholder="e.g., Chapter 3 Comprehension Questions">
                            @error('title') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <!-- Subject Selection -->
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Subject *</label>
                            <select class="w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm dark:bg-gray-700 dark:text-white focus:border-indigo-500 focus:ring-indigo-500"
                                    wire:model="selectedSubjectId">
                                <option value="">Choose a subject...</option>
                                @foreach($availableSubjects as $subject)
                                    <option value="{{ $subject->id }}">
                                        {{ $subject->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('selectedSubjectId') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            <p class="mt-1 text-sm text-gray-500">Select the subject this assignment is related to</p>
                        </div>

                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                            <textarea class="w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm dark:bg-gray-700 dark:text-white focus:border-indigo-500 focus:ring-indigo-500"
                                      wire:model="description" rows="3"
                                      placeholder="Instructions or additional information for students"></textarea>
                        </div>

                        <!-- Content Source -->
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Content Source *</label>

                            <div class="border-b border-gray-200">
                                <nav class="-mb-px flex space-x-8">
                                    <button type="button"
                                            class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm
                                                   {{ $contentSourceTab === 'book' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-500' }}"
                                            wire:click="$set('contentSourceTab', 'book')">
                                        Select Book
                                    </button>
                                    <button type="button"
                                            class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm
                                                   {{ $contentSourceTab === 'upload' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-500' }}"
                                            wire:click="$set('contentSourceTab', 'upload')">
                                        Upload Content
                                    </button>
                                </nav>
                            </div>

                            <div class="mt-4">
                                @if($contentSourceTab === 'book')
                                    <div class="space-y-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Select Book</label>
                                            <select class="w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm dark:bg-gray-700 dark:text-white focus:border-indigo-500 focus:ring-indigo-500"
                                                    wire:model="selectedBookId">
                                                <option value="">Choose a book...</option>
                                                @foreach($availableBooks as $book)
                                                    <option value="{{ $book->id }}">
                                                        {{ $book->title }} by {{ $book->author_name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('selectedBookId') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                                        </div>

                                        @if($selectedBook)
                                            <div class="bg-gray-50 rounded-lg p-4">
                                                <h4 class="font-medium text-gray-900">{{ $selectedBook->title }}</h4>
                                                <div class="mt-2 grid grid-cols-1 sm:grid-cols-3 gap-2 text-sm">
                                                    <div>
                                                        <p class="text-gray-500">Author</p>
                                                        <p class="font-medium">{{ $selectedBook->author_name }}</p>
                                                    </div>
                                                    <div>
                                                        <p class="text-gray-500">Genre</p>
                                                        <p class="font-medium">{{ $selectedBook->genre }}</p>
                                                    </div>
                                                    <div>
                                                        <p class="text-gray-500">Difficulty</p>
                                                        <p class="font-medium">{{ $selectedBook->reading_difficulty }}</p>
                                                    </div>
                                                </div>
                                            </div>

                                            @if(!empty($bookChapters))
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700 mb-1">Chapter (Optional)</label>
                                                    <select class="w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm dark:bg-gray-700 dark:text-white focus:border-indigo-500 focus:ring-indigo-500"
                                                            wire:model="selectedChapterId">
                                                        <option value="">All chapters</option>
                                                        @foreach($bookChapters as $chapter)
                                                            <option value="{{ $chapter->id }}">
                                                                Chapter {{ $chapter->chapter_number }}: {{ $chapter->title }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            @endif

                                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-4">
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700 mb-1">Start Page (Optional)</label>
                                                    <input type="number" min="1"
                                                           class="w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm dark:bg-gray-700 dark:text-white focus:border-indigo-500 focus:ring-indigo-500"
                                                           wire:model="pageStart">
                                                </div>
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700 mb-1">End Page (Optional)</label>
                                                    <input type="number" min="1"
                                                           class="w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm dark:bg-gray-700 dark:text-white focus:border-indigo-500 focus:ring-indigo-500"
                                                           wire:model="pageEnd">
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                @else
                                    <div class="space-y-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Upload Content File</label>
                                            <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md">
                                                <div class="space-y-1 text-center">
                                                    <div class="flex text-sm text-gray-600">
                                                        <label class="relative cursor-pointer bg-white rounded-md font-medium text-indigo-600 hover:text-indigo-500">
                                                            <span>Upload a file</span>
                                                            <input type="file" class="sr-only" wire:model="uploadedFile" accept=".pdf,.doc,.docx,.txt">
                                                        </label>
                                                        <p class="pl-1">or drag and drop</p>
                                                    </div>
                                                    <p class="text-xs text-gray-500">PDF, DOC, DOCX, TXT up to 10MB</p>
                                                </div>
                                            </div>
                                            @error('uploadedFile') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                                        </div>

                                        @if($fileName)
                                            <div class="rounded-md bg-blue-50 p-4">
                                                <div class="flex">
                                                    <div class="flex-shrink-0">
                                                        <svg class="h-5 w-5 text-blue-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                                        </svg>
                                                    </div>
                                                    <div class="ml-3 flex-1">
                                                        <h3 class="text-sm font-medium text-blue-800">File uploaded successfully</h3>
                                                        <div class="mt-2 text-sm text-blue-700">
                                                            <p>{{ $fileName }}</p>
                                                            @if($fileContent)
                                                                <p class="text-xs mt-1">Content extracted ({{ strlen($fileContent) }} characters)</p>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Question Configuration -->
                        <div class="mb-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Question Configuration</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Question Type</label>
                                    <select class="w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm dark:bg-gray-700 dark:text-white focus:border-indigo-500 focus:ring-indigo-500"
                                            wire:model="questionType">
                                        <option value="multiple_choice">Multiple Choice</option>
                                        <option value="true_false">True/False</option>
                                        <option value="essay">Essay</option>
                                        <option value="mixed">Mixed Types</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Number of Questions</label>
                                    <input type="number" min="1" max="50"
                                           class="w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm dark:bg-gray-700 dark:text-white focus:border-indigo-500 focus:ring-indigo-500"
                                           wire:model="questionCount" value="10">
                                    @error('questionCount') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Difficulty</label>
                                    <select class="w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm dark:bg-gray-700 dark:text-white focus:border-indigo-500 focus:ring-indigo-500"
                                            wire:model="difficulty">
                                        <option value="easy">Easy</option>
                                        <option value="medium">Medium</option>
                                        <option value="hard">Hard</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Total Marks *</label>
                                    <input type="number" min="1"
                                           class="w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm dark:bg-gray-700 dark:text-white focus:border-indigo-500 focus:ring-indigo-500"
                                           wire:model="totalMarks"
                                           placeholder="e.g., 100">
                                    @error('totalMarks') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                                    <p class="mt-1 text-xs text-gray-500">Total marks for the assignment</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Focus Topics (Optional)</label>
                                    <input type="text"
                                           class="w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm dark:bg-gray-700 dark:text-white focus:border-indigo-500 focus:ring-indigo-500"
                                           wire:model="focusTopics" placeholder="e.g., characters, themes, plot">
                                </div>
                            </div>

                            <div class="mt-4 flex items-center">
                                <input type="checkbox"
                                       class="h-4 w-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500"
                                       wire:model="includeQuotes" id="includeQuotes">
                                <label for="includeQuotes" class="ml-2 block text-sm text-gray-900">
                                    Include quotes from content in questions
                                </label>
                            </div>
                        </div>

                        <!-- Generate Questions Button -->
                        <div class="mb-6">
                            <button type="button"
                                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                                    wire:click="generateQuestions"
                                    wire:loading.attr="disabled" wire:target="generateQuestions">
                                <span wire:loading.remove wire:target="generateQuestions">Generate Questions</span>
                                <span wire:loading wire:target="generateQuestions" class="flex items-center">
                                    <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    Generating...
                                </span>
                            </button>

                            @if($isGenerating)
                                <div class="mt-3">
                                    <div class="w-full bg-gray-200 rounded-full h-2">
                                        <div class="bg-indigo-600 h-2 rounded-full animate-pulse" style="width: 100%"></div>
                                    </div>
                                    <p class="mt-2 text-sm text-gray-500">Generating questions based on content...</p>
                                </div>
                            @endif
                        </div>

                        <!-- Generated Questions Preview -->
                        @if(!empty($generatedQuestions))
                            <div class="mb-6">
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Generated Questions ({{ count($generatedQuestions) }})</h3>
                                <div class="border border-gray-200 rounded-md overflow-hidden">
                                    @foreach($generatedQuestions as $index => $question)
                                        <div class="border-b border-gray-200 last:border-b-0">
                                            <button type="button"
                                                    class="w-full px-4 py-3 text-left text-sm font-medium text-gray-900 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 flex justify-between items-center"
                                                    @click="document.getElementById('question-{{ $index }}').classList.toggle('hidden')">
                                                <span>Question {{ $index + 1 }}: {{ Str::limit($question['question'], 50) }}</span>
                                                <svg class="h-5 w-5 text-gray-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                                </svg>
                                            </button>
                                            <div id="question-{{ $index }}" class="hidden px-4 py-3 bg-gray-50 border-t border-gray-200">
                                                <div class="space-y-3">
                                                    <div>
                                                        <p class="text-sm font-medium text-gray-700 dark:text-gray-300">Type</p>
                                                        <p class="text-sm text-gray-900">{{ ucfirst(str_replace('_', ' ', $question['type'])) }}</p>
                                                    </div>
                                                    <div>
                                                        <p class="text-sm font-medium text-gray-700 dark:text-gray-300">Question</p>
                                                        <p class="text-sm text-gray-900">{{ $question['question'] }}</p>
                                                    </div>

                                                    @if(isset($question['options']) && !empty($question['options']))
                                                        <div>
                                                            <p class="text-sm font-medium text-gray-700 dark:text-gray-300">Options</p>
                                                            <ul class="mt-1 space-y-1">
                                                                @foreach($question['options'] as $option)
                                                                    <li class="text-sm text-gray-900">• {{ $option }}</li>
                                                                @endforeach
                                                            </ul>
                                                        </div>
                                                    @endif

                                                    @if(isset($question['correct_answer']))
                                                        <div>
                                                            <p class="text-sm font-medium text-gray-700 dark:text-gray-300">Correct Answer</p>
                                                            <p class="text-sm text-gray-900">{{ $question['correct_answer'] }}</p>
                                                        </div>
                                                    @endif

                                                    @if(isset($question['explanation']))
                                                        <div>
                                                            <p class="text-sm font-medium text-gray-700 dark:text-gray-300">Explanation</p>
                                                            <p class="text-sm text-gray-900">{{ $question['explanation'] }}</p>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <!-- Assignment Settings -->
                        <div class="mb-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Assignment Settings</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Duration (minutes)</label>
                                    <input type="number" min="1"
                                           class="w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm dark:bg-gray-700 dark:text-white focus:border-indigo-500 focus:ring-indigo-500"
                                           wire:model="durationInMinutes" value="60">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Randomize Questions</label>
                                    <select class="w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm dark:bg-gray-700 dark:text-white focus:border-indigo-500 focus:ring-indigo-500"
                                            wire:model="isRandomized">
                                        <option value="1">Yes</option>
                                        <option value="0">No</option>
                                    </select>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Start Date</label>
                                    <input type="datetime-local"
                                           class="w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm dark:bg-gray-700 dark:text-white focus:border-indigo-500 focus:ring-indigo-500"
                                           wire:model="startDate">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">End Date</label>
                                    <input type="datetime-local"
                                           class="w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm dark:bg-gray-700 dark:text-white focus:border-indigo-500 focus:ring-indigo-500"
                                           wire:model="endDate">
                                    @error('endDate') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Target Groups -->
                        <div class="mb-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Target Groups</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                @if($studentGroups->count())
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Student Groups</label>
                                        <div class="border border-gray-300 rounded-md p-4 max-h-48 overflow-y-auto">
                                            @foreach($studentGroups as $group)
                                                <div class="flex items-center mb-2 last:mb-0">
                                                    <input type="checkbox"
                                                           class="h-4 w-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500"
                                                           wire:model="selectedStudentGroups"
                                                           value="{{ $group->id }}"
                                                           id="group-{{ $group->id }}">
                                                    <label for="group-{{ $group->id }}" class="ml-2 block text-sm text-gray-900">
                                                        {{ $group->name }}
                                                    </label>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif

                                @if($academicLevels->count())
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Academic Levels</label>
                                        <div class="border border-gray-300 rounded-md p-4 max-h-48 overflow-y-auto">
                                            @foreach($academicLevels as $level)
                                                <div class="flex items-center mb-2 last:mb-0">
                                                    <input type="checkbox"
                                                           class="h-4 w-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500"
                                                           wire:model="selectedAcademicLevels"
                                                           value="{{ $level->id }}"
                                                           id="level-{{ $level->id }}">
                                                    <label for="level-{{ $level->id }}" class="ml-2 block text-sm text-gray-900">
                                                        {{ $level->name }}
                                                    </label>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            </div>

                            @if($academicGroups->count())
                                <div class="mt-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Academic Groups</label>
                                    <div class="border border-gray-300 rounded-md p-4 max-h-48 overflow-y-auto">
                                        @foreach($academicGroups as $group)
                                            <div class="flex items-center mb-2 last:mb-0">
                                                <input type="checkbox"
                                                       class="h-4 w-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500"
                                                       wire:model="selectedAcademicGroups"
                                                       value="{{ $group->id }}"
                                                       id="agroup-{{ $group->id }}">
                                                <label for="agroup-{{ $group->id }}" class="ml-2 block text-sm text-gray-900">
                                                    {{ $group->name }}
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>

                        <!-- Submit Button -->
                        <div class="flex justify-between pt-4">
                            <button type="button"
                                    class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md shadow-sm text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                                    onclick="window.history.back()">
                                Cancel
                            </button>
                            <button type="submit"
                                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white {{ empty($generatedQuestions) ? 'bg-gray-400 cursor-not-allowed' : 'bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500' }}"
                                    wire:loading.attr="disabled"
                                    wire:target="createAssignment"
                                    {{ empty($generatedQuestions) ? 'disabled' : '' }}>
                                <span wire:loading.remove wire:target="createAssignment">Create Assignment</span>
                                <span wire:loading wire:target="createAssignment" class="flex items-center">
                                    <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    Creating...
                                </span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="lg:col-span-1">
            <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Instructions</h3>
                </div>
                <div class="px-4 py-5 sm:p-6">
                    <ol class="list-decimal pl-5 space-y-3 text-sm text-gray-600">
                        <li><span class="font-medium">Select Subject:</span> Choose the subject this assignment relates to</li>
                        <li><span class="font-medium">Select Content Source:</span> Choose an existing book from the library or upload your own content</li>
                        <li><span class="font-medium">Configure Questions:</span> Set question type, count, difficulty, and focus areas</li>
                        <li><span class="font-medium">Generate Questions:</span> Click "Generate Questions" to create AI-powered questions</li>
                        <li><span class="font-medium">Review Questions:</span> Check the generated questions in the preview section</li>
                        <li><span class="font-medium">Set Assignment Details:</span> Configure duration, dates, and target groups</li>
                        <li><span class="font-medium">Create Assignment:</span> Click "Create Assignment" to assign to students</li>
                    </ol>
                    <div class="mt-4 p-4 bg-blue-50 rounded-md">
                        <p class="text-sm text-blue-700">
                            Students will be able to access this assignment during the specified period and
                            submit their answers for automatic grading (where applicable). Questions will be
                            generated based on your configuration when students start the assignment.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
