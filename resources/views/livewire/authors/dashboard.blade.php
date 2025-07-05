<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div x-data="{ darkMode: localStorage.getItem('darkMode') === 'true' }"
         x-init="$watch('darkMode', value => localStorage.setItem('darkMode', value))"
         :class="{ 'dark': darkMode }"
         class="min-h-screen transition-colors duration-300 bg-gray-50 dark:bg-gray-900">

        <!-- Enhanced Header with Dark Mode Toggle -->
        <div class="bg-white dark:bg-gray-800 shadow-sm border-b border-gray-200 dark:border-gray-700">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center py-6">
                    <div class="flex items-center space-x-4">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-purple-600 rounded-lg flex items-center justify-center">
                                <i class="fas fa-feather-alt text-white text-lg"></i>
                            </div>
                            <div class="ml-3">
                                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Author Dashboard</h1>
                                <p class="text-sm text-gray-600 dark:text-gray-300">Manage your books and track performance</p>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center space-x-4">
                        <!-- Dark Mode Toggle -->
                        <button @click="darkMode = !darkMode"
                                class="p-2 rounded-lg bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
                            <i x-show="!darkMode" class="fas fa-moon text-gray-600 dark:text-gray-300"></i>
                            <i x-show="darkMode" class="fas fa-sun text-yellow-500"></i>
                        </button>

                        <!-- Enhanced Add Book Button -->
                        <button wire:click="openBookModal"
                                class="relative px-6 py-3 bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white rounded-lg font-medium shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-200">
                            <i class="fas fa-plus mr-2"></i>
                            <span>Add New Book</span>
                            <div class="absolute inset-0 bg-white opacity-20 rounded-lg blur-xl -z-10"></div>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Enhanced Stats Cards with Animation -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6 mb-8">
                <!-- Total Books -->
                <div class="group bg-white dark:bg-gray-800 rounded-xl shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-300 overflow-hidden">
                    <div class="p-6 relative">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="p-3 rounded-full bg-gradient-to-br from-blue-500 to-blue-600 shadow-lg">
                                    <i class="fas fa-book text-white text-xl"></i>
                                </div>
                                <div class="ml-4">
                                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $totalBooks }}</h3>
                                    <p class="text-sm text-gray-600 dark:text-gray-300">Total Books</p>
                                </div>
                            </div>
                        </div>
                        <div class="absolute top-0 right-0 w-20 h-20 bg-gradient-to-br from-blue-500/10 to-blue-600/10 rounded-full transform translate-x-8 -translate-y-8"></div>
                    </div>
                </div>

                <!-- Published Books -->
                <div class="group bg-white dark:bg-gray-800 rounded-xl shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-300 overflow-hidden">
                    <div class="p-6 relative">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="p-3 rounded-full bg-gradient-to-br from-green-500 to-green-600 shadow-lg">
                                    <i class="fas fa-check-circle text-white text-xl"></i>
                                </div>
                                <div class="ml-4">
                                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $publishedBooks }}</h3>
                                    <p class="text-sm text-gray-600 dark:text-gray-300">Published</p>
                                </div>
                            </div>
                        </div>
                        <div class="absolute top-0 right-0 w-20 h-20 bg-gradient-to-br from-green-500/10 to-green-600/10 rounded-full transform translate-x-8 -translate-y-8"></div>
                    </div>
                </div>

                <!-- Draft Books -->
                <div class="group bg-white dark:bg-gray-800 rounded-xl shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-300 overflow-hidden">
                    <div class="p-6 relative">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="p-3 rounded-full bg-gradient-to-br from-yellow-500 to-yellow-600 shadow-lg">
                                    <i class="fas fa-edit text-white text-xl"></i>
                                </div>
                                <div class="ml-4">
                                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $draftBooks }}</h3>
                                    <p class="text-sm text-gray-600 dark:text-gray-300">Drafts</p>
                                </div>
                            </div>
                        </div>
                        <div class="absolute top-0 right-0 w-20 h-20 bg-gradient-to-br from-yellow-500/10 to-yellow-600/10 rounded-full transform translate-x-8 -translate-y-8"></div>
                    </div>
                </div>

                <!-- Subscriptions -->
                <div class="group bg-white dark:bg-gray-800 rounded-xl shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-300 overflow-hidden">
                    <div class="p-6 relative">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="p-3 rounded-full bg-gradient-to-br from-purple-500 to-purple-600 shadow-lg">
                                    <i class="fas fa-users text-white text-xl"></i>
                                </div>
                                <div class="ml-4">
                                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $totalSubscriptions }}</h3>
                                    <p class="text-sm text-gray-600 dark:text-gray-300">Subscriptions</p>
                                </div>
                            </div>
                        </div>
                        <div class="absolute top-0 right-0 w-20 h-20 bg-gradient-to-br from-purple-500/10 to-purple-600/10 rounded-full transform translate-x-8 -translate-y-8"></div>
                    </div>
                </div>

                <!-- Revenue -->
                <div class="group bg-white dark:bg-gray-800 rounded-xl shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-300 overflow-hidden">
                    <div class="p-6 relative">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="p-3 rounded-full bg-gradient-to-br from-emerald-500 to-emerald-600 shadow-lg">
                                    <i class="fas fa-dollar-sign text-white text-xl"></i>
                                </div>
                                <div class="ml-4">
                                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white">GHS {{ number_format($totalRevenue, 2) }}</h3>
                                    <p class="text-sm text-gray-600 dark:text-gray-300">Revenue</p>
                                </div>
                            </div>
                        </div>
                        <div class="absolute top-0 right-0 w-20 h-20 bg-gradient-to-br from-emerald-500/10 to-emerald-600/10 rounded-full transform translate-x-8 -translate-y-8"></div>
                    </div>
                </div>
            </div>

            <!-- Enhanced Flash Messages -->
            @if (session()->has('success'))
                <div x-data="{ show: true }" x-show="show" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform translate-y-2" x-transition:enter-end="opacity-100 transform translate-y-0" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 transform translate-y-0" x-transition:leave-end="opacity-0 transform translate-y-2" class="mb-6 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-300 px-6 py-4 rounded-lg shadow-lg" role="alert">
                    <div class="flex items-center">
                        <i class="fas fa-check-circle text-green-500 mr-3"></i>
                        <span class="font-medium">{{ session('success') }}</span>
                        <button @click="show = false" class="ml-auto text-green-500 hover:text-green-700">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
            @endif

            @if (session()->has('error'))
                <div x-data="{ show: true }" x-show="show" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform translate-y-2" x-transition:enter-end="opacity-100 transform translate-y-0" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 transform translate-y-0" x-transition:leave-end="opacity-0 transform translate-y-2" class="mb-6 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-300 px-6 py-4 rounded-lg shadow-lg" role="alert">
                    <div class="flex items-center">
                        <i class="fas fa-exclamation-circle text-red-500 mr-3"></i>
                        <span class="font-medium">{{ session('error') }}</span>
                        <button @click="show = false" class="ml-auto text-red-500 hover:text-red-700">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
            @endif

            <!-- Enhanced Filters and Search -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg mb-6 overflow-hidden">
                <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                        <div class="flex-1 max-w-md">
                            <div class="relative">
                                <input type="text"
                                       wire:model.live="search"
                                       placeholder="Search books..."
                                       class="w-full pl-12 pr-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 transition-colors">
                                <i class="fas fa-search absolute left-4 top-4 text-gray-400"></i>
                            </div>
                        </div>

                        <div class="flex gap-4">
                            <select wire:model.live="categoryFilter"
                                    class="px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white transition-colors">
                                <option value="">All Categories</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>

                            <select wire:model.live="statusFilter"
                                    class="px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white transition-colors">
                                <option value="">All Status</option>
                                <option value="published">Published</option>
                                <option value="draft">Draft</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Enhanced Books Table -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    <button wire:click="sortBy('title')" class="flex items-center space-x-1 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
                                        <span>Book</span>
                                        <i class="fas fa-sort text-gray-400"></i>
                                    </button>
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Category</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    <button wire:click="sortBy('annual_subscription_fee')" class="flex items-center space-x-1 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
                                        <span>Price</span>
                                        <i class="fas fa-sort text-gray-400"></i>
                                    </button>
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Subscriptions</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Borrowings</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    <button wire:click="sortBy('created_at')" class="flex items-center space-x-1 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
                                        <span>Created</span>
                                        <i class="fas fa-sort text-gray-400"></i>
                                    </button>
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @if($books && $books->count() > 0)
                                @foreach($books as $book)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 h-16 w-12">
                                                    <img class="h-16 w-12 rounded-lg object-cover shadow-md"
                                                         src="{{ $book->cover_image }}"
                                                         alt="{{ $book->title }}">
                                                </div>
                                                <div class="ml-4">
                                                    <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $book->title }}</div>
                                                    <div class="text-sm text-gray-500 dark:text-gray-400">
                                                        @if($book->edition)
                                                            Edition: {{ $book->edition }}
                                                        @endif
                                                        @if($book->publisher)
                                                            | {{ $book->publisher }}
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-3 py-1 text-xs font-medium rounded-full bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200">
                                                {{ $book->bookCategory->name ?? 'Uncategorized' }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($book->cover_image_path)
                                                <span class="px-3 py-1 text-xs font-medium rounded-full bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200">
                                                    <i class="fas fa-check-circle mr-1"></i>Published
                                                </span>
                                            @else
                                                <span class="px-3 py-1 text-xs font-medium rounded-full bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-200">
                                                    <i class="fas fa-edit mr-1"></i>Draft
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                            @if($book->is_free)
                                                <span class="text-green-600 dark:text-green-400 font-medium">Free</span>
                                            @else
                                                <span class="font-medium">{{ $book->formatted_subscription_fee }}</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                            <div class="flex items-center">
                                                <i class="fas fa-users text-gray-400 mr-2"></i>
                                                {{ $book->subscriptions_count }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                            <div class="flex items-center">
                                                <i class="fas fa-book-open text-gray-400 mr-2"></i>
                                                {{ $book->borrowings_count }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                            {{ $book->created_at->format('M d, Y') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <div class="flex space-x-2">
                                                <button wire:click="openBookModal({{ $book->id }})"
                                                        class="text-blue-600 dark:text-blue-400 hover:text-blue-900 dark:hover:text-blue-300 p-2 rounded-lg hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-colors">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button wire:click="confirmDelete({{ $book->id }})"
                                                        class="text-red-600 dark:text-red-400 hover:text-red-900 dark:hover:text-red-300 p-2 rounded-lg hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="8" class="px-6 py-16 text-center text-gray-500 dark:text-gray-400">
                                        <div class="flex flex-col items-center">
                                            <div class="w-20 h-20 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mb-4">
                                                <i class="fas fa-book text-4xl text-gray-400"></i>
                                            </div>
                                            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">No books found</h3>
                                            <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Get started by adding your first book</p>
                                            <button wire:click="openBookModal"
                                                    class="px-6 py-3 bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white rounded-lg font-medium shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-200">
                                                <i class="fas fa-plus mr-2"></i>Add New Book
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>

                @if($books && $books->hasPages())
                    <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700">
                        {{ $books->links() }}
                    </div>
                @endif
            </div>
        </div>

        <!-- Enhanced Book Modal -->
        @if($showBookModal)
            <div class="fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center z-50 p-4"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0">
                <div class="bg-white dark:bg-gray-800 rounded-2xl max-w-4xl w-full max-h-[90vh] overflow-y-auto shadow-2xl"
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 transform scale-90"
                     x-transition:enter-end="opacity-100 transform scale-100"
                     x-transition:leave="transition ease-in duration-200"
                     x-transition:leave-start="opacity-100 transform scale-100"
                     x-transition:leave-end="opacity-0 transform scale-90">

                    <!-- Modal Header -->
                    <div class="p-6 border-b border-gray-200 dark:border-gray-700 bg-gradient-to-r from-blue-600 to-purple-600 text-white rounded-t-2xl">
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
                            <button wire:click="closeBookModal"
                                    class="text-white hover:text-gray-200 p-2 rounded-lg hover:bg-white/10 transition-colors">
                                <i class="fas fa-times text-xl"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Modal Body -->
                    <form wire:submit="saveBook" class="p-6">
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                            <!-- Title -->
                            <div class="lg:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    <i class="fas fa-heading mr-2 text-blue-500"></i>Book Title *
                                </label>
                                <input type="text"
                                       wire:model="title"
                                       class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white transition-colors"
                                       placeholder="Enter book title">
                                @error('title') <span class="text-red-500 text-sm mt-1 block"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</span> @enderror
                            </div>

                            <!-- Category -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    <i class="fas fa-tags mr-2 text-purple-500"></i>Category *
                                </label>
                                <select wire:model="book_category_id"
                                        class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white transition-colors">
                                    <option value="">Select Category</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                    @endforeach
                                </select>
                                @error('book_category_id') <span class="text-red-500 text-sm mt-1 block"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</span> @enderror
                            </div>

                            <!-- Edition -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    <i class="fas fa-bookmark mr-2 text-green-500"></i>Edition
                                </label>
                                <input type="text"
                                       wire:model="edition"
                                       class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white transition-colors"
                                       placeholder="e.g., 1st Edition">
                                @error('edition') <span class="text-red-500 text-sm mt-1 block"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</span> @enderror
                            </div>

                            <!-- Publisher -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    <i class="fas fa-building mr-2 text-indigo-500"></i>Publisher
                                </label>
                                <input type="text"
                                       wire:model="publisher"
                                       class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white transition-colors"
                                       placeholder="Publisher name">
                                @error('publisher') <span class="text-red-500 text-sm mt-1 block"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</span> @enderror
                            </div>

                            <!-- Pages -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    <i class="fas fa-file-alt mr-2 text-orange-500"></i>Pages
                                </label>
                                <input type="number"
                                       wire:model="pages"
                                       class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white transition-colors"
                                       placeholder="Number of pages">
                                @error('pages') <span class="text-red-500 text-sm mt-1 block"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</span> @enderror
                            </div>

                            <!-- Subscription Fee -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    <i class="fas fa-dollar-sign mr-2 text-emerald-500"></i>Annual Subscription Fee (GHS)
                                </label>
                                <input type="number"
                                       step="0.01"
                                       wire:model="annual_subscription_fee"
                                       class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white transition-colors"
                                       placeholder="0.00">
                                @error('annual_subscription_fee') <span class="text-red-500 text-sm mt-1 block"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</span> @enderror
                            </div>

                            <!-- Book Format -->
                            <div class="lg:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
                                    <i class="fas fa-layer-group mr-2 text-cyan-500"></i>Book Format
                                </label>
                                <div class="flex flex-wrap gap-4">
                                    <label class="flex items-center p-3 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors cursor-pointer">
                                        <input type="checkbox"
                                               wire:model="has_hardcopy"
                                               class="rounded border-gray-300 dark:border-gray-600 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                        <span class="ml-3 text-sm font-medium text-gray-700 dark:text-gray-300">
                                            <i class="fas fa-book mr-2"></i>Has Hardcopy
                                        </span>
                                    </label>
                                    <label class="flex items-center p-3 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors cursor-pointer">
                                        <input type="checkbox"
                                               wire:model="has_softcopy"
                                               class="rounded border-gray-300 dark:border-gray-600 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                        <span class="ml-3 text-sm font-medium text-gray-700 dark:text-gray-300">
                                            <i class="fas fa-file-pdf mr-2"></i>Has Softcopy
                                        </span>
                                    </label>
                                </div>
                            </div>

                            <!-- File Uploads -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    <i class="fas fa-image mr-2 text-pink-500"></i>Cover Image
                                </label>
                                <div class="border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg p-6 text-center hover:border-blue-500 dark:hover:border-blue-400 transition-colors">
                                    <input type="file"
                                           wire:model="cover_image"
                                           accept="image/*"
                                           class="hidden"
                                           id="cover-image-upload">
                                    <label for="cover-image-upload" class="cursor-pointer">
                                        <i class="fas fa-cloud-upload-alt text-3xl text-gray-400 mb-2"></i>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">Click to upload cover image</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-500">PNG, JPG up to 2MB</p>
                                    </label>
                                </div>
                                @error('cover_image') <span class="text-red-500 text-sm mt-1 block"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    <i class="fas fa-file-pdf mr-2 text-red-500"></i>PDF File
                                </label>
                                <div class="border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg p-6 text-center hover:border-blue-500 dark:hover:border-blue-400 transition-colors">
                                    <input type="file"
                                           wire:model="pdf_file"
                                           accept=".pdf"
                                           class="hidden"
                                           id="pdf-file-upload">
                                    <label for="pdf-file-upload" class="cursor-pointer">
                                        <i class="fas fa-file-pdf text-3xl text-gray-400 mb-2"></i>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">Click to upload PDF file</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-500">PDF up to 10MB</p>
                                    </label>
                                </div>
                                @error('pdf_file') <span class="text-red-500 text-sm mt-1 block"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</span> @enderror
                            </div>

                            <!-- Additional Information -->
                            <div class="lg:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    <i class="fas fa-info-circle mr-2 text-blue-500"></i>Additional Information
                                </label>
                                <textarea wire:model="additional_info"
                                          rows="4"
                                          class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white transition-colors resize-none"
                                          placeholder="Any additional information about the book..."></textarea>
                                @error('additional_info') <span class="text-red-500 text-sm mt-1 block"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</span> @enderror
                            </div>

                            <!-- Subscription Conditions -->
                            <div class="lg:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    <i class="fas fa-scroll mr-2 text-amber-500"></i>Subscription Conditions
                                </label>
                                <textarea wire:model="subscription_conditions"
                                          rows="4"
                                          class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white transition-colors resize-none"
                                          placeholder="Enter custom subscription conditions or leave blank for defaults..."></textarea>
                                @error('subscription_conditions') <span class="text-red-500 text-sm mt-1 block"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <!-- Modal Footer -->
                        <div class="flex justify-end space-x-4 mt-8 pt-6 border-t border-gray-200 dark:border-gray-700">
                            <button type="button"
                                    wire:click="closeBookModal"
                                    class="px-6 py-3 text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-lg font-medium transition-colors">
                                <i class="fas fa-times mr-2"></i>Cancel
                            </button>
                            <button type="submit"
                                    class="px-6 py-3 bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white rounded-lg font-medium shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-200">
                                <i class="fas fa-save mr-2"></i>
                                {{ $editingBook ? 'Update Book' : 'Create Book' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        @endif

        <!-- Enhanced Delete Confirmation Modal -->
        @if($showDeleteModal)
            <div class="fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center z-50 p-4">
                <div class="bg-white dark:bg-gray-800 rounded-2xl max-w-md w-full shadow-2xl">
                    <div class="p-6">
                        <div class="flex items-center mb-6">
                            <div class="flex-shrink-0 w-12 h-12 bg-red-100 dark:bg-red-900/20 rounded-full flex items-center justify-center">
                                <i class="fas fa-exclamation-triangle text-red-600 dark:text-red-400 text-xl"></i>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Delete Book</h3>
                                <p class="text-sm text-gray-600 dark:text-gray-400">This action cannot be undone</p>
                            </div>
                        </div>

                        @if($bookToDelete)
                            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 mb-6">
                                <div class="flex items-center">
                                    <img class="w-12 h-16 rounded object-cover" src="{{ $bookToDelete->cover_image }}" alt="{{ $bookToDelete->title }}">
                                    <div class="ml-3">
                                        <p class="font-medium text-gray-900 dark:text-white">{{ $bookToDelete->title }}</p>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ $bookToDelete->bookCategory->name ?? 'Uncategorized' }}</p>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <div class="flex justify-end space-x-4">
                            <button wire:click="cancelDelete"
                                    class="px-4 py-2 text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-lg font-medium transition-colors">
                                <i class="fas fa-times mr-2"></i>Cancel
                            </button>
                            <button wire:click="deleteBook"
                                    class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg font-medium transition-colors">
                                <i class="fas fa-trash mr-2"></i>Delete Book
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <script>
        // Enhanced interactions
        document.addEventListener('DOMContentLoaded', function() {
            // Auto-hide flash messages
            setTimeout(() => {
                document.querySelectorAll('[role="alert"]').forEach(el => {
                    el.style.transition = 'opacity 0.5s';
                    el.style.opacity = '0';
                    setTimeout(() => el.remove(), 500);
                });
            }, 5000);
        });
    </script>
