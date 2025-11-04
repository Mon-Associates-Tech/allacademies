<x-layouts.app>
    @php
        $cover = $userBook->cover_image ? asset('storage/' . $userBook->cover_image) : asset('images/book-cover.png');
    @endphp

    <div x-data="{
        currentTab: 'overview',
        showShareMenu: false,
        isBookmarked: false,
        showNotesModal: false,
        notes: '',
        imageLoaded: false,
        isLoading: false,
        toggleTab(tab) {
            this.currentTab = tab;
            window.history.pushState({}, '', `#${tab}`);
        },
        initTab() {
            const hash = window.location.hash.replace('#', '');
            this.currentTab = hash || 'overview';
            // Check if bookmarked
            this.isBookmarked = localStorage.getItem('user_bookmarked_{{ $userBook->id }}') === 'true';
            // Load saved notes
            this.notes = localStorage.getItem('user_notes_{{ $userBook->id }}') || '';
        },
        toggleBookmark() {
            this.isBookmarked = !this.isBookmarked;
            localStorage.setItem('user_bookmarked_{{ $userBook->id }}', this.isBookmarked);
        },
        saveNotes() {
            localStorage.setItem('user_notes_{{ $userBook->id }}', this.notes);
            this.showNotesModal = false;
        },
        copyLink() {
            navigator.clipboard.writeText(window.location.href);
            // Show toast notification (you can implement this)
        }
    }" x-init="initTab()"
         class="min-h-screen bg-gradient-to-b from-gray-50 to-gray-100 rounded-md dark:from-gray-900 dark:to-gray-800">

        <!-- Breadcrumb Navigation -->
        <div class="rounded-t-md bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3">
                <nav class="flex" aria-label="Breadcrumb">
                    <ol class="inline-flex items-center space-x-1 md:space-x-3">
                        <li class="inline-flex items-center">
                            <a href="{{ route('user-books.index') }}"
                               class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-blue-600 dark:text-gray-400 dark:hover:text-white">
                                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path
                                        d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path>
                                </svg>
                                My Books
                            </a>
                        </li>
                        <li aria-current="page">
                            <div class="flex items-center">
                                <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                          d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                                          clip-rule="evenodd"></path>
                                </svg>
                                <span
                                    class="ml-1 text-sm font-medium text-gray-500 dark:text-gray-400 md:ml-2 truncate">{{ $userBook->title }}</span>
                            </div>
                        </li>
                    </ol>
                </nav>
            </div>
        </div>

        <!-- Hero Section -->
        <div class="relative">
            <!-- Background Image with Overlay -->
            <div class="absolute inset-0 h-[500px] overflow-hidden">
                <img src="{{ $cover }}"
                     alt=""
                     class="w-full h-full object-cover blur-lg opacity-30 transition-opacity duration-300">
                <div
                    class="absolute inset-0 bg-gradient-to-b from-transparent via-transparent to-gray-50 dark:to-gray-900"></div>
            </div>

            <!-- Quick Stats Bar -->
            <div
                class="relative z-10 bg-white/90 dark:bg-gray-800/90 backdrop-blur-sm border-b border-gray-200 dark:border-gray-700">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3">
                    <div class="flex flex-wrap items-center gap-6 text-sm">
                        <div class="flex items-center text-gray-600 dark:text-gray-400">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                            </svg>
                            {{ $userBook->pages ?? 'N/A' }} pages
                        </div>
                        <div class="flex items-center text-gray-600 dark:text-gray-400">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                            </svg>
                            User Uploaded
                        </div>
                        <div class="flex items-center text-gray-600 dark:text-gray-400">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            @if($userBook->pages)
                                ~{{ ceil($userBook->pages / 250) }} hour read
                            @else
                                Unknown duration
                            @endif
                        </div>
                        <span
                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-800 dark:text-blue-100">
                            {{ ucfirst($userBook->status) }}
                        </span>

                        <!-- Social Activity -->
                        <div class="flex items-center text-gray-600 dark:text-gray-400">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                            Shared with {{ $userBook->shares_count }} users
                        </div>
                    </div>
                </div>
            </div>

            <!-- Content -->
            <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-8 pb-16">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
                    <!-- Book Cover -->
                    <div class="lg:col-span-4 xl:col-span-3">
                        <div class="sticky top-24">
                            <div
                                class="aspect-[3/4] rounded-2xl overflow-hidden shadow-2xl ring-1 ring-gray-900/10 dark:ring-white/10 group">
                                <img src="{{ $cover }}"
                                     alt="{{ $userBook->title }}"
                                     class="w-full h-full object-cover transform group-hover:scale-105 transition-transform duration-300"
                                >

                                <!-- Bookmark overlay -->
                                <div class="absolute top-4 right-4">
                                    <button @click="toggleBookmark()"
                                            class="p-2 rounded-full bg-white/80 backdrop-blur-sm shadow-lg hover:bg-white transition-all duration-200"
                                            :class="{ 'text-red-500': isBookmarked, 'text-gray-400': !isBookmarked }">
                                        <svg class="w-5 h-5" :fill="isBookmarked ? 'currentColor' : 'none'"
                                             stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                                        </svg>
                                    </button>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="mt-6 space-y-3">
                                <!-- Primary Action -->
                                @if($userBook->content_url)
                                <div class="flex flex-col space-y-3">
                                    <x-button.primary
                                        onclick="Livewire.dispatch('openUserBookPDFReader', {bookId: {{ $userBook->id }}})"
                                        class="px-4 py-3 flex w-full text-gray-600 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-xl">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                        <span>Read Now</span>
                                    </x-button.primary>

                                        <a href="{{ route('learning.quiz') }}?bookId={{$userBook->id}}"
                                       class="flex items-center text-nowrap justify-center w-full px-4 py-3 text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-xl shadow-md hover:shadow-lg transition-all duration-200 border border-gray-200 dark:border-gray-700 group">
                                        <svg class="w-5 h-5 mr-2 text-blue-500 group-hover:scale-110 transition-transform"
                                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                                        </svg>
                                        <span class="font-medium">Take Quiz</span>
                                    </a>
                                </div>
                                
                                @endif

                                <!-- Secondary Actions Grid -->
                                <div class="grid grid-cols-2 gap-3">
                                    <!-- Preview Button -->
                                    @if($userBook->sample_url)
                                        <button
                                            x-data="{}"
                                            @click="$dispatch('open-modal', {name: 'book-preview'})"
                                            class="flex items-center justify-center px-4 py-3 text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-xl shadow-md hover:shadow-lg transition-all duration-200 border border-gray-200 dark:border-gray-700 group">
                                            <svg class="w-4 h-4 mr-2 group-hover:scale-110 transition-transform"
                                                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                            </svg>
                                            <span class="text-sm font-medium">Preview</span>
                                        </button>
                                    @endif

                                    <!-- Notes Button -->
                                    <button
                                        x-data="{}"
                                        @click="$dispatch('open-modal', {name: 'book-notes'})"
                                        class="flex hidden items-center justify-center px-4 py-3 text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-xl shadow-md hover:shadow-lg transition-all duration-200 border border-gray-200 dark:border-gray-700 group">
                                        <svg class="w-4 h-4 mr-2 group-hover:scale-110 transition-transform" fill="none"
                                             stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                        <span class="text-sm font-medium">Notes</span>
                                    </button>
                                </div>

                                <!-- Share Button -->
                                <div class="relative hidden" x-data="{ open: false }">
                                    <button @click="open = !open"
                                            class="flex items-center justify-center w-full px-4 py-3 text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-xl shadow-md hover:shadow-lg transition-all duration-200 border border-gray-200 dark:border-gray-700">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.367 2.684 3 3 0 00-5.367-2.684z"></path>
                                        </svg>
                                        Share Book
                                    </button>
                                    <div x-show="open"
                                         @click.away="open = false"
                                         x-transition:enter="transition ease-out duration-200"
                                         x-transition:enter-start="opacity-0 transform scale-95"
                                         x-transition:enter-end="opacity-100 transform scale-100"
                                         x-transition:leave="transition ease-in duration-75"
                                         x-transition:leave-start="opacity-100 transform scale-100"
                                         x-transition:leave-end="opacity-0 transform scale-95"
                                         class="absolute right-0 w-56 mt-2 py-2 bg-white dark:bg-gray-800 rounded-lg shadow-xl z-50 border border-gray-200 dark:border-gray-700">
                                        <button @click="copyLink(); open = false"
                                                class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 flex items-center">
                                            <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor"
                                                 viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                            </svg>
                                            Copy Link
                                        </button>
                                        <a href="https://twitter.com/intent/tweet?text={{ urlencode($userBook->title) }}&url={{ urlencode(request()->url()) }}"
                                           target="_blank"
                                           class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700">
                                            <div class="flex items-center">
                                                <svg class="w-4 h-4 mr-3" fill="currentColor" viewBox="0 0 24 24">
                                                    <path
                                                        d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/>
                                                </svg>
                                                Share on Twitter
                                            </div>
                                        </a>
                                        <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(request()->url()) }}"
                                           target="_blank"
                                           class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700">
                                            <div class="flex items-center">
                                                <svg class="w-4 h-4 mr-3" fill="currentColor" viewBox="0 0 24 24">
                                                    <path
                                                        d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                                                </svg>
                                                Share on Facebook
                                            </div>
                                        </a>
                                    </div>
                                </div>

                                <!-- Edit Button -->
                                @if($userBook->user_id === auth()->id())
                                <a href="{{ route('user-books.edit', $userBook) }}"
                                   class="flex items-center justify-center w-full px-4 py-3 text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-xl shadow-md hover:shadow-lg transition-all duration-200 border border-gray-200 dark:border-gray-700">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                    Edit Book
                                </a>
                                    @endif
                            </div>

                            <!-- Quick Facts Card -->
                            <div
                                class="mt-6 hidden bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4">
                                <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-3">Quick Facts</h3>
                                <div class="space-y-2 text-sm">
                                    <div class="flex justify-between">
                                        <span class="text-gray-600 dark:text-gray-400">Uploaded by:</span>
                                        <span
                                            class="font-medium text-gray-900 dark:text-white">{{ $userBook->user->name }}</span>
                                    </div>
                                    @if($userBook->pages)
                                        <div class="flex justify-between">
                                            <span class="text-gray-600 dark:text-gray-400">Pages:</span>
                                            <span
                                                class="font-medium text-gray-900 dark:text-white">{{ $userBook->pages }}</span>
                                        </div>
                                    @endif
                                    @if($userBook->publisher)
                                        <div class="flex justify-between">
                                            <span class="text-gray-600 dark:text-gray-400">Publisher:</span>
                                            <span
                                                class="font-medium text-gray-900 dark:text-white">{{ $userBook->publisher }}</span>
                                        </div>
                                    @endif
                                    @if($userBook->edition)
                                        <div class="flex justify-between">
                                            <span class="text-gray-600 dark:text-gray-400">Edition:</span>
                                            <span
                                                class="font-medium text-gray-900 dark:text-white">{{ $userBook->edition }}</span>
                                        </div>
                                    @endif
                                    <div class="flex justify-between">
                                        <span class="text-gray-600 dark:text-gray-400">Status:</span>
                                        <span
                                            class="font-medium text-gray-900 dark:text-white">{{ ucfirst($userBook->status) }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Book Information -->
                    <div class="lg:col-span-8 xl:col-span-9">
                        <div class="space-y-6">
                            <!-- Title and Author -->
                            <div>
                                <h1 class="text-3xl lg:text-4xl font-bold text-gray-900 dark:text-white leading-tight">{{ $userBook->title }}</h1>
                                <div class="mt-4 flex flex-wrap items-center gap-4">
                                    <p class="text-xl text-gray-600 dark:text-gray-400">
                                        by <span class="text-blue-600 font-medium">{{ $userBook->user->name }}</span>
                                    </p>
                                </div>
                            </div>

                            <!-- Enhanced Tabs -->
                            <div class="border-b border-gray-200 dark:border-gray-700">
                                <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                                    <button @click="toggleTab('overview')"
                                            :class="currentTab === 'overview' ? 'border-blue-500 text-blue-600 dark:text-blue-400' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300'"
                                            class="whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm flex items-center transition-colors duration-200">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                        Overview
                                    </button>
                                    <button @click="toggleTab('contents')"
                                            :class="currentTab === 'contents' ? 'border-blue-500 text-blue-600 dark:text-blue-400' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300'"
                                            class="whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm flex items-center transition-colors duration-200">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M4 6h16M4 10h16M4 14h16M4 18h16"></path>
                                        </svg>
                                        Contents
                                    </button>
                                    @if($userBook->has_audio || $userBook->has_video)
                                        <button @click="toggleTab('media')"
                                                :class="currentTab === 'media' ? 'border-blue-500 text-blue-600 dark:text-blue-400' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300'"
                                                class="whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm flex items-center transition-colors duration-200">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 7h8m-8 4h8m-8 4h8M5 7a2 2 0 01-2-2v8a2 2 0 012 2zm0 0a2 2 0 002 2h8a2 2 0 002-2M5 7a2 2 0 012-2m8 0a2 2 0 012 2m0 0a2 2 0 002 2m0 0v4a2 2 0 01-2 2m0 0a2 2 0 01-2-2"></path>
                                            </svg>
                                            Media
                                        </button>
                                    @endif
                                </nav>
                            </div>

                            <!-- Tab Content -->
                            <div class="tab-content">
                                <!-- Overview Tab -->
                                <div x-show="currentTab === 'overview'"
                                     x-transition:enter="transition ease-out duration-300"
                                     x-transition:enter-start="opacity-0 transform translate-y-4"
                                     x-transition:enter-end="opacity-100 transform translate-y-0"
                                     class="space-y-6">

                                    <!-- Book Description -->
                                    @if($userBook->description)
                                        <div
                                            class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                                            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">About
                                                This
                                                Book</h2>
                                            <div class="prose prose-gray dark:prose-invert max-w-none">
                                                <p class="text-gray-700 dark:text-gray-300 leading-relaxed">
                                                    {{ $userBook->description }}
                                                </p>
                                            </div>
                                        </div>
                                    @endif

                                    <!-- Key Features -->
                                    <div
                                        class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                                        <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Key
                                            Features</h2>
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            <div class="flex items-start">
                                                <div class="flex-shrink-0">
                                                    <div
                                                        class="w-8 h-8 bg-blue-100 dark:bg-blue-900 rounded-lg flex items-center justify-center">
                                                        <svg class="w-4 h-4 text-blue-600 dark:text-blue-400"
                                                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                  stroke-width="2"
                                                                  d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                        </svg>
                                                    </div>
                                                </div>
                                                <div class="ml-3">
                                                    <h3 class="text-sm font-medium text-gray-900 dark:text-white">
                                                        User Uploaded Content</h3>
                                                    <p class="text-sm text-gray-500 dark:text-gray-400">This book was uploaded by a user</p>
                                                </div>
                                            </div>
                                            @if($userBook->has_audio)
                                                <div class="flex items-start">
                                                    <div class="flex-shrink-0">
                                                        <div
                                                            class="w-8 h-8 bg-green-100 dark:bg-green-900 rounded-lg flex items-center justify-center">
                                                            <svg class="w-4 h-4 text-green-600 dark:text-green-400"
                                                                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                      stroke-width="2"
                                                                      d="M15.536 8.464a5 5 0 010 7.072M18.364 5.636a9 9 0 010 12.728M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z"></path>
                                                            </svg>
                                                        </div>
                                                    </div>
                                                    <div class="ml-3">
                                                        <h3 class="text-sm font-medium text-gray-900 dark:text-white">
                                                            Audio Content</h3>
                                                        <p class="text-sm text-gray-500 dark:text-gray-400">Includes audio files for listening</p>
                                                    </div>
                                                </div>
                                            @endif
                                            @if($userBook->has_video)
                                                <div class="flex items-start">
                                                    <div class="flex-shrink-0">
                                                        <div
                                                            class="w-8 h-8 bg-purple-100 dark:bg-purple-900 rounded-lg flex items-center justify-center">
                                                            <svg class="w-4 h-4 text-purple-600 dark:text-purple-400"
                                                                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                      stroke-width="2"
                                                                      d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                                            </svg>
                                                        </div>
                                                    </div>
                                                    <div class="ml-3">
                                                        <h3 class="text-sm font-medium text-gray-900 dark:text-white">
                                                            Video Content</h3>
                                                        <p class="text-sm text-gray-500 dark:text-gray-400">Includes video files for watching</p>
                                                    </div>
                                                </div>
                                            @endif
                                            <div class="flex items-start">
                                                <div class="flex-shrink-0">
                                                    <div
                                                        class="w-8 h-8 bg-orange-100 dark:bg-orange-900 rounded-lg flex items-center justify-center">
                                                        <svg class="w-4 h-4 text-orange-600 dark:text-orange-400"
                                                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                  stroke-width="2"
                                                                  d="M7 4V2a1 1 0 011-1h8a1 1 0 011 1v2m-9 0h10a2 2 0 012 2v14a2 2 0 01-2 2H6a2 2 0 01-2-2V6a2 2 0 012-2z"></path>
                                                        </svg>
                                                    </div>
                                                </div>
                                                <div class="ml-3">
                                                    <h3 class="text-sm font-medium text-gray-900 dark:text-white">Personal
                                                        Collection</h3>
                                                    <p class="text-sm text-gray-500 dark:text-gray-400">Part of user's personal book collection</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Contents Tab -->
                                <div x-show="currentTab === 'contents'"
                                     x-transition:enter="transition ease-out duration-300"
                                     x-transition:enter-start="opacity-0 transform translate-y-4"
                                     x-transition:enter-end="opacity-100 transform translate-y-0"
                                     class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Table of Contents</h2>
                                    @if($userBook->table_of_contents)
                                        <div class="space-y-4">
                                            @foreach($userBook->formatted_table_of_contents as $chapter)
                                                <div class="border-l-4 border-blue-500 pl-4 py-1">
                                                    <div class="flex justify-between">
                                                        <h3 class="font-medium text-gray-900 dark:text-white">
                                                            Chapter {{ $chapter['chapter_number'] }}: {{ $chapter['title'] }}
                                                        </h3>
                                                        @if($chapter['page_range'])
                                                            <span class="text-sm text-gray-500 dark:text-gray-400">
                                                                {{ $chapter['page_range'] }}
                                                            </span>
                                                        @endif
                                                    </div>
                                                    @if($chapter['description'])
                                                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                                            {{ $chapter['description'] }}
                                                        </p>
                                                    @endif
                                                    @if(!empty($chapter['sections']))
                                                        <div class="mt-2 space-y-2 ml-4">
                                                            @foreach($chapter['sections'] as $section)
                                                                <div class="flex justify-between text-sm">
                                                                    <span class="text-gray-700 dark:text-gray-300">
                                                                        {{ $section['title'] }}
                                                                    </span>
                                                                    @if(isset($section['page_start']) && isset($section['page_end']))
                                                                        <span class="text-gray-500 dark:text-gray-400">
                                                                            Pages {{ $section['page_start'] }}-{{ $section['page_end'] }}
                                                                        </span>
                                                                    @endif
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    @endif
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <p class="text-gray-500 dark:text-gray-400">No table of contents available for this book.</p>
                                    @endif
                                </div>

                                <!-- Media Tab -->
                                @if($userBook->has_audio || $userBook->has_video)
                                    <div x-show="currentTab === 'media'"
                                         x-transition:enter="transition ease-out duration-300"
                                         x-transition:enter-start="opacity-0 transform translate-y-4"
                                         x-transition:enter-end="opacity-100 transform translate-y-0"
                                         class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6"
                                         x-data="{ mediaTab: 'audio' }">

                                        <!-- Media Type Tabs -->
                                        <div class="border-b border-gray-200 dark:border-gray-700 mb-6">
                                            <nav class="-mb-px flex space-x-8">
                                                @if($userBook->has_audio)
                                                    <!-- Audio Tab -->
                                                    <button @click="mediaTab = 'audio'"
                                                            :class="{
                                'border-blue-500 text-blue-600 dark:text-blue-400': mediaTab === 'audio',
                                'border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300': mediaTab !== 'audio'
                            }"
                                                            class="group inline-flex items-center py-2 px-1 border-b-2 font-medium text-sm transition-colors duration-200">
                                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor"
                                                             viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                  stroke-width="2"
                                                                  d="M15.536 8.464a5 5 0 010 7.072M18.364 5.636a9 9 0 010 12.728M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z"/>
                                                        </svg>
                                                        Audio Player
                                                    </button>
                                                @endif

                                                @if($userBook->has_video)
                                                    <!-- Video Tab -->
                                                    <button @click="mediaTab = 'video'"
                                                            :class="{
                                'border-blue-500 text-blue-600 dark:text-blue-400': mediaTab === 'video',
                                'border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300': mediaTab !== 'video'
                            }"
                                                            class="group inline-flex items-center py-2 px-1 border-b-2 font-medium text-sm transition-colors duration-200">
                                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor"
                                                             viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                  stroke-width="2"
                                                                  d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                                        </svg>
                                                        Video Player
                                                    </button>
                                                @endif
                                            </nav>
                                        </div>

                                        <!-- Media Content -->
                                        <div class="space-y-6">
                                            <!-- Audio Player Tab Content -->
                                            @if($userBook->has_audio)
                                                <div x-show="mediaTab === 'audio'"
                                                     x-transition:enter="transition ease-out duration-200"
                                                     x-transition:enter-start="opacity-0 transform translate-x-4"
                                                     x-transition:enter-end="opacity-100 transform translate-x-0"
                                                     x-transition:leave="transition ease-in duration-150"
                                                     x-transition:leave-start="opacity-100 transform translate-x-0"
                                                     x-transition:leave-end="opacity-0 transform -translate-x-4">

                                                    <!-- Audio Player Header -->
                                                    <div class="flex items-center justify-between mb-4">
                                                        <div class="flex items-center space-x-3">
                                                            <div
                                                                class="w-10 h-10 bg-gradient-to-r from-green-500 to-teal-500 rounded-lg flex items-center justify-center">
                                                                <svg class="w-5 h-5 text-white" fill="none"
                                                                     stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                                          stroke-width="2"
                                                                          d="M15.536 8.464a5 5 0 010 7.072M18.364 5.636a9 9 0 010 12.728M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z"/>
                                                                </svg>
                                                            </div>
                                                            <div>
                                                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                                                                    Audio Content</h3>
                                                                <p class="text-sm text-gray-500 dark:text-gray-400">Listen to
                                                                    the audio version</p>
                                                            </div>
                                                        </div>
                                                        <div class="flex items-center space-x-2">
                        <span
                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                            <span class="w-1.5 h-1.5 bg-green-500 rounded-full mr-1.5"></span>
                            Audio
                        </span>
                                                        </div>
                                                    </div>

                                                    <!-- Audio Player -->
                                                  {{-- Full Book Audio (if available) --}}

                                                  {{-- Debug: see what chapter_audios contains --}}

