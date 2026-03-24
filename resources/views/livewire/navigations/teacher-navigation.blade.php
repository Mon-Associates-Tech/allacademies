<div>
    <h3 class="text-xs uppercase text-gray-400 dark:text-gray-500 font-semibold pl-3">
        <span class="hidden lg:hidden lg:sidebar-expanded:hidden 2xl:hidden text-center w-6"
              aria-hidden="true">•••</span>
        <span class="lg:hidden lg:sidebar-expanded:block 2xl:block">Teacher Portal</span>
    </h3>
    <ul class="mt-3">
        <!-- Dashboard -->
        <li class="mb-0.5 last:mb-0">
            <a class="block pl-3 pr-3 py-2 rounded-lg transition {{ Route::is('dashboard') ? 'bg-violet-500 text-white font-bold' : 'text-gray-800 dark:text-gray-100 hover:bg-gray-200 dark:hover:bg-gray-700' }}"
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

        <!-- My Courses -->
        <li class="mb-0.5 last:mb-0">
            <a class="block pl-3 pr-3 py-2 rounded-lg transition {{ Route::is('teachers.subjects*') ? 'bg-violet-500 text-white font-bold' : 'text-gray-800 dark:text-gray-100 hover:bg-gray-200 dark:hover:bg-gray-700' }}"
               href="{{route('teachers.subjects.index')}}">
                <div class="flex items-center">
                    <svg
                        class="shrink-0 fill-current {{ Route::is('teachers.subjects*') ? 'text-white' : 'text-gray-400 dark:text-gray-500' }}"
                        width="16" height="16" viewBox="0 0 24 24">
                        <path
                            d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                    </svg>
                    <span class="text-sm ml-2 sidebar-text duration-200">My Courses</span>
                </div>
            </a>
        </li>


        <li class="mb-0.5 last:mb-0">
            <a class="block pl-3 pr-3 py-2 rounded-lg transition {{ Route::is('teachers.attendance*') ? 'bg-violet-500 text-white font-bold' : 'text-gray-800 dark:text-gray-100 hover:bg-gray-200 dark:hover:bg-gray-700' }}"
               href="{{route('teachers.attendance.index')}}">
                <div class="flex items-center">
                    <svg
                        class="shrink-0 fill-current {{ Route::is('teachers.attendance*') ? 'text-white' : 'text-gray-400 dark:text-gray-500' }}"
                        width="16" height="16" viewBox="0 0 24 24">
                        <path
                            d="M19 3h-4.18C14.4 1.84 13.3 1 12 1c-1.3 0-2.4.84-2.82 2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-7 0c.55 0 1 .45 1 1s-.45 1-1 1-1-.45-1-1 .45-1 1-1zm2 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/>
                    </svg>
                    <span class="text-sm ml-2 sidebar-text duration-200">Attendance</span>
                </div>
            </a>
        </li>


        <!-- Books -->
        <li class="mb-0.5 last:mb-0">
            <a class="block pl-3 pr-3 py-2 rounded-lg transition {{ Route::is('books*') ? 'bg-violet-500 text-white font-bold' : 'text-gray-800 dark:text-gray-100 hover:bg-gray-200 dark:hover:bg-gray-700' }}"
               href="{{route('books.index')}}">
                <div class="flex items-center">
                    <svg
                        class="shrink-0 fill-current {{ Route::is('books*') ? 'text-white' : 'text-gray-400 dark:text-gray-500' }}"
                        width="16" height="16" viewBox="0 0 24 24">
                        <path
                            d="M21 5c-1.11-.35-2.33-.5-3.5-.5-1.95 0-4.05.4-5.5 1.5-1.45-1.1-3.55-1.5-5.5-1.5S2.45 4.9 1 6v14.65c0 .25.25.5.5.5.1 0 .15-.05.25-.05C3.1 20.45 5.05 20 6.5 20c1.95 0 4.05.4 5.5 1.5 1.35-.85 3.8-1.5 5.5-1.5 1.65 0 3.35.3 4.75 1.05.1.05.15.05.25.05.25 0 .5-.25.5-.5V6c-.6-.45-1.25-.75-2-1zm0 13.5c-1.1-.35-2.3-.5-3.5-.5-1.7 0-4.15.65-5.5 1.5V8c1.35-.85 3.8-1.5 5.5-1.5 1.2 0 2.4.15 3.5.5v11.5z"/>
                    </svg>
                    <span class="text-sm ml-2 sidebar-text duration-200">Books</span>
                </div>
            </a>
        </li>


        <p class="px-3 mt-4 mb-1 text-[10px] uppercase tracking-wide text-gray-400 dark:text-gray-500">Assignments</p>
        <li class="mb-0.5 last:mb-0">
            <a class="block pl-3 pr-3 py-2 rounded-lg transition {{ Route::is('teachers.assignments*') ? 'bg-violet-500 text-white font-bold' : 'text-gray-800 dark:text-gray-100 hover:bg-gray-200 dark:hover:bg-gray-700' }}"
               href="{{route('teachers.assignments.index')}}">
                <div class="flex items-center">
                    <svg
                        class="shrink-0 fill-current {{ Route::is('teachers.assignments*') ? 'text-white' : 'text-gray-400 dark:text-gray-500' }}"
                        width="16" height="16" viewBox="0 0 24 24">
                        <path
                            d="M19 3h-4.18C14.4 1.84 13.3 1 12 1c-1.3 0-2.4.84-2.82 2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-7 0c.55 0 1 .45 1 1s-.45 1-1 1-1-.45-1-1 .45-1 1-1zm-5 16h10v-2H7v2zm0-4h10v-2H7v2zm0-4h7v-2H7v2z"/>
                    </svg>
                    <span class="text-sm ml-2 sidebar-text duration-200">Assignments</span>
                </div>
            </a>
        </li>

        <!-- Book Assignments -->
        <li class="mb-0.5 last:mb-0">
            <a class="block pl-3 pr-3 py-2 rounded-lg transition {{ Route::is('teachers.book-assignments*') ? 'bg-violet-500 text-white font-bold' : 'text-gray-800 dark:text-gray-100 hover:bg-gray-200 dark:hover:bg-gray-700' }}"
               href="{{route('teachers.book-assignments.create')}}">
                <div class="flex items-center">
                    <svg
                        class="shrink-0 fill-current {{ Route::is('teachers.book-assignments*') ? 'text-white' : 'text-gray-400 dark:text-gray-500' }}"
                        width="16" height="16" viewBox="0 0 24 24">
                        <path
                            d="M19 3h-4.18C14.4 1.84 13.3 1 12 1c-1.3 0-2.4.84-2.82 2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-7 0c.55 0 1 .45 1 1s-.45 1-1 1-1-.45-1-1 .45-1 1-1zm-5 16h10v-2H7v2zm0-4h10v-2H7v2zm0-4h7v-2H7v2z"/>
                    </svg>
                    <span class="text-sm ml-2 sidebar-text duration-200">Book Assignments</span>
                </div>
            </a>
        </li>

        <p class="px-3 mt-4 mb-1 text-[10px] uppercase tracking-wide text-gray-400 dark:text-gray-500">General Exams</p>
        <li class="mb-0.5 last:mb-0">
            <a class="block pl-3 pr-3 py-2 rounded-lg transition {{ Route::is('teachers.general-exams.index') ? 'bg-violet-500 text-white font-bold' : 'text-gray-800 dark:text-gray-100 hover:bg-gray-200 dark:hover:bg-gray-700' }}"
               href="{{ route('teachers.general-exams.index') }}">
                <div class="flex items-center">
                    <svg class="shrink-0 fill-current {{ Route::is('teachers.general-exams.index') ? 'text-white' : 'text-gray-400 dark:text-gray-500' }}" width="16" height="16" viewBox="0 0 24 24">
                        <path d="M19 3h-4.18C14.4 1.84 13.3 1 12 1c-1.3 0-2.4.84-2.82 2H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V5a2 2 0 0 0-2-2zm-7 0c.55 0 1 .45 1 1s-.45 1-1 1-1-.45-1-1 .45-1 1-1zm-5 16h10v-2H7v2zm0-4h10v-2H7v2zm0-4h10V7H7v4z"/>
                    </svg>
                    <span class="text-sm ml-2 sidebar-text duration-200">Manage General Exams</span>
                </div>
            </a>
        </li>
        <li class="mb-0.5 last:mb-0">
            <a class="block pl-3 pr-3 py-2 rounded-lg transition {{ Route::is('general-exams.join*') ? 'bg-violet-500 text-white font-bold' : 'text-gray-800 dark:text-gray-100 hover:bg-gray-200 dark:hover:bg-gray-700' }}"
               href="{{ route('general-exams.join') }}">
                <div class="flex items-center">
                    <svg class="shrink-0 fill-current {{ Route::is('general-exams.join*') ? 'text-white' : 'text-gray-400 dark:text-gray-500' }}" width="16" height="16" viewBox="0 0 24 24">
                        <path d="M19 3h-4.18C14.4 1.84 13.3 1 12 1c-1.3 0-2.4.84-2.82 2H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V5a2 2 0 0 0-2-2zm-7 0c.55 0 1 .45 1 1s-.45 1-1 1-1-.45-1-1 .45-1 1-1zm-2 14h-3v-2h3v2zm5-4h-8v-2h8v2zm0-4h-8V7h8v4z"/>
                    </svg>
                    <span class="text-sm ml-2 sidebar-text duration-200">Join General Exam</span>
                </div>
            </a>
        </li>
        <li class="mb-0.5 last:mb-0">
            <a class="block pl-3 pr-3 py-2 rounded-lg transition {{ Route::is('teachers.general-exams.create') ? 'bg-violet-500 text-white font-bold' : 'text-gray-800 dark:text-gray-100 hover:bg-gray-200 dark:hover:bg-gray-700' }}"
               href="{{ route('teachers.general-exams.create') }}">
                <div class="flex items-center">
                    <svg class="shrink-0 fill-current {{ Route::is('teachers.general-exams.create') ? 'text-white' : 'text-gray-400 dark:text-gray-500' }}" width="16" height="16" viewBox="0 0 24 24">
                        <path d="M13 11h6v2h-6v6h-2v-6H5v-2h6V5h2v6zm8-6V5a2 2 0 0 0-2-2h-4.18C14.4 1.84 13.3 1 12 1c-1.3 0-2.4.84-2.82 2H5a2 2 0 0 0-2 2v1h16z"/>
                    </svg>
                    <span class="text-sm ml-2 sidebar-text duration-200">Create General Exam</span>
                </div>
            </a>
        </li>


        <!-- Students -->
        <li class="mb-0.5 last:mb-0">
            <a class="block pl-3 pr-3 py-2 rounded-lg transition {{ Route::is('teacher.students*') ? 'bg-violet-500 text-white font-bold' : 'text-gray-800 dark:text-gray-100 hover:bg-gray-200 dark:hover:bg-gray-700' }}"
               href="{{route('teachers.students.index')}}">
                <div class="flex items-center">
                    <svg
                        class="shrink-0 fill-current {{ Route::is('teacher.students*') ? 'text-white' : 'text-gray-400 dark:text-gray-500' }}"
                        width="16" height="16" viewBox="0 0 24 24">
                        <path
                            d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"/>
                    </svg>
                    <span class="text-sm ml-2 sidebar-text duration-200">Students</span>
                </div>
            </a>
        </li>


        <!-- Schedules -->
        <li class="mb-0.5 last:mb-0">
            <a class="block pl-3 pr-3 py-2 rounded-lg transition {{ Route::is('teachers.schedules*') ? 'bg-violet-500 text-white font-bold' : 'text-gray-800 dark:text-gray-100 hover:bg-gray-200 dark:hover:bg-gray-700' }}"
               href="{{route('teachers.schedules')}}">
                <div class="flex items-center">
                    <svg
                        class="shrink-0 fill-current {{ Route::is('teachers.schedules*') ? 'text-white' : 'text-gray-400 dark:text-gray-500' }}"
                        width="16" height="16" viewBox="0 0 24 24">
                        <path
                            d="M19 3h-1V1h-2v2H8V1H6v2H5c-1.11 0-2 .9-2 2v14c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V8h14v11zM7 10h5v5H7z"/>
                    </svg>
                    <span class="text-sm ml-2 sidebar-text duration-200">Schedules</span>
                </div>
            </a>
        </li>

        <li class="mb-0.5 last:mb-0">
            <a class="block pl-3 pr-3 py-2 rounded-lg transition {{ Route::is('learning.quiz') ? 'bg-violet-500 text-white font-bold' : 'text-gray-800 dark:text-gray-100 hover:bg-gray-200 dark:hover:bg-gray-700' }}"
               href="{{ route('learning.quiz') }}">
                <div class="flex items-center">
                   <svg class="shrink-0 fill-current {{ Route::is('learning.quiz') ? 'text-white' : 'text-gray-400 dark:text-gray-500' }}" width="16" height="16" viewBox="0 0 24 24">
    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 17h-2v-2h2v2zm2.07-7.75l-.9.92C13.45 12.9 13 13.5 13 15h-2v-.5c0-1.1.45-2.1 1.17-2.83l1.24-1.26c.37-.36.59-.86.59-1.41 0-1.1-.9-2-2-2s-2 .9-2 2H8c0-2.21 1.79-4 4-4s4 1.79 4 4c0 .88-.36 1.68-.93 2.25z"/>
