<x-layouts.app>

    <div x-data="{
    showImageModal: false,
    imageModalSrc: '',
    showNotification: false,
    notificationMessage: '',
    notificationType: 'success',
    init() {
        // Listen for browser events dispatched from Livewire
        window.addEventListener('notify', event => {
            this.notificationMessage = event.detail.message;
            this.notificationType = event.detail.type || 'success';
            this.showNotification = true;
            setTimeout(() => { this.showNotification = false }, 5000);
        });
    }
}"
         x-init="init()"
         class="min-h-screen rounded-lg bg-gray-50 dark:bg-gray-900/95">

        <div x-show="showNotification"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="transform opacity-0 translate-y-2"
             x-transition:enter-end="transform opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-300"
             x-transition:leave-start="transform opacity-100 translate-y-0"
             x-transition:leave-end="transform opacity-0 translate-y-2"
             class="fixed top-5 right-5 w-full max-w-xs z-[100000]"
             style="display: none;">
            <div class="p-4 rounded-xl shadow-2xl"
                 :class="{
                'bg-gradient-to-br from-green-500 to-emerald-600 text-white': notificationType === 'success',
                'bg-gradient-to-br from-red-500 to-rose-600 text-white': notificationType === 'error'
             }">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <svg x-show="notificationType === 'success'" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <svg x-show="notificationType === 'error'" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <div class="ml-3 w-0 flex-1 pt-0.5">
                        <p class="text-sm font-semibold" x-text="notificationMessage"></p>
                    </div>
                    <div class="ml-4 flex-shrink-0 flex">
                        <button @click="showNotification = false" class="inline-flex text-white/70 hover:text-white">
                            <span class="sr-only">Close</span>
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="fixed inset-0 overflow-hidden pointer-events-none -z-10">
            <div class="absolute -top-40 -right-40 w-80 h-80 bg-blue-400/10 rounded-full blur-3xl animate-pulse"></div>
            <div class="absolute -bottom-40 -left-40 w-80 h-80 bg-indigo-400/10 rounded-full blur-3xl animate-pulse delay-1000"></div>
        </div>

        <header class="sticky top-0 z-50 bg-white/70 dark:bg-gray-900/70 backdrop-blur-xl border-b border-gray-200/80 dark:border-gray-800/80">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-5">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <button onclick="history.back()" class="group inline-flex items-center px-3 py-2 text-sm font-medium text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white bg-white/50 dark:bg-gray-800/50 rounded-lg shadow-sm hover:shadow-md transition-all">
                            <svg class="w-5 h-5 mr-1.5 transform group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                            Back
                        </button>
                        <div class="hidden sm:block">
                            <h1 class="text-2xl font-bold bg-gradient-to-r from-gray-900 to-gray-700 dark:from-white dark:to-gray-300 bg-clip-text text-transparent">
                                {{ $book->title }}
                            </h1>
                            <p class="text-xs text-gray-500 dark:text-gray-400">by {{ $book->author->user->name }}</p>
                        </div>
                    </div>

                    @auth
                        @if(auth()->user()->hasRole('author'))
                            <div class="flex items-center space-x-3">
                                <a href="{{ route('author.books.edit', $book) }}" class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-blue-600 to-indigo-700 hover:from-blue-700 hover:to-indigo-800 text-white rounded-lg font-medium shadow-md hover:shadow-lg transform hover:-translate-y-0.5 transition-all">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                    Edit
                                </a>
                            </div>
                        @endif
                    @endauth
                </div>
            </div>
        </header>

        <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start">
                <div class="lg:col-span-2 space-y-8">
                    <div class="bg-white/60 dark:bg-gray-800/60 backdrop-blur-lg rounded-2xl shadow-lg p-6 border border-gray-200/50 dark:border-gray-700/50">
                        <div class="relative group">
                            <div class="aspect-w-16 aspect-h-9 max-h-[450px] rounded-xl overflow-hidden shadow-2xl">
                                <img src="{{ $book->cover_image }}" alt="{{ $book->title }}" class="w-full h-full object-cover transform group-hover:scale-105 transition-transform duration-500 cursor-pointer" @click="imageModalSrc = '{{ $book->cover_image }}'; showImageModal = true">
                            </div>
                            <div class="absolute inset-0 bg-gradient-to-t from-black/30 via-black/10 to-transparent rounded-xl pointer-events-none"></div>
                            <div class="absolute bottom-4 left-4">
                                <h2 class="text-4xl font-bold text-white drop-shadow-lg">{{ $book->title }}</h2>
                                <span class="inline-flex mt-2 items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/70 dark:text-blue-200">{{ $book->bookCategory->name }}</span>
                            </div>
                            @if($book->content_url)
                                <a href="{{route('books.read', $book)}}"
                                   class="absolute bottom-4 right-4 {{ $canRead ? 'bg-green-600 hover:bg-green-700' : 'bg-gray-500 cursor-not-allowed' }} text-white p-3 rounded-full shadow-lg {{ $canRead ? 'transform hover:scale-110 transition-all duration-200' : '' }}"
                                   {{ $canRead ? '' : 'disabled' }} aria-label="Read Book">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                                </a>
                            @endif
                        </div>
                    </div>

                    <div class="bg-white/60 dark:bg-gray-800/60 backdrop-blur-lg rounded-2xl shadow-lg p-8 border border-gray-200/50 dark:border-gray-700/50">
                        @auth
                            @if(auth()->user()->student)
                                @if($subscription && $subscription->status === 'active')
                                    <div class="bg-green-50 dark:bg-green-900/20 rounded-xl p-6">
                                        <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                                            <div class="flex items-center">
                                                <svg class="w-8 h-8 text-green-500 dark:text-green-400 mr-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                <div>
                                                    <h3 class="text-lg font-semibold text-green-800 dark:text-green-200">Subscription Active</h3>
                                                    <p class="text-sm text-green-600 dark:text-green-400">You have access until {{ $subscription->end_date->format('F d, Y') }}.</p>
                                                </div>
                                            </div>
                                            <button wire:click="openPdfReader" class="w-full sm:w-auto flex-shrink-0 bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg font-medium transition-colors shadow-md hover:shadow-lg transform hover:-translate-y-0.5">Start Reading</button>
                                        </div>
                                    </div>
                                @elseif($subscription && $subscription->status === 'pending_payment')
                                    <div class="bg-yellow-50 dark:bg-yellow-900/20 rounded-xl p-6">
                                        <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                                            <div class="flex items-center">
                                                <svg class="w-8 h-8 text-yellow-500 dark:text-yellow-400 mr-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.082 16.5c-.77.833.192 2.5 1.732 2.5z"></path></svg>
                                                <div>
                                                    <h3 class="text-lg font-semibold text-yellow-800 dark:text-yellow-200">Payment Pending</h3>
                                                    <p class="text-sm text-yellow-600 dark:text-yellow-400">Complete your payment to unlock this book.</p>
                                                </div>
                                            </div>
                                            <button wire:click="..." class="w-full sm:w-auto flex-shrink-0 bg-yellow-500 hover:bg-yellow-600 text-white px-6 py-3 rounded-lg font-medium transition-colors shadow-md hover:shadow-lg transform hover:-translate-y-0.5">Pay Now</button>
                                        </div>
                                    </div>
                                @else
                                    <div class="text-center">
                                        <h3 class="text-2xl font-bold text-gray-800 dark:text-white mb-2">Unlock Full Access</h3>
                                        <p class="text-gray-600 dark:text-gray-400 mb-6">Subscribe to read this book online.</p>
                                        <div class="bg-gray-100 dark:bg-gray-700/50 rounded-xl p-6 mb-6">
                                            <p class="text-sm text-gray-500 dark:text-gray-400">Annual Subscription</p>
                                            <p class="my-2">
                                                @if($book->is_free)
                                                    <span class="text-4xl font-extrabold text-green-600 dark:text-green-400">FREE</span>
                                                @else
                                                    <span class="text-4xl font-extrabold text-gray-800 dark:text-white">{{ $book->formatted_subscription_fee }}</span>
                                                @endif
                                            </p>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">Billed once per year</p>
                                        </div>
                                        <button wire:click="subscribeToBook" wire:loading.attr="disabled" class="w-full bg-gradient-to-r from-blue-600 to-indigo-700 hover:from-blue-700 hover:to-indigo-800 text-white font-bold px-8 py-4 rounded-xl shadow-lg hover:shadow-xl transform hover:-translate-y-1 transition-all duration-300 disabled:opacity-50 disabled:cursor-wait">
                                            <span wire:loading.remove wire:target="subscribeToBook">{{ $book->is_free ? 'Get Instant Access' : 'Subscribe & Pay' }}</span>
                                            <span wire:loading wire:target="subscribeToBook">Processing...</span>
                                        </button>
                                    </div>
                                @endif
                            @endif
                        @else
                            <div class="text-center">
                                <svg class="w-16 h-16 text-blue-500 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">Join to Read</h3>
                                <p class="text-gray-600 dark:text-gray-400 mb-4">Please log in or create an account to subscribe.</p>
                                <a href="{{ route('sign-in') }}" class="inline-flex items-center px-8 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors shadow-md hover:shadow-lg transform hover:-translate-y-0.5">Log In or Sign Up</a>
                            </div>
                        @endauth
                    </div>
                </div>

                <div class="lg:col-span-1 space-y-8" x-data="{ tab: 'details' }">
                    <div class="bg-white/60 dark:bg-gray-800/60 backdrop-blur-lg rounded-2xl shadow-lg border border-gray-200/50 dark:border-gray-700/50 overflow-hidden">
                        <div class="flex border-b border-gray-200/80 dark:border-gray-700/80">
                            <button @click="tab = 'details'"
                                    :class="tab === 'details' ? 'border-blue-500 text-blue-600 dark:text-blue-400' : 'border-transparent text-gray-500 hover:text-gray-700 dark:hover:text-gray-300'"
                                    class="flex-1 p-4 font-medium border-b-2 transition-colors focus:outline-none">
                                Details
                            </button>
                            @if(in_array(auth()->user()?->role, ['owner', 'author']))
                                <button @click="tab = 'statistics'"
                                        :class="tab === 'statistics' ? 'border-blue-500 text-blue-600 dark:text-blue-400' : 'border-transparent text-gray-500 hover:text-gray-700 dark:hover:text-gray-300'"
                                        class="flex-1 p-4 font-medium border-b-2 transition-colors focus:outline-none">
                                    Statistics
                                </button>
                            @endif
                        </div>

                        <div class="p-6">
                            <div x-show="tab === 'details'" x-transition>
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Book Information</h3>
                                <dl class="space-y-4">
                                    <div class="flex justify-between">
                                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Author</dt>
                                        <dd class="text-sm text-gray-900 dark:text-white">{{ $book->author->user->name }}</dd>
                                    </div>
                                    <div class="flex justify-between">
                                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Published</dt>
                                        <dd class="text-sm text-gray-900 dark:text-white">{{ $book->created_at->format('M d, Y') }}</dd>
                                    </div>
                                    <div class="flex justify-between">
                                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Pages</dt>
                                        <dd class="text-sm text-gray-900 dark:text-white">{{ $book->page_count ?? 'N/A' }}</dd>
                                    </div>
                                    <div class="flex justify-between">
                                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Language</dt>
                                        <dd class="text-sm text-gray-900 dark:text-white">English</dd>
                                    </div>
                                    <div class="flex justify-between">
                                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Hard Copy</dt>
                                        <dd class="text-sm">@if($book->has_hardcopy) <span class="px-2 py-1 text-xs font-semibold text-green-800 bg-green-100 dark:bg-green-900 dark:text-green-200 rounded-full">Available</span> @else <span class="px-2 py-1 text-xs font-semibold text-red-800 bg-red-100 dark:bg-red-900 dark:text-red-200 rounded-full">Unavailable</span> @endif</dd>
                                    </div>
                                </dl>
                            </div>

                            @if(in_array(auth()->user()?->role, ['owner', 'author']))
                                <div x-show="tab === 'statistics'" x-transition>
                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Performance Metrics</h3>
                                    <div class="space-y-4">
                                        <div class="p-4 rounded-xl bg-gradient-to-r from-blue-50 to-indigo-100 dark:from-blue-900/30 dark:to-indigo-900/30">
                                            <p class="text-sm font-medium text-blue-800 dark:text-blue-300">Active Subscriptions</p>
                                            <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ $book->subscriptions_count ?? 0 }}</p>
                                        </div>
                                        <div class="p-4 rounded-xl bg-gradient-to-r from-green-50 to-emerald-100 dark:from-green-900/30 dark:to-emerald-900/30">
                                            <p class="text-sm font-medium text-green-800 dark:text-green-300">Total Borrowings</p>
                                            <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ $book->borrowings_count ?? 0 }}</p>
                                        </div>
                                        <div class="p-4 rounded-xl bg-gradient-to-r from-purple-50 to-pink-100 dark:from-purple-900/30 dark:to-pink-900/30">
                                            <p class="text-sm font-medium text-purple-800 dark:text-purple-300">Est. Revenue</p>
                                            <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">GHS {{ number_format(($book->subscriptions_count ?? 0) * $book->annual_subscription_fee, 2) }}</p>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</x-layouts.app>
