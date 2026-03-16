@props(['variant' => 'v1', 'hasSchoolSwitcher' => false, 'hasImpersonationBanner' => false])
@php
    // Calculate sidebar height and top position based on visible banners
    // On small devices (< lg), sidebar is absolutely positioned and needs top offset for banners
    // On large devices (lg+), sidebar is static so top doesn't apply
    $heightClass = 'h-[100dvh]';
    $topClass = 'top-0';
    if ($hasSchoolSwitcher && $hasImpersonationBanner) {
        $heightClass = 'h-[calc(100dvh-5rem)]'; // Both banners: 2.5rem + 2.5rem
        $topClass = 'top-[5rem] lg:top-0'; // Offset for banners on small devices
    } elseif ($hasSchoolSwitcher || $hasImpersonationBanner) {
        $heightClass = 'h-[calc(100dvh-2.5rem)]'; // One banner: 2.5rem
        $topClass = 'top-[2.5rem] lg:top-0'; // Offset for banner on small devices
    }
@endphp
<div class="min-w-fit h-full thin-scrollbar">
    <!-- Sidebar backdrop (mobile only) -->
    <div
        class="fixed inset-0 bg-gray-900/30 z-40 lg:hidden lg:z-auto transition-opacity duration-200"
        :class="$store.sidebar.open ? 'opacity-100' : 'opacity-0 pointer-events-none'"
        aria-hidden="true"
        x-cloak
    ></div>

    <!-- Sidebar -->
    <div
        id="sidebar"
        class="flex lg:flex! flex-col absolute z-40 left-0 {{ $topClass }} lg:static lg:left-auto lg:top-auto lg:translate-x-0 {{ $heightClass }} overflow-hidden no-scrollbar w-80 lg:w-64 lg:sidebar-expanded:!w-[32rem] 2xl:!w-[32rem] shrink-0 bg-gradient-to-b from-slate-50 to-slate-100 dark:from-slate-900 dark:to-slate-800  transition-all duration-200 ease-in-out {{ $variant === 'v2' ? 'border-r border-gray-200 dark:border-gray-700/60' : ' shadow-xs' }}"
        :class="$store.sidebar.open ? 'max-lg:translate-x-0' : 'max-lg:-translate-x-80 lg:translate-x-0'"
        @click.outside="$store.sidebar.open =  false"
        @keydown.escape.window="$store.sidebar.open = false"
    >

        <!-- Sidebar header -->
        <div class="flex lg:hidden justify-start pt-4 pb-6 pl-6 sm:px-6 shrink-0">
            <!-- Close button -->
            <button class="lg:hidden text-gray-500 hover:text-gray-400" @click.stop="$store.sidebar.toggleOpen()"
                    aria-controls="sidebar" :aria-expanded="$store.sidebar.open">
                <span class="sr-only">Close sidebar</span>
                <svg class="w-6 h-6 fill-current" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path d="M10.7 18.7l1.4-1.4L7.8 13H20v-2H7.8l4.3-4.3-1.4-1.4L4 12z"/>
                </svg>
            </button>
        </div>

        <!-- Scrollable content area -->
        <div class="flex-1 min-h-0 overflow-y-auto overflow-x-hidden no-scrollbar hide-scrollbar">
            <div class="lg:pt-6">
                <div :class="$store.sidebar.open ? 'w-16 h-16' : 'w-12 h-12'" class="mx-auto mb-2">
                    <x-avatar
                        :name="auth()->user()->name"
                        avatar="{{ auth()->user()->avatar }}"
                        class="rounded-full w-full h-full"
                    ></x-avatar>
                </div>
                <div x-show="$store.sidebar.expanded" class="sidebar-text">
                    <h1 class="text-center text-lg font-bold text-gray-800 dark:text-white">{{ auth()->user()->name }}</h1>
                    <h2 class="text-center text-xs text-gray-500 -mt-1 tracking-tight dark:text-gray-400">{{ auth()->user()->email }}</h2>

                    @php
                        // Use the centralized helper function
                        $currentSchool = getCurrentSchoolContext();
                        $viewingAllSchools = isViewingAllSchools();
                    @endphp

                    @if($currentSchool)
                        <div class="mt-1 text-center">
                            <span
                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-100">
                                <svg class="-ml-0.5 mr-1.5 h-2 w-2 text-blue-400" fill="currentColor" viewBox="0 0 8 8">
                                    <circle cx="4" cy="4" r="3"/>
                                </svg>
                                {{ $currentSchool->name }}
                            </span>
                        </div>
                    @elseif($viewingAllSchools && (auth()->user()->hasRole('owner') || auth()->user()->isSuperAdmin()))
                        <div class="mt-1 text-center">
                            <span
                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-100">
                                <svg class="-ml-0.5 mr-1.5 h-2 w-2 text-green-400" fill="currentColor"
                                     viewBox="0 0 8 8">
                                    <circle cx="4" cy="4" r="3"/>
                                </svg>
                                All Schools
                            </span>
                        </div>
                    @elseif(auth()->user()->school)
                        <div class="mt-1 text-center">
                            <span
                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-100">
                                <svg class="-ml-0.5 mr-1.5 h-2 w-2 text-blue-400" fill="currentColor" viewBox="0 0 8 8">
                                    <circle cx="4" cy="4" r="3"/>
                                </svg>
                                {{ auth()->user()->school->name }}
                            </span>
                        </div>
                    @endif

                </div>
                <div class="border-b rounded-lg border-gray-200 border-2 mt-6"></div>
            </div>

            <div class="space- px-2">
                @php
                    use App\Enums\UserRole;

                    // Get role value for comparison
                    $userRole = auth()->user()->role;
                    $roleValue = $userRole instanceof UserRole ? $userRole->value : $userRole;
                @endphp

                <div class="mb-4">
                     @auth
                        @if($userRole === UserRole::ADMIN || $userRole === UserRole::OWNER || in_array($roleValue, ['admin', 'owner']))
                            @livewire('administrators.admin-navigation', [
                                'activeTab' => Route::is('admin.dashboard') ? request()->query('activeTab', 'overview') : 'overview'
                            ])
                        @elseif($userRole === UserRole::STUDENT || $roleValue === 'student')
                            @livewire('students.student-navigation', [
                                'activeTab' => Route::is('dashboard') ? request()->query('activeTab', 'overview') : 'overview'
                            ])
                        @elseif($userRole === UserRole::TEACHER || $roleValue === 'teacher')
                            @include('livewire.navigations.teacher-navigation')
                        @elseif($userRole === UserRole::PARENT || $roleValue === 'parent')
                            @include('livewire.navigations.parent-navigation')
                        @elseif($userRole === UserRole::LIBRARIAN || $roleValue === 'librarian')
                            @include('livewire.navigations.librarian-navigation', [
                                'activeTab' => Route::is('librarian.dashboard') ? request()->query('activeTab', 'overview') : 'overview'
                            ])
                        @elseif($userRole === UserRole::AUTHOR || $roleValue === 'author')
                            @include('livewire.navigations.author-navigation', [
                                'activeTab' => Route::is('author.dashboard') ? request()->query('activeTab', 'overview') : 'overview'
                            ])
                        @elseif($userRole === UserRole::GUEST || $roleValue === 'guest')
                            @include('livewire.navigations.subscriber-navigation', [
                                'activeTab' => Route::is('author.dashboard') ? request()->query('activeTab', 'overview') : 'overview'
                            ])
                        @elseif($userRole === UserRole::MODERATOR || $roleValue === 'moderator')
                            @include('livewire.navigations.moderator-navigation', [
                                'activeTab' => Route::is('author.dashboard') ? request()->query('activeTab', 'overview') : 'overview'
                            ])
                        @elseif($userRole === UserRole::ACCOUNTANT || $roleValue === 'accountant')
                            @include('livewire.navigations.accountant-navigation')
                        @endif
                    @endauth
                </div>


                <ul x-data="{ sidebarExpanded: $store.sidebar.expanded }"
                    class="border-t-2 pt-4 border-gray-200 dark:border-gray-700 space-y-1">


                    <!-- Sponsorship Section -->
                    <template class="hidden">
                        <li class="px-3 py-2 rounded-sm mb-0.5">
                            <span class="text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase sidebar-text">Sponsorship</span>
                        </li>

                        <li class="mb-0.5 last:mb-2" title="Programs">
                            <a :class="sidebarExpanded ? 'py-2' : ''"
                               class="block pl-3 rounded-lg transition {{ Route::is('sponsorship.programs*') ? 'bg-violet-500 text-white font-bold' : 'text-gray-800 dark:text-gray-100 hover:bg-gray-200 dark:hover:bg-gray-700' }}"
                               href="{{ route('sponsorship.programs.index') }}">
                                <div class="flex items-center">
                                    <svg
                                        class="shrink-0 fill-current {{ Route::is('sponsorship.programs*') ? 'text-white' : 'text-gray-400 dark:text-gray-500' }}"
                                        xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24">
                                        <path
                                            d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1.41 16.09V20h-2.67v-1.93c-1.71-.36-3.16-1.46-3.27-3.4h1.96c.1 1.05.82 1.87 2.65 1.87 1.96 0 2.4-.98 2.4-1.59 0-.83-.44-1.61-2.67-2.14-2.48-.6-4.18-1.62-4.18-3.67 0-1.72 1.39-2.84 3.11-3.21V4h2.67v1.95c1.86.45 2.79 1.86 2.85 3.39H14.3c-.05-1.11-.64-1.87-2.22-1.87-1.5 0-2.4.68-2.4 1.64 0 .84.65 1.39 2.67 1.91s4.18 1.39 4.18 3.91c-.01 1.83-1.38 2.83-3.12 3.16z"/>
                                    </svg>
                                    <span class="text-sm ml-2 sidebar-text duration-200">Programs</span>
                                </div>
                            </a>
                        </li>

                        <li class="mb-0.5 last:mb-2" title="Sponsor Offers">
                            <a :class="sidebarExpanded ? 'py-2' : ''"
                               class="block pl-3 rounded-lg transition {{ Route::is('sponsorship.offers*') ? 'bg-violet-500 text-white font-bold' : 'text-gray-800 dark:text-gray-100 hover:bg-gray-200 dark:hover:bg-gray-700' }}"
                               href="{{ route('sponsorship.offers.index') }}">
                                <div class="flex items-center">
                                    <svg
                                        class="shrink-0 fill-current {{ Route::is('sponsorship.offers*') ? 'text-white' : 'text-gray-400 dark:text-gray-500' }}"
                                        xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24">
                                        <path
                                            d="M20 6h-2.18c.11-.31.18-.65.18-1 0-1.66-1.34-3-3-3-1.05 0-1.96.54-2.5 1.35l-.5.67-.5-.68C10.96 2.54 10.05 2 9 2 7.34 2 6 3.34 6 5c0 .35.07.69.18 1H4c-1.11 0-1.99.89-1.99 2L2 19c0 1.11.89 2 2 2h16c1.11 0 2-.89 2-2V8c0-1.11-.89-2-2-2zm-5-2c.55 0 1 .45 1 1s-.45 1-1 1-1-.45-1-1 .45-1 1-1zM9 4c.55 0 1 .45 1 1s-.45 1-1 1-1-.45-1-1 .45-1 1-1zm11 15H4v-2h16v2zm0-5H4V8h5.08L7 10.83 8.62 12 11 8.76l1-1.36 1 1.36L15.38 12 17 10.83 14.92 8H20v6z"/>
                                    </svg>
                                    <span class="text-sm ml-2 sidebar-text duration-200">Sponsor Offers</span>
                                </div>
                            </a>
                        </li>

                        @auth
                            <li class="mb-0.5 last:mb-2" title="My Contributions">
                                <a :class="sidebarExpanded ? 'py-2' : ''"
                                   class="block pl-3 rounded-lg transition {{ Route::is('sponsorship.contributions*') ? 'bg-violet-500 text-white font-bold' : 'text-gray-800 dark:text-gray-100 hover:bg-gray-200 dark:hover:bg-gray-700' }}"
                                   href="{{ route('sponsorship.contributions.mine') }}">
                                    <div class="flex items-center">
                                        <svg
                                            class="shrink-0 fill-current {{ Route::is('sponsorship.contributions*') ? 'text-white' : 'text-gray-400 dark:text-gray-500' }}"
                                            xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                            viewBox="0 0 24 24">
                                            <path
                                                d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm.89-8.9c-.6-.14-1.19-.31-1.19-.76 0-.37.35-.59 1-.59.75 0 1.03.36 1.1.89h1.37c-.09-.84-.59-1.56-1.47-1.82V7h-2v1.79c-.93.22-1.7.84-1.7 1.96 0 1.41 1.22 1.91 2.37 2.18.68.16 1.28.34 1.28.81 0 .3-.27.65-1 .65-.81 0-1.15-.43-1.21-.98H10.1c.08 1.03.75 1.76 1.9 2.01V17h2v-1.61c.94-.24 1.71-.87 1.71-1.99 0-1.56-1.22-2.08-2.82-2.3z"/>
                                        </svg>
                                        <span class="text-sm ml-2 sidebar-text duration-200">My Contributions</span>
                                    </div>
                                </a>
                            </li>

                            <!-- Benefactor Dashboard (conditionally shown) -->
                            <li class="mb-0.5 last:mb-2" title="Benefactor Dashboard">
                                <a :class="sidebarExpanded ? 'py-2' : ''"
                                   class="block pl-3 rounded-lg transition {{ Route::is('benefactor.*') ? 'bg-violet-500 text-white font-bold' : 'text-gray-800 dark:text-gray-100 hover:bg-gray-200 dark:hover:bg-gray-700' }}"
                                   href="{{ route('benefactor.dashboard') }}">
                                    <div class="flex items-center">
                                        <svg
                                            class="shrink-0 fill-current {{ Route::is('benefactor.*') ? 'text-white' : 'text-gray-400 dark:text-gray-500' }}"
                                            xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                            viewBox="0 0 24 24">
                                            <path
                                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"
                                                stroke="currentColor" stroke-width="2" fill="none"
                                                stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                        <span class="text-sm ml-2 sidebar-text duration-200">Benefactor</span>
                                    </div>
                                </a>
                            </li>

                            <!-- Sponsor Dashboard -->
                            <li class="mb-0.5 last:mb-2" title="Sponsor Dashboard">
                                <a :class="sidebarExpanded ? 'py-2' : ''"
                                   class="block pl-3 rounded-lg transition {{ Route::is('sponsor.*') ? 'bg-violet-500 text-white font-bold' : 'text-gray-800 dark:text-gray-100 hover:bg-gray-200 dark:hover:bg-gray-700' }}"
                                   href="{{ route('sponsor.dashboard') }}">
                                    <div class="flex items-center">
                                        <svg
                                            class="shrink-0 fill-current {{ Route::is('sponsor.*') ? 'text-white' : 'text-gray-400 dark:text-gray-500' }}"
                                            xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                            viewBox="0 0 24 24">
                                            <path
                                                d="M9 11.75A2.25 2.25 0 116.75 9 2.25 2.25 0 019 11.75zm0 1.5a3.75 3.75 0 100-7.5 3.75 3.75 0 000 7.5zM12.75 12a2.25 2.25 0 112.25-2.25A2.25 2.25 0 0112.75 12zm0 1.5a3.75 3.75 0 100-7.5 3.75 3.75 0 000 7.5zM8.25 16.5A2.25 2.25 0 116 14.25a2.25 2.25 0 012.25 2.25zm0 1.5a3.75 3.75 0 100-7.5 3.75 3.75 0 000 7.5zM15.75 18a2.25 2.25 0 112.25-2.25A2.25 2.25 0 0115.75 18zm0 1.5a3.75 3.75 0 100-7.5 3.75 3.75 0 000 7.5z"
                                                fill="currentColor"/>
                                        </svg>
                                        <span class="text-sm ml-2 sidebar-text duration-200">Sponsor</span>
                                    </div>
                                </a>
                            </li>

                            @if(auth()->user()->hasRole('owner') || auth()->user()->hasRole('reviewer'))
                                <!-- Reviewer Queue -->
                                <li class="mb-0.5 last:mb-2" title="Verification Queue">
                                    <a :class="sidebarExpanded ? 'py-2' : ''"
                                       class="block pl-3 rounded-lg transition {{ Route::is('reviewer.*') ? 'bg-violet-500 text-white font-bold' : 'text-gray-800 dark:text-gray-100 hover:bg-gray-200 dark:hover:bg-gray-700' }}"
                                       href="{{ route('reviewer.verification.queue') }}">
                                        <div class="flex items-center">
                                            <svg
                                                class="shrink-0 fill-current {{ Route::is('reviewer.*') ? 'text-white' : 'text-gray-400 dark:text-gray-500' }}"
                                                xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                viewBox="0 0 24 24">
                                                <path d="M9 16.2L4.8 12l-1.4 1.4L9 19 21 7l-1.4-1.4L9 16.2z"/>
                                            </svg>
                                            <span class="text-sm ml-2 sidebar-text duration-200">Verification</span>
                                        </div>
                                    </a>
                                </li>
                            @endif
                        @endauth
                    </template>


                    <!-- Other Sections -->
                    <li class="px-3 py-2 rounded-sm mb-0.5 mt-4">
                        <span class="text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase sidebar-text">Resources</span>
                    </li>

                    <li class="mb-0.5 last:mb-2" title="Messenger Subscriptions">
                        <a :class="sidebarExpanded ? 'py-2' : ''"
                           class="block pl-3  rounded-lg transition {{ Route::is('token-subscriptions*') ? 'bg-violet-500 text-white font-bold' : 'text-gray-800 dark:text-gray-100 hover:bg-gray-200 dark:hover:bg-gray-700' }}"
                           href="{{route('token-subscriptions.index')}}">
                            <div class="flex items-center">
                                <svg
                                    class="shrink-0 fill-current {{ Route::is('token-subscriptions*') ? 'text-white' : 'text-gray-400 dark:text-gray-500' }}"
                                    xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16">
                                    <path
                                        d="M2 2a1 1 0 0 0-1 1v10a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V3a1 1 0 0 0-1-1H2zm11 2v2H3V4h10zM6 8h5v1H6V8zm-1 2h6v1H5v-1zm-1 2h7v1H4v-1z"/>
                                    <path
                                        d="M3 4h10v6H3V4zm0-1a1 1 0 0 0-1 1v6a1 1 0 0 0 1 1h10a1 1 0 0 0 1-1V4a1 1 0 0 0-1-1H3z"/>
                                </svg>

                                <span class="text-sm ml-2 sidebar-text duration-200">Premium Subscriptions</span>
                            </div>
                        </a>
                    </li>

                    <li class="mb-0.5 last:mb-0" title="Shared Resources">
                        <a :class="sidebarExpanded ? 'py-2' : ''"
                           class="block pl-3 rounded-lg transition {{ Route::is('user-books*') ? 'bg-violet-500 text-white font-bold' : 'text-gray-800 dark:text-gray-100 hover:bg-gray-200 dark:hover:bg-gray-700' }}"
                           href="{{route('user-books.index')}}">
                            <div class="flex items-center">
                                <svg
                                    class="shrink-0 fill-current {{ Route::is('user-books*') ? 'text-white' : 'text-gray-400 dark:text-gray-500' }}"
                                    xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24">
                                    <path
                                        d="M21 5c-1.11-.35-2.33-.5-3.5-.5-1.95 0-4.05.4-5.5 1.5-1.45-1.1-3.55-1.5-5.5-1.5S2.45 4.9 1 6v14.65c0 .25.25.5.5.5.1 0 .15-.05.25-.05C3.1 20.45 5.05 20 6.5 20c1.95 0 4.05.4 5.5 1.5 1.35-.85 3.8-1.5 5.5-1.5 1.65 0 3.35.3 4.75 1.05.1.05.15.05.25.05.25 0 .5-.25.5-.5V6c-.6-.45-1.25-.75-2-1zm0 13.5c-1.1-.35-2.3-.5-3.5-.5-1.7 0-4.15.65-5.5 1.5V8c1.35-.85 3.8-1.5 5.5-1.5 1.2 0 2.4.15 3.5.5v11.5z"/>
                                </svg>

                                <span class="text-sm ml-2 sidebar-text duration-200">Shared Resources</span>
                            </div>
                        </a>
                    </li>

                    <li class="mb-0.5 last:mb-2" title="Notes">
                        <a :class="sidebarExpanded ? 'py-2' : 'py-2'"
                           class="block pl-3 rounded-lg transition {{ Route::is('notes*') ? 'bg-violet-500 text-white font-bold' : 'text-gray-800 dark:text-gray-100 hover:bg-gray-200 dark:hover:bg-gray-700' }}"
                           href="{{route('notes.index')}}">
                            <div class="flex items-center">
                                <svg
                                    class="shrink-0 fill-current {{ Route::is('notes*') ? 'text-white' : 'text-gray-400 dark:text-gray-500' }}"
                                    xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24">
                                    <path
                                        d="M14 2H6c-1.1 0-1.99.9-1.99 2L4 20c0 1.1.89 2 1.99 2H18c1.1 0 2-.9 2-2V8l-6-6zm2 16H8v-2h8v2zm0-4H8v-2h8v2zm-3-5V3.5L18.5 9H13z"/>
                                </svg>

                                <span class="text-sm ml-2 sidebar-text duration-200">Notes</span>
                            </div>
                        </a>
                    </li>

                    <li class="mb-0.5 last:mb-2" title="Calendar">
                        <a :class="sidebarExpanded ? 'py-2' : 'py-2'"
                           class="block pl-3 rounded-lg transition {{ Route::is('calendar*') ? 'bg-violet-500 text-white font-bold' : 'text-gray-800 dark:text-gray-100 hover:bg-gray-200 dark:hover:bg-gray-700' }}"
                           href="{{route('calendar.index')}}">
                            <div class="flex items-center">
                                <svg
                                    class="shrink-0 fill-current {{ Route::is('calendar*') ? 'text-white' : 'text-gray-400 dark:text-gray-500' }}"
                                    xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24">
                                    <path
                                        d="M19 3h-1V1h-2v2H8V1H6v2H5c-1.11 0-1.99.9-1.99 2L3 19c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V8h14v13z"/>
                                    <path
                                        d="M16 11H8v2h8v-2zm0 4H8v2h8v-2z"/>
                                </svg>

                                <span class="text-sm ml-2 sidebar-text duration-200">Calendar</span>
                            </div>
                        </a>
                    </li>

                    <li class="mb-0.5 last:mb-2 hidden" title="Activity Tracker">
                        <a :class="sidebarExpanded ? 'py-2' : 'py-2'"
                           class="block pl-3 rounded-lg transition {{ Route::is('activities*') ? 'bg-violet-500 text-white font-bold' : 'text-gray-800 dark:text-gray-100 hover:bg-gray-200 dark:hover:bg-gray-700' }}"
                           href="{{route('activities.index')}}">
                            <div class="flex items-center">
                                <svg
                                    class="shrink-0 fill-current {{ Route::is('activities*') ? 'text-white' : 'text-gray-400 dark:text-gray-500' }}"
                                    xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24">
                                    <path
                                        d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm3.5-9c.83 0 1.5-.67 1.5-1.5S16.33 8 15.5 8 14 8.67 14 9.5s.67 1.5 1.5 1.5zm-7 0c.83 0 1.5-.67 1.5-1.5S9.33 8 8.5 8 7 8.67 7 9.5 7.67 11 8.5 11zm3.5 6.5c2.33 0 4.31-1.46 5.11-3.5H6.89c.8 2.04 2.78 3.5 5.11 3.5z"/>
                                </svg>

                                <span class="text-sm ml-2 sidebar-text duration-200">Activity Tracker</span>
                            </div>
                        </a>
                    </li>

                    <li class="mb-0.5 last:mb-0" title="Labs & Practicals">
                        <a :class="sidebarExpanded ? 'py-2' : 'py-2'"
                           class="block pl-3 rounded-lg transition {{ Route::is('educational-resources.*') ? 'bg-violet-500 text-white font-bold' : 'text-gray-800 dark:text-gray-100 hover:bg-gray-200 dark:hover:bg-gray-700' }}"
                           href="{{ route('educational-resources.index') }}">
                            <div class="flex items-center">
                                <svg
                                    class="shrink-0 fill-current {{ Route::is('educational-resources.*') ? 'text-white' : 'text-gray-400 dark:text-gray-500' }}"
                                    xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24">
                                    <path d="M4 4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h13c1.1 0 2-.9 2-2v-9l-5-5H4zm0 2h8v4h4v8H4V6zm10-1.5 3.5 3.5H14V4.5z"/>
                                </svg>

                                <span class="text-sm ml-2 sidebar-text duration-200">Labs & Practicals</span>
                            </div>
                        </a>
                    </li>
                </ul>
            </div>
        </div>


        <div class="hidden lg:flex mt-auto py-1 justify-end border-t border-gray-200 dark:border-gray-700 shrink-0 bg-slate-50 dark:bg-slate-900">
            <div class="w-12 pl-4 pr-3 py-2">
                <button
                    class="text-gray-400 hover:text-gray-500 dark:text-gray-500 dark:hover:text-gray-400 transition-colors"
                    @click="$store.sidebar.toggleExpanded()">
                    <span class="sr-only">Expand / collapse sidebar</span>
                    <svg
                        class="shrink-0 fill-current text-gray-400 dark:text-gray-500 transition-transform duration-200"
                        :class="{ 'rotate-180': $store.sidebar.expanded }"
                        xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16">
                        <path
                            d="M15 16a1 1 0 0 1-1-1V1a1 1 0 1 1 2 0v14a1 1 0 0 1-1 1ZM8.586 7H1a1 1 0 1 0 0 2h7.586l-2.793 2.793a1 1 0 1 0 1.414 1.414l4.5-4.5A.997.997 0 0 0 12 8.01M11.924 7.617a.997.997 0 0 0-.217-.324l-4.5-4.5a1 1 0 0 0-1.414 1.414L8.586 7M12 7.99a.996.996 0 0 0-.076-.373Z"/>
                    </svg>
                </button>
            </div>
        </div>

    </div>
</div>
