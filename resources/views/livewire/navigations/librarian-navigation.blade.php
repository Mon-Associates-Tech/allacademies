<div>
    <h3 class="text-xs uppercase text-gray-400 dark:text-gray-500 font-semibold pl-3">
        <span class="hidden lg:hidden lg:sidebar-expanded:hidden 2xl:hidden text-center w-6"
              aria-hidden="true">•••</span>
        <span class="lg:hidden lg:sidebar-expanded:block 2xl:block">Library Management</span>
    </h3>
    <ul class="mt-3">
        <!-- Dashboard -->
        <li class="mb-0.5 last:mb-0">
           <a class="block px-3 py-2 rounded-lg transition {{ Route::is('librarian.dashboard') ? 'bg-violet-500 text-white font-bold' : 'text-gray-800 dark:text-gray-100 hover:bg-gray-200 dark:hover:bg-gray-700' }}"
               href="{{ route('librarian.dashboard') }}">
                <div class="flex items-center">
                    <svg
                        class="shrink-0 fill-current {{ Route::is('librarian.dashboard') ? 'text-white' : 'text-gray-400 dark:text-gray-500' }}"
                        width="16" height="16" viewBox="0 0 24 24">
                        <path d="M3 13h8V3H3v10zm0 8h8v-6H3v6zm10 0h8V11h-8v10zm0-18v6h8V3h-8z"/>
                    </svg>
                    <span class="text-sm ml-2 sidebar-text duration-200">Dashboard</span>
                </div>
            </a>
        </li>
        <!-- Academic Chat -->
        <li class="mb-0.5 last:mb-0" title="Research Assistant">
           <a class="block px-3 py-2 rounded-lg transition {{ Route::is('academic-chat*') ? 'bg-violet-500 text-white font-bold' : 'text-gray-800 dark:text-gray-100 hover:bg-gray-200 dark:hover:bg-gray-700' }}"
               href="{{route('research-assistant.index')}}">
                <div class="flex items-center">
                    <!-- Chat bubble with spark -->
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="16" height="16" fill="none"
                         stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                         class="shrink-0 {{ Route::is('academic-chat*') ? 'text-white' : 'text-gray-400 dark:text-gray-500' }}">
                        <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h10"/>
                        <path d="M17 3l1.5 3L22 8l-3.5 2L17 13l-1.5-3L12 8l3.5-2L17 3z"/>
                    </svg>
                    <span class="text-sm ml-2 sidebar-text duration-200">Research Assistant</span>
                </div>
            </a>
        </li>
        <!-- Books Management -->
        <li class="mb-0.5 last:mb-0">
           <a class="block px-3 py-2 rounded-lg transition {{ Route::is('librarian.books*') ? 'bg-violet-500 text-white font-bold' : 'text-gray-800 dark:text-gray-100 hover:bg-gray-200 dark:hover:bg-gray-700' }}"
               href="{{ route('librarian.books') }}">
                <div class="flex items-center">
                    <svg
                        class="shrink-0 fill-current {{ Route::is('librarian.books*') ? 'text-white' : 'text-gray-400 dark:text-gray-500' }}"
                        width="16" height="16" viewBox="0 0 24 24">
                        <path
                            d="M21 5c-1.11-.35-2.33-.5-3.5-.5-1.95 0-4.05.4-5.5 1.5-1.45-1.1-3.55-1.5-5.5-1.5S2.45 4.9 1 6v14.65c0 .25.25.5.5.5.1 0 .15-.05.25-.05C3.1 20.45 5.05 20 6.5 20c1.95 0 4.05.4 5.5 1.5 1.35-.85 3.8-1.5 5.5-1.5 1.65 0 3.35.3 4.75 1.05.1.05.15.05.25.05.25 0 .5-.25.5-.5V6c-.6-.45-1.25-.75-2-1zm0 13.5c-1.1-.35-2.3-.5-3.5-.5-1.7 0-4.15.65-5.5 1.5V8c1.35-.85 3.8-1.5 5.5-1.5 1.2 0 2.4.15 3.5.5v11.5z"/>
                    </svg>
                    <span class="text-sm ml-2 sidebar-text duration-200">Books Catalog</span>
                </div>
            </a>
        </li>

        <!-- Book Inventory -->
        <li class="mb-0.5 last:mb-0">
           <a class="block px-3 py-2 rounded-lg transition {{ Route::is('librarian.inventory') ? 'bg-violet-500 text-white font-bold' : 'text-gray-800 dark:text-gray-100 hover:bg-gray-200 dark:hover:bg-gray-700' }}"
               href="{{ route('librarian.inventory') }}">
                <div class="flex items-center">
                    <svg
                        class="shrink-0 fill-current {{ Route::is('librarian.inventory') ? 'text-white' : 'text-gray-400 dark:text-gray-500' }}"
                        width="16" height="16" viewBox="0 0 24 24">
                        <path
                            d="M20 6h-2.18c.11-.31.18-.65.18-1a2.996 2.996 0 00-5.5-1.65l-.5.67-.5-.68C10.96 2.54 10.05 2 9 2 7.34 2 6 3.34 6 5c0 .35.07.69.18 1H4c-1.11 0-1.99.89-1.99 2L2 19c0 1.11.89 2 2 2h16c1.11 0 2-.89 2-2V8c0-1.11-.89-2-2-2zM9 4c.55 0 1 .45 1 1s-.45 1-1 1-1-.45-1-1 .45-1 1-1zm6 0c.55 0 1 .45 1 1s-.45 1-1 1-1-.45-1-1 .45-1 1-1z"/>
                    </svg>
                    <span class="text-sm ml-2 sidebar-text duration-200">Book Inventory</span>
                </div>
            </a>
        </li>

        <!-- Borrowing Management -->
        <li class="mb-0.5 last:mb-0">
           <a class="block px-3 py-2 rounded-lg transition {{ Route::is('librarian.book-requests') ? 'bg-violet-500 text-white font-bold' : 'text-gray-800 dark:text-gray-100 hover:bg-gray-200 dark:hover:bg-gray-700' }}"
               href="{{ route('librarian.book-requests') }}">
                <div class="flex items-center">
                    <svg
                        class="shrink-0 fill-current {{ Route::is('librarian.book-requests') ? 'text-white' : 'text-gray-400 dark:text-gray-500' }}"
                        width="16" height="16" viewBox="0 0 24 24">
                        <path
                            d="M14 2H6c-1.1 0-2 .9-2 2v16c0 1.1.89 2 2 2h12c1.1 0 2-.9 2-2V8l-6-6zm4 18H6V4h7v5h5v11z"/>
                    </svg>
                    <span class="text-sm ml-2 sidebar-text duration-200">Borrowing Requests</span>
                </div>
            </a>
        </li>

        <!-- Borrowed Books -->
        <li class="mb-0.5 last:mb-0">
           <a class="block px-3 py-2 rounded-lg transition {{ Route::is('librarian.borrowed-books') ? 'bg-violet-500 text-white font-bold' : 'text-gray-800 dark:text-gray-100 hover:bg-gray-200 dark:hover:bg-gray-700' }}"
               href="{{ route('librarian.borrowed-books') }}">
                <div class="flex items-center">
                    <svg
                        class="shrink-0 fill-current {{ Route::is('librarian.borrowed-books') ? 'text-white' : 'text-gray-400 dark:text-gray-500' }}"
                        width="16" height="16" viewBox="0 0 24 24">
                        <path
                            d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                    </svg>
                    <span class="text-sm ml-2 sidebar-text duration-200">Borrowed Books</span>
                </div>
            </a>
        </li>

        <!-- Book Returns -->
        <li class="mb-0.5 last:mb-0">
           <a class="block px-3 py-2 rounded-lg transition {{ Route::is('librarian.book-returns') ? 'bg-violet-500 text-white font-bold' : 'text-gray-800 dark:text-gray-100 hover:bg-gray-200 dark:hover:bg-gray-700' }}"
               href="{{ route('librarian.book-returns') }}">
                <div class="flex items-center">
                    <svg
                        class="shrink-0 fill-current {{ Route::is('librarian.book-returns') ? 'text-white' : 'text-gray-400 dark:text-gray-500' }}"
                        width="16" height="16" viewBox="0 0 24 24">
                        <path d="M9 11H7l3-3 3 3h-2v8h-2v-8zm4-6.5L9.5 0h-3L10 4.5H6.5L10 9h4L10.5 4.5z"/>
                    </svg>
                    <span class="text-sm ml-2 sidebar-text duration-200">Book Returns</span>
                </div>
            </a>
        </li>

        <!-- Overdue Books -->
        <li class="mb-0.5 last:mb-0">
           <a class="block px-3 py-2 rounded-lg transition {{ Route::is('librarian.overdue-books') ? 'bg-violet-500 text-white font-bold' : 'text-gray-800 dark:text-gray-100 hover:bg-gray-200 dark:hover:bg-gray-700' }}"
               href="{{ route('librarian.overdue-books') }}">
                <div class="flex items-center">
                    <svg
                        class="shrink-0 fill-current {{ Route::is('librarian.overdue-books') ? 'text-white' : 'text-gray-400 dark:text-gray-500' }}"
                        width="16" height="16" viewBox="0 0 24 24">
                        <path
                            d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                    </svg>
                    <span class="text-sm ml-2 sidebar-text duration-200">Overdue Books</span>
                    @if(isset($overdueCount) && $overdueCount > 0)
                        <span
                            class="ml-2 inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300">
                            {{ $overdueCount }}
                        </span>
                    @endif
                </div>
            </a>
        </li>

        <!-- Student Profiles -->
        <li class="mb-0.5 last:mb-0">
           <a class="block px-3 py-2 rounded-lg transition {{ Route::is('librarian.student-profiles') ? 'bg-violet-500 text-white font-bold' : 'text-gray-800 dark:text-gray-100 hover:bg-gray-200 dark:hover:bg-gray-700' }}"
               href="{{ route('librarian.student-profiles') }}">
                <div class="flex items-center">
                    <svg
                        class="shrink-0 fill-current {{ Route::is('librarian.student-profiles') ? 'text-white' : 'text-gray-400 dark:text-gray-500' }}"
                        width="16" height="16" viewBox="0 0 24 24">
                        <path
                            d="M16 4c0-1.11.89-2 2-2s2 .89 2 2-.89 2-2 2-2-.89-2-2zm4 18v-6h2.5l-2.54-7.63A3.01 3.01 0 0018 6c-.35 0-.69.07-1 .18V4c0-2.21-1.79-4-4-4s-4 1.79-4 4v2.18c-.31-.11-.65-.18-1-.18-1.3 0-2.4.84-2.82 2L2.5 16H5v6h2v-6h2v6h2v-6h2v6h2v-6h2v6h2z"/>
                    </svg>
                    <span class="text-sm ml-2 sidebar-text duration-200">Student Profiles</span>
                </div>
            </a>
        </li>

        <!-- Reports -->
        <li class="mb-0.5 last:mb-0">
           <a class="block px-3 py-2 rounded-lg transition {{ Route::is('librarian.reports') ? 'bg-violet-500 text-white font-bold' : 'text-gray-800 dark:text-gray-100 hover:bg-gray-200 dark:hover:bg-gray-700' }}"
               href="{{ route('librarian.reports') }}">
                <div class="flex items-center">
                    <svg
                        class="shrink-0 fill-current {{ Route::is('librarian.reports') ? 'text-white' : 'text-gray-400 dark:text-gray-500' }}"
                        width="16" height="16" viewBox="0 0 24 24">
                        <path
                            d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-7 2h7v2h-7V5zm0 4h7v2h-7V9zm0 4h7v2h-7v-2zM5 19V5h2v14H5zm4 0V5h2v14H9z"/>
                    </svg>
                    <span class="text-sm ml-2 sidebar-text duration-200">Reports & Analytics</span>
                </div>
            </a>
        </li>
    </ul>

    <!-- Quick Actions Section -->
    <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
        <h3 class="text-xs uppercase text-gray-400 dark:text-gray-500 font-semibold pl-3 mb-3">
            <span class="lg:hidden lg:sidebar-expanded:block 2xl:block">Quick Actions</span>
        </h3>
        <ul class="space-y-1">
            <li>
                <button
                    wire:click="$dispatch('openModal', { component: 'quick-book-lending' })"
                    class="w-full text-left block pl-4 pr-3 py-2 rounded-lg transition text-gray-800 dark:text-gray-100 hover:bg-gray-200 dark:hover:bg-gray-700"
                >
                    <div class="flex items-center">
                        <svg class="shrink-0 fill-current text-gray-400 dark:text-gray-500" width="16" height="16"
                             viewBox="0 0 24 24">
                            <path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/>
                        </svg>
                        <span class="text-sm ml-2 sidebar-text duration-200">Quick Lending</span>
                    </div>
                </button>
            </li>
            <li>
                <button
                    wire:click="$dispatch('openModal', { component: 'quick-book-return' })"
                    class="w-full text-left block pl-4 pr-3 py-2 rounded-lg transition text-gray-800 dark:text-gray-100 hover:bg-gray-200 dark:hover:bg-gray-700"
                >
                    <div class="flex items-center">
                        <svg class="shrink-0 fill-current text-gray-400 dark:text-gray-500" width="16" height="16"
                             viewBox="0 0 24 24">
                            <path d="M9 11H7l3-3 3 3h-2v8h-2v-8z"/>
                        </svg>
                        <span class="text-sm ml-2 sidebar-text duration-200">Quick Return</span>
                    </div>
                </button>
            </li>
            <li>
                <button
                    wire:click="$dispatch('openModal', { component: 'send-overdue-reminders' })"
                    class="w-full text-left block pl-4 pr-3 py-2 rounded-lg transition text-gray-800 dark:text-gray-100 hover:bg-gray-200 dark:hover:bg-gray-700"
                >
                    <div class="flex items-center">
                        <svg class="shrink-0 fill-current text-gray-400 dark:text-gray-500" width="16" height="16"
                             viewBox="0 0 24 24">
                            <path
                                d="M12 22c1.1 0 2-.9 2-2h-4c0 1.1.89 2 2 2zm6-6v-5c0-3.07-1.64-5.64-4.5-6.32V4c0-.83-.67-1.5-1.5-1.5s-1.5.67-1.5 1.5v.68C7.63 5.36 6 7.92 6 11v5l-2 2v1h16v-1l-2-2z"/>
                        </svg>
                        <span class="text-sm ml-2 sidebar-text duration-200">Send Reminders</span>
                    </div>
                </button>
            </li>
        </ul>
    </div>

    <!-- Library Stats Section -->
    <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
        <h3 class="text-xs uppercase text-gray-400 dark:text-gray-500 font-semibold pl-3 mb-3">
            <span class="lg:hidden lg:sidebar-expanded:block 2xl:block">Today's Overview</span>
        </h3>
        <div class="pl-3 pr-3">
            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-3 space-y-2">
                <div class="flex items-center justify-between text-sm">
                    <span class="text-gray-600 dark:text-gray-400">Books Borrowed</span>
                    <span class="font-semibold text-gray-900 dark:text-gray-100">{{ $todayBorrowings ?? 0 }}</span>
                </div>
                <div class="flex items-center justify-between text-sm">
                    <span class="text-gray-600 dark:text-gray-400">Books Returned</span>
                    <span class="font-semibold text-gray-900 dark:text-gray-100">{{ $todayReturns ?? 0 }}</span>
                </div>
                <div class="flex items-center justify-between text-sm">
                    <span class="text-gray-600 dark:text-gray-400">Pending Requests</span>
                    <span class="font-semibold text-orange-600 dark:text-orange-400">{{ $pendingRequests ?? 0 }}</span>
                </div>
                <div class="flex items-center justify-between text-sm">
                    <span class="text-gray-600 dark:text-gray-400">Overdue Books</span>
                    <span class="font-semibold text-red-600 dark:text-red-400">{{ $overdueCount ?? 0 }}</span>
                </div>
            </div>
        </div>
    </div>
</div>
