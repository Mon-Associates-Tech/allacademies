<div class="min-h-screen bg-gray-50 rounded-xl">
    <!-- Clean Header -->
    <div class="bg-white shadow-sm rounded-t-xl  border-b border-gray-200 mb-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-6">
                <div>
                    <h1 class="text-2xl font-semibold text-gray-900">Book Management</h1>
                    <p class="mt-1 text-sm text-gray-500">Manage your library's book collection</p>
                </div>
                <div class="">

                </div>

                    <div class="">
                        <x-link.white class="mr-2" :to="route('books.index')">Browse Books</x-link.white>
                        <a href="{{route('admin.books.create')}}"
                                class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md text-sm font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                            Add Book
                        </a>
                    </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto mb-4 px-4 pb-4 sm:px-6 lg:px-8">
        <!-- Notifications -->
        @if (session()->has('message'))
            <div class="mb-6 bg-green-50 border border-green-200 rounded-md p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-green-800">{!! session('message') !!}</p>
                    </div>
                </div>
            </div>
        @endif

        @if (session()->has('error'))
            <div class="mb-6 bg-red-50 border border-red-200 rounded-md p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-red-800">{!! session('error') !!}</p>
                    </div>
                </div>
            </div>
        @endif

        @if($showForm)
            <div class="mb-8 bg-white shadow-lg rounded-xl border border-gray-100 overflow-hidden">
                <!-- Form Header with Progress -->
                <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-4">
                    <div class="flex justify-between items-center">
                        <div class="flex items-center space-x-3">
                            <div class="p-2 bg-white/10 rounded-lg">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                </svg>
                            </div>
                            <div>
                                <h2 class="text-xl font-semibold text-white">
                                    {{ $isEditing ? 'Edit Book' : 'Add New Book' }}
                                </h2>
                                <p class="text-blue-100 text-sm">
                                    {{ $isEditing ? 'Update book information and settings' : 'Fill in the details to add a new book to your library' }}
                                </p>
                            </div>
                        </div>
                        <button wire:click="hideForm" class="text-white/80 hover:text-white p-2 hover:bg-white/10 rounded-lg transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                </div>

                <form wire:submit.prevent="{{ $isEditing ? 'update' : 'create' }}" class="relative">
                    <!-- Form Content -->
                    <div class="p-8">
                        <!-- Step 1: Basic Information -->
                        <div class="mb-10">
                            <div class="flex items-center mb-6">
                                <div class="flex items-center justify-center w-8 h-8 bg-blue-600 text-white rounded-full text-sm font-medium mr-3">1</div>
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900">Basic Information</h3>
                                    <p class="text-sm text-gray-500">Enter the fundamental details about the book</p>
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
                                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                            </svg>
                                        </div>
                                    </div>
                                    @error('title') <p class="mt-2 text-sm text-red-600 flex items-center"><svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>{{ $message }}</p> @enderror
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
                                                <option value="{{ $author->id }}">{{ $author->user?->name }}</option>
                                            @endforeach
                                        </select>
                                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                            </svg>
                                        </div>
                                    </div>
                                    @error('authorId') <p class="mt-2 text-sm text-red-600 flex items-center"><svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Category <span class="text-red-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <select wire:model="bookCategoryId"
                                                class="block w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 appearance-none bg-white">
                                            <option value="">Select a category</option>
                                            @foreach($bookCategories as $category)
                                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                                            @endforeach
                                        </select>
                                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                            </svg>
                                        </div>
                                    </div>
                                    @error('bookCategoryId') <p class="mt-2 text-sm text-red-600 flex items-center"><svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Edition</label>
                                    <input type="text" wire:model="edition"
                                           placeholder="e.g., 1st Edition, Revised Edition"
                                           class="block w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    @error('edition') <p class="mt-2 text-sm text-red-600 flex items-center"><svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Publisher</label>
                                    <input type="text" wire:model="publisher"
                                           placeholder="Publishing house name"
                                           class="block w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    @error('publisher') <p class="mt-2 text-sm text-red-600 flex items-center"><svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Number of Pages</label>
                                    <input type="number" wire:model="pages" min="1"
                                           placeholder="Total pages"
                                           class="block w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    @error('pages') <p class="mt-2 text-sm text-red-600 flex items-center"><svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Annual Subscription Fee
                                        <span class="text-xs bg-gray-100 text-gray-600 px-2 py-1 rounded-full ml-1">GHS</span>
                                    </label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <span class="text-gray-500 text-sm">₵</span>
                                        </div>
                                        <input type="number" wire:model="annualSubscriptionFee" step="0.01" min="0"
                                               placeholder="0.00"
                                               class="block w-full pl-8 pr-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    </div>
                                    <p class="mt-1 text-xs text-gray-500 flex items-center">
                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                                        </svg>
                                        Enter 0 to make this book free for all users
                                    </p>
                                    @error('annualSubscriptionFee') <p class="mt-2 text-sm text-red-600 flex items-center"><svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>{{ $message }}</p> @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Step 2: Book Formats -->
                        <div class="mb-10">
                            <div class="flex items-center mb-6">
                                <div class="flex items-center justify-center w-8 h-8 bg-blue-600 text-white rounded-full text-sm font-medium mr-3">2</div>
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900">Available Formats</h3>
                                    <p class="text-sm text-gray-500">Select the formats in which this book is available</p>
                                </div>
                            </div>

                            <div class="ml-11">
                                <div class="bg-gray-50 rounded-lg p-6">
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <label class="relative flex items-start p-4 bg-white border-2 border-gray-200 rounded-lg cursor-pointer hover:border-blue-300 transition-colors">
                                            <input type="checkbox" wire:model="hasHardcopy"
                                                   class="h-5 w-5 text-blue-600 dark:text-blue-500 focus:ring-blue-500 dark:focus:ring-blue-400 border-gray-300 dark:border-gray-600 dark:bg-gray-700 rounded mt-0.5">
                                            <div class="ml-3">
                                                <div class="flex items-center">
                                                    <svg class="w-5 h-5 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                                    </svg>
                                                    <span class="font-medium text-gray-900">Physical Hardcopy</span>
                                                </div>
                                                <p class="text-sm text-gray-500 mt-1">Traditional printed book available for borrowing</p>
                                            </div>
                                        </label>

                                        <label class="relative flex items-start p-4 bg-white border-2 border-gray-200 rounded-lg cursor-pointer hover:border-blue-300 transition-colors">
                                            <input type="checkbox" wire:model="hasSoftcopy"
                                                   class="h-5 w-5 text-blue-600 dark:text-blue-500 focus:ring-blue-500 dark:focus:ring-blue-400 border-gray-300 dark:border-gray-600 dark:bg-gray-700 rounded mt-0.5">
                                            <div class="ml-3">
                                                <div class="flex items-center">
                                                    <svg class="w-5 h-5 text-purple-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                    </svg>
                                                    <span class="font-medium text-gray-900">Digital Softcopy</span>
                                                </div>
                                                <p class="text-sm text-gray-500 mt-1">PDF version for online reading and downloads</p>
                                            </div>
                                        </label>
                                    </div>
                                </div>
                                @error('hasHardcopy') <p class="mt-2 text-sm text-red-600 flex items-center"><svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <!-- Step 3: Media Files -->
                        <div class="mb-10">
                            <div class="flex items-center mb-6">
                                <div class="flex items-center justify-center w-8 h-8 bg-blue-600 text-white rounded-full text-sm font-medium mr-3">3</div>
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900">Media & Files</h3>
                                    <p class="text-sm text-gray-500">Upload cover image and PDF file</p>
                                </div>
                            </div>

                            <div class="ml-11 grid grid-cols-1 lg:grid-cols-2 gap-8">
                                <!-- Cover Image Upload -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-3">Cover Image</label>
                                    <div class="space-y-4">
                                        <!-- File Input -->
                                        <div class="flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg hover:border-blue-400 transition-colors">
                                            <div class="space-y-1 text-center">
                                                <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                                    <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                                </svg>
                                                <div class="flex text-sm text-gray-600">
                                                    <label class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-blue-500">
                                                        <span>Upload a file</span>
                                                        <input type="file" wire:model="coverImage" accept="image/*" class="sr-only">
                                                    </label>
                                                    <p class="pl-1">or drag and drop</p>
                                                </div>
                                                <p class="text-xs text-gray-500">PNG, JPG, JPEG up to 2MB</p>
                                            </div>
                                        </div>

                                        <!-- Current/Preview Image -->
                                        @if ($isEditing && $existingCover && !$coverImage)
                                            <div class="bg-gray-50 rounded-lg p-4">
                                                <p class="text-sm font-medium text-gray-700 mb-2">Current Cover:</p>
                                                <img src="{{ Storage::url($existingCover) }}" alt="Book Cover" class="h-32 w-24 object-cover rounded-lg border-2 border-gray-200 shadow-sm">
                                            </div>
                                        @endif

                                        @if ($coverImage)
                                            <div class="bg-green-50 rounded-lg p-4 border border-green-200">
                                                <p class="text-sm font-medium text-green-800 mb-2 flex items-center">
                                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                                    </svg>
                                                    New Cover Preview:
                                                </p>
                                                <img src="{{ $coverImage->temporaryUrl() }}" alt="Cover Preview" class="h-32 w-24 object-cover rounded-lg border-2 border-green-300 shadow-sm">
                                            </div>
                                        @endif
                                    </div>
                                    @error('coverImage') <p class="mt-2 text-sm text-red-600 flex items-center"><svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>{{ $message }}</p> @enderror
                                </div>

                                <!-- PDF Upload -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-3">
                                        PDF File
                                        <span class="text-xs bg-purple-100 text-purple-700 px-2 py-1 rounded-full ml-1">Required for softcopy</span>
                                    </label>
                                    <div class="space-y-4">
                                        <!-- File Input -->
                                        <div class="flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg hover:border-purple-400 transition-colors">
                                            <div class="space-y-1 text-center">
                                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 48 48">
                                                    <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m6 0h6m-6 6v6m-6-6v6m6 0v6"/>
                                                </svg>
                                                <div class="flex text-sm text-gray-600">
                                                    <label class="relative cursor-pointer bg-white rounded-md font-medium text-purple-600 hover:text-purple-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-purple-500">
                                                        <span>Upload PDF file</span>
                                                        <input type="file" wire:model="pdfFile" accept=".pdf" class="sr-only">
                                                    </label>
                                                </div>
                                                <p class="text-xs text-gray-500">PDF files only, up to 10MB</p>
                                            </div>
                                        </div>

                                        <!-- Current/New PDF Status -->
                                        @if ($isEditing && $existingPdf && !$pdfFile)
                                            <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                                                <p class="text-sm font-medium text-gray-700 mb-2">Current PDF:</p>
                                                <a href="{{ Storage::url($existingPdf) }}" target="_blank"
                                                   class="inline-flex items-center px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 dark:text-white text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                    </svg>
                                                    View Current PDF
                                                </a>
                                            </div>
                                        @endif

                                        @if ($pdfFile)
                                            <div class="bg-purple-50 rounded-lg p-4 border border-purple-200">
                                                <p class="text-sm font-medium text-purple-800 flex items-center">
                                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                                    </svg>
                                                    New PDF Selected: {{ $pdfFile->getClientOriginalName() }}
                                                </p>
                                            </div>
                                        @endif
                                    </div>
                                    @error('pdfFile') <p class="mt-2 text-sm text-red-600 flex items-center"><svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>{{ $message }}</p> @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Step 4: Additional Details -->
                        <div class="mb-8">
                            <div class="flex items-center mb-6">
                                <div class="flex items-center justify-center w-8 h-8 bg-blue-600 text-white rounded-full text-sm font-medium mr-3">4</div>
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900">Additional Details</h3>
                                    <p class="text-sm text-gray-500">Extra information and subscription conditions</p>
                                </div>
                            </div>

                            <div class="ml-11 space-y-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Additional Information</label>
                                    <textarea wire:model="additionalInfo" rows="4"
                                              placeholder="Enter any additional information about the book, such as description, special notes, or summary..."
                                              class="block w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 resize-none"></textarea>
                                    @error('additionalInfo') <p class="mt-2 text-sm text-red-600 flex items-center"><svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Subscription Conditions</label>
                                    <textarea wire:model="subscriptionConditions" rows="4"
                                              placeholder="Enter specific terms and conditions for subscribing to this book. Leave blank to use default library conditions..."
                                              class="block w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 resize-none"></textarea>
                                    <p class="mt-1 text-xs text-gray-500">These conditions will be shown to users when they subscribe to this book</p>
                                    @error('subscriptionConditions') <p class="mt-2 text-sm text-red-600 flex items-center"><svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>{{ $message }}</p> @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="bg-gray-50 px-8 py-6 border-t border-gray-200 flex flex-col sm:flex-row sm:justify-between sm:items-center space-y-3 sm:space-y-0">
                        <div class="text-sm text-gray-500">
                            <span class="font-medium">Required fields are marked with</span> <span class="text-red-500">*</span>
                        </div>
                        <div class="flex space-x-3">
                            <button type="button" wire:click="hideForm"
                                    class="inline-flex items-center px-6 py-3 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                                Cancel
                            </button>
                            <button type="submit"
                                    class="inline-flex items-center px-6 py-3 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200">
                                @if($isEditing)
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                                    </svg>
                                    Update Book
                                @else
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                    </svg>
                                    Create Book
                                @endif
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        @endif

        <!-- Books List -->
        <div class="bg-white shadow rounded-lg mb-4">
            <!-- Filters -->
            <div class="px-6 py-4 border-b mb-4 border-gray-200">
                <div class="relative mt-2 mb-4">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                    <input type="text" wire:model.debounce.300ms="searchTerm" placeholder="Search books..."
                           class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md text-sm focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div class="flex flex-col w-full lg:flex-row lg:items-center lg:justify-between space-y-4 lg:space-y-0">
                    <div class="flex flex-col w-full sm:flex-row space-y-2 sm:space-y-0 sm:space-x-3">

                        <select wire:model="filterCategory"
                                class="block w-full border-gray-300 rounded-md text-sm focus:ring-blue-500 focus:border-blue-500">
                            <option value="">All Categories</option>
                            @foreach($bookCategories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>

                        <select wire:model="filterAuthor"
                                class="block w-full border-gray-300 rounded-md text-sm focus:ring-blue-500 focus:border-blue-500">
                            <option value="">All Authors</option>
                            @foreach($authors as $author)
                                <option value="{{ $author->id }}">{{ $author->user?->name  }}</option>
                            @endforeach
                        </select>

                        <select wire:model="filterFormat"
                                class="block w-full border-gray-300 rounded-md text-sm focus:ring-blue-500 focus:border-blue-500">
                            <option value="">All Formats</option>
                            <option value="hardcopy">Hardcopy Only</option>
                            <option value="softcopy">Softcopy Only</option>
                            <option value="both">Both Formats</option>
                        </select>

                        <button wire:click="resetFilters"
                                class="px-3 py-2 text-sm text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-md transition-colors">
                            Reset
                        </button>
                    </div>
                </div>
            </div>

            <!-- Table -->
            <div class="overflow-x-auto  mb-4">
                <table class="min-w-full divide-y divide-gray-200 thin-scrollbar">
                    <thead class="bg-gray-50">
                    <tr>
                        <th class="w-8 px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <input type="checkbox" wire:model="selectAll"
                                   class="h-4 w-4 text-blue-600 dark:text-blue-500 focus:ring-blue-500 dark:focus:ring-blue-400 border-gray-300 dark:border-gray-600 dark:bg-gray-700 rounded">
                        </th>
                        <th class="w-80 px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100"
                            wire:click="sortBy('title')">
                            <div class="flex items-center space-x-1">
                                <span>Title</span>
                                @if($sortBy === 'title')
                                    <svg class="w-3 h-3 {{ $sortDirection === 'asc' ? 'transform rotate-180' : '' }}" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"></path>
                                    </svg>
                                @endif
                            </div>
                        </th>
{{--                        <th class="w-32 px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>--}}
                        <th class="w-24 px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Format</th>
                        <th class="w-24 px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fee</th>
                        <th class="w-24 px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="w-32 px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200 thin-scrollbar">
                    @forelse($books as $book)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <input type="checkbox" wire:model="selectedBooks" value="{{ $book->id }}"
                                       class="h-4 w-4 text-blue-600 dark:text-blue-500 focus:ring-blue-500 dark:focus:ring-blue-400 border-gray-300 dark:border-gray-600 dark:bg-gray-700 rounded">
                            </td>
                            <td class="px-6 py-4">
                                <a href="{{route('books.show', $book)}}">
                                    <div class="flex items-start">
                                        <div class="flex-shrink-0 mr-3">
                                            @if($book->cover_image)
                                                <img src="{{$book->cover_image }}" alt="{{ $book->title }}"
                                                     class="h-16 w-12 object-cover overflow-hidden rounded shadow-sm border">
                                            @else
                                                <div class="h-16 w-12 bg-gray-200 rounded flex items-center justify-center border">
                                                    <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                                    </svg>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <div class="text-sm font-semibold text-gray-900 leading-5">{{ $book->title }}</div>
                                            <div class="text-xs text-gray-500 mt-1">
                                                <div class="text-sm font-medium text-gray-900">{{ $book->author_name }}</div>
                                                @if($book->edition)Edition {{ $book->edition }}@endif
                                                @if($book->edition && $book->publisher) • @endif
                                                @if($book->publisher){{ $book->publisher }}@endif
                                            </div>
                                            @if($book->pages)
                                                <div class="text-xs text-gray-400 mt-1">{{ $book->pages }} pages</div>
                                            @endif
                                        </div>
                                    </div>
                                </a>

                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex flex-col space-y-1">
                                    @if($book->has_hardcopy)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">
                                                Hardcopy
                                            </span>
                                    @endif
                                    @if($book->has_softcopy)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-purple-100 text-purple-800">
                                                Softcopy
                                            </span>
                                    @endif
                                </div>
                            </td>
{{--                            <td class="px-6 py-4 whitespace-nowrap">--}}
{{--                                <div class="text-sm font-medium text-gray-900">--}}
{{--                                    @if($book->annual_subscription_fee > 0)--}}
{{--                                        GHS {{ number_format($book->annual_subscription_fee, 2) }}--}}
{{--                                    @else--}}
{{--                                        <span class="text-green-600 font-medium">Free</span>--}}
{{--                                    @endif--}}
{{--                                </div>--}}
{{--                            </td>--}}
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($book->borrowings->count() > 0 || $book->subscriptions->count() > 0)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                            Active
                                        </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                            Available
                                        </span>
                                @endif
                            </td>
                            <td class="px- py-2 whitespace-nowrap text-center">
                                @php
                                    // Handle legacy boolean/integer status values
                                    $statusEnum = App\Enums\PublishingStatus::fromLegacy($book->status);
                                    $isPublished = $statusEnum === App\Enums\PublishingStatus::PUBLISHED;
                                @endphp

                                <div class="flex flex-col items-center space-y-1">
                                    <!-- Toggle Switch -->
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox"
                                               wire:click="toggleBookStatus({{ $book->id }})"
                                               {{ $isPublished ? 'checked' : '' }}
                                               class="sr-only peer">
                                        <div class="relative w-10 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-blue-500 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-green-500 hover:peer-checked:bg-green-600"></div>
                                    </label>

                                    <!-- Status Text -->
                                    <span class="text-xs font-medium {{ $isPublished ? 'text-green-600' : 'text-yellow-600' }}">
                                        {{ $statusEnum->getLabel() }}
                                    </span>

                                    <!-- Debug info (remove in production) -->
                                    @if(config('app.debug'))
                                        <span class="text-xs text-gray-400">
                                            Raw: {{ $book->status }}
                                        </span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                <div class="flex items-center justify-end space-x-2">
                                    <a href="{{route('admin.books.edit', ['book' => $book])}}" wire:click="edit({{ $book->id }})"
                                            class="text-blue-600 hover:text-blue-900 p-1.5 hover:bg-blue-50 rounded transition-colors"
                                            title="Edit">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </a>
                                    @if($book->has_softcopy && $book->pdf_file)
                                        <a href="{{ Storage::url($book->pdf_file) }}" target="_blank"
                                           class="text-green-600 hover:text-green-900 p-1.5 hover:bg-green-50 rounded transition-colors"
                                           title="View PDF">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                            </svg>
                                        </a>
                                    @endif
                                    <button wire:click="delete({{ $book->id }})"
                                            onclick="return confirm('Are you sure you want to delete this book?')"
                                            class="text-red-600 hover:text-red-900 p-1.5 hover:bg-red-50 rounded transition-colors"
                                            title="Delete">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center">
                                    <svg class="w-12 h-12 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                    </svg>
                                    <h3 class="text-sm font-medium text-gray-900 mb-1">No books found</h3>
                                    <p class="text-sm text-gray-500">Try adjusting your search or filter criteria</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($books->hasPages())
                <div class="bg-white px-6 py-3 border-t border-gray-200">
                    {{ $books->links() }}
                </div>
            @endif

            <!-- Bulk Actions -->
            @if(count($selectedBooks) > 0)
                <div class="bg-blue-50 border-t border-blue-200 px-6 py-3">
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-blue-900">{{ count($selectedBooks) }} book(s) selected</span>
                        <button wire:click="bulkDelete"
                                onclick="return confirm('Are you sure you want to delete selected books?')"
                                class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                            Delete Selected
                        </button>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
