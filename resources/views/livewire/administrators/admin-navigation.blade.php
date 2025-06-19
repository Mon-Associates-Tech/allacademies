<div>
    <h3 class="text-xs hidden uppercase text-gray-400 dark:text-gray-500 font-semibold pl-3">
        <span class="hidden lg:block lg:sidebar-expanded:hidden 2xl:hidden text-center w-6" aria-hidden="true">•••</span>
        <span class="lg:hidden lg:sidebar-expanded:block 2xl:block">Admin Controls</span>
    </h3>
    <ul class="mt-3">
        <!-- Dashboard Overview -->
        <li class="mb-0.5 last:mb-0">
            <a class="block pl-4 pr-3 py-2 rounded-lg transition {{ $activeTab === 'overview' ? 'bg-violet-500 text-white font-bold' : 'text-gray-800 dark:text-gray-100 hover:bg-gray-200 dark:hover:bg-gray-700' }}"
               href="#"
               wire:click.prevent="setActiveTab('overview')">
                <div class="flex items-center">
                    <svg class="shrink-0 fill-current {{ $activeTab === 'overview' ? 'text-white' : 'text-gray-400 dark:text-gray-500' }}" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16">
                        <path d="M5.936.278A7.983 7.983 0 0 1 8 0a8 8 0 1 1-8 8c0-.722.104-1.413.278-2.064a1 1 0 1 1 1.932.516A5.99 5.99 0 0 0 2 8a6 6 0 1 0 6-6c-.53 0-1.045.076-1.548.21A1 1 0 1 1 5.936.278Z" />
                        <path d="M6.068 7.482A2.003 2.003 0 0 0 8 10a2 2 0 1 0-.518-3.932L3.707 2.293a1 1 0 0 0-1.414 1.414l3.775 3.775Z" />
                    </svg>
                    <span class="text-sm ml-4 sidebar-text duration-200">Dashboard Overview</span>
                </div>
            </a>
        </li>

        <li class="mb-0.5 last:mb-0">
            <a class="block pl-4 pr-3 py-2 rounded-lg transition {{ $activeTab === 'academic-management' ? 'bg-violet-500 text-white font-bold' : 'text-gray-800 dark:text-gray-100 hover:bg-gray-200 dark:hover:bg-gray-700' }}"
               href="#"
               wire:click.prevent="setActiveTab('academic-management')">
                <div class="flex items-center">
                    <svg class="shrink-0 fill-current {{ $activeTab === 'academic-management' ? 'text-white' : 'text-gray-400 dark:text-gray-500' }}" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16">
                        <path d="M5.936.278A7.983 7.983 0 0 1 8 0a8 8 0 1 1-8 8c0-.722.104-1.413.278-2.064a1 1 0 1 1 1.932.516A5.99 5.99 0 0 0 2 8a6 6 0 1 0 6-6c-.53 0-1.045.076-1.548.21A1 1 0 1 1 5.936.278Z" />
                        <path d="M6.068 7.482A2.003 2.003 0 0 0 8 10a2 2 0 1 0-.518-3.932L3.707 2.293a1 1 0 0 0-1.414 1.414l3.775 3.775Z" />
                    </svg>
                    <span class="text-sm ml-4 sidebar-text duration-200">Academic Management</span>
                </div>
            </a>
        </li>


        <!-- User Management -->
        <li class="mb-0.5 last:mb-0">
            <a class="block pl-4 pr-3 py-2 rounded-lg transition {{ $activeTab === 'users' ? 'bg-violet-500 text-white font-bold' : 'text-gray-800 dark:text-gray-100 hover:bg-gray-200 dark:hover:bg-gray-700' }}"
               href="#"
               wire:click.prevent="setActiveTab('users')">
                <div class="flex items-center">
                    <svg class="shrink-0 fill-current {{ $activeTab === 'users' ? 'text-white' : 'text-gray-400 dark:text-gray-500' }}" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16">
                        <path d="M8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm2-3a2 2 0 1 1-4 0 2 2 0 0 1 4 0zm4 8c0 1-1 1-1 1H3s-1 0-1-1 1-4 6-4 6 3 6 4zm-1-.004c-.001-.246-.154-.986-.832-1.664C11.516 10.68 10.289 10 8 10c-2.29 0-3.516.68-4.168 1.332-.678.678-.83 1.418-.832 1.664h10z"/>
                    </svg>
                    <span class="text-sm ml-4 sidebar-text duration-200">User Management</span>
                </div>
            </a>
        </li>

        <!-- Student Management -->
        <li class="mb-0.5 last:mb-0">
            <a class="block pl-4 pr-3 py-2 rounded-lg transition {{ $activeTab === 'students' ? 'bg-violet-500 text-white font-bold' : 'text-gray-800 dark:text-gray-100 hover:bg-gray-200 dark:hover:bg-gray-700' }}"
               href="#"
               wire:click.prevent="setActiveTab('students')">
                <div class="flex items-center">
                    <svg class="shrink-0 fill-current {{ $activeTab === 'students' ? 'text-white' : 'text-gray-400 dark:text-gray-500' }}" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16">
                        <path d="M8.211 2.047a.5.5 0 0 0-.422 0l-7.5 3.5a.5.5 0 0 0 .025.917l7.5 3a.5.5 0 0 0 .372 0L14 7.14V13a1 1 0 0 0-1 1v2h3v-2a1 1 0 0 0-1-1V6.739l.686-.275a.5.5 0 0 0 .025-.917l-7.5-3.5Z"/>
                        <path d="M4.176 9.032a.5.5 0 0 0-.656.327l-.5 1.7a.5.5 0 0 0 .294.605l4.5 1.8a.5.5 0 0 0 .372 0l4.5-1.8a.5.5 0 0 0 .294-.605l-.5-1.7a.5.5 0 0 0-.656-.327L8 10.466 4.176 9.032Z"/>
                    </svg>
                    <span class="text-sm ml-4 sidebar-text duration-200">Student Management</span>
                </div>
            </a>
        </li>

        <!-- Student Groups -->
        <li class="mb-0.5 last:mb-0">
            <a class="block pl-4 pr-3 py-2 rounded-lg transition {{ $activeTab === 'groups' ? 'bg-violet-500 text-white font-bold' : 'text-gray-800 dark:text-gray-100 hover:bg-gray-200 dark:hover:bg-gray-700' }}"
               href="#"
               wire:click.prevent="setActiveTab('groups')">
                <div class="flex items-center">
                    <svg class="shrink-0 fill-current {{ $activeTab === 'groups' ? 'text-white' : 'text-gray-400 dark:text-gray-500' }}" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16">
                        <path d="M7 14s-1 0-1-1 1-4 5-4 5 3 5 4-1 1-1 1H7Zm4-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6Zm-5.784 6A2.238 2.238 0 0 1 5 13c0-1.355.68-2.75 1.936-3.72A6.325 6.325 0 0 0 5 9c-4 0-5 3-5 4s1 1 1 1h4.216ZM4.5 8a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5Z"/>
                    </svg>
                    <span class="text-sm ml-4 sidebar-text duration-200">Student Groups</span>
                </div>
            </a>
        </li>

        <!-- Teacher Management -->
        <li class="mb-0.5 last:mb-0">
            <a class="block pl-4 pr-3 py-2 rounded-lg transition {{ $activeTab === 'teachers' ? 'bg-violet-500 text-white font-bold' : 'text-gray-800 dark:text-gray-100 hover:bg-gray-200 dark:hover:bg-gray-700' }}"
               href="#"
               wire:click.prevent="setActiveTab('teachers')">
                <div class="flex items-center">
                    <svg class="shrink-0 fill-current {{ $activeTab === 'teachers' ? 'text-white' : 'text-gray-400 dark:text-gray-500' }}" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16">
                        <path d="M1 2.828c.885-.37 2.154-.769 3.388-.893 1.33-.134 2.458.063 3.112.752v9.746c-.935-.53-2.12-.603-3.213-.493-1.18.12-2.37.461-3.287.811V2.828zm7.5-.141c.654-.689 1.782-.886 3.112-.752 1.234.124 2.503.523 3.388.893v9.923c-.918-.35-2.107-.692-3.287-.81-1.094-.111-2.278-.039-3.213.492V2.687zM8 1.783C7.015.936 5.587.81 4.287.94c-1.514.153-3.042.672-3.994 1.105A.5.5 0 0 0 0 2.5v11a.5.5 0 0 0 .707.455c.882-.4 2.303-.881 3.68-1.02 1.409-.142 2.59.087 3.223.877a.5.5 0 0 0 .78 0c.633-.79 1.814-1.019 3.222-.877 1.378.139 2.8.62 3.681 1.02A.5.5 0 0 0 16 13.5v-11a.5.5 0 0 0-.293-.455c-.952-.433-2.48-.952-3.994-1.105C10.413.809 8.985.936 8 1.783z"/>
                    </svg>
                    <span class="text-sm ml-4 sidebar-text duration-200">Teacher Management</span>
                </div>
            </a>
        </li>

        <!-- Librarian Management -->
        <li class="mb-0.5 last:mb-0">
            <a class="block pl-4 pr-3 py-2 rounded-lg transition {{ $activeTab === 'librarians' ? 'bg-violet-500 text-white font-bold' : 'text-gray-800 dark:text-gray-100 hover:bg-gray-200 dark:hover:bg-gray-700' }}"
               href="#"
               wire:click.prevent="setActiveTab('librarians')">
                <div class="flex items-center">
                    <svg class="shrink-0 fill-current {{ $activeTab === 'librarians' ? 'text-white' : 'text-gray-400 dark:text-gray-500' }}" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16">
                        <path d="M8 2.748l-.717-.737C5.6.281 2.514.878 1.4 3.053c-.523 1.023-.641 2.5.314 4.385.92 1.815 2.834 3.989 6.286 6.357 3.452-2.368 5.365-4.542 6.286-6.357.955-1.886.838-3.362.314-4.385C13.486.878 10.4.28 8.717 2.01L8 2.748zM8 15C-7.333 4.868 3.279-3.04 7.824 1.143c.06.055.119.112.176.171a3.12 3.12 0 0 1 .176-.17C12.72-3.042 23.333 4.867 8 15z"/>
                    </svg>
                    <span class="text-sm ml-4 sidebar-text duration-200">Librarian Management</span>
                </div>
            </a>
        </li>

        <!-- Author Management -->
        <li class="mb-0.5 last:mb-0">
            <a class="block pl-4 pr-3 py-2 rounded-lg transition {{ $activeTab === 'authors' ? 'bg-violet-500 text-white font-bold' : 'text-gray-800 dark:text-gray-100 hover:bg-gray-200 dark:hover:bg-gray-700' }}"
               href="#"
               wire:click.prevent="setActiveTab('authors')">
                <div class="flex items-center">
                    <svg class="shrink-0 fill-current {{ $activeTab === 'authors' ? 'text-white' : 'text-gray-400 dark:text-gray-500' }}" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16">
                        <path d="M12.146.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1 0 .708l-10 10a.5.5 0 0 1-.168.11l-5 2a.5.5 0 0 1-.65-.65l2-5a.5.5 0 0 1 .11-.168l10-10zM11.207 2.5 13.5 4.793 14.793 3.5 12.5 1.207 11.207 2.5zm1.586 3L10.5 3.207 4 9.707V10h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.293l6.5-6.5zm-9.761 5.175-.106.106-1.528 3.821 3.821-1.528.106-.106A.5.5 0 0 1 5 12.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.468-.325z"/>
                    </svg>
                    <span class="text-sm ml-4 sidebar-text duration-200">Author Management</span>
                </div>
            </a>
        </li>

        <!-- Book Management -->
        <li class="mb-0.5 last:mb-0">
            <a class="block pl-4 pr-3 py-2 rounded-lg transition {{ $activeTab === 'books' ? 'bg-violet-500 text-white font-bold' : 'text-gray-800 dark:text-gray-100 hover:bg-gray-200 dark:hover:bg-gray-700' }}"
               href="#"
               wire:click.prevent="setActiveTab('books')">
                <div class="flex items-center">
                    <svg class="shrink-0 fill-current {{ $activeTab === 'books' ? 'text-white' : 'text-gray-400 dark:text-gray-500' }}" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16">
                        <path d="M1 2.828c.885-.37 2.154-.769 3.388-.893 1.33-.134 2.458.063 3.112.752v9.746c-.935-.53-2.12-.603-3.213-.493-1.18.12-2.37.461-3.287.811V2.828zm7.5-.141c.654-.689 1.782-.886 3.112-.752 1.234.124 2.503.523 3.388.893v9.923c-.918-.35-2.107-.692-3.287-.81-1.094-.111-2.278-.039-3.213.492V2.687zM8 1.783C7.015.936 5.587.81 4.287.94c-1.514.153-3.042.672-3.994 1.105A.5.5 0 0 0 0 2.5v11a.5.5 0 0 0 .707.455c.882-.4 2.303-.881 3.68-1.02 1.409-.142 2.59.087 3.223.877a.5.5 0 0 0 .78 0c.633-.79 1.814-1.019 3.222-.877 1.378.139 2.8.62 3.681 1.02A.5.5 0 0 0 16 13.5v-11a.5.5 0 0 0-.293-.455c-.952-.433-2.48-.952-3.994-1.105C10.413.809 8.985.936 8 1.783z"/>
                    </svg>
                    <span class="text-sm ml-4 sidebar-text duration-200">Book Management</span>
                </div>
            </a>
        </li>

        <!-- Book Approvals -->
        <li class="mb-0.5 last:mb-0">
            <a class="block pl-4 pr-3 py-2 rounded-lg transition {{ $activeTab === 'book-approvals' ? 'bg-violet-500 text-white font-bold' : 'text-gray-800 dark:text-gray-100 hover:bg-gray-200 dark:hover:bg-gray-700' }}"
               href="#"
               wire:click.prevent="setActiveTab('book-approvals')">
                <div class="flex items-center">
                    <svg class="shrink-0 fill-current {{ $activeTab === 'book-approvals' ? 'text-white' : 'text-gray-400 dark:text-gray-500' }}" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16">
                        <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
                        <path d="M10.97 4.97a.235.235 0 0 0-.02.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-1.071-1.05z"/>
                    </svg>
                    <span class="text-sm ml-4 sidebar-text duration-200">Book Approvals</span>
                </div>
            </a>
        </li>

        <!-- Subject Management -->
        <li class="mb-0.5 last:mb-0">
            <a class="block pl-4 pr-3 py-2 rounded-lg transition {{ $activeTab === 'subjects' ? 'bg-violet-500 text-white font-bold' : 'text-gray-800 dark:text-gray-100 hover:bg-gray-200 dark:hover:bg-gray-700' }}"
               href="#"
               wire:click.prevent="setActiveTab('subjects')">
                <div class="flex items-center">
                    <svg class="shrink-0 fill-current {{ $activeTab === 'subjects' ? 'text-white' : 'text-gray-400 dark:text-gray-500' }}" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16">
                        <path d="M5 0h8a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2 2 2 0 0 1-2 2H3a2 2 0 0 1-2-2h1a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1V4a1 1 0 0 0-1-1H3a1 1 0 0 0-1 1H1a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v9a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1H5a1 1 0 0 0-1 1H3a2 2 0 0 1 2-2z"/>
                        <path d="M1 6v-.5a.5.5 0 0 1 1 0V6h.5a.5.5 0 0 1 0 1h-2a.5.5 0 0 1 0-1H1zm0 3v-.5a.5.5 0 0 1 1 0V9h.5a.5.5 0 0 1 0 1h-2a.5.5 0 0 1 0-1H1zm0 2.5v.5H.5a.5.5 0 0 0 0 1h2a.5.5 0 0 0 0-1H2v-.5a.5.5 0 0 0-1 0z"/>
                    </svg>
                    <span class="text-sm ml-4 sidebar-text duration-200">Subject Management</span>
                </div>
            </a>
        </li>

        <!-- Reports -->
        <li class="mb-0.5 last:mb-0">
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
            <a class="block pl-4 pr-3 py-2 rounded-lg transition {{ $activeTab === 'user-logins' ? 'bg-violet-500 text-white font-bold' : 'text-gray-800 dark:text-gray-100 hover:bg-gray-200 dark:hover:bg-gray-700' }}"
               href="#"
               wire:click.prevent="setActiveTab('user-logins')">
                <div class="flex items-center">
                    <svg class="shrink-0 fill-current {{ $activeTab === 'reports' ? 'text-white' : 'text-gray-400 dark:text-gray-500' }}" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16">
                        <path d="M0 0h1v15h15v1H0V0Zm14.817 3.113a.5.5 0 0 1 .07.704l-4.5 5.5a.5.5 0 0 1-.74.037L7.06 6.767l-3.656 5.027a.5.5 0 0 1-.808-.588l4-5.5a.5.5 0 0 1 .758-.06l2.609 2.61 4.15-5.073a.5.5 0 0 1 .704-.07Z"/>
                    </svg>
                    <span class="text-sm ml-4 sidebar-text duration-200">User Login Activity</span>
                </div>
            </a>
        </li>
    </ul>
</div>
