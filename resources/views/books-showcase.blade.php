<x-layouts.guest>
    <div class="bg-white dark:bg-gray-900 transition-colors duration-300 min-h-screen">
        
        <div class="py-24 bg-white dark:bg-gray-900 transition-colors duration-300">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-20">
                    <h1 class="text-4xl font-extrabold text-gray-900 dark:text-white sm:text-5xl mb-6">
                        Digital Library <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-green-600">Collection</span>
                    </h1>
                    <p class="max-w-3xl text-xl text-gray-600 dark:text-gray-300 mx-auto leading-relaxed">
                        Access {{ number_format($totalBooks) }} educational resources across {{ $totalCategories }} disciplines. Join our platform to unlock the full library.
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-16">
                    <div class="bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900/20 dark:to-blue-800/20 rounded-2xl p-8 text-center">
                        <div class="text-5xl font-black text-blue-600 dark:text-blue-400 mb-2">{{ number_format($totalBooks) }}</div>
                        <div class="text-lg font-semibold text-gray-700 dark:text-gray-300">Books Available</div>
                    </div>
                    
                    <div class="bg-gradient-to-br from-green-50 to-green-100 dark:from-green-900/20 dark:to-green-800/20 rounded-2xl p-8 text-center">
                        <div class="text-5xl font-black text-green-600 dark:text-green-400 mb-2">500+</div>
                        <div class="text-lg font-semibold text-gray-700 dark:text-gray-300">Expert Authors</div>
                    </div>
                    
                    <div class="bg-gradient-to-br from-purple-50 to-purple-100 dark:from-purple-900/20 dark:to-purple-800/20 rounded-2xl p-8 text-center">
                        <div class="text-5xl font-black text-purple-600 dark:text-purple-400 mb-2">{{ $totalCategories }}</div>
                        <div class="text-lg font-semibold text-gray-700 dark:text-gray-300">Categories</div>
                    </div>
                </div>

                <!-- Featured Books -->
                @if($featuredBooks->count() > 0)
                <div class="mb-16">
                    <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-8 text-center">Featured Books</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($featuredBooks as $book)
                        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
                            <img src="{{ $book['cover_image'] }}" alt="{{ $book['title'] }}" class="w-full h-64 object-cover">
                            <div class="p-6">
                                <div class="text-sm text-blue-600 dark:text-blue-400 font-semibold mb-2">{{ $book['category'] }}</div>
                                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">{{ $book['title'] }}</h3>
                                <p class="text-gray-600 dark:text-gray-400 text-sm mb-3">by {{ $book['author'] }}</p>
                                @if($book['description'])
                                <p class="text-gray-600 dark:text-gray-300 text-sm mb-4">{{ $book['description'] }}</p>
                                @endif
                                <a href="{{ route('register') }}" class="inline-flex items-center text-blue-600 dark:text-blue-400 font-semibold hover:text-blue-700 dark:hover:text-blue-300">
                                    Sign up to read
                                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                    </svg>
                                </a>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Book Categories -->
                <div class="mb-16">
                    <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-8 text-center">Browse by Category</h2>
                    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                        @foreach($categories as $category)
                        <a href="{{ route('register') }}" class="bg-white dark:bg-gray-800 rounded-xl p-6 border border-gray-200 dark:border-gray-700 hover:shadow-lg hover:border-blue-500 dark:hover:border-blue-400 transition-all duration-300 text-center group">
                            <div class="text-4xl mb-3">📚</div>
                            <div class="font-semibold text-gray-900 dark:text-white group-hover:text-blue-600 dark:group-hover:text-blue-400">{{ $category->name }}</div>
                            <div class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ $category->books_count }} books</div>
                        </a>
                        @endforeach
                    </div>
                </div>

                <!-- Features -->
                <div class="mb-16">
                    <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-8 text-center">Library Features</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="flex items-start space-x-4 bg-white dark:bg-gray-800 rounded-xl p-6 border border-gray-200 dark:border-gray-700">
                            <div class="flex-shrink-0">
                                <div class="flex items-center justify-center h-12 w-12 rounded-lg bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400">
                                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                    </svg>
                                </div>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">AI-Powered Search</h3>
                                <p class="text-gray-600 dark:text-gray-300">Find exactly what you need with intelligent search and recommendations</p>
                            </div>
                        </div>

                        <div class="flex items-start space-x-4 bg-white dark:bg-gray-800 rounded-xl p-6 border border-gray-200 dark:border-gray-700">
                            <div class="flex-shrink-0">
                                <div class="flex items-center justify-center h-12 w-12 rounded-lg bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400">
                                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Offline Access</h3>
                                <p class="text-gray-600 dark:text-gray-300">Download books and read them anywhere, anytime without internet</p>
                            </div>
                        </div>

                        <div class="flex items-start space-x-4 bg-white dark:bg-gray-800 rounded-xl p-6 border border-gray-200 dark:border-gray-700">
                            <div class="flex-shrink-0">
                                <div class="flex items-center justify-center h-12 w-12 rounded-lg bg-purple-100 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400">
                                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3"/>
                                    </svg>
                                </div>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Audio Books</h3>
                                <p class="text-gray-600 dark:text-gray-300">Listen to books with AI-generated audio narration</p>
                            </div>
                        </div>

                        <div class="flex items-start space-x-4 bg-white dark:bg-gray-800 rounded-xl p-6 border border-gray-200 dark:border-gray-700">
                            <div class="flex-shrink-0">
                                <div class="flex items-center justify-center h-12 w-12 rounded-lg bg-yellow-100 dark:bg-yellow-900/30 text-yellow-600 dark:text-yellow-400">
                                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                                    </svg>
                                </div>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Progress Tracking</h3>
                                <p class="text-gray-600 dark:text-gray-300">Track your reading progress and earn achievements</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- CTA Section -->
                <div class="bg-gradient-to-r from-blue-600 to-green-600 rounded-3xl p-12 text-center text-white">
                    <h2 class="text-3xl font-bold mb-4">Ready to Start Reading?</h2>
                    <p class="text-xl mb-8 text-blue-100">Join thousands of students and educators accessing our digital library</p>
                    <div class="flex flex-col sm:flex-row gap-4 justify-center">
                        <a href="{{ route('register') }}" class="inline-flex items-center px-8 py-4 text-lg font-semibold rounded-xl bg-white text-blue-600 hover:bg-gray-100 shadow-xl hover:shadow-2xl transform hover:scale-105 transition-all duration-300">
                            Get Started Free
                            <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                            </svg>
                        </a>
                        <a href="{{ route('login') }}" class="inline-flex items-center px-8 py-4 text-lg font-semibold rounded-xl border-2 border-white text-white hover:bg-white/10 transition-all duration-300">
                            Sign In
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.guest>
