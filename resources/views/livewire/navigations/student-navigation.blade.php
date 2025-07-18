<div>
            <h3 class="text-xs uppercase text-gray-400 dark:text-gray-500 font-semibold pl-3">
                <span class="hidden lg:hidden lg:sidebar-expanded:hidden 2xl:hidden text-center w-6" aria-hidden="true">•••</span>
                <span class="lg:hidden lg:sidebar-expanded:block 2xl:block">Student Portal</span>
            </h3>
            <ul class="mt-3">
                <!-- Main -->
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

                <!-- Learning -->
                <li class="mb-0.5 last:mb-0">
                    <a class="block pl-4 pr-3 py-2 rounded-lg transition {{ Route::is('student.assessments') ? 'bg-violet-500 text-white font-bold' : 'text-gray-800 dark:text-gray-100 hover:bg-gray-200 dark:hover:bg-gray-700' }}"
                       href="{{route('student.assessments')}}">
                        <div class="flex items-center">
                            <svg class="shrink-0 fill-current {{ Route::is('student.assessments') ? 'text-white' : 'text-gray-400 dark:text-gray-500' }}" width="16" height="16" viewBox="0 0 24 24">
                                <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/>
                            </svg>
                            <span class="text-sm ml-4 sidebar-text duration-200">Learning Center</span>
                        </div>
                    </a>
                </li>

                <li class="mb-0.5 last:mb-0">
                    <a class="block pl-4 pr-3 py-2 rounded-lg transition {{ Route::is('student.assignments') ? 'bg-violet-500 text-white font-bold' : 'text-gray-800 dark:text-gray-100 hover:bg-gray-200 dark:hover:bg-gray-700' }}"
                       href="{{route('student.assignments')}}">
                        <div class="flex items-center">
                            <svg class="shrink-0 fill-current {{ Route::is('student.assignments') ? 'text-white' : 'text-gray-400 dark:text-gray-500' }}" width="16" height="16" viewBox="0 0 24 24">
                                <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/>
                            </svg>
                            <span class="text-sm ml-4 sidebar-text duration-200">Assignments</span>
                        </div>
                    </a>
                </li>

                <!-- Resources -->
                <li class="mb-0.5 last:mb-0">
                    <a class="block pl-4 pr-3 py-2 rounded-lg transition {{ Route::is('student.books') ? 'bg-violet-500 text-white font-bold' : 'text-gray-800 dark:text-gray-100 hover:bg-gray-200 dark:hover:bg-gray-700' }}"
                       href="{{route('student.books')}}">
                        <div class="flex items-center">
                            <svg class="shrink-0 fill-current {{ Route::is('student.books') ? 'text-white' : 'text-gray-400 dark:text-gray-500' }}" width="16" height="16" viewBox="0 0 24 24">
                                <path d="M21 5c-1.11-.35-2.33-.5-3.5-.5-1.95 0-4.05.4-5.5 1.5-1.45-1.1-3.55-1.5-5.5-1.5S2.45 4.9 1 6v14.65c0 .25.25.5.5.5.1 0 .15-.05.25-.05C3.1 20.45 5.05 20 6.5 20c1.95 0 4.05.4 5.5 1.5 1.35-.85 3.8-1.5 5.5-1.5 1.65 0 3.35.3 4.75 1.05.1.05.15.05.25.05.25 0 .5-.25.5-.5V6c-.6-.45-1.25-.75-2-1zm0 13.5c-1.1-.35-2.3-.5-3.5-.5-1.7 0-4.15.65-5.5 1.5V8c1.35-.85 3.8-1.5 5.5-1.5 1.2 0 2.4.15 3.5.5v11.5z"/>
                            </svg>
                            <span class="text-sm ml-4 sidebar-text duration-200">Books</span>
                        </div>
                    </a>
                </li>

                <li class="mb-0.5 last:mb-0">
                    <a class="block pl-4 pr-3 py-2 rounded-lg transition {{ Route::is('student.courses') ? 'bg-violet-500 text-white font-bold' : 'text-gray-800 dark:text-gray-100 hover:bg-gray-200 dark:hover:bg-gray-700' }}"
                       href="{{route('student.courses')}}">
                        <div class="flex items-center">
                            <svg class="shrink-0 fill-current {{ Route::is('student.courses') ? 'text-white' : 'text-gray-400 dark:text-gray-500' }}" width="16" height="16" viewBox="0 0 24 24">
                                <path d="M12 3L1 9l4 2.18v6L12 21l7-3.82v-6l2-1.09V17h2V9L12 3zm6.82 6L12 12.72 5.18 9 12 5.28 18.82 9zM17 15.99l-5 2.73-5-2.73v-3.72L12 15l5-2.73v3.72z"/>
                            </svg>
                            <span class="text-sm ml-4 sidebar-text duration-200">Course Subscriptions</span>
                        </div>
                    </a>
                </li>

                <!-- Schedule -->
                <li class="mb-0.5 last:mb-0">
                    <a class="block pl-4 pr-3 py-2 rounded-lg transition {{ Route::is('student.schedules') ? 'bg-violet-500 text-white font-bold' : 'text-gray-800 dark:text-gray-100 hover:bg-gray-200 dark:hover:bg-gray-700' }}"
                       href="{{route('student.schedules')}}">
                        <div class="flex items-center">
                            <svg class="shrink-0 fill-current {{ Route::is('student.schedules') ? 'text-white' : 'text-gray-400 dark:text-gray-500' }}" width="16" height="16" viewBox="0 0 24 24">
                                <path d="M19 3h-1V1h-2v2H8V1H6v2H5c-1.11 0-2 .9-2 2v14c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V8h14v11zM7 10h5v5H7z"/>
                            </svg>
                            <span class="text-sm ml-4 sidebar-text duration-200">Schedules</span>
                        </div>
                    </a>
                </li>

                <!-- Academic -->
                <li class="mb-0.5 last:mb-0">
                    <a class="block pl-4 pr-3 py-2 rounded-lg transition {{ Route::is('student.performance') ? 'bg-violet-500 text-white font-bold' : 'text-gray-800 dark:text-gray-100 hover:bg-gray-200 dark:hover:bg-gray-700' }}"
                       href="{{route('student.performance')}}">
                        <div class="flex items-center">
                            <svg class="shrink-0 fill-current {{ Route::is('student.performance') ? 'text-white' : 'text-gray-400 dark:text-gray-500' }}" width="16" height="16" viewBox="0 0 24 24">
                                <path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-7 2h7v2h-7V5zm0 4h7v2h-7V9zm0 4h7v2h-7v-2zM5 19V5h2v14H5zm4 0V5h2v14H9z"/>
                            </svg>
                            <span class="text-sm ml-4 sidebar-text duration-200">Academic Performance</span>
                        </div>
                    </a>
                </li>

                <!-- Account -->
                <li class="mb-0.5 last:mb-0">
                    <a class="block pl-4 pr-3 py-2 rounded-lg transition {{ Route::is('student.account') ? 'bg-violet-500 text-white font-bold' : 'text-gray-800 dark:text-gray-100 hover:bg-gray-200 dark:hover:bg-gray-700' }}"
                       href="{{route('student.account')}}">
                        <div class="flex items-center">
                            <svg class="shrink-0 fill-current {{ Route::is('student.account') ? 'text-white' : 'text-gray-400 dark:text-gray-500' }}" width="16" height="16" viewBox="0 0 24 24">
                                <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                            </svg>
                            <span class="text-sm ml-4 sidebar-text duration-200">My Profile</span>
                        </div>
                    </a>
                </li>

                <li class="mb-0.5 last:mb-0">
                    <a class="block pl-4 pr-3 py-2 rounded-lg transition {{ Route::is('student.notifications.index') ? 'bg-violet-500 text-white font-bold' : 'text-gray-800 dark:text-gray-100 hover:bg-gray-200 dark:hover:bg-gray-700' }}"
                       href="{{route('student.notifications.index')}}">
                        <div class="flex items-center">
                            <svg class="shrink-0 fill-current {{ Route::is('student.notifications.index') ? 'text-white' : 'text-gray-400 dark:text-gray-500' }}" width="16" height="16" viewBox="0 0 24 24">
                                <path d="M12 22c1.1 0 2-.9 2-2h-4c0 1.1.9 2 2 2zm6-6v-5c0-3.07-1.63-5.64-4.5-6.32V4c0-.83-.67-1.5-1.5-1.5s-1.5.67-1.5 1.5v.68C7.64 5.36 6 7.92 6 11v5l-2 2v1h16v-1l-2-2zm-2 1H8v-6c0-2.48 1.51-4.5 4-4.5s4 2.02 4 4.5v6z"/>
                            </svg>
                            <span class="text-sm ml-4 sidebar-text duration-200">Notifications</span>
                        </div>
                    </a>
                </li>

                <!-- Activity -->
                <li class="mb-0.5 last:mb-0">
                    <a class="block pl-4 pr-3 py-2 rounded-lg transition {{ Route::is('student.activities') ? 'bg-violet-500 text-white font-bold' : 'text-gray-800 dark:text-gray-100 hover:bg-gray-200 dark:hover:bg-gray-700' }}"
                       href="{{route('student.activities')}}">
                        <div class="flex items-center">
                            <svg class="shrink-0 fill-current {{ Route::is('student.activities') ? 'text-white' : 'text-gray-400 dark:text-gray-500' }}" width="16" height="16" viewBox="0 0 24 24">
                                <path d="M13 3c-4.97 0-9 4.03-9 9H1l3.89 3.89.07.14L9 12H6c0-3.87 3.13-7 7-7s7 3.13 7 7-3.13 7-7 7c-1.93 0-3.68-.79-4.94-2.06l-1.42 1.42C8.27 19.99 10.51 21 13 21c4.97 0 9-4.03 9-9s-4.03-9-9-9zm-1 5v5l4.28 2.54.72-1.21-3.5-2.08V8H12z"/>
                            </svg>
                            <span class="text-sm ml-4 sidebar-text duration-200">Activities</span>
                        </div>
                    </a>
                </li>
            </ul>
        </div>
