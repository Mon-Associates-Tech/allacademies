{{-- resources/views/partials/navigation/sidebar.blade.php --}}
@php use App\Enums\UserRole; @endphp

<div class="space-y-6" x-data="{ sidebarExpanded: $store.sidebar.expanded }">

    {{-- ═══════════════════════════════════════════════════════════
        SECTION: Dashboard & Overview
    ═══════════════════════════════════════════════════════════ --}}
    <div>
        <h3 class="text-xs uppercase text-gray-400 dark:text-gray-500 font-semibold pl-3 mb-2">
            <span class="hidden lg:block lg:sidebar-expanded:hidden 2xl:hidden text-center w-6" aria-hidden="true">•••</span>
            <span class="lg:hidden lg:sidebar-expanded:block 2xl:block">Overview</span>
        </h3>
        <ul class="space-y-0.5">
            <li>
                <a :class="sidebarExpanded ? 'py-2' : ''"
                   class="block pl-3 rounded-lg transition {{ Route::is('dashboard') ? 'bg-violet-500 text-white font-bold' : 'text-gray-800 dark:text-gray-100 hover:bg-gray-200 dark:hover:bg-gray-700' }}"
                   href="{{ route('dashboard') }}">
                    <div class="flex items-center">
                        <x-heroicon-o-home class="shrink-0 w-4 h-4 {{ Route::is('dashboard') ? 'text-white' : 'text-gray-400 dark:text-gray-500' }}" />
                        <span class="text-sm ml-2 sidebar-text duration-200">Dashboard</span>
                    </div>
                </a>
            </li>

            @if(auth()->user()->role === UserRole::OWNER)
                <li>
                    <a :class="sidebarExpanded ? 'py-2' : ''"
                       class="block pl-3 rounded-lg transition {{ Route::is('admin.school-switcher') ? 'bg-violet-500 text-white font-bold' : 'text-gray-800 dark:text-gray-100 hover:bg-gray-200 dark:hover:bg-gray-700' }}"
                       href="{{ route('admin.school-switcher') }}">
                        <div class="flex items-center">
                            <x-heroicon-o-building-office-2 class="shrink-0 w-4 h-4 {{ Route::is('admin.school-switcher') ? 'text-white' : 'text-gray-400 dark:text-gray-500' }}" />
                            <span class="text-sm ml-2 sidebar-text duration-200">School Switcher</span>
                        </div>
                    </a>
                </li>
            @endif
        </ul>
    </div>

    {{-- ═══════════════════════════════════════════════════════════
        SECTION: User & Role Management
    ═══════════════════════════════════════════════════════════ --}}
    <div>
        <h3 class="text-xs uppercase text-gray-400 dark:text-gray-500 font-semibold pl-3 mb-2">
            <span class="hidden lg:block lg:sidebar-expanded:hidden 2xl:hidden text-center w-6" aria-hidden="true">•••</span>
            <span class="lg:hidden lg:sidebar-expanded:block 2xl:block">People</span>
        </h3>
        <ul class="space-y-0.5">
            <li>
                <a :class="sidebarExpanded ? 'py-2' : ''"
                   class="block pl-3 rounded-lg transition {{ Route::is('users.index') ? 'bg-violet-500 text-white font-bold' : 'text-gray-800 dark:text-gray-100 hover:bg-gray-200 dark:hover:bg-gray-700' }}"
                   href="{{ route('users.index') }}">
                    <div class="flex items-center">
                        <x-heroicon-o-users class="shrink-0 w-4 h-4 {{ Route::is('users.index') ? 'text-white' : 'text-gray-400 dark:text-gray-500' }}" />
                        <span class="text-sm ml-2 sidebar-text duration-200">User Management</span>
                    </div>
                </a>
            </li>

            @if(auth()->user()->role === UserRole::OWNER)
                <li>
                    <a :class="sidebarExpanded ? 'py-2' : ''"
                       class="block pl-3 rounded-lg transition {{ Route::is('admin.student-management') ? 'bg-violet-500 text-white font-bold' : 'text-gray-800 dark:text-gray-100 hover:bg-gray-200 dark:hover:bg-gray-700' }}"
                       href="{{ route('admin.student-management') }}">
                        <div class="flex items-center">
                            <x-heroicon-o-academic-cap class="shrink-0 w-4 h-4 {{ Route::is('admin.student-management') ? 'text-white' : 'text-gray-400 dark:text-gray-500' }}" />
                            <span class="text-sm ml-2 sidebar-text duration-200">Students</span>
                        </div>
                    </a>
                </li>

                <li>
                    <a :class="sidebarExpanded ? 'py-2' : ''"
                       class="block pl-3 rounded-lg transition {{ Route::is('admin.teacher-management') ? 'bg-violet-500 text-white font-bold' : 'text-gray-800 dark:text-gray-100 hover:bg-gray-200 dark:hover:bg-gray-700' }}"
                       href="{{ route('admin.teacher-management') }}">
                        <div class="flex items-center">
                            <x-heroicon-o-user-group class="shrink-0 w-4 h-4 {{ Route::is('admin.teacher-management') ? 'text-white' : 'text-gray-400 dark:text-gray-500' }}" />
                            <span class="text-sm ml-2 sidebar-text duration-200">Teachers</span>
                        </div>
                    </a>
                </li>

                <li>
                    <a :class="sidebarExpanded ? 'py-2' : ''"
                       class="block pl-3 rounded-lg transition {{ Route::is('admin.parent-management') ? 'bg-violet-500 text-white font-bold' : 'text-gray-800 dark:text-gray-100 hover:bg-gray-200 dark:hover:bg-gray-700' }}"
                       href="{{ route('admin.parent-management') }}">
                        <div class="flex items-center">
                            <x-heroicon-o-users class="shrink-0 w-4 h-4 {{ Route::is('admin.parent-management') ? 'text-white' : 'text-gray-400 dark:text-gray-500' }}" />
                            <span class="text-sm ml-2 sidebar-text duration-200">Parents</span>
                        </div>
                    </a>
                </li>

                <li>
                    <a :class="sidebarExpanded ? 'py-2' : ''"
                       class="block pl-3 rounded-lg transition {{ Route::is('admin.librarian-management') ? 'bg-violet-500 text-white font-bold' : 'text-gray-800 dark:text-gray-100 hover:bg-gray-200 dark:hover:bg-gray-700' }}"
                       href="{{ route('admin.librarian-management') }}">
                        <div class="flex items-center">
                            <x-heroicon-o-book-open class="shrink-0 w-4 h-4 {{ Route::is('admin.librarian-management') ? 'text-white' : 'text-gray-400 dark:text-gray-500' }}" />
                            <span class="text-sm ml-2 sidebar-text duration-200">Librarians</span>
                        </div>
                    </a>
                </li>

                <li>
                    <a :class="sidebarExpanded ? 'py-2' : ''"
                       class="block pl-3 rounded-lg transition {{ Route::is('admin.accountant-management') ? 'bg-violet-500 text-white font-bold' : 'text-gray-800 dark:text-gray-100 hover:bg-gray-200 dark:hover:bg-gray-700' }}"
                       href="{{ route('admin.accountant-management') }}">
                        <div class="flex items-center">
                            <x-heroicon-o-calculator class="shrink-0 w-4 h-4 {{ Route::is('admin.accountant-management') ? 'text-white' : 'text-gray-400 dark:text-gray-500' }}" />
                            <span class="text-sm ml-2 sidebar-text duration-200">Accountants</span>
                        </div>
                    </a>
                </li>

                <li>
                    <a :class="sidebarExpanded ? 'py-2' : ''"
                       class="block pl-3 rounded-lg transition {{ Route::is('admin.author-management') ? 'bg-violet-500 text-white font-bold' : 'text-gray-800 dark:text-gray-100 hover:bg-gray-200 dark:hover:bg-gray-700' }}"
                       href="{{ route('admin.author-management') }}">
                        <div class="flex items-center">
                            <x-heroicon-o-pencil-square class="shrink-0 w-4 h-4 {{ Route::is('admin.author-management') ? 'text-white' : 'text-gray-400 dark:text-gray-500' }}" />
                            <span class="text-sm ml-2 sidebar-text duration-200">Authors</span>
                        </div>
                    </a>
                </li>
            @endif

            {{-- Student Groups --}}
            <li>
                <a :class="sidebarExpanded ? 'py-2' : ''"
                   class="block pl-3 rounded-lg transition {{ Route::is('admin.student-groups') ? 'bg-violet-500 text-white font-bold' : 'text-gray-800 dark:text-gray-100 hover:bg-gray-200 dark:hover:bg-gray-700' }}"
                   href="{{ route('admin.student-groups') }}">
                    <div class="flex items-center">
                        <x-heroicon-o-users class="shrink-0 w-4 h-4 {{ Route::is('admin.student-groups') ? 'text-white' : 'text-gray-400 dark:text-gray-500' }}" />
                        <span class="text-sm ml-2 sidebar-text duration-200">Student Groups</span>
                    </div>
                </a>
            </li>

            {{-- Impersonation (hidden by default) --}}
            <li class="hidden">
                <a :class="sidebarExpanded ? 'py-2' : ''"
                   class="block pl-3 rounded-lg transition {{ Route::is('admin.users.impersonate') ? 'bg-violet-500 text-white font-bold' : 'text-gray-800 dark:text-gray-100 hover:bg-gray-200 dark:hover:bg-gray-700' }}"
                   href="{{ route('admin.users.impersonate') }}">
                    <div class="flex items-center">
                        <x-heroicon-o-user-circle class="shrink-0 w-4 h-4 {{ Route::is('admin.users.impersonate') ? 'text-white' : 'text-gray-400 dark:text-gray-500' }}" />
                        <span class="text-sm ml-2 sidebar-text duration-200">Impersonate User</span>
                    </div>
                </a>
            </li>
        </ul>
    </div>

    {{-- ═══════════════════════════════════════════════════════════
        SECTION: Academic & Content Management
    ═══════════════════════════════════════════════════════════ --}}
    <div>
        <h3 class="text-xs uppercase text-gray-400 dark:text-gray-500 font-semibold pl-3 mb-2">
            <span class="hidden lg:block lg:sidebar-expanded:hidden 2xl:hidden text-center w-6" aria-hidden="true">•••</span>
            <span class="lg:hidden lg:sidebar-expanded:block 2xl:block">Academics</span>
        </h3>
        <ul class="space-y-0.5">
            @if(auth()->user()->role === UserRole::OWNER)
                <li>
                    <a :class="sidebarExpanded ? 'py-2' : ''"
                       class="block pl-3 rounded-lg transition {{ Route::is('academic-groups.index') ? 'bg-violet-500 text-white font-bold' : 'text-gray-800 dark:text-gray-100 hover:bg-gray-200 dark:hover:bg-gray-700' }}"
                       href="{{ route('academic-groups.index') }}">
                        <div class="flex items-center">
                            <x-heroicon-o-book-open class="shrink-0 w-4 h-4 {{ Route::is('academic-groups.index') ? 'text-white' : 'text-gray-400 dark:text-gray-500' }}" />
                            <span class="text-sm ml-2 sidebar-text duration-200">Academic Structure</span>
                        </div>
                    </a>
                </li>

                <li>
                    <a :class="sidebarExpanded ? 'py-2' : ''"
                       class="block pl-3 rounded-lg transition {{ Route::is('admin.subject-management') ? 'bg-violet-500 text-white font-bold' : 'text-gray-800 dark:text-gray-100 hover:bg-gray-200 dark:hover:bg-gray-700' }}"
                       href="{{ route('admin.subject-management') }}">
                        <div class="flex items-center">
                            <x-heroicon-o-list-bullet class="shrink-0 w-4 h-4 {{ Route::is('admin.subject-management') ? 'text-white' : 'text-gray-400 dark:text-gray-500' }}" />
                            <span class="text-sm ml-2 sidebar-text duration-200">Subjects</span>
                        </div>
                    </a>
                </li>

                <li>
                    <a :class="sidebarExpanded ? 'py-2' : ''"
                       class="block pl-3 rounded-lg transition {{ Route::is('admin.question-availability') ? 'bg-violet-500 text-white font-bold' : 'text-gray-800 dark:text-gray-100 hover:bg-gray-200 dark:hover:bg-gray-700' }}"
                       href="{{ route('admin.question-availability') }}">
                        <div class="flex items-center">
                            <x-heroicon-o-magnifying-glass class="shrink-0 w-4 h-4 {{ Route::is('admin.question-availability') ? 'text-white' : 'text-gray-400 dark:text-gray-500' }}" />
                            <span class="text-sm ml-2 sidebar-text duration-200">Question Bank</span>
                        </div>
                    </a>
                </li>
            @endif

            {{-- Report Cards --}}
            <li>
                <a :class="sidebarExpanded ? 'py-2' : ''"
                   class="block pl-3 rounded-lg transition {{ Route::is('admin.report-cards') ? 'bg-violet-500 text-white font-bold' : 'text-gray-800 dark:text-gray-100 hover:bg-gray-200 dark:hover:bg-gray-700' }}"
                   href="{{ route('admin.report-cards') }}">
                    <div class="flex items-center">
                        <x-heroicon-o-document-text class="shrink-0 w-4 h-4 {{ Route::is('admin.report-cards') ? 'text-white' : 'text-gray-400 dark:text-gray-500' }}" />
                        <span class="text-sm ml-2 sidebar-text duration-200">Report Cards</span>
                    </div>
                </a>
            </li>
        </ul>
    </div>

    {{-- ═══════════════════════════════════════════════════════════
        SECTION: Library & Books
    ═══════════════════════════════════════════════════════════ --}}
    @if(auth()->user()->role === UserRole::OWNER)
        <div>
            <h3 class="text-xs uppercase text-gray-400 dark:text-gray-500 font-semibold pl-3 mb-2">
                <span class="hidden lg:block lg:sidebar-expanded:hidden 2xl:hidden text-center w-6" aria-hidden="true">•••</span>
                <span class="lg:hidden lg:sidebar-expanded:block 2xl:block">Library</span>
            </h3>
            <ul class="space-y-0.5">
                <li>
                    <a :class="sidebarExpanded ? 'py-2' : ''"
                       class="block pl-3 rounded-lg transition {{ Route::is('admin.book-management') ? 'bg-violet-500 text-white font-bold' : 'text-gray-800 dark:text-gray-100 hover:bg-gray-200 dark:hover:bg-gray-700' }}"
                       href="{{ route('admin.book-management') }}">
                        <div class="flex items-center">
                            <x-heroicon-c-bookmark class="shrink-0 w-4 h-4 {{ Route::is('admin.book-management') ? 'text-white' : 'text-gray-400 dark:text-gray-500' }}" />
                            <span class="text-sm ml-2 sidebar-text duration-200">Book Catalog</span>
                        </div>
                    </a>
                </li>

                <li>
                    <a :class="sidebarExpanded ? 'py-2' : ''"
                       class="block pl-3 rounded-lg transition {{ Route::is('admin.book-approvals') ? 'bg-violet-500 text-white font-bold' : 'text-gray-800 dark:text-gray-100 hover:bg-gray-200 dark:hover:bg-gray-700' }}"
                       href="{{ route('admin.book-approvals') }}">
                        <div class="flex items-center">
                            <x-heroicon-o-check-circle class="shrink-0 w-4 h-4 {{ Route::is('admin.book-approvals') ? 'text-white' : 'text-gray-400 dark:text-gray-500' }}" />
                            <span class="text-sm ml-2 sidebar-text duration-200">Book Approvals</span>
                        </div>
                    </a>
                </li>
            </ul>
        </div>
    @endif

    {{-- ═══════════════════════════════════════════════════════════
        SECTION: Examinations & Assessments
    ═══════════════════════════════════════════════════════════ --}}
    <div>
        <h3 class="text-xs uppercase text-gray-400 dark:text-gray-500 font-semibold pl-3 mb-2">
            <span class="hidden lg:block lg:sidebar-expanded:hidden 2xl:hidden text-center w-6" aria-hidden="true">•••</span>
            <span class="lg:hidden lg:sidebar-expanded:block 2xl:block">Examinations</span>
        </h3>
        <ul class="space-y-0.5">
            {{-- General Exams Group --}}
            <li>
                <a :class="sidebarExpanded ? 'py-2' : ''"
                   class="block pl-3 rounded-lg transition {{ Route::is('teachers.general-exams.index') ? 'bg-violet-500 text-white font-bold' : 'text-gray-800 dark:text-gray-100 hover:bg-gray-200 dark:hover:bg-gray-700' }}"
                   href="{{ route('teachers.general-exams.index') }}">
                    <div class="flex items-center">
                        <x-heroicon-o-document class="shrink-0 w-4 h-4 {{ Route::is('teachers.general-exams.index') ? 'text-white' : 'text-gray-400 dark:text-gray-500' }}" />
                        <span class="text-sm ml-2 sidebar-text duration-200">Manage Exams</span>
                    </div>
                </a>
            </li>

            <li class="hidden">
                <a :class="sidebarExpanded ? 'py-2' : ''"
                   class="block pl-3 rounded-lg transition {{ Route::is('teachers.general-exams.create') ? 'bg-violet-500 text-white font-bold' : 'text-gray-800 dark:text-gray-100 hover:bg-gray-200 dark:hover:bg-gray-700' }}"
                   href="{{ route('teachers.general-exams.create') }}">
                    <div class="flex items-center">
                        <x-heroicon-o-plus-circle class="shrink-0 w-4 h-4 {{ Route::is('teachers.general-exams.create') ? 'text-white' : 'text-gray-400 dark:text-gray-500' }}" />
                        <span class="text-sm ml-2 sidebar-text duration-200">Create Exam</span>
                    </div>
                </a>
            </li>

            <li>
                <a :class="sidebarExpanded ? 'py-2' : ''"
                   class="block pl-3 rounded-lg transition {{ Route::is('general-exams.subscription.dashboard') ? 'bg-violet-500 text-white font-bold' : 'text-gray-800 dark:text-gray-100 hover:bg-gray-200 dark:hover:bg-gray-700' }}"
                   href="{{ route('general-exams.subscription.dashboard') }}">
                    <div class="flex items-center">
                        <x-heroicon-o-ticket class="shrink-0 w-4 h-4 {{ Route::is('general-exams.subscription.dashboard') ? 'text-white' : 'text-gray-400 dark:text-gray-500' }}" />
                        <span class="text-sm ml-2 sidebar-text duration-200">Exam Subscriptions</span>
                    </div>
                </a>
            </li>

            @if(auth()->user()->role === UserRole::OWNER)
                <li>
                    <a :class="sidebarExpanded ? 'py-2' : ''"
                       class="block pl-3 rounded-lg transition {{ Route::is('admin.generate-examination') ? 'bg-violet-500 text-white font-bold' : 'text-gray-800 dark:text-gray-100 hover:bg-gray-200 dark:hover:bg-gray-700' }}"
                       href="{{ route('admin.generate-examination') }}">
                        <div class="flex items-center">
                            <x-heroicon-o-sparkles class="shrink-0 w-4 h-4 {{ Route::is('admin.generate-examination') ? 'text-white' : 'text-gray-400 dark:text-gray-500' }}" />
                            <span class="text-sm ml-2 sidebar-text duration-200">AI Exam Generator</span>
                        </div>
                    </a>
                </li>
            @endif
        </ul>
    </div>

    {{-- ═══════════════════════════════════════════════════════════
        SECTION: Finance & Payments
    ═══════════════════════════════════════════════════════════ --}}
    <div>
        <h3 class="text-xs uppercase text-gray-400 dark:text-gray-500 font-semibold pl-3 mb-2">
            <span class="hidden lg:block lg:sidebar-expanded:hidden 2xl:hidden text-center w-6" aria-hidden="true">•••</span>
            <span class="lg:hidden lg:sidebar-expanded:block 2xl:block">Finance</span>
        </h3>
        <ul class="space-y-0.5">
            <li>
                <a :class="sidebarExpanded ? 'py-2' : ''"
                   class="block pl-3 rounded-lg transition {{ Route::is('admin.payments*') ? 'bg-violet-500 text-white font-bold' : 'text-gray-800 dark:text-gray-100 hover:bg-gray-200 dark:hover:bg-gray-700' }}"
                   href="{{ route('admin.payments.index') }}">
                    <div class="flex items-center">
                        <x-heroicon-o-credit-card class="shrink-0 w-4 h-4 {{ Route::is('admin.payments*') ? 'text-white' : 'text-gray-400 dark:text-gray-500' }}" />
                        <span class="text-sm ml-2 sidebar-text duration-200">Payments</span>
                    </div>
                </a>
            </li>

            <li>
                <a :class="sidebarExpanded ? 'py-2' : ''"
                   class="block pl-3 rounded-lg transition {{ Route::is('admin.transactions*') ? 'bg-violet-500 text-white font-bold' : 'text-gray-800 dark:text-gray-100 hover:bg-gray-200 dark:hover:bg-gray-700' }}"
                   href="{{ route('admin.transactions.index') }}">
                    <div class="flex items-center">
                        <x-heroicon-o-arrow-trending-up class="shrink-0 w-4 h-4 {{ Route::is('admin.transactions*') ? 'text-white' : 'text-gray-400 dark:text-gray-500' }}" />
                        <span class="text-sm ml-2 sidebar-text duration-200">Transactions</span>
                    </div>
                </a>
            </li>

            @if(auth()->user()->role === UserRole::OWNER)
                <li>
                    <a :class="sidebarExpanded ? 'py-2' : ''"
                       class="block pl-3 rounded-lg transition {{ Route::is('token-allocations*') ? 'bg-violet-500 text-white font-bold' : 'text-gray-800 dark:text-gray-100 hover:bg-gray-200 dark:hover:bg-gray-700' }}"
                       href="{{ route('token-allocations.index') }}">
                        <div class="flex items-center">
                            <x-heroicon-o-ticket class="shrink-0 w-4 h-4 {{ Route::is('token-allocations*') ? 'text-white' : 'text-gray-400 dark:text-gray-500' }}" />
                            <span class="text-sm ml-2 sidebar-text duration-200">Token Allocations</span>
                        </div>
                    </a>
                </li>

                <li>
                    <a :class="sidebarExpanded ? 'py-2' : ''"
                       class="block pl-3 rounded-lg transition {{ Route::is('admin.messenger-transactions*') ? 'bg-violet-500 text-white font-bold' : 'text-gray-800 dark:text-gray-100 hover:bg-gray-200 dark:hover:bg-gray-700' }}"
                       href="{{ route('admin.messenger-transactions.index') }}">
                        <div class="flex items-center">
                            <x-heroicon-o-arrows-right-left class="shrink-0 w-4 h-4 {{ Route::is('admin.messenger-transactions*') ? 'text-white' : 'text-gray-400 dark:text-gray-500' }}" />
                            <span class="text-sm ml-2 sidebar-text duration-200">Messenger Transactions</span>
                        </div>
                    </a>
                </li>
            @endif
        </ul>
    </div>

    {{-- ═══════════════════════════════════════════════════════════
        SECTION: Communication
    ═══════════════════════════════════════════════════════════ --}}
    <div>
        <h3 class="text-xs uppercase text-gray-400 dark:text-gray-500 font-semibold pl-3 mb-2">
            <span class="hidden lg:block lg:sidebar-expanded:hidden 2xl:hidden text-center w-6" aria-hidden="true">•••</span>
            <span class="lg:hidden lg:sidebar-expanded:block 2xl:block">Communication</span>
        </h3>
        <ul class="space-y-0.5">
            <li>
                <a :class="sidebarExpanded ? 'py-2' : ''"
                   class="block pl-3 rounded-lg transition {{ Route::is('notifications*') ? 'bg-violet-500 text-white font-bold' : 'text-gray-800 dark:text-gray-100 hover:bg-gray-200 dark:hover:bg-gray-700' }}"
                   href="{{ route('notifications.index') }}">
                    <div class="flex items-center">
                        <x-heroicon-o-bell class="shrink-0 w-4 h-4 {{ Route::is('notifications*') ? 'text-white' : 'text-gray-400 dark:text-gray-500' }}" />
                        <span class="text-sm ml-2 sidebar-text duration-200">Notifications</span>
                    </div>
                </a>
            </li>

            <li>
                <a :class="sidebarExpanded ? 'py-2' : ''"
                   class="block pl-3 rounded-lg transition {{ Route::is('chat*') ? 'bg-violet-500 text-white font-bold' : 'text-gray-800 dark:text-gray-100 hover:bg-gray-200 dark:hover:bg-gray-700' }}"
                   href="{{ route('chat') }}">
                    <div class="flex items-center">
                        <x-heroicon-o-chat-bubble-left-right class="shrink-0 w-4 h-4 {{ Route::is('chat*') ? 'text-white' : 'text-gray-400 dark:text-gray-500' }}" />
                        <span class="text-sm ml-2 sidebar-text duration-200">Group Chat</span>
                    </div>
                </a>
            </li>

            <li>
                <a :class="sidebarExpanded ? 'py-2' : ''"
                   class="block pl-3 rounded-lg transition {{ Route::is('academic-chat*') ? 'bg-violet-500 text-white font-bold' : 'text-gray-800 dark:text-gray-100 hover:bg-gray-200 dark:hover:bg-gray-700' }}"
                   href="{{ route('research-assistant.index') }}">
                    <div class="flex items-center">
                        <x-heroicon-o-sparkles class="shrink-0 w-4 h-4 {{ Route::is('academic-chat*') ? 'text-white' : 'text-gray-400 dark:text-gray-500' }}" />
                        <span class="text-sm ml-2 sidebar-text duration-200">Research Assistant</span>
                    </div>
                </a>
            </li>

            <li>
                <a :class="sidebarExpanded ? 'py-2' : ''"
                   class="block pl-3 rounded-lg transition {{ Route::is('forums*') ? 'bg-violet-500 text-white font-bold' : 'text-gray-800 dark:text-gray-100 hover:bg-gray-200 dark:hover:bg-gray-700' }}"
                   href="{{ route('forums') }}">
                    <div class="flex items-center">
                        <x-heroicon-o-chat-bubble-left-ellipsis class="shrink-0 w-4 h-4 {{ Route::is('forums*') ? 'text-white' : 'text-gray-400 dark:text-gray-500' }}" />
                        <span class="text-sm ml-2 sidebar-text duration-200">Discussion Forums</span>
                    </div>
                </a>
            </li>

            {{-- Hidden: Message Center --}}
            <li class="hidden">
                <a :class="sidebarExpanded ? 'py-2' : ''"
                   class="block pl-3 rounded-lg transition {{ Route::is('admin.messages*') ? 'bg-violet-500 text-white font-bold' : 'text-gray-800 dark:text-gray-100 hover:bg-gray-200 dark:hover:bg-gray-700' }}"
                   href="{{ route('admin.messages.index') }}">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <x-heroicon-o-envelope class="shrink-0 w-4 h-4 {{ Route::is('admin.messages*') ? 'text-white' : 'text-gray-400 dark:text-gray-500' }}" />
                            <span class="text-sm ml-2 sidebar-text duration-200">Message Center</span>
                        </div>
                        <x-heroicon-o-chevron-right class="shrink-0 w-4 h-4 text-gray-400 dark:text-gray-500" />
                    </div>
                </a>
            </li>
        </ul>
    </div>

    {{-- ═══════════════════════════════════════════════════════════
        SECTION: Settings & Configuration
    ═══════════════════════════════════════════════════════════ --}}
    <div>
        <h3 class="text-xs uppercase text-gray-400 dark:text-gray-500 font-semibold pl-3 mb-2">
            <span class="hidden lg:block lg:sidebar-expanded:hidden 2xl:hidden text-center w-6" aria-hidden="true">•••</span>
            <span class="lg:hidden lg:sidebar-expanded:block 2xl:block">Settings</span>
        </h3>
        <ul class="space-y-0.5">
            <li>
                <a :class="sidebarExpanded ? 'py-2' : ''"
                   class="block pl-3 rounded-lg transition {{ Route::is('school-settings*') ? 'bg-violet-500 text-white font-bold' : 'text-gray-800 dark:text-gray-100 hover:bg-gray-200 dark:hover:bg-gray-700' }}"
                   href="{{ route('school-settings.index') }}">
                    <div class="flex items-center">
                        <x-heroicon-o-cog-6-tooth class="shrink-0 w-4 h-4 {{ Route::is('school-settings*') ? 'text-white' : 'text-gray-400 dark:text-gray-500' }}" />
                        <span class="text-sm ml-2 sidebar-text duration-200">Academic Settings</span>
                    </div>
                </a>
            </li>

            <li>
                <a :class="sidebarExpanded ? 'py-2' : ''"
                   class="block pl-3 rounded-lg transition {{ Route::is('admin.settings.certificate-templates') ? 'bg-violet-500 text-white font-bold' : 'text-gray-800 dark:text-gray-100 hover:bg-gray-200 dark:hover:bg-gray-700' }}"
                   href="{{ route('admin.settings.certificate-templates') }}">
                    <div class="flex items-center">
                        <x-heroicon-o-document-duplicate class="shrink-0 w-4 h-4 {{ Route::is('admin.settings.certificate-templates') ? 'text-white' : 'text-gray-400 dark:text-gray-500' }}" />
                        <span class="text-sm ml-2 sidebar-text duration-200">Certificate Templates</span>
                    </div>
                </a>
            </li>

            {{-- Hidden: ID Card Templates --}}
            <li class="hidden">
                <a :class="sidebarExpanded ? 'py-2' : ''"
                   class="block pl-3 rounded-lg transition {{ Route::is('admin.settings.id-card-templates') ? 'bg-violet-500 text-white font-bold' : 'text-gray-800 dark:text-gray-100 hover:bg-gray-200 dark:hover:bg-gray-700' }}"
                   href="{{ route('admin.settings.id-card-templates') }}">
                    <div class="flex items-center">
                        <x-heroicon-o-identification class="shrink-0 w-4 h-4 {{ Route::is('admin.settings.id-card-templates') ? 'text-white' : 'text-gray-400 dark:text-gray-500' }}" />
                        <span class="text-sm ml-2 sidebar-text duration-200">ID Card Templates</span>
                    </div>
                </a>
            </li>

            <li>
                <a :class="sidebarExpanded ? 'py-2' : ''"
                   class="block pl-3 rounded-lg transition {{ Route::is('onboarding.*') ? 'bg-violet-500 text-white font-bold' : 'text-gray-800 dark:text-gray-100 hover:bg-gray-200 dark:hover:bg-gray-700' }}"
                   href="{{ route('onboarding.school-setup') }}">
                    <div class="flex items-center">
                        <x-heroicon-o-wrench-screwdriver class="shrink-0 w-4 h-4 {{ Route::is('onboarding.*') ? 'text-white' : 'text-gray-400 dark:text-gray-500' }}" />
                        <span class="text-sm ml-2 sidebar-text duration-200">School Setup</span>
                    </div>
                </a>
            </li>
        </ul>
    </div>

    {{-- ═══════════════════════════════════════════════════════════
        SECTION: Owner Tools (Admin Only)
    ═══════════════════════════════════════════════════════════ --}}
    @if(auth()->user()->role === UserRole::OWNER)
        <div>
            <h3 class="text-xs uppercase text-gray-400 dark:text-gray-500 font-semibold pl-3 mb-2">
                <span class="hidden lg:block lg:sidebar-expanded:hidden 2xl:hidden text-center w-6" aria-hidden="true">•••</span>
                <span class="lg:hidden lg:sidebar-expanded:block 2xl:block">Owner Tools</span>
            </h3>
            <ul class="space-y-0.5">
                <li>
                    <a :class="sidebarExpanded ? 'py-2' : ''"
                       class="block pl-3 rounded-lg transition {{ Route::is('admin.pricing-settings.*') ? 'bg-violet-500 text-white font-bold' : 'text-gray-800 dark:text-gray-100 hover:bg-gray-200 dark:hover:bg-gray-700' }}"
                       href="{{ route('admin.pricing-settings.edit') }}">
                        <div class="flex items-center">
                            <x-heroicon-o-currency-dollar class="shrink-0 w-4 h-4 {{ Route::is('admin.pricing-settings.*') ? 'text-white' : 'text-gray-400 dark:text-gray-500' }}" />
                            <span class="text-sm ml-2 sidebar-text duration-200">Pricing Settings</span>
                        </div>
                    </a>
                </li>

                <li>
                    <a :class="sidebarExpanded ? 'py-2' : ''"
                       class="block pl-3 rounded-lg transition {{ Route::is('admin.general-exams.subscriptions') ? 'bg-violet-500 text-white font-bold' : 'text-gray-800 dark:text-gray-100 hover:bg-gray-200 dark:hover:bg-gray-700' }}"
                       href="{{ route('admin.general-exams.subscriptions') }}">
                        <div class="flex items-center">
                            <x-heroicon-o-ticket class="shrink-0 w-4 h-4 {{ Route::is('admin.general-exams.subscriptions') ? 'text-white' : 'text-gray-400 dark:text-gray-500' }}" />
                            <span class="text-sm ml-2 sidebar-text duration-200">Exam Subscriptions</span>
                        </div>
                    </a>
                </li>

                <li>
                    <a :class="sidebarExpanded ? 'py-2' : ''"
                       class="block pl-3 rounded-lg transition {{ Route::is('admin.general-exams.pricing-tiers') ? 'bg-violet-500 text-white font-bold' : 'text-gray-800 dark:text-gray-100 hover:bg-gray-200 dark:hover:bg-gray-700' }}"
                       href="{{ route('admin.general-exams.pricing-tiers') }}">
                        <div class="flex items-center">
                            <x-heroicon-o-tag class="shrink-0 w-4 h-4 {{ Route::is('admin.general-exams.pricing-tiers') ? 'text-white' : 'text-gray-400 dark:text-gray-500' }}" />
                            <span class="text-sm ml-2 sidebar-text duration-200">Exam Pricing Tiers</span>
                        </div>
                    </a>
                </li>

                <li>
                    <a :class="sidebarExpanded ? 'py-2' : ''"
                       class="block pl-3 rounded-lg transition {{ Route::is('admin.pricing-settings.audits') ? 'bg-violet-500 text-white font-bold' : 'text-gray-800 dark:text-gray-100 hover:bg-gray-200 dark:hover:bg-gray-700' }}"
                       href="{{ route('admin.pricing-settings.audits') }}">
                        <div class="flex items-center">
                            <x-heroicon-o-clipboard-document-list class="shrink-0 w-4 h-4 {{ Route::is('admin.pricing-settings.audits') ? 'text-white' : 'text-gray-400 dark:text-gray-500' }}" />
                            <span class="text-sm ml-2 sidebar-text duration-200">Pricing Audit Log</span>
                        </div>
                    </a>
                </li>

                <li>
                    <a :class="sidebarExpanded ? 'py-2' : ''"
                       class="block pl-3 rounded-lg transition {{ Route::is('admin.artisan-commands') ? 'bg-violet-500 text-white font-bold' : 'text-gray-800 dark:text-gray-100 hover:bg-gray-200 dark:hover:bg-gray-700' }}"
                       href="{{ route('admin.artisan-commands') }}">
                        <div class="flex items-center">
                            <x-heroicon-o-command-line class="shrink-0 w-4 h-4 {{ Route::is('admin.artisan-commands') ? 'text-white' : 'text-gray-400 dark:text-gray-500' }}" />
                            <span class="text-sm ml-2 sidebar-text duration-200">System Commands</span>
                        </div>
                    </a>
                </li>

                <li>
                    <a :class="sidebarExpanded ? 'py-2' : ''"
                       class="block pl-3 rounded-lg transition {{ Route::is('admin.logins') ? 'bg-violet-500 text-white font-bold' : 'text-gray-800 dark:text-gray-100 hover:bg-gray-200 dark:hover:bg-gray-700' }}"
                       href="{{ route('admin.logins') }}">
                        <div class="flex items-center">
                            <x-heroicon-o-key class="shrink-0 w-4 h-4 {{ Route::is('admin.logins') ? 'text-white' : 'text-gray-400 dark:text-gray-500' }}" />
                            <span class="text-sm ml-2 sidebar-text duration-200">Login Activity</span>
                        </div>
                    </a>
                </li>

                <li>
                    <a :class="sidebarExpanded ? 'py-2' : ''"
                       class="block pl-3 rounded-lg transition {{ Route::is('admin.activity-trail*') ? 'bg-violet-500 text-white font-bold' : 'text-gray-800 dark:text-gray-100 hover:bg-gray-200 dark:hover:bg-gray-700' }}"
                       href="{{ route('admin.activity-trail.index') }}">
                        <div class="flex items-center">
                            <x-heroicon-o-list-bullet class="shrink-0 w-4 h-4 {{ Route::is('admin.activity-trail*') ? 'text-white' : 'text-gray-400 dark:text-gray-500' }}" />
                            <span class="text-sm ml-2 sidebar-text duration-200">User Actions</span>
                        </div>
                    </a>
                </li>

                <li>
                    <a :class="sidebarExpanded ? 'py-2' : ''"
                       class="block pl-3 rounded-lg transition {{ Route::is('admin.academic-activities*') ? 'bg-violet-500 text-white font-bold' : 'text-gray-800 dark:text-gray-100 hover:bg-gray-200 dark:hover:bg-gray-700' }}"
                       href="{{ route('admin.academic-activities') }}">
                        <div class="flex items-center">
                            <x-heroicon-o-shield-check class="shrink-0 w-4 h-4 {{ Route::is('admin.academic-activities*') ? 'text-white' : 'text-gray-400 dark:text-gray-500' }}" />
                            <span class="text-sm ml-2 sidebar-text duration-200">Moderator Activities</span>
                        </div>
                    </a>
                </li>

                {{-- Hidden: Media & ChangeLog --}}
                <li class="hidden">
                    <a :class="sidebarExpanded ? 'py-2' : ''"
                       class="block pl-3 rounded-lg transition {{ Route::is('media*') ? 'bg-violet-500 text-white font-bold' : 'text-gray-800 dark:text-gray-100 hover:bg-gray-200 dark:hover:bg-gray-700' }}"
                       href="{{ route('media.index') }}">
                        <div class="flex items-center">
                            <x-heroicon-o-photo class="shrink-0 w-4 h-4 {{ Route::is('media*') ? 'text-white' : 'text-gray-400 dark:text-gray-500' }}" />
                            <span class="text-sm ml-2 sidebar-text duration-200">Media Management</span>
                        </div>
                    </a>
                </li>

                <li class="hidden">
                    <a :class="sidebarExpanded ? 'py-2' : ''"
                       class="block pl-3 rounded-lg transition {{ Route::is('admin.change-log*') ? 'bg-violet-500 text-white font-bold' : 'text-gray-800 dark:text-gray-100 hover:bg-gray-200 dark:hover:bg-gray-700' }}"
                       href="{{ route('admin.change-log.index') }}">
                        <div class="flex items-center">
                            <x-heroicon-o-clock class="shrink-0 w-4 h-4 {{ Route::is('admin.change-log*') ? 'text-white' : 'text-gray-400 dark:text-gray-500' }}" />
                            <span class="text-sm ml-2 sidebar-text duration-200">Change Log</span>
                        </div>
                    </a>
                </li>
            </ul>
        </div>
    @endif

    {{-- ═══════════════════════════════════════════════════════════
        SECTION: LMS & Courses
    ═══════════════════════════════════════════════════════════ --}}
    <div class="border-t border-gray-200 dark:border-gray-700 pt-4 mt-2">
        <h3 class="text-xs uppercase text-gray-400 dark:text-gray-500 font-semibold pl-3 mb-2">
            <span class="hidden lg:block lg:sidebar-expanded:hidden 2xl:hidden text-center w-6" aria-hidden="true">•••</span>
            <span class="lg:hidden lg:sidebar-expanded:block 2xl:block">LMS</span>
        </h3>
        <ul class="space-y-0.5">
            @can('create', App\Models\Lms\Course::class)
                <li>
                    <a :class="sidebarExpanded ? 'py-2' : ''"
                       class="block pl-3 rounded-lg transition {{ Route::is('course-management.*') ? 'bg-violet-500 text-white font-bold' : 'text-gray-800 dark:text-gray-100 hover:bg-gray-200 dark:hover:bg-gray-700' }}"
                       href="{{ route('course-management.index') }}">
                        <div class="flex items-center">
                            <x-heroicon-o-book-open class="shrink-0 w-4 h-4 {{ Route::is('course-management.*') ? 'text-white' : 'text-gray-400 dark:text-gray-500' }}" />
                            <span class="text-sm ml-2 sidebar-text duration-200">Course Management</span>
                        </div>
                    </a>
                </li>
            @endcan
        </ul>
    </div>

</div>