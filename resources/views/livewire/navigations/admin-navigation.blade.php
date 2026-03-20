@php use App\Enums\UserRole; @endphp
<div>
    <h3 class="text-xs hidden uppercase text-gray-400 dark:text-gray-500 font-semibold pl-3">
        <span class="hidden lg:block lg:sidebar-expanded:hidden 2xl:hidden text-center w-6"
              aria-hidden="true">•••</span>
        <span class="lg:hidden lg:sidebar-expanded:block 2xl:block">Admin Controls</span>
    </h3>
    <ul class="mt-3" x-data="{ sidebarExpanded: $store.sidebar.expanded }" x-init="{}">
        <!-- Dashboard Overview -->
        <li class="mb-0.5 last:mb-0" title="Dashboard Overview">
            <a :class="sidebarExpanded ? 'py-2' : ''"
               class="block pl-3 rounded-lg transition {{ Route::is('dashboard') ? 'bg-violet-500 text-white font-bold' : 'text-gray-800 dark:text-gray-100 hover:bg-gray-200 dark:hover:bg-gray-700' }}"
               href="{{route('dashboard')}}">
                <div class="flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" role="img"
                         aria-labelledby="title-dashboard" width="16" height="16" fill="none" stroke="currentColor"
                         stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"
                         class="shrink-0 fill-current {{ Route::is('dashboard') ? 'text-white' : 'text-gray-400 dark:text-gray-500' }}">
                        <title id="title-dashboard">Dashboard</title>
                        <rect x="3" y="3" width="7" height="7"></rect>
                        <rect x="14" y="3" width="7" height="7"></rect>
                        <rect x="14" y="14" width="7" height="7"></rect>
                        <rect x="3" y="14" width="7" height="7"></rect>
                    </svg>

                    <span class="text-sm ml-2 sidebar-text duration-200">Dashboard Overview</span>
                </div>
            </a>
        </li>

        @if(auth()->user()->role === UserRole::OWNER)
            <li class="mb-0.5 last:mb-0" title="School Switcher">
                <a :class="sidebarExpanded ? 'py-2' : ''"
                   class="block pl-3 rounded-lg transition {{ Route::is('admin.school-switcher') ? 'bg-violet-500 text-white font-bold' : 'text-gray-800 dark:text-gray-100 hover:bg-gray-200 dark:hover:bg-gray-700' }}"
                   href="{{ route('admin.school-switcher') }}">
                    <div class="flex items-center">
                        <svg
                            class="shrink-0 fill-current {{ Route::is('admin.school-switcher') ? 'text-white' : 'text-gray-400 dark:text-gray-500' }}"
                            xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16">
                            <path d="M8 0a8 8 0 1 0 0 16A8 8 0 0 0 8 0zM7 14.5a6.5 6.5 0 1 1 0-13 6.5 6.5 0 0 1 0 13z"/>
                            <path d="M8 2a6 6 0 1 0 0 12A6 6 0 0 0 8 2zm0 11a5 5 0 1 1 0-10 5 5 0 0 1 0 10z"/>
                            <path d="M8 4a4 4 0 1 0 0 8 4 4 0 0 0 0-8zm0 7a3 3 0 1 1 0-6 3 3 0 0 1 0 6z"/>
                            <path d="M8 6a2 2 0 1 0 0 4 2 2 0 0 0 0-4z"/>
                            <path d="M3.5 7.5a.5.5 0 0 0 0 1h9a.5.5 0 0 0 0-1h-9z"/>
                        </svg>
                        <span class="text-sm ml-2 sidebar-text duration-200">School Switcher</span>
                    </div>
                </a>
            </li>
        @endif


        @if(auth()->user()->role === UserRole::OWNER)
        <li class="mb-0.5 last:mb-0" title="Academic Management">
            <a :class="sidebarExpanded ? 'py-2' : ''"
               class="block pl-3 rounded-lg transition {{ Route::is('academic-groups.index')? 'bg-violet-500 text-white my-1 font-bold' : 'text-gray-800 dark:text-gray-100 hover:bg-gray-200 dark:hover:bg-gray-700' }}"
               href="{{route('academic-groups.index')}}">
                <div class="flex items-center">
                    <svg
                        class="shrink-0 fill-current {{ Route::is('academic-groups.index') ? 'text-white' : 'text-gray-400 dark:text-gray-500' }}"
                        xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16">
                        <path
                            d="M1 2.828c.885-.37 2.154-.769 3.388-.893 1.33-.134 2.458.063 3.112.752v9.746c-.935-.53-2.12-.603-3.213-.493-1.18.12-2.37.461-3.287.811V2.828zm7.5-.141c.654-.689 1.782-.886 3.112-.752 1.234.124 2.503.523 3.388.893v9.923c-.918-.35-2.107-.692-3.287-.81-1.094-.111-2.278-.039-3.213.492V2.687zM8 1.783C7.015.936 5.587.81 4.287.94c-1.514.153-3.042.672-3.994 1.105A.5.5 0 0 0 0 2.5v11a.5.5 0 0 0 .707.455c.882-.4 2.303-.881 3.68-1.02 1.409-.142 2.59.087 3.223.877a.5.5 0 0 0 .78 0c.633-.79 1.814-1.019 3.222-.877 1.378.139 2.8.62 3.681 1.02A.5.5 0 0 0 16 13.5v-11a.5.5 0 0 0-.293-.455c-.952-.433-2.48-.952-3.994-1.105C10.413.809 8.985.936 8 1.783z"/>
                    </svg>
                    <span class="text-sm ml-2 sidebar-text duration-200">Academic Management</span>
                </div>
            </a>
        </li>
        @endif

        <!-- User Management -->
        <li class="mb-0.5 last:mb-0 " title="User Management">
            <a :class="sidebarExpanded ? 'py-2' : ''"
               class="block pl-3 rounded-lg transition {{ Route::is('users.index') ? 'bg-violet-500 text-white font-bold' : 'text-gray-800 dark:text-gray-100 hover:bg-gray-200 dark:hover:bg-gray-700' }}"
               href="{{route('users.index')}}">
                <div class="flex items-center">
                    <svg
                        class="shrink-0 fill-current {{ Route::is('users.index') ? 'text-white' : 'text-gray-400 dark:text-gray-500' }}"
                        xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16">
                        <path
                            d="M15 14s1 0 1-1-1-4-5-4-5 3-5 4 1 1 1 1h8Zm-7.978-1A.261.261 0 0 1 7 12.996c.001-.264.167-1.03.76-1.72C8.312 10.629 9.282 10 11 10c1.717 0 2.687.63 3.24 1.276.593.69.758 1.457.76 1.72l-.008.002A.274.274 0 0 1 15 13H7.022ZM11 7a2 2 0 1 0 0-4 2 2 0 0 0 0 4Zm3-2a3 3 0 1 1-6 0 3 3 0 0 1 6 0ZM6.936 9.28a5.88 5.88 0 0 0-1.23-.247A7.35 7.35 0 0 0 5 9c-4 0-5 3-5 4 0 .667.333 1 1 1h4.216A2.238 2.238 0 0 1 5 13c0-1.01.377-2.042 1.09-2.904.243-.294.526-.569.846-.816ZM4.92 10A5.493 5.493 0 0 0 4 13H1c0-.26.164-1.03.76-1.724.545-.636 1.492-1.256 3.16-1.275ZM1.5 5.5a3 3 0 1 1 6 0 3 3 0 0 1-6 0Zm3-2a2 2 0 1 0 0 4 2 2 0 0 0 0-4Z"/>
                    </svg>
                    <span class="text-sm ml-2 sidebar-text duration-200">User Management</span>
                </div>
            </a>
        </li>
        <!-- User Impersonation -->
        <li class="mb-0.5 last:mb-0 hidden" title="User Impersonation">
            <a :class="sidebarExpanded ? 'py-2' : ''"
               class="block pl-3 rounded-lg transition {{ Route::is('admin.users.impersonate') ? 'bg-violet-500 text-white font-bold' : 'text-gray-800 dark:text-gray-100 hover:bg-gray-200 dark:hover:bg-gray-700' }}"
               href="{{route('admin.users.impersonate')}}">
                <div class="flex items-center">
                    <svg
                        class="shrink-0 fill-current {{ Route::is('admin.users.impersonate') ? 'text-white' : 'text-gray-400 dark:text-gray-500' }}"
                        xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16">
                        <path d="M1 14s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1H1zm5-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6z"/>
                        <path fill-rule="evenodd"
                              d="M13.5 5a.5.5 0 0 1 .5.5V7h1.5a.5.5 0 0 1 0 1H14v1.5a.5.5 0 0 1-1 0V8h-1.5a.5.5 0 0 1 0-1H13V5.5a.5.5 0 0 1 .5-.5z"/>
                    </svg>
                    <span class="text-sm ml-2 sidebar-text duration-200">User Impersonation</span>
                </div>
            </a>
        </li>

        <!-- Student Management -->
        <li class="mb-0.5 last:mb-0 " title="Student Management">
            <a :class="sidebarExpanded ? 'py-2' : ''"
               class="block pl-3 rounded-lg transition {{ Route::is('admin.student-management') ? 'bg-violet-500 text-white font-bold' : 'text-gray-800 dark:text-gray-100 hover:bg-gray-200 dark:hover:bg-gray-700' }}"
               href="{{route('admin.student-management')}}">
                <div class="flex items-center">
                    <svg
                        class="shrink-0 fill-current {{ Route::is('admin.student-management') ? 'text-white' : 'text-gray-400 dark:text-gray-500' }}"
                        xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16">
                        <path
                            d="M8.211 2.047a.5.5 0 0 0-.422 0l-7.5 3.5a.5.5 0 0 0 .025.917l7.5 3a.5.5 0 0 0 .372 0L14 7.14V13a1 1 0 0 0-1 1v2h3v-2a1 1 0 0 0-1-1V6.739l.686-.275a.5.5 0 0 0 .025-.917l-7.5-3.5Z"/>
                        <path
                            d="M4.176 9.032a.5.5 0 0 0-.656.327l-.5 1.7a.5.5 0 0 0 .294.605l4.5 1.8a.5.5 0 0 0 .372 0l4.5-1.8a.5.5 0 0 0 .294-.605l-.5-1.7a.5.5 0 0 0-.656-.327L8 10.466 4.176 9.032Z"/>
                    </svg>
                    <span class="text-sm ml-2 sidebar-text duration-200">Student Management</span>
                </div>
            </a>
        </li>

        <!-- Teacher Management -->
        <li class="mb-0.5 last:mb-0 " title="Teacher Management">
            <a :class="sidebarExpanded ? 'py-2' : ''"
               class="block pl-3 rounded-lg transition {{ Route::is('admin.teacher-management') ? 'bg-violet-500 text-white font-bold' : 'text-gray-800 dark:text-gray-100 hover:bg-gray-200 dark:hover:bg-gray-700' }}"
               href="{{route('admin.teacher-management')}}">
                <div class="flex items-center">
                    <svg
                        class="shrink-0 fill-current {{ Route::is('admin.teacher-management') ? 'text-white' : 'text-gray-400 dark:text-gray-500' }}"
                        xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16">
                        <path
                            d="M8.211 2.047a.5.5 0 0 0-.422 0l-7.5 3.5a.5.5 0 0 0 .025.917l7.5 3a.5.5 0 0 0 .372 0L14 7.14V13a1 1 0 0 0-1 1v2h3v-2a1 1 0 0 0-1-1V6.739l.686-.275a.5.5 0 0 0 .025-.917l-7.5-3.5Z"/>
                        <path
                            d="M4.176 9.032a.5.5 0 0 0-.656.327l-.5 1.7a.5.5 0 0 0 .294.605l4.5 1.8a.5.5 0 0 0 .372 0l4.5-1.8a.5.5 0 0 0 .294-.605l-.5-1.7a.5.5 0 0 0-.656-.327L8 10.466 4.176 9.032Z"/>
                    </svg>
                    <span class="text-sm ml-2 sidebar-text duration-200">Teacher Management</span>
                </div>
            </a>
        </li>
        <!-- Student Groups -->
        <li class="mb-0.5 last:mb-0 " title="Student Groups">
            <a :class="sidebarExpanded ? 'py-2' : ''"
               class="block pl-3 rounded-lg transition {{ Route::is('admin.student-groups') ? 'bg-violet-500 text-white font-bold' : 'text-gray-800 dark:text-gray-100 hover:bg-gray-200 dark:hover:bg-gray-700' }}"
               href="{{route('admin.student-groups')}}">
                <div class="flex items-center">
                    <svg
                        class="shrink-0 fill-current {{ Route::is('admin.student-groups') ? 'text-white' : 'text-gray-400 dark:text-gray-500' }}"
                        xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16">
                        <path
                            d="M7 14s-1 0-1-1 1-4 5-4 5 3 5 4-1 1-1 1H7Zm4-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6Zm-5.784 6A2.238 2.238 0 0 1 5 13c0-1.355.68-2.75 1.936-3.72A6.325 6.325 0 0 0 5 9c-4 0-5 3-5 4s1 1 1 1h4.216ZM4.5 8a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5Z"/>
                    </svg>
                    <span class="text-sm ml-2 sidebar-text duration-200">Student Groups</span>
                </div>
            </a>
        </li>


        <!-- Librarian Management -->
        <li class="mb-0.5 last:mb-0 " title="Librarian Management">
            <a :class="sidebarExpanded ? 'py-2' : ''"
               class="block pl-3 rounded-lg transition {{ Route::is('admin.librarian-management') ? 'bg-violet-500 text-white font-bold' : 'text-gray-800 dark:text-gray-100 hover:bg-gray-200 dark:hover:bg-gray-700' }}"
               href="{{route('admin.librarian-management')}}">
                <div class="flex items-center">
                    <svg
                        class="shrink-0 fill-current {{ Route::is('admin.librarian-management') ? 'text-white' : 'text-gray-400 dark:text-gray-500' }}"
                        xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16">
                        <path
                            d="M4 0h5.293A1 1 0 0 1 10 .293L13.707 4a1 1 0 0 1 .293.707V14a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V2a2 2 0 0 1 2-2zm5.5 1.5v2a1 1 0 0 0 1 1h2l-3-3zM4.5 8a.5.5 0 0 0 0 1h7a.5.5 0 0 0 0-1h-7zM4 10.5a.5.5 0 0 1 .5-.5h7a.5.5 0 0 1 0 1h-7a.5.5 0 0 1-.5-.5zm.5 2.5a.5.5 0 0 0 0 1h4a.5.5 0 0 0 0-1h-4z"/>
                    </svg>
                    <span class="text-sm ml-2 sidebar-text duration-200">Librarian Management</span>
                </div>
            </a>
        </li>

        <!-- Accountant Management -->
        <li class="mb-0.5 last:mb-0 " title="Accountant Management">
            <a :class="sidebarExpanded ? 'py-2' : ''"
               class="block pl-3 rounded-lg transition {{ Route::is('admin.accountant-management') ? 'bg-violet-500 text-white font-bold' : 'text-gray-800 dark:text-gray-100 hover:bg-gray-200 dark:hover:bg-gray-700' }}"
               href="{{route('admin.accountant-management')}}">
                <div class="flex items-center">
                    <svg
                        class="shrink-0 fill-current {{ Route::is('admin.accountant-management') ? 'text-white' : 'text-gray-400 dark:text-gray-500' }}"
                        xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16">
                        <path d="M1 3a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1H1zm7 8a2 2 0 1 0 0-4 2 2 0 0 0 0 4z"/>
                        <path d="M0 5a1 1 0 0 1 1-1h14a1 1 0 0 1 1 1v8a1 1 0 0 1-1 1H1a1 1 0 0 1-1-1V5zm3 0a2 2 0 0 1-2 2v4a2 2 0 0 1 2 2h10a2 2 0 0 1 2-2V7a2 2 0 0 1-2-2H3z"/>
                    </svg>
                    <span class="text-sm ml-2 sidebar-text duration-200">Accountant Management</span>
                </div>
            </a>
        </li>
        @if(auth()->user()->role === UserRole::OWNER)
            <!-- Author Management -->
            <li class="mb-0.5 last:mb-0 " title="Author Management">
                <a :class="sidebarExpanded ? 'py-2' : ''"
                   class="block pl-3 rounded-lg transition {{ Route::is('admin.author-management') ? 'bg-violet-500 text-white font-bold' : 'text-gray-800 dark:text-gray-100 hover:bg-gray-200 dark:hover:bg-gray-700' }}"
                   href="{{route('admin.author-management')}}">
                    <div class="flex items-center">
                        <svg
                            class="shrink-0 fill-current {{ Route::is('admin.author-management') ? 'text-white' : 'text-gray-400 dark:text-gray-500' }}"
                            xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16">
                            <path
                                d="m13.498.795.149-.149a1.207 1.207 0 1 1 1.707 1.708l-.149.148a1.5 1.5 0 0 1-.059 2.059L4.854 14.854a.5.5 0 0 1-.233.131l-4 1a.5.5 0 0 1-.606-.606l1-4a.5.5 0 0 1 .131-.232l9.642-9.642a.5.5 0 0 0-.642.056L6.854 4.854a.5.5 0 1 1-.708-.708L9.44.854A1.5 1.5 0 0 1 11.5.796a1.5 1.5 0 0 1 1.998-.001zm-.644.766a.5.5 0 0 0-.707 0L1.95 11.756l-.764 3.057 3.057-.764L14.44 3.854a.5.5 0 0 0 0-.708l-1.585-1.585z"/>
                        </svg>
                        <span class="text-sm ml-2 sidebar-text duration-200">Author Management</span>
                    </div>
                </a>
            </li>
        @endif

        <!-- Parent Management -->
        <li class="mb-0.5 last:mb-0" title="Parent Management">
            <a :class="sidebarExpanded ? 'py-2' : ''"
               class="block pl-3 rounded-lg transition {{ Route::is('admin.parent-management') ? 'bg-violet-500 text-white font-bold' : 'text-gray-800 dark:text-gray-100 hover:bg-gray-200 dark:hover:bg-gray-700' }}"
               href="{{route('admin.parent-management')}}">
                <div class="flex items-center">
                    <svg
                        class="shrink-0 fill-current {{ Route::is('admin.parent-management') ? 'text-white' : 'text-gray-400 dark:text-gray-500' }}"
                        xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16">
                        <path
                            d="M7 14s-1 0-1-1 1-4 5-4 5 3 5 4-1 1-1 1H7Zm4-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6Zm-5.784 6A2.238 2.238 0 0 1 5 13c0-1.355.68-2.75 1.936-3.72A6.325 6.325 0 0 0 5 9c-4 0-5 3-5 4s1 1 1 1h4.216ZM4.5 8a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5Z"/>
                    </svg>
                    <span class="text-sm ml-2 sidebar-text duration-200">Parent Management</span>
                </div>
            </a>
        </li>

        <!-- Book Management -->
        @if(auth()->user()->role === UserRole::OWNER)
            <li class="mb-0.5 last:mb-0 " title="Book Management">
                <a :class="sidebarExpanded ? 'py-2' : ''"
                   class="block pl-3 rounded-lg transition {{ Route::is('admin.book-management') ? 'bg-violet-500 text-white font-bold' : 'text-gray-800 dark:text-gray-100 hover:bg-gray-200 dark:hover:bg-gray-700' }}"
                   href="{{route('admin.book-management')}}">
                    <div class="flex items-center">
                        <svg
                            class="shrink-0 fill-current {{ Route::is('admin.book-management') ? 'text-white' : 'text-gray-400 dark:text-gray-500' }}"
                            xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16">
                            <path
                                d="M1 2.828c.885-.37 2.154-.769 3.388-.893 1.33-.134 2.458.063 3.112.752v9.746c-.935-.53-2.12-.603-3.213-.493-1.18.12-2.37.461-3.287.811V2.828zm7.5-.141c.654-.689 1.782-.886 3.112-.752 1.234.124 2.503.523 3.388.893v9.923c-.918-.35-2.107-.692-3.287-.81-1.094-.111-2.278-.039-3.213.492V2.687zM8 1.783C7.015.936 5.587.81 4.287.94c-1.514.153-3.042.672-3.994 1.105A.5.5 0 0 0 0 2.5v11a.5.5 0 0 0 .707.455c.882-.4 2.303-.881 3.68-1.02 1.409-.142 2.59.087 3.223.877a.5.5 0 0 0 .78 0c.633-.79 1.814-1.019 3.222-.877 1.378.139 2.8.62 3.681 1.02A.5.5 0 0 0 16 13.5v-11a.5.5 0 0 0-.293-.455c-.952-.433-2.48-.952-3.994-1.105C10.413.809 8.985.936 8 1.783z"/>
                        </svg>
                        <span class="text-sm ml-2 sidebar-text duration-200">Book Management</span>
                    </div>
                </a>
            </li>
        @endif

        <!-- Book Approvals -->
        @if(auth()->user()->role === UserRole::OWNER)
            <li class="mb-0.5 last:mb-0 " title="Book Approvals">
                <a :class="sidebarExpanded ? 'py-2' : ''"
                   class="block pl-3 rounded-lg transition {{ Route::is('admin.book-approvals') ? 'bg-violet-500 text-white font-bold' : 'text-gray-800 dark:text-gray-100 hover:bg-gray-200 dark:hover:bg-gray-700' }}"
                   href="{{route('admin.book-approvals')}}">
                    <div class="flex items-center">
                        <svg
                            class="shrink-0 fill-current {{ Route::is('admin.book-approvals') ? 'text-white' : 'text-gray-400 dark:text-gray-500' }}"
                            xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16">
                            <path
                                d="M10.97 4.97a.235.235 0 0 0-.02.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-1.071-1.05z"/>
                            <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
                        </svg>
                        <span class="text-sm ml-2 sidebar-text duration-200">Book Approvals</span>
                    </div>
                </a>
            </li>
        @endif


        <!-- Subject Management -->
         @if(auth()->user()->role === UserRole::OWNER)
        <li class="mb-0.5 last:mb-0 " title="Subject Management">
            <a :class="sidebarExpanded ? 'py-2' : ''"
               class="block pl-3 rounded-lg transition {{ Route::is('admin.subject-management') ? 'bg-violet-500 text-white font-bold' : 'text-gray-800 dark:text-gray-100 hover:bg-gray-200 dark:hover:bg-gray-700' }}"
               href="{{route('admin.subject-management')}}">
                <div class="flex items-center">
                    <svg
                        class="shrink-0 fill-current {{ Route::is('admin.subject-management') ? 'text-white' : 'text-gray-400 dark:text-gray-500' }}"
                        xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16">
                        <path
                            d="M5 0h8a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2 2 2 0 0 1-2 2H3a2 2 0 0 1-2-2h1a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1V4a1 1 0 0 0-1-1H3a1 1 0 0 0-1 1H1a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v9a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1H5a1 1 0 0 0-1 1H3a2 2 0 0 1 2-2z"/>
                        <path
                            d="M1 6v-.5a.5.5 0 0 1 1 0V6h.5a.5.5 0 0 1 0 1h-2a.5.5 0 0 1 0-1H1zm0 3v-.5a.5.5 0 0 1 1 0V9h.5a.5.5 0 0 1 0 1h-2a.5.5 0 0 1 0-1H1zm0 2.5v.5H.5a.5.5 0 0 0 0 1h2a.5.5 0 0 0 0-1H2v-.5a.5.5 0 0 0-1 0z"/>
                    </svg>
                    <span class="text-sm ml-2 sidebar-text duration-200">Subject Management</span>
                </div>
            </a>
        </li>
        @endif

        <!-- Report Card Management -->
        <li class="mb-0.5 last:mb-0" title="Report Card Management">
            <a :class="sidebarExpanded ? 'py-2' : ''"
               class="block pl-3 rounded-lg transition {{ Route::is('admin.report-cards') ? 'bg-violet-500 text-white font-bold' : 'text-gray-800 dark:text-gray-100 hover:bg-gray-200 dark:hover:bg-gray-700' }}"
               href="{{route('admin.report-cards')}}">
                <div class="flex items-center">
                    <svg
                        class="shrink-0 fill-current {{ Route::is('admin.report-cards') ? 'text-white' : 'text-gray-400 dark:text-gray-500' }}"
                        xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16">
                        <path d="M14 14V4.5L9.5 0H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2zM9.5 3A1.5 1.5 0 0 0 11 4.5h2V14a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1h5.5v2z"/>
                        <path d="M4.603 14.087a.81.81 0 0 1-.438-.42c-.195-.388-.13-.776.08-1.102.198-.307.526-.568.897-.787a7.68 7.68 0 0 1 1.482-.645 19.697 19.697 0 0 0 1.062-2.227 7.269 7.269 0 0 1-.43-1.295c-.086-.4-.119-.796-.046-1.136.075-.354.274-.672.65-.823.192-.077.4-.12.602-.077a.7.7 0 0 1 .477.365c.088.164.12.356.127.538.007.188-.012.396-.047.614-.084.51-.27 1.134-.52 1.794a10.954 10.954 0 0 0 .98 1.686 5.753 5.753 0 0 1 1.334.05c.364.066.734.195.96.465.12.144.193.32.2.518.007.192-.047.382-.138.563a1.04 1.04 0 0 1-.354.416.856.856 0 0 1-.51.138c-.331-.014-.654-.196-.933-.417a5.712 5.712 0 0 1-.911-.95 11.651 11.651 0 0 0-1.997.406 11.307 11.307 0 0 1-1.02 1.51c-.292.35-.609.656-.927.787a.793.793 0 0 1-.58.029zm1.379-1.901c-.166.076-.32.156-.459.238-.328.194-.541.383-.647.547-.094.145-.096.25-.04.361.01.022.02.036.026.044a.266.266 0 0 0 .035-.012c.137-.056.355-.235.635-.572a8.18 8.18 0 0 0 .45-.606zm1.64-1.33a12.71 12.71 0 0 1 1.01-.193 11.744 11.744 0 0 1-.51-.858 20.801 20.801 0 0 1-.5 1.05zm2.446.45c.15.163.296.3.435.41.24.19.407.253.498.256a.107.107 0 0 0 .07-.015.307.307 0 0 0 .094-.125.436.436 0 0 0 .059-.2.095.095 0 0 0-.026-.063c-.052-.062-.2-.152-.518-.209a3.876 3.876 0 0 0-.612-.053zM8.078 7.8a6.7 6.7 0 0 0 .2-.828c.031-.188.043-.343.038-.465a.613.613 0 0 0-.032-.198.517.517 0 0 0-.145.04c-.087.035-.158.106-.196.283-.04.192-.03.469.046.822.024.111.054.227.09.346z"/>
                    </svg>
                    <span class="text-sm ml-2 sidebar-text duration-200">Report Cards</span>
                </div>
            </a>
        </li>

        <!-- Payments -->
        <li class="mb-0.5 last:mb-0" title="School Payments">
            <a :class="sidebarExpanded ? 'py-2' : ''"
               class="block pl-3 rounded-lg transition {{ Route::is('admin.payments*') || Route::is('parent.fees*') ? 'bg-violet-500 text-white font-bold' : 'text-gray-800 dark:text-gray-100 hover:bg-gray-200 dark:hover:bg-gray-700' }}"
               href="{{route('admin.payments.index')}}">
                <div class="flex items-center">
                    <svg
                        class="shrink-0 fill-current {{ Route::is('admin.payments*') || Route::is('parent.fees*') ? 'text-white' : 'text-gray-400 dark:text-gray-500' }}"
                        width="16" height="16" viewBox="0 0 24 24">
                        <path
                            d="M20 4H4c-1.11 0-1.99.89-1.99 2L2 18c0 1.11.89 2 2 2h16c1.11 0 2-.89 2-2V6c0-1.11-.89-2-2-2zm0 14H4v-6h16v6zm0-10H4V6h16v2z"/>
                    </svg>
                    <span class="text-sm ml-2 sidebar-text duration-200">Payments</span>
                </div>
            </a>
        </li>

        <p class="px-3 mt-4 mb-1 text-[10px] uppercase tracking-wide text-gray-400 dark:text-gray-500">General Exams</p>
       <li class="mb-0.5 last:mb-0" title="General Exams">
           <a :class="sidebarExpanded ? 'py-2' : ''"
              class="block pl-3 rounded-lg transition {{ Route::is('teachers.general-exams.index') ? 'bg-violet-500 text-white font-bold' : 'text-gray-800 dark:text-gray-100 hover:bg-gray-200 dark:hover:bg-gray-700' }}"
              href="{{ route('teachers.general-exams.index') }}">
               <div class="flex items-center">
                   <svg class="shrink-0 fill-current {{ Route::is('teachers.general-exams.index') ? 'text-white' : 'text-gray-400 dark:text-gray-500' }}" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24">
                       <path d="M19 3h-4.18C14.4 1.84 13.3 1 12 1c-1.3 0-2.4.84-2.82 2H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V5a2 2 0 0 0-2-2zm-7 0c.55 0 1 .45 1 1s-.45 1-1 1-1-.45-1-1 .45-1 1-1zm-5 16h10v-2H7v2zm0-4h10v-2H7v2zm0-4h10V7H7v4z"/>
                   </svg>
                   <span class="text-sm ml-2 sidebar-text duration-200">Manage General Exams</span>
               </div>
           </a>
       </li>
       <li class="mb-0.5 last:mb-0" title="Create General Exam">
           <a :class="sidebarExpanded ? 'py-2' : ''"
              class="block pl-3 rounded-lg transition {{ Route::is('teachers.general-exams.create') ? 'bg-violet-500 text-white font-bold' : 'text-gray-800 dark:text-gray-100 hover:bg-gray-200 dark:hover:bg-gray-700' }}"
              href="{{ route('teachers.general-exams.create') }}">
               <div class="flex items-center">
                   <svg class="shrink-0 fill-current {{ Route::is('teachers.general-exams.create') ? 'text-white' : 'text-gray-400 dark:text-gray-500' }}" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24">
                       <path d="M13 11h6v2h-6v6h-2v-6H5v-2h6V5h2v6zm8-6V5a2 2 0 0 0-2-2h-4.18C14.4 1.84 13.3 1 12 1c-1.3 0-2.4.84-2.82 2H5a2 2 0 0 0-2 2v1h16z"/>
                   </svg>
                   <span class="text-sm ml-2 sidebar-text duration-200">Create General Exam</span>
               </div>
           </a>
       </li>


        <li class="mb-0.5 last:mb-0" title="Messages">
            <a :class="sidebarExpanded ? 'py-2' : ''"
               class="block pl-3 rounded-lg transition {{ Route::is('admin.messages*') ? 'bg-violet-500 text-white font-bold' : 'text-gray-800 dark:text-gray-100 hover:bg-gray-200 dark:hover:bg-gray-700' }}"
               href="{{route('admin.messages.index')}}">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <svg
                            class="shrink-0 fill-current {{ Route::is('admin.messages*') ? 'text-white' : 'text-gray-400 dark:text-gray-500' }}"
                            xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16">
                            <path
                                d="M0 4a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V4zm2-1a1 1 0 0 0-1 1v.217l7 4.2 7-4.2V4a1 1 0 0 0-1-1H2zm13 2.383-4.758 2.855L15 11.114v-5.73zm-.034 6.878L9.271 8.82 8 9.583 6.728 8.82l-5.694 3.44A1 1 0 0 0 2 13h12a1 1 0 0 0 .966-.739zM1 11.114l4.758-2.876L1 5.383v5.73z"/>
                        </svg>
                        <span class="text-sm ml-2 sidebar-text duration-200">Message Center</span>
                    </div>
                    <svg class="shrink-0 fill-current text-gray-400 dark:text-gray-500"
                         xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16">
                        <path
                            d="M4.646 1.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1 0 .708l-6 6a.5.5 0 0 1-.708-.708L10.293 8 4.646 2.354a.5.5 0 0 1 0-.708z"/>
                    </svg>
                </div>
            </a>
        </li>

        <!-- Reports -->
        <li class="mb-0.5 last:mb-0 hidden" title="Reports">
            <a :class="sidebarExpanded ? 'py-2' : ''"
               class="block pl-3 rounded-lg transition {{ $activeTab === 'reports' ? 'bg-violet-500 text-white font-bold' : 'text-gray-800 dark:text-gray-100 hover:bg-gray-200 dark:hover:bg-gray-700' }}"
               href="#"
               wire:click.prevent="setActiveTab('reports')">
                <div class="flex items-center">
                    <svg
                        class="shrink-0 fill-current {{ $activeTab === 'reports' ? 'text-white' : 'text-gray-400 dark:text-gray-500' }}"
                        xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16">
                        <path
                            d="M0 0h1v15h15v1H0V0Zm14.817 3.113a.5.5 0 0 1 .07.704l-4.5 5.5a.5.5 0 0 1-.74.037L7.06 6.767l-3.656 5.027a.5.5 0 0 1-.808-.588l4-5.5a.5.5 0 0 1 .758-.06l2.609 2.61 4.15-5.073a.5.5 0 0 1 .704-.07Z"/>
                    </svg>
                    <span class="text-sm ml-2 sidebar-text duration-200">Reports</span>
                </div>
            </a>
        </li>

        <!-- User Logins -->
        <li class="mb-0.5 last:mb-0" title="User Login Activity">
            <a :class="sidebarExpanded ? 'py-2' : ''"
               class="block pl-3 rounded-lg transition {{ Route::is('admin.logins') ? 'bg-violet-500 text-white font-bold' : 'text-gray-800 dark:text-gray-100 hover:bg-gray-200 dark:hover:bg-gray-700' }}"
               href="{{route('admin.logins')}}">
                <div class="flex items-center">
                    <svg
                        class="shrink-0 fill-current {{ Route::is('admin.logins') ? 'text-white' : 'text-gray-400 dark:text-gray-500' }}"
                        xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16">
                        <path d="M11 6a3 3 0 1 1-6 0 3 3 0 0 1 6 0z"/>
                        <path fill-rule="evenodd"
                              d="M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8zm8-7a7 7 0 0 0-5.468 11.37C3.242 11.226 4.805 10 8 10s4.757 1.225 5.468 2.37A7 7 0 0 0 8 1z"/>
                    </svg>
                    <span class="text-sm ml-2 sidebar-text duration-200">User Login Activity</span>
                </div>
            </a>
        </li>

        <!-- User Actions -->
        <li class="mb-0.5 last:mb-0" title="User Actions">
            <a :class="sidebarExpanded ? 'py-2' : ''"
               class="block pl-3 rounded-lg transition {{ Route::is('admin.activity-trail*') ? 'bg-violet-500 text-white font-bold' : 'text-gray-800 dark:text-gray-100 hover:bg-gray-200 dark:hover:bg-gray-700' }}"
               href="{{route('admin.activity-trail.index')}}">
                <div class="flex items-center">
                    <svg
                        class="shrink-0 fill-current {{ Route::is('admin.activity-trail*') ? 'text-white' : 'text-gray-400 dark:text-gray-500' }}"
                        xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16">
                        <path
                            d="M3 0h10a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V2a2 2 0 0 1 2-2zm0 1a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h10a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1H3zm2 5a1 1 0 1 1-2 0 1 1 0 0 1 2 0zm0 4a1 1 0 1 1-2 0 1 1 0 0 1 2 0zm0 4a1 1 0 1 1-2 0 1 1 0 0 1 2 0zm4-8h4a.5.5 0 0 1 0 1H9a.5.5 0 0 1 0-1zm0 4h4a.5.5 0 0 1 0 1H9a.5.5 0 0 1 0-1zm0 4h4a.5.5 0 0 1 0 1H9a.5.5 0 0 1 0-1z"/>
                    </svg>
                    <span class="text-sm ml-2 sidebar-text duration-200">User Actions</span>
                </div>
            </a>
        </li>

        {{-- Moderator Activities --}}
        @if(auth()->user()->role === UserRole::OWNER)
            <li class="mb-0.5 last:mb-0" title="Moderator Activities">
                <a :class="sidebarExpanded ? 'py-2' : ''"
                   class="block pl-3 rounded-lg transition {{ Route::is('admin.academic-activities*') ? 'bg-violet-500 text-white font-bold' : 'text-gray-800 dark:text-gray-100 hover:bg-gray-200 dark:hover:bg-gray-700' }}"
                   href="{{route('admin.academic-activities')}}">
                    <div class="flex items-center">
                        <svg
                            class="shrink-0 fill-current {{ Route::is('admin.academic-activities*') ? 'text-white' : 'text-gray-400 dark:text-gray-500' }}"
                            xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16">
                            <path
                                d="M3 0h10a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V2a2 2 0 0 1 2-2zm0 1a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h10a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1H3zm2 5a1 1 0 1 1-2 0 1 1 0 0 1 2 0zm0 4a1 1 0 1 1-2 0 1 1 0 0 1 2 0zm0 4a1 1 0 1 1-2 0 1 1 0 0 1 2 0zm4-8h4a.5.5 0 0 1 0 1H9a.5.5 0 0 1 0-1zm0 4h4a.5.5 0 0 1 0 1H9a.5.5 0 0 1 0-1zm0 4h4a.5.5 0 0 1 0 1H9a.5.5 0 0 1 0-1z"/>
                        </svg>
                        <span class="text-sm ml-2 sidebar-text duration-200">Moderator Activities</span>
                    </div>
                </a>
            </li>
        @endif

        <li class="mb-0.5 last:mb-0" title="School Settings">
            <a :class="sidebarExpanded ? 'py-2' : ''"
               class="block pl-3 rounded-lg transition {{ Route::is('school-settings*') ? 'bg-violet-500 text-white font-bold' : 'text-gray-800 dark:text-gray-100 hover:bg-gray-200 dark:hover:bg-gray-700' }}"
               href="{{route('school-settings.index')}}">
                <div class="flex items-center">
                    <svg
                        class="shrink-0 fill-current {{ Route::is('school-settings*') ? 'text-white' : 'text-gray-400 dark:text-gray-500' }}"
                        xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16">
                        <path
                            d="M9.405 1.05c-.413-1.4-2.397-1.4-2.81 0l-.1.34a1.464 1.464 0 0 1-2.105.872l-.31-.17c-1.283-.698-2.686.705-1.987 1.987l.169.311c.446.82.023 1.841-.872 2.105l-.34.1c-1.4.413-1.4 2.397 0 2.81l.34.1a1.464 1.464 0 0 1 .872 2.105l-.17.31c-.698 1.283.705 2.686 1.987 1.987l.311-.169a1.464 1.464 0 0 1 2.105.872l.1.34c.413 1.4 2.397 1.4 2.81 0l.1-.34a1.464 1.464 0 0 1 2.105-.872l.31.17c1.283.698 2.686-.705 1.987-1.987l-.169-.311a1.464 1.464 0 0 1 .872-2.105l.34-.1c1.4-.413 1.4-2.397 0-2.81l-.34-.1a1.464 1.464 0 0 1-.872-2.105l.17-.31c.698-1.283-.705-2.686-1.987-1.987l-.311.169a1.464 1.464 0 0 1-2.105-.872l-.1-.34zM8 10.93a2.929 2.929 0 1 1 0-5.86 2.929 2.929 0 0 1 0 5.858z"/>
                    </svg>
                    <span class="text-sm ml-2 sidebar-text duration-200">Academic Settings</span>
                </div>
            </a>
        </li>

        @if(auth()->user()->role === UserRole::OWNER)

            <li class="mt-3 mb-2">
                <h4 class="text-[11px] uppercase text-gray-400 dark:text-gray-500 font-semibold pl-3">
                    Owner Tools
                </h4>
            </li>

            <li class="mb-0.5 last:mb-0" title="Messenger Allocations">
                <a :class="sidebarExpanded ? 'py-2' : ''"
                   class="block pl-3 rounded-lg transition {{ Route::is('token-allocations*') ? 'bg-violet-500 text-white font-bold' : 'text-gray-800 dark:text-gray-100 hover:bg-gray-200 dark:hover:bg-gray-700' }}"
                   href="{{route('token-allocations.index')}}">
                    <div class="flex items-center">
                        <svg
                            class="shrink-0 fill-current {{ Route::is('token-allocations*') ? 'text-white' : 'text-gray-400 dark:text-gray-500' }}"
                            xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16">
                            <path
                                d="M2 2a1 1 0 0 0-1 1v10a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V3a1 1 0 0 0-1-1H2zm11 2v2H3V4h10zM6 8h5v1H6V8zm-1 2h6v1H5v-1zm-1 2h7v1H4v-1z"/>
                            <path
                                d="M3 4h10v6H3V4zm0-1a1 1 0 0 0-1 1v6a1 1 0 0 0 1 1h10a1 1 0 0 0 1-1V4a1 1 0 0 0-1-1H3z"/>
                        </svg>
                        <span class="text-sm ml-2 sidebar-text duration-200">Messenger Allocations</span>
                    </div>
                </a>
            </li>

            <li class="mb-0.5 last:mb-0" title="Messenger Transactions">
                <a :class="sidebarExpanded ? 'py-2' : ''"
                   class="block pl-3 rounded-lg transition {{ Route::is('admin.messenger-transactions*') ? 'bg-violet-500 text-white font-bold' : 'text-gray-800 dark:text-gray-100 hover:bg-gray-200 dark:hover:bg-gray-700' }}"
                   href="{{route('admin.messenger-transactions.index')}}">
                    <div class="flex items-center">
                        <svg
                            class="shrink-0 fill-current {{ Route::is('admin.messenger-transactions*') ? 'text-white' : 'text-gray-400 dark:text-gray-500' }}"
                            xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16">
                            <path d="M1 3a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1H1zm7 8a2 2 0 1 0 0-4 2 2 0 0 0 0 4z"/>
                            <path
                                d="M0 5a1 1 0 0 1 1-1h14a1 1 0 0 1 1 1v8a1 1 0 0 1-1 1H1a1 1 0 0 1-1-1V5zm3 0a2 2 0 0 1-2 2v4a2 2 0 0 1 2 2h10a2 2 0 0 1 2-2V7a2 2 0 0 1-2-2H3z"/>
                        </svg>
                        <span class="text-sm ml-2 sidebar-text duration-200">Messengers Transactions</span>
                    </div>
                </a>
            </li>

            <li class="mb-0.5 last:mb-0" title="Pricing Settings">
                <a :class="sidebarExpanded ? 'py-2' : ''"
                   class="block pl-3 rounded-lg transition {{ Route::is('admin.pricing-settings.*') ? 'bg-violet-500 text-white font-bold' : 'text-gray-800 dark:text-gray-100 hover:bg-gray-200 dark:hover:bg-gray-700' }}"
                   href="{{ route('admin.pricing-settings.edit') }}">
                    <div class="flex items-center">
                        <svg
                            class="shrink-0 fill-current {{ Route::is('admin.pricing-settings.*') ? 'text-white' : 'text-gray-400 dark:text-gray-500' }}"
                            xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16">
                            <path
                                d="M8 0a4 4 0 0 1 4 4v1h2a2 2 0 0 1 2 2v2a2 2 0 0 1-2 2h-2v1a4 4 0 0 1-8 0v-1H2a2 2 0 0 1-2-2V7a2 2 0 0 1 2-2h2V4a4 4 0 0 1 4-4zm-2 6h4V4a2 2 0 0 0-4 0v2zm-2 2v2h2v-2H4zm6 0v2h2v-2h-2z"/>
                        </svg>
                        <span class="text-sm ml-2 sidebar-text duration-200">Pricing Settings</span>
                    </div>
                </a>
            </li>
            <li class="mb-0.5 last:mb-0" title="Pricing Audit Log">
                <a :class="sidebarExpanded ? 'py-2' : ''"
                   class="block pl-3 rounded-lg transition {{ Route::is('admin.pricing-settings.audits') ? 'bg-violet-500 text-white font-bold' : 'text-gray-800 dark:text-gray-100 hover:bg-gray-200 dark:hover:bg-gray-700' }}"
                   href="{{ route('admin.pricing-settings.audits') }}">
                    <div class="flex items-center">
                        <svg
                            class="shrink-0 fill-current {{ Route::is('admin.pricing-settings.audits') ? 'text-white' : 'text-gray-400 dark:text-gray-500' }}"
                            xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16">
                            <path
                                d="M2 1h9.293L14 3.707V15a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1zm8.5 1.5V4h1.5L10.5 2.5zM4 6h8v1H4V6zm0 3h8v1H4V9zm0 3h6v1H4v-1z"/>
                        </svg>
                        <span class="text-sm ml-2 sidebar-text duration-200">Pricing Audit Log</span>
                    </div>
                </a>
            </li>

            <li class="mb-0.5 last:mb-0" title="Artisan Commands">
                <a :class="sidebarExpanded ? 'py-2' : ''"
                   class="block pl-3 rounded-lg transition {{ Route::is('admin.artisan-commands') ? 'bg-violet-500 text-white font-bold' : 'text-gray-800 dark:text-gray-100 hover:bg-gray-200 dark:hover:bg-gray-700' }}"
                   href="{{ route('admin.artisan-commands') }}">
                    <div class="flex items-center">
                        <svg
                            class="shrink-0 fill-current {{ Route::is('admin.artisan-commands') ? 'text-white' : 'text-gray-400 dark:text-gray-500' }}"
                            xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16">
                            <path d="M6 10.5a.5.5 0 0 1 .5-.5h3a.5.5 0 0 1 0 1h-3a.5.5 0 0 1-.5-.5zm-2-3a.5.5 0 0 1 .5-.5h7a.5.5 0 0 1 0 1h-7a.5.5 0 0 1-.5-.5zm-2-3a.5.5 0 0 1 .5-.5h11a.5.5 0 0 1 0 1h-11a.5.5 0 0 1-.5-.5z"/>
                        </svg>
                        <span class="text-sm ml-2 sidebar-text duration-200">System Commands</span>
                    </div>
                </a>
            </li>
        @endif

        <li class="mb-0.5 last:mb-0" title="Notifications">
            <a :class="sidebarExpanded ? 'py-2' : 'py-2'"
               class="block pl-3 rounded-lg transition {{ Route::is('notifications*') ? 'bg-violet-500 text-white font-bold' : 'text-gray-800 dark:text-gray-100 hover:bg-gray-200 dark:hover:bg-gray-700' }}"
               href="{{route('notifications.index')}}">
                <div class="flex items-center">
                    <svg
                        class="shrink-0 fill-current {{ Route::is('notifications*') ? 'text-white' : 'text-gray-400 dark:text-gray-500' }}"
                        xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16">
                        <path
                            d="M8 16a2 2 0 0 0 2-2H6a2 2 0 0 0 2 2zm.995-14.901a1 1 0 1 0-1.99 0A5.002 5.002 0 0 0 3 6c0 1.098-.5 6-2 7h14c-1.5-1-2-5.902-2-7 0-2.42-1.72-4.44-4.005-4.901z"/>
                    </svg>
                    <span class="text-sm ml-2 sidebar-text duration-200">Alerts</span>
                </div>
            </a>
        </li>

        <li class="mb-0.5 last:mb-0 hidden" title="Media Management">
            <a :class="sidebarExpanded ? 'py-2' : ''"
               class="block pl-3 rounded-lg transition {{ Route::is('media*') ? 'bg-violet-500 text-white font-bold' : 'text-gray-800 dark:text-gray-100 hover:bg-gray-200 dark:hover:bg-gray-700' }}"
               href="{{route('media.index')}}">
                <div class="flex items-center">
                    <svg
                        class="shrink-0 fill-current {{ Route::is('media*') ? 'text-white' : 'text-gray-400 dark:text-gray-500' }}"
                        xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16">
                        <path
                            d="M6 0C4.343 0 3 1.343 3 3v10c0 1.657 1.343 3 3 3h4c1.657 0 3-1.343 3-3V3c0-1.657-1.343-3-3-3H6zm0 1h4c1.105 0 2 .895 2 2v8l-2-2-1.5 2-1.5-2-2 2V3c0-1.105.895-2 2-2zm1 3a1 1 0 1 0 0 2 1 1 0 0 0 0-2z"/>
                    </svg>
                    <span class="text-sm ml-2 sidebar-text duration-200">Media Management</span>
                </div>
            </a>
        </li>

        <!-- School Onboarding -->
        <li class="mb-0.5 last:mb-0" title="School Onboarding">
            <a :class="sidebarExpanded ? 'py-2' : ''"
               class="block pl-3 rounded-lg transition {{ Route::is('onboarding.*') ? 'bg-violet-500 text-white font-bold' : 'text-gray-800 dark:text-gray-100 hover:bg-gray-200 dark:hover:bg-gray-700' }}"
               href="{{route('onboarding.school-setup')}}">
                <div class="flex items-center">
                    <svg
                        class="shrink-0 fill-current {{ Route::is('onboarding.*') ? 'text-white' : 'text-gray-400 dark:text-gray-500' }}"
                        xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16">
                        <path
                            d="M1 2.828c.885-.37 2.154-.769 3.388-.893 1.33-.134 2.458.063 3.112.752v9.746c-.935-.53-2.12-.603-3.213-.493-1.18.12-2.37.461-3.287.811V2.828zm7.5-.141c.654-.689 1.782-.886 3.112-.752 1.234.124 2.503.523 3.388.893v9.923c-.918-.35-2.107-.692-3.287-.81-1.094-.111-2.278-.039-3.213.492V2.687zM8 1.783C7.015.936 5.587.81 4.287.94c-1.514.153-3.042.672-3.994 1.105A.5.5 0 0 0 0 2.5v11a.5.5 0 0 0 .707.455c.882-.4 2.303-.881 3.68-1.02 1.409-.142 2.59.087 3.223.877a.5.5 0 0 0 .78 0c.633-.79 1.814-1.019 3.222-.877 1.378.139 2.8.62 3.681 1.02A.5.5 0 0 0 16 13.5v-11a.5.5 0 0 0-.293-.455c-.952-.433-2.48-.952-3.994-1.105C10.413.809 8.985.936 8 1.783z"/>
                    </svg>
                    <span class="text-sm ml-2 sidebar-text duration-200">School Setup</span>
                </div>
            </a>
        </li>

        <!-- Chat System -->
        <li class="mb-0.5 last:mb-0" title="Group Chat">
            <a :class="sidebarExpanded ? 'py-2' : ''"
               class="block pl-3 rounded-lg transition {{ Route::is('chat*') ? 'bg-violet-500 text-white font-bold' : 'text-gray-800 dark:text-gray-100 hover:bg-gray-200 dark:hover:bg-gray-700' }}"
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

        <!-- Academic Chat -->
        <li class="mb-0.5 last:mb-0" title="Research Assistant">
            <a :class="sidebarExpanded ? 'py-2' : ''"
               class="block pl-3 rounded-lg transition {{ Route::is('academic-chat*') ? 'bg-violet-500 text-white font-bold' : 'text-gray-800 dark:text-gray-100 hover:bg-gray-200 dark:hover:bg-gray-700' }}"
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

        <!-- Forums -->
        <li class="mb-0.5 last:mb-0" title="Forums">
            <a :class="sidebarExpanded ? 'py-2' : ''"
               class="block pl-3 rounded-lg transition {{ Route::is('forums*') ? 'bg-violet-500 text-white font-bold' : 'text-gray-800 dark:text-gray-100 hover:bg-gray-200 dark:hover:bg-gray-700' }}"
               href="{{route('forums')}}">
                <div class="flex items-center">
                    <svg
                        class="shrink-0 fill-current {{ Route::is('forums*') ? 'text-white' : 'text-gray-400 dark:text-gray-500' }}"
                        xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16">
                        <path
                            d="M2.678 11.894a1 1 0 0 1 .287.801 10.97 10.97 0 0 1-.398 2c1.395-.323 2.247-.697 2.634-.893a1 1 0 0 1 .71-.074A8.06 8.06 0 0 0 8 14c3.996 0 7-2.807 7-6 0-3.192-3.004-6-7-6S1 4.808 1 8c0 1.468.617 2.83 1.678 3.894zm-.493 3.905a21.682 21.682 0 0 1-.713.129c-.2.032-.352-.176-.273-.362a9.68 9.68 0 0 0 .244-.637l.003-.01c.248-.72.45-1.548.524-2.319C.743 11.37 0 9.76 0 8c0-3.866 3.582-7 8-7s8 3.134 8 7-3.582 7-8 7a9.06 9.06 0 0 1-2.347-.306c-.52.263-1.639.742-3.468 1.105z"/>
                        <path
                            d="M4 5.5a.5.5 0 0 1 .5-.5h7a.5.5 0 0 1 0 1h-7a.5.5 0 0 1-.5-.5zM4 8a.5.5 0 0 1 .5-.5h7a.5.5 0 0 1 0 1h-7A.5.5 0 0 1 4 8zm0 2.5a.5.5 0 0 1 .5-.5h4a.5.5 0 0 1 0 1h-4a.5.5 0 0 1-.5-.5z"/>
                    </svg>
                    <span class="text-sm ml-2 sidebar-text duration-200">Discussion Forums</span>
                </div>
            </a>
        </li>


        <li class="mb-0.5 last:mb-0" title="Student Management">
            <a :class="sidebarExpanded ? 'py-2' : ''"
               class="block pl-3  rounded-lg transition {{ Route::is('admin.student-management') ? 'bg-violet-500 text-white font-bold' : 'text-gray-800 dark:text-gray-100 hover:bg-gray-200 dark:hover:bg-gray-700' }}"
               href="{{route('admin.student-management')}}">
                <div class="flex items-center">
                    <svg
                        class="shrink-0 fill-current {{ Route::is('admin.student-management') ? 'text-white' : 'text-gray-400 dark:text-gray-500' }}"
                        xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16">
                        <path
                            d="M.5 9.9a.5.5 0 0 1 .5.5v2.5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5a.5.5 0 0 1 1 0v2.5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.5a.5.5 0 0 1 .5-.5z"/>
                        <path
                            d="M7.646 1.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1-.708.708L8.5 2.707V11.5a.5.5 0 0 1-1 0V2.707L5.354 4.854a.5.5 0 1 1-.708-.708l3-3z"/>
                    </svg>
                    <span class="text-sm ml-2 sidebar-text duration-200">Data Import/Export</span>
                </div>
            </a>
        </li>

        @if(Auth::user()->hasRole('owner'))
            <li class="mb-2 last:mb-2" tile="ChangeLog">
                <a :class="sidebarExpanded ? 'py-2' : ''"
                   class="block pl-3 rounded-lg transition {{ Route::is('admin.change-log*') ? 'bg-violet-500 text-white font-bold' : 'text-gray-800 dark:text-gray-100 hover:bg-gray-200 dark:hover:bg-gray-700' }}"
                   href="{{route('admin.change-log.index')}}">
                    <div class="flex items-center">
                        <svg
                            class="shrink-0 fill-current {{ Route::is('admin.change-log*') ? 'text-white' : 'text-gray-400 dark:text-gray-500' }}"
                            xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16">
                            <path
                                d="M8 1C4.1 1 1 4.1 1 8s3.1 7 7 7 7-3.1 7-7-3.1-7-7-7zm0 13c-3.3 0-6-2.7-6-6s2.7-6 6-6 6 2.7 6 6-2.7 6-6 6z"/>
                            <path
                                d="M8 4c.3 0 .5.2.5.5v3.6l2.4 1.5c.2.1.3.4.1.6-.1.2-.4.3-.6.1l-2.6-1.6c-.2-.1-.3-.3-.3-.5V4.5c0-.3.2-.5.5-.5z"/>
                        </svg>

                        <span class="text-sm ml-2 sidebar-text duration-200">Change  Log</span>
                    </div>
                </a>
            </li>
        @endif
        <!-- User Logins -->
        <li class="mb-0.5 last:mb-0 hidden" title="User Logins">
            <a :class="sidebarExpanded ? 'py-2' : ''"
               class="block pl-3 rounded-lg transition {{ $activeTab === 'teacher-delegate' ? 'bg-violet-500 text-white font-bold' : 'text-gray-800 dark:text-gray-100 hover:bg-gray-200 dark:hover:bg-gray-700' }}"
               href="#"
               wire:click.prevent="setActiveTab('teacher-delegate')">
                <div class="flex items-center">
                    <svg
                        class="shrink-0 fill-current {{ $activeTab === 'teacher-delegate' ? 'text-white' : 'text-gray-400 dark:text-gray-500' }}"
                        xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16">
                        <path
                            d="M0 0h1v15h15v1H0V0Zm14.817 3.113a.5.5 0 0 1 .07.704l-4.5 5.5a.5.5 0 0 1-.74.037L7.06 6.767l-3.656 5.027a.5.5 0 0 1-.808-.588l4-5.5a.5.5 0 0 1 .758-.06l2.609 2.61 4.15-5.073a.5.5 0 0 1 .704-.07Z"/>
                    </svg>
                    <span class="text-sm ml-2 sidebar-text duration-200">Teacher Delegation</span>
                </div>
            </a>
        </li>
    </ul>
</div>
