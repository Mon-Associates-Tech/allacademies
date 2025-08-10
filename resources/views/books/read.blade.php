<x-layouts.app :show-title-area="false">
    <section class="min-h-screen relative flex flex-col">
        <header class="sticky top-0 z-10 bg-white flex justify-between px-4 items-center py-4 shadow-md">
            <a onclick="history.back(); return false" href="javascript:void(0)"  class="flex items-center text-blue-600 hover:text-blue-800">
                <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
                Back
            </a>
            <h1 class="text-2xl font-bold text-center flex-1 px-4 truncate">{{ $book->title }}</h1>
            <div class="w-10"></div> <!-- Spacer for alignment -->
        </header>
            <iframe
                src="{{ $book->content_url }}"
                class="sticky top-[360px] z-0 w-full" style="height: 100vh;"
                frameborder="0">
            </iframe>
    </section>
</x-layouts.app>

{{--<x-layouts.app>--}}
{{--    <div class="min-h-screen bg-gray-100">--}}
{{--        <livewire:common.p-d-f-reader-component :book-id="$book->id" />--}}
{{--    </div>--}}

{{--    <script>--}}
{{--        // Auto-open the reader when page loads--}}
{{--        document.addEventListener('DOMContentLoaded', function() {--}}
{{--            window.Livewire.dispatch('openPDFReader', {{ $book->id }});--}}

{{--        });--}}
{{--    </script>--}}
{{--</x-layouts.app>--}}
