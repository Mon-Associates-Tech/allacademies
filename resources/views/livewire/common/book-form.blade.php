<div>
    <div class="fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center z-50 p-4">
        <div class="bg-white dark:bg-gray-800 rounded-2xl max-w-4xl w-full max-h-[95vh] overflow-y-auto shadow-2xl">
            <!-- Modal Header -->
            <div
                class="p-6 border-b border-gray-200 dark:border-gray-700 bg-gradient-to-r from-blue-600 to-purple-600 text-white rounded-t-2xl">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-white/20 rounded-lg flex items-center justify-center">
                            <i class="fas fa-book text-white"></i>
                        </div>
                        <div>
                            <h2 class="text-2xl font-bold">
                                {{ $editingBook ? 'Edit Book' : 'Add New Book' }}
                            </h2>
                            <p class="text-blue-100 text-sm">
                                {{ $editingBook ? 'Update your book details' : 'Create a new book for your readers' }}
                            </p>
                        </div>
                    </div>

                    <button wire:click="closeModal"
                            class="text-white/80 hover:text-white p-2 hover:bg-white/10 rounded-lg transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Modal Body -->
            <form wire:submit="saveBook" class="p-6">
                <div class="space-y-8">
                    <!-- Author Selection Section -->
                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                            <i class="fas fa-user-edit mr-2 text-blue-500"></i>Author Information
                        </h3>

                        <!-- Author Selection Type -->
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
                                How would you like to set the author?
                            </label>
                            <div class="flex gap-4">
                                <label
                                    class="flex items-center p-4 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors cursor-pointer">
                                    <input type="radio" wire:model.live="author_selection_type" value="existing"
                                           class="text-blue-600 border-gray-300 focus:ring-blue-500">
                                    <span class="ml-3 text-sm font-medium text-gray-700 dark:text-gray-300">
                                            <i class="fas fa-users mr-2"></i>Select Existing Author
                                        </span>
                                </label>
                                <label
                                    class="flex items-center p-4 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors cursor-pointer">
                                    <input type="radio" wire:model.live="author_selection_type" value="new"
                                           class="text-blue-600 border-gray-300 focus:ring-blue-500">
                                    <span class="ml-3 text-sm font-medium text-gray-700 dark:text-gray-300">
                                            <i class="fas fa-user-plus mr-2"></i>Enter Author Details
                                        </span>
                                </label>
                            </div>
                        </div>

                        <!-- Existing Author Selection -->
                        @if($author_selection_type === 'existing')
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Select Author <span class="text-red-500">*</span>
                                </label>
                                <select wire:model="selected_author_id"
                                        class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white transition-colors">
                                    <option value="">Choose an author</option>
                                    @foreach($authors as $author)
                                        <option value="{{ $author->id }}">{{ $author->user->name }}</option>
                                    @endforeach
                                </select>
                                @error('selected_author_id')
                                <span class="text-red-500 text-sm mt-1 block">
                                            <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                                        </span>
                                @enderror
                            </div>
                        @endif

                        <!-- New Author Details -->
                        @if($author_selection_type === 'new')
                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                                <div class="lg:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Author Name <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" wire:model="author_name"
                                           class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white transition-colors"
                                           placeholder="Enter author's full name">
                                    @error('author_name')
                                    <span class="text-red-500 text-sm mt-1 block">
                                                <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                                            </span>
                                    @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Pen Name
                                    </label>
                                    <input type="text" wire:model="author_pen_name"
                                           class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white transition-colors"
                                           placeholder="Author's pen name (if any)">
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Website
                                    </label>
                                    <input type="url" wire:model="author_website"
                                           class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white transition-colors"
                                           placeholder="https://author-website.com">
                                    @error('author_website')
                                    <span class="text-red-500 text-sm mt-1 block">
                                                <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                                            </span>
                                    @enderror
                                </div>

                                <div class="lg:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Biography
                                    </label>
                                    <textarea wire:model="author_biography" rows="3"
                                              class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white transition-colors"
                                              placeholder="Tell us about the author..."></textarea>
                                </div>

                                <!-- Additional author fields in collapsible section -->
                                <div class="lg:col-span-2">
                                    <div x-data="{ expanded: false }"
                                         class="border border-gray-300 dark:border-gray-600 rounded-lg">
                                        <button type="button" @click="expanded = !expanded"
                                                class="w-full px-4 py-3 text-left text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-50 dark:bg-gray-700 rounded-t-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors">
                                            <div class="flex items-center justify-between">
                                                <span><i class="fas fa-plus-circle mr-2"></i>Additional Author Information (Optional)</span>
                                                <i class="fas fa-chevron-down transition-transform"
                                                   :class="expanded ? 'rotate-180' : ''"></i>
                                            </div>
                                        </button>

                                        <div x-show="expanded" x-collapse
                                             class="p-4 space-y-4 bg-white dark:bg-gray-800">
                                            <div>
                                                <label
                                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Education</label>
                                                <textarea wire:model="author_education" rows="2"
                                                          class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white transition-colors"
                                                          placeholder="Educational background"></textarea>
                                            </div>
                                            <div>
                                                <label
                                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Writing
                                                    Experience</label>
                                                <textarea wire:model="author_writing_experience" rows="2"
                                                          class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white transition-colors"
                                                          placeholder="Years of writing experience, notable works, etc."></textarea>
                                            </div>
                                            <div>
                                                <label
                                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Awards
                                                    & Recognition</label>
                                                <textarea wire:model="author_awards" rows="2"
                                                          class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white transition-colors"
                                                          placeholder="Awards, recognition, achievements"></textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Book Information Section -->

                    <div class="mb-8 bg-white shadow-lg rounded-xl border border-gray-100 overflow-hidden">
                        <!-- Form Header with Progress -->
                        <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-4">
                            <div class="flex justify-between items-center">
                                <div class="flex items-center space-x-3">
                                    <div class="p-2 bg-white/10 rounded-lg">
                                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor"
                                             viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <h2 class="text-xl font-semibold text-white">
                                            {{ $editingBook ? 'Edit Book' : 'Add New Book' }}
                                        </h2>
                                        <p class="text-blue-100 text-sm">
                                            {{ $editingBook ? 'Update book information and settings' : 'Fill in the details to add a new book to your library' }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <form wire:submit.prevent="{{ $editingBook ? 'update' : 'create' }}" class="relative">
                            <!-- Form Content -->
                            <div class="p-8">
                                <!-- Step 1: Basic Information -->
                                <div class="mb-10">
                                    <div class="flex items-center mb-6">
                                        <div
                                            class="flex items-center justify-center w-8 h-8 bg-blue-600 text-white rounded-full text-sm font-medium mr-3">
                                            1
                                        </div>
                                        <div>
                                            <h3 class="text-lg font-semibold text-gray-900">Basic Information</h3>
                                            <p class="text-sm text-gray-500">Enter the fundamental details about the
                                                book</p>
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 ml-11">
                                        <div class="lg:col-span-2">
                                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                                Book Title <span class="text-red-500">*</span>
                                            </label>
                                            <div class="relative">
                                                <input type="text" wire:model="title"
                                                       placeholder="Enter the complete book title"
                                                       class="block w-full pl-4 pr-10 py-3 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                                                <div
                                                    class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor"
                                                         viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                              stroke-width="2"
                                                              d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                    </svg>
                                                </div>
                                            </div>
                                            @error('title')
                                            <p class="mt-2 text-sm text-red-600 flex items-center">
                                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd"
                                                          d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                                          clip-rule="evenodd"></path>
                                                </svg>{{ $message }}</p> @enderror
                                        </div>

                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                                URL Slug
                                            </label>
                                            <input type="text" wire:model="slug" readonly
                                                   class="block w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-600 dark:text-gray-400 text-gray-500 cursor-not-allowed">
                                            <p class="mt-1 text-xs text-gray-500">Auto-generated from title</p>
                                        </div>

                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                                Author <span class="text-red-500">*</span>
                                            </label>
                                            <div class="relative">
                                                <select wire:model="authorId"
                                                        class="block w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 appearance-none bg-white">
                                                    <option value="">Choose an author</option>
                                                    @foreach($authors as $author)
                                                        <option
                                                            value="{{ $author->id }}">{{ $author->user->name }}</option>
                                                    @endforeach
                                                </select>
                                                <div
                                                    class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor"
                                                         viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                              stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                                    </svg>
                                                </div>
                                            </div>
                                            @error('authorId')
                                            <p class="mt-2 text-sm text-red-600 flex items-center">
                                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd"
                                                          d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                                          clip-rule="evenodd"></path>
                                                </svg>{{ $message }}</p> @enderror
                                        </div>

                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                                Category <span class="text-red-500">*</span>
                                            </label>
                                            <div class="relative">
                                                <select wire:model="bookCategoryId"
                                                        class="block w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 appearance-none bg-white">
                                                    <option value="">Select a category</option>
                                                    @foreach($categories as $category)
                                                        <option
                                                            value="{{ $category->id }}">{{ $category->name }}</option>
                                                    @endforeach
                                                </select>
                                                <div
                                                    class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor"
                                                         viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                              stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                                    </svg>
                                                </div>
                                            </div>
                                            @error('bookCategoryId')
                                            <p class="mt-2 text-sm text-red-600 flex items-center">
                                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd"
                                                          d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                                          clip-rule="evenodd"></path>
                                                </svg>{{ $message }}</p> @enderror
                                        </div>

                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Edition</label>
                                            <input type="text" wire:model="edition"
                                                   placeholder="e.g., 1st Edition, Revised Edition"
                                                   class="block w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                            @error('edition')
                                            <p class="mt-2 text-sm text-red-600 flex items-center">
                                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd"
                                                          d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                                          clip-rule="evenodd"></path>
                                                </svg>{{ $message }}</p> @enderror
                                        </div>

                                        <div>
                                            <label
                                                class="block text-sm font-medium text-gray-700 mb-2">Publisher</label>
                                            <input type="text" wire:model="publisher"
                                                   placeholder="Publishing house name"
                                                   class="block w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                            @error('publisher')
                                            <p class="mt-2 text-sm text-red-600 flex items-center">
                                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd"
                                                          d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                                          clip-rule="evenodd"></path>
                                                </svg>{{ $message }}</p> @enderror
                                        </div>

                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Number of
                                                Pages</label>
                                            <input type="number" wire:model="pages" min="1"
                                                   placeholder="Total pages"
                                                   class="block w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                            @error('pages')
                                            <p class="mt-2 text-sm text-red-600 flex items-center">
                                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd"
                                                          d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                                          clip-rule="evenodd"></path>
                                                </svg>{{ $message }}</p> @enderror
                                        </div>

                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                                Annual Subscription Fee
                                                <span
                                                    class="text-xs bg-gray-100 text-gray-600 px-2 py-1 rounded-full ml-1">GHS</span>
                                            </label>
                                            <div class="relative">
                                                <div
                                                    class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                    <span class="text-gray-500 text-sm">₵</span>
                                                </div>
                                                <input type="number" wire:model="annualSubscriptionFee" step="0.01"
                                                       min="0"
                                                       placeholder="0.00"
                                                       class="block w-full pl-8 pr-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                            </div>
                                            <p class="mt-1 text-xs text-gray-500 flex items-center">
                                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd"
                                                          d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                                          clip-rule="evenodd"></path>
                                                </svg>
                                                Enter 0 to make this book free for all users
                                            </p>
                                            @error('annualSubscriptionFee')
                                            <p class="mt-2 text-sm text-red-600 flex items-center">
                                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd"
                                                          d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                                          clip-rule="evenodd"></path>
                                                </svg>{{ $message }}</p> @enderror
                                        </div>
                                    </div>
                                </div>

                                <!-- Step 2: Book Formats -->
                                <div class="mb-10">
                                    <div class="flex items-center mb-6">
                                        <div
                                            class="flex items-center justify-center w-8 h-8 bg-blue-600 text-white rounded-full text-sm font-medium mr-3">
                                            2
                                        </div>
                                        <div>
                                            <h3 class="text-lg font-semibold text-gray-900">Available Formats</h3>
                                            <p class="text-sm text-gray-500">Select the formats in which this book is
                                                available</p>
                                        </div>
                                    </div>

                                    <div class="ml-11">
                                        <div class="bg-gray-50 rounded-lg p-6">
                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                <label
                                                    class="relative flex items-start p-4 bg-white border-2 border-gray-200 rounded-lg cursor-pointer hover:border-blue-300 transition-colors">
                                                    <input type="checkbox" wire:model="hasHardcopy"
                                                           class="h-5 w-5 text-blue-600 dark:text-blue-500 focus:ring-blue-500 dark:focus:ring-blue-400 border-gray-300 dark:border-gray-600 dark:bg-gray-700 rounded mt-0.5">
                                                    <div class="ml-3">
                                                        <div class="flex items-center">
                                                            <svg class="w-5 h-5 text-green-600 mr-2" fill="none"
                                                                 stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                      stroke-width="2"
                                                                      d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                                            </svg>
                                                            <span
                                                                class="font-medium text-gray-900">Physical Hardcopy</span>
                                                        </div>
                                                        <p class="text-sm text-gray-500 mt-1">Traditional printed book
                                                            available for borrowing</p>
                                                    </div>
                                                </label>

                                                <label
                                                    class="relative flex items-start p-4 bg-white border-2 border-gray-200 rounded-lg cursor-pointer hover:border-blue-300 transition-colors">
                                                    <input type="checkbox" wire:model="hasSoftcopy"
                                                           class="h-5 w-5 text-blue-600 dark:text-blue-500 focus:ring-blue-500 dark:focus:ring-blue-400 border-gray-300 dark:border-gray-600 dark:bg-gray-700 rounded mt-0.5">
                                                    <div class="ml-3">
                                                        <div class="flex items-center">
                                                            <svg class="w-5 h-5 text-purple-600 mr-2" fill="none"
                                                                 stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                      stroke-width="2"
                                                                      d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                            </svg>
                                                            <span
                                                                class="font-medium text-gray-900">Digital Softcopy</span>
                                                        </div>
                                                        <p class="text-sm text-gray-500 mt-1">PDF version for online
                                                            reading and downloads</p>
                                                    </div>
                                                </label>
                                            </div>
                                        </div>
                                        @error('hasHardcopy')
                                        <p class="mt-2 text-sm text-red-600 flex items-center">
                                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                      d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                                      clip-rule="evenodd"></path>
                                            </svg>{{ $message }}</p> @enderror
                                    </div>
                                </div>

                                <!-- Step 3: Media Files -->
                                <div class="mb-10">
                                    <div class="flex items-center mb-6">
                                        <div
                                            class="flex items-center justify-center w-8 h-8 bg-blue-600 text-white rounded-full text-sm font-medium mr-3">
                                            3
                                        </div>
                                        <div>
                                            <h3 class="text-lg font-semibold text-gray-900">Media & Files</h3>
                                            <p class="text-sm text-gray-500">Upload cover image and PDF file</p>
                                        </div>
                                    </div>

                                    <div class="ml-11 grid grid-cols-1 lg:grid-cols-2 gap-8">
                                        <!-- Cover Image Upload -->
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-3">Cover
                                                Image</label>
                                            <div class="space-y-4">
                                                <!-- File Input -->
                                                <div
                                                    class="flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg hover:border-blue-400 transition-colors">
                                                    <div class="space-y-1 text-center">
                                                        <svg class="mx-auto h-12 w-12 text-gray-400"
                                                             stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                                            <path
                                                                d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02"
                                                                stroke-width="2" stroke-linecap="round"
                                                                stroke-linejoin="round"/>
                                                        </svg>
                                                        <div class="flex text-sm text-gray-600">
                                                            <label
                                                                class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-blue-500">
                                                                <span>Upload a file</span>
                                                                <input type="file" wire:model="coverImage"
                                                                       accept="image/*" class="sr-only">
                                                            </label>
                                                            <p class="pl-1">or drag and drop</p>
                                                        </div>
                                                        <p class="text-xs text-gray-500">PNG, JPG, JPEG up to 2MB</p>
                                                    </div>
                                                </div>

                                                <!-- Current/Preview Image -->
                                                @if ($editingBook && $existingCover && !$cover_image)
                                                    <div class="bg-gray-50 rounded-lg p-4">
                                                        <p class="text-sm font-medium text-gray-700 mb-2">Current
                                                            Cover:</p>
                                                        <img src="{{ Storage::url($existingCover) }}" alt="Book Cover"
                                                             class="h-32 w-24 object-cover rounded-lg border-2 border-gray-200 shadow-sm">
                                                    </div>
                                                @endif

                                                @if ($cover_image)
                                                    <div class="bg-green-50 rounded-lg p-4 border border-green-200">
                                                        <p class="text-sm font-medium text-green-800 mb-2 flex items-center">
                                                            <svg class="w-4 h-4 mr-1" fill="currentColor"
                                                                 viewBox="0 0 20 20">
                                                                <path fill-rule="evenodd"
                                                                      d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                                                      clip-rule="evenodd"></path>
                                                            </svg>
                                                            New Cover Preview:
                                                        </p>
                                                        <img src="{{ $cover_image->temporaryUrl() }}"
                                                             alt="Cover Preview"
                                                             class="h-32 w-24 object-cover rounded-lg border-2 border-green-300 shadow-sm">
                                                    </div>
                                                @endif
                                            </div>
                                            @error('coverImage')
                                            <p class="mt-2 text-sm text-red-600 flex items-center">
                                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd"
                                                          d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                                          clip-rule="evenodd"></path>
                                                </svg>{{ $message }}</p> @enderror
                                        </div>

                                        <!-- PDF Upload -->
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-3">
                                                PDF File
                                                <span
                                                    class="text-xs bg-purple-100 text-purple-700 px-2 py-1 rounded-full ml-1">Required for softcopy</span>
                                            </label>
                                            <div class="space-y-4">
                                                <!-- File Input -->
                                                <div
                                                    class="flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg hover:border-purple-400 transition-colors">
                                                    <div class="space-y-1 text-center">
                                                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none"
                                                             stroke="currentColor" viewBox="0 0 48 48">
                                                            <path stroke-width="2" stroke-linecap="round"
                                                                  stroke-linejoin="round"
                                                                  d="M9 12h6m6 0h6m-6 6v6m-6-6v6m6 0v6"/>
                                                        </svg>
                                                        <div class="flex text-sm text-gray-600">
                                                            <label
                                                                class="relative cursor-pointer bg-white rounded-md font-medium text-purple-600 hover:text-purple-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-purple-500">
                                                                <span>Upload PDF file</span>
                                                                <input type="file" wire:model="pdfFile" accept=".pdf"
                                                                       class="sr-only">
                                                            </label>
                                                        </div>
                                                        <p class="text-xs text-gray-500">PDF files only, up to 10MB</p>
                                                    </div>
                                                </div>

                                                <!-- Current/New PDF Status -->
                                                @if ($editingBook && $existingPdf && !$pdfFile)
                                                    <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                                                        <p class="text-sm font-medium text-gray-700 mb-2">Current
                                                            PDF:</p>
                                                        <a href="{{ Storage::url($existingPdf) }}" target="_blank"
                                                           class="inline-flex items-center px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 dark:text-white text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor"
                                                                 viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                      stroke-width="2"
                                                                      d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                      stroke-width="2"
                                                                      d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                            </svg>
                                                            View Current PDF
                                                        </a>
                                                    </div>
                                                @endif

                                                @if ($pdf_file)
                                                    <div class="bg-purple-50 rounded-lg p-4 border border-purple-200">
                                                        <p class="text-sm font-medium text-purple-800 flex items-center">
                                                            <svg class="w-4 h-4 mr-1" fill="currentColor"
                                                                 viewBox="0 0 20 20">
                                                                <path fill-rule="evenodd"
                                                                      d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                                                      clip-rule="evenodd"></path>
                                                            </svg>
                                                            New PDF Selected: {{ $pdf_file->getClientOriginalName() }}
                                                        </p>
                                                    </div>
                                                @endif
                                            </div>
                                            @error('pdfFile')
                                            <p class="mt-2 text-sm text-red-600 flex items-center">
                                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd"
                                                          d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                                          clip-rule="evenodd"></path>
                                                </svg>{{ $message }}</p> @enderror
                                        </div>
                                    </div>
                                </div>

                                <!-- Step 4: Additional Details -->
                                <div class="mb-8">
                                    <div class="flex items-center mb-6">
                                        <div
                                            class="flex items-center justify-center w-8 h-8 bg-blue-600 text-white rounded-full text-sm font-medium mr-3">
                                            4
                                        </div>
                                        <div>
                                            <h3 class="text-lg font-semibold text-gray-900">Additional Details</h3>
                                            <p class="text-sm text-gray-500">Extra information and subscription
                                                conditions</p>
                                        </div>
                                    </div>

                                    <div class="ml-11 space-y-6">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Additional
                                                Information</label>
                                            <textarea wire:model="additionalInfo" rows="4"
                                                      placeholder="Enter any additional information about the book, such as description, special notes, or summary..."
                                                      class="block w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 resize-none"></textarea>
                                            @error('additionalInfo')
                                            <p class="mt-2 text-sm text-red-600 flex items-center">
                                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd"
                                                          d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                                          clip-rule="evenodd"></path>
                                                </svg>{{ $message }}</p> @enderror
                                        </div>

                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Subscription
                                                Conditions</label>
                                            <textarea wire:model="subscriptionConditions" rows="4"
                                                      placeholder="Enter specific terms and conditions for subscribing to this book. Leave blank to use default library conditions..."
                                                      class="block w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 resize-none"></textarea>
                                            <p class="mt-1 text-xs text-gray-500">These conditions will be shown to
                                                users when they subscribe to this book</p>
                                            @error('subscriptionConditions')
                                            <p class="mt-2 text-sm text-red-600 flex items-center">
                                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd"
                                                          d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                                          clip-rule="evenodd"></path>
                                                </svg>{{ $message }}</p> @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Form Actions -->
                            <div
                                class="bg-gray-50 px-8 py-6 border-t border-gray-200 flex flex-col sm:flex-row sm:justify-between sm:items-center space-y-3 sm:space-y-0">
                                <div class="text-sm text-gray-500">
                                    <span class="font-medium">Required fields are marked with</span> <span
                                        class="text-red-500">*</span>
                                </div>
                                <div class="flex space-x-3">
                                    <button type="button" wire:click="closeModal"
                                            class="inline-flex items-center px-6 py-3 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                        Cancel
                                    </button>
                                    <button type="submit"
                                            class="inline-flex items-center px-6 py-3 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200">
                                        @if($editingBook)
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor"
                                                 viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                                            </svg>
                                            Update Book
                                        @else
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor"
                                                 viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M12 4v16m8-8H4"></path>
                                            </svg>
                                            Create Book
                                        @endif
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>

                </div>
            </form>
        </div>
    </div>
</div>
