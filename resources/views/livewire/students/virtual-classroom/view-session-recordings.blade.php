<div class="min-h-screen bg-gray-50 dark:bg-gray-900">
    <div class="px-4 py-8 mx-auto max-w-7xl sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
                Session Recordings
            </h1>
            <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                Watch recorded virtual classroom sessions
            </p>
        </div>

        <!-- Search and Filter -->
        <div class="mb-6">
            <div class="flex flex-col gap-4 sm:flex-row">
                <div class="relative flex-1">
                    <input wire:model.live.debounce.300ms="search"
                           type="text"
                           placeholder="Search recordings..."
                           class="w-full px-4 py-2 pr-10 text-sm border border-gray-300 rounded-lg focus:ring-violet-500 focus:border-violet-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    <svg class="absolute w-5 h-5 text-gray-400 transform -translate-y-1/2 right-3 top-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>

                @if($subject_filter)
                    <select wire:model.live="subject_filter"
                            class="px-4 py-2 text-sm border border-gray-300 rounded-lg focus:ring-violet-500 focus:border-violet-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        <option value="">All Subjects</option>
                        <!-- Populate with subjects -->
                    </select>
                @endif
            </div>
        </div>

        <!-- Recordings Grid -->
        <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
            @forelse($recordings as $recording)
                @php $session = $recording->virtualSession; @endphp
                <div class="overflow-hidden transition-shadow bg-white rounded-lg shadow hover:shadow-lg dark:bg-gray-800">
                    <!-- Thumbnail -->
                    <div class="relative bg-gray-900 aspect-video">
                        @if($recording->thumbnail_path)
                            <img src="{{ Storage::url($recording->thumbnail_path) }}"
                                 alt="{{ $recording->name }}"
                                 class="object-cover w-full h-full">
                        @else
                            <div class="flex items-center justify-center w-full h-full">
                                <svg class="w-16 h-16 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                </svg>
                            </div>
                        @endif

                        <!-- Duration Badge -->
                        @if($recording->duration_seconds)
                            <div class="absolute px-2 py-1 text-xs font-medium text-white bg-black bg-opacity-75 rounded bottom-2 right-2">
                                {{ $recording->getFormattedDuration() }}
                            </div>
                        @endif

                        <!-- Play Button Overlay -->
                        <div class="absolute inset-0 flex items-center justify-center transition-opacity opacity-0 bg-black bg-opacity-50 hover:opacity-100">
                            <svg class="w-16 h-16 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M6.3 2.841A1.5 1.5 0 004 4.11V15.89a1.5 1.5 0 002.3 1.269l9.344-5.89a1.5 1.5 0 000-2.538L6.3 2.84z"/>
                            </svg>
                        </div>
                    </div>

                    <!-- Content -->
                    <div class="p-6">
                        <h3 class="mb-2 text-lg font-semibold text-gray-900 dark:text-white line-clamp-2">
                            {{ $recording->name }}
                        </h3>

                        <p class="mb-4 text-sm text-gray-600 dark:text-gray-400">
                            {{ $session->teacher->user->name }}
                        </p>

                        <!-- Info -->
                        <div class="space-y-2 text-sm text-gray-600 dark:text-gray-400">
                            <div class="flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                {{ $recording->recorded_at->format('M d, Y') }}
                            </div>

                            @if($session->academicSubject)
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                    </svg>
                                    {{ $session->academicSubject->name }}
                                </div>
                            @endif

                            @if($recording->size_bytes)
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                    </svg>
                                    {{ $recording->getFormattedSize() }}
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Footer / Actions -->
                    <div class="px-6 py-4 bg-gray-50 dark:bg-gray-700">
                        <a href="{{ $recording->playback_url }}"
                           target="_blank"
                           class="w-full inline-flex justify-center items-center px-4 py-2 text-sm font-medium text-white bg-violet-600 rounded-lg hover:bg-violet-700">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Watch Recording
                        </a>
                    </div>
                </div>
            @empty
                <div class="col-span-full">
                    <div class="py-12 text-center bg-white rounded-lg shadow dark:bg-gray-800">
                        <svg class="w-12 h-12 mx-auto text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                        </svg>
                        <h3 class="mt-4 text-lg font-medium text-gray-900 dark:text-white">No recordings found</h3>
                        <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                            {{ $search ? 'Try adjusting your search criteria.' : 'Recordings will appear here once sessions are recorded.' }}
                        </p>
                    </div>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if($recordings->hasPages())
            <div class="mt-6">
                {{ $recordings->links() }}
            </div>
        @endif
    </div>
</div>
