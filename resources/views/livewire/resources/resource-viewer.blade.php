<div>
    <!-- Back Button -->
    <div class="mb-6">
        <a href="{{ route('educational-resources.index') }}" class="inline-flex items-center text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition-colors">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Back to Media Center
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Resource Preview -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                <!-- Preview Area -->
                <div class="relative {{ $this->formatBgColor }} p-8 flex items-center justify-center min-h-[300px]">
                    @switch($resource->format)
                        @case('video')
                            <video
                                controls
                                class="max-w-full max-h-[500px] rounded-lg shadow-lg"
                                preload="metadata"
                            >
                                <source src="{{ route('educational-resources.stream', $resource) }}" type="{{ $resource->file_type }}">
                                Your browser does not support the video tag.
                            </video>
                            @break

                        @case('pdf')
                            <div class="w-full">
                                <iframe
                                    src="{{ route('educational-resources.stream', $resource) }}"
                                    class="w-full h-[500px] rounded-lg shadow-lg bg-white"
                                    title="{{ $resource->title }}"
                                ></iframe>
                            </div>
                            @break

                        @case('image')
                            <img
                                src="{{ route('educational-resources.stream', $resource) }}"
                                alt="{{ $resource->title }}"
                                class="max-w-full max-h-[500px] rounded-lg shadow-lg object-contain"
                            >
                            @break

                        @default
                            <div class="text-center">
                                <div class="{{ $this->formatColor }} mb-4">
                                    <svg class="w-24 h-24 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                </div>
                                <p class="text-gray-600 dark:text-gray-400 mb-4">This file type cannot be previewed in the browser.</p>
                                <a
                                    href="{{ route('educational-resources.download', $resource) }}"
                                    class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors"
                                >
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                    </svg>
                                    Download to View
                                </a>
                            </div>
                    @endswitch
                </div>

                <!-- Resource Info -->
                <div class="p-6">
                    <div class="flex items-start justify-between mb-4">
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">{{ $resource->title }}</h1>
                            <div class="flex items-center gap-4 text-sm text-gray-500 dark:text-gray-400">
                                <span class="flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                    {{ $resource->uploader->name ?? 'Unknown' }}
                                </span>
                                <span class="flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                    {{ $resource->created_at->format('M d, Y') }}
                                </span>
                            </div>
                        </div>

                        <!-- Format Badge -->
                        <span class="px-3 py-1 text-sm font-medium rounded-full
                            @switch($resource->format)
                                @case('video') bg-red-100 text-red-800 dark:bg-red-900/50 dark:text-red-300 @break
                                @case('pdf') bg-orange-100 text-orange-800 dark:bg-orange-900/50 dark:text-orange-300 @break
                                @case('image') bg-green-100 text-green-800 dark:bg-green-900/50 dark:text-green-300 @break
                                @default bg-blue-100 text-blue-800 dark:bg-blue-900/50 dark:text-blue-300
                            @endswitch
                        ">
                            {{ ucfirst($resource->format) }}
                        </span>
                    </div>

                    <!-- Description -->
                    @if($resource->description)
                        <x-prose-content :content="$resource->description" class="mb-6" />
                    @endif

                    <!-- Tags -->
                    @if($resource->tags && count($resource->tags) > 0)
                        <div class="mb-6">
                            <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Tags</h3>
                            <div class="flex flex-wrap gap-2">
                                @foreach($resource->tags as $tag)
                                    <a
                                        href="{{ route('educational-resources.index', ['tagSearch' => $tag]) }}"
                                        class="px-3 py-1 text-sm bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-full hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors"
                                    >
                                        {{ $tag }}
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Action Buttons -->
                    <div class="flex flex-wrap gap-3 pt-4 border-t border-gray-200 dark:border-gray-700">
                        <a
                            href="{{ route('educational-resources.download', $resource) }}"
                            class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors"
                        >
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                            </svg>
                            Download
                        </a>

                        @if($resource->format === 'video' || $resource->format === 'pdf')
                            <a
                                href="{{ route('educational-resources.stream', $resource) }}"
                                target="_blank"
                                class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
                            >
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                                </svg>
                                Open in New Tab
                            </a>
                        @endif

                        @if($this->canEdit())
                            <a
                                href="{{ route('educational-resources.edit', $resource) }}"
                                class="inline-flex items-center px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white rounded-lg transition-colors"
                            >
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                                Edit
                            </a>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Topics & Subtopics -->
            @if($resource->topics->count() > 0 || $resource->subtopics->count() > 0)
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Related Topics</h2>

                    @if($resource->topics->count() > 0)
                        <div class="mb-4">
                            <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Topics</h3>
                            <div class="flex flex-wrap gap-2">
                                @foreach($resource->topics as $topic)
                                    <a
                                        href="{{ route('educational-resources.index', ['topicId' => $topic->id]) }}"
                                        class="px-3 py-1 text-sm bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300 rounded-full hover:bg-purple-200 dark:hover:bg-purple-900/50 transition-colors"
                                    >
                                        {{ $topic->name }}
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    @if($resource->subtopics->count() > 0)
                        <div>
                            <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Subtopics</h3>
                            <div class="flex flex-wrap gap-2">
                                @foreach($resource->subtopics as $subtopic)
                                    <a
                                        href="{{ route('educational-resources.index', ['subtopicId' => $subtopic->id]) }}"
                                        class="px-3 py-1 text-sm bg-indigo-100 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-300 rounded-full hover:bg-indigo-200 dark:hover:bg-indigo-900/50 transition-colors"
                                    >
                                        {{ $subtopic->name }}
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Resource Details -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Details</h2>

                <dl class="space-y-4">
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">File Name</dt>
                        <dd class="text-sm text-gray-900 dark:text-white mt-1 break-all">{{ $resource->file_name }}</dd>
                    </div>

                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">File Size</dt>
                        <dd class="text-sm text-gray-900 dark:text-white mt-1">{{ $resource->formatted_file_size }}</dd>
                    </div>

                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">File Type</dt>
                        <dd class="text-sm text-gray-900 dark:text-white mt-1">{{ $resource->file_type }}</dd>
                    </div>

                    <div class="pt-4 border-t border-gray-200 dark:border-gray-700">
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Academic Group</dt>
                        <dd class="text-sm text-gray-900 dark:text-white mt-1">
                            {{ $resource->academicSubject->academicLevel->academicGroup->name ?? 'N/A' }}
                        </dd>
                    </div>

                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Academic Level</dt>
                        <dd class="text-sm text-gray-900 dark:text-white mt-1">
                            {{ $resource->academicSubject->academicLevel->name ?? 'N/A' }}
                        </dd>
                    </div>

                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Subject</dt>
                        <dd class="text-sm text-gray-900 dark:text-white mt-1">
                            <a
                                href="{{ route('educational-resources.index', ['academicSubjectId' => $resource->academic_subject_id]) }}"
                                class="text-blue-600 dark:text-blue-400 hover:underline"
                            >
                                {{ $resource->academicSubject->name ?? 'N/A' }}
                            </a>
                        </dd>
                    </div>

                    @if($resource->school)
                        <div class="pt-4 border-t border-gray-200 dark:border-gray-700">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">School</dt>
                            <dd class="text-sm text-gray-900 dark:text-white mt-1">{{ $resource->school->name }}</dd>
                        </div>
                    @endif

                    <div class="pt-4 border-t border-gray-200 dark:border-gray-700">
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Views</dt>
                        <dd class="text-sm text-gray-900 dark:text-white mt-1">{{ number_format($resource->view_count) }}</dd>
                    </div>

                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Downloads</dt>
                        <dd class="text-sm text-gray-900 dark:text-white mt-1">{{ number_format($resource->download_count) }}</dd>
                    </div>
                </dl>
            </div>

            <!-- Related Resources -->
            @if($relatedResources->count() > 0)
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Related Resources</h2>

                    <div class="space-y-4">
                        @foreach($relatedResources as $related)
                            <a href="{{ route('educational-resources.show', $related) }}" class="flex items-start gap-3 group">
                                <div class="flex-shrink-0 w-10 h-10 rounded-lg flex items-center justify-center
                                    @switch($related->format)
                                        @case('video') bg-red-100 dark:bg-red-900/30 text-red-500 @break
                                        @case('pdf') bg-orange-100 dark:bg-orange-900/30 text-orange-500 @break
                                        @case('image') bg-green-100 dark:bg-green-900/30 text-green-500 @break
                                        @default bg-blue-100 dark:bg-blue-900/30 text-blue-500
                                    @endswitch
                                ">
                                    @switch($related->format)
                                        @case('video')
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            @break
                                        @case('pdf')
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                            </svg>
                                            @break
                                        @case('image')
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                            </svg>
                                            @break
                                        @default
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                            </svg>
                                    @endswitch
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h3 class="text-sm font-medium text-gray-900 dark:text-white group-hover:text-blue-600 dark:group-hover:text-blue-400 line-clamp-2">
                                        {{ $related->title }}
                                    </h3>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                        {{ $related->academicSubject->name ?? 'N/A' }}
                                    </p>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
