@props(['title' => null, 'action' => null, 'breadcrumb' => null])
<x-layouts.app>
    <div class="w-full min-h-screen flex flex-col h-full mx-auto overflow-y-visible max-w-7xl px-2 lg:px-0">
        <main class="flex-grow">
            <div>
                {{ $slot }}
            </div>
        </main>
    </div>
</x-layouts.app>
