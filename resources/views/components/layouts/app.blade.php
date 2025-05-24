@props(['action' => null,
'titleAlignCenter' => false,
'breadcrumb' => null, 'title' => null, 'hasAction' => false, 'pageName' => null, 'action_link' => '', 'actionLinkText' => ''])

    <!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name') }}  {{ $pageName ? ' - '.  $pageName : '' }}</title>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400..900&display=swap" rel="stylesheet"/>

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('head')
    <!-- Styles -->
    @livewireStyles

    <script>
        if (localStorage.getItem('dark-mode') == 'false' || !('dark-mode' in localStorage)) {
            document.querySelector('html').classList.remove('dark');
            document.querySelector('html').style.colorScheme = 'light';
        } else {
            document.querySelector('html').classList.add('dark');
            document.querySelector('html').style.colorScheme = 'dark';
        }
    </script>

    <script>
        document.addEventListener('alpine:initializing', () => {
            Alpine.store('deleteForm', {
                open: false,
                title: null,
                content: null,
                action: null,
                button: null,
                show(title, content, action, button = 'Delete') {
                    this.title = title
                    this.content = content
                    this.action = action
                    this.button = button
                    this.open = true
                },
                hide() {
                    this.open = false
                }
            })
        })
    </script>

</head>
<body
    class="font-inter antialiased bg-gray-100 dark:bg-gray-900 text-gray-600 dark:text-gray-400"
    :class="{ 'sidebar-expanded': sidebarExpanded }"
    x-data="{ sidebarOpen: false, sidebarExpanded: localStorage.getItem('sidebar-expanded') == 'true' }"
    x-init="$watch('sidebarExpanded', value => localStorage.setItem('sidebar-expanded', value))"
>

<script>
    if (localStorage.getItem('sidebar-expanded') === 'true') {
        document.querySelector('body').classList.add('sidebar-expanded');
    } else {
        document.querySelector('body').classList.remove('sidebar-expanded');
    }
</script>

<!-- Page wrapper -->
<div class="flex h-[100dvh] overflow-hidden">

    <x-app.sidebar :variant="$attributes['sidebarVariant']" />

    <!-- Content area -->
    <div
        class="relative flex flex-col flex-1 overflow-y-auto overflow-x-hidden @if($attributes['background']){{ $attributes['background'] }}@endif"
        x-ref="contentarea">

        <x-app.header :variant="$attributes['headerVariant']"/>

        <main class="grow mt-4">
            <div class="max-w-5xl mx-auto print:hidden">{{$breadcrumb}}</div>
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 print:hidden">
                <x-alert.success/>
                <x-alert.danger/>
            </div>
            <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 text-lg font-bold py-3 flex" :class="{{$titleAlignCenter}} ? 'justify-center' : 'justify-between'">
                <div class="text-lg md:text-2xl print:hidden font-bold w-full" :class="{{$titleAlignCenter}} ? 'text-center' : 'text-start'">{{$title}}</div>
                <div class="text-sm text-opacity-50! print:hidden">
                    <div class="my-auto">

                        @if(isset($hasAction) && $hasAction && !isset($action))
                            @can('administrate')
                                <x-link.primary class="text-nowrap whitespace-nowrap"
                                                to="{{$action_link ?? request()->route()->path}}">
                                    {{$actionLinkText ?? 'Add Action'}}
                                </x-link.primary>
                            @endcan
                        @else
                            {{$action}}
                        @endif

                    </div>
                </div>
            </div>
            <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 w-full mb-12">
                {{ $slot }}
            </div>

        </main>

    </div>

</div>

{{--@livewireScripts--}}
@livewireScriptConfig
</body>
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.store('darkMode', {
            on: localStorage.getItem('dark-mode') === 'true',
            toggle() {
                this.on = !this.on
                localStorage.setItem('dark-mode', this.on)
                this.updateDOM()
            },
            updateDOM() {
                if (this.on) {
                    document.documentElement.classList.add('dark')
                    document.documentElement.style.colorScheme = 'dark'
                } else {
                    document.documentElement.classList.remove('dark')
                    document.documentElement.style.colorScheme = 'light'
                }
            }
        })
    })
</script>
</html>
