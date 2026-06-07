<div>
    <h3 class="text-xs uppercase text-gray-400 dark:text-gray-500 font-semibold pl-3">
        <span class="hidden lg:hidden lg:sidebar-expanded:hidden 2xl:hidden text-center w-6" aria-hidden="true">•••</span>
        <span class="lg:hidden lg:sidebar-expanded:block 2xl:block">Parent Portal</span>
    </h3>
    <ul class="mt-3">
        <!-- Dashboard -->
        <li class="mb-0.5 last:mb-0">
           <a class="block px-3 py-2 rounded-lg transition {{ Route::is('parent.dashboard') ? 'bg-violet-500 text-white font-bold' : 'text-gray-800 dark:text-gray-100 hover:bg-gray-200 dark:hover:bg-gray-700' }}"
               href="{{ route('parent.dashboard') }}">
                <div class="flex items-center">
                    <svg class="shrink-0 fill-current {{ Route::is('parent.dashboard') ? 'text-white' : 'text-gray-400 dark:text-gray-500' }}" width="16" height="16" viewBox="0 0 24 24">
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
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="shrink-0 {{ Route::is('academic-chat*') ? 'text-white' : 'text-gray-400 dark:text-gray-500' }}">
                        <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h10"/>
                        <path d="M17 3l1.5 3L22 8l-3.5 2L17 13l-1.5-3L12 8l3.5-2L17 3z"/>
                    </svg>

                    <span class="text-sm ml-2 sidebar-text duration-200">Research Assistant</span>
                </div>
            </a>
        </li>

        <!-- Wards Management -->
        <li class="mb-0.5 last:mb-0">
           <a class="block px-3 py-2 rounded-lg transition {{ Route::is('parent.wards*') ? 'bg-violet-500 text-white font-bold' : 'text-gray-800 dark:text-gray-100 hover:bg-gray-200 dark:hover:bg-gray-700' }}"
               href="{{ route('parent.wards') }}">
                <div class="flex items-center">
                    <svg class="shrink-0 fill-current {{ Route::is('parent.wards*') ? 'text-white' : 'text-gray-400 dark:text-gray-500' }}" width="16" height="16" viewBox="0 0 24 24">
                        <path d="M16 4c0-1.11.89-2 2-2s2 .89 2 2-.89 2-2 2-2-.89-2-2zM4 18v-4h3v4h2v-7.5C9 8.57 7.43 7 5.5 7S2 8.57 2 10.5V18h2zM22.5 9.5c0-1.93-1.57-3.5-3.5-3.5S15.5 7.57 15.5 9.5V18H22v-8.5z"/>
                    </svg>
                    <span class="text-sm ml-2 sidebar-text duration-200">My Wards</span>
                </div>
            </a>
        </li>

        <!-- Academic Performance -->
        <li class="mb-0.5 last:mb-0">
           <a class="block px-3 py-2 rounded-lg transition {{ Route::is('parent.performance*') ? 'bg-violet-500 text-white font-bold' : 'text-gray-800 dark:text-gray-100 hover:bg-gray-200 dark:hover:bg-gray-700' }}"
               href="{{ route('parent.performance') }}">
                <div class="flex items-center">
                    <svg class="shrink-0 fill-current {{ Route::is('parent.performance*') ? 'text-white' : 'text-gray-400 dark:text-gray-500' }}" width="16" height="16" viewBox="0 0 24 24">
                        <path d="M16 6l2.29 2.29-4.88 4.88-4-4L2 16.59 3.41 18l6-6 4 4 6.3-6.29L22 12V6z"/>
                    </svg>
                    <span class="text-sm ml-2 sidebar-text duration-200">Academic Performance</span>
                </div>
            </a>
        </li>

        <!-- Reports & Analytics -->
        <li class="mb-0.5 last:mb-0">
           <a class="block px-3 py-2 rounded-lg transition {{ Route::is('parent.reports*') ? 'bg-violet-500 text-white font-bold' : 'text-gray-800 dark:text-gray-100 hover:bg-gray-200 dark:hover:bg-gray-700' }}"
               href="{{ route('parent.reports') }}">
                <div class="flex items-center">
                    <svg class="shrink-0 fill-current {{ Route::is('parent.reports*') ? 'text-white' : 'text-gray-400 dark:text-gray-500' }}" width="16" height="16" viewBox="0 0 24 24">
                        <path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zM9 17H7v-7h2v7zm4 0h-2V7h2v10zm4 0h-2v-4h2v4z"/>
                    </svg>
                    <span class="text-sm ml-2 sidebar-text duration-200">Reports & Analytics</span>
                </div>
            </a>
        </li>

        <!-- Payments -->
        <li class="mb-0.5 last:mb-0" title="Payments">
           <a class="block px-3 py-2 rounded-lg transition {{ Route::is('parent.fees*') || Route::is('parent.fees*') ? 'bg-violet-500 text-white font-bold' : 'text-gray-800 dark:text-gray-100 hover:bg-gray-200 dark:hover:bg-gray-700' }}"
               href="{{route('parent.fees.index')}}">
                <div class="flex items-center">
                    <svg
                        class="shrink-0 fill-current {{ Route::is('parent.fees*') || Route::is('parent.fees*') ? 'text-white' : 'text-gray-400 dark:text-gray-500' }}"
                        width="16" height="16" viewBox="0 0 24 24">
                        <path
                            d="M20 4H4c-1.11 0-1.99.89-1.99 2L2 18c0 1.11.89 2 2 2h16c1.11 0 2-.89 2-2V6c0-1.11-.89-2-2-2zm0 14H4v-6h16v6zm0-10H4V6h16v2z"/>
                    </svg>
                    <span class="text-sm ml-2 sidebar-text duration-200">Pay Fees</span>
                </div>
            </a>
        </li>

        <!-- Transaction History -->
        <li class="mb-0.5 last:mb-0" title="Transaction History">
           <a class="block px-3 py-2 rounded-lg transition {{ Route::is('parent.payments.transactions') || Route::is('parent.payments.transactions') ? 'bg-violet-500 text-white font-bold' : 'text-gray-800 dark:text-gray-100 hover:bg-gray-200 dark:hover:bg-gray-700' }}"
               href="{{route('parent.payments.transactions')}}">
                <div class="flex items-center">
                    <svg
                        class="shrink-0 fill-current {{ Route::is('parent.payments.transactions') || Route::is('parent.payments.transactions') ? 'text-white' : 'text-gray-400 dark:text-gray-500' }}"
                        width="16" height="16" viewBox="0 0 24 24">
                        <path
                            d="M13 2.05v3.03c3.39.49 6 3.39 6 6.92 0 .9-.18 1.75-.48 2.54l2.6 1.53c.56-1.24.88-2.62.88-4.07 0-5.18-3.95-9.45-9-9.95zM12 19c-3.87 0-7-3.13-7-7 0-3.53 2.61-6.43 6-6.92V2.05c-5.06.5-9 4.76-9 9.95 0 5.52 4.47 10 9.99 10 3.31 0 6.24-1.61 8.06-4.09l-2.6-1.53C16.17 17.98 14.21 19 12 19z"/>
                    </svg>
                    <span class="text-sm ml-2 sidebar-text duration-200">Transactions</span>
                </div>
            </a>
        </li>

        <!-- Notifications -->
        <li class="mb-0.5 last:mb-0">
           <a class="block px-3 py-2 rounded-lg transition {{ Route::is('parent.notifications*') ? 'bg-violet-500 text-white font-bold' : 'text-gray-800 dark:text-gray-100 hover:bg-gray-200 dark:hover:bg-gray-700' }}"
               href="{{ route('parent.notifications') }}">
                <div class="flex items-center">
                    <svg class="shrink-0 fill-current {{ Route::is('parent.notifications*') ? 'text-white' : 'text-gray-400 dark:text-gray-500' }}" width="16" height="16" viewBox="0 0 24 24">
                        <path d="M12 22c1.1 0 2-.9 2-2h-4c0 1.1.9 2 2 2zm6-6v-5c0-3.07-1.64-5.64-4.5-6.32V4c0-.83-.67-1.5-1.5-1.5s-1.5.67-1.5 1.5v.68C7.63 5.36 6 7.92 6 11v5l-2 2v1h16v-1l-2-2z"/>
                    </svg>
                    <span class="text-sm ml-2 sidebar-text duration-200">Notifications</span>
                </div>
            </a>
        </li>

        <li class="mb-0.5 last:mb-0" title="Join General Exams">
            <a class="block px-3 py-2 rounded-lg transition {{ Route::is('general-exams.join*') ? 'bg-violet-500 text-white font-bold' : 'text-gray-800 dark:text-gray-100 hover:bg-gray-200 dark:hover:bg-gray-700' }}"
               href="{{ route('general-exams.join') }}">
                <div class="flex items-center">
                    <svg class="shrink-0 fill-current {{ Route::is('general-exams.join*') ? 'text-white' : 'text-gray-400 dark:text-gray-500' }}" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24">
                        <path d="M19 3h-4.18C14.4 1.84 13.3 1 12 1c-1.3 0-2.4.84-2.82 2H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V5a2 2 0 0 0-2-2zm-7 0c.55 0 1 .45 1 1s-.45 1-1 1-1-.45-1-1 .45-1 1-1zm-2 14h-3v-2h3v2zm5-4h-8v-2h8v2zm0-4h-8V7h8v4z"/>
                    </svg>
                    <span class="text-sm ml-2 sidebar-text duration-200">Join General Exams</span>
                </div>
            </a>
        </li>

        <!-- Book Subscriptions -->
        <li class="mb-0.5 last:mb-0">
           <a class="block px-3 py-2 rounded-lg transition {{ Route::is('parent.books*') ? 'bg-violet-500 text-white font-bold' : 'text-gray-800 dark:text-gray-100 hover:bg-gray-200 dark:hover:bg-gray-700' }}"
               href="{{ route('parent.books') }}">
                <div class="flex items-center">
                    <svg class="shrink-0 fill-current {{ Route::is('parent.books*') ? 'text-white' : 'text-gray-400 dark:text-gray-500' }}" width="16" height="16" viewBox="0 0 24 24">
                        <path d="M21 5c-1.11-.35-2.33-.5-3.5-.5-1.95 0-4.05.4-5.5 1.5-1.45-1.1-3.55-1.5-5.5-1.5S2.45 4.9 1 6v14.65c0 .25.25.5.5.5.1 0 .15-.05.25-.05C3.1 20.45 5.05 20 6.5 20c1.95 0 4.05.4 5.5 1.5 1.35-.85 3.8-1.5 5.5-1.5 1.65 0 3.35.3 4.75 1.05.1.05.15.05.25.05.25 0 .5-.25.5-.5V6c-.6-.45-1.25-.75-2-1zm0 13.5c-1.1-.35-2.3-.5-3.5-.5-1.7 0-4.15.65-5.5 1.5V8c1.35-.85 3.8-1.5 5.5-1.5 1.2 0 2.4.15 3.5.5v11.5z"/>
                    </svg>
                    <span class="text-sm ml-2 sidebar-text duration-200">Book Subscriptions</span>
                </div>
            </a>
        </li>

        <!-- Kids Books -->
        <li class="mb-0.5 last:mb-0">
           <a class="block px-3 py-2 rounded-lg transition {{ Route::is('kids-books*') ? 'bg-violet-500 text-white font-bold' : 'text-gray-800 dark:text-gray-100 hover:bg-gray-200 dark:hover:bg-gray-700' }}"
               href="{{ route('kids-books.index') }}">
                <div class="flex items-center">
                    <svg class="shrink-0 fill-current {{ Route::is('kids-books*') ? 'text-white' : 'text-gray-400 dark:text-gray-500' }}" width="16" height="16" viewBox="0 0 16 16">
                        <path d="M4 2a2 2 0 0 0-2 2v8a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2H4zm0 1h8a1 1 0 0 1 1 1v8a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V4a1 1 0 0 1 1-1z"/>
                        <path d="M6 7a1 1 0 1 0 0-2 1 1 0 0 0 0 2zm4 0a1 1 0 1 0 0-2 1 1 0 0 0 0 2zM5 9h6v1H5V9zm1 2h4v1H6v-1z"/>
                    </svg>
                    <span class="text-sm ml-2 sidebar-text duration-200">Children's Books</span>
                </div>
            </a>
        </li>

        <!-- Library -->
        <li class="mb-0.5 last:mb-0 hidden">
           <a class="block px-3 py-2 rounded-lg transition {{ Route::is('parent.library*') ? 'bg-violet-500 text-white font-bold' : 'text-gray-800 dark:text-gray-100 hover:bg-gray-200 dark:hover:bg-gray-700' }}"
               href="{{ route('parent.library') }}">
                <div class="flex items-center">
                    <svg class="shrink-0 fill-current {{ Route::is('parent.library*') ? 'text-white' : 'text-gray-400 dark:text-gray-500' }}" width="16" height="16" viewBox="0 0 24 24">
                        <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                    </svg>
                    <span class="text-sm ml-2 sidebar-text duration-200">Digital Library</span>
                </div>
            </a>
        </li>

        <!-- LMS Divider -->
        <li class="my-4 border-t border-gray-200 dark:border-gray-700"></li>
        <h3 class="text-xs uppercase text-gray-400 dark:text-gray-500 font-semibold pl-3 mb-2">
            <span class="lg:hidden lg:sidebar-expanded:block 2xl:block">Learning Management</span>
        </h3>

        <!-- Browse Courses -->
        <li class="mb-0.5 last:mb-0" title="Browse Courses">
            <a class="block px-3 py-2 rounded-lg transition {{ Route::is('lms.courses.*') ? 'bg-violet-500 text-white font-bold' : 'text-gray-800 dark:text-gray-100 hover:bg-gray-200 dark:hover:bg-gray-700' }}"
               href="{{ route('lms.courses.index') }}">
                <div class="flex items-center">
                    <svg class="shrink-0 fill-current {{ Route::is('lms.courses.*') ? 'text-white' : 'text-gray-400 dark:text-gray-500' }}"
                         xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16">
                        <path d="M12 0H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2zM4 1h8a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1z"/>
                        <path d="M11 3H5a.5.5 0 0 0 0 1h6a.5.5 0 0 0 0-1zm0 2H5a.5.5 0 0 0 0 1h6a.5.5 0 0 0 0-1zm0 2H5a.5.5 0 0 0 0 1h6a.5.5 0 0 0 0-1zm0 2H5a.5.5 0 0 0 0 1h6a.5.5 0 0 0 0-1z"/>
                    </svg>
                    <span class="text-sm ml-2 sidebar-text duration-200">Browse Courses</span>
                </div>
            </a>
        </li>

        <!-- My Learning -->
        <li class="mb-0.5 last:mb-0" title="My Learning">
            <a class="block px-3 py-2 rounded-lg transition {{ Route::is('my-learning.index') ? 'bg-violet-500 text-white font-bold' : 'text-gray-800 dark:text-gray-100 hover:bg-gray-200 dark:hover:bg-gray-700' }}"
               href="{{ route('my-learning.index') }}">
                <div class="flex items-center">
                    <svg class="shrink-0 fill-current {{ Route::is('my-learning.index') ? 'text-white' : 'text-gray-400 dark:text-gray-500' }}"
                         xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16">
                        <path d="M1 2.828c.885-.37 2.154-.769 3.388-.893 1.33-.134 2.458.063 3.112.752v9.746c-.933-.475-2.393-.967-4.109-.893a11.95 11.95 0 0 0-2.39.238V2.828zM10 12.396c.654-.689 1.782-.886 3.112-.752 1.234.124 2.503.523 3.388.893V2.828a11.95 11.95 0 0 0-2.39-.238c-1.716-.074-3.176.418-4.109.893v9.746zM8 1.783C7.015.936 5.587.81 4.287.94c-1.514.153-3.042.672-3.994 1.105A.5.5 0 0 0 0 2.5v11a.5.5 0 0 0 .707.455c.882-.4 2.303-.881 3.68-1.02 1.409-.142 2.59.087 3.223.877a.5.5 0 0 0 .78 0c.633-.79 1.814-1.019 3.222-.877 1.378.139 2.8.62 3.681 1.02a.5.5 0 0 0 .707-.455v-11a.5.5 0 0 0-.293-.452C14.958.81 13.53.936 12.517 1.783c-1.017.847-2.184 1.174-3.517 1.174s-2.499-.327-3.517-1.174z"/>
                    </svg>
                    <span class="text-sm ml-2 sidebar-text duration-200">My Learning</span>
                </div>
            </a>
        </li>
    </ul>
</div>
