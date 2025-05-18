@props(['title' => null, 'action' => null, 'breadcrumb' => null])

<x-app>
    <div class="w-full min-h-screen flex flex-col h-full mx-auto max-w-7xl py-8 px-2 lg:px-0 space-y-5">
        <main class="flex-grow">

            <div>
                {{ $slot }}
            </div>

        </main>

    </div>

</x-app>
