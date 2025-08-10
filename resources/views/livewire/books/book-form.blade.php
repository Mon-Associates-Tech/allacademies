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
                        <h1 class="text-2xl font-bold text-gray-900">{{ $this->pageTitle }}</h1>
                        <p class="text-sm text-gray-500">
                            @if($mode === 'edit')
                                Update the details of "{{ $book->title }}"
                            @else
                                Add a new book to your library collection
                            @endif
                        </p>
                    </div>
                </div>
                <div class="flex space-x-3">
                    <button wire:click="cancel" type="button"
                            class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                        Cancel
                    </button>
                    <button wire:click="submit" type="button"
                            class="px-4 py-2 bg-blue-600 border border-transparent rounded-lg text-sm font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                        @if($mode === 'edit')
                            <svg class="w-4 h-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                            Update Book
                        @else
                            <svg class="w-4 h-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                            Create Book
                        @endif
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
                            <p class="text-blue-100 text-sm">
                                @if($mode === 'edit')
                                    Update the details for "{{ $book->title }}"
                                @else
                                    Fill in the details to create a new book
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <form wire:submit.prevent="submit" class="p-8">
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
                            <input type="text" wire:model="slug" {{ $mode === 'edit' ? '' : 'readonly' }}
                                   class="block w-full px-4 py-4 border border-gray-300 rounded-xl {{ $mode === 'edit' ? 'bg-white' : 'bg-gray-50 cursor-not-allowed' }}">
                            @if($mode === 'create')
                                <p class="mt-1 text-xs text-gray-500">Auto-generated from title</p>
                            @endif
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

                        <!-- Category Selection with New Category Option -->
                        <div class="lg:col-span-2">
                            <label class="block text-sm font-semibold text-gray-700 mb-3">
                                Category <span class="text-red-500">*</span>
                            </label>

                            @if(!$showNewCategoryForm)
                                <div class="space-y-3">
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
                                    <button type="button" wire:click="toggleNewCategoryForm"
                                            class="w-full flex items-center justify-center px-4 py-2 border border-dashed border-purple-300 rounded-xl text-sm font-medium text-purple-600 hover:border-purple-400 hover:text-purple-700 transition-colors">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                        </svg>
                                        Add New Category
                                    </button>
                                </div>
                            @else
                                <div class="space-y-4 p-4 bg-purple-50 rounded-xl border border-purple-200">
                                    <div class="flex items-center justify-between">
                                        <h4 class="font-medium text-purple-900">Add New Category</h4>
                                        <button type="button" wire:click="toggleNewCategoryForm"
                                                class="text-purple-600 hover:text-purple-800">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                            </svg>
                                        </button>
                                    </div>
                                    <div class="grid grid-cols-1 gap-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Category Name</label>
                                            <input type="text" wire:model="newCategoryName"
                                                   placeholder="Enter category name"
                                                   class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                                            @error('newCategoryName') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Category Description (Optional)</label>
                                            <textarea wire:model="newCategoryDescription" rows="3"
                                                      placeholder="Brief description of this category..."
                                                      class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 resize-none"></textarea>
                                            @error('newCategoryDescription') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                                        </div>
                                        <button type="button" wire:click="createNewCategory"
                                                class="w-full bg-purple-600 text-white py-2 px-4 rounded-lg hover:bg-purple-700 transition-colors">
                                            Create Category
                                        </button>
                                    </div>
                                </div>
                            @endif
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

                @include('livewire.books.partials.book-table-of-contents')

                @include('livewire.books.partials.media-files')

                @include('livewire.books.partials.publishing-settings')

                <!-- Final Actions -->
                <div class="flex items-center justify-end space-x-4 pt-8 border-t border-gray-200">
                    <button type="button" wire:click="cancel"
                            class="px-6 py-3 border border-gray-300 rounded-xl text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                        Cancel
                    </button>
                    <button type="submit"
                            class="px-8 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 border border-transparent rounded-xl text-sm font-medium text-white hover:from-blue-700 hover:to-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all shadow-lg">
                        @if($mode === 'edit')
                            <svg class="w-4 h-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                            {{ $this->submitButtonText }}
                        @else
                            <svg class="w-4 h-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                            {{ $this->submitButtonText }}
                        @endif
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
