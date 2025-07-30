@props(['variant' => 'v1'])
<div class="min-w-fit">
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
        class="flex lg:flex! flex-col absolute z-40 left-0 top-0 lg:static lg:left-auto lg:top-auto lg:translate-x-0 h-[100dvh] overflow-y-scroll lg:overflow-y-auto no-scrollbar w-64 lg:w-20 lg:sidebar-expanded:!w-64 2xl:w-64! shrink-0 bg-gradient-to-b from-slate-50 to-slate-100 dark:from-slate-900 dark:to-slate-800  transition-all duration-200 ease-in-out {{ $variant === 'v2' ? 'border-r border-gray-200 dark:border-gray-700/60' : ' shadow-xs' }}"
        :class="$store.sidebar.open ? 'max-lg:translate-x-0' : 'max-lg:-translate-x-64'"
        @click.outside="$store.sidebar.open =  false"
        style=""
        @keydown.escape.window="$store.sidebar.open = false"
    >

        <!-- Sidebar header -->
        <div class="flex justify-between mb-10 pr-3 sm:px-2">
            <!-- Close button -->
            <button class="lg:hidden text-gray-500 hover:text-gray-400" @click.stop="$store.sidebar.toggleOpen()"
                    aria-controls="sidebar" :aria-expanded="$store.sidebar.open">
                <span class="sr-only">Close sidebar</span>
                <svg class="w-6 h-6 fill-current" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path d="M10.7 18.7l1.4-1.4L7.8 13H20v-2H7.8l4.3-4.3-1.4-1.4L4 12z"/>
                </svg>
            </button>
        </div>
        <div class="">
            <x-avatar :name="auth()->user()->name" avatar="{{ auth()->user()->avatar }}" class="w-12 h-12 rounded-full mx-auto mb-2"></x-avatar>
            <div x-show="$store.sidebar.expanded" class="sidebar-text">
                <h1 class="text-center text-lg font-bold text-gray-800 dark:text-white">{{ auth()->user()->name }}</h1>
                <h2 class="text-center text-xs text-gray-500 -mt-1 tracking-tight dark:text-gray-400">{{ auth()->user()->email }}</h2>
            </div>
            <div class="border-b rounded-lg border-gray-200 border-2 mt-6"></div>
        </div>

        <div class="space-y-8 p-4">

            @auth
                @if(in_array(auth()->user()->role, ['admin', 'owner']))
                    @livewire('administrators.admin-navigation', [
                        'activeTab' => Route::is('admin.dashboard') ? request()->query('activeTab', 'overview') : 'overview'
                    ])
                @elseif(auth()->user()->role === 'student' )
                    @livewire('students.student-navigation', [
                        'activeTab' => Route::is('dashboard') ? request()->query('activeTab', 'overview') : 'overview'
                    ])
                    @elseif(auth()->user()->role === 'teacher')
                    @include('livewire.navigations.teacher-navigation')
                    @elseif(auth()->user()->role === 'parent')
                    @include('livewire.navigations.parent-navigation')
                    @elseif(auth()->user()->role === 'librarian')
                    @include('livewire.navigations.librarian-navigation', [
                        'activeTab' => Route::is('librarian.dashboard') ? request()->query('activeTab', 'overview') : 'overview'
                    ])
                    @elseif(auth()->user()->role === 'author')
                    @include('livewire.navigations.author-navigation', [
                        'activeTab' => Route::is('author.dashboard') ? request()->query('activeTab', 'overview') : 'overview'
                    ])
                @elseif(auth()->user()->role === 'subscriber')
                    @include('livewire.navigations.subscriber-navigation', [
                        'activeTab' => Route::is('author.dashboard') ? request()->query('activeTab', 'overview') : 'overview'
                    ])
                @elseif(auth()->user()->role === 'moderator')
                    @include('livewire.navigations.moderator-navigation', [
                        'activeTab' => Route::is('author.dashboard') ? request()->query('activeTab', 'overview') : 'overview'
                    ])
                @endif

            @endauth
        </div>

        <!-- Expand / collapse button -->
        <div class="pt-3 hidden lg:inline-flex 2xl:hidden justify-end mt-auto">
            <div class="w-12 pl-4 pr-3 py-2">
                <button
                    class="text-gray-400 hover:text-gray-500 dark:text-gray-500 dark:hover:text-gray-400 transition-colors"
                    @click="$store.sidebar.toggleExpanded()">
                    <span class="sr-only">Expand / collapse sidebar</span>
                    <svg class="shrink-0 fill-current text-gray-400 dark:text-gray-500 transition-transform duration-200"
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