</svg>
                    <span class="text-sm ml-2 sidebar-text duration-200">Self Assessment</span>
                </div>
            </a>
        </li>

        <li class="mb-0.5 last:mb-0" title="Messages">
            <a class="block pl-3 pr-3 py-2 rounded-lg transition {{ Route::is('teachers.messages*') ? 'bg-violet-500 text-white font-bold' : 'text-gray-800 dark:text-gray-100 hover:bg-gray-200 dark:hover:bg-gray-700' }}"
               href="{{ route('teachers.messages.index') }}">
                <div class="flex items-center">
                    <svg
                        class="shrink-0 fill-current {{ Route::is('admin.messages*') ? 'text-white' : 'text-gray-400 dark:text-gray-500' }}"
                        xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16">
                        <path
                            d="M0 4a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V4zm2-1a1 1 0 0 0-1 1v.217l7 4.2 7-4.2V4a1 1 0 0 0-1-1H2zm13 2.383-4.758 2.855L15 11.114v-5.73zm-.034 6.878L9.271 8.82 8 9.583 6.728 8.82l-5.694 3.44A1 1 0 0 0 2 13h12a1 1 0 0 0 .966-.739zM1 11.114l4.758-2.876L1 5.383v5.73z"/>
                    </svg>

                    <span class="text-sm ml-2 sidebar-text duration-200">Messages</span>
                </div>
            </a>
        </li>

        <li class="mb-0.5 last:mb-0" title="Research Assistant">
            <a class="block pl-3 pr-3 py-2 rounded-lg transition {{ Route::is('academic-chat*') ? 'bg-violet-500 text-white font-bold' : 'text-gray-800 dark:text-gray-100 hover:bg-gray-200 dark:hover:bg-gray-700' }}"
               href="{{ route('academic-chat.index') }}">
                <div class="flex items-center">
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

        <li class="mb-0.5 last:mb-0" title="Chat Groups">
            <a class="block pl-3 pr-3 py-2 rounded-lg transition {{ Route::is('chat*') ? 'bg-violet-500 text-white font-bold' : 'text-gray-800 dark:text-gray-100 hover:bg-gray-200 dark:hover:bg-gray-700' }}"
               href="{{ route('chat') }}">
                <div class="flex items-center">
                    <svg
                        class="shrink-0 fill-current {{ Route::is('chat*') ? 'text-white' : 'text-gray-400 dark:text-gray-500' }}"
                        xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16">
                        <path
                            d="M8 15c4.418 0 8-3.134 8-7s-3.582-7-8-7-8 3.134-8 7c0 1.76.743 3.37 1.97 4.6-.097 1.016-.417 2.13-.771 2.966-.079.186.074.394.273.362 2.256-.37 3.597-.938 4.18-1.234A9.06 9.06 0 0 0 8 15z"/>
                    </svg>

                    <span class="text-sm ml-2 sidebar-text duration-200">Chat Groups</span>
                </div>
            </a>
        </li>

        <!-- Performance -->
        <li class="mb-0.5 last:mb-0">
            <a class="block pl-3 pr-3 py-2 rounded-lg transition {{ Route::is('teachers.performance*') ? 'bg-violet-500 text-white font-bold' : 'text-gray-800 dark:text-gray-100 hover:bg-gray-200 dark:hover:bg-gray-700' }}"
               href="{{route('teachers.performance')}}">
                <div class="flex items-center">
                    <svg
                        class="shrink-0 fill-current {{ Route::is('teacher.performance*') ? 'text-white' : 'text-gray-400 dark:text-gray-500' }}"
                        width="16" height="16" viewBox="0 0 24 24">
                        <path d="M16 6l2.29 2.29-4.88 4.88-4-4L2 16.59 3.41 18l6-6 4 4 6.3-6.29L22 12V6z"/>
                    </svg>
                    <span class="text-sm ml-2 sidebar-text duration-200">Performance</span>
                </div>
            </a>
        </li>

        <!-- Report Cards -->
        <li class="mb-0.5 last:mb-0">
            <a class="block pl-3 pr-3 py-2 rounded-lg transition {{ Route::is('teachers.report-cards') ? 'bg-violet-500 text-white font-bold' : 'text-gray-800 dark:text-gray-100 hover:bg-gray-200 dark:hover:bg-gray-700' }}"
               href="{{route('teachers.report-cards')}}">
                <div class="flex items-center">
                    <svg
                        class="shrink-0 fill-current {{ Route::is('teachers.report-cards') ? 'text-white' : 'text-gray-400 dark:text-gray-500' }}"
                        width="16" height="16" viewBox="0 0 24 24">
                        <path d="M14 2H6c-1.1 0-2 .9-2 2v16c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V8l-6-6zm4 18H6V4h7v5h5v11zM8 15h8v2H8v-2zm0-3h8v2H8v-2zm0-3h5v2H8V9z"/>
                    </svg>
                    <span class="text-sm ml-2 sidebar-text duration-200">Report Cards</span>
                </div>
            </a>
        </li>

        <!-- Notifications -->
        <li class="mb-0.5 last:mb-0">
            <a class="block pl-3 pr-3 py-2 rounded-lg transition {{ Route::is('notifications*') ? 'bg-violet-500 text-white font-bold' : 'text-gray-800 dark:text-gray-100 hover:bg-gray-200 dark:hover:bg-gray-700' }}"
               href="{{route('notifications.index')}}">
                <div class="flex items-center">
                    <svg
                        class="shrink-0 fill-current {{ Route::is('notifications*') ? 'text-white' : 'text-gray-400 dark:text-gray-500' }}"
                        width="16" height="16" viewBox="0 0 24 24">
                        <path
                            d="M12 22c1.1 0 2-.9 2-2h-4c0 1.1.9 2 2 2zm6-6v-5c0-3.07-1.63-5.64-4.5-6.32V4c0-.83-.67-1.5-1.5-1.5s-1.5.67-1.5 1.5v.68C7.64 5.36 6 7.92 6 11v5l-2 2v1h16v-1l-2-2z"/>
                    </svg>
                    <span class="text-sm ml-2 sidebar-text duration-200">Notifications</span>
                </div>
            </a>
        </li>

        <li class="mb-0.5 last:mb-0">
            <a class="block pl-3 pr-3 py-2 rounded-lg transition {{ Route::is('teachers.classroom.*') ? 'bg-violet-500 text-white font-bold' : 'text-gray-800 dark:text-gray-100 hover:bg-gray-200 dark:hover:bg-gray-700' }}"
               href="{{route('teachers.classroom.index')}}">
                <div class="flex items-center">
                    <svg
                        class="shrink-0 fill-current {{ Route::is('teacher.classroom.*') ? 'text-white' : 'text-gray-400 dark:text-gray-500' }}"
                        width="16" height="16" viewBox="0 0 24 24">
                        <path
                            d="M21 3H3c-1.1 0-2 .9-2 2v11c0 1.1.9 2 2 2h6l-2 2v1h8v-1l-2-2h6c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 13H3V5h18v11z"/>
                        <circle cx="8" cy="10" r="2"/>
                        <circle cx="16" cy="10" r="2"/>
                        <path
                            d="M8 13c-2.21 0-4 1.79-4 4h8c0-2.21-1.79-4-4-4zM16 13c-2.21 0-4 1.79-4 4h8c0-2.21-1.79-4-4-4z"/>
                    </svg>
                    <span class="text-sm ml-2 sidebar-text duration-200">Classroom</span>
                </div>
            </a>
        </li>

        <!-- Activities -->
        <li class="mb-0.5 last:mb-0 hidden">
            <a class="block pl-3 pr-3 py-2 rounded-lg transition {{ Route::is('teachers.activities.*') ? 'bg-violet-500 text-white font-bold' : 'text-gray-800 dark:text-gray-100 hover:bg-gray-200 dark:hover:bg-gray-700' }}"
               href="{{route('teachers.activities')}}">
                <div class="flex items-center">
                    <svg
                        class="shrink-0 fill-current {{ Route::is('teachers.activities.*') ? 'text-white' : 'text-gray-400 dark:text-gray-500' }}"
                        width="16" height="16" viewBox="0 0 24 24">
                        <path
                            d="M13 3c-4.97 0-9 4.03-9 9H1l3.89 3.89.07.14L9 12H6c0-3.87 3.13-7 7-7s7 3.13 7 7-3.13 7-7 7c-1.93 0-3.68-.79-4.94-2.06l-1.42 1.42C8.27 19.99 10.51 21 13 21c4.97 0 9-4.03 9-9s-4.03-9-9-9zm-1 5v5l4.28 2.54.72-1.21-3.5-2.08V8H12z"/>
                    </svg>
                    <span class="text-sm ml-2 sidebar-text duration-200">Activities</span>
                </div>
            </a>
        </li>

        <!-- Profile -->
        <li class="mb-0.5 last:mb-0">
            <a class="block pl-3 pr-3 py-2 rounded-lg transition {{ Route::is('teachers.account*') ? 'bg-violet-500 text-white font-bold' : 'text-gray-800 dark:text-gray-100 hover:bg-gray-200 dark:hover:bg-gray-700' }}"
               href="{{route('teachers.account')}}">
                <div class="flex items-center">
                    <svg
                        class="shrink-0 fill-current {{ Route::is('teachers.account*') ? 'text-white' : 'text-gray-400 dark:text-gray-500' }}"
                        width="16" height="16" viewBox="0 0 24 24">
                        <path
                            d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                    </svg>
                    <span class="text-sm ml-2 sidebar-text duration-200">Profile</span>
                </div>
            </a>
        </li>

        <!-- Course Management Divider -->
        <li class="my-4 border-t border-gray-200 dark:border-gray-700"></li>
        <h3 class="text-xs uppercase text-gray-400 dark:text-gray-500 font-semibold pl-3 mb-2">
            <span class="lg:hidden lg:sidebar-expanded:block 2xl:block">LMS Management</span>
        </h3>

        <!-- Course Management -->
        @can('create', App\Models\Lms\Course::class)
            <li class="mb-0.5 last:mb-0" title="Course Management">
                <a class="block pl-3 pr-3 py-2 rounded-lg transition {{ Route::is('course-management.*') ? 'bg-violet-500 text-white font-bold' : 'text-gray-800 dark:text-gray-100 hover:bg-gray-200 dark:hover:bg-gray-700' }}"
                   href="{{ route('course-management.index') }}">
                    <div class="flex items-center">
                        <svg class="shrink-0 fill-current {{ Route::is('course-management.*') ? 'text-white' : 'text-gray-400 dark:text-gray-500' }}"
                             xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16">
                            <path d="M11 0H5a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2zM5 1h6a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1z"/>
                            <path d="M6.5 4h3a.5.5 0 0 1 0 1h-3a.5.5 0 0 1 0-1zm0 2h3a.5.5 0 0 1 0 1h-3a.5.5 0 0 1 0-1zm0 2h3a.5.5 0 0 1 0 1h-3a.5.5 0 0 1 0-1zm0 2h3a.5.5 0 0 1 0 1h-3a.5.5 0 0 1 0-1z"/>
                        </svg>
                        <span class="text-sm ml-2 sidebar-text duration-200">Course Management</span>
                    </div>
                </a>
            </li>
        @endcan
    </ul>
</div>
