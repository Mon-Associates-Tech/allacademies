@props(['variant' => 'v1'])
<div class="min-w-fit hide-scrollbar">
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
        class="flex lg:flex! flex-col absolute z-40 left-0 top-0 lg:static lg:left-auto lg:top-auto lg:translate-x-0 h-[100dvh] overflow-y-scroll hide-scrollbar lg:overflow-y-auto no-scrollbar w-64 lg:w-20 lg:sidebar-expanded:!w-64 2xl:w-64! shrink-0 bg-gradient-to-b from-slate-50 to-slate-100 dark:from-slate-900 dark:to-slate-800  transition-all duration-200 ease-in-out {{ $variant === 'v2' ? 'border-r border-gray-200 dark:border-gray-700/60' : ' shadow-xs' }}"
        :class="$store.sidebar.open ? 'max-lg:translate-x-0' : 'max-lg:-translate-x-64'"
        @click.outside="$store.sidebar.open =  false"
        style=""
        @keydown.escape.window="$store.sidebar.open = false"
    >

        <!-- Sidebar header -->
        <div class="flex lg:hidden justify-start pt-4 pb-6 pl-6 sm:px-6">
            <!-- Close button -->
            <button class="lg:hidden text-gray-500 hover:text-gray-400" @click.stop="$store.sidebar.toggleOpen()"
                    aria-controls="sidebar" :aria-expanded="$store.sidebar.open">
                <span class="sr-only">Close sidebar</span>
                <svg class="w-6 h-6 fill-current" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path d="M10.7 18.7l1.4-1.4L7.8 13H20v-2H7.8l4.3-4.3-1.4-1.4L4 12z"/>
                </svg>
            </button>
        </div>
        <div class="lg:pt-6">
            <x-avatar :name="auth()->user()->name" avatar="{{ auth()->user()->avatar }}"
                      class="w-12 h-12 rounded-full mx-auto mb-2"></x-avatar>
            <div x-show="$store.sidebar.expanded" class="sidebar-text">
                <h1 class="text-center text-lg font-bold text-gray-800 dark:text-white">{{ auth()->user()->name }}</h1>
                <h2 class="text-center text-xs text-gray-500 -mt-1 tracking-tight dark:text-gray-400">{{ auth()->user()->email }}</h2>
                @if(auth()->check())
                    @php
                        $user = auth()->user();
                        $currentSchool = null;

                        // For owners, show the switched school context
                        if ($user->hasRole('owner') || $user->isSuperAdmin()) {
                            // Check if we're in "all schools" view (current_school_id is explicitly null)
                            if (session()->has('current_school_id') && session('current_school_id') === null) {
                                $currentSchool = null; // Don't show school name when viewing all schools
                            }
                            // Check if we have a specific school context
                            elseif (app()->bound('current_school')) {
                                try {
                                    $currentSchool = app('current_school');
                                } catch (Exception $e) {
                                    $currentSchool = null;
                                }
                            }
                            // Default to user's school if no context is set
                            elseif ($user->school) {
                                $currentSchool = $user->school;
                            }
                        }
                        // For regular users, show their own school
                        elseif ($user->school) {
                            $currentSchool = $user->school;
                        }
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
                    @elseif($user->hasRole('owner') || $user->isSuperAdmin())
                        <div class="mt-1 text-center">
            <span
                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-100">
                <svg class="-ml-0.5 mr-1.5 h-2 w-2 text-green-400" fill="currentColor" viewBox="0 0 8 8">
                    <circle cx="4" cy="4" r="3"/>
                </svg>
                All Schools
            </span>
                        </div>
                    @endif
                @endif

            </div>
            <div class="border-b rounded-lg border-gray-200 border-2 mt-6"></div>
        </div>

        <div class="space-y-8 p-4">
            @php
                use App\Enums\UserRole;

                // Get role value for comparison
                $userRole = auth()->user()->role;
                $roleValue = $userRole instanceof UserRole ? $userRole->value : $userRole;
            @endphp

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
                @elseif($userRole === UserRole::SUBSCRIBER || $roleValue === 'subscriber')
                    @include('livewire.navigations.subscriber-navigation', [
                        'activeTab' => Route::is('author.dashboard') ? request()->query('activeTab', 'overview') : 'overview'
                    ])
                @elseif($userRole === UserRole::MODERATOR || $roleValue === 'moderator')
                    @include('livewire.navigations.moderator-navigation', [
                        'activeTab' => Route::is('author.dashboard') ? request()->query('activeTab', 'overview') : 'overview'
                    ])
                @endif
            @endauth

            <ul>
                <li class="mb-0.5 last:mb-0" title="Messenger Subscriptions">
                    <a class="block pl-4 pr-3 py-2 rounded-lg transition {{ Route::is('token-subscriptions*') ? 'bg-violet-500 text-white font-bold' : 'text-gray-800 dark:text-gray-100 hover:bg-gray-200 dark:hover:bg-gray-700' }}"
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

                            <span class="text-sm ml-4 sidebar-text duration-200">Messenger Subscriptions</span>
                        </div>
                    </a>
                </li>

           
            </ul>
        </div>

        <!-- Expand / collapse button -->
        <div class="pt-3 hidden lg:inline-flex justify-end mt-auto">
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
