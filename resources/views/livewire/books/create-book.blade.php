<div class="min-h-screen bg-gradient-to-br from-gray-50 to-blue-50">
    <!-- Header -->
    <div class="bg-white shadow-sm border-b border-gray-200 sticky top-0 z-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-6">
                <div class="flex items-center space-x-4">
                    <button wire:click="cancel" class="flex items-center text-gray-600 hover:text-gray-900 transition-colors">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                        Back to Books
                    </button>
                    <div class="border-l border-gray-300 pl-4">
                        <h1 class="text-2xl font-bold text-gray-900">Create New Book</h1>
                        <p class="text-sm text-gray-500">Add a new book to your library collection</p>
                    </div>
                </div>
                <div class="flex space-x-3">
                    <button wire:click="cancel" type="button"
                            class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                        Cancel
                    </button>
                    <button wire:click="create" type="button"
                            class="px-4 py-2 bg-blue-600 border border-transparent rounded-lg text-sm font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                        <svg class="w-4 h-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        Create Book
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Notifications -->
        @if (session()->has('message'))
            <div class="mb-6 bg-green-50 border border-green-200 rounded-lg p-4">
                <div class="flex">
                    <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-green-800">{{ session('message') }}</p>
                    </div>
                </div>
            </div>
        @endif

        @error('general')
        <div class="mb-6 bg-red-50 border border-red-200 rounded-lg p-4">
            <div class="flex">
                <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                </svg>
                <div class="ml-3">
                    <p class="text-sm font-medium text-red-800">{{ $message }}</p>
                </div>
            </div>
        </div>
        @enderror

        <!-- Form Container -->
        <div class="bg-white shadow-xl rounded-2xl overflow-hidden">
            <!-- Progress Bar -->
            <div class="bg-gradient-to-r from-blue-600 to-indigo-600 px-6 py-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <div class="p-2 bg-white/10 rounded-lg">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-xl font-semibold text-white">Book Information</h2>
                            <p class="text-blue-100 text-sm">Fill in the details to create a new book</p>
                        </div>
                    </div>
                </div>
            </div>

            <form wire:submit.prevent="create" class="p-8">
                <!-- Step 1: Basic Information -->
                <div class="mb-12">
                    <div class="flex items-center mb-8">
                        <div class="flex items-center justify-center w-10 h-10 bg-blue-600 text-white rounded-full text-sm font-bold mr-4">1</div>
                        <div>
                            <h3 class="text-xl font-bold text-gray-900">Basic Information</h3>
                            <p class="text-gray-600">Enter the fundamental details about the book</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 ml-14">
                        <!-- Title -->
                        <div class="lg:col-span-2">
                            <label class="block text-sm font-semibold text-gray-700 mb-3">
                                Book Title <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <input type="text" wire:model.live="title"
                                       placeholder="Enter the complete book title"
                                       class="block w-full pl-4 pr-10 py-4 border border-gray-300 rounded-xl shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-lg">
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                </div>
                            </div>
                            @error('title') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <!-- Slug -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-3">URL Slug</label>
                            <input type="text" wire:model="slug" readonly
                                   class="block w-full px-4 py-4 border border-gray-300 rounded-xl bg-gray-50 text-gray-500 cursor-not-allowed">
                            <p class="mt-1 text-xs text-gray-500">Auto-generated from title</p>
                        </div>

                        <!-- Author Selection with New Author Option -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-3">
                                Author <span class="text-red-500">*</span>
                            </label>

                            @if(!$showNewAuthorForm)
                                <div class="space-y-3">
                                    <div class="relative">
                                        <select wire:model="authorId"
                                                class="block w-full px-4 py-4 border border-gray-300 rounded-xl shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 appearance-none bg-white">
                                            <option value="">Choose an author</option>
                                            @foreach($authors as $author)
                                                <option value="{{ $author->id }}">{{ $author->user->name }}</option>
                                            @endforeach
                                        </select>
                                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                            </svg>
                                        </div>
                                    </div>
                                    <button type="button" wire:click="toggleNewAuthorForm"
                                            class="w-full flex items-center justify-center px-4 py-2 border border-dashed border-blue-300 rounded-xl text-sm font-medium text-blue-600 hover:border-blue-400 hover:text-blue-700 transition-colors">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                        </svg>
                                        Add New Author
                                    </button>
                                </div>
                            @else
                                <div class="space-y-4 p-4 bg-blue-50 rounded-xl border border-blue-200">
                                    <div class="flex items-center justify-between">
                                        <h4 class="font-medium text-blue-900">Add New Author</h4>
                                        <button type="button" wire:click="toggleNewAuthorForm"
                                                class="text-blue-600 hover:text-blue-800">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                            </svg>
                                        </button>
                                    </div>
                                    <div class="grid grid-cols-1 gap-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Author Name</label>
                                            <input type="text" wire:model="newAuthorName"
                                                   placeholder="Full name of the author"
                                                   class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                            @error('newAuthorName') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Author Email</label>
                                            <input type="email" wire:model="newAuthorEmail"
                                                   placeholder="author@example.com"
                                                   class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                            @error('newAuthorEmail') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                                        </div>
                                        <button type="button" wire:click="createNewAuthor"
                                                class="w-full bg-blue-600 text-white py-2 px-4 rounded-lg hover:bg-blue-700 transition-colors">
                                            Create Author
                                        </button>
                                    </div>
                                </div>
                            @endif
                            @error('authorId') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <!-- Category -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-3">
                                Category <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <select wire:model="bookCategoryId"
                                        class="block w-full px-4 py-4 border border-gray-300 rounded-xl shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 appearance-none bg-white">
                                    <option value="">Select a category</option>
                                    @foreach($bookCategories as $category)
                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                    @endforeach
                                </select>
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </div>
                            </div>
                            @error('bookCategoryId') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <!-- Edition -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-3">Edition</label>
                            <input type="text" wire:model="edition"
                                   placeholder="e.g., 1st Edition, Revised Edition"
                                   class="block w-full px-4 py-4 border border-gray-300 rounded-xl shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            @error('edition') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <!-- Publisher -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-3">Publisher</label>
                            <input type="text" wire:model="publisher"
                                   placeholder="Publishing house name"
                                   class="block w-full px-4 py-4 border border-gray-300 rounded-xl shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            @error('publisher') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <!-- Pages -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-3">Number of Pages</label>
                            <input type="number" wire:model.live="pages" min="1"
                                   placeholder="Total pages"
                                   class="block w-full px-4 py-4 border border-gray-300 rounded-xl shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            @error('pages') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <!-- Subscription Fee -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-3">
                                Annual Subscription Fee
                                <span class="text-xs bg-gray-100 text-gray-600 px-2 py-1 rounded-full ml-1">GHS</span>
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center">
                                    <span class="text-gray-500">₵</span>
                                </div>
                                <input type="number" wire:model="annualSubscriptionFee" step="0.01" min="0"
                                       placeholder="0.00"
                                       class="block w-full pl-8 pr-4 py-4 border border-gray-300 rounded-xl shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            <p class="mt-1 text-xs text-gray-500">Enter 0 to make this book free</p>
                            @error('annualSubscriptionFee') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <!-- Additional Info -->
                        <div class="lg:col-span-2">
                            <label class="block text-sm font-semibold text-gray-700 mb-3">Additional Information</label>
                            <textarea wire:model="additionalInfo" rows="4"
                                      placeholder="Any additional information about the book..."
                                      class="block w-full px-4 py-3 border border-gray-300 rounded-xl shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 resize-none"></textarea>
                            @error('additionalInfo') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                <!-- Step 2: Book Formats -->
                <div class="mb-12">
                    <div class="flex items-center mb-8">
                        <div class="flex items-center justify-center w-10 h-10 bg-blue-600 text-white rounded-full text-sm font-bold mr-4">2</div>
                        <div>
                            <h3 class="text-xl font-bold text-gray-900">Available Formats</h3>
                            <p class="text-gray-600">Select the formats in which this book is available</p>
                        </div>
                    </div>

                    <div class="ml-14">
                        <div class="bg-gradient-to-br from-gray-50 to-blue-50 rounded-2xl p-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <label class="relative flex items-start p-6 bg-white border-2 border-gray-200 rounded-xl cursor-pointer hover:border-blue-300 hover:shadow-md transition-all">
                                    <input type="checkbox" wire:model="hasHardcopy"
                                           class="h-5 w-5 text-blue-600 focus:ring-blue-500 border-gray-300 rounded mt-1">
                                    <div class="ml-4">
                                        <div class="flex items-center mb-2">
                                            <svg class="w-6 h-6 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                            </svg>
                                            <span class="font-semibold text-gray-900">Physical Hardcopy</span>
                                        </div>
                                        <p class="text-sm text-gray-600">Traditional printed book available for borrowing</p>
                                    </div>
                                </label>

                                <label class="relative flex items-start p-6 bg-white border-2 border-gray-200 rounded-xl cursor-pointer hover:border-blue-300 hover:shadow-md transition-all">
                                    <input type="checkbox" wire:model="hasSoftcopy"
                                           class="h-5 w-5 text-blue-600 focus:ring-blue-500 border-gray-300 rounded mt-1">
                                    <div class="ml-4">
                                        <div class="flex items-center mb-2">
                                            <svg class="w-6 h-6 text-purple-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                            </svg>
                                            <span class="font-semibold text-gray-900">Digital Softcopy</span>
                                        </div>
                                        <p class="text-sm text-gray-600">PDF version for online reading and downloads</p>
                                    </div>
                                </label>
                            </div>
                        </div>
                        @error('hasHardcopy') <p class="mt-3 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>

                <!-- Step 3: Table of Contents -->


                <!-- Replace the existing Step 3: Table of Contents section with this enhanced version -->

                <!-- Step 3: Table of Contents -->
                <div class="mb-12">
                    <div class="flex items-center mb-8">
                        <div class="flex items-center justify-center w-10 h-10 bg-blue-600 text-white rounded-full text-sm font-bold mr-4">3</div>
                        <div class="flex-1">
                            <h3 class="text-xl font-bold text-gray-900">Table of Contents</h3>
                            <p class="text-gray-600">Define the structure, chapters, and sections of your book</p>
                        </div>
                        <div class="flex space-x-3">
                            <button type="button" wire:click="toggleTableOfContents"
                                    class="px-4 py-2 bg-blue-100 text-blue-700 rounded-lg hover:bg-blue-200 transition-colors">
                                {{ $showTableOfContents ? 'Hide' : 'Show' }} Table of Contents
                            </button>
                        </div>
                    </div>

                    @if($showTableOfContents)
                        <div class="ml-14 space-y-8">
                            @foreach($tableOfContents as $chapterIndex => $chapter)
                                <div class="bg-white border-2 border-gray-200 rounded-2xl shadow-sm hover:shadow-md transition-shadow">
                                    <!-- Chapter Header -->
                                    <div class="bg-gradient-to-r from-gray-50 to-blue-50 px-6 py-4 rounded-t-2xl border-b border-gray-200">
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center space-x-4">
                                                <button type="button" wire:click="toggleChapter({{ $chapterIndex }})"
                                                        class="flex items-center justify-center w-8 h-8 bg-blue-600 text-white rounded-full text-sm font-bold hover:bg-blue-700 transition-colors">
                                                    {{ $chapter['chapter'] }}
                                                </button>
                                                <div>
                                                    <h4 class="text-lg font-bold text-gray-900">Chapter {{ $chapter['chapter'] }}</h4>
                                                    <p class="text-sm text-gray-600">
                                                        Pages {{ $chapter['page_start'] }}-{{ $chapter['page_end'] }}
                                                        @if(!empty($chapter['sections']))
                                                            • {{ count($chapter['sections']) }} section(s)
                                                        @endif
                                                    </p>
                                                </div>
                                            </div>
                                            <div class="flex items-center space-x-2">
                                                <button type="button" wire:click="generateSections({{ $chapterIndex }})"
                                                        class="px-3 py-1 text-xs bg-green-100 text-green-700 rounded-full hover:bg-green-200 transition-colors"
                                                        title="Auto-generate sections">
                                                    <svg class="w-3 h-3 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                                    </svg>
                                                    Auto
                                                </button>
                                                <button type="button" wire:click="addSection({{ $chapterIndex }})"
                                                        class="px-3 py-1 text-xs bg-blue-100 text-blue-700 rounded-full hover:bg-blue-200 transition-colors">
                                                    <svg class="w-3 h-3 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                                    </svg>
                                                    Add Section
                                                </button>
                                                <button type="button" wire:click="toggleChapter({{ $chapterIndex }})"
                                                        class="p-1 text-gray-500 hover:text-gray-700 transition-colors">
                                                    <svg class="w-4 h-4 transform transition-transform {{ in_array($chapterIndex, $expandedChapters) ? 'rotate-180' : '' }}"
                                                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                                    </svg>
                                                </button>
                                                @if(count($tableOfContents) > 1)
                                                    <button type="button" wire:click="removeChapter({{ $chapterIndex }})"
                                                            class="p-1 text-red-500 hover:text-red-700 transition-colors"
                                                            title="Remove chapter">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                        </svg>
                                                    </button>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Chapter Content -->
                                    <div class="p-6 space-y-6">
                                        <!-- Chapter Details -->
                                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-1">Chapter Number</label>
                                                <input type="number" wire:model="tableOfContents.{{ $chapterIndex }}.chapter" min="1"
                                                       class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                                @error("tableOfContents.{$chapterIndex}.chapter")
                                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                                @enderror
                                            </div>

                                            <div class="lg:col-span-2">
                                                <label class="block text-sm font-medium text-gray-700 mb-1">Chapter Title</label>
                                                <input type="text" wire:model="tableOfContents.{{ $chapterIndex }}.title"
                                                       placeholder="Chapter title"
                                                       class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                                @error("tableOfContents.{$chapterIndex}.title")
                                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                                @enderror
                                            </div>

                                            <div class="grid grid-cols-2 gap-2">
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700 mb-1">Start Page</label>
                                                    <input type="number" wire:model="tableOfContents.{{ $chapterIndex }}.page_start" min="1"
                                                           class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                                    @error("tableOfContents.{$chapterIndex}.page_start")
                                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                                    @enderror
                                                </div>

                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700 mb-1">End Page</label>
                                                    <input type="number" wire:model="tableOfContents.{{ $chapterIndex }}.page_end" min="1"
                                                           class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                                    @error("tableOfContents.{$chapterIndex}.page_end")
                                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>

                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Chapter Description</label>
                                            <textarea wire:model="tableOfContents.{{ $chapterIndex }}.description" rows="2"
                                                      placeholder="Brief description of this chapter..."
                                                      class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 resize-none"></textarea>
                                        </div>

                                        <!-- Sections -->
                                        @if(in_array($chapterIndex, $expandedChapters) && !empty($chapter['sections']))
                                            <div class="border-t border-gray-200 pt-6">
                                                <div class="flex items-center justify-between mb-4">
                                                    <h5 class="text-lg font-semibold text-gray-900 flex items-center">
                                                        <svg class="w-5 h-5 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                        </svg>
                                                        Sections ({{ count($chapter['sections']) }})
                                                    </h5>
                                                    <div class="text-sm text-gray-500">
                                                        Pages {{ $chapter['page_start'] }}-{{ $chapter['page_end'] }}
                                                    </div>
                                                </div>

                                                <div class="space-y-4">
                                                    @foreach($chapter['sections'] as $sectionIndex => $section)
                                                        <div class="bg-gradient-to-r from-indigo-50 to-purple-50 border border-indigo-200 rounded-xl p-4">
                                                            <div class="flex items-center justify-between mb-3">
                                                                <div class="flex items-center space-x-2">
                                                                    <div class="flex items-center justify-center w-6 h-6 bg-indigo-600 text-white rounded-full text-xs font-bold">
                                                                        {{ $sectionIndex + 1 }}
                                                                    </div>
                                                                    <span class="font-medium text-gray-900">Section {{ $sectionIndex + 1 }}</span>
                                                                </div>
                                                                <button type="button" wire:click="removeSection({{ $chapterIndex }}, {{ $sectionIndex }})"
                                                                        class="p-1 text-red-500 hover:text-red-700 transition-colors"
                                                                        title="Remove section">
                                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                                    </svg>
                                                                </button>
                                                            </div>

                                                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                                                <div class="md:col-span-2">
                                                                    <label class="block text-sm font-medium text-gray-700 mb-1">Section Title</label>
                                                                    <input type="text" wire:model="tableOfContents.{{ $chapterIndex }}.sections.{{ $sectionIndex }}.title"
                                                                           placeholder="Section title"
                                                                           class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                                                    @error("tableOfContents.{$chapterIndex}.sections.{$sectionIndex}.title")
                                                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                                                    @enderror
                                                                </div>

                                                                <div class="grid grid-cols-2 gap-2">
                                                                    <div>
                                                                        <label class="block text-sm font-medium text-gray-700 mb-1">Start</label>
                                                                        <input type="number"
                                                                               wire:model="tableOfContents.{{ $chapterIndex }}.sections.{{ $sectionIndex }}.page_start"
                                                                               min="{{ $chapter['page_start'] }}"
                                                                               max="{{ $chapter['page_end'] }}"
                                                                               class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                                                        @error("tableOfContents.{$chapterIndex}.sections.{$sectionIndex}.page_start")
                                                                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                                                        @enderror
                                                                    </div>

                                                                    <div>
                                                                        <label class="block text-sm font-medium text-gray-700 mb-1">End</label>
                                                                        <input type="number"
                                                                               wire:model="tableOfContents.{{ $chapterIndex }}.sections.{{ $sectionIndex }}.page_end"
                                                                               min="{{ $chapter['page_start'] }}"
                                                                               max="{{ $chapter['page_end'] }}"
                                                                               class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                                                        @error("tableOfContents.{$chapterIndex}.sections.{$sectionIndex}.page_end")
                                                                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                                                        @enderror
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <div class="mt-3">
                                                                <label class="block text-sm font-medium text-gray-700 mb-1">Section Description</label>
                                                                <textarea wire:model="tableOfContents.{{ $chapterIndex }}.sections.{{ $sectionIndex }}.description"
                                                                          rows="2"
                                                                          placeholder="Brief description of this section..."
                                                                          class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 resize-none"></textarea>
                                                            </div>

                                                            @error("tableOfContents.{$chapterIndex}.sections.{$sectionIndex}.page_range")
                                                            <p class="mt-2 text-xs text-red-600 bg-red-50 p-2 rounded">{{ $message }}</p>
                                                            @enderror
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach

                            <!-- Add New Chapter Button -->
                            <button type="button" wire:click="addChapter"
                                    class="w-full flex items-center justify-center py-6 border-2 border-dashed border-blue-300 rounded-2xl text-blue-600 hover:border-blue-400 hover:text-blue-700 hover:bg-blue-50 transition-all">
                                <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                </svg>
                                <div>
                                    <div class="font-medium">Add New Chapter</div>
                                    <div class="text-sm text-gray-500">Create another chapter for your book</div>
                                </div>
                            </button>

                            <!-- Table of Contents Summary -->
                            @if(!empty($tableOfContents))
                                <div class="bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 rounded-2xl p-6">
                                    <h5 class="font-semibold text-gray-900 mb-3 flex items-center">
                                        <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                        </svg>
                                        Table of Contents Summary
                                    </h5>
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                                        <div class="bg-white rounded-lg p-3 border border-blue-200">
                                            <div class="font-medium text-gray-900">Total Chapters</div>
                                            <div class="text-2xl font-bold text-blue-600">{{ count($tableOfContents) }}</div>
                                        </div>
                                        <div class="bg-white rounded-lg p-3 border border-blue-200">
                                            <div class="font-medium text-gray-900">Total Sections</div>
                                            <div class="text-2xl font-bold text-indigo-600">
                                                {{ collect($tableOfContents)->sum(function($chapter) { return count($chapter['sections'] ?? []); }) }}
                                            </div>
                                        </div>
                                        <div class="bg-white rounded-lg p-3 border border-blue-200">
                                            <div class="font-medium text-gray-900">Page Coverage</div>
                                            <div class="text-2xl font-bold text-purple-600">
                                                @if(!empty($tableOfContents))
                                                    {{ min($tableOfContents)->page_start ?? 1 }}-{{ max($tableOfContents)->page_end ?? 1 }}
                                                @else
                                                    N/A
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>

               {{-- <div class="mb-12">
                    <div class="flex items-center mb-8">
                        <div class="flex items-center justify-center w-10 h-10 bg-blue-600 text-white rounded-full text-sm font-bold mr-4">3</div>
                        <div class="flex-1">
                            <h3 class="text-xl font-bold text-gray-900">Table of Contents</h3>
                            <p class="text-gray-600">Define the structure and chapters of your book</p>
                        </div>
                        <button type="button" wire:click="toggleTableOfContents"
                                class="px-4 py-2 bg-blue-100 text-blue-700 rounded-lg hover:bg-blue-200 transition-colors">
                            {{ $showTableOfContents ? 'Hide' : 'Show' }} Table of Contents
                        </button>
                    </div>

                    @if($showTableOfContents)
                        <div class="ml-14 space-y-6">
                            @foreach($tableOfContents as $index => $chapter)
                                <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm">
                                    <div class="flex items-center justify-between mb-4">
                                        <h4 class="text-lg font-semibold text-gray-900">Chapter {{ $chapter['chapter'] }}</h4>
                                        @if(count($tableOfContents) > 1)
                                            <button type="button" wire:click="removeChapter({{ $index }})"
                                                    class="text-red-600 hover:text-red-800 p-1">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                </svg>
                                            </button>
                                        @endif
                                    </div>

                                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Chapter Number</label>
                                            <input type="number" wire:model="tableOfContents.{{ $index }}.chapter" min="1"
                                                   class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                            @error("tableOfContents.{$index}.chapter") <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                                        </div>

                                        <div class="lg:col-span-2">
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Chapter Title</label>
                                            <input type="text" wire:model="tableOfContents.{{ $index }}.title"
                                                   placeholder="Chapter title"
                                                   class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                            @error("tableOfContents.{$index}.title") <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                                        </div>

                                        <div class="grid grid-cols-2 gap-2">
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-1">Start Page</label>
                                                <input type="number" wire:model="tableOfContents.{{ $index }}.page_start" min="1"
                                                       class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                                @error("tableOfContents.{$index}.page_start") <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                                            </div>

                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-1">End Page</label>
                                                <input type="number" wire:model="tableOfContents.{{ $index }}.page_end" min="1"
                                                       class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                                @error("tableOfContents.{$index}.page_end") <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mt-4">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                                        <textarea wire:model="tableOfContents.{{ $index }}.description" rows="2"
                                                  placeholder="Brief description of this chapter..."
                                                  class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 resize-none"></textarea>
                                    </div>
                                </div>
                            @endforeach

                            <button type="button" wire:click="addChapter"
                                    class="w-full flex items-center justify-center py-4 border-2 border-dashed border-blue-300 rounded-xl text-blue-600 hover:border-blue-400 hover:text-blue-700 transition-colors">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                </svg>
                                Add New Chapter
                            </button>
                        </div>
                    @endif
                </div>--}}

                <!-- Step 4: Media Files -->
                <div class="mb-12">
                    <div class="flex items-center mb-8">
                        <div class="flex items-center justify-center w-10 h-10 bg-blue-600 text-white rounded-full text-sm font-bold mr-4">4</div>
                        <div>
                            <h3 class="text-xl font-bold text-gray-900">Media & Files</h3>
                            <p class="text-gray-600">Upload cover image and PDF file</p>
                        </div>
                    </div>

                    <div class="ml-14 grid grid-cols-1 lg:grid-cols-2 gap-8">
                        <!-- Cover Image Upload -->
                        <div class="space-y-4">
                            <label class="block text-sm font-semibold text-gray-700 mb-3">Cover Image</label>
                            <div class="flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-xl hover:border-blue-400 transition-colors">
                                <div class="space-y-1 text-center">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                        <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                    <div class="flex text-sm text-gray-600">
                                        <label class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500">
                                            <span>Upload a file</span>
                                            <input type="file" wire:model="coverImage" accept="image/*" class="sr-only">
                                        </label>
                                        <p class="pl-1">or drag and drop</p>
                                    </div>
                                    <p class="text-xs text-gray-500">PNG, JPG, JPEG up to 2MB</p>
                                </div>
                            </div>

                            @if ($coverImage)
                                <div class="bg-green-50 rounded-xl p-4 border border-green-200">
                                    <p class="text-sm font-medium text-green-800 mb-2">Cover Preview:</p>
                                    <img src="{{ $coverImage->temporaryUrl() }}" alt="Cover Preview" class="h-40 w-28 object-cover rounded-lg border-2 border-green-300 shadow-sm">
                                </div>
                            @endif
                            @error('coverImage') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <!-- PDF Upload -->
                        <div class="space-y-4">
                            <label class="block text-sm font-semibold text-gray-700 mb-3">
                                PDF File
                                <span class="text-xs bg-purple-100 text-purple-700 px-2 py-1 rounded-full ml-1">Required for softcopy</span>
                            </label>
                            <div class="flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-xl hover:border-purple-400 transition-colors">
                                <div class="space-y-1 text-center">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 48 48">
                                        <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m6 0h6m-6 6v6m-6-6v6m6 0v6"/>
                                    </svg>
                                    <div class="flex text-sm text-gray-600">
                                        <label class="relative cursor-pointer bg-white rounded-md font-medium text-purple-600 hover:text-purple-500">
                                            <span>Upload PDF file</span>
                                            <input type="file" wire:model="pdfFile" accept=".pdf" class="sr-only">
                                        </label>
                                    </div>
                                    <p class="text-xs text-gray-500">PDF files only, up to 10MB</p>
                                </div>
                            </div>

                            @if ($pdfFile)
                                <div class="bg-purple-50 rounded-xl p-4 border border-purple-200">
                                    <p class="text-sm font-medium text-purple-800 mb-2 flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                        </svg>
                                        PDF File Selected: {{ $pdfFile->getClientOriginalName() }}
                                    </p>
                                    <p class="text-sm text-purple-600">Size: {{ number_format($pdfFile->getSize() / 1024 / 1024, 2) }} MB</p>
                                </div>
                            @endif
                            @error('pdfFile') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                <!-- Final Actions -->
                <div class="flex items-center justify-end space-x-4 pt-8 border-t border-gray-200">
                    <button type="button" wire:click="cancel"
                            class="px-6 py-3 border border-gray-300 rounded-xl text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                        Cancel
                    </button>
                    <button type="submit"
                            class="px-8 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 border border-transparent rounded-xl text-sm font-medium text-white hover:from-blue-700 hover:to-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all shadow-lg">
                        <svg class="w-4 h-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        Create Book
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
