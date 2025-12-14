@props(['action' => null,
'titleAlignCenter' => false,
'breadcrumb' => null,
'title' => null,
'hasAction' => false,
'pageName' => null,
'action_link' => '',
'actionLinkText' => '',
'showTitleArea' => true,
]
)

    <!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" :class="{ 'dark': $store.darkMode.on }">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name') }}{{ $pageName ? ' - ' . $pageName : '' }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">

    <!-- Scripts & Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('head')
    @livewireStyles

    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>
</head>
<body class="font-sans antialiased text-gray-600 dark:text-gray-400 thin-scrollbar
  bg-[radial-gradient(73%_147%,#EADFDF_59%,#ECE2DF_100%),radial-gradient(91%_146%,rgba(255,255,255,0.50)_47%,rgba(0,0,0,0.50)_100%)]
  dark:bg-gradient-to-tr dark:from-gray-900 dark:via-gray-800 dark:to-gray-900
  bg-blend-screen"
      :class="{ 'sidebar-expanded': $store.sidebar.expanded }"
      x-data="{ pageLoaded: false }"
      x-init="setTimeout(() => pageLoaded = true, 150)"
>
<!-- Global Page Loader -->
<div
    class="fixed inset-0 z-[100] flex items-center justify-center bg-gray-50 dark:bg-gray-900 transition-opacity duration-500 ease-in-out"
    x-show="!pageLoaded"
    x-transition:leave="opacity-0"
    aria-hidden="true">
    <div class="flex flex-col items-center">
        <!-- Spinner -->
        <div class="w-12 h-12 border-4 border-violet-500 border-t-transparent rounded-full animate-spin"></div>
        <p class="mt-4 text-sm font-medium text-gray-500 dark:text-gray-400 animate-pulse">Loading...</p>
    </div>
</div>

<x-alert.impersonation-banner></x-alert.impersonation-banner>
@if(auth()->check() && auth()->user()->canAccessCrossSchool())
        <livewire:administrators.school-switcher />
@endif
<!-- Page wrapper -->
<div class="flex h-screen overflow-hidden">

    <!-- Sidebar -->
    <aside class="print:hidden">
        <x-app.sidebar :variant="$attributes['sidebarVariant']"></x-app.sidebar>
    </aside>

    <!-- Content area -->
    <div class="relative flex flex-col thin-scrollbar flex-1 overflow-y-auto overflow-x-hidden" x-ref="contentarea">
        <!-- Header -->
        <x-app.header class="print:hidden" :variant="$attributes['headerVariant']"></x-app.header>

        <!-- Main content -->
        <main class="mt-0 p-0  animate-fade-in">
            <!-- Breadcrumb -->
            @if($breadcrumb)
                <div class="max-w-5xl px-4 lg:px-8 py-1 mx-auto print:hidden">{{ $breadcrumb }}</div>
            @endif

            <!-- Alerts -->
            <div class="max-w-5xl ml-auto px-4 sm:px-6 lg:px-8 print:hidden">
                <x-alert.success></x-alert.success>
                <x-alert.danger></x-alert.danger>
            </div>

            <!-- Page header -->
            @if($showTitleArea)
                <div class="max-w-7xl mr-auto  sm:px-6 lg:pl-8 lg:pr-2 print:hidden">
                    <div
                        class="text-lg font-bold py-3 flex {{ $titleAlignCenter ? 'justify-center' : 'justify-between' }}">
                        <div
                            class="text-lg md:text-2xl hidden print:hidden font-bold w-full {{ $titleAlignCenter ? 'text-center' : 'text-start' }}">
                            {{ $title }}
                        </div>
                    </div>
                </div>
            @endif

            <!-- Page content -->
            <div
                class="transition-all duration-300 bg-inherit mb-12 w-full overflow-y-visible thin-scrollbar overflow-x-hidden">
                <div
                    x-data="{}"
                    class="w-full overflow-y-visible thin-scrollbar sm:px-4 lg:px-4 "
                >
                    {{ $slot }}
                </div>
            </div>
            <livewire:common.flash-message-handler></livewire:common.flash-message-handler>

        </main>
    </div>
</div>

<!-- Scripts -->
@livewireScriptConfig

<script>
    // Check for dark mode preference and apply immediately to prevent flash
    if (localStorage.getItem('dark-mode') === 'true' ||
        (localStorage.getItem('dark-mode') === null &&
            window.matchMedia('(prefers-color-scheme: dark)').matches)) {
        document.documentElement.classList.add('dark');
        document.documentElement.style.colorScheme = 'dark';
    } else {
        document.documentElement.classList.remove('dark');
        document.documentElement.style.colorScheme = 'light';
    }
</script>

<script>
    document.addEventListener('alpine:initializing', () => {
        // Define stores
        // Delete confirmation store
        Alpine.store('deleteForm', {
            open: false,
            title: null,
            content: null,
            action: null,
            button: null,
            show(title, content, action, button = 'Delete') {
                this.title = title;
                this.content = content;
                this.action = action;
                this.button = button;
                this.open = true;
            },
            hide() {
                this.open = false;
            }
        });


        // Change Role store
        Alpine.store('changeRole', {
            open: false,
            userName: '',
            userEmail: '',
            userId: null,
            selectedRole: '',
            show(name, email, currentRole, userId) {
                this.userName = name;
                this.userEmail = email;
                this.userId = userId;
                this.selectedRole = currentRole;
                this.open = true;
            },
            hide() {
                this.open = false;
                this.userName = '';
                this.userEmail = '';
                this.userId = null;
                this.selectedRole = '';
            }
        });


        // Sidebar store
        Alpine.store('sidebar', {
            open: false,
            expanded: localStorage.getItem('sidebar-expanded') === 'true',
            toggleOpen() {
                this.open = !this.open;
            },
            toggleExpanded() {
                this.expanded = !this.expanded;
                localStorage.setItem('sidebar-expanded', this.expanded);
            }
        });

        // Dark mode store
        Alpine.store('darkMode', {
            on: false,

            init() {
                this.on = localStorage.getItem('dark-mode') === 'true' ||
                    (localStorage.getItem('dark-mode') === null &&
                        window.matchMedia('(prefers-color-scheme: dark)').matches);

                // Watch for system theme changes
                window.matchMedia('(prefers-color-scheme: dark)')
                    .addEventListener('change', e => {
                        if (localStorage.getItem('dark-mode') === null) {
                            this.toggle(e.matches);
                        }
                    });
            },

            toggle(value = null) {
                this.on = value !== null ? value : !this.on;
                localStorage.setItem('dark-mode', this.on);
                this.updateDOM();
            },

            updateDOM() {
                document.documentElement.classList.toggle('dark', this.on);
                document.documentElement.style.colorScheme = this.on ? 'dark' : 'light';
            }
        });
    });
</script>
</body>
</html>
