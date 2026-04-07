<div>
    <ul class="mt-3">
        <!-- Main -->
        <li class="mb-0.5 last:mb-0" title="Dashboard">
            <a class="block px-3 py-2 rounded-lg transition {{ Route::is('dashboard') ? 'bg-violet-500 text-white font-bold' : 'text-gray-800 dark:text-gray-100 hover:bg-gray-200 dark:hover:bg-gray-700' }}"
               href="{{route('dashboard')}}">
                <div class="flex items-center">
                    <svg
                        class="shrink-0 fill-current {{ Route::is('dashboard') ? 'text-white' : 'text-gray-400 dark:text-gray-500' }}"
                        width="16" height="16" viewBox="0 0 24 24">
                        <path d="M3 13h8V3H3v10zm0 8h8v-6H3v6zm10 0h8V11h-8v10zm0-18v6h8V3h-8z"/>
                    </svg>
                    <span class="text-sm ml-2 sidebar-text duration-200">Dashboard</span>
                </div>
            </a>
        </li>

        <li class="mb-0.5 last:mb-0 " title="Books">
            <a class="block px-3 py-2 rounded-lg transition {{ Route::is('books*') ? 'bg-violet-500 text-white font-bold' : 'text-gray-800 dark:text-gray-100 hover:bg-gray-200 dark:hover:bg-gray-700' }}"
               href="{{route('books.index')}}">
                <div class="flex items-center">
                    <svg
                        class="shrink-0 fill-current {{ Route::is('books*') ? 'text-white' : 'text-gray-400 dark:text-gray-500' }}"
                        xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16">
                        <path
                            d="M1 2.828c.885-.37 2.154-.769 3.388-.893 1.33-.134 2.458.063 3.112.752v9.746c-.935-.53-2.12-.603-3.213-.493-1.18.12-2.37.461-3.287.811V2.828zm7.5-.141c.654-.689 1.782-.886 3.112-.752 1.234.124 2.503.523 3.388.893v9.923c-.918-.35-2.107-.692-3.287-.81-1.094-.111-2.278-.039-3.213.492V2.687zM8 1.783C7.015.936 5.587.81 4.287.94c-1.514.153-3.042.672-3.994 1.105A.5.5 0 0 0 0 2.5v11a.5.5 0 0 0 .707.455c.882-.4 2.303-.881 3.68-1.02 1.409-.142 2.59.087 3.223.877a.5.5 0 0 0 .78 0c.633-.79 1.814-1.019 3.222-.877 1.378.139 2.8.62 3.681 1.02A.5.5 0 0 0 16 13.5v-11a.5.5 0 0 0-.293-.455c-.952-.433-2.48-.952-3.994-1.105C10.413.809 8.985.936 8 1.783z"/>
                    </svg>
                    <span class="text-sm ml-2 sidebar-text duration-200">Books</span>
                </div>
            </a>
        </li>

        <li class="mb-0.5 last:mb-0 " title="Kids Books">
            <a class="block px-3 py-2 rounded-lg transition {{ Route::is('kids-books*') ? 'bg-violet-500 text-white font-bold' : 'text-gray-800 dark:text-gray-100 hover:bg-gray-200 dark:hover:bg-gray-700' }}"
               href="{{route('kids-books.index')}}">
                <div class="flex items-center">
                    <svg
                        class="shrink-0 fill-current {{ Route::is('kids-books*') ? 'text-white' : 'text-gray-400 dark:text-gray-500' }}"
                        xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16">
                        <path
                            d="M4 2a2 2 0 0 0-2 2v8a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2H4zm0 1h8a1 1 0 0 1 1 1v8a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V4a1 1 0 0 1 1-1z"/>
                        <path
                            d="M6 7a1 1 0 1 0 0-2 1 1 0 0 0 0 2zm4 0a1 1 0 1 0 0-2 1 1 0 0 0 0 2zM5 9h6v1H5V9zm1 2h4v1H6v-1z"/>
                    </svg>
                    <span class="text-sm ml-2 sidebar-text duration-200">Kids Books</span>
                </div>
            </a>
        </li>
        <!-- Academic Chat -->
        <li class="mb-0.5 last:mb-0" title="Research Assistant">
            <a class="block px-3 py-2 rounded-lg transition {{ Route::is('academic-chat*') ? 'bg-violet-500 text-white font-bold' : 'text-gray-800 dark:text-gray-100 hover:bg-gray-200 dark:hover:bg-gray-700' }}"
               href="{{route('academic-chat.index')}}">
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

        <!-- Learning -->
        <li class="mb-0.5 last:mb-0" title="Learning Center">
            <a class="block px-3 py-2 rounded-lg transition {{ Route::is('students.assessments') ? 'bg-violet-500 text-white font-bold' : 'text-gray-800 dark:text-gray-100 hover:bg-gray-200 dark:hover:bg-gray-700' }}"
               href="{{route('students.assessments')}}">
                <div class="flex items-center">
                    <svg
                        class="shrink-0 fill-current {{ Route::is('students.assessments') ? 'text-white' : 'text-gray-400 dark:text-gray-500' }}"
                        width="16" height="16" viewBox="0 0 24 24">
                        <path d="M10 20h4V4h-4v16zm-6 0h4v-8H4v8zM16 9v11h4V9h-4z"/>
                    </svg>
                    <span class="text-sm ml-2 sidebar-text duration-200">Quiz Generator</span>
                </div>
            </a>
        </li>

        <li class="mb-0.5 last:mb-0" title="Assessment Performance">
            <a class="block px-3 py-2 text-nowrap overflow-ellipsis rounded-lg transition {{ Route::is('quiz.performance*') ? 'bg-violet-500 text-white font-bold' : 'text-gray-800 dark:text-gray-100 hover:bg-gray-200 dark:hover:bg-gray-700' }}"
               href="{{ route('quiz.performance') }}">
                <div class="flex items-center">
                    <svg
                        class="shrink-0 fill-current {{ Route::is('quiz.performance*') ? 'text-white' : 'text-gray-400 dark:text-gray-500' }}"
                        xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16">
                        <path
                            d="M0 0h1v15h15v1H0V0Zm10 3.5a.5.5 0 0 1 .5-.5h4a.5.5 0 0 1 .5.5v4a.5.5 0 0 1-1 0V4.9l-3.613 4.417a.5.5 0 0 1-.74.037L7.06 6.767l-3.656 5.027a.5.5 0 0 1-.808-.588l4-5.5a.5.5 0 0 1 .758-.06l2.609 2.61L13.445 4H10.5a.5.5 0 0 1-.5-.5Z"/>
                    </svg>
                    <span class="text-sm text-nowrap overflow-ellipsis ml-4 sidebar-text duration-200">Assessment Performance</span>
                </div>
            </a>
        </li>

        <li class="mb-0.5 last:mb-0" title="Assignments">
            <a class="block px-3 py-2 rounded-lg transition {{ Route::is('students.assignments') ? 'bg-violet-500 text-white font-bold' : 'text-gray-800 dark:text-gray-100 hover:bg-gray-200 dark:hover:bg-gray-700' }}"
               href="{{route('students.assignments')}}">
                <div class="flex items-center">
                    <svg
                        class="shrink-0 fill-current {{ Route::is('students.assignments') ? 'text-white' : 'text-gray-400 dark:text-gray-500' }}"
                        width="16" height="16" viewBox="0 0 24 24">
                        <path
                            d="M17 12H7v-2h10v2zm-4 2H7v2h6v-2zm8-8v12c0 1.1-.9 2-2 2H5c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2h14c1.1 0 2 .9 2 2zm-2 0H5v12h14V6z"/>
                    </svg>
                    <span class="text-sm ml-2 sidebar-text duration-200">Assignments</span>
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

        <li class="mb-0.5 last:mb-0" title="Messages">
            <a class="block px-3 py-2 rounded-lg transition {{ Route::is('students.messages*') ? 'bg-violet-500 text-white font-bold' : 'text-gray-800 dark:text-gray-100 hover:bg-gray-200 dark:hover:bg-gray-700' }}"
               href="{{route('students.messages.index')}}">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <svg
                            class="shrink-0 fill-current {{ Route::is('students.messages*') ? 'text-white' : 'text-gray-400 dark:text-gray-500' }}"
                            xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16">
                            <path
                                d="M0 4a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V4zm2-1a1 1 0 0 0-1 1v.217l7 4.2 7-4.2V4a1 1 0 0 0-1-1H2zm13 2.383-4.758 2.855L15 11.114v-5.73zm-.034 6.878L9.271 8.82 8 9.583 6.728 8.82l-5.694 3.44A1 1 0 0 0 2 13h12a1 1 0 0 0 .966-.739zM1 11.114l4.758-2.876L1 5.383v5.73z"/>
                        </svg>
                        <span class="text-sm ml-2 sidebar-text duration-200">Message Center</span>
                    </div>
                </div>
            </a>
        </li>


        <!-- Chat System -->
        <li class="mb-0.5 last:mb-0" title="Group Chat">
            <a class="block px-3 py-2 rounded-lg transition {{ Route::is('chat*') ? 'bg-violet-500 text-white font-bold' : 'text-gray-800 dark:text-gray-100 hover:bg-gray-200 dark:hover:bg-gray-700' }}"
               href="{{route('chat')}}">
                <div class="flex items-center">
                    <svg
                        class="shrink-0 fill-current {{ Route::is('chat*') ? 'text-white' : 'text-gray-400 dark:text-gray-500' }}"
                        xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16">
                        <path
                            d="M8 15c4.418 0 8-3.134 8-7s-3.582-7-8-7-8 3.134-8 7c0 1.76.743 3.37 1.97 4.6-.097 1.016-.417 2.13-.771 2.966-.079.186.074.394.273.362 2.256-.37 3.597-.938 4.18-1.234A9.06 9.06 0 0 0 8 15z"/>
                    </svg>
                    <span class="text-sm ml-2 sidebar-text duration-200">Group Chat</span>
                </div>
            </a>
        </li>

        <!-- Payments -->
        <li class="mb-0.5 last:mb-0" title="Payments">
            <a class="block px-3 py-2 rounded-lg transition {{ Route::is('students.fees*') ? 'bg-violet-500 text-white font-bold' : 'text-gray-800 dark:text-gray-100 hover:bg-gray-200 dark:hover:bg-gray-700' }}"
               href="{{route('students.payments.index')}}">
                <div class="flex items-center">
                    <svg
                        class="shrink-0 fill-current {{ Route::is('students.fees*') ? 'text-white' : 'text-gray-400 dark:text-gray-500' }}"
                        width="16" height="16" viewBox="0 0 24 24">
                        <path
                            d="M20 4H4c-1.11 0-1.99.89-1.99 2L2 18c0 1.11.89 2 2 2h16c1.11 0 2-.89 2-2V6c0-1.11-.89-2-2-2zm0 14H4v-6h16v6zm0-10H4V6h16v2z"/>
                    </svg>
                    <span class="text-sm ml-2 sidebar-text duration-200">Payments</span>
                </div>
            </a>
        </li>


        <!-- Schedule -->
        <li class="mb-0.5 last:mb-0" title="Schedule">
            <a class="block px-3 py-2 rounded-lg transition {{ Route::is('students.schedules') ? 'bg-violet-500 text-white font-bold' : 'text-gray-800 dark:text-gray-100 hover:bg-gray-200 dark:hover:bg-gray-700' }}"
               href="{{route('students.schedules')}}">
                <div class="flex items-center">
                    <svg
                        class="shrink-0 fill-current {{ Route::is('students.schedules') ? 'text-white' : 'text-gray-400 dark:text-gray-500' }}"
                        width="16" height="16" viewBox="0 0 24 24">
                        <path
                            d="M19 3h-1V1h-2v2H8V1H6v2H5c-1.11 0-2 .9-2 2v14c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V8h14v11zM7 10h5v5H7z"/>
                    </svg>
                    <span class="text-sm ml-2 sidebar-text duration-200">Schedules</span>
                </div>
            </a>
        </li>

        <!-- Academic -->
        <li class="mb-0.5 last:mb-0" title="Academic Performance">
            <a class="block px-3 py-2 rounded-lg transition {{ Route::is('students.performance') ? 'bg-violet-500 text-white font-bold' : 'text-gray-800 dark:text-gray-100 hover:bg-gray-200 dark:hover:bg-gray-700' }}"
               href="{{route('students.performance')}}">
                <div class="flex items-center">
                    <svg
                        class="shrink-0 fill-current {{ Route::is('students.performance') ? 'text-white' : 'text-gray-400 dark:text-gray-500' }}"
                        width="16" height="16" viewBox="0 0 24 24">
                        <path
                            d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-7 2h7v2h-7V5zm0 4h7v2h-7V9zm0 4h7v2h-7v-2zM5 19V5h2v14H5zm4 0V5h2v14H9z"/>
                    </svg>
                    <span class="text-sm ml-2 sidebar-text duration-200">Academic Performance</span>
                </div>
            </a>
        </li>

        <!-- Report Cards -->
        <li class="mb-0.5 last:mb-0" title="Report Cards">
            <a class="block px-3 py-2 rounded-lg transition {{ Route::is('students.report-cards') ? 'bg-violet-500 text-white font-bold' : 'text-gray-800 dark:text-gray-100 hover:bg-gray-200 dark:hover:bg-gray-700' }}"
               href="{{route('students.report-cards')}}">
                <div class="flex items-center">
                    <svg
                        class="shrink-0 fill-current {{ Route::is('students.report-cards') ? 'text-white' : 'text-gray-400 dark:text-gray-500' }}"
                        width="16" height="16" viewBox="0 0 24 24">
                        <path d="M14 2H6c-1.1 0-2 .9-2 2v16c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V8l-6-6zm4 18H6V4h7v5h5v11zM8 15h8v2H8v-2zm0-3h8v2H8v-2zm0-3h5v2H8V9z"/>
                    </svg>
                    <span class="text-sm ml-2 sidebar-text duration-200">Report Cards</span>
                </div>
            </a>
        </li>

        <li class="mb-0.5 last:mb-0" title="Forums">
            <a class="block px-3 py-2 rounded-lg transition {{ Route::is('forums*') ? 'bg-violet-500 text-white font-bold' : 'text-gray-800 dark:text-gray-100 hover:bg-gray-200 dark:hover:bg-gray-700' }}"
               href="{{route('forums')}}">
                <div class="flex items-center">
                    <svg
                        class="shrink-0 fill-current {{ Route::is('forums*') ? 'text-white' : 'text-gray-400 dark:text-gray-500' }}"
                        width="16" height="16" viewBox="0 0 24 24">
                        <path
                            d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-7 2h7v2h-7V5zm0 4h7v2h-7V9zm0 4h7v2h-7v-2zM5 19V5h2v14H5zm4 0V5h2v14H9z"/>
                    </svg>
                    <span class="text-sm ml-2 sidebar-text duration-200">Forums</span>
                </div>
            </a>
        </li>

        <!-- Account -->
        <li class="mb-0.5 last:mb-0" title="My Profile">
            <a class="block px-3 py-2 rounded-lg transition {{ Route::is('students.account') ? 'bg-violet-500 text-white font-bold' : 'text-gray-800 dark:text-gray-100 hover:bg-gray-200 dark:hover:bg-gray-700' }}"
               href="{{route('students.account')}}">
                <div class="flex items-center">
                    <svg
                        class="shrink-0 fill-current {{ Route::is('students.account') ? 'text-white' : 'text-gray-400 dark:text-gray-500' }}"
                        width="16" height="16" viewBox="0 0 24 24">
                        <path
                            d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                    </svg>
                    <span class="text-sm ml-2 sidebar-text duration-200">My Profile</span>
                </div>
            </a>
        </li>

        <li class="mb-0.5 last:mb-0" title="Notifications">
            <a class="block px-3 py-2 rounded-lg transition {{ Route::is('students.notifications*') ? 'bg-violet-500 text-white font-bold' : 'text-gray-800 dark:text-gray-100 hover:bg-gray-200 dark:hover:bg-gray-700' }}"
               href="{{route('students.notifications')}}">
                <div class="flex items-center">
                    <svg
                        class="shrink-0 fill-current {{ Route::is('notifications*') ? 'text-white' : 'text-gray-400 dark:text-gray-500' }}"
                        width="16" height="16" viewBox="0 0 24 24">
                        <path
                            d="M12 22c1.1 0 2-.9 2-2h-4c0 1.1.9 2 2 2zm6-6v-5c0-3.07-1.63-5.64-4.5-6.32V4c0-.83-.67-1.5-1.5-1.5s-1.5.67-1.5 1.5v.68C7.64 5.36 6 7.92 6 11v5l-2 2v1h16v-1l-2-2zm-2 1H8v-6c0-2.48 1.51-4.5 4-4.5s4 2.02 4 4.5v6z"/>
                    </svg>
                    <span class="text-sm ml-2 sidebar-text duration-200">Notifications</span>
                </div>
            </a>
        </li>

        <!-- Activity -->
        <li class="mb-0.5 last:mb-0" title="Activities">
            <a class="block px-3 py-2 rounded-lg transition {{ Route::is('students.activities') ? 'bg-violet-500 text-white font-bold' : 'text-gray-800 dark:text-gray-100 hover:bg-gray-200 dark:hover:bg-gray-700' }}"
               href="{{route('students.activities')}}">
                <div class="flex items-center">
                    <svg
                        class="shrink-0 fill-current {{ Route::is('students.activities') ? 'text-white' : 'text-gray-400 dark:text-gray-500' }}"
                        width="16" height="16" viewBox="0 0 24 24">
                        <path
                            d="M13 3c-4.97 0-9 4.03-9 9H1l3.89 3.89.07.14L9 12H6c0-3.87 3.13-7 7-7s7 3.13 7 7-3.13 7-7 7c-1.93 0-3.68-.79-4.94-2.06l-1.42 1.42C8.27 19.99 10.51 21 13 21c4.97 0 9-4.03 9-9s-4.03-9-9-9zm-1 5v5l4.28 2.54.72-1.21-3.5-2.08V8H12z"/>
                    </svg>
                    <span class="text-sm ml-2 sidebar-text duration-200">Activities</span>
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

        <!-- My Certificates -->
        <li class="mb-0.5 last:mb-0" title="My Certificates">
            <a class="block px-3 py-2 rounded-lg transition {{ Route::is('my-learning.certificates') ? 'bg-violet-500 text-white font-bold' : 'text-gray-800 dark:text-gray-100 hover:bg-gray-200 dark:hover:bg-gray-700' }}"
               href="{{ route('my-learning.certificates') }}">
                <div class="flex items-center">
                    <svg class="shrink-0 fill-current {{ Route::is('my-learning.certificates') ? 'text-white' : 'text-gray-400 dark:text-gray-500' }}"
                         xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16">
                        <path d="M14.5 3a.5.5 0 0 1 .5.5v9a.5.5 0 0 1-.5.5h-13a.5.5 0 0 1-.5-.5v-9a.5.5 0 0 1 .5-.5h13zm-13-1A1.5 1.5 0 0 0 0 3.5v9A1.5 1.5 0 0 0 1.5 14h13a1.5 1.5 0 0 0 1.5-1.5v-9A1.5 1.5 0 0 0 14.5 2h-13z"/>
                        <path d="M7.002 11a1 1 0 1 1 2 0 1 1 0 0 1-2 0zM7.1 4.995a.905.905 0 1 1 1.8 0l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 4.995z"/>
                    </svg>
                    <span class="text-sm ml-2 sidebar-text duration-200">My Certificates</span>
                </div>
            </a>
        </li>
    </ul>
</div>
