<div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
    <!-- Enhanced Header with Icon and Gradient -->
    <div class="mb-8 bg-gradient-to-r from-indigo-600 to-purple-600 dark:from-indigo-800 dark:to-purple-800 rounded-xl p-6 shadow-lg">
        <div class="flex items-center gap-4">
            <div class="flex-shrink-0 bg-white/20 dark:bg-white/10 rounded-lg p-3">
                <svg class="h-8 w-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                </svg>
            </div>
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold text-white">Create Book-Based Assignment</h1>
                <p class="mt-1 text-indigo-100 dark:text-indigo-200">Generate AI-powered questions from books or uploaded content and assign to your students</p>
            </div>
        </div>
    </div>

    <!-- Progress Steps Indicator -->
    <div class="mb-8 bg-white dark:bg-gray-800 rounded-lg shadow p-4">
        <div class="flex items-center justify-between">
            <!-- Step 1: Setup -->
            <button type="button" wire:click="goToStep(1)" class="flex items-center group cursor-pointer">
                <div class="flex items-center justify-center w-10 h-10 rounded-full transition-all duration-200
                    {{ $currentStep === 1 ? 'bg-indigo-600 ring-4 ring-indigo-100 dark:ring-indigo-900' : ($this->isStep1Complete() ? 'bg-green-500 hover:bg-green-600' : 'bg-gray-300 dark:bg-gray-600 hover:bg-gray-400 dark:hover:bg-gray-500') }}
                    text-white text-sm font-medium">
                    @if($this->isStep1Complete() && $currentStep !== 1)
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                    @else
                        1
                    @endif
                </div>
                <span class="ml-2 text-sm font-medium transition-colors
                    {{ $currentStep === 1 ? 'text-indigo-600 dark:text-indigo-400' : ($this->isStep1Complete() ? 'text-green-600 dark:text-green-400' : 'text-gray-500 dark:text-gray-400 group-hover:text-gray-700 dark:group-hover:text-gray-300') }}">
                    Setup
                </span>
            </button>

            <!-- Progress Line 1-2 -->
            <div class="flex-1 mx-4 h-1 bg-gray-200 dark:bg-gray-700 rounded">
                <div class="h-1 rounded transition-all duration-300 {{ $this->isStep1Complete() ? 'bg-green-500 w-full' : ($currentStep >= 2 ? 'bg-indigo-600 w-full' : 'w-0') }}"></div>
            </div>

            <!-- Step 2: Generate -->
            <button type="button" wire:click="goToStep(2)" class="flex items-center group cursor-pointer">
                <div class="flex items-center justify-center w-10 h-10 rounded-full transition-all duration-200
                    {{ $currentStep === 2 ? 'bg-indigo-600 ring-4 ring-indigo-100 dark:ring-indigo-900' : ($this->isStep2Complete() ? 'bg-green-500 hover:bg-green-600' : ($this->isStep1Complete() ? 'bg-gray-400 dark:bg-gray-500 hover:bg-gray-500 dark:hover:bg-gray-400' : 'bg-gray-300 dark:bg-gray-600')) }}
                    text-white text-sm font-medium">
                    @if($this->isStep2Complete() && $currentStep !== 2)
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                    @else
                        2
                    @endif
                </div>
                <span class="ml-2 text-sm font-medium transition-colors
                    {{ $currentStep === 2 ? 'text-indigo-600 dark:text-indigo-400' : ($this->isStep2Complete() ? 'text-green-600 dark:text-green-400' : ($this->isStep1Complete() ? 'text-gray-500 dark:text-gray-400 group-hover:text-gray-700 dark:group-hover:text-gray-300' : 'text-gray-400 dark:text-gray-500')) }}">
                    Generate
                </span>
            </button>

            <!-- Progress Line 2-3 -->
            <div class="flex-1 mx-4 h-1 bg-gray-200 dark:bg-gray-700 rounded">
                <div class="h-1 rounded transition-all duration-300 {{ $this->isStep2Complete() ? 'bg-green-500 w-full' : ($currentStep === 3 ? 'bg-indigo-600 w-full' : 'w-0') }}"></div>
            </div>

            <!-- Step 3: Assign -->
            <button type="button" wire:click="goToStep(3)" class="flex items-center group cursor-pointer">
                <div class="flex items-center justify-center w-10 h-10 rounded-full transition-all duration-200
                    {{ $currentStep === 3 ? 'bg-indigo-600 ring-4 ring-indigo-100 dark:ring-indigo-900' : ($this->isStep2Complete() ? 'bg-gray-400 dark:bg-gray-500 hover:bg-gray-500 dark:hover:bg-gray-400' : 'bg-gray-300 dark:bg-gray-600') }}
                    text-white text-sm font-medium">
                    3
                </div>
                <span class="ml-2 text-sm font-medium transition-colors
                    {{ $currentStep === 3 ? 'text-indigo-600 dark:text-indigo-400' : ($this->isStep2Complete() ? 'text-gray-500 dark:text-gray-400 group-hover:text-gray-700 dark:group-hover:text-gray-300' : 'text-gray-400 dark:text-gray-500') }}">
                    Assign
                </span>
            </button>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="lg:col-span-2">
            <div class="bg-white dark:bg-gray-800 shadow overflow-hidden sm:rounded-lg">
                <div class="px-4 py-5 sm:px-6 border-b border-gray-200 dark:border-gray-700 flex items-center gap-3">
                    <div class="flex-shrink-0 bg-indigo-100 dark:bg-indigo-900/50 rounded-lg p-2">
                        <svg class="h-5 w-5 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white">Assignment Configuration</h3>
                </div>
                <div class="px-4 py-5 sm:p-6">
                    <form wire:submit.prevent="createAssignment">
                        <!-- Error Summary -->
                        @if($errors->any())
                            <div class="mb-6 rounded-md bg-red-50 dark:bg-red-900/30 p-4">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <h3 class="text-sm font-medium text-red-800 dark:text-red-300">Please fix the following errors:</h3>
                                        <div class="mt-2 text-sm text-red-700 dark:text-red-400">
                                            <ul class="list-disc pl-5 space-y-1">
                                                @foreach($errors->all() as $error)
                                                    <li>{{ $error }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Success Message -->
                        @if(session()->has('success'))
                            <div class="mb-6 rounded-md bg-green-50 dark:bg-green-900/30 p-4">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm font-medium text-green-800 dark:text-green-300">{{ session('success') }}</p>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- ==================== STEP 1: SETUP ==================== -->
                        @if($currentStep === 1)
                        <div class="mb-4 p-4 bg-indigo-50 dark:bg-indigo-900/30 rounded-lg border border-indigo-200 dark:border-indigo-800">
                            <h3 class="text-lg font-semibold text-indigo-900 dark:text-indigo-100 flex items-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                Step 1: Assignment Setup
                            </h3>
                            <p class="mt-1 text-sm text-indigo-700 dark:text-indigo-300">Enter the basic assignment details and select your content source.</p>
                        </div>

                        <!-- Assignment Details -->
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Assignment Title *</label>
                            <input type="text" class="w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm dark:bg-gray-700 dark:text-white focus:border-indigo-500 focus:ring-indigo-500"
                                   wire:model="title" placeholder="e.g., Chapter 3 Comprehension Questions">
                            @error('title') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                        </div>

                        <!-- Subject Selection -->
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Subject *</label>
                            <select class="w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm dark:bg-gray-700 dark:text-white focus:border-indigo-500 focus:ring-indigo-500"
                                    wire:model="selectedSubjectId">
                                <option value="">Choose a subject...</option>
                                @foreach($availableSubjects as $subject)
                                    <option value="{{ $subject->id }}">
                                        {{ $subject->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('selectedSubjectId') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Select the subject this assignment is related to</p>
                        </div>

                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Description</label>
                            <textarea class="w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm dark:bg-gray-700 dark:text-white focus:border-indigo-500 focus:ring-indigo-500"
                                      wire:model="description" rows="3"
                                      placeholder="Instructions or additional information for students"></textarea>
                        </div>

                        <!-- Content Source -->
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Content Source *</label>

                            <div class="border-b border-gray-200 dark:border-gray-700">
                                <nav class="-mb-px flex space-x-8">
                                    <button type="button"
                                            class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm
                                                   {{ $contentSourceTab === 'book' ? 'border-indigo-500 text-indigo-600 dark:text-indigo-400' : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-500' }}"
                                            wire:click="$set('contentSourceTab', 'book')">
                                        Select Book
                                    </button>
                                    <button type="button"
                                            class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm
                                                   {{ $contentSourceTab === 'upload' ? 'border-indigo-500 text-indigo-600 dark:text-indigo-400' : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-500' }}"
                                            wire:click="$set('contentSourceTab', 'upload')">
                                        Upload Content
                                    </button>
                                </nav>
                            </div>

                            <div class="mt-4">
                                @if($contentSourceTab === 'book')
                                    <div class="space-y-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Select Book</label>
                                            <select class="w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm dark:bg-gray-700 dark:text-white focus:border-indigo-500 focus:ring-indigo-500"
                                                    wire:model="selectedBookId">
                                                <option value="">Choose a book...</option>
                                                @foreach($availableBooks as $book)
                                                    <option value="{{ $book->id }}">
                                                        {{ $book->title }} by {{ $book->author_name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('selectedBookId') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                                        </div>

                                        @if($selectedBook)
                                            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                                                <h4 class="font-medium text-gray-900 dark:text-white">{{ $selectedBook->title }}</h4>
                                                <div class="mt-2 grid grid-cols-1 sm:grid-cols-3 gap-2 text-sm">
                                                    <div>
                                                        <p class="text-gray-500 dark:text-gray-400">Author</p>
                                                        <p class="font-medium text-gray-900 dark:text-gray-200">{{ $selectedBook->author_name }}</p>
                                                    </div>
                                                    <div>
                                                        <p class="text-gray-500 dark:text-gray-400">Genre</p>
                                                        <p class="font-medium text-gray-900 dark:text-gray-200">{{ $selectedBook->genre }}</p>
                                                    </div>
                                                    <div>
                                                        <p class="text-gray-500 dark:text-gray-400">Difficulty</p>
                                                        <p class="font-medium text-gray-900 dark:text-gray-200">{{ $selectedBook->reading_difficulty }}</p>
                                                    </div>
                                                </div>
                                            </div>

                                            @if(!empty($bookChapters))
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Chapter (Optional)</label>
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
                                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Start Page (Optional)</label>
                                                    <input type="number" min="1"
                                                           class="w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm dark:bg-gray-700 dark:text-white focus:border-indigo-500 focus:ring-indigo-500"
                                                           wire:model="pageStart">
                                                </div>
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">End Page (Optional)</label>
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
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Upload Content File</label>
                                            <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 dark:border-gray-600 border-dashed rounded-md bg-white dark:bg-gray-700">
                                                <div class="space-y-1 text-center">
                                                    <div class="flex text-sm text-gray-600 dark:text-gray-300">
                                                        <label class="relative cursor-pointer bg-white dark:bg-gray-700 rounded-md font-medium text-indigo-600 dark:text-indigo-400 hover:text-indigo-500 dark:hover:text-indigo-300">
                                                            <span>Upload a file</span>
                                                            <input type="file" class="sr-only" wire:model="uploadedFile" accept=".pdf,.doc,.docx,.txt">
                                                        </label>
                                                        <p class="pl-1">or drag and drop</p>
                                                    </div>
                                                    <p class="text-xs text-gray-500 dark:text-gray-400">PDF, DOC, DOCX, TXT up to 10MB</p>
                                                </div>
                                            </div>
                                            @error('uploadedFile') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                                        </div>

                                        @if($fileName)
                                            <div class="rounded-md bg-blue-50 dark:bg-blue-900/30 p-4">
                                                <div class="flex">
                                                    <div class="flex-shrink-0">
                                                        <svg class="h-5 w-5 text-blue-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                                        </svg>
                                                    </div>
                                                    <div class="ml-3 flex-1">
                                                        <h3 class="text-sm font-medium text-blue-800 dark:text-blue-300">File uploaded successfully</h3>
                                                        <div class="mt-2 text-sm text-blue-700 dark:text-blue-400">
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

                        <!-- Step 1 Navigation -->
                        <div class="flex justify-between pt-4 border-t border-gray-200 dark:border-gray-700 mt-6">
                            <button type="button"
                                    class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 text-sm font-medium rounded-md shadow-sm text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800"
                                    onclick="window.history.back()">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                                </svg>
                                Cancel
                            </button>
                            <button type="button"
                                    wire:click="nextStep"
                                    class="inline-flex items-center px-6 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800">
                                Next: Configure Questions
                                <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                                </svg>
                            </button>
                        </div>
                        @endif
                        <!-- ==================== END STEP 1 ==================== -->

                        <!-- ==================== STEP 2: GENERATE ==================== -->
                        @if($currentStep === 2)
                        <div class="mb-4 p-4 bg-purple-50 dark:bg-purple-900/30 rounded-lg border border-purple-200 dark:border-purple-800">
                            <h3 class="text-lg font-semibold text-purple-900 dark:text-purple-100 flex items-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                                </svg>
                                Step 2: Generate Questions
                            </h3>
                            <p class="mt-1 text-sm text-purple-700 dark:text-purple-300">Configure question settings and generate AI-powered questions from your content.</p>
                        </div>

                        <!-- Question Configuration -->
                        <div class="mb-6">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Question Configuration</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Question Type</label>
                                    <select class="w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm dark:bg-gray-700 dark:text-white focus:border-indigo-500 focus:ring-indigo-500"
                                            wire:model="questionType">
                                        <option value="multiple_choice">Multiple Choice</option>
                                        <option value="true_false">True/False</option>
                                        <option value="essay">Essay</option>
                                        <option value="mixed">Mixed Types</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Number of Questions</label>
                                    <input type="number" min="1" max="50"
                                           class="w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm dark:bg-gray-700 dark:text-white focus:border-indigo-500 focus:ring-indigo-500"
                                           wire:model="questionCount" value="10">
                                    @error('questionCount') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Difficulty</label>
                                    <select class="w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm dark:bg-gray-700 dark:text-white focus:border-indigo-500 focus:ring-indigo-500"
                                            wire:model="difficulty">
                                        <option value="easy">Easy</option>
                                        <option value="medium">Medium</option>
                                        <option value="hard">Hard</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Total Marks *</label>
                                    <input type="number" min="1"
                                           class="w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm dark:bg-gray-700 dark:text-white focus:border-indigo-500 focus:ring-indigo-500"
                                           wire:model="totalMarks"
                                           placeholder="e.g., 100">
                                    @error('totalMarks') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Total marks for the assignment</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Focus Topics (Optional)</label>
                                    <input type="text"
                                           class="w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm dark:bg-gray-700 dark:text-white focus:border-indigo-500 focus:ring-indigo-500"
                                           wire:model="focusTopics" placeholder="e.g., characters, themes, plot">
                                </div>
                            </div>

                            <div class="mt-4 flex items-center">
                                <input type="checkbox"
                                       class="h-4 w-4 text-indigo-600 border-gray-300 dark:border-gray-600 rounded focus:ring-indigo-500 dark:bg-gray-700"
                                       wire:model="includeQuotes" id="includeQuotes">
                                <label for="includeQuotes" class="ml-2 block text-sm text-gray-900 dark:text-white">
                                    Include quotes from content in questions
                                </label>
                            </div>
                        </div>

                        <!-- Generate Questions Button -->
                        <div class="mb-6">
                            <button type="button"
                                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800"
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
                                    <div class="w-full bg-gray-200 dark:bg-gray-600 rounded-full h-2">
                                        <div class="bg-indigo-600 h-2 rounded-full animate-pulse" style="width: 100%"></div>
                                    </div>
                                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Generating questions based on content...</p>
                                </div>
                            @endif
                        </div>

                        <!-- Generated Questions Preview -->
                        @if(!empty($generatedQuestions))
                            <div class="mb-6">
                                <div class="flex justify-between items-center mb-4">
                                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">Generated Questions ({{ count($generatedQuestions) }})</h3>
                                    <div class="flex items-center gap-3">
                                        <span class="text-sm text-gray-600 dark:text-gray-400">Total: <strong class="dark:text-white">{{ $this->computedTotalMarks }}</strong> marks</span>
                                        <button type="button"
                                                wire:click="distributeMarksEvenly"
                                                class="inline-flex items-center px-3 py-1.5 border border-gray-300 dark:border-gray-600 text-xs font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                            </svg>
                                            Distribute Marks Evenly
                                        </button>
                                    </div>
                                </div>

                                @error('regeneration')
                                    <div class="mb-4 rounded-md bg-red-50 dark:bg-red-900/30 p-4">
                                        <div class="flex">
                                            <div class="flex-shrink-0">
                                                <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                                </svg>
                                            </div>
                                            <div class="ml-3">
                                                <p class="text-sm font-medium text-red-800 dark:text-red-300">{{ $message }}</p>
                                            </div>
                                        </div>
                                    </div>
                                @enderror

                                <div class="space-y-3">
                                    @foreach($generatedQuestions as $index => $question)
                                        <div wire:key="question-{{ $index }}" class="border border-gray-200 dark:border-gray-600 rounded-lg overflow-hidden bg-white dark:bg-gray-800 shadow-sm">
                                            <!-- Question Header -->
                                            <div class="px-4 py-3 bg-gray-50 dark:bg-gray-700 border-b border-gray-200 dark:border-gray-600">
                                                <div class="flex items-center justify-between">
                                                    <div class="flex items-center gap-3">
                                                        <!-- Reorder Buttons -->
                                                        <div class="flex flex-col gap-0.5">
                                                            <button type="button"
                                                                    wire:click="moveQuestionUp({{ $index }})"
                                                                    @if($index === 0) disabled @endif
                                                                    class="p-1 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 disabled:opacity-30 disabled:cursor-not-allowed"
                                                                    title="Move up">
                                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path>
                                                                </svg>
                                                            </button>
                                                            <button type="button"
                                                                    wire:click="moveQuestionDown({{ $index }})"
                                                                    @if($index === count($generatedQuestions) - 1) disabled @endif
                                                                    class="p-1 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 disabled:opacity-30 disabled:cursor-not-allowed"
                                                                    title="Move down">
                                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                                                </svg>
                                                            </button>
                                                        </div>

                                                        <div>
                                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 dark:bg-indigo-900/50 text-indigo-800 dark:text-indigo-300">
                                                                Q{{ $index + 1 }}
                                                            </span>
                                                            <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 dark:bg-gray-600 text-gray-800 dark:text-gray-200">
                                                                {{ ucfirst(str_replace('_', ' ', $question['type'] ?? 'unknown')) }}
                                                            </span>
                                                            <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ ($question['difficulty'] ?? 'medium') === 'easy' ? 'bg-green-100 dark:bg-green-900/50 text-green-800 dark:text-green-300' : (($question['difficulty'] ?? 'medium') === 'hard' ? 'bg-red-100 dark:bg-red-900/50 text-red-800 dark:text-red-300' : 'bg-yellow-100 dark:bg-yellow-900/50 text-yellow-800 dark:text-yellow-300') }}">
                                                                {{ ucfirst($question['difficulty'] ?? 'medium') }}
                                                            </span>
                                                        </div>
                                                    </div>

                                                    <div class="flex items-center gap-2">
                                                        <!-- Marks Input -->
                                                        <div class="flex items-center gap-1">
                                                            <label class="text-xs text-gray-500 dark:text-gray-400">Marks:</label>
                                                            <input type="number"
                                                                   min="1"
                                                                   class="w-16 text-sm rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-600 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                                                   value="{{ $question['points'] ?? 1 }}"
                                                                   wire:change="updateQuestionMarks({{ $index }}, $event.target.value)">
                                                        </div>

                                                        <!-- Action Buttons -->
                                                        <div class="flex items-center gap-1 ml-2 border-l pl-2 border-gray-200 dark:border-gray-600">
                                                            <button type="button"
                                                                    wire:click="startEditingQuestion({{ $index }})"
                                                                    class="p-1.5 text-gray-500 dark:text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400 hover:bg-indigo-50 dark:hover:bg-indigo-900/30 rounded"
                                                                    title="Edit question">
                                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                                </svg>
                                                            </button>
                                                            <button type="button"
                                                                    wire:click="regenerateQuestion({{ $index }})"
                                                                    wire:loading.attr="disabled"
                                                                    wire:target="regenerateQuestion({{ $index }})"
                                                                    class="p-1.5 text-gray-500 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/30 rounded disabled:opacity-50"
                                                                    title="Regenerate question">
                                                                <svg class="w-4 h-4 {{ $isRegenerating && $regeneratingIndex === $index ? 'animate-spin' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                                                </svg>
                                                            </button>
                                                            <button type="button"
                                                                    wire:click="confirmDeleteQuestion({{ $index }})"
                                                                    class="p-1.5 text-gray-500 dark:text-gray-400 hover:text-red-600 dark:hover:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/30 rounded"
                                                                    title="Delete question">
                                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                                </svg>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Question Content -->
                                            <div class="px-4 py-3">
                                                <p class="text-sm text-gray-900 dark:text-white font-medium mb-2">{{ $question['question'] }}</p>

                                                @if(isset($question['options']) && !empty($question['options']))
                                                    <div class="mt-2 space-y-1">
                                                        @foreach($question['options'] as $key => $option)
                                                            <div class="flex items-center text-sm {{ ($question['correct_answer'] ?? '') === $key || ($question['correct_answer'] ?? '') === $option ? 'text-green-700 dark:text-green-400 font-medium' : 'text-gray-600 dark:text-gray-300' }}">
                                                                <span class="w-6 h-6 flex items-center justify-center rounded-full {{ ($question['correct_answer'] ?? '') === $key || ($question['correct_answer'] ?? '') === $option ? 'bg-green-100 dark:bg-green-900/50' : 'bg-gray-100 dark:bg-gray-600' }} text-xs mr-2">
                                                                    {{ is_numeric($key) ? chr(65 + $key) : $key }}
                                                                </span>
                                                                {{ $option }}
                                                                @if(($question['correct_answer'] ?? '') === $key || ($question['correct_answer'] ?? '') === $option)
                                                                    <svg class="w-4 h-4 ml-1 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                                                    </svg>
                                                                @endif
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                @endif

                                                @if(($question['type'] ?? '') === 'true_false')
                                                    <div class="mt-2 text-sm">
                                                        <span class="text-gray-500 dark:text-gray-400">Correct Answer:</span>
                                                        <span class="ml-1 font-medium {{ ($question['correct_answer'] ?? '') === 'True' || ($question['correct_answer'] ?? '') === true ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                                                            {{ is_bool($question['correct_answer'] ?? false) ? ($question['correct_answer'] ? 'True' : 'False') : ($question['correct_answer'] ?? 'N/A') }}
                                                        </span>
                                                    </div>
                                                @endif

                                                @if(isset($question['explanation']) && !empty($question['explanation']))
                                                    <div class="mt-3 p-2 bg-blue-50 dark:bg-blue-900/30 rounded-md">
                                                        <p class="text-xs text-blue-700 dark:text-blue-300"><strong>Explanation:</strong> {{ $question['explanation'] }}</p>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <!-- Edit Question Modal -->
                        @if($editingQuestionIndex !== null && $editingQuestion)
                            <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                                <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                                    <div class="fixed inset-0 bg-gray-500 dark:bg-gray-900 bg-opacity-75 dark:bg-opacity-75 transition-opacity" wire:click="cancelEditing"></div>
                                    <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
                                    <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                                        <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                                            <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white mb-4">Edit Question {{ $editingQuestionIndex + 1 }}</h3>

                                            <div class="space-y-4">
                                                <!-- Question Text -->
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Question Text *</label>
                                                    <textarea wire:model="editingQuestion.question"
                                                              rows="3"
                                                              class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                                                    @error('editingQuestion.question') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                                                </div>

                                                <!-- Options (for multiple choice) -->
                                                @if(($editingQuestion['type'] ?? '') === 'multiple_choice')
                                                    <div>
                                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Options</label>
                                                        @foreach($editingQuestion['options'] ?? [] as $key => $option)
                                                            <div class="flex items-center gap-2 mb-2">
                                                                <span class="w-6 h-6 flex items-center justify-center rounded-full bg-gray-100 dark:bg-gray-600 text-xs dark:text-gray-200">
                                                                    {{ is_numeric($key) ? chr(65 + $key) : $key }}
                                                                </span>
                                                                <input type="text"
                                                                       wire:model="editingQuestion.options.{{ $key }}"
                                                                       class="flex-1 rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                                            </div>
                                                        @endforeach
                                                        @error('editingQuestion.options') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                                                    </div>

                                                    <!-- Correct Answer -->
                                                    <div>
                                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Correct Answer *</label>
                                                        <select wire:model="editingQuestion.correct_answer"
                                                                class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                                            <option value="">Select correct answer...</option>
                                                            @foreach($editingQuestion['options'] ?? [] as $key => $option)
                                                                <option value="{{ $key }}">{{ is_numeric($key) ? chr(65 + $key) : $key }}: {{ Str::limit($option, 50) }}</option>
                                                            @endforeach
                                                        </select>
                                                        @error('editingQuestion.correct_answer') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                                                    </div>
                                                @endif

                                                <!-- True/False Answer -->
                                                @if(($editingQuestion['type'] ?? '') === 'true_false')
                                                    <div>
                                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Correct Answer *</label>
                                                        <select wire:model="editingQuestion.correct_answer"
                                                                class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                                            <option value="True">True</option>
                                                            <option value="False">False</option>
                                                        </select>
                                                    </div>
                                                @endif

                                                <!-- Explanation -->
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Explanation</label>
                                                    <textarea wire:model="editingQuestion.explanation"
                                                              rows="2"
                                                              class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                                                              placeholder="Explanation shown after answering..."></textarea>
                                                </div>

                                                <!-- Points -->
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Points</label>
                                                    <input type="number"
                                                           min="1"
                                                           wire:model="editingQuestion.points"
                                                           class="w-24 rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse gap-2">
                                            <button type="button"
                                                    wire:click="saveQuestion"
                                                    class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800 sm:w-auto sm:text-sm">
                                                Save Changes
                                            </button>
                                            <button type="button"
                                                    wire:click="cancelEditing"
                                                    class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 dark:border-gray-600 shadow-sm px-4 py-2 bg-white dark:bg-gray-600 text-base font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800 sm:mt-0 sm:w-auto sm:text-sm">
                                                Cancel
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Delete Confirmation Modal -->
                        @if($showDeleteConfirmation)
                            <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                                <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                                    <div class="fixed inset-0 bg-gray-500 dark:bg-gray-900 bg-opacity-75 dark:bg-opacity-75 transition-opacity" wire:click="cancelDelete"></div>
                                    <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
                                    <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                                        <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                                            <div class="sm:flex sm:items-start">
                                                <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 dark:bg-red-900/50 sm:mx-0 sm:h-10 sm:w-10">
                                                    <svg class="h-6 w-6 text-red-600 dark:text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                                    </svg>
                                                </div>
                                                <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                                                    <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white">Delete Question</h3>
                                                    <div class="mt-2">
                                                        <p class="text-sm text-gray-500 dark:text-gray-400">
                                                            Are you sure you want to delete Question {{ ($questionToDelete ?? 0) + 1 }}? This action cannot be undone.
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse gap-2">
                                            <button type="button"
                                                    wire:click="removeQuestion"
                                                    class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 dark:focus:ring-offset-gray-800 sm:w-auto sm:text-sm">
                                                Delete
                                            </button>
                                            <button type="button"
                                                    wire:click="cancelDelete"
                                                    class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 dark:border-gray-600 shadow-sm px-4 py-2 bg-white dark:bg-gray-600 text-base font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800 sm:mt-0 sm:w-auto sm:text-sm">
                                                Cancel
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Step 2 Navigation -->
                        <div class="flex justify-between pt-4 border-t border-gray-200 dark:border-gray-700 mt-6">
                            <button type="button"
                                    wire:click="previousStep"
                                    class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 text-sm font-medium rounded-md shadow-sm text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                                </svg>
                                Back to Setup
                            </button>
                            <button type="button"
                                    wire:click="nextStep"
                                    class="inline-flex items-center px-6 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white {{ empty($generatedQuestions) ? 'bg-gray-400 dark:bg-gray-600 cursor-not-allowed' : 'bg-indigo-600 hover:bg-indigo-700' }} focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800"
                                    {{ empty($generatedQuestions) ? 'disabled' : '' }}>
                                Next: Assign to Students
                                <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                                </svg>
                            </button>
                        </div>
                        @if(empty($generatedQuestions))
                            <p class="mt-2 text-sm text-amber-600 dark:text-amber-400 text-right">
                                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                </svg>
                                Generate questions first to proceed
                            </p>
                        @endif
                        @endif
                        <!-- ==================== END STEP 2 ==================== -->

                        <!-- ==================== STEP 3: ASSIGN ==================== -->
                        @if($currentStep === 3)
                        <div class="mb-4 p-4 bg-green-50 dark:bg-green-900/30 rounded-lg border border-green-200 dark:border-green-800">
                            <h3 class="text-lg font-semibold text-green-900 dark:text-green-100 flex items-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                                Step 3: Assign to Students
                            </h3>
                            <p class="mt-1 text-sm text-green-700 dark:text-green-300">Configure assignment settings and select target student groups.</p>
                        </div>

                        <!-- Generated Questions Summary -->
                        @if(!empty($generatedQuestions))
                        <div class="mb-6 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg border border-gray-200 dark:border-gray-600">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h4 class="text-sm font-medium text-gray-900 dark:text-white">Questions Ready</h4>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ count($generatedQuestions) }} questions generated • {{ $this->computedTotalMarks ?? $totalMarks }} total marks</p>
                                </div>
                                <button type="button" wire:click="goToStep(2)" class="text-sm text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300">
                                    Edit Questions →
                                </button>
                            </div>
                        </div>
                        @endif

                        <!-- Assignment Settings -->
                        <div class="mb-6">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Assignment Settings</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Duration (minutes)</label>
                                    <input type="number" min="1"
                                           class="w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm dark:bg-gray-700 dark:text-white focus:border-indigo-500 focus:ring-indigo-500"
                                           wire:model="durationInMinutes" value="60">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Randomize Questions</label>
                                    <select class="w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm dark:bg-gray-700 dark:text-white focus:border-indigo-500 focus:ring-indigo-500"
                                            wire:model="isRandomized">
                                        <option value="1">Yes</option>
                                        <option value="0">No</option>
                                    </select>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Start Date</label>
                                    <input type="datetime-local"
                                           class="w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm dark:bg-gray-700 dark:text-white focus:border-indigo-500 focus:ring-indigo-500"
                                           wire:model="startDate">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">End Date</label>
                                    <input type="datetime-local"
                                           class="w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm dark:bg-gray-700 dark:text-white focus:border-indigo-500 focus:ring-indigo-500"
                                           wire:model="endDate">
                                    @error('endDate') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Target Groups -->
                        <div class="mb-6">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Target Groups</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                @if($studentGroups->count())
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Student Groups</label>
                                        <div class="border border-gray-300 dark:border-gray-600 rounded-md p-4 max-h-48 overflow-y-auto bg-white dark:bg-gray-700">
                                            @foreach($studentGroups as $group)
                                                <div class="flex items-center mb-2 last:mb-0">
                                                    <input type="checkbox"
                                                           class="h-4 w-4 text-indigo-600 border-gray-300 dark:border-gray-600 rounded focus:ring-indigo-500 dark:bg-gray-600"
                                                           wire:model="selectedStudentGroups"
                                                           value="{{ $group->id }}"
                                                           id="group-{{ $group->id }}">
                                                    <label for="group-{{ $group->id }}" class="ml-2 block text-sm text-gray-900 dark:text-white">
                                                        {{ $group->name }}
                                                    </label>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif

                                @if($academicLevels->count())
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Academic Levels</label>
                                        <div class="border border-gray-300 dark:border-gray-600 rounded-md p-4 max-h-48 overflow-y-auto bg-white dark:bg-gray-700">
                                            @foreach($academicLevels as $level)
                                                <div class="flex items-center mb-2 last:mb-0">
                                                    <input type="checkbox"
                                                           class="h-4 w-4 text-indigo-600 border-gray-300 dark:border-gray-600 rounded focus:ring-indigo-500 dark:bg-gray-600"
                                                           wire:model="selectedAcademicLevels"
                                                           value="{{ $level->id }}"
                                                           id="level-{{ $level->id }}">
                                                    <label for="level-{{ $level->id }}" class="ml-2 block text-sm text-gray-900 dark:text-white">
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
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Academic Groups</label>
                                    <div class="border border-gray-300 dark:border-gray-600 rounded-md p-4 max-h-48 overflow-y-auto bg-white dark:bg-gray-700">
                                        @foreach($academicGroups as $group)
                                            <div class="flex items-center mb-2 last:mb-0">
                                                <input type="checkbox"
                                                       class="h-4 w-4 text-indigo-600 border-gray-300 dark:border-gray-600 rounded focus:ring-indigo-500 dark:bg-gray-600"
                                                       wire:model="selectedAcademicGroups"
                                                       value="{{ $group->id }}"
                                                       id="agroup-{{ $group->id }}">
                                                <label for="agroup-{{ $group->id }}" class="ml-2 block text-sm text-gray-900 dark:text-white">
                                                    {{ $group->name }}
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>

                        <!-- Step 3 Navigation / Submit Button -->
                        <div class="flex justify-between pt-4 border-t border-gray-200 dark:border-gray-700 mt-6">
                            <button type="button"
                                    wire:click="previousStep"
                                    class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 text-sm font-medium rounded-md shadow-sm text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                                </svg>
                                Back to Questions
                            </button>
                            <button type="submit"
                                    class="inline-flex items-center px-6 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 dark:focus:ring-offset-gray-800"
                                    wire:loading.attr="disabled"
                                    wire:target="createAssignment">
                                <span wire:loading.remove wire:target="createAssignment">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    Create Assignment
                                </span>
                                <span wire:loading wire:target="createAssignment" class="flex items-center">
                                    <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    Creating...
                                </span>
                            </button>
                        </div>
                        @endif
                        <!-- ==================== END STEP 3 ==================== -->
                    </form>
                </div>
            </div>
        </div>

        <div class="lg:col-span-1">
            <div class="bg-white dark:bg-gray-800 shadow overflow-hidden sm:rounded-lg sticky top-6">
                <div class="px-4 py-5 sm:px-6 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white">Instructions</h3>
                </div>
                <div class="px-4 py-5 sm:p-6">
                    <ol class="list-decimal pl-5 space-y-3 text-sm text-gray-600 dark:text-gray-400">
                        <li><span class="font-medium text-gray-900 dark:text-white">Select Subject:</span> Choose the subject this assignment relates to</li>
                        <li><span class="font-medium text-gray-900 dark:text-white">Select Content Source:</span> Choose an existing book from the library or upload your own content</li>
                        <li><span class="font-medium text-gray-900 dark:text-white">Configure Questions:</span> Set question type, count, difficulty, and focus areas</li>
                        <li><span class="font-medium text-gray-900 dark:text-white">Generate Questions:</span> Click "Generate Questions" to create AI-powered questions</li>
                        <li><span class="font-medium text-gray-900 dark:text-white">Review Questions:</span> Check the generated questions in the preview section</li>
                        <li><span class="font-medium text-gray-900 dark:text-white">Set Assignment Details:</span> Configure duration, dates, and target groups</li>
                        <li><span class="font-medium text-gray-900 dark:text-white">Create Assignment:</span> Click "Create Assignment" to assign to students</li>
                    </ol>
                    <div class="mt-4 p-4 bg-blue-50 dark:bg-blue-900/30 rounded-md">
                        <p class="text-sm text-blue-700 dark:text-blue-300">
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
