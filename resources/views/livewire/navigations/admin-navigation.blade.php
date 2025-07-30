<div>
    <h3 class="text-xs hidden uppercase text-gray-400 dark:text-gray-500 font-semibold pl-3">
        <span class="hidden lg:block lg:sidebar-expanded:hidden 2xl:hidden text-center w-6" aria-hidden="true">•••</span>
        <span class="lg:hidden lg:sidebar-expanded:block 2xl:block">Admin Controls</span>
    </h3>
    <ul class="mt-3">
        <!-- Dashboard Overview -->
        <li class="mb-0.5 last:mb-0">
            <a class="block pl-4 pr-3 py-2 rounded-lg transition {{ Route::is('dashboard') ? 'bg-violet-500 text-white font-bold' : 'text-gray-800 dark:text-gray-100 hover:bg-gray-200 dark:hover:bg-gray-700' }}"
               href="{{route('dashboard')}}">
                <div class="flex items-center">
                    <svg class="shrink-0 fill-current {{ Route::is('dashboard') ? 'text-white' : 'text-gray-400 dark:text-gray-500' }}" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16">
                        <path d="M4 2a2 2 0 0 0-2 2v1h12V4a2 2 0 0 0-2-2H4ZM2 7v5a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V7H2Zm3 2a1 1 0 1 1 2 0 1 1 0 0 1-2 0Zm6 0a1 1 0 1 1 2 0 1 1 0 0 1-2 0Z"/>
                    </svg>
                    <span class="text-sm ml-4 sidebar-text duration-200">Dashboard Overview</span>
                </div>
            </a>
        </li>

        <li class="mb-0.5 last:mb-0">
            <a class="block pl-4 pr-3 py-2 rounded-lg transition {{ Route::is('academic-groups.index')? 'bg-violet-500 text-white my-1 font-bold' : 'text-gray-800 dark:text-gray-100 hover:bg-gray-200 dark:hover:bg-gray-700' }}"
               href="{{route('academic-groups.index')}}">
                <div class="flex items-center">
                    <svg class="shrink-0 fill-current {{ Route::is('academic-groups.index') ? 'text-white' : 'text-gray-400 dark:text-gray-500' }}" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16">
                        <path d="M1 2.828c.885-.37 2.154-.769 3.388-.893 1.33-.134 2.458.063 3.112.752v9.746c-.935-.53-2.12-.603-3.213-.493-1.18.12-2.37.461-3.287.811V2.828zm7.5-.141c.654-.689 1.782-.886 3.112-.752 1.234.124 2.503.523 3.388.893v9.923c-.918-.35-2.107-.692-3.287-.81-1.094-.111-2.278-.039-3.213.492V2.687zM8 1.783C7.015.936 5.587.81 4.287.94c-1.514.153-3.042.672-3.994 1.105A.5.5 0 0 0 0 2.5v11a.5.5 0 0 0 .707.455c.882-.4 2.303-.881 3.68-1.02 1.409-.142 2.59.087 3.223.877a.5.5 0 0 0 .78 0c.633-.79 1.814-1.019 3.222-.877 1.378.139 2.8.62 3.681 1.02A.5.5 0 0 0 16 13.5v-11a.5.5 0 0 0-.293-.455c-.952-.433-2.48-.952-3.994-1.105C10.413.809 8.985.936 8 1.783z"/>
                    </svg>
                    <span class="text-sm ml-4 sidebar-text duration-200">Academic Management</span>
                </div>
            </a>
        </li>

        <!-- User Management -->
        <li class="mb-0.5 last:mb-0 ">
            <a class="block pl-4 pr-3 py-2 rounded-lg transition {{ Route::is('users.index') ? 'bg-violet-500 text-white font-bold' : 'text-gray-800 dark:text-gray-100 hover:bg-gray-200 dark:hover:bg-gray-700' }}"
               href="{{route('users.index')}}">
                <div class="flex items-center">
                    <svg class="shrink-0 fill-current {{ Route::is('users.index') ? 'text-white' : 'text-gray-400 dark:text-gray-500' }}" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16">
                        <path d="M15 14s1 0 1-1-1-4-5-4-5 3-5 4 1 1 1 1h8Zm-7.978-1A.261.261 0 0 1 7 12.996c.001-.264.167-1.03.76-1.72C8.312 10.629 9.282 10 11 10c1.717 0 2.687.63 3.24 1.276.593.69.758 1.457.76 1.72l-.008.002A.274.274 0 0 1 15 13H7.022ZM11 7a2 2 0 1 0 0-4 2 2 0 0 0 0 4Zm3-2a3 3 0 1 1-6 0 3 3 0 0 1 6 0ZM6.936 9.28a5.88 5.88 0 0 0-1.23-.247A7.35 7.35 0 0 0 5 9c-4 0-5 3-5 4 0 .667.333 1 1 1h4.216A2.238 2.238 0 0 1 5 13c0-1.01.377-2.042 1.09-2.904.243-.294.526-.569.846-.816ZM4.92 10A5.493 5.493 0 0 0 4 13H1c0-.26.164-1.03.76-1.724.545-.636 1.492-1.256 3.16-1.275ZM1.5 5.5a3 3 0 1 1 6 0 3 3 0 0 1-6 0Zm3-2a2 2 0 1 0 0 4 2 2 0 0 0 0-4Z"/>
                    </svg>
                    <span class="text-sm ml-4 sidebar-text duration-200">User Management</span>
                </div>
            </a>
        </li>

        <!-- User Management -->
        <li class="mb-0.5 last:mb-0 ">
            <a class="block pl-4 pr-3 py-2 rounded-lg transition {{ Route::is('admin.users.impersonate') ? 'bg-violet-500 text-white font-bold' : 'text-gray-800 dark:text-gray-100 hover:bg-gray-200 dark:hover:bg-gray-700' }}"
               href="{{route('admin.users.impersonate')}}">
                <div class="flex items-center">
                    <svg class="shrink-0 fill-current {{ Route::is('users.impersonate') ? 'text-white' : 'text-gray-400 dark:text-gray-500' }}" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16">
                        <path d="M15 14s1 0 1-1-1-4-5-4-5 3-5 4 1 1 1 1h8Zm-7.978-1A.261.261 0 0 1 7 12.996c.001-.264.167-1.03.76-1.72C8.312 10.629 9.282 10 11 10c1.717 0 2.687.63 3.24 1.276.593.69.758 1.457.76 1.72l-.008.002A.274.274 0 0 1 15 13H7.022ZM11 7a2 2 0 1 0 0-4 2 2 0 0 0 0 4Zm3-2a3 3 0 1 1-6 0 3 3 0 0 1 6 0ZM6.936 9.28a5.88 5.88 0 0 0-1.23-.247A7.35 7.35 0 0 0 5 9c-4 0-5 3-5 4 0 .667.333 1 1 1h4.216A2.238 2.238 0 0 1 5 13c0-1.01.377-2.042 1.09-2.904.243-.294.526-.569.846-.816ZM4.92 10A5.493 5.493 0 0 0 4 13H1c0-.26.164-1.03.76-1.724.545-.636 1.492-1.256 3.16-1.275ZM1.5 5.5a3 3 0 1 1 6 0 3 3 0 0 1-6 0Zm3-2a2 2 0 1 0 0 4 2 2 0 0 0 0-4Z"/>
                    </svg>
                    <span class="text-sm ml-4 sidebar-text duration-200">User Impersonation</span>
                </div>
            </a>
        </li>

        <!-- Student Management -->
        <li class="mb-0.5 last:mb-0 ">
            <a class="block pl-4 pr-3 py-2 rounded-lg transition {{ Route::is('admin.student-management') ? 'bg-violet-500 text-white font-bold' : 'text-gray-800 dark:text-gray-100 hover:bg-gray-200 dark:hover:bg-gray-700' }}"
               href="{{route('admin.student-management')}}">
                <div class="flex items-center">
                    <svg class="shrink-0 fill-current {{ Route::is('admin.student-management') ? 'text-white' : 'text-gray-400 dark:text-gray-500' }}" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16">
                        <path d="M8.211 2.047a.5.5 0 0 0-.422 0l-7.5 3.5a.5.5 0 0 0 .025.917l7.5 3a.5.5 0 0 0 .372 0L14 7.14V13a1 1 0 0 0-1 1v2h3v-2a1 1 0 0 0-1-1V6.739l.686-.275a.5.5 0 0 0 .025-.917l-7.5-3.5Z"/>
                        <path d="M4.176 9.032a.5.5 0 0 0-.656.327l-.5 1.7a.5.5 0 0 0 .294.605l4.5 1.8a.5.5 0 0 0 .372 0l4.5-1.8a.5.5 0 0 0 .294-.605l-.5-1.7a.5.5 0 0 0-.656-.327L8 10.466 4.176 9.032Z"/>
                    </svg>
                    <span class="text-sm ml-4 sidebar-text duration-200">Student Management</span>
                </div>
            </a>
        </li>

        <!-- Student Groups -->
        <li class="mb-0.5 last:mb-0 ">
            <a class="block pl-4 pr-3 py-2 rounded-lg transition {{ Route::is('admin.student-groups') ? 'bg-violet-500 text-white font-bold' : 'text-gray-800 dark:text-gray-100 hover:bg-gray-200 dark:hover:bg-gray-700' }}"
               href="{{route('admin.student-groups')}}">
                <div class="flex items-center">
                    <svg class="shrink-0 fill-current {{ Route::is('admin.student-groups') ? 'text-white' : 'text-gray-400 dark:text-gray-500' }}" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16">
                        <path d="M7 14s-1 0-1-1 1-4 5-4 5 3 5 4-1 1-1 1H7Zm4-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6Zm-5.784 6A2.238 2.238 0 0 1 5 13c0-1.355.68-2.75 1.936-3.72A6.325 6.325 0 0 0 5 9c-4 0-5 3-5 4s1 1 1 1h4.216ZM4.5 8a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5Z"/>
                    </svg>
                    <span class="text-sm ml-4 sidebar-text duration-200">Student Groups</span>
                </div>
            </a>
        </li>

        <!-- Teacher Management -->
        <li class="mb-0.5 last:mb-0 ">
            <a class="block pl-4 pr-3 py-2 rounded-lg transition {{ Route::is('admin.teacher-management') ? 'bg-violet-500 text-white font-bold' : 'text-gray-800 dark:text-gray-100 hover:bg-gray-200 dark:hover:bg-gray-700' }}"
               href="{{route('admin.teacher-management')}}">
                <div class="flex items-center">
                    <svg class="shrink-0 fill-current {{ Route::is('admin.teacher-management') ? 'text-white' : 'text-gray-400 dark:text-gray-500' }}" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16">
                        <path d="M9.405 1.05c-.413-1.4-2.397-1.4-2.81 0l-.1.34a1.464 1.464 0 0 1-2.105.872l-.31-.17c-1.283-.698-2.686.705-1.987 1.987l.169.311c.446.82.023 1.841-.872 2.105l-.34.1c-1.4.413-1.4 2.397 0 2.81l.34.1a1.464 1.464 0 0 1 .872 2.105l-.17.31c-.698 1.283.705 2.686 1.987 1.987l.311-.169a1.464 1.464 0 0 1 2.105.872l.1.34c.413 1.4 2.397 1.4 2.81 0l.1-.34a1.464 1.464 0 0 1 2.105-.872l.31.17c1.283.698 2.686-.705 1.987-1.987l-.169-.311a1.464 1.464 0 0 1 .872-2.105l.34-.1c1.4-.413 1.4-2.397 0-2.81l-.34-.1a1.464 1.464 0 0 1-.872-2.105l.17-.31c.698-1.283-.705-2.686-1.987-1.987l-.311.169a1.464 1.464 0 0 1-2.105-.872l-.1-.34zM8 10.93a2.929 2.929 0 1 1 0-5.86 2.929 2.929 0 0 1 0 5.858z"/>
                    </svg>
                    <span class="text-sm ml-4 sidebar-text duration-200">Teacher Management</span>
                </div>
            </a>
        </li>

        <!-- Librarian Management -->
        <li class="mb-0.5 last:mb-0 ">
            <a class="block pl-4 pr-3 py-2 rounded-lg transition {{ Route::is('admin.librarian-management') ? 'bg-violet-500 text-white font-bold' : 'text-gray-800 dark:text-gray-100 hover:bg-gray-200 dark:hover:bg-gray-700' }}"
               href="{{route('admin.librarian-management')}}">
                <div class="flex items-center">
                    <svg class="shrink-0 fill-current {{ Route::is('admin.librarian-management') ? 'text-white' : 'text-gray-400 dark:text-gray-500' }}" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16">
                        <path d="M4 0h5.293A1 1 0 0 1 10 .293L13.707 4a1 1 0 0 1 .293.707V14a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V2a2 2 0 0 1 2-2zm5.5 1.5v2a1 1 0 0 0 1 1h2l-3-3zM4.5 8a.5.5 0 0 0 0 1h7a.5.5 0 0 0 0-1h-7zM4 10.5a.5.5 0 0 1 .5-.5h7a.5.5 0 0 1 0 1h-7a.5.5 0 0 1-.5-.5zm.5 2.5a.5.5 0 0 0 0 1h4a.5.5 0 0 0 0-1h-4z"/>
                    </svg>
                    <span class="text-sm ml-4 sidebar-text duration-200">Librarian Management</span>
                </div>
            </a>
        </li>

        <!-- Author Management -->
        <li class="mb-0.5 last:mb-0 ">
            <a class="block pl-4 pr-3 py-2 rounded-lg transition {{ Route::is('admin.author-management') ? 'bg-violet-500 text-white font-bold' : 'text-gray-800 dark:text-gray-100 hover:bg-gray-200 dark:hover:bg-gray-700' }}"
               href="{{route('admin.author-management')}}">
                <div class="flex items-center">
                    <svg class="shrink-0 fill-current {{ Route::is('admin.author-management') ? 'text-white' : 'text-gray-400 dark:text-gray-500' }}" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16">
                        <path d="m13.498.795.149-.149a1.207 1.207 0 1 1 1.707 1.708l-.149.148a1.5 1.5 0 0 1-.059 2.059L4.854 14.854a.5.5 0 0 1-.233.131l-4 1a.5.5 0 0 1-.606-.606l1-4a.5.5 0 0 1 .131-.232l9.642-9.642a.5.5 0 0 0-.642.056L6.854 4.854a.5.5 0 1 1-.708-.708L9.44.854A1.5 1.5 0 0 1 11.5.796a1.5 1.5 0 0 1 1.998-.001zm-.644.766a.5.5 0 0 0-.707 0L1.95 11.756l-.764 3.057 3.057-.764L14.44 3.854a.5.5 0 0 0 0-.708l-1.585-1.585z"/>
                    </svg>
                    <span class="text-sm ml-4 sidebar-text duration-200">Author Management</span>
                </div>
            </a>
        </li>

        <!-- Parent Management -->
        <li class="mb-0.5 last:mb-0">
            <a class="block pl-4 pr-3 py-2 rounded-lg transition {{ Route::is('admin.parent-management') ? 'bg-violet-500 text-white font-bold' : 'text-gray-800 dark:text-gray-100 hover:bg-gray-200 dark:hover:bg-gray-700' }}"
               href="{{route('admin.parent-management')}}">
                <div class="flex items-center">
                    <svg class="shrink-0 fill-current {{ Route::is('admin.parent-management') ? 'text-white' : 'text-gray-400 dark:text-gray-500' }}" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16">
                        <path d="M7 14s-1 0-1-1 1-4 5-4 5 3 5 4-1 1-1 1H7Zm4-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6Zm-5.784 6A2.238 2.238 0 0 1 5 13c0-1.355.68-2.75 1.936-3.72A6.325 6.325 0 0 0 5 9c-4 0-5 3-5 4s1 1 1 1h4.216ZM4.5 8a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5Z"/>
                    </svg>
                    <span class="text-sm ml-4 sidebar-text duration-200">Parent Management</span>
                </div>
            </a>
        </li>

        <!-- Book Management -->
        <li class="mb-0.5 last:mb-0 ">
            <a class="block pl-4 pr-3 py-2 rounded-lg transition {{ Route::is('admin.book-management') ? 'bg-violet-500 text-white font-bold' : 'text-gray-800 dark:text-gray-100 hover:bg-gray-200 dark:hover:bg-gray-700' }}"
               href="{{route('admin.book-management')}}">
                <div class="flex items-center">
                    <svg class="shrink-0 fill-current {{ Route::is('admin.book-management') ? 'text-white' : 'text-gray-400 dark:text-gray-500' }}" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16">
                        <path d="M1 2.828c.885-.37 2.154-.769 3.388-.893 1.33-.134 2.458.063 3.112.752v9.746c-.935-.53-2.12-.603-3.213-.493-1.18.12-2.37.461-3.287.811V2.828zm7.5-.141c.654-.689 1.782-.886 3.112-.752 1.234.124 2.503.523 3.388.893v9.923c-.918-.35-2.107-.692-3.287-.81-1.094-.111-2.278-.039-3.213.492V2.687zM8 1.783C7.015.936 5.587.81 4.287.94c-1.514.153-3.042.672-3.994 1.105A.5.5 0 0 0 0 2.5v11a.5.5 0 0 0 .707.455c.882-.4 2.303-.881 3.68-1.02 1.409-.142 2.59.087 3.223.877a.5.5 0 0 0 .78 0c.633-.79 1.814-1.019 3.222-.877 1.378.139 2.8.62 3.681 1.02A.5.5 0 0 0 16 13.5v-11a.5.5 0 0 0-.293-.455c-.952-.433-2.48-.952-3.994-1.105C10.413.809 8.985.936 8 1.783z"/>
                    </svg>
                    <span class="text-sm ml-4 sidebar-text duration-200">Book Management</span>
                </div>
            </a>
        </li>

        <!-- Book Approvals -->
        <li class="mb-0.5 last:mb-0 ">
            <a class="block pl-4 pr-3 py-2 rounded-lg transition {{ Route::is('admin.book-approvals') ? 'bg-violet-500 text-white font-bold' : 'text-gray-800 dark:text-gray-100 hover:bg-gray-200 dark:hover:bg-gray-700' }}"
               href="{{route('admin.book-approvals')}}">
                <div class="flex items-center">
                    <svg class="shrink-0 fill-current {{ Route::is('admin.book-approvals') ? 'text-white' : 'text-gray-400 dark:text-gray-500' }}" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16">
                        <path d="M10.97 4.97a.235.235 0 0 0-.02.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-1.071-1.05z"/>
                        <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
                    </svg>
                    <span class="text-sm ml-4 sidebar-text duration-200">Book Approvals</span>
                </div>
            </a>
        </li>

        <!-- Subject Management -->
        <li class="mb-0.5 last:mb-0 ">
            <a class="block pl-4 pr-3 py-2 rounded-lg transition {{ Route::is('admin.subject-management') ? 'bg-violet-500 text-white font-bold' : 'text-gray-800 dark:text-gray-100 hover:bg-gray-200 dark:hover:bg-gray-700' }}"
               href="{{route('admin.subject-management')}}">
                <div class="flex items-center">
                    <svg class="shrink-0 fill-current {{ Route::is('admin.subject-management') ? 'text-white' : 'text-gray-400 dark:text-gray-500' }}" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16">
                        <path d="M5 0h8a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2 2 2 0 0 1-2 2H3a2 2 0 0 1-2-2h1a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1V4a1 1 0 0 0-1-1H3a1 1 0 0 0-1 1H1a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v9a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1H5a1 1 0 0 0-1 1H3a2 2 0 0 1 2-2z"/>
                        <path d="M1 6v-.5a.5.5 0 0 1 1 0V6h.5a.5.5 0 0 1 0 1h-2a.5.5 0 0 1 0-1H1zm0 3v-.5a.5.5 0 0 1 1 0V9h.5a.5.5 0 0 1 0 1h-2a.5.5 0 0 1 0-1H1zm0 2.5v.5H.5a.5.5 0 0 0 0 1h2a.5.5 0 0 0 0-1H2v-.5a.5.5 0 0 0-1 0z"/>
                    </svg>
                    <span class="text-sm ml-4 sidebar-text duration-200">Subject Management</span>
                </div>
            </a>
        </li>

        <!-- Reports -->
        <li class="mb-0.5 last:mb-0 hidden">
            <a class="block pl-4 pr-3 py-2 rounded-lg transition {{ $activeTab === 'reports' ? 'bg-violet-500 text-white font-bold' : 'text-gray-800 dark:text-gray-100 hover:bg-gray-200 dark:hover:bg-gray-700' }}"
               href="#"
               wire:click.prevent="setActiveTab('reports')">
                <div class="flex items-center">
                    <svg class="shrink-0 fill-current {{ $activeTab === 'reports' ? 'text-white' : 'text-gray-400 dark:text-gray-500' }}" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16">
                        <path d="M0 0h1v15h15v1H0V0Zm14.817 3.113a.5.5 0 0 1 .07.704l-4.5 5.5a.5.5 0 0 1-.74.037L7.06 6.767l-3.656 5.027a.5.5 0 0 1-.808-.588l4-5.5a.5.5 0 0 1 .758-.06l2.609 2.61 4.15-5.073a.5.5 0 0 1 .704-.07Z"/>
                    </svg>
                    <span class="text-sm ml-4 sidebar-text duration-200">Reports</span>
                </div>
            </a>
        </li>

        <!-- User Logins -->
        <li class="mb-0.5 last:mb-0">
            <a class="block pl-4 pr-3 py-2 rounded-lg transition {{ Route::is('admin.logins') ? 'bg-violet-500 text-white font-bold' : 'text-gray-800 dark:text-gray-100 hover:bg-gray-200 dark:hover:bg-gray-700' }}"
               href="{{route('admin.logins')}}">
                <div class="flex items-center">
                    <svg class="shrink-0 fill-current {{ Route::is('admin.logins') ? 'text-white' : 'text-gray-400 dark:text-gray-500' }}" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16">
                        <path d="M11 6a3 3 0 1 1-6 0 3 3 0 0 1 6 0z"/>
                        <path fill-rule="evenodd" d="M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8zm8-7a7 7 0 0 0-5.468 11.37C3.242 11.226 4.805 10 8 10s4.757 1.225 5.468 2.37A7 7 0 0 0 8 1z"/>
                    </svg>
                    <span class="text-sm ml-4 sidebar-text duration-200">User Login Activity</span>
                </div>
            </a>
        </li>

        <li class="mb-0.5 last:mb-0">
            <a class="block pl-4 pr-3 py-2 rounded-lg transition {{ Route::is('school-settings.index') ? 'bg-violet-500 text-white font-bold' : 'text-gray-800 dark:text-gray-100 hover:bg-gray-200 dark:hover:bg-gray-700' }}"
               href="{{route('school-settings.index')}}">
                <div class="flex items-center">
                    <svg class="shrink-0 fill-current {{ Route::is('school-settings.index') ? 'text-white' : 'text-gray-400 dark:text-gray-500' }}" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16">
                        <path d="M11 6a3 3 0 1 1-6 0 3 3 0 0 1 6 0z"/>
                        <path fill-rule="evenodd" d="M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8zm8-7a7 7 0 0 0-5.468 11.37C3.242 11.226 4.805 10 8 10s4.757 1.225 5.468 2.37A7 7 0 0 0 8 1z"/>
                    </svg>
                    <span class="text-sm ml-4 sidebar-text duration-200">Academic Settings</span>
                </div>
            </a>
        </li>

        <!-- User Logins -->
        <li class="mb-0.5 last:mb-0 hidden">
            <a class="block pl-4 pr-3 py-2 rounded-lg transition {{ $activeTab === 'teacher-delegate' ? 'bg-violet-500 text-white font-bold' : 'text-gray-800 dark:text-gray-100 hover:bg-gray-200 dark:hover:bg-gray-700' }}"
               href="#"
               wire:click.prevent="setActiveTab('teacher-delegate')">
                <div class="flex items-center">
                    <svg class="shrink-0 fill-current {{ $activeTab === 'teacher-delegate' ? 'text-white' : 'text-gray-400 dark:text-gray-500' }}" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16">
                        <path d="M0 0h1v15h15v1H0V0Zm14.817 3.113a.5.5 0 0 1 .07.704l-4.5 5.5a.5.5 0 0 1-.74.037L7.06 6.767l-3.656 5.027a.5.5 0 0 1-.808-.588l4-5.5a.5.5 0 0 1 .758-.06l2.609 2.61 4.15-5.073a.5.5 0 0 1 .704-.07Z"/>
                    </svg>
                    <span class="text-sm ml-4 sidebar-text duration-200">Teacher Delegation</span>
                </div>
            </a>
        </li>
    </ul>
</div>
