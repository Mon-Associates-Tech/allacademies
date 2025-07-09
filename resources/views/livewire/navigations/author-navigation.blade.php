<div>
    <h3 class="text-xs uppercase text-gray-400 dark:text-gray-500 font-semibold pl-3">
        <span class="hidden lg:hidden lg:sidebar-expanded:hidden 2xl:hidden text-center w-6" aria-hidden="true">•••</span>
        <span class="lg:hidden lg:sidebar-expanded:block 2xl:block">Author Portal</span>
    </h3>
    <ul class="mt-3">
        <!-- Dashboard -->
        <li class="mb-0.5 last:mb-0">
            <a class="block pl-4 pr-3 py-2 rounded-lg transition {{ Route::is('dashboard') ? 'bg-violet-500 text-white font-bold' : 'text-gray-800 dark:text-gray-100 hover:bg-gray-200 dark:hover:bg-gray-700' }}"
               href="{{route('dashboard')}}">
                <div class="flex items-center">
                    <svg class="shrink-0 fill-current {{ Route::is('dashboard') ? 'text-white' : 'text-gray-400 dark:text-gray-500' }}" width="16" height="16" viewBox="0 0 24 24">
                        <path d="M3 13h8V3H3v10zm0 8h8v-6H3v6zm10 0h8V11h-8v10zm0-18v6h8V3h-8z"/>
                    </svg>
                    <span class="text-sm ml-4 sidebar-text duration-200">Dashboard</span>
                </div>
            </a>
        </li>

        <!-- My Books -->
        <li class="mb-0.5 last:mb-0">
            <a class="block pl-4 pr-3 py-2 rounded-lg transition {{ Route::is('author.books.index') ? 'bg-violet-500 text-white font-bold' : 'text-gray-800 dark:text-gray-100 hover:bg-gray-200 dark:hover:bg-gray-700' }}"
               href="{{route('author.books.index')}}">
                <div class="flex items-center">
                    <svg class="shrink-0 fill-current {{ Route::is('author.books.index') ? 'text-white' : 'text-gray-400 dark:text-gray-500' }}" width="16" height="16" viewBox="0 0 24 24">
                        <path d="M21 5c-1.11-.35-2.33-.5-3.5-.5-1.95 0-4.05.4-5.5 1.5-1.45-1.1-3.55-1.5-5.5-1.5S2.45 4.9 1 6v14.65c0 .25.25.5.5.5.1 0 .15-.05.25-.05C3.1 20.45 5.05 20 6.5 20c1.95 0 4.05.4 5.5 1.5 1.35-.85 3.8-1.5 5.5-1.5 1.65 0 3.35.3 4.75 1.05.1.05.15.05.25.05.25 0 .5-.25.5-.5V6c-.6-.45-1.25-.75-2-1zm0 13.5c-1.1-.35-2.3-.5-3.5-.5-1.7 0-4.15.65-5.5 1.5V8c1.35-.85 3.8-1.5 5.5-1.5 1.2 0 2.4.15 3.5.5v11.5z"/>
                    </svg>
                    <span class="text-sm ml-4 sidebar-text duration-200">My Books</span>
                </div>
            </a>
        </li>

        <!-- Book Management -->
        <li class="mb-0.5 last:mb-0 hidden">
            <a class="block pl-4 pr-3 py-2 rounded-lg transition {{ Route::is('author.books.create') ? 'bg-violet-500 text-white font-bold' : 'text-gray-800 dark:text-gray-100 hover:bg-gray-200 dark:hover:bg-gray-700' }}"
               href="{{route('author.books.create')}}">
                <div class="flex items-center">
                    <svg class="shrink-0 fill-current {{ Route::is('author.books.create') ? 'text-white' : 'text-gray-400 dark:text-gray-500' }}" width="16" height="16" viewBox="0 0 24 24">
                        <path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/>
                    </svg>
                    <span class="text-sm ml-4 sidebar-text duration-200">Add New Book</span>
                </div>
            </a>
        </li>

        <!-- Subscriptions -->
        <li class="mb-0.5 last:mb-0">
            <a class="block pl-4 pr-3 py-2 rounded-lg transition {{ Route::is('author.subscriptions*') ? 'bg-violet-500 text-white font-bold' : 'text-gray-800 dark:text-gray-100 hover:bg-gray-200 dark:hover:bg-gray-700' }}"
               href="{{route('author.subscribers.index')}}">
                <div class="flex items-center">
                    <svg class="shrink-0 fill-current {{ Route::is('author.subscriptions*') ? 'text-white' : 'text-gray-400 dark:text-gray-500' }}" width="16" height="16" viewBox="0 0 24 24">
                        <path d="M16 7c0-2.21-1.79-4-4-4s-4 1.79-4 4 1.79 4 4 4 4-1.79 4-4zm4 7v4h-2v-4c0-1.1-.9-2-2-2H8c-1.1 0-2 .9-2 2v4H4v-4c0-2.21 1.79-4 4-4h8c2.21 0 4 1.79 4 4z"/>
                    </svg>
                    <span class="text-sm ml-4 sidebar-text duration-200">Subscribers</span>
                </div>
            </a>
        </li>

        <!-- Book Borrowings -->
        <li class="mb-0.5 last:mb-0 hidden">
            <a class="block pl-4 pr-3 py-2 rounded-lg transition {{ Route::is('author.borrowings*') ? 'bg-violet-500 text-white font-bold' : 'text-gray-800 dark:text-gray-100 hover:bg-gray-200 dark:hover:bg-gray-700' }}"
               href="{{route('author.borrowings.index')}}">
                <div class="flex items-center">
                    <svg class="shrink-0 fill-current {{ Route::is('author.borrowings*') ? 'text-white' : 'text-gray-400 dark:text-gray-500' }}" width="16" height="16" viewBox="0 0 24 24">
                        <path d="M17 3H7c-1.1 0-2 .9-2 2v16l7-3 7 3V5c0-1.1-.9-2-2-2zm0 15l-5-2.18L7 18V5h10v13z"/>
                    </svg>
                    <span class="text-sm ml-4 sidebar-text duration-200">Book Borrowings</span>
                </div>
            </a>
        </li>

        <!-- Analytics & Reports -->
        <li class="mb-0.5 last:mb-0">
            <a class="block pl-4 pr-3 py-2 rounded-lg transition {{ Route::is('author.analytics*') ? 'bg-violet-500 text-white font-bold' : 'text-gray-800 dark:text-gray-100 hover:bg-gray-200 dark:hover:bg-gray-700' }}"
               href="{{route('author.analytics.index')}}">
                <div class="flex items-center">
                    <svg class="shrink-0 fill-current {{ Route::is('author.analytics*') ? 'text-white' : 'text-gray-400 dark:text-gray-500' }}" width="16" height="16" viewBox="0 0 24 24">
                        <path d="M16 6l2.29 2.29-4.88 4.88-4-4L2 16.59 3.41 18l6-6 4 4 6.3-6.29L22 12V6z"/>
                    </svg>
                    <span class="text-sm ml-4 sidebar-text duration-200">Analytics</span>
                </div>
            </a>
        </li>

        <!-- Revenue & Earnings -->
        <li class="mb-0.5 last:mb-0">
            <a class="block pl-4 pr-3 py-2 rounded-lg transition {{ Route::is('author.revenue*') ? 'bg-violet-500 text-white font-bold' : 'text-gray-800 dark:text-gray-100 hover:bg-gray-200 dark:hover:bg-gray-700' }}"
               href="{{route('author.revenue.index')}}">
                <div class="flex items-center">
                    <svg class="shrink-0 fill-current {{ Route::is('author.revenue*') ? 'text-white' : 'text-gray-400 dark:text-gray-500' }}" width="16" height="16" viewBox="0 0 24 24">
                        <path d="M11.8 10.9c-2.27-.59-3-1.2-3-2.15 0-1.09 1.01-1.85 2.7-1.85 1.78 0 2.44.85 2.5 2.1h2.21c-.07-1.72-1.12-3.3-3.21-3.81V3h-3v2.16c-1.94.42-3.5 1.68-3.5 3.61 0 2.31 1.91 3.46 4.7 4.13 2.5.6 3 1.48 3 2.41 0 .69-.49 1.79-2.7 1.79-2.06 0-2.87-.92-2.98-2.1h-2.2c.12 2.19 1.76 3.42 3.68 3.83V21h3v-2.15c1.95-.37 3.5-1.5 3.5-3.55 0-2.84-2.43-3.81-4.7-4.4z"/>
                    </svg>
                    <span class="text-sm ml-4 sidebar-text duration-200">Revenue</span>
                </div>
            </a>
        </li>

        <!-- Reviews & Feedback -->
        <li class="mb-0.5 last:mb-0">
            <a class="block pl-4 pr-3 py-2 rounded-lg transition {{ Route::is('author.reviews*') ? 'bg-violet-500 text-white font-bold' : 'text-gray-800 dark:text-gray-100 hover:bg-gray-200 dark:hover:bg-gray-700' }}"
               href="{{route('author.reviews.index')}}">
                <div class="flex items-center">
                    <svg class="shrink-0 fill-current {{ Route::is('author.reviews*') ? 'text-white' : 'text-gray-400 dark:text-gray-500' }}" width="16" height="16" viewBox="0 0 24 24">
                        <path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/>
                    </svg>
                    <span class="text-sm ml-4 sidebar-text duration-200">Reviews</span>
                </div>
            </a>
        </li>

        <!-- Book Categories -->
        <li class="mb-0.5 last:mb-0 hidden">
            <a class="block pl-4 pr-3 py-2 rounded-lg transition {{ Route::is('author.categories*') ? 'bg-violet-500 text-white font-bold' : 'text-gray-800 dark:text-gray-100 hover:bg-gray-200 dark:hover:bg-gray-700' }}"
               href="{{route('author.categories.index')}}">
                <div class="flex items-center">
                    <svg class="shrink-0 fill-current {{ Route::is('author.categories*') ? 'text-white' : 'text-gray-400 dark:text-gray-500' }}" width="16" height="16" viewBox="0 0 24 24">
                        <path d="M17.63 5.84C17.27 5.33 16.67 5 16 5L5 5.01C3.9 5.01 3 5.9 3 7v10c0 1.1.9 1.99 2 1.99L16 19c.67 0 1.27-.33 1.63-.84L22 12l-4.37-6.16z"/>
                    </svg>
                    <span class="text-sm ml-4 sidebar-text duration-200">Categories</span>
                </div>
            </a>
        </li>

        <!-- Publishing Status -->
        <li class="mb-0.5 last:mb-0 hidden">
            <a class="block pl-4 pr-3 py-2 rounded-lg transition {{ Route::is('author.publishing*') ? 'bg-violet-500 text-white font-bold' : 'text-gray-800 dark:text-gray-100 hover:bg-gray-200 dark:hover:bg-gray-700' }}"
               href="{{route('author.publishing.index')}}">
                <div class="flex items-center">
                    <svg class="shrink-0 fill-current {{ Route::is('author.publishing*') ? 'text-white' : 'text-gray-400 dark:text-gray-500' }}" width="16" height="16" viewBox="0 0 24 24">
                        <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/>
                    </svg>
                    <span class="text-sm ml-4 sidebar-text duration-200">Publishing</span>
                </div>
            </a>
        </li>

        <!-- Promotions & Marketing -->
        <li class="mb-0.5 last:mb-0">
            <a class="block pl-4 pr-3 py-2 rounded-lg transition {{ Route::is('author.promotions*') ? 'bg-violet-500 text-white font-bold' : 'text-gray-800 dark:text-gray-100 hover:bg-gray-200 dark:hover:bg-gray-700' }}"
               href="{{route('author.promotions.index')}}">
                <div class="flex items-center">
                    <svg class="shrink-0 fill-current {{ Route::is('author.promotions*') ? 'text-white' : 'text-gray-400 dark:text-gray-500' }}" width="16" height="16" viewBox="0 0 24 24">
                        <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                    </svg>
                    <span class="text-sm ml-4 sidebar-text duration-200">Promotions</span>
                </div>
            </a>
        </li>

        <!-- Author Community -->
        <li class="mb-0.5 last:mb-0">
            <a class="block pl-4 pr-3 py-2 rounded-lg transition {{ Route::is('author.community*') ? 'bg-violet-500 text-white font-bold' : 'text-gray-800 dark:text-gray-100 hover:bg-gray-200 dark:hover:bg-gray-700' }}"
               href="{{route('author.community.index')}}">
                <div class="flex items-center">
                    <svg class="shrink-0 fill-current {{ Route::is('author.community*') ? 'text-white' : 'text-gray-400 dark:text-gray-500' }}" width="16" height="16" viewBox="0 0 24 24">
                        <path d="M16 4c0-1.11.89-2 2-2s2 .89 2 2-.89 2-2 2-2-.89-2-2zm4 18v-6h2.5l-2.54-7.63A3.982 3.982 0 0 0 16.5 6.5c-1.11 0-2.18.46-2.95 1.26L10.93 9.5l-1.93.5V12h3v8h-2v-6.5H8.93L7 14v8H5v-8.5l3.55-1.78L9.5 9.5l3.5-2.5c.73-.52 1.6-.77 2.5-.5L18 13v7h2z"/>
                    </svg>
                    <span class="text-sm ml-4 sidebar-text duration-200">Community</span>
                </div>
            </a>
        </li>

        <!-- Notifications -->
        <li class="mb-0.5 last:mb-0">
            <a class="block pl-4 pr-3 py-2 rounded-lg transition {{ Route::is('author.notifications*') ? 'bg-violet-500 text-white font-bold' : 'text-gray-800 dark:text-gray-100 hover:bg-gray-200 dark:hover:bg-gray-700' }}"
               href="{{route('author.notifications.index')}}">
                <div class="flex items-center">
                    <svg class="shrink-0 fill-current {{ Route::is('author.notifications*') ? 'text-white' : 'text-gray-400 dark:text-gray-500' }}" width="16" height="16" viewBox="0 0 24 24">
                        <path d="M12 22c1.1 0 2-.9 2-2h-4c0 1.1.9 2 2 2zm6-6v-5c0-3.07-1.63-5.64-4.5-6.32V4c0-.83-.67-1.5-1.5-1.5s-1.5.67-1.5 1.5v.68C7.64 5.36 6 7.92 6 11v5l-2 2v1h16v-1l-2-2z"/>
                    </svg>
                    <span class="text-sm ml-4 sidebar-text duration-200">Notifications</span>
                    @if(auth()->user()->unreadNotifications->count() > 0)
                        <span class="ml-auto bg-red-500 text-white text-xs rounded-full px-2 py-0.5 min-w-5 text-center">
                            {{ auth()->user()->unreadNotifications->count() }}
                        </span>
                    @endif
                </div>
            </a>
        </li>

        <!-- Settings -->
        <li class="mb-0.5 last:mb-0">
            <a class="block pl-4 pr-3 py-2 rounded-lg transition {{ Route::is('author.settings*') ? 'bg-violet-500 text-white font-bold' : 'text-gray-800 dark:text-gray-100 hover:bg-gray-200 dark:hover:bg-gray-700' }}"
               href="{{route('author.settings.index')}}">
                <div class="flex items-center">
                    <svg class="shrink-0 fill-current {{ Route::is('author.settings*') ? 'text-white' : 'text-gray-400 dark:text-gray-500' }}" width="16" height="16" viewBox="0 0 24 24">
                        <path d="M19.14,12.94c0.04-0.3,0.06-0.61,0.06-0.94c0-0.32-0.02-0.64-0.07-0.94l2.03-1.58c0.18-0.14,0.23-0.41,0.12-0.61 l-1.92-3.32c-0.12-0.22-0.37-0.29-0.59-0.22l-2.39,0.96c-0.5-0.38-1.03-0.7-1.62-0.94L14.4,2.81c-0.04-0.24-0.24-0.41-0.48-0.41 h-3.84c-0.24,0-0.43,0.17-0.47,0.41L9.25,5.35C8.66,5.59,8.12,5.92,7.63,6.29L5.24,5.33c-0.22-0.08-0.47,0-0.59,0.22L2.74,8.87 C2.62,9.08,2.66,9.34,2.86,9.48l2.03,1.58C4.84,11.36,4.82,11.69,4.82,12s0.02,0.64,0.07,0.94l-2.03,1.58 c-0.18,0.14-0.23,0.41-0.12,0.61l1.92,3.32c0.12,0.22,0.37,0.29,0.59,0.22l2.39-0.96c0.5,0.38,1.03,0.7,1.62,0.94l0.36,2.54 c0.05,0.24,0.24,0.41,0.48,0.41h3.84c0.24,0,0.44-0.17,0.47-0.41l0.36-2.54c0.59-0.24,1.13-0.56,1.62-0.94l2.39,0.96 c0.22,0.08,0.47,0,0.59-0.22l1.92-3.32c0.12-0.22,0.07-0.47-0.12-0.61L19.14,12.94z M12,15.6c-1.98,0-3.6-1.62-3.6-3.6 s1.62-3.6,3.6-3.6s3.6,1.62,3.6,3.6S13.98,15.6,12,15.6z"/>
                    </svg>
                    <span class="text-sm ml-4 sidebar-text duration-200">Settings</span>
                </div>
            </a>
        </li>

        <!-- Profile -->
        <li class="mb-0.5 last:mb-0">
            <a class="block pl-4 pr-3 py-2 rounded-lg transition {{ Route::is('author.profile*') ? 'bg-violet-500 text-white font-bold' : 'text-gray-800 dark:text-gray-100 hover:bg-gray-200 dark:hover:bg-gray-700' }}"
               href="{{route('author.profile.show')}}">
                <div class="flex items-center">
                    <svg class="shrink-0 fill-current {{ Route::is('author.profile*') ? 'text-white' : 'text-gray-400 dark:text-gray-500' }}" width="16" height="16" viewBox="0 0 24 24">
                        <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                    </svg>
                    <span class="text-sm ml-4 sidebar-text duration-200">My Profile</span>
                </div>
            </a>
        </li>

        <!-- Help & Support -->
        <li class="mb-0.5 last:mb-0">
            <a class="block pl-4 pr-3 py-2 rounded-lg transition {{ Route::is('author.help*') ? 'bg-violet-500 text-white font-bold' : 'text-gray-800 dark:text-gray-100 hover:bg-gray-200 dark:hover:bg-gray-700' }}"
               href="{{route('author.help.index')}}">
                <div class="flex items-center">
                    <svg class="shrink-0 fill-current {{ Route::is('author.help*') ? 'text-white' : 'text-gray-400 dark:text-gray-500' }}" width="16" height="16" viewBox="0 0 24 24">
                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 17h-2v-2h2v2zm2.07-7.75l-.9.92C13.45 12.9 13 13.5 13 15h-2v-.5c0-1.1.45-2.1 1.17-2.83l1.24-1.26c.37-.36.59-.86.59-1.41 0-1.1-.9-2-2-2s-2 .9-2 2H8c0-2.21 1.79-4 4-4s4 1.79 4 4c0 .88-.36 1.68-.93 2.25z"/>
                    </svg>
                    <span class="text-sm ml-4 sidebar-text duration-200">Help & Support</span>
                </div>
            </a>
        </li>
    </ul>
</div>
