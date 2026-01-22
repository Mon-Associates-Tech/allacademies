<x-layouts.app>
        @if(auth()->check() && !$has_token_subscription ?? false)
        <x-alert.token-subscription-banner />
    @else
    <div class="max-w-5xl mx-auto  lg:px-8">
        {{-- Header Section --}}
        <div class="page-header-sky rounded-t-xl overflow-visible">
            <div class="px-2 py-3 sm:px-6 sm:py-6 border-b border-gray-100 dark:border-gray-700">
                <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-3 md:gap-4">
                    {{-- Title & Meta --}}
                    <div class="flex-1 min-w-0 order-1 md:order-none">
                        <h1 class="text-xl md:text-2xl font-bold text-gray-900 dark:text-white mb-2 md:mb-3 break-words md:whitespace-normal">
                            {{ $note->title }}
                        </h1>

                        <div class="flex flex-wrap items-center gap-2 md:gap-3 text-sm text-gray-600 dark:text-gray-400">
                            {{-- Author --}}
                            <div class="flex items-center gap-2">
                                <x-avatar class="h-8 w-8" :name="$note->user->name" :avatar="$note->user->avatar"/>
                                <span
                                    class="font-medium text-gray-700 dark:text-gray-300">{{ $note->user->name }}</span>
                            </div>

                            <span class="text-gray-300 dark:text-gray-600">•</span>

                            {{-- Created Date --}}
                            <div class="flex items-center gap-1.5">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                <span>{{ $note->created_at->format('M d, Y') }}</span>
                            </div>

                            {{-- Updated indicator --}}
                            @if($note->created_at != $note->updated_at)
                                <span class="text-gray-300 dark:text-gray-600">•</span>
                                <span class="italic">Updated {{ $note->updated_at->diffForHumans() }}</span>
                            @endif
                        </div>

                        {{-- Badges --}}
                        <div class="flex flex-wrap gap-2 mt-2 md:mt-3">
                            @if($note->academicSubject)
                                <span
                                    class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-indigo-50 text-indigo-700 ring-1 ring-inset ring-indigo-700/10 dark:bg-indigo-500/10 dark:text-indigo-400 dark:ring-indigo-500/20">
                                    <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor"
                                         viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    {{ $note->academicSubject->name }}
                                </span>
                            @endif

                            @if($note->book)
                                <span
                                    class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-50 text-blue-700 ring-1 ring-inset ring-blue-700/10 dark:bg-blue-500/10 dark:text-blue-400 dark:ring-blue-500/20">
                                    <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor"
                                         viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                    </svg>
                                    {{ $note->book->title }}
                                </span>
                            @endif

                            @if($note->is_public)
                                <span
                                    class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-50 text-green-700 ring-1 ring-inset ring-green-600/20 dark:bg-green-500/10 dark:text-green-400 dark:ring-green-500/20">
                                    <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor"
                                         viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    Public
                                </span>
                            @else
                                <span
                                    class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-700 ring-1 ring-inset ring-gray-500/20 dark:bg-gray-700 dark:text-gray-300 dark:ring-gray-500/20">
                                    <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor"
                                         viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                    </svg>
                                    Private
                                </span>
                            @endif
                        </div>
                    </div>

                    {{-- Action Buttons --}}
                    <div class="flex flex-wrap items-center gap-2 order-2 md:order-none w-full md:w-auto mt-1 md:ml-6 md:flex-none">
                        {{-- Prev / Next Navigation --}}
                        @if(isset($previousNote) && $previousNote)
                            <a href="{{ route('notes.show', $previousNote) }}"
                               class="inline-flex items-center px-3 sm:px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
                               aria-label="Previous note">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M15 19l-7-7 7-7"/>
                                </svg>
                                <span class="hidden xs:inline">Previous</span>
                                <span class="sr-only">Previous</span>
                            </a>
                        @endif

                        @if(isset($nextNote) && $nextNote)
                            <a href="{{ route('notes.show', $nextNote) }}"
                               class="inline-flex items-center px-3 sm:px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
                               aria-label="Next note">
                                <span class="sr-only">Next</span>
                                <span class="hidden xs:inline mr-2">Next</span>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M9 5l7 7-7 7"/>
                                </svg>
                            </a>
                        @endif

                        {{-- Mobile Actions Dropdown (sm and below) --}}
                        <div class="relative md:hidden" x-data="{ open: false }">
                            <button @click="open = !open" @click.outside="open = false" :aria-expanded="open.toString()"
                                    class="inline-flex items-center px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6h.01M12 12h.01M12 18h.01"/>
                                </svg>
                                Actions
                                <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>
                            <template x-teleport="body">
                                <div x-show="open"
                                     x-transition:enter="transition ease-out duration-100"
                                     x-transition:enter-start="transform opacity-0 scale-95"
                                     x-transition:enter-end="transform opacity-100 scale-100"
                                     x-transition:leave="transition ease-in duration-75"
                                     x-transition:leave-start="transform opacity-100 scale-100"
                                     x-transition:leave-end="transform opacity-0 scale-95"
                                     @keydown.escape.window="open=false"
                                     class="fixed inset-x-4 top-16 z-50 min-w-[16rem] sm:min-w-[14rem] rounded-lg shadow-xl bg-white dark:bg-gray-800 p-1 max-h-[70vh] overflow-auto"
                                     role="menu" aria-orientation="vertical" :aria-hidden="(!open).toString()">
                                    <div class="py-1">
                                      {{-- Download submenu entries --}}
                                    <a href="{{ route('notes.download', ['note' => $note, 'format' => 'pdf']) }}" class="flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700" role="menuitem">
                                        <svg class="w-5 h-5 mr-3 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                                        <div>
                                            <div class="font-medium">Save as PDF Document</div>
                                            <div class="text-xs text-gray-500 dark:text-gray-400">Starts download • Best for printing</div>
                                        </div>
                                    </a>
                                    <a href="{{ route('notes.download', ['note' => $note, 'format' => 'docx']) }}" class="flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700" role="menuitem">
                                        <svg class="w-5 h-5 mr-3 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                        <div>
                                            <div class="font-medium">Save as Word Document</div>
                                            <div class="text-xs text-gray-500 dark:text-gray-400">Starts download • Editable format</div>
                                        </div>
                                    </a>
                                    <a href="{{ route('notes.download', ['note' => $note, 'format' => 'txt']) }}" class="flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700" role="menuitem">
                                        <svg class="w-5 h-5 mr-3 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                        <div>
                                            <div class="font-medium">Save as Plain Text</div>
                                            <div class="text-xs text-gray-500 dark:text-gray-400">Starts download • Simple format</div>
                                        </div>
                                    </a>
                                    @if($note->canUserEdit(Auth::id()))
                                        <a href="{{ route('notes.edit', $note) }}" class="flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700" role="menuitem">
                                            <svg class="w-5 h-5 mr-3 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                            Edit
                                        </a>
                                    @endif
                                    @if($note->book)
                                        <a href="{{ route('books.show', $note->book) }}" class="flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700" role="menuitem">
                                            <svg class="w-5 h-5 mr-3 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                                            View Book
                                        </a>
                                    @endif
                                    <a href="{{ route('notes.index') }}" class="flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700" role="menuitem">
                                        <svg class="w-5 h-5 mr-3 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                                        Back to list
                                    </a>
                                </div>
                                </div>
                            </template>
                        </div>

                        {{-- Desktop/Tablet visible actions (md and up) --}}
                        <div class="hidden md:flex md:flex-wrap md:gap-2">
                            {{-- Download Dropdown (md+) --}}
                            <div x-data="{ open: false }" class="relative">
                                <button @click="open = !open" @click.away="open = false" :aria-expanded="open.toString()"
                                        class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    Download
                                    <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M19 9l-7 7-7-7"/>
                                    </svg>
                                </button>

                                <div x-show="open"
                                     x-transition:enter="transition ease-out duration-100"
                                     x-transition:enter-start="transform opacity-0 scale-95"
                                     x-transition:enter-end="transform opacity-100 scale-100"
                                     x-transition:leave="transition ease-in duration-75"
                                     x-transition:leave-start="transform opacity-100 scale-100"
                                     x-transition:leave-end="transform opacity-0 scale-95"
                                     class="absolute right-0 mt-2 w-56 rounded-lg shadow-lg bg-white dark:bg-gray-800 ring-1 ring-black ring-opacity-5 z-50">
                                    <div class="py-1">
                                        <a href="{{ route('notes.download', ['note' => $note, 'format' => 'pdf']) }}"
                                           class="flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                            <svg class="w-5 h-5 mr-3 text-red-500" fill="none" stroke="currentColor"
                                                 viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                            </svg>
                                            <div>
                                                <div class="font-medium">Save as PDF Document</div>
                                                <div class="text-xs text-gray-500 dark:text-gray-400">Starts download • Best for printing
                                                </div>
                                            </div>
                                        </a>

                                        <a href="{{ route('notes.download', ['note' => $note, 'format' => 'docx']) }}"
                                           class="flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                            <svg class="w-5 h-5 mr-3 text-blue-500" fill="none" stroke="currentColor"
                                                 viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                            </svg>
                                            <div>
                                                <div class="font-medium">Save as Word Document</div>
                                                <div class="text-xs text-gray-500 dark:text-gray-400">Starts download • Editable format</div>
                                            </div>
                                        </a>

                                        <a href="{{ route('notes.download', ['note' => $note, 'format' => 'txt']) }}"
                                           class="flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                            <svg class="w-5 h-5 mr-3 text-gray-500" fill="none" stroke="currentColor"
                                                 viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                            </svg>
                                            <div>
                                                <div class="font-medium">Save as Plain Text</div>
                                                <div class="text-xs text-gray-500 dark:text-gray-400">Starts download • Simple format</div>
                                            </div>
                                        </a>
                                    </div>
                                </div>
                            </div>

                            @if($note->canUserEdit(Auth::id()))
                                <a href="{{ route('notes.edit', $note) }}"
                                   class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                    </svg>
                                    Edit
                                </a>
                            @endif

                            @if($note->book)
                                <a href="{{ route('books.show', $note->book) }}"
                                   class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                    </svg>
                                    View Book
                                </a>
                            @endif

                            <a href="{{ route('notes.index') }}"
                               class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                                </svg>
                                Back
                            </a>
                        </div>

                    </div>
                </div>
            </div>
        </div>

        {{-- Content Area --}}
        <div class="border-x border-gray-200 dark:border-gray-700 shadow-sm {{ $note->getBackgroundClass() }}">
            <div class="px-6 py-8 sm:px-8">
                <x-prose-content :content="$note->content"/>
            </div>
        </div>

        {{-- Attachments Section --}}
        <div class="border-x border-b border-gray-200 dark:border-gray-700 shadow-sm {{ $note->getBackgroundClass() }}">
            <div class="px-6 py-6 sm:px-8">
                @livewire('notes.note-attachment-manager', ['note' => $note])
            </div>
        </div>

        {{-- Share Section - Only for note owner --}}
        @if($note->user_id === Auth::id())
            <div x-data="{ shareOpen: true }"
                 class="bg-gray-50 dark:bg-gray-800/50 rounded-b-xl border border-t-0 border-gray-200 dark:border-gray-700 shadow-sm">
                {{-- Collapsible Header --}}
                <button @click="shareOpen = !shareOpen" type="button"
                        class="w-full px-4 sm:px-6 py-4 sm:py-6 flex items-center justify-between hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                    <div class="flex items-center gap-3">
                        <div class="flex-shrink-0">
                            <div
                                class="h-10 w-10 rounded-lg bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center">
                                <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor"
                                     viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/>
                                </svg>
                            </div>
                        </div>
                        <div class="text-left">
                            <h3 class="text-base sm:text-lg font-semibold text-gray-900 dark:text-white">Share with
                                Others</h3>
                            <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400 mt-0.5">Collaborate by sharing
                                this note</p>
                        </div>
                    </div>
                    <svg x-show="!shareOpen" class="w-5 h-5 text-gray-400 flex-shrink-0" fill="none"
                         stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                    <svg x-show="shareOpen" class="w-5 h-5 text-gray-400 flex-shrink-0" fill="none"
                         stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/>
                    </svg>
                </button>

                {{-- Collapsible Content --}}
                <div x-show="shareOpen"
                     x-collapse
                     class="px-4 sm:px-6 pb-6 sm:pb-8">
                    {{-- Livewire Share Component --}}
                    @livewire('notes.share-note', ['note' => $note])

                    {{-- Livewire Shared With List Component --}}
                    @livewire('notes.shared-with-list', ['note' => $note])
                </div>
            </div>
        @endif
    </div>
    @endif
</x-layouts.app>
