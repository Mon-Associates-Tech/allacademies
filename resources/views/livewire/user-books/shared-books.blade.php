<div class="max-w-7xl mx-auto py-6">
    <div class="bg-white shadow sm:rounded-lg">
        @if (session()->has('message'))
            <div class="bg-green-50 border-l-4 hidden border-green-400 p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-green-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-green-700">
                            {{ session('message') }}
                        </p>
                    </div>
                </div>
            </div>
        @endif

        <!-- Search Bar -->
        <div class="px-4 hidden py-3 border-b border-gray-200">
            <div class="flex">
                <div class="relative flex-grow">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <input
                        wire:model.debounce.300ms="search"
                        type="text"
                        class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                        placeholder="Search books...">
                </div>
            </div>
        </div>

        <!-- Tab Navigation -->
        <div class="border-b border-gray-200">
            <nav class="-mb-px flex" aria-label="Tabs">
                <button
                    wire:click="setActiveTab('my-books')"
                    class="whitespace-nowrap py-4 px-6 border-b-2 font-medium text-sm cursor-pointer transition-colors duration-200
        {{ $activeTab === 'my-books'
            ? 'border-blue-500 text-blue-600'
            : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                    My Books
                    <span class="bg-purple-100 text-purple-800 text-xs font-medium ml-2 px-2.5 py-0.5 rounded-full">
        {{ $myBooks->count() }}
    </span>
                </button>

                <button
                    wire:click="setActiveTab('pending')"
                    class="whitespace-nowrap py-4 px-6 border-b-2 font-medium text-sm cursor-pointer transition-colors duration-200
                        {{ $activeTab === 'pending'
                            ? 'border-blue-500 text-blue-600'
                            : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                    Pending Requests
                    <span class="bg-blue-100 text-blue-800 text-xs font-medium ml-2 px-2.5 py-0.5 rounded-full">
                        {{ $pendingShares->count() }}
                    </span>
                </button>

                <button
                    wire:click="setActiveTab('accepted')"
                    class="whitespace-nowrap py-4 px-6 border-b-2 font-medium text-sm cursor-pointer transition-colors duration-200
                        {{ $activeTab === 'accepted'
                            ? 'border-blue-500 text-blue-600'
                            : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                    Shared with Me
                    <span class="bg-green-100 text-green-800 text-xs font-medium ml-2 px-2.5 py-0.5 rounded-full">
                        {{ $acceptedShares->count() }}
                    </span>
                </button>
            </nav>
        </div>

        <div class="px-4 py-5 sm:p-6">
            <!-- Loading Indicator -->
            <div wire:loading class="flex justify-center py-4">
                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-500"></div>
            </div>

            <!-- My Books Tab -->
            @if($activeTab === 'my-books')
                @if($myBooks->isEmpty())
                    <div class="text-center py-12">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">No uploaded books</h3>
                        <p class="mt-1 text-sm text-gray-500">You haven't uploaded any books yet.</p>
                        <div class="mt-6">
                            <a href="{{ route('user-books.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                                <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                                </svg>
                                Upload Book
                            </a>
                        </div>
                    </div>
                @else
                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                        @foreach($myBooks as $book)
                            <div class="bg-white border border-gray-200 rounded-lg shadow-sm overflow-hidden hover:shadow-md transition-all duration-200 transform hover:-translate-y-0.5">
                                <div class="p-5">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0">
                                            @if($book->cover_image)
                                                <img class="h-20 w-16 object-cover rounded"
                                                     src="{{ asset('storage/' . $book->cover_image) }}"
                                                     alt="{{ $book->title }}">
                                            @else
                                                <div class="bg-gray-200 border-2 border-dashed rounded-xl w-16 h-20 flex items-center justify-center">
                                                    <svg class="h-8 w-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                                    </svg>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="ml-4">
                                            <h4 class="text-lg font-medium text-gray-900 line-clamp-2">{{ $book->title }}</h4>
                                            <p class="text-sm text-gray-500 mt-1">
                                                {{ $book->shares_count }} {{ \Illuminate\Support\Str::plural('share', $book->shares_count) }}
                                            </p>
                                        </div>
                                    </div>

                                    <div class="mt-4 flex justify-between items-center">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                            @if($book->status === 'published')
                                                bg-green-100 text-green-800
                                            @elseif($book->status === 'draft')
                                                bg-yellow-100 text-yellow-800
                                            @else
                                                bg-gray-100 text-gray-800
                                            @endif">
                                            {{ ucfirst($book->status) }}
                                        </span>

                                        <div class="flex space-x-2">
                                            @if($book->content_url)
                                                <a href="{{ route('user-books.show', $book) }}"
                                                   class="text-blue-600 hover:text-blue-900 font-medium transition-colors duration-200">
                                                    Read
                                                </a>
                                            @endif
                                            <a href="{{ route('user-books.edit', $book) }}"
                                               class="text-gray-600 hover:text-gray-900 font-medium transition-colors duration-200">
                                                Edit
                                            </a>
                                        </div>
                                    </div>

                                    <div class="mt-3 pt-3 border-t border-gray-100">
                                        <div class="flex justify-between text-sm text-gray-500">
                                            <span>
                                                @if($book->pages)
                                                    {{ $book->pages }} pages
                                                @else
                                                    No page count
                                                @endif
                                            </span>
                                            <span>
                                                {{ $book->created_at->format('M d, Y') }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Pagination -->
                 {{--   @if($myBooks->hasPages())
                        <div class="mt-6">
                            {{ $myBooks->links() }}
                        </div>
                    @endif--}}
                @endif
            @endif

            <!-- Pending Shares Tab -->
            @if($activeTab === 'pending')
                @if($pendingShares->isEmpty())
                    <div class="text-center py-12">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 8v8m0 0v-8m0 8h8m-8 0H4m12 0v-8m0 8h4m-4 0l-4-4m4 4l4-4M4 16l4 4" />
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">No pending requests</h3>
                        <p class="mt-1 text-sm text-gray-500">You don't have any pending book share requests.</p>
                    </div>
                @else
                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                        @foreach($pendingShares as $share)
                            <div class="bg-white border border-gray-200 rounded-lg shadow-sm overflow-hidden hover:shadow-md transition-all duration-200 transform hover:-translate-y-0.5">
                                <div class="p-5">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0">
                                            @if($share->userBook->cover_image)
                                                <img class="h-20 w-16 object-cover rounded"
                                                     src="{{ asset('storage/' . $share->userBook->cover_image) }}"
                                                     alt="{{ $share->userBook->title }}">
                                            @else
                                                <div class="bg-gray-200 border-2 border-dashed rounded-xl w-16 h-20 flex items-center justify-center">
                                                    <svg class="h-8 w-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                                    </svg>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="ml-4">
                                            <h4 class="text-lg font-medium text-gray-900 line-clamp-2">{{ $share->userBook->title }}</h4>
                                            <p class="text-sm text-gray-500 mt-1">
                                                Shared by {{ $share->sharedBy->name }}
                                            </p>
                                        </div>
                                    </div>

                                    <div class="mt-4 flex justify-between items-center">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                            Pending
                                        </span>

                                        <div class="flex space-x-2">
                                            <button
                                                wire:click="acceptShare({{ $share->id }})"
                                                wire:loading.attr="disabled"
                                                class="text-green-600 hover:text-green-900 font-medium transition-colors duration-200">
                                                Accept
                                            </button>
                                            <button
                                                wire:click="declineShare({{ $share->id }})"
                                                wire:confirm="Are you sure you want to decline this book share? This action cannot be undone."
                                                wire:loading.attr="disabled"
                                                class="text-red-600 hover:text-red-900 font-medium transition-colors duration-200">
                                                Decline
                                            </button>
                                        </div>
                                    </div>

                                    <div class="mt-3 pt-3 border-t border-gray-100">
                                        <div class="flex justify-between text-sm text-gray-500">
                                            <span>
                                                Requested: {{ $share->created_at->format('M d, Y') }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Pagination -->
                  {{--  @if($pendingShares->hasPages())
                        <div class="mt-6">
                            {{ $pendingShares->links() }}
                        </div>
                    @endif--}}
                @endif
            @endif

            <!-- Accepted Shares Tab -->
            @if($activeTab === 'accepted')
                @if($acceptedShares->isEmpty())
                    <div class="text-center py-12">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 8v8m0 0v-8m0 8h8m-8 0H4m12 0v-8m0 8h4m-4 0l-4-4m4 4l4-4M4 16l4 4" />
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">No shared books</h3>
                        <p class="mt-1 text-sm text-gray-500">You haven't accepted any book shares yet.</p>
                    </div>
                @else
                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                        @foreach($acceptedShares as $share)
                            <div class="bg-white border border-gray-200 rounded-lg shadow-sm overflow-hidden hover:shadow-md transition-all duration-200 transform hover:-translate-y-0.5">
                                <div class="p-5">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0">
                                            @if($share->userBook->cover_image)
                                                <img class="h-20 w-16 object-cover rounded"
                                                     src="{{ asset('storage/' . $share->userBook->cover_image) }}"
                                                     alt="{{ $share->userBook->title }}">
                                            @else
                                                <div class="bg-gray-200 border-2 border-dashed rounded-xl w-16 h-20 flex items-center justify-center">
                                                    <svg class="h-8 w-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                                    </svg>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="ml-4">
                                            <h4 class="text-lg font-medium text-gray-900 line-clamp-2">{{ $share->userBook->title }}</h4>
                                            <p class="text-sm text-gray-500 mt-1">
                                                Shared by {{ $share->sharedBy->name }}
                                            </p>
                                        </div>
                                    </div>

                                    <div class="mt-4 flex justify-between items-center">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            Accepted
                                        </span>

                                        <div class="flex space-x-2">
                                            <a href="{{ route('user-books.show', $share->userBook) }}"
                                               class="text-blue-600 hover:text-blue-900 font-medium transition-colors duration-200">
                                                Read
                                            </a>
                                        </div>
                                    </div>

                                    <div class="mt-3 pt-3 border-t border-gray-100">
                                        <div class="flex justify-between text-sm text-gray-500">
                                            <span>
                                                @if($share->userBook->pages)
                                                    {{ $share->userBook->pages }} pages
                                                @else
                                                    No page count
                                                @endif
                                            </span>
                                            <span>
                                                Shared: {{ $share->created_at->format('M d, Y') }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Pagination -->
                  {{--  @if($acceptedShares->hasPages())
                        <div class="mt-6">
                            {{ $acceptedShares->links() }}
                        </div>
                    @endif--}}
                @endif
            @endif
        </div>
    </div>
</div>
