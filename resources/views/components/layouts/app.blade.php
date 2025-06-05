
@props(['action' => null,
'titleAlignCenter' => false,
'breadcrumb' => null,
'title' => null,
'hasAction' => false,
'pageName' => null,
'action_link' => '',
'actionLinkText' => ''])

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
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    @livewireStyles

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

            // Sidebar store
            Alpine.store('sidebar', {
                open: false,
                expanded: localStorage.getItem('sidebar-expanded') === 'true',
                toggleOpen() { this.open = !this.open; },
                toggleExpanded() {
                    this.expanded = !this.expanded;
                    localStorage.setItem('sidebar-expanded', this.expanded);
                }
            });

            // Dark mode store

                // Set up global state
                window.isDarkModeOn = localStorage.getItem('dark-mode') === 'true';

                 window.toggleDarkMode = function () {
                window.isDarkModeOn = !window.isDarkModeOn;
                localStorage.setItem('dark-mode', window.isDarkModeOn);
                updateDarkMode();
            }

                function updateDarkMode() {
                document.documentElement.classList.toggle('dark', window.isDarkModeOn);
                document.documentElement.style.colorScheme = window.isDarkModeOn ? 'dark' : 'light';
            }

                // Initialize on load
                document.addEventListener('DOMContentLoaded', () => {
                // Apply saved preference or system default
                if (localStorage.getItem('dark-mode') === null) {
                    window.isDarkModeOn = window.matchMedia('(prefers-color-scheme: dark)').matches;
            }
                updateDarkMode();
            });

    });


    </script>

</head>
<body
    class="font-sans antialiased bg-gray-100 dark:bg-gray-900 text-gray-600 dark:text-gray-400"
    :class="{ 'sidebar-expanded': $store.sidebar.expanded }"
    x-data="{}"
>
    <!-- Page wrapper -->
    <div class="flex h-screen overflow-hidden">

        <!-- Sidebar -->
        <x-app.sidebar :variant="$attributes['sidebarVariant']" />

        <!-- Content area -->
        <div class="relative flex flex-col flex-1 overflow-y-auto overflow-x-hidden" x-ref="contentarea">
            <!-- Header -->
            <x-app.header :variant="$attributes['headerVariant']"/>

            <!-- Main content -->
            <main class="mt-5">
                <!-- Breadcrumb -->
                <div class="max-w-5xl mx-auto print:hidden">{{ $breadcrumb }}</div>

                <!-- Alerts -->
                <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 print:hidden">
                    <x-alert.success/>
                    <x-alert.danger/>
                </div>

                <!-- Page header -->
                <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
                    <div class="text-lg font-bold py-3 flex {{ $titleAlignCenter ? 'justify-center' : 'justify-between' }}">
                        <div class="text-lg md:text-2xl print:hidden font-bold w-full {{ $titleAlignCenter ? 'text-center' : 'text-start' }}">
                            {{ $title }}
                        </div>

                        <div class="text-sm opacity-50 print:hidden">
                            <div class="my-auto">
                                @if(isset($hasAction) && $hasAction && !isset($action))
                                    @can('administrate')
                                        <x-link.primary class="whitespace-nowrap"
                                                        to="{{ $action_link ?? request()->route()->path }}">
                                            {{ $actionLinkText ?? 'Add Action' }}
                                        </x-link.primary>
                                    @endcan
                                @else
                                    {{ $action }}
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Page content -->
                <div class="transition-all duration-300 bg-inherit mb-12 w-full overflow-x-hidden">
                    <div
                        x-data="{}"
                        class="w-full px-4 sm:px-6 lg:px-8 "
                    >
                        <div class="" style="">
                            {{ $slot }}
                        </div>
                    </div>
                </div>

            </main>
        </div>
    </div>

    <!-- Scripts -->
    @livewireScriptConfig
</body>
</html>
