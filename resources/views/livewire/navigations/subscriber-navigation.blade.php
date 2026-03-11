<div>
    <h3 class="text-xs uppercase text-gray-400 dark:text-gray-500 font-semibold pl-3">
        <span class="hidden lg:block lg:sidebar-expanded:hidden 2xl:hidden text-center w-6"
              aria-hidden="true">•••</span>
        <span class="lg:hidden lg:sidebar-expanded:block 2xl:block">Subscriber Menu</span>
    </h3>
    <ul class="mt-3">
        <!-- Dashboard -->
        <li class="mb-0.5 last:mb-0">
            <a class="block px-3 py-2 rounded-lg transition {{ Route::is('dashboard') ? 'bg-violet-500 text-white font-bold' : 'text-gray-800 dark:text-gray-100 hover:bg-gray-200 dark:hover:bg-gray-700' }}"
               href="{{ route('dashboard') }}">
                <div class="flex items-center">
                    <svg
                        class="shrink-0 fill-current {{ Route::is('dashboard') ? 'text-white' : 'text-gray-400 dark:text-gray-500' }}"
                        xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16">
                        <path
                            d="M4 2a2 2 0 0 0-2 2v1h12V4a2 2 0 0 0-2-2H4ZM2 7v5a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V7H2Zm3 2a1 1 0 1 1 2 0 1 1 0 0 1-2 0Zm6 0a1 1 0 1 1 2 0 1 1 0 0 1-2 0Z"/>
                    </svg>
                    <span class="text-sm ml-2 sidebar-text duration-200">Dashboard</span>
                </div>
            </a>
        </li>

        <!-- Browse Free Books -->
        <li class="mb-0.5 last:mb-0">
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

        <!-- Book Subscriptions -->
        <li class="mb-0.5 hidden last:mb-0">
            <a class="block px-3 py-2 rounded-lg transition {{ Route::is('subscriber.book-subscriptions') ? 'bg-violet-500 text-white font-bold' : 'text-gray-800 dark:text-gray-100 hover:bg-gray-200 dark:hover:bg-gray-700' }}"
               href="{{ route('subscriptions.index') }}">
                <div class="flex items-center">
                    <svg
                        class="shrink-0 fill-current {{ Route::is('subscriber.book-subscriptions') ? 'text-white' : 'text-gray-400 dark:text-gray-500' }}"
                        xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16">
                        <path
                            d="M8 4.754a3.246 3.246 0 1 0 0 6.492 3.246 3.246 0 0 0 0-6.492zM5.754 8a2.246 2.246 0 1 1 4.492 0 2.246 2.246 0 0 1-4.492 0z"/>
                        <path
                            d="M9.796 1.343c-.527-1.79-3.065-1.79-3.592 0l-.094.319a.873.873 0 0 1-1.255.52l-.292-.16c-1.64-.892-3.433.902-2.54 2.541l.159.292a.873.873 0 0 1-.52 1.255l-.319.094c-1.79.527-1.79 3.065 0 3.592l.319.094a.873.873 0 0 1 .52 1.255l-.16.292c-.892 1.64.901 3.434 2.541 2.54l.292-.159a.873.873 0 0 1 1.255.52l.094.319c.527 1.79 3.065 1.79 3.592 0l.094-.319a.873.873 0 0 1 1.255-.52l.292.16c1.64.893 3.434-.902 2.54-2.541l-.159-.292a.873.873 0 0 1 .52-1.255l.319-.094c1.79-.527 1.79-3.065 0-3.592l-.319-.094a.873.873 0 0 1-.52-1.255l.16-.292c.893-1.64-.902-3.433-2.541-2.54l-.292.159a.873.873 0 0 1-1.255-.52l-.094-.319zm-2.633.283c.246-.835 1.428-.835 1.674 0l.094.319a1.873 1.873 0 0 0 2.693 1.115l.291-.16c.764-.415 1.6.42 1.184 1.185l-.159.292a1.873 1.873 0 0 0 1.116 2.692l.318.094c.835.246.835 1.428 0 1.674l-.319.094a1.873 1.873 0 0 0-1.115 2.693l.16.291c.415.764-.42 1.6-1.185 1.184l-.291-.159a1.873 1.873 0 0 0-2.693 1.116l-.094.318c-.246.835-1.428.835-1.674 0l-.094-.319a1.873 1.873 0 0 0-2.692-1.115l-.292.16c-.764.415-1.6-.42-1.184-1.185l.159-.291A1.873 1.873 0 0 0 1.945 8.93l-.319-.094c-.835-.246-.835-1.428 0-1.674l.319-.094A1.873 1.873 0 0 0 3.06 4.377l-.16-.292c-.415-.764.42-1.6 1.185-1.184l.292.159a1.873 1.873 0 0 0 2.692-1.115l.094-.319z"/>
                    </svg>
                    <span class="text-sm ml-2 sidebar-text duration-200">Book Subscriptions</span>
                </div>
            </a>
        </li>

        <!-- All Academies Subscription -->
        <li class="mb-0.5 hidden last:mb-0">
            <a class="block px-3 py-2 rounded-lg transition {{ Route::is('subscriber.academy-subscription') ? 'bg-violet-500 text-white font-bold' : 'text-gray-800 dark:text-gray-100 hover:bg-gray-200 dark:hover:bg-gray-700' }}"
               href="{{ route('subscriptions.index') }}">
                <div class="flex items-center">
                    <svg
                        class="shrink-0 fill-current {{ Route::is('subscriber.academy-subscription') ? 'text-white' : 'text-gray-400 dark:text-gray-500' }}"
                        xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16">
                        <path
                            d="M8.211 2.047a.5.5 0 0 0-.422 0l-7.5 3.5a.5.5 0 0 0 .025.917l7.5 3a.5.5 0 0 0 .372 0L14 7.14V13a1 1 0 0 0-1 1v2h3v-2a1 1 0 0 0-1-1V6.739l.686-.275a.5.5 0 0 0 .025-.917l-7.5-3.5Z"/>
                        <path
                            d="M4.176 9.032a.5.5 0 0 0-.656.327l-.5 1.7a.5.5 0 0 0 .294.605l4.5 1.8a.5.5 0 0 0 .372 0l4.5-1.8a.5.5 0 0 0 .294-.605l-.5-1.7a.5.5 0 0 0-.656-.327L8 10.466 4.176 9.032Z"/>
                    </svg>
                    <span class="text-sm ml-2 sidebar-text duration-200">All Subscription</span>
                </div>
            </a>
        </li>

        <li class="mb-0.5 last:mb-0">
            <a class="block px-3 py-2 rounded-lg transition {{ Route::is('learning.quiz') ? 'bg-violet-500 text-white font-bold' : 'text-gray-800 dark:text-gray-100 hover:bg-gray-200 dark:hover:bg-gray-700' }}"
               href="{{ route('learning.quiz') }}">
                <div class="flex items-center">
                    <svg
                        class="shrink-0 fill-current {{ Route::is('learning.quiz') ? 'text-white' : 'text-gray-400 dark:text-gray-500' }}"
                        xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16">
                        <path
                            d="M11.5 5.5a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-1 0V6a.5.5 0 0 0-.5-.5H10a.5.5 0 0 1 0-1h.5a1.5 1.5 0 0 1 1.5 1.5v1a1.5 1.5 0 0 1-1.5 1.5H9a1.5 1.5 0 0 1-1.5-1.5v-1A1.5 1.5 0 0 1 9 4h1a1.5 1.5 0 0 1 1.5 1.5v1a.5.5 0 0 0 .5.5v-1a.5.5 0 0 1 .5-.5zM8 12a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-1 0v-1A.5.5 0 0 1 8 12zm-3-3a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-1 0v-1A.5.5 0 0 1 5 9zm6 0a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-1 0v-1A.5.5 0 0 1 11 9zM8 1a7 7 0 1 0 0 14A7 7 0 0 0 8 1zm0 1a6 6 0 1 1 0 12A6 6 0 0 1 8 2z"/>
                    </svg>
                    <span class="text-sm ml-2 sidebar-text duration-200">Self Assessment</span>
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
        <li class="mb-0.5 last:mb-0" title="Performance">
            <a class="block px-3 py-2 rounded-lg transition {{ Route::is('quiz.performance*') ? 'bg-violet-500 text-white font-bold' : 'text-gray-800 dark:text-gray-100 hover:bg-gray-200 dark:hover:bg-gray-700' }}"
               href="{{ route('quiz.performance') }}">
                <div class="flex items-center">
                    <svg
                        class="shrink-0 fill-current {{ Route::is('quiz.performance*') ? 'text-white' : 'text-gray-400 dark:text-gray-500' }}"
                        xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16">
                        <path
                            d="M0 0h1v15h15v1H0V0Zm10 3.5a.5.5 0 0 1 .5-.5h4a.5.5 0 0 1 .5.5v4a.5.5 0 0 1-1 0V4.9l-3.613 4.417a.5.5 0 0 1-.74.037L7.06 6.767l-3.656 5.027a.5.5 0 0 1-.808-.588l4-5.5a.5.5 0 0 1 .758-.06l2.609 2.61L13.445 4H10.5a.5.5 0 0 1-.5-.5Z"/>
                    </svg>
                    <span class="text-sm ml-2 sidebar-text duration-200">Performance</span>
                </div>
            </a>
        </li>


        <!-- Forums -->
        <li class="mb-0.5 last:mb-0">
            <a class="block px-3 py-2 rounded-lg transition {{ Route::is('subscriber.forums') ? 'bg-violet-500 text-white font-bold' : 'text-gray-800 dark:text-gray-100 hover:bg-gray-200 dark:hover:bg-gray-700' }}"
               href="{{route('guests.forums')}}">
                <div class="flex items-center">
                    <svg
                        class="shrink-0 fill-current {{ Route::is('subscriber.forums') ? 'text-white' : 'text-gray-400 dark:text-gray-500' }}"
                        xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16">
                        <path
                            d="M2.678 11.894a1 1 0 0 1 .287.801 10.97 10.97 0 0 1-.398 2c1.395-.323 2.247-.697 2.634-.893a1 1 0 0 1 .71-.074A8.06 8.06 0 0 0 8 14c3.996 0 7-2.807 7-6 0-3.192-3.004-6-7-6S1 4.808 1 8c0 1.468.617 2.83 1.678 3.894zm-.493 3.905a21.682 21.682 0 0 1-.713.129c-.2.032-.352-.176-.273-.362a9.68 9.68 0 0 0 .244-.637l.003-.01c.248-.72.45-1.548.524-2.319C.743 11.37 0 9.76 0 8c0-3.866 3.582-7 8-7s8 3.134 8 7-3.582 7-8 7a9.06 9.06 0 0 1-2.347-.306c-.52.263-1.639.742-3.468 1.105z"/>
                        <path
                            d="M5 8a1 1 0 1 1-2 0 1 1 0 0 1 2 0zm4 0a1 1 0 1 1-2 0 1 1 0 0 1 2 0zm3 1a1 1 0 1 0 0-2 1 1 0 0 0 0 2z"/>
                    </svg>
                    <span class="text-sm ml-2 sidebar-text duration-200">Forums</span>
                </div>
            </a>
        </li>
        <!-- Profile -->
        <li class="mb-0.5 last:mb-0">
            <a class="block px-3 py-2 rounded-lg transition {{ Route::is('subscriber.profile') ? 'bg-violet-500 text-white font-bold' : 'text-gray-800 dark:text-gray-100 hover:bg-gray-200 dark:hover:bg-gray-700' }}"
               href="{{ route('profile.show') }}">
                <div class="flex items-center">
                    <svg
                        class="shrink-0 fill-current {{ Route::is('subscriber.profile') ? 'text-white' : 'text-gray-400 dark:text-gray-500' }}"
                        xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16">
                        <path
                            d="M8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm2-3a2 2 0 1 1-4 0 2 2 0 0 1 4 0zm4 8c0 1-1 1-1 1H3s-1 0-1-1 1-4 6-4 6 3 6 4zm-1-.004c-.001-.246-.154-.986-.832-1.664C11.516 10.68 10.289 10 8 10c-2.29 0-3.516.68-4.168 1.332-.678.678-.83 1.418-.832 1.664h10z"/>
                    </svg>
                    <span class="text-sm ml-2 sidebar-text duration-200">Profile</span>
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
