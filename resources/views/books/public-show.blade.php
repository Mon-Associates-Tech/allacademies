<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"
      x-data="{ darkMode: localStorage.getItem('darkMode') === 'true' }"
      x-init="$watch('darkMode', val => localStorage.setItem('darkMode', val))" :class="{ 'dark': darkMode }">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- SEO Meta Tags -->
    <title>{{ $book->title }} - {{ $book->author_name }} | {{ config('app.name') }}</title>
    <meta name="description" content="{{ Str::limit($book->description ?? $book->additional_info, 160) }}">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="book">
    <meta property="og:url" content="{{ route('books.public', $book) }}">
    <meta property="og:title" content="{{ $book->title }}">
    <meta property="og:description" content="{{ Str::limit($book->description ?? $book->additional_info, 200) }}">
    <meta property="og:image" content="{{ $book->cover_image }}">

    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="{{ route('books.public', $book) }}">
    <meta property="twitter:title" content="{{ $book->title }}">
    <meta property="twitter:description" content="{{ Str::limit($book->description ?? $book->additional_info, 200) }}">
    <meta property="twitter:image" content="{{ $book->cover_image }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased bg-gray-50 dark:bg-gray-900">
<x-layouts.guest>
    <div x-data="{ currentTab: 'overview' }" class="min-h-screen">
        <!-- Hero Section -->
        <div class="relative bg-gradient-to-b from-gray-50 to-gray-100 dark:from-gray-900 dark:to-gray-800">
            <!-- Background Image with Overlay -->
            <div class="absolute inset-0 h-[400px] overflow-hidden">
                <img src="{{ $book->cover_image }}"
                     alt=""
                     class="w-full h-full object-cover blur-lg opacity-20">
                <div
                    class="absolute inset-0 bg-gradient-to-b from-transparent via-transparent to-gray-50 dark:to-gray-900"></div>
            </div>

            <!-- Content -->
            <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-12 pb-16">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
                    <!-- Book Cover -->
                    <div class="lg:col-span-4 xl:col-span-3">
                        <div class="sticky top-24">
                            <div
                                class="aspect-[3/4] rounded-2xl overflow-hidden shadow-2xl ring-1 ring-gray-900/10 dark:ring-white/10">
                                <img src="{{ $book->cover_image }}"
                                     alt="{{ $book->title }}"
                                     class="w-full h-full object-cover">
                            </div>

                            <!-- CTA Box -->
                            <div
                                class="mt-6 bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 p-6">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">
                                    Want to read this book?
                                </h3>
                                <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                                    Sign up or log in to access the full book and many more.
                                </p>
                                <div class="space-y-3">
                                    <a href="{{ route('register') }}"
                                       class="flex items-center text-nowrap justify-center w-full px-6 py-3 text-white bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 rounded-xl shadow-lg hover:shadow-xl transition-all duration-200">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                                        </svg>
                                        Create Free Account
                                    </a>
                                    <a href="{{ route('login') }}"
                                       class="flex items-center justify-center w-full px-6 py-3 text-gray-700 dark:text-gray-200 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-xl transition-all duration-200">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                                        </svg>
                                        Login
                                    </a>
                                </div>
                            </div>

                            <!-- Quick Facts Card -->
                            <div
                                class="mt-6 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4">
                                <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-3">Quick Facts</h3>
                                <div class="space-y-2 text-sm">
                                    <div class="flex justify-between">
                                        <span class="text-gray-600 dark:text-gray-400">Author:</span>
                                        <span
                                            class="font-medium text-gray-900 dark:text-white">{{ $book->author_name }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600 dark:text-gray-400">Pages:</span>
                                        <span
                                            class="font-medium text-gray-900 dark:text-white">{{ $book->pages }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600 dark:text-gray-400">Publisher:</span>
                                        <span
                                            class="font-medium text-gray-900 dark:text-white">{{ $book->publisher }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600 dark:text-gray-400">Edition:</span>
                                        <span
                                            class="font-medium text-gray-900 dark:text-white">{{ $book->edition }}</span>
                                    </div>
                                    @if($book->is_free)
                                        <div
                                            class="flex justify-between pt-2 border-t border-gray-200 dark:border-gray-600">
                                            <span class="text-gray-600 dark:text-gray-400">Price:</span>
                                            <span class="font-semibold text-green-600 dark:text-green-400">Free</span>
                                        </div>
                                    @elseif($book->annual_subscription_fee)
                                        <div
                                            class="flex justify-between pt-2 border-t border-gray-200 dark:border-gray-600">
                                            <span class="text-gray-600 dark:text-gray-400">Price:</span>
                                            <span class="font-semibold text-blue-600 dark:text-blue-400">GHS {{ number_format($book->annual_subscription_fee, 2) }}/year</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Book Information -->
                    <div class="lg:col-span-8 xl:col-span-9">
                        <div class="space-y-6">
                            <!-- Title and Author -->
                            <div>
                                <h1 class="text-3xl lg:text-4xl font-bold text-gray-900 dark:text-white leading-tight">
                                    {{ $book->title }}
                                </h1>
                                <div class="mt-4 flex flex-wrap items-center gap-4">
                                    <p class="text-xl text-gray-600 dark:text-gray-400">
                                        by <span class="text-blue-600 font-medium">{{ $book->author_name }}</span>
                                    </p>
                                    <div class="flex items-center space-x-1">
                                        @for($i = 1; $i <= 5; $i++)
                                            <svg
                                                class="w-5 h-5 {{ $i <= $book->average_rating ? 'text-yellow-400' : 'text-gray-300' }}"
                                                fill="currentColor" viewBox="0 0 20 20">
                                                <path
                                                    d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                            </svg>
                                        @endfor
                                        <span class="ml-2 text-sm text-gray-600 dark:text-gray-400">
                                            ({{ number_format($book->average_rating, 1) }})
                                        </span>
                                    </div>
                                </div>

                                <!-- Tags/Categories -->
                                <div class="mt-4 flex flex-wrap gap-2">
                                    <span
                                        class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                        {{ $book->primaryCategory->name }}
                                    </span>
                                    @if($book->has_softcopy)
                                        <span
                                            class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                            E-Book Available
                                        </span>
                                    @endif
                                    @if($book->is_free)
                                        <span
                                            class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800 dark:bg-emerald-900 dark:text-emerald-200">
                                            Free
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <!-- Tabs -->
                            <div class="border-b border-gray-200 dark:border-gray-700">
                                <nav class="-mb-px flex space-x-8">
                                    <button @click="currentTab = 'overview'"
                                            :class="currentTab === 'overview' ? 'border-blue-500 text-blue-600 dark:text-blue-400' : 'border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400'"
                                            class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors">
                                        Overview
                                    </button>
                                    @if($book->table_of_contents)
                                        <button @click="currentTab = 'contents'"
                                                :class="currentTab === 'contents' ? 'border-blue-500 text-blue-600 dark:text-blue-400' : 'border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400'"
                                                class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors">
                                            Contents
                                        </button>
                                    @endif
                                    <button @click="currentTab = 'author'"
                                            :class="currentTab === 'author' ? 'border-blue-500 text-blue-600 dark:text-blue-400' : 'border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400'"
                                            class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors">
                                        Author
                                    </button>
                                </nav>
                            </div>

                            <!-- Tab Content -->
                            <div class="min-h-[400px]">
                                <!-- Overview Tab -->
                                <div x-show="currentTab === 'overview'"
                                     x-transition
                                     class="space-y-6">
                                    @if($book->description || $book->additional_info)
                                        <div
                                            class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                                            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">
                                                About This Book
                                            </h2>
                                            <div class="prose prose-gray dark:prose-invert max-w-none">
                                                <p class="text-gray-700 dark:text-gray-300 leading-relaxed">
                                                    {{ $book->description ?? $book->additional_info }}
                                                </p>
                                            </div>
                                        </div>
                                    @endif

                                    @if($book->subscription_conditions && !$book->is_free)
                                        <div
                                            class="bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 rounded-xl border border-blue-200 dark:border-blue-700 p-6">
                                            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">
                                                What You Get
                                            </h2>
                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                @foreach(explode("\n", $book->subscription_conditions) as $condition)
                                                    @if(trim($condition))
                                                        <div class="flex items-start">
                                                            <svg
                                                                class="w-5 h-5 text-blue-600 dark:text-blue-400 mt-0.5 mr-3 flex-shrink-0"
                                                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                      stroke-width="2"
                                                                      d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                            </svg>
                                                            <span class="text-sm text-gray-700 dark:text-gray-300">
                                                                {{ trim($condition, '0123456789. ') }}
                                                            </span>
                                                        </div>
                                                    @endif
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif
                                </div>

                                <!-- Contents Tab -->
                                @if($book->table_of_contents)
                                    <div x-show="currentTab === 'contents'"
                                         x-transition
                                         class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                                        <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">
                                            Table of Contents
                                        </h2>
                                        <div class="space-y-2">
                                            @foreach($book->table_of_contents as $index => $chapter)
                                                <div
                                                    class="flex items-center py-2 px-3 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-lg">
                                                    <span
                                                        class="text-sm font-medium text-gray-500 dark:text-gray-400 mr-3">
                                                        {{ $index + 1 }}.
                                                    </span>
                                                    <span class="text-sm text-gray-700 dark:text-gray-300">
                                                        {{ $chapter['title'] ?? $chapter }}
                                                    </span>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif

                                <!-- Author Tab -->
                                <div x-show="currentTab === 'author'"
                                     x-transition
                                     class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                                    <div class="flex items-start space-x-4">
                                        @if($book->author->user->avatar)
                                            <img src="{{ $book->author->user->avatar }}"
                                                 alt="{{ $book->author_name }}"
                                                 class="w-20 h-20 rounded-full object-cover">
                                        @else
                                            <div
                                                class="w-20 h-20 rounded-full bg-blue-100 dark:bg-blue-900 flex items-center justify-center">
                                                <span class="text-2xl font-bold text-blue-600 dark:text-blue-400">
                                                    {{ substr($book->author_name, 0, 1) }}
                                                </span>
                                            </div>
                                        @endif
                                        <div>
                                            <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                                                {{ $book->author_name }}
                                            </h3>
                                            @if($book->author->bio)
                                                <p class="mt-2 text-gray-600 dark:text-gray-400">
                                                    {{ $book->author->bio }}
                                                </p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.guest>

</body>
</html>
