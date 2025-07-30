@props([
    'author',
    'showStats' => true,
    'showBio' => true,
    'showSocials' => true,
    'showBooks' => true,
    'booksLimit' => 4,
    'variant' => 'default' // 'default', 'compact', 'card', 'minimal', 'hero', 'sidebar'
])

@php
    $authorName = $author->name ?: $author->user->name ?: 'Unknown Author';
    $penName = $author->pen_name ?: null;
    $displayName = $penName ?: $authorName;
    $socialLinks = is_string($author->social_links) ? json_decode($author->social_links, true) : $author->social_links;
    $socialLinks = $socialLinks ?: [];
    $authorBooks = $showBooks ? $author->books()->published()->limit($booksLimit)->get() : collect();
    $totalBooks = $author->published_books_count ?: 0;
    $totalReaders = $author->total_readers ?: 0;
    $avgRating = $author->average_rating ?: 0.0;
    $biography = $author->biography ?: 'No biography available.';
    $education = $author->education ?: 'Not specified';
    $awards = $author->awards ?: 'None listed';
    $authorStatement = $author->author_statement ?: 'No statement provided.';
    $website = $author->website ?: '#';
@endphp

<div class="author-profile {{ $variant === 'card' ? 'bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 p-6' : '' }}">

    @if($variant === 'minimal')
        <!-- Minimal Layout - For quick author info -->
        <div class="flex items-center space-x-3">
            <x-avatar :name="$displayName" class="w-12 h-12" />
            <div class="flex-1">
                <h4 class="text-base font-semibold text-gray-900 dark:text-white">{{ $displayName }}</h4>
                <p class="text-sm text-gray-600 dark:text-gray-400">{{ $totalBooks }} books • {{ number_format($avgRating, 1) }}★</p>
            </div>
            @if($showSocials && !empty($socialLinks))
                <div class="flex space-x-1">
                    @include('components.common.social-links', ['socialLinks' => array_slice($socialLinks, 0, 2), 'size' => 'sm'])
                </div>
            @endif
        </div>

    @elseif($variant === 'hero')
        <!-- Hero Layout - For dedicated author pages -->
        <div class="relative overflow-hidden bg-gradient-to-br from-blue-50 via-white to-purple-50 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900 rounded-2xl">
            <!-- Background Pattern -->
            <div class="absolute inset-0 opacity-10">
                <svg class="w-full h-full" viewBox="0 0 100 100" fill="none">
                    <defs>
                        <pattern id="hero-pattern" x="0" y="0" width="10" height="10" patternUnits="userSpaceOnUse">
                            <circle cx="2" cy="2" r="1" fill="currentColor"/>
                        </pattern>
                    </defs>
                    <rect width="100" height="100" fill="url(#hero-pattern)"/>
                </svg>
            </div>

            <div class="relative p-8 md:p-12">
                <div class="flex flex-col md:flex-row items-center space-y-6 md:space-y-0 md:space-x-8">
                    <!-- Large Avatar with decorative elements -->
                    <div class="relative">
                        <div class="absolute -inset-4 bg-gradient-to-r from-blue-400 to-purple-400 rounded-full opacity-20 blur-xl"></div>
                        <x-avatar :name="$displayName" class="relative w-32 h-32 md:w-40 md:h-40 ring-4 ring-white dark:ring-gray-700" />
                        @if($author->awards)
                            <div class="absolute -top-2 -right-2 w-10 h-10 bg-gradient-to-r from-yellow-400 to-orange-400 rounded-full flex items-center justify-center shadow-lg">
                                <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                </svg>
                            </div>
                        @endif
                    </div>

                    <!-- Hero Content -->
                    <div class="flex-1 text-center md:text-left space-y-4">
                        <div>
                            <h1 class="text-3xl md:text-4xl lg:text-5xl font-bold text-gray-900 dark:text-white mb-2">
                                {{ $displayName }}
                            </h1>
                            @if($penName && $penName !== $authorName)
                                <p class="text-xl text-gray-600 dark:text-gray-400 italic">
                                    Also known as {{ $authorName }}
                                </p>
                            @endif
                        </div>

                        <!-- Hero Stats -->
                        <div class="flex flex-wrap justify-center md:justify-start gap-6 text-sm">
                            <div class="flex items-center space-x-2">
                                <div class="w-8 h-8 bg-blue-100 dark:bg-blue-900 rounded-lg flex items-center justify-center">
                                    <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                    </svg>
                                </div>
                                <span class="font-semibold text-gray-900 dark:text-white">{{ $totalBooks }} Books</span>
                            </div>
                            <div class="flex items-center space-x-2">
                                <div class="w-8 h-8 bg-green-100 dark:bg-green-900 rounded-lg flex items-center justify-center">
                                    <svg class="w-4 h-4 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                    </svg>
                                </div>
                                <span class="font-semibold text-gray-900 dark:text-white">{{ number_format($totalReaders) }} Readers</span>
                            </div>
                            <div class="flex items-center space-x-2">
                                <div class="w-8 h-8 bg-yellow-100 dark:bg-yellow-900 rounded-lg flex items-center justify-center">
                                    <svg class="w-4 h-4 text-yellow-600 dark:text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                    </svg>
                                </div>
                                <span class="font-semibold text-gray-900 dark:text-white">{{ number_format($avgRating, 1) }} Rating</span>
                            </div>
                        </div>

                        <!-- Social Links -->
                        @if($showSocials && !empty($socialLinks))
                            <div class="flex justify-center md:justify-start space-x-3">
                                @include('components.common.social-links', ['socialLinks' => $socialLinks, 'size' => 'lg'])
                            </div>
                        @endif

                        <!-- Short Bio -->
                        @if($showBio && $author->biography)
                            <p class="text-gray-700 dark:text-gray-300 leading-relaxed max-w-2xl">
                                {{ Str::limit($author->biography, 200) }}
                            </p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

    @elseif($variant === 'sidebar')
        <!-- Sidebar Layout - For complementary content -->
        <div class="space-y-4">
            <!-- Author Header -->
            <div class="text-center">
                <x-avatar :name="$displayName" class="w-20 h-20 mx-auto mb-3" />
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ $displayName }}</h3>
                @if($penName && $penName !== $authorName)
                    <p class="text-sm text-gray-500 dark:text-gray-400 italic">({{ $authorName }})</p>
                @endif
            </div>

            <!-- Quick Stats -->
            @if($showStats)
                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-3 space-y-2">
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600 dark:text-gray-400">Books</span>
                        <span class="font-semibold text-gray-900 dark:text-white">{{ $totalBooks }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600 dark:text-gray-400">Readers</span>
                        <span class="font-semibold text-gray-900 dark:text-white">{{ number_format($totalReaders) }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600 dark:text-gray-400">Rating</span>
                        <div class="flex items-center space-x-1">
                            <span class="font-semibold text-gray-900 dark:text-white">{{ number_format($avgRating, 1) }}</span>
                            <svg class="w-4 h-4 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Bio -->
            @if($showBio && $author->biography)
                <div>
                    <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-2">About</h4>
                    <p class="text-sm text-gray-600 dark:text-gray-400 leading-relaxed">
                        {{ Str::limit($author->biography, 150) }}
                    </p>
                </div>
            @endif

            <!-- Social Links -->
            @if($showSocials && !empty($socialLinks))
                <div>
                    <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-2">Connect</h4>
                    <div class="flex justify-center space-x-2">
                        @include('components.common.social-links', ['socialLinks' => $socialLinks, 'size' => 'sm'])
                    </div>
                </div>
            @endif

            <!-- Recent Books -->
            @if($showBooks && $authorBooks->count() > 0)
                <div>
                    <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-3">Recent Books</h4>
                    <div class="space-y-2">
                        @foreach($authorBooks->take(3) as $book)
                            <a href="{{ route('books.show', $book) }}" class="flex items-center space-x-3 p-2 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                <div class="w-10 h-12 bg-gradient-to-br from-blue-400 to-purple-500 rounded flex items-center justify-center flex-shrink-0">
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                    </svg>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ $book->title }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $book->bookCategory->name ?? 'General' }}</p>
                                </div>
                            </a>
                        @endforeach

                        @if($totalBooks > 3)
                            <a href="{{ route('books.index', ['author' => $author->id]) }}"
                               class="block text-center text-sm text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 py-2">
                                View all {{ $totalBooks }} books
                            </a>
                        @endif
                    </div>
                </div>
            @endif
        </div>

    @elseif($variant === 'compact')
        <!-- Compact Layout (existing) -->
        <div class="flex items-center space-x-4">
            <div class="flex-shrink-0">
                <x-avatar :name="$displayName" class="w-14 h-14" />
            </div>
            <div class="flex-1 min-w-0">
                <div class="flex items-center space-x-2">
                    <h3 class="text-base font-semibold text-gray-900 dark:text-white truncate">
                        {{ $displayName }}
                    </h3>
                    @if($penName && $penName !== $authorName)
                        <span class="text-xs text-gray-500 dark:text-gray-400">({{ $authorName }})</span>
                    @endif
                </div>
                <p class="text-xs text-gray-600 dark:text-gray-400">
                    {{ $totalBooks }} {{ Str::plural('book', $totalBooks) }} •
                    {{ number_format($totalReaders) }} readers
                </p>
                @if($showSocials && !empty($socialLinks))
                    <div class="flex items-center space-x-2 mt-1">
                        @include('components.common.social-links', ['socialLinks' => $socialLinks, 'size' => 'sm'])
                    </div>
                @endif
            </div>
        </div>

    @else
        <!-- Default/Card Layout (existing full layout) -->
        <div class="space-y-6">
            <!-- Header Section -->
            <div class="flex flex-col sm:flex-row items-start sm:items-center space-y-4 sm:space-y-0 sm:space-x-6">
                <div class="flex-shrink-0">
                    <div class="relative">
                        <x-avatar :name="$displayName" class="w-24 h-24 sm:w-32 sm:h-32" />
                        @if($author->awards)
                            <div class="absolute -top-2 -right-2 w-8 h-8 bg-yellow-400 rounded-full flex items-center justify-center shadow-lg">
                                <svg class="w-4 h-4 text-yellow-800" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                </svg>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="flex-1 space-y-3">
                    <!-- Name and Title -->
                    <div>
                        <div class="flex flex-col sm:flex-row sm:items-center sm:space-x-3">
                            <h2 class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white">
                                {{ $displayName }}
                            </h2>
                            @if($penName && $penName !== $authorName)
                                <span class="text-lg text-gray-500 dark:text-gray-400 italic">
                                    ({{ $authorName }})
                                </span>
                            @endif
                        </div>
                        <div class="flex flex-wrap items-center gap-2 mt-2">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                                </svg>
                                Author
                            </span>
                            @if($author->education)
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"></path>
                                    </svg>
                                    Academic
                                </span>
                            @endif
                            @if($author->awards)
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">
                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                    </svg>
                                    Award Winner
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- Social Links -->
                    @if($showSocials && !empty($socialLinks))
                        <div class="flex items-center space-x-3">
                            @include('components.common.social-links', ['socialLinks' => $socialLinks, 'size' => 'md'])
                        </div>
                    @endif
                </div>
            </div>

            <!-- Stats Section -->
            @if($showStats)
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                    <div class="text-center p-4 bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900/20 dark:to-blue-800/20 rounded-xl border border-blue-200 dark:border-blue-700">
                        <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ $totalBooks }}</div>
                        <div class="text-sm text-blue-700 dark:text-blue-300">Published Books</div>
                    </div>
                    <div class="text-center p-4 bg-gradient-to-br from-green-50 to-green-100 dark:from-green-900/20 dark:to-green-800/20 rounded-xl border border-green-200 dark:border-green-700">
                        <div class="text-2xl font-bold text-green-600 dark:text-green-400">{{ number_format($totalReaders) }}</div>
                        <div class="text-sm text-green-700 dark:text-green-300">Total Readers</div>
                    </div>
                    <div class="text-center p-4 bg-gradient-to-br from-purple-50 to-purple-100 dark:from-purple-900/20 dark:to-purple-800/20 rounded-xl border border-purple-200 dark:border-purple-700">
                        <div class="text-2xl font-bold text-purple-600 dark:text-purple-400">{{ $author->writing_experience ?: '10+' }}</div>
                        <div class="text-sm text-purple-700 dark:text-purple-300">Years Experience</div>
                    </div>
                    <div class="text-center p-4 bg-gradient-to-br from-orange-50 to-orange-100 dark:from-orange-900/20 dark:to-orange-800/20 rounded-xl border border-orange-200 dark:border-orange-700">
                        <div class="text-2xl font-bold text-orange-600 dark:text-orange-400">{{ number_format($avgRating, 1) }}</div>
                        <div class="text-sm text-orange-700 dark:text-orange-300">Avg Rating</div>
                    </div>
                </div>
            @endif

            <!-- Biography Section -->
            @if($showBio && $author->biography)
                <div class="prose prose-gray dark:prose-invert max-w-none">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-3">About the Author</h3>
                    <p class="text-gray-700 dark:text-gray-300 leading-relaxed">
                        {{ $biography }}
                    </p>
                </div>
            @endif

            <!-- Education & Awards -->
            @if($author->education || $author->awards)
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @if($author->education)
                        <div class="space-y-3">
                            <h4 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                                <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"></path>
                                </svg>
                                Education
                            </h4>
                            <p class="text-gray-600 dark:text-gray-400">{{ $education }}</p>
                        </div>
                    @endif

                    @if($author->awards)
                        <div class="space-y-3">
                            <h4 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                                <svg class="w-5 h-5 mr-2 text-yellow-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                </svg>
                                Awards & Recognition
                            </h4>
                            <p class="text-gray-600 dark:text-gray-400">{{ $awards }}</p>
                        </div>
                    @endif
                </div>
            @endif

            <!-- Author Statement -->
            @if($author->author_statement)
                <div class="bg-gray-50 dark:bg-gray-700 rounded-xl p-6 border-l-4 border-blue-500">
                    <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-3">Author's Statement</h4>
                    <blockquote class="text-gray-700 dark:text-gray-300 italic leading-relaxed">
                        "{{ $authorStatement }}"
                    </blockquote>
                </div>
            @endif

            <!-- Books Section -->
            @if($showBooks && $authorBooks->count() > 0)
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Other Books by {{ $displayName }}</h3>
                        @if($totalBooks > $booksLimit)
                            <a href="{{ route('books.index', ['author' => $author->id]) }}"
                               class="text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300 text-sm font-medium">
                                View all {{ $totalBooks }} books →
                            </a>
                        @endif
                    </div>

                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
                        @foreach($authorBooks as $book)
                            <a href="{{ route('books.show', $book) }}" class="group block">
                                <div class="aspect-[3/4] rounded-lg overflow-hidden shadow-md group-hover:shadow-lg transition-all duration-200">
                                    @if($book->cover_image)
                                        <img src="{{ $book->cover_image }}"
                                             alt="{{ $book->title }}"
                                             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-200">
                                    @else
                                        <div class="w-full h-full bg-gradient-to-br from-gray-200 to-gray-300 dark:from-gray-600 dark:to-gray-700 flex items-center justify-center">
                                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                            </svg>
                                        </div>
                                    @endif
                                </div>
                                <h4 class="mt-2 text-sm font-medium text-gray-900 dark:text-white group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors line-clamp-2">
                                    {{ $book->title }}
                                </h4>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $book->bookCategory->name ?? 'General' }}</p>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Website Link -->
            @if($author->website)
                <div class="pt-4 border-t border-gray-200 dark:border-gray-700">
                    <a href="{{ $author->website }}"
                       target="_blank"
                       rel="noopener noreferrer"
                       class="inline-flex items-center text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300 font-medium">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                        </svg>
                        Visit Author's Website
                    </a>
                </div>
            @endif
        </div>
    @endif
</div>
