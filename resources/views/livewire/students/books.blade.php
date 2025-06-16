@php use App\Models\BookSubscription; @endphp
<div class="space-y-6">
    <!-- Header -->
    <div class="sm:flex sm:items-center sm:justify-between">
        <div>
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white">My Books</h2>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Manage your book subscriptions and borrowings</p>
        </div>
    </div>

    <!-- Flash Messages -->
    @if (session()->has('success'))
        <div
            class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded relative dark:bg-green-900 dark:border-green-700 dark:text-green-200"
            role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif

    @if (session()->has('error'))
        <div
            class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded relative dark:bg-red-900 dark:border-red-700 dark:text-red-200"
            role="alert">
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
    @endif

    <!-- Tabs -->
    <div class="border-b border-gray-200 dark:border-gray-700">
        <nav class="-mb-px flex space-x-8" aria-label="Tabs">
            <button wire:click="changeTab('available')"
                    class="@if($bookTab === 'available') border-indigo-500 text-indigo-600 dark:text-indigo-400 @else border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300 @endif whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm">
                Available Books
            </button>
            <button wire:click="changeTab('subscribed')"
                    class="@if($bookTab === 'subscribed') border-indigo-500 text-indigo-600 dark:text-indigo-400 @else border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300 @endif whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm">
                Subscribed Books
            </button>
            <button wire:click="changeTab('borrowed')"
                    class="@if($bookTab === 'borrowed') border-indigo-500 text-indigo-600 dark:text-indigo-400 @else border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300 @endif whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm">
                Borrowed Books
            </button>
        </nav>
    </div>

    <!-- Filters -->
    <!-- Filters -->
    @if($bookTab === 'available')
        <div class="bg-white dark:bg-gray-800 p-4 rounded-lg border border-gray-200 dark:border-gray-700">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label for="search"
                           class="block text-sm font-medium text-gray-700 dark:text-gray-300">Search</label>
                    <input wire:model.debounce.300ms="search"
                           type="text"
                           id="search"
                           wire:change="$refresh"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white sm:text-sm"
                           placeholder="Search books..."
                           wire:loading.class="opacity-50"
                           wire:loading.attr="disabled">
                </div>
                <div>
                    <label for="category"
                           class="block text-sm font-medium text-gray-700 dark:text-gray-300">Category</label>
                    <select wire:model.live="selectedCategory"
                            id="category"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white sm:text-sm"
                            wire:loading.class="opacity-50"
                            wire:loading.attr="disabled">
                        <option value="">All Categories</option>
                        @foreach($categories as $id => $name)
                            <option value="{{ $id }}">{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="format"
                           class="block text-sm font-medium text-gray-700 dark:text-gray-300">Format</label>
                    <select wire:model.live="selectedFormat"
                            id="format"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white sm:text-sm"
                            wire:loading.class="opacity-50"
                            wire:loading.attr="disabled">
                        <option value="">All Formats</option>
                        <option value="softcopy">Softcopy</option>
                        <option value="hardcopy">Hardcopy</option>
                    </select>
                </div>

                <div>
                    <label for="price" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Price</label>
                    <select wire:model.live="selectedPrice"
                            id="price"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white sm:text-sm"
                            wire:loading.class="opacity-50"
                            wire:loading.attr="disabled">
                        <option value="">All Prices</option>
                        <option value="free">Free</option>
                        <option value="paid">Paid</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Loading Indicator -->
        <div wire:loading class="flex justify-center">
            <div class="inline-flex items-center px-4 py-2 font-semibold leading-6 text-sm text-indigo-500">
                <svg class="animate-spin -ml-1 mr-3 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none"
                     viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor"
                          d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Loading...
            </div>
        </div>
    @endif

    <!-- Books Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6" wire:key="books-grid">
        @forelse($books as $book)
            @php
//                $book = $bookTab === 'available' ? $item : $item->book;
            @endphp
                <!-- Add this in your book card template, typically near the top of each book card -->
            @foreach($books as $book)
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow duration-300">
                    <!-- Book Status Badge -->
                    @php
                        $bookStatus = $this->getBookStatus($book->id);
                    @endphp

                    @if($bookStatus)
                        <div class="absolute top-2 right-2 z-10">
        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $bookStatus['class'] }}">
            @if($bookStatus['type'] === 'free')
                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                </svg>
            @elseif($bookStatus['type'] === 'subscribed')
                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                </svg>
            @elseif($bookStatus['type'] === 'group_subscribed')
                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3z"></path>
                </svg>
            @elseif($bookStatus['type'] === 'pending')
                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                </svg>
            @endif
            {{ $bookStatus['label'] }}
        </span>
                        </div>
                    @endif

                    <!-- Book Cover Image -->
                    <div class="relative">
                        <img src="{{ $book->cover_image }}" alt="{{ $book->title }}" class="w-full h-48 object-cover">
                    </div>

                    <!-- Book Content -->
                    <div class="p-4">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">{{ $book->title }}</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">by {{ $book->author->name ?? 'Unknown Author' }}</p>

                        <!-- Category and Format badges -->
                        <div class="flex flex-wrap gap-2 mb-3">
                            @if($book->bookCategory)
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200">
                {{ $book->bookCategory->name }}
            </span>
                            @endif

                            @if($book->has_hardcopy)
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-800 dark:text-blue-100">
                Hardcopy
            </span>
                            @endif

                            @if($book->has_softcopy)
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100">
                Softcopy
            </span>
                            @endif
                        </div>

                        <!-- Action Buttons -->
                        <div class="space-y-2">
                            @if($book->has_softcopy)
                                @php
                                    $bookStatus = $this->getBookStatus($book->id);
                                    $hasAccess = $this->hasBookAccess($book->id);
                                @endphp

                                @if($hasAccess)
                                    <!-- User has access - show Read button -->
                                    <button wire:click="openPdfReader({{ $book->id }})"
                                            class="w-full inline-flex justify-center items-center px-4 py-2.5 border border-transparent text-sm font-medium rounded-lg text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-all duration-300 transform hover:scale-[1.02]">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C20.832 18.477 19.246 18 17.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                        </svg>
                                        Read Now
                                    </button>
                                @else
                                    @php
                                        $subscription = BookSubscription::where('student_id', auth()->user()->student->id ?? null)
                                            ->where('book_id', $book->id)
                                            ->where('status', 'pending_payment')
                                            ->first();
                                    @endphp

                                    @if($subscription)
                                        <!-- Pending payment - show complete payment button -->
                                        <button wire:click="showSubscriptionDetails({{ $subscription->id }})"
                                                class="w-full inline-flex justify-center items-center px-4 py-2.5 border border-transparent text-sm font-medium rounded-lg text-white bg-orange-600 hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 transition-all duration-300 transform hover:scale-[1.02]">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                            </svg>
                                            Complete Payment
                                        </button>
                                    @else
                                        <!-- No access - show subscribe button -->
                                        @if($this->isBookFree($book->id))
                                            <button wire:click="subscribeToBook({{ $book->id }})"
                                                    class="w-full inline-flex justify-center items-center px-4 py-2.5 border border-transparent text-sm font-medium rounded-lg text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-all duration-300 transform hover:scale-[1.02]">
                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                                </svg>
                                                Add to Library - Free
                                            </button>
                                        @else
                                            <button wire:click="subscribeToBook({{ $book->id }})"
                                                    class="w-full inline-flex justify-center items-center px-4 py-2.5 border border-transparent text-sm font-medium rounded-lg text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-300 transform hover:scale-[1.02]">
                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                </svg>
                                                Subscribe - GHS {{ number_format($book->annual_subscription_fee ?? 50.00, 2) }}/year
                                            </button>
                                        @endif
                                    @endif
                                @endif
                            @endif

                            <!-- Hardcopy borrowing section -->
                            @if($book->has_hardcopy)
                                @if($this->isBookBorrowed($book->id))
                                    <button wire:click="returnBook({{ $book->id }})"
                                            class="w-full inline-flex justify-center items-center px-4 py-2.5 border border-transparent text-sm font-medium rounded-lg text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-all duration-300 transform hover:scale-[1.02]">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"></path>
                                        </svg>
                                        Return Book
                                    </button>
                                @else
                                    <button wire:click="borrowBook({{ $book->id }})"
                                            class="w-full inline-flex justify-center items-center px-4 py-2.5 border border-gray-300 text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-600 transition-all duration-300 transform hover:scale-[1.02]">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2v0a2 2 0 01-2-2v-1"></path>
                                        </svg>
                                        Borrow Book
                                    </button>
                                @endif
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        @empty
            <div class="col-span-full">
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No books found</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        @if($bookTab === 'available')
                            Try adjusting your search or filters.
                        @else
                            You haven't {{ $bookTab === 'subscribed' ? 'subscribed to' : 'borrowed' }} any books yet.
                        @endif
                    </p>
                </div>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($books->hasPages())
        <div class="mt-6">
            {{ $books->appends(request()->query())->links() }}

        </div>
    @endif
{{--    <x-pdf-reader-modal/>--}}
{{--    @include('components.book-subscription-modal', [--}}
{{--    'showSubscriptionModal' => $showSubscriptionModal,--}}
{{--    'subscriptionData' => $subscriptionData--}}
{{--])--}}

    @livewire('students.book-subscription-modal')

</div>
