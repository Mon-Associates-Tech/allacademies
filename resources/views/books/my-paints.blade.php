<x-layouts.app page-name="My Paintings" :show-title-area="false">
    <div class="max-w-7xl mx-auto px-4 py-8">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">My Paintings</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Your saved paint sessions across all books</p>
            </div>
            @if($paints->isNotEmpty())
                <a href="{{ route('books.my-paints') }}?download_all=1"
                   id="download-all"
                   class="inline-flex items-center gap-2 px-4 py-2 bg-violet-600 hover:bg-violet-700 text-white text-sm font-medium rounded-lg transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    Download All
                </a>
            @endif
        </div>

        @if($paints->isEmpty())
            <div class="text-center py-20 text-gray-400 dark:text-gray-500">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-16 h-16 mx-auto mb-4 opacity-40" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                </svg>
                <p class="text-lg font-medium">No paintings yet</p>
                <p class="text-sm mt-1">Open a book and use the Paint tool to get started</p>
                <a href="{{ route('books.index') }}" class="mt-4 inline-block text-violet-600 hover:underline text-sm">Browse Books</a>
            </div>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                @foreach($paints as $paint)
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden group">
                        <div class="relative aspect-[3/4] bg-gray-100 dark:bg-gray-900 overflow-hidden">
                            <img src="/storage/{{ $paint->image_path }}"
                                 alt="Page {{ $paint->page + 1 }} of {{ $paint->book->title }}"
                                 class="w-full h-full object-contain">
                        </div>
                        <div class="p-3">
                            <p class="text-sm font-semibold text-gray-800 dark:text-white truncate" title="{{ $paint->book->title }}">
                                {{ $paint->book->title }}
                            </p>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                                Page {{ $paint->page + 1 }} &middot; {{ $paint->updated_at->diffForHumans() }}
                            </p>
                            <div class="flex items-center gap-2 mt-3">
                                <a href="{{ route('books.paint', $paint->book) }}?imageUrl={{ urlencode(route('books.pdf-page-png', $paint->book) . '?page=' . $paint->page) }}"
                                   class="flex-1 text-center text-xs px-3 py-1.5 bg-gray-100 dark:bg-gray-700 hover:bg-violet-100 dark:hover:bg-violet-900 text-gray-700 dark:text-gray-200 rounded-lg transition">
                                    Edit
                                </a>
                                <a href="/storage/{{ $paint->image_path }}"
                                   download="{{ $paint->book->title }}-page-{{ $paint->page + 1 }}.png"
                                   class="flex-1 text-center text-xs px-3 py-1.5 bg-violet-600 hover:bg-violet-700 text-white rounded-lg transition">
                                    Download
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    @if($paints->isNotEmpty())
    <script>
        document.getElementById('download-all')?.addEventListener('click', async (e) => {
            e.preventDefault();
            const links = @json($paints->map(fn($p) => [
                'url' => '/storage/' . $p->image_path,
                'name' => $p->book->title . '-page-' . ($p->page + 1) . '.png',
            ]));
            for (const { url, name } of links) {
                const a = document.createElement('a');
                a.href = url;
                a.download = name;
                document.body.appendChild(a);
                a.click();
                document.body.removeChild(a);
                await new Promise(r => setTimeout(r, 300));
            }
        });
    </script>
    @endif
</x-layouts.app>
