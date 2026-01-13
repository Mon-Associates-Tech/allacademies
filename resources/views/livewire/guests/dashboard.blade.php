<div class="max-w-7xl mx-auto overflow-x-hidden">
    <!-- Welcome Section -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">
            Welcome to All Academies, {{ Auth::user()->name }}!
        </h1>
        <p class="text-gray-600 dark:text-gray-400">
            Explore free books, take assessments, chat with AI, and join our learning community.
        </p>
    </div>

    <!-- Quick Stats -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
        @foreach($quickStats as $stat)
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">{{ $stat['label'] }}</p>
                        <p class="text-2xl font-bold text-{{ $stat['color'] }}-600">{{ $stat['value'] }}</p>
                    </div>
                    <div
                        class="w-12 h-12 bg-{{ $stat['color'] }}-100 dark:bg-{{ $stat['color'] }}-900 rounded-lg flex items-center justify-center">
                        @if($stat['icon'] === 'book-open')
                            <svg class="w-6 h-6 text-{{ $stat['color'] }}-600" fill="none" stroke="currentColor"
                                 viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                            </svg>
                        @elseif($stat['icon'] === 'bookmark')
                            <svg class="w-6 h-6 text-{{ $stat['color'] }}-600" fill="none" stroke="currentColor"
                                 viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"></path>
                            </svg>
                        @elseif($stat['icon'] === 'zap')
                            <svg class="w-6 h-6 text-{{ $stat['color'] }}-600" fill="none" stroke="currentColor"
                                 viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                            </svg>
                        @else
                            <svg class="w-6 h-6 text-{{ $stat['color'] }}-600" fill="none" stroke="currentColor"
                                 viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path>
                            </svg>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Guest Capabilities -->
    <div class="mb-8">
        <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">What You Can Do</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($guestCapabilities as $capability)
                <a href="{{ route($capability['route']) }}"
                   class="bg-white dark:bg-gray-800 rounded-lg shadow hover:shadow-lg transition-shadow p-6 group">
                    <div class="flex items-start space-x-4">
                        <div
                            class="w-12 h-12 bg-{{ $capability['color'] }}-100 dark:bg-{{ $capability['color'] }}-900 rounded-lg flex items-center justify-center flex-shrink-0 group-hover:scale-110 transition-transform">
                            @if($capability['icon'] === 'book')
                                <svg class="w-6 h-6 text-{{ $capability['color'] }}-600" fill="none"
                                     stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                </svg>
                            @elseif($capability['icon'] === 'star')
                                <svg class="w-6 h-6 text-{{ $capability['color'] }}-600" fill="none"
                                     stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                                </svg>
                            @elseif($capability['icon'] === 'clipboard')
                                <svg class="w-6 h-6 text-{{ $capability['color'] }}-600" fill="none"
                                     stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                                </svg>
                            @elseif($capability['icon'] === 'chat')
                                <svg class="w-6 h-6 text-{{ $capability['color'] }}-600" fill="none"
                                     stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path>
                                </svg>
                            @elseif($capability['icon'] === 'users')
                                <svg class="w-6 h-6 text-{{ $capability['color'] }}-600" fill="none"
                                     stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                            @else
                                <svg class="w-6 h-6 text-{{ $capability['color'] }}-600" fill="none"
                                     stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                </svg>
                            @endif
                        </div>
                        <div class="flex-1">
                            <h3 class="font-semibold text-gray-900 dark:text-white mb-1 group-hover:text-{{ $capability['color'] }}-600 transition-colors">{{ $capability['title'] }}</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400">{{ $capability['description'] }}</p>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
    </div>

    <!-- Recent Free Books -->
    <div class="mb-8">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Recent Free Books</h2>
            <a href="{{ route('books.index') }}"
               class="inline-flex items-center gap-2 bg-violet-100 dark:bg-violet-900/30 text-violet-600 dark:text-violet-400 hover:bg-violet-200 dark:hover:bg-violet-900/50 px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200 group">
                <span>View all</span>
                <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor"
                     viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            </a>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 xl:grid-cols-2 gap-4">
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

    <div class="mb-8">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white">My Subscriptions</h2>
            <a href="{{ route('books.index') }}"
               class="inline-flex items-center gap-2 bg-violet-100 dark:bg-violet-900/30 text-violet-600 dark:text-violet-400 hover:bg-violet-200 dark:hover:bg-violet-900/50 px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200 group">
                <span>View all</span>
                <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none"
                     stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            </a>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 xl:grid-cols-3 gap-4">
            @foreach($subscribedBooks as $subscription)
                @include('livewire.books.partials.book-card', ['book' => $subscription->book])
            @endforeach
        </div>
    </div>


    <!-- Recommended Books -->
    <div class="mb-8">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Recommended for You</h2>
            <a href="{{ route('books.index') }}"
               class="inline-flex items-center gap-2 bg-violet-100 dark:bg-violet-900/30 text-violet-600 dark:text-violet-400 hover:bg-violet-200 dark:hover:bg-violet-900/50 px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200 group">
                <span>View all</span>
                <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor"
                     viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            </a>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 xl:grid-cols-2 gap-4">
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
                @include('components.category-card', ['category' => $category])
            @endforeach
        </div>
    </div>

    <!-- CTA Section -->
    <div class="bg-gradient-to-r hidden from-violet-600 to-indigo-600 rounded-lg shadow-lg p-8 text-center">
        <h2 class="text-2xl font-bold text-white mb-4">Ready to Access All Content?</h2>
        <p class="text-violet-100 mb-6">Subscribe to All Academies for unlimited access to our entire library of books
            and exclusive content.</p>
        <a href="{{route('subscriptions.create')}}"
           class="bg-white text-violet-600 hover:bg-gray-100 font-medium py-3 px-6 rounded-md transition-colors">
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
