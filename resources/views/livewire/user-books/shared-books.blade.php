<section>
    <div
        class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-50 dark:from-gray-900 dark:via-slate-900 dark:to-indigo-950">
        <!-- Main Content Card -->
        <div
            class="relative bg-gradient-to-r from-white via-gray-50 to-white dark:from-gray-800 dark:via-gray-850 dark:to-gray-800 border-b border-gray-200 dark:border-gray-700">
            <div class="flex overflow-x-auto no-scrollbar">
                <!-- My Books Tab -->
                <button
                    wire:click="setActiveTab('my-books')"
                    class="relative group flex-1 min-w-[200px] py-6 px-6 font-bold text-sm transition-all duration-300 ease-out
                        {{ $activeTab === 'my-books'
                            ? 'text-purple-700 dark:text-purple-400'
                            : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200' }}">

                    <div class="flex items-center justify-center space-x-3">
                        <div class="relative">
                            <div class="w-10 h-10 rounded-xl flex items-center justify-center transition-all duration-300
                                    {{ $activeTab === 'my-books'
                                        ? 'bg-gradient-to-br from-purple-500 to-purple-600 shadow-lg shadow-purple-500/50 scale-110'
                                        : 'bg-gray-200 dark:bg-gray-700 group-hover:bg-gray-300 dark:group-hover:bg-gray-600' }}">
                                <svg
                                    class="w-5 h-5 {{ $activeTab === 'my-books' ? 'text-white' : 'text-gray-600 dark:text-gray-400' }}"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                </svg>
                            </div>
                            @if($myBooks->count() > 0)
                                <div
                                    class="absolute -top-1 -right-1 w-5 h-5 bg-red-500 rounded-full flex items-center justify-center text-[10px] font-bold text-white border-2 border-white dark:border-gray-800 animate-pulse">
                                    {{ $myBooks->count() }}
                                </div>
                            @endif
                        </div>
                        <div class="text-left">
                            <div class="font-bold text-base">My Books</div>
                            <div
                                class="text-xs {{ $activeTab === 'my-books' ? 'text-purple-600 dark:text-purple-400' : 'text-gray-500 dark:text-gray-500' }}">
                                Personal collection
                            </div>
                        </div>
                    </div>

                    <!-- Active Indicator -->
                    <div class="absolute bottom-0 left-0 right-0 h-1 transition-all duration-300
                            {{ $activeTab === 'my-books'
                                ? 'bg-gradient-to-r from-purple-500 via-purple-600 to-purple-500 shadow-lg shadow-purple-500/50'
                                : 'bg-transparent' }}">
                    </div>
                </button>

                <!-- Pending Requests Tab -->
                <button
                    wire:click="setActiveTab('pending')"
                    class="relative group flex-1 min-w-[200px] py-6 px-6 font-bold text-sm transition-all duration-300 ease-out
                        {{ $activeTab === 'pending'
                            ? 'text-amber-700 dark:text-amber-400'
                            : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200' }}">

                    <div class="flex items-center justify-center space-x-3">
                        <div class="relative">
                            <div class="w-10 h-10 rounded-xl flex items-center justify-center transition-all duration-300
                                    {{ $activeTab === 'pending'
                                        ? 'bg-gradient-to-br from-amber-500 to-orange-600 shadow-lg shadow-amber-500/50 scale-110'
                                        : 'bg-gray-200 dark:bg-gray-700 group-hover:bg-gray-300 dark:group-hover:bg-gray-600' }}">
                                <svg
                                    class="w-5 h-5 {{ $activeTab === 'pending' ? 'text-white' : 'text-gray-600 dark:text-gray-400' }}"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            @if($pendingShares->count() > 0)
                                <div
                                    class="absolute -top-1 -right-1 w-5 h-5 bg-red-500 rounded-full flex items-center justify-center text-[10px] font-bold text-white border-2 border-white dark:border-gray-800 animate-bounce">
                                    {{ $pendingShares->count() }}
                                </div>
                            @endif
                        </div>
                        <div class="text-left">
                            <div class="font-bold text-base">Pending</div>
                            <div
                                class="text-xs {{ $activeTab === 'pending' ? 'text-amber-600 dark:text-amber-400' : 'text-gray-500 dark:text-gray-500' }}">
                                Awaiting action
                            </div>
                        </div>
                    </div>

                    <div class="absolute bottom-0 left-0 right-0 h-1 transition-all duration-300
                            {{ $activeTab === 'pending'
                                ? 'bg-gradient-to-r from-amber-500 via-orange-600 to-amber-500 shadow-lg shadow-amber-500/50'
                                : 'bg-transparent' }}">
                    </div>
                </button>

                <!-- Shared with Me Tab -->
                <button
                    wire:click="setActiveTab('accepted')"
                    class="relative group flex-1 min-w-[200px] py-6 px-6 font-bold text-sm transition-all duration-300 ease-out
                        {{ $activeTab === 'accepted'
                            ? 'text-emerald-700 dark:text-emerald-400'
                            : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200' }}">

                    <div class="flex items-center justify-center space-x-3">
                        <div class="relative">
                            <div class="w-10 h-10 rounded-xl flex items-center justify-center transition-all duration-300
                                    {{ $activeTab === 'accepted'
                                        ? 'bg-gradient-to-br from-emerald-500 to-green-600 shadow-lg shadow-emerald-500/50 scale-110'
                                        : 'bg-gray-200 dark:bg-gray-700 group-hover:bg-gray-300 dark:group-hover:bg-gray-600' }}">
                                <svg
                                    class="w-5 h-5 {{ $activeTab === 'accepted' ? 'text-white' : 'text-gray-600 dark:text-gray-400' }}"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                </svg>
                            </div>
                            @if($acceptedShares->count() > 0)
                                <div
                                    class="absolute -top-1 -right-1 w-5 h-5 bg-emerald-500 rounded-full flex items-center justify-center text-[10px] font-bold text-white border-2 border-white dark:border-gray-800">
                                    {{ $acceptedShares->count() }}
                                </div>
                            @endif
                        </div>
                        <div class="text-left">
                            <div class="font-bold text-base">Shared</div>
                            <div
                                class="text-xs {{ $activeTab === 'accepted' ? 'text-emerald-600 dark:text-emerald-400' : 'text-gray-500 dark:text-gray-500' }}">
                                From friends
                            </div>
                        </div>
                    </div>

                    <div class="absolute bottom-0 left-0 right-0 h-1 transition-all duration-300
                            {{ $activeTab === 'accepted'
                                ? 'bg-gradient-to-r from-emerald-500 via-green-600 to-emerald-500 shadow-lg shadow-emerald-500/50'
                                : 'bg-transparent' }}">
                    </div>
                </button>
            </div>
        </div>

        <div class="pt-6 px-6">

            <!-- Elegant Loading Indicator -->
            <div wire:loading class="flex flex-col items-center justify-center py-20">
                <div class="relative w-20 h-20 mb-6">
                    <div
                        class="absolute inset-0 border-4 border-purple-200 dark:border-purple-900 rounded-full"></div>
                    <div
                        class="absolute inset-0 border-4 border-transparent border-t-purple-600 dark:border-t-purple-400 rounded-full animate-spin"></div>
                    <div
                        class="absolute inset-2 border-4 border-transparent border-t-blue-600 dark:border-t-blue-400 rounded-full animate-spin animation-delay-150"></div>
                </div>
                <p class="text-lg font-semibold text-gray-700 dark:text-gray-300 animate-pulse">Loading your
                    library...</p>
            </div>

            <!-- MY BOOKS TAB CONTENT -->
            @if($activeTab === 'my-books')
                @if($myBooks->isEmpty())
                    <!-- Empty State - My Books -->
                    <div class="text-center py-20">
                        <div class="relative inline-flex mb-8">
                            <div
                                class="absolute inset-0 bg-purple-200 dark:bg-purple-900/30 rounded-full blur-3xl opacity-50 animate-pulse"></div>
                            <div
                                class="relative w-32 h-32 rounded-full bg-gradient-to-br from-purple-100 to-purple-200 dark:from-purple-900/50 dark:to-purple-800/50 flex items-center justify-center">
                                <svg class="w-16 h-16 text-purple-600 dark:text-purple-400" fill="none"
                                     viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                          d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                </svg>
                            </div>
                        </div>
                        <h3 class="text-3xl font-bold text-gray-900 dark:text-white mb-3">Your Library
                            Awaits</h3>
                        <p class="text-gray-600 dark:text-gray-400 mb-8 max-w-md mx-auto text-lg">
                            Start building your digital library by uploading your first book and sharing it with
                            the world
                        </p>
                        <a href="{{ route('user-books.create') }}"
                           class="inline-flex items-center px-8 py-4 bg-gradient-to-r from-purple-600 via-purple-700 to-indigo-700 text-white font-bold rounded-2xl shadow-xl hover:shadow-2xl hover:scale-105 transform transition-all duration-300 group">
                            <svg class="w-6 h-6 mr-3 group-hover:rotate-90 transition-transform duration-300"
                                 fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                      d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z"
                                      clip-rule="evenodd"/>
                            </svg>
                            Upload Your First Book
                        </a>
                    </div>
                @else
                    <!-- Books Grid -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-3 gap-6">
                        @foreach($myBooks as $book)
                            <div
                                class="group relative bg-white dark:bg-gray-800 rounded-2xl overflow-hidden border border-gray-200 dark:border-gray-700 hover:border-purple-300 dark:hover:border-purple-700 shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2">
                                <!-- Book Cover with Overlay -->
                                <div
                                    class="relative aspect-[4/3] overflow-hidden bg-gradient-to-br from-gray-100 to-gray-200 dark:from-gray-700 dark:to-gray-800">
                                    @if($book->cover_image)
                                        <img
                                            class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500"
                                            src="{{ asset('storage/' . $book->cover_image) }}"
                                            alt="{{ $book->title }}">
                                    @else
                                        <div
                                            class="w-full h-full flex items-center justify-center bg-gradient-to-br from-purple-200 via-purple-300 to-indigo-300 dark:from-purple-900 dark:via-purple-800 dark:to-indigo-900">
                                            <svg class="w-24 h-24 text-purple-600/30 dark:text-purple-400/30"
                                                 fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                      stroke-width="1"
                                                      d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                            </svg>
                                        </div>
                                    @endif

                                    <!-- Gradient Overlay -->
                                    <div
                                        class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>

                                    <!-- Status Badge -->
                                    <div class="absolute top-3 right-3">
                                            <span class="px-3 py-1.5 rounded-xl text-xs font-bold backdrop-blur-md shadow-lg
                                                @if($book->status === 'published')
                                                    bg-emerald-500/90 text-white ring-2 ring-white/50
                                                @elseif($book->status === 'draft')
                                                    bg-amber-500/90 text-white ring-2 ring-white/50
                                                @else
                                                    bg-gray-500/90 text-white ring-2 ring-white/50
                                                @endif">
                                                {{ ucfirst($book->status) }}
                                            </span>
                                    </div>

                                    <!-- Quick Actions Overlay -->
                                    <div
                                        class="absolute inset-x-0 bottom-0 p-4 translate-y-full group-hover:translate-y-0 transition-transform duration-300">
                                        <div class="flex gap-2">
                                            @if($book->content_url)
                                                <a href="{{ route('user-books.show', $book) }}"
                                                   class="flex-1 px-4 py-2.5 bg-white/95 hover:bg-white text-purple-700 font-bold rounded-xl text-sm text-center transition-all duration-200 shadow-lg">
                                                    Read Now
                                                </a>
                                            @endif
                                            <a href="{{ route('user-books.edit', $book) }}"
                                               class="flex-1 px-4 py-2.5 bg-gray-800/95 hover:bg-gray-900 text-white font-bold rounded-xl text-sm text-center transition-all duration-200 shadow-lg">
                                                Edit
                                            </a>
                                        </div>
                                    </div>
                                </div>

                                <!-- Book Info -->
                                <div class="p-5">
                                    <h4 class="font-bold text-gray-900 dark:text-white text-lg mb-2 line-clamp-2 group-hover:text-purple-600 dark:group-hover:text-purple-400 transition-colors">
                                        {{ $book->title }}
                                    </h4>

                                    <div
                                        class="flex items-center justify-between text-sm text-gray-600 dark:text-gray-400 mb-4">
                                        <div class="flex items-center space-x-1">
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                <path
                                                    d="M15 8a3 3 0 10-2.977-2.63l-4.94 2.47a3 3 0 100 4.319l4.94 2.47a3 3 0 10.895-1.789l-4.94-2.47a3.027 3.027 0 000-.74l4.94-2.47C13.456 7.68 14.19 8 15 8z"/>
                                            </svg>
                                            <span class="font-semibold">{{ $book->shares_count }}</span>
                                            <span>shares</span>
                                        </div>
                                        <div class="flex items-center space-x-1">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                 viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                      stroke-width="2"
                                                      d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                            </svg>
                                            <span class="font-semibold">{{ $book->pages ?? 'N/A' }}</span>
                                        </div>
                                    </div>

                                    <div
                                        class="pt-4 border-t border-gray-200 dark:border-gray-700 text-xs text-gray-500 dark:text-gray-500">
                                        <div class="flex items-center justify-between">
                                            <span>Added {{ $book->created_at->diffForHumans() }}</span>
                                            <span class="text-gray-400">•</span>
                                            <span>{{ $book->created_at->format('M d, Y') }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            @endif

            <!-- PENDING SHARES TAB CONTENT -->
            @if($activeTab === 'pending')
                @if($pendingShares->isEmpty())
                    <!-- Empty State - Pending -->
                    <div class="text-center py-20">
                        <div class="relative inline-flex mb-8">
                            <div
                                class="absolute inset-0 bg-amber-200 dark:bg-amber-900/30 rounded-full blur-3xl opacity-50 animate-pulse"></div>
                            <div
                                class="relative w-32 h-32 rounded-full bg-gradient-to-br from-amber-100 to-orange-200 dark:from-amber-900/50 dark:to-orange-800/50 flex items-center justify-center">
                                <svg class="w-16 h-16 text-amber-600 dark:text-amber-400" fill="none"
                                     viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                          d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                        </div>
                        <h3 class="text-3xl font-bold text-gray-900 dark:text-white mb-3">All Caught Up!</h3>
                        <p class="text-gray-600 dark:text-gray-400 max-w-md mx-auto text-lg">
                            You have no pending book share requests at the moment
                        </p>
                    </div>
                @else
                    <!-- Pending Shares Grid -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($pendingShares as $share)
                            <div
                                class="group relative bg-gradient-to-br from-amber-50 to-orange-50 dark:from-amber-900/10 dark:to-orange-900/10 rounded-2xl overflow-hidden border-2 border-amber-200 dark:border-amber-800 hover:border-amber-400 dark:hover:border-amber-600 shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">

                                <!-- Pending Pulse Indicator -->
                                <div class="absolute top-4 left-4 z-10">
                                    <div class="relative">
                                        <div
                                            class="w-3 h-3 bg-amber-500 rounded-full animate-ping absolute"></div>
                                        <div class="w-3 h-3 bg-amber-600 rounded-full relative"></div>
                                    </div>
                                </div>

                                <div
                                    class="aspect-[3/4] overflow-hidden bg-gradient-to-br from-amber-100 to-orange-100 dark:from-amber-900/30 dark:to-orange-900/30 relative">
                                    @if($share->userBook->cover_image)
                                        <img
                                            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                                            src="{{ asset('storage/' . $share->userBook->cover_image) }}"
                                            alt="{{ $share->userBook->title }}">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center">
                                            <svg class="w-24 h-24 text-amber-400/40 dark:text-amber-600/40"
                                                 fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                      stroke-width="1"
                                                      d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                            </svg>
                                        </div>
                                    @endif

                                    <div
                                        class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>

                                    <div class="absolute bottom-3 left-3 right-3">
                                            <span
                                                class="inline-block px-3 py-1.5 bg-amber-500/95 text-white text-xs font-bold rounded-lg backdrop-blur-sm animate-pulse">
                                                Awaiting Response
                                            </span>
                                    </div>
                                </div>

                                <div class="p-5">
                                    <h4 class="font-bold text-gray-900 dark:text-white text-lg mb-3 line-clamp-2">
                                        {{ $share->userBook->title }}
                                    </h4>

                                    <div
                                        class="flex items-center space-x-2 text-sm text-gray-700 dark:text-gray-300 mb-5 bg-white/50 dark:bg-gray-800/50 rounded-lg p-3">
                                        <div
                                            class="w-8 h-8 rounded-full bg-gradient-to-br from-amber-400 to-orange-500 flex items-center justify-center text-white font-bold text-xs">
                                            {{ substr($share->sharedBy->name, 0, 1) }}
                                        </div>
                                        <div class="flex-1">
                                            <div class="font-semibold text-xs text-gray-500 dark:text-gray-400">
                                                Shared by
                                            </div>
                                            <div class="font-bold">{{ $share->sharedBy->name }}</div>
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-2 gap-2 mb-4">
                                        <button
                                            wire:click="acceptShare({{ $share->id }})"
                                            wire:loading.attr="disabled"
                                            wire:loading.class="opacity-50 cursor-not-allowed"
                                            class="flex items-center justify-center px-4 py-3 bg-gradient-to-r from-emerald-600 to-green-600 hover:from-emerald-700 hover:to-green-700 text-white font-bold rounded-xl transition-all duration-200 shadow-lg hover:shadow-xl transform hover:scale-105 disabled:transform-none">
                                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor"
                                                 viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                      stroke-width="2" d="M5 13l4 4L19 7"/>
                                            </svg>
                                            Accept
                                        </button>
                                        <button
                                            wire:click="declineShare({{ $share->id }})"
                                            wire:confirm="Are you sure you want to decline this book share?"
                                            wire:loading.attr="disabled"
                                            wire:loading.class="opacity-50 cursor-not-allowed"
                                            class="flex items-center justify-center px-4 py-3 bg-gray-200 dark:bg-gray-700 hover:bg-red-100 dark:hover:bg-red-900/30 text-gray-700 dark:text-gray-300 hover:text-red-700 dark:hover:text-red-400 font-bold rounded-xl transition-all duration-200 shadow-lg transform hover:scale-105 disabled:transform-none">
                                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor"
                                                 viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                      stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                            </svg>
                                            Decline
                                        </button>
                                    </div>

                                    <div
                                        class="pt-3 border-t border-amber-200 dark:border-amber-800 text-xs text-gray-600 dark:text-gray-400 flex items-center justify-between">
                                        <span>Requested {{ $share->created_at->diffForHumans() }}</span>
                                        <svg class="w-4 h-4 text-amber-500" fill="currentColor"
                                             viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                  d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z"
                                                  clip-rule="evenodd"/>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            @endif

            <!-- ACCEPTED SHARES TAB CONTENT -->
            @if($activeTab === 'accepted')
                @if($acceptedShares->isEmpty())
                    <!-- Empty State - Accepted -->
                    <div class="text-center py-20">
                        <div class="relative inline-flex mb-8">
                            <div
                                class="absolute inset-0 bg-emerald-200 dark:bg-emerald-900/30 rounded-full blur-3xl opacity-50 animate-pulse"></div>
                            <div
                                class="relative w-32 h-32 rounded-full bg-gradient-to-br from-emerald-100 to-green-200 dark:from-emerald-900/50 dark:to-green-800/50 flex items-center justify-center">
                                <svg class="w-16 h-16 text-emerald-600 dark:text-emerald-400" fill="none"
                                     viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                          d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                </svg>
                            </div>
                        </div>
                        <h3 class="text-3xl font-bold text-gray-900 dark:text-white mb-3">No Shared Books
                            Yet</h3>
                        <p class="text-gray-600 dark:text-gray-400 max-w-md mx-auto text-lg">
                            Accept book share requests from friends to see them here
                        </p>
                    </div>
                @else
                    <!-- Accepted Shares Grid -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                        @foreach($acceptedShares as $share)
                            <div
                                class="group relative bg-white dark:bg-gray-800 rounded-2xl overflow-hidden border border-emerald-200 dark:border-emerald-900 hover:border-emerald-400 dark:hover:border-emerald-600 shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2">

                                <div
                                    class="relative aspect-[3/4] overflow-hidden bg-gradient-to-br from-emerald-100 to-green-100 dark:from-emerald-900/30 dark:to-green-900/30">
                                    @if($share->userBook->cover_image)
                                        <img
                                            class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500"
                                            src="{{ asset('storage/' . $share->userBook->cover_image) }}"
                                            alt="{{ $share->userBook->title }}">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center">
                                            <svg class="w-24 h-24 text-emerald-400/40 dark:text-emerald-600/40"
                                                 fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                      stroke-width="1"
                                                      d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                            </svg>
                                        </div>
                                    @endif

                                    <div
                                        class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>

                                    <div class="absolute top-3 right-3">
                                            <span
                                                class="px-3 py-1.5 rounded-xl text-xs font-bold bg-emerald-500/95 text-white ring-2 ring-white/50 backdrop-blur-sm shadow-lg">
                                                Shared Book
                                            </span>
                                    </div>

                                    <!-- Quick Read Button Overlay -->
                                    <div
                                        class="absolute inset-x-0 bottom-0 p-4 translate-y-full group-hover:translate-y-0 transition-transform duration-300">
                                        <a href="{{ route('user-books.show', $share->userBook) }}"
                                           class="block w-full px-4 py-3 bg-white/95 hover:bg-white text-emerald-700 font-bold rounded-xl text-center transition-all duration-200 shadow-xl">
                                                <span class="flex items-center justify-center">
                                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor"
                                                         viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                              stroke-width="2"
                                                              d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                                    </svg>
                                                    Start Reading
                                                </span>
                                        </a>
                                    </div>
                                </div>

                                <div class="p-5">
                                    <h4 class="font-bold text-gray-900 dark:text-white text-lg mb-3 line-clamp-2 group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition-colors">
                                        {{ $share->userBook->title }}
                                    </h4>

                                    <div
                                        class="flex items-center space-x-2 text-sm text-gray-700 dark:text-gray-300 mb-4 bg-emerald-50/50 dark:bg-emerald-900/20 rounded-lg p-3">
                                        <div
                                            class="w-8 h-8 rounded-full bg-gradient-to-br from-emerald-400 to-green-500 flex items-center justify-center text-white font-bold text-xs">
                                            {{ substr($share->sharedBy->name, 0, 1) }}
                                        </div>
                                        <div class="flex-1">
                                            <div class="font-semibold text-xs text-gray-500 dark:text-gray-400">
                                                Shared by
                                            </div>
                                            <div class="font-bold">{{ $share->sharedBy->name }}</div>
                                        </div>
                                    </div>

                                    <div
                                        class="flex items-center justify-between text-sm text-gray-600 dark:text-gray-400 mb-4">
                                        <div class="flex items-center space-x-1">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                 viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                      stroke-width="2"
                                                      d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                            </svg>
                                            <span
                                                class="font-semibold">{{ $share->userBook->pages ?? 'N/A' }}</span>
                                            <span>pages</span>
                                        </div>
                                    </div>

                                    <div
                                        class="pt-3 border-t border-gray-200 dark:border-gray-700 text-xs text-gray-500 dark:text-gray-500">
                                        <div class="flex items-center justify-between">
                                            <span>Shared {{ $share->created_at->diffForHumans() }}</span>
                                            <span class="text-gray-400">•</span>
                                            <span>{{ $share->created_at->format('M d, Y') }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            @endif
        </div>
    </div>

    <style>
        @keyframes animation-delay-150 {
            0% {
                transform: rotate(0deg);
            }
            100% {
                transform: rotate(360deg);
            }
        }

        .animation-delay-150 {
            animation-delay: 150ms;
        }

        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }

        .no-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }

        @media (prefers-reduced-motion: reduce) {
            * {
                animation-duration: 0.01ms !important;
                animation-iteration-count: 1 !important;
                transition-duration: 0.01ms !important;
            }
        }
    </style>
</section>