{{-- @if($userBook->single_audio)
    <div class="mb-6">
        <h4 class="font-medium text-gray-900 dark:text-white mb-2">Full Book Audio</h4>
        <audio controls class="w-full">
            <source src="{{ $userBook->single_audio }}" type="audio/mpeg">
            Your browser does not support the audio element.
        </audio>
    </div>
@endif --}}
{{-- Chapter Audios (multi-volume) --}}
@if($userBook->chapter_audios && count($userBook->chapter_audios) > 0)
    <div>
        <h4 class="font-medium text-gray-900 dark:text-white mb-2">Chapter Audios</h4>
        <div class="space-y-3">
            @foreach($userBook->chapter_audios as $index => $audioUrl)
                <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                    <span class="text-gray-700 dark:text-gray-300">Volume {{ $index + 1 }}</span>
                    <audio controls class="w-64">
                        <source src="{{ $audioUrl }}" type="audio/mpeg">
                        Your browser does not support the audio element.
                    </audio>
                </div>
            @endforeach
        </div>
    </div>
@endif

                                                    <!-- Audio Player -->
{{-- @if($userBook->single_audio)
    <div class="mb-6">
        <h4 class="font-medium text-gray-900 dark:text-white mb-2">Full Book Audio</h4>
        <audio controls class="w-full">
            <source src="{{ Storage::url($userBook->single_audio) }}" type="audio/mpeg">
            Your browser does not support the audio element.
        </audio>
    </div>
@endif

@if(!empty($userBook->chapter_audios))
    @php
        $chapterAudios = is_array($userBook->chapter_audios)
            ? $userBook->chapter_audios
            : json_decode($userBook->chapter_audios, true);
    @endphp

    @if(!empty($chapterAudios))
        <div>
            <h4 class="font-medium text-gray-900 dark:text-white mb-2">Chapter Audios</h4>
            <div class="space-y-3">
                @foreach($chapterAudios as $index => $audioUrl)
                    <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        <span class="text-gray-700 dark:text-gray-300">Chapter {{ $index + 1 }}</span>
                        <audio controls class="w-64">
                            <source src="{{ asset('storage/' . ltrim($audioUrl, '/')) }}" type="audio/mpeg">
                            Your browser does not support the audio element.
                        </audio>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
@endif --}}


                                                </div>
                                            @endif

                                            <!-- Video Player Tab Content -->
                                            @if($userBook->has_video)
                                                <div x-show="mediaTab === 'video'"
                                                     x-transition:enter="transition ease-out duration-200"
                                                     x-transition:enter-start="opacity-0 transform translate-x-4"
                                                     x-transition:enter-end="opacity-100 transform translate-x-0"
                                                     x-transition:leave="transition ease-in duration-150"
                                                     x-transition:leave-start="opacity-100 transform translate-x-0"
                                                     x-transition:leave-end="opacity-0 transform -translate-x-4">

                                                    <!-- Video Player Header -->
                                                    <div class="flex items-center justify-between mb-4">
                                                        <div class="flex items-center space-x-3">
                                                            <div
                                                                class="w-10 h-10 bg-gradient-to-r from-purple-500 to-pink-500 rounded-lg flex items-center justify-center">
                                                                <svg class="w-5 h-5 text-white" fill="none"
                                                                     stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                                          stroke-width="2"
                                                                          d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                                                </svg>
                                                            </div>
                                                            <div>
                                                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                                                                    Video Content</h3>
                                                                <p class="text-sm text-gray-500 dark:text-gray-400">Watch the
                                                                    video version</p>
                                                            </div>
                                                        </div>
                                                        <div class="flex items-center space-x-2">
                        <span
                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200">
                            <span class="w-1.5 h-1.5 bg-purple-500 rounded-full mr-1.5"></span>
                            Video
                        </span>
                                                        </div>
                                                    </div>

                                                    <!-- Video Player -->
                                                    @if($userBook->single_video)
                                                        <div class="mb-6">
                                                            <h4 class="font-medium text-gray-900 dark:text-white mb-2">Full Book Video</h4>
                                                            <video controls class="w-full rounded-lg">
                                                                <source src="{{ $userBook->single_video }}" type="video/mp4">
                                                                Your browser does not support the video element.
                                                            </video>
                                                        </div>
                                                    @endif

                                                    @if($userBook->chapter_videos && count($userBook->chapter_videos) > 0)
                                                        <div>
                                                            <h4 class="font-medium text-gray-900 dark:text-white mb-2">Chapter Videos</h4>
                                                            <div class="space-y-3">
                                                                @foreach($userBook->chapter_videos as $index => $videoUrl)
                                                                    <div class="p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                                                        <h5 class="text-gray-700 dark:text-gray-300 mb-2">Chapter {{ $index + 1 }}</h5>
                                                                        <video controls class="w-full rounded">
                                                                            <source src="{{ $videoUrl }}" type="video/mp4">
                                                                            Your browser does not support the video element.
                                                                        </video>
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    @endif
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Preview Modal -->
        <x-modal-component name="book-preview" size="4xl" title="{{ $userBook->title }} - Preview">
            @if($userBook->sample_url)
                <div class="aspect-video">
                    <iframe src="{{ asset('storage/' . $userBook->sample_url) }}"
                            style="height: 90vh; width: 100%;"
                            class="w-full h-full rounded-lg"
                            frameborder="0"></iframe>
                </div>
            @else
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No preview available</h3>
                    <p class="mt-1 text-sm text-gray-500">A preview has not been created for this book.</p>
                </div>
            @endif

            <x-slot name="actions">
                <button @click="$dispatch('close-modal', {name: 'book-preview'})"
                        class="px-4 py-2 text-gray-700 dark:text-gray-300 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700">
                    Close
                </button>
            </x-slot>
        </x-modal-component>

        <!-- Notes Modal -->
        <x-modal-component name="book-notes" size="2xl" title="My Notes - {{ $userBook->title }}">
            <textarea
                x-data="{ notes: localStorage.getItem('user_notes_{{ $userBook->id }}') || '' }"
                x-model="notes"
                x-init="$watch('notes', value => localStorage.setItem('user_notes_{{ $userBook->id }}', value))"
                class="w-full h-64 p-4 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white resize-none"
                placeholder="Write your notes about this book here..."></textarea>

            <x-slot name="actions">
                <button @click="$dispatch('close-modal', {name: 'book-notes'})"
                        class="px-4 py-2 text-gray-700 dark:text-gray-300 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700">
                    Cancel
                </button>
                <button @click="saveNotes(); $dispatch('close-modal', {name: 'book-notes'})"
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    Save Notes
                </button>
            </x-slot>
        </x-modal-component>

        <!-- Mobile Sticky Action Bar -->
        <div
            class="lg:hidden fixed bottom-0 left-0 right-0 bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 p-4 z-40">
            <div class="flex space-x-3">
                @if($userBook->content_url)
                    <x-button.primary
                        onclick="Livewire.dispatch('openUserBookPDFReader', {bookId: {{ $userBook->id }}})"
                        class="px-4 py-3 flex w-full text-gray-600 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-xl">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                        </svg>
                        <span>Read Now</span>
                    </x-button.primary>
                @endif
                @if($userBook->sample_url)
                    <button @click="$dispatch('open-modal', {name: 'book-preview'})"
                            class="px-4 py-3 text-gray-600 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-xl">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                        </svg>
                    </button>
                @endif
                <button @click="toggleBookmark()" class="px-4 py-3 rounded-xl transition-colors duration-200"
                        :class="isBookmarked ? 'text-red-500 bg-red-50 dark:bg-red-900/20' : 'text-gray-600 dark:text-gray-300 bg-gray-100 dark:bg-gray-700'">
                    <svg class="w-5 h-5" :fill="isBookmarked ? 'currentColor' : 'none'" stroke="currentColor"
                         viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                    </svg>
                </button>
            </div>
        </div>

        @livewire('user-books.user-book-p-d-f-reader', ['bookId' => $userBook->id, 'config' => ['book' => $userBook]])
    </div>
</x-layouts.app>

