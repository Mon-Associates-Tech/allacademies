<x-layouts.app>
    {{-- A more dynamic and futuristic background --}}
    <div class="min-h-screen bg-gray-900 bg-gradient-to-br from-gray-900 via-gray-800 to-indigo-900/50 text-white">

        {{-- Main Content Area --}}
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

            {{-- Back Navigation --}}
            <div class="mb-8">
                <a href="{{ route('books.index') }}" class="inline-flex items-center gap-2 text-sm font-medium text-gray-400 hover:text-white transition-all group">
                    <svg class="w-5 h-5 transform group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                    <span>Back to Galactic Library</span>
                </a>
            </div>

            <div class="lg:grid lg:grid-cols-5 lg:gap-12">

                {{-- Left Column: Book Cover & Progress --}}
                <div class="lg:col-span-2">
                    <div class="relative group">
                        {{-- Adding a glow effect for the futuristic feel --}}
                        <div class="absolute -inset-0.5 bg-gradient-to-r from-purple-600 to-blue-500 rounded-2xl blur-lg opacity-25 group-hover:opacity-50 transition duration-1000 group-hover:duration-200 animate-tilt"></div>
                        <div class="relative aspect-[3/4] bg-gray-800/60 rounded-2xl overflow-hidden shadow-2xl border border-gray-700">
                            @if($book->cover_image)
                                <img src="{{ $book->cover_image }}" alt="{{ $book->title }}" class="w-full h-full object-cover transition-transform group-hover:scale-105 duration-300">
                            @else
                                {{-- A more stylish placeholder --}}
                                <div class="w-full h-full flex flex-col items-center justify-center text-center text-gray-500 p-4">
                                    <svg class="w-16 h-16 mb-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.25 12l8.954-8.955a1.125 1.125 0 011.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h7.5"/>
                                    </svg>
                                    <h3 class="font-bold text-gray-400">{{ $book->title }}</h3>
                                    <p class="text-xs">Cover not available</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Reading Progress Bar --}}
                    @if($isSubscribed || !$book->annual_subscription_fee)
                        <div class="mt-8">
                            <h4 class="text-sm font-medium text-gray-400 mb-2">Reading Progress</h4>
                            <div class="w-full bg-gray-700/50 rounded-full h-2.5">
                                <div class="bg-gradient-to-r from-blue-500 to-cyan-400 h-2.5 rounded-full shadow-lg shadow-blue-500/20" style="width: {{ $book->reading_progress ?? 0 }}%"></div>
                            </div>
                            <div class="text-xs text-right text-gray-500 mt-1">{{ $book->reading_progress ?? 0 }}% Complete</div>
                        </div>
                    @endif
                </div>

                {{-- Right Column: Book Details & Actions --}}
                <div class="lg:col-span-3 mt-8 lg:mt-0">
                    {{-- Category and Author --}}
                    <div class="flex items-center gap-4 text-sm font-medium text-gray-400">
                        <span>{{ $book->category->name ?? 'Uncategorized' }}</span>
                        <span class="text-gray-600">|</span>
                        <span>By {{ $book->author->name ?? 'Unknown Author' }}</span>
                    </div>

                    {{-- Book Title --}}
                    <h1 class="text-4xl lg:text-5xl font-bold tracking-tight text-transparent bg-clip-text bg-gradient-to-r from-gray-100 to-gray-300 mt-2 mb-6">
                        {{ $book->title }}
                    </h1>

                    {{-- Action Buttons --}}
                    <div class="my-8">
                        @if($isSubscribed || !$book->annual_subscription_fee)
                            <x-button href="{{ route('books.read', $book) }}" color="primary" size="xl" class="w-full sm:w-auto !py-4 !px-8 group">
                                <span class="group-hover:drop-shadow-[0_0_8px_rgba(255,255,255,0.4)] transition-all">Continue Reading</span>
                                <svg class="w-5 h-5 ml-2 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" /></svg>
                            </x-button>
                        @else
                            {{-- Fancy subscribe button --}}
                            <form action="{{ route('books.subscribe', $book) }}" method="POST">
                                @csrf
                                <button type="submit" class="w-full sm:w-auto inline-flex items-center justify-center px-8 py-4 font-bold text-white transition-all duration-300 bg-gradient-to-r from-purple-600 to-blue-500 rounded-lg hover:from-purple-700 hover:to-blue-600 hover:scale-105 focus:ring-4 focus:ring-purple-300 dark:focus:ring-purple-800">
                                    <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z" /></svg>
                                    Subscribe for ${{ number_format($book->annual_subscription_fee, 2) }} / year
                                </button>
                            </form>
                        @endif
                    </div>

                    {{-- Book Description --}}
                    <div class="prose prose-invert prose-p:text-gray-300">
                        <h3 class="text-white">Synopsis</h3>
                        <p>{{ $book->description ?? 'No description available for this book.' }}</p>
                    </div>

                    {{-- Book Stats --}}
                    <div class="mt-10 pt-6 border-t border-gray-800">
                        <h3 class="text-lg font-semibold text-white mb-4">Book Data</h3>
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-4 text-sm">
                            <div class="bg-gray-800/50 p-4 rounded-lg">
                                <div class="font-bold text-gray-400 uppercase text-xs tracking-wider">Published</div>
                                <div class="text-lg font-semibold mt-1">{{ $book->published_at ? $book->published_at->format('Y') : 'N/A' }}</div>
                            </div>
                            <div class="bg-gray-800/50 p-4 rounded-lg">
                                <div class="font-bold text-gray-400 uppercase text-xs tracking-wider">Pages</div>
                                <div class="text-lg font-semibold mt-1">{{ $book->pages ?? 'N/A' }}</div>
                            </div>
                            <div class="bg-gray-800/50 p-4 rounded-lg">
                                <div class="font-bold text-gray-400 uppercase text-xs tracking-wider">Est. Reading Time</div>
                                {{-- You may need to calculate this value in your controller or model --}}
                                <div class="text-lg font-semibold mt-1">{{ $book->estimated_reading_time ?? '5h 30m' }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>

{{-- Add this to your main layout or push to a stack to enable the tilt animation --}}
@push('styles')
    <style>
        @keyframes tilt {
            0%, 50%, 100% {
                transform: rotate(0deg);
            }
            25% {
                transform: rotate(0.5deg);
            }
            75% {
                transform: rotate(-0.5deg);
            }
        }
        .animate-tilt {
            animation: tilt 10s infinite linear;
        }
    </style>
@endpush
