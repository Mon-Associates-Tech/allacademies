<x-layouts.app>
    <section class="min-h-screen relative flex flex-col">
        <!-- Sticky Header -->
        <header class="sticky top-0 z-10 bg-white flex justify-between px-4 items-center py-4">
            <a onclick="history.back()" href="">Go back</a>
            <h1 class="text-2xl font-bold text-center">{{ $book->title }}</h1>
        </header>

            <iframe
                src="{{ $book->content_url }}"
                class="sticky top-[360px] z-0 w-full" style="height: 100vh;"
                frameborder="0">
            </iframe>
    </section>
</x-layouts.app>
