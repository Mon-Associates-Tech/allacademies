<div>
    <h3 class="text-xs uppercase text-gray-400 dark:text-gray-500 font-semibold pl-3">
        <span class="hidden lg:hidden lg:sidebar-expanded:hidden 2xl:hidden text-center w-6" aria-hidden="true">•••</span>
        <span class="lg:hidden lg:sidebar-expanded:block 2xl:block">Parent Portal</span>
    </h3>
    <ul class="mt-3">
        <!-- Dashboard -->
        <li class="mb-0.5 last:mb-0">
            <a class="block pl-4 pr-3 py-2 rounded-lg transition {{ Route::is('parent.dashboard') ? 'bg-violet-500 text-white font-bold' : 'text-gray-800 dark:text-gray-100 hover:bg-gray-200 dark:hover:bg-gray-700' }}"
               href="{{ route('parent.dashboard') }}">
                <div class="flex items-center">
                    <svg class="shrink-0 fill-current {{ Route::is('parent.dashboard') ? 'text-white' : 'text-gray-400 dark:text-gray-500' }}" width="16" height="16" viewBox="0 0 24 24">
                        <path d="M3 13h8V3H3v10zm0 8h8v-6H3v6zm10 0h8V11h-8v10zm0-18v6h8V3h-8z"/>
                    </svg>
                    <span class="text-sm ml-4 sidebar-text duration-200">Dashboard</span>
                </div>
            </a>
        </li>
        <!-- Academic Chat -->
        <li class="mb-0.5 last:mb-0" title="Academic Assistant">
            <a class="block pl-4 pr-3 py-2 rounded-lg transition {{ Route::is('academic-chat*') ? 'bg-violet-500 text-white font-bold' : 'text-gray-800 dark:text-gray-100 hover:bg-gray-200 dark:hover:bg-gray-700' }}"
               href="{{route('academic-chat.index')}}">
                <div class="flex items-center">
                    <!-- Chat bubble with spark -->
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="shrink-0 {{ Route::is('academic-chat*') ? 'text-white' : 'text-gray-400 dark:text-gray-500' }}">
                        <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h10"/>
                        <path d="M17 3l1.5 3L22 8l-3.5 2L17 13l-1.5-3L12 8l3.5-2L17 3z"/>
                    </svg>

                    <span class="text-sm ml-4 sidebar-text duration-200">Academic Assistant</span>
                </div>
            </a>
        </li>

        <!-- Wards Management -->
        <li class="mb-0.5 last:mb-0">
            <a class="block pl-4 pr-3 py-2 rounded-lg transition {{ Route::is('parent.wards*') ? 'bg-violet-500 text-white font-bold' : 'text-gray-800 dark:text-gray-100 hover:bg-gray-200 dark:hover:bg-gray-700' }}"
               href="{{ route('parent.wards') }}">
                <div class="flex items-center">
                    <svg class="shrink-0 fill-current {{ Route::is('parent.wards*') ? 'text-white' : 'text-gray-400 dark:text-gray-500' }}" width="16" height="16" viewBox="0 0 24 24">
                        <path d="M16 4c0-1.11.89-2 2-2s2 .89 2 2-.89 2-2 2-2-.89-2-2zM4 18v-4h3v4h2v-7.5C9 8.57 7.43 7 5.5 7S2 8.57 2 10.5V18h2zM22.5 9.5c0-1.93-1.57-3.5-3.5-3.5S15.5 7.57 15.5 9.5V18H22v-8.5z"/>
                    </svg>
                    <span class="text-sm ml-4 sidebar-text duration-200">My Wards</span>
                </div>
            </a>
        </li>

        <!-- Academic Performance -->
        <li class="mb-0.5 last:mb-0">
            <a class="block pl-4 pr-3 py-2 rounded-lg transition {{ Route::is('parent.performance*') ? 'bg-violet-500 text-white font-bold' : 'text-gray-800 dark:text-gray-100 hover:bg-gray-200 dark:hover:bg-gray-700' }}"
               href="{{ route('parent.performance') }}">
                <div class="flex items-center">
                    <svg class="shrink-0 fill-current {{ Route::is('parent.performance*') ? 'text-white' : 'text-gray-400 dark:text-gray-500' }}" width="16" height="16" viewBox="0 0 24 24">
                        <path d="M16 6l2.29 2.29-4.88 4.88-4-4L2 16.59 3.41 18l6-6 4 4 6.3-6.29L22 12V6z"/>
                    </svg>
                    <span class="text-sm ml-4 sidebar-text duration-200">Academic Performance</span>
                </div>
            </a>
        </li>

        <!-- Reports & Analytics -->
        <li class="mb-0.5 last:mb-0">
            <a class="block pl-4 pr-3 py-2 rounded-lg transition {{ Route::is('parent.reports*') ? 'bg-violet-500 text-white font-bold' : 'text-gray-800 dark:text-gray-100 hover:bg-gray-200 dark:hover:bg-gray-700' }}"
               href="{{ route('parent.reports') }}">
                <div class="flex items-center">
                    <svg class="shrink-0 fill-current {{ Route::is('parent.reports*') ? 'text-white' : 'text-gray-400 dark:text-gray-500' }}" width="16" height="16" viewBox="0 0 24 24">
                        <path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zM9 17H7v-7h2v7zm4 0h-2V7h2v10zm4 0h-2v-4h2v4z"/>
                    </svg>
                    <span class="text-sm ml-4 sidebar-text duration-200">Reports & Analytics</span>
                </div>
            </a>
        </li>

        <!-- Payments -->
        <li class="mb-0.5 last:mb-0" title="Payments">
            <a class="block pl-4 pr-3 py-2 rounded-lg transition {{ Route::is('parent.fees*') || Route::is('parent.fees*') ? 'bg-violet-500 text-white font-bold' : 'text-gray-800 dark:text-gray-100 hover:bg-gray-200 dark:hover:bg-gray-700' }}"
               href="{{route('parent.fees.index')}}">
                <div class="flex items-center">
                    <svg
                        class="shrink-0 fill-current {{ Route::is('parent.fees*') || Route::is('parent.fees*') ? 'text-white' : 'text-gray-400 dark:text-gray-500' }}"
                        width="16" height="16" viewBox="0 0 24 24">
                        <path
                            d="M20 4H4c-1.11 0-1.99.89-1.99 2L2 18c0 1.11.89 2 2 2h16c1.11 0 2-.89 2-2V6c0-1.11-.89-2-2-2zm0 14H4v-6h16v6zm0-10H4V6h16v2z"/>
                    </svg>
                    <span class="text-sm ml-4 sidebar-text duration-200">Pay Fees</span>
                </div>
            </a>
        </li>

        <!-- Transaction History -->
        <li class="mb-0.5 last:mb-0" title="Transaction History">
            <a class="block pl-4 pr-3 py-2 rounded-lg transition {{ Route::is('parent.payments.transactions') || Route::is('parent.payments.transactions') ? 'bg-violet-500 text-white font-bold' : 'text-gray-800 dark:text-gray-100 hover:bg-gray-200 dark:hover:bg-gray-700' }}"
               href="{{route('parent.payments.transactions')}}">
                <div class="flex items-center">
                    <svg
                        class="shrink-0 fill-current {{ Route::is('parent.payments.transactions') || Route::is('parent.payments.transactions') ? 'text-white' : 'text-gray-400 dark:text-gray-500' }}"
                        width="16" height="16" viewBox="0 0 24 24">
                        <path
                            d="M13 2.05v3.03c3.39.49 6 3.39 6 6.92 0 .9-.18 1.75-.48 2.54l2.6 1.53c.56-1.24.88-2.62.88-4.07 0-5.18-3.95-9.45-9-9.95zM12 19c-3.87 0-7-3.13-7-7 0-3.53 2.61-6.43 6-6.92V2.05c-5.06.5-9 4.76-9 9.95 0 5.52 4.47 10 9.99 10 3.31 0 6.24-1.61 8.06-4.09l-2.6-1.53C16.17 17.98 14.21 19 12 19z"/>
                    </svg>
                    <span class="text-sm ml-4 sidebar-text duration-200">Transactions</span>
                </div>
            </a>
        </li>

        <!-- Notifications -->
        <li class="mb-0.5 last:mb-0">
            <a class="block pl-4 pr-3 py-2 rounded-lg transition {{ Route::is('parent.notifications*') ? 'bg-violet-500 text-white font-bold' : 'text-gray-800 dark:text-gray-100 hover:bg-gray-200 dark:hover:bg-gray-700' }}"
               href="{{ route('parent.notifications') }}">
                <div class="flex items-center">
                    <svg class="shrink-0 fill-current {{ Route::is('parent.notifications*') ? 'text-white' : 'text-gray-400 dark:text-gray-500' }}" width="16" height="16" viewBox="0 0 24 24">
                        <path d="M12 22c1.1 0 2-.9 2-2h-4c0 1.1.9 2 2 2zm6-6v-5c0-3.07-1.64-5.64-4.5-6.32V4c0-.83-.67-1.5-1.5-1.5s-1.5.67-1.5 1.5v.68C7.63 5.36 6 7.92 6 11v5l-2 2v1h16v-1l-2-2z"/>
                    </svg>
                    <span class="text-sm ml-4 sidebar-text duration-200">Notifications</span>
                </div>
            </a>
        </li>

        <!-- Book Subscriptions -->
        <li class="mb-0.5 last:mb-0">
            <a class="block pl-4 pr-3 py-2 rounded-lg transition {{ Route::is('parent.books*') ? 'bg-violet-500 text-white font-bold' : 'text-gray-800 dark:text-gray-100 hover:bg-gray-200 dark:hover:bg-gray-700' }}"
               href="{{ route('parent.books') }}">
                <div class="flex items-center">
                    <svg class="shrink-0 fill-current {{ Route::is('parent.books*') ? 'text-white' : 'text-gray-400 dark:text-gray-500' }}" width="16" height="16" viewBox="0 0 24 24">
                        <path d="M21 5c-1.11-.35-2.33-.5-3.5-.5-1.95 0-4.05.4-5.5 1.5-1.45-1.1-3.55-1.5-5.5-1.5S2.45 4.9 1 6v14.65c0 .25.25.5.5.5.1 0 .15-.05.25-.05C3.1 20.45 5.05 20 6.5 20c1.95 0 4.05.4 5.5 1.5 1.35-.85 3.8-1.5 5.5-1.5 1.65 0 3.35.3 4.75 1.05.1.05.15.05.25.05.25 0 .5-.25.5-.5V6c-.6-.45-1.25-.75-2-1zm0 13.5c-1.1-.35-2.3-.5-3.5-.5-1.7 0-4.15.65-5.5 1.5V8c1.35-.85 3.8-1.5 5.5-1.5 1.2 0 2.4.15 3.5.5v11.5z"/>
                    </svg>
                    <span class="text-sm ml-4 sidebar-text duration-200">Book Subscriptions</span>
                </div>
            </a>
        </li>

        <!-- Library -->
        <li class="mb-0.5 last:mb-0 hidden">
            <a class="block pl-4 pr-3 py-2 rounded-lg transition {{ Route::is('parent.library*') ? 'bg-violet-500 text-white font-bold' : 'text-gray-800 dark:text-gray-100 hover:bg-gray-200 dark:hover:bg-gray-700' }}"
               href="{{ route('parent.library') }}">
                <div class="flex items-center">
                    <svg class="shrink-0 fill-current {{ Route::is('parent.library*') ? 'text-white' : 'text-gray-400 dark:text-gray-500' }}" width="16" height="16" viewBox="0 0 24 24">
                        <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                    </svg>
                    <span class="text-sm ml-4 sidebar-text duration-200">Digital Library</span>
                </div>
            </a>
        </li>
    </ul>
</div>
