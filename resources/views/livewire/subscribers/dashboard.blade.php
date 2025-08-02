<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <!-- Welcome Section -->
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">
            Welcome to All Academies, {{ Auth::user()->name }}!
        </h1>
        <p class="text-gray-600 dark:text-gray-400">
            Start your learning journey with free books and explore premium subscriptions.
        </p>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                    </svg>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">My Subscriptions</dt>
                        <dd class="text-lg font-medium text-gray-900 dark:text-white">{{ $subscriptionStats['total_subscriptions'] }}</dd>
                    </dl>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Free Books</dt>
                        <dd class="text-lg font-medium text-gray-900 dark:text-white">{{ $subscriptionStats['free_books_available'] }}</dd>
                    </dl>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="w-8 h-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                    </svg>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Total Books</dt>
                        <dd class="text-lg font-medium text-gray-900 dark:text-white">{{ $subscriptionStats['total_books'] }}</dd>
                    </dl>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                    </svg>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Categories</dt>
                        <dd class="text-lg font-medium text-gray-900 dark:text-white">{{ $subscriptionStats['total_categories'] }}</dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Free Books -->
    <div class="mb-8">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Recent Free Books</h2>
            <a href="#" class="text-violet-600 hover:text-violet-800 text-sm font-medium">
                View all →
            </a>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-3 gap-4">
            @forelse($recentBooks as $book)
                @include('livewire.books.partials.book-card')
            @empty
                <div class="col-span-full text-center py-8 text-gray-500 dark:text-gray-400">
                    No free books available yet.
                </div>
            @endforelse
        </div>
    </div>

    <!-- My Subscriptions -->
    @if(Auth::user()->student && $subscribedBooks->count() > 0)
        <div class="mb-8">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white">My Subscriptions</h2>
                <a href="#" class="text-violet-600 hover:text-violet-800 text-sm font-medium">
                    View all →
                </a>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-4">
                @foreach($subscribedBooks as $subscription)
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-4 hover:shadow-lg transition-shadow">
                        <div class="aspect-w-3 aspect-h-4 mb-3">
                            @if($subscription->book->cover_image)
                                <img src="{{ asset('storage/' . $subscription->book->cover_image) }}" alt="{{ $subscription->book->title }}" class="w-full h-32 object-cover rounded">
                            @else
                                <div class="w-full h-32 bg-gray-200 dark:bg-gray-700 rounded flex items-center justify-center">
                                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                    </svg>
                                </div>
                            @endif
                        </div>
                        <h3 class="font-medium text-gray-900 dark:text-white text-sm mb-1 line-clamp-2">{{ $subscription->book->title }}</h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mb-2">{{ $subscription->book->author->user->name ?? 'Unknown Author' }}</p>
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                            Subscribed
                        </span>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Recommended Books -->
    <div class="mb-8">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Recommended for You</h2>
            <a href="#" class="text-violet-600 hover:text-violet-800 text-sm font-medium">
                View all →
            </a>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-3 gap-4">
            @forelse($recommendedBooks as $book)
                @include('livewire.books.partials.book-card')
            @empty
                <div class="col-span-full text-center py-8 text-gray-500 dark:text-gray-400">
                    No recommended books available.
                </div>
            @endforelse
        </div>
    </div>

    <!-- Categories -->
    <div class="mb-8">
        <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Browse by Category</h2>
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
            @foreach($categories as $category)
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-4 hover:shadow-lg transition-shadow cursor-pointer">
                    <div class="text-center">
                        <div class="w-12 h-12 bg-violet-100 dark:bg-violet-900 rounded-full flex items-center justify-center mx-auto mb-2">
                            <svg class="w-6 h-6 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                            </svg>
                        </div>
                        <h3 class="font-medium text-gray-900 dark:text-white text-sm mb-1">{{ $category->name }}</h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ $category->books_count }} books</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- CTA Section -->
    <div class="bg-gradient-to-r from-violet-600 to-indigo-600 rounded-lg shadow-lg p-8 text-center">
        <h2 class="text-2xl font-bold text-white mb-4">Ready to Access All Content?</h2>
        <p class="text-violet-100 mb-6">Subscribe to All Academies for unlimited access to our entire library of books and exclusive content.</p>
        <a href="#" class="bg-white text-violet-600 hover:bg-gray-100 font-medium py-3 px-6 rounded-md transition-colors">
            Get All Academies Subscription
        </a>
    </div>
</div>

@if (session()->has('success'))
    <div class="fixed top-4 right-4 bg-green-500 text-white px-4 py-2 rounded-md shadow-lg z-50">
        {{ session('success') }}
    </div>
@endif

@if ($errors->any())
    <div class="fixed top-4 right-4 bg-red-500 text-white px-4 py-2 rounded-md shadow-lg z-50">
        @foreach ($errors->all() as $error)
            <p>{{ $error }}</p>
        @endforeach
    </div>
@endif
