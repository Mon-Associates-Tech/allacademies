<!-- Step 3: Table of Contents -->
<div class="mb-12">
    <div class="flex items-center mb-8">
        <div class="flex items-center justify-center w-10 h-10 bg-blue-600 text-white rounded-full text-sm font-bold mr-4">3</div>
        <div class="flex-1">
            <h3 class="text-xl font-bold text-gray-900">Table of Contents</h3>
            <p class="text-gray-600">Define the structure, chapters, and sections of your book</p>
        </div>
        <div class="flex space-x-3">
            <button type="button" wire:click="toggleTableOfContents"
                    class="px-4 py-2 {{ $showTableOfContents ? 'bg-red-100 text-red-700 hover:bg-red-200' : 'bg-blue-100 text-blue-700 hover:bg-blue-200' }} rounded-lg transition-colors">
                {{ $showTableOfContents ? 'Hide' : 'Show' }} Table of Contents
            </button>
        </div>
    </div>

    @if($showTableOfContents)
        <div class="ml-14 space-y-8">
            <!-- TOC Controls -->
            <div class="bg-gradient-to-r from-indigo-50 to-purple-50 border border-indigo-200 rounded-xl p-4">
                <div class="flex flex-wrap items-center justify-between gap-4">
                    <div class="flex items-center space-x-4">
                        <div class="flex items-center text-sm text-indigo-700">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Quick Actions:
                        </div>
                        <button type="button" wire:click="generateTableOfContents"
                                class="px-3 py-1 text-xs bg-green-100 text-green-700 rounded-full hover:bg-green-200 transition-colors">
                            <svg class="w-3 h-3 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                            </svg>
                            Auto-Generate from Pages
                        </button>
                    </div>

                    <div class="text-sm text-gray-600">
                        @if($pages)
                            Based on {{ $pages }} pages
                        @else
                            <span class="text-amber-600">Set page count first for better auto-generation</span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Chapters -->
            @foreach($tableOfContents as $chapterIndex => $chapter)
                <div class="bg-white border-2 border-gray-200 rounded-2xl shadow-sm hover:shadow-md transition-shadow"
                     wire:key="chapter-{{ $chapterIndex }}">
                    <!-- Chapter Header -->
                    <div class="bg-gradient-to-r from-gray-50 to-blue-50 px-6 py-4 rounded-t-2xl border-b border-gray-200">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-4">
                                <button type="button" wire:click="toggleChapter({{ $chapterIndex }})"
                                        class="flex items-center justify-center w-8 h-8 bg-blue-600 text-white rounded-full text-sm font-bold hover:bg-blue-700 transition-colors">
                                    {{ $chapter['chapter'] }}
                                </button>
                                <div>
                                    <h4 class="text-lg font-bold text-gray-900">Chapter {{ $chapter['chapter'] }}</h4>
                                    <p class="text-sm text-gray-600">
                                        Pages {{ $chapter['page_start'] }}-{{ $chapter['page_end'] }}
                                        @if(!empty($chapter['sections']))
                                            • {{ count($chapter['sections']) }} section(s)
                                        @endif
                                    </p>
                                </div>
                            </div>
                            <div class="flex items-center space-x-2">
                                <button type="button" wire:click="generateSections({{ $chapterIndex }})"
                                        class="px-3 py-1 text-xs bg-green-100 text-green-700 rounded-full hover:bg-green-200 transition-colors"
                                        title="Auto-generate sections for this chapter">
                                    <svg class="w-3 h-3 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                    </svg>
                                    Auto-Sections
                                </button>
                                <button type="button" wire:click="addSection({{ $chapterIndex }})"
                                        class="px-3 py-1 text-xs bg-indigo-100 text-indigo-700 rounded-full hover:bg-indigo-200 transition-colors"
                                        title="Add a new section to this chapter">
                                    <svg class="w-3 h-3 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                    </svg>
                                    Add Section
                                </button>
                                <button type="button" wire:click="toggleChapter({{ $chapterIndex }})"
                                        class="p-1 text-gray-500 hover:text-gray-700 transition-colors"
                                        title="Toggle chapter details">
                                    <svg class="w-4 h-4 transform transition-transform {{ in_array($chapterIndex, $expandedChapters) ? 'rotate-180' : '' }}"
                                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </button>
                                @if(count($tableOfContents) > 1)
                                    <button type="button" wire:click="removeChapter({{ $chapterIndex }})"
                                            class="p-1 text-red-500 hover:text-red-700 transition-colors"
                                            title="Remove this chapter"
                                            onclick="return confirm('Are you sure you want to remove this chapter and all its sections?')">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Chapter Content -->
                    <div class="p-6 space-y-6">
                        <!-- Chapter Details Form -->
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Chapter Number <span class="text-red-500">*</span>
                                </label>
                                <input type="number" wire:model.live="tableOfContents.{{ $chapterIndex }}.chapter" min="1"
                                       class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                                @error("tableOfContents.{$chapterIndex}.chapter")
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="lg:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Chapter Title <span class="text-red-500">*</span>
                                </label>
                                <input type="text" wire:model.live="tableOfContents.{{ $chapterIndex }}.title"
                                       placeholder="Enter chapter title"
                                       class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                                @error("tableOfContents.{$chapterIndex}.title")
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="grid grid-cols-2 gap-2">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">
                                        Start Page <span class="text-red-500">*</span>
                                    </label>
                                    <input type="number" wire:model.live="tableOfContents.{{ $chapterIndex }}.page_start" min="1"
                                           class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                                    @error("tableOfContents.{$chapterIndex}.page_start")
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">
                                        End Page <span class="text-red-500">*</span>
                                    </label>
                                    <input type="number" wire:model.live="tableOfContents.{{ $chapterIndex }}.page_end" min="1"
                                           class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                                    @error("tableOfContents.{$chapterIndex}.page_end")
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Chapter Description -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Chapter Description</label>
                            <textarea wire:model.live="tableOfContents.{{ $chapterIndex }}.description" rows="2"
                                      placeholder="Brief description of what this chapter covers..."
                                      class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 resize-none text-sm"></textarea>
                            @error("tableOfContents.{$chapterIndex}.description")
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Page Range Validation Warning -->
                        @if(isset($chapter['page_start']) && isset($chapter['page_end']) && $chapter['page_start'] >= $chapter['page_end'])
                            <div class="bg-amber-50 border border-amber-200 rounded-lg p-3 flex items-start">
                                <svg class="w-5 h-5 text-amber-500 mt-0.5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                </svg>
                                <div class="text-sm text-amber-800">
                                    <strong>Invalid page range:</strong> End page must be greater than start page.
                                </div>
                            </div>
                        @endif

                        <!-- Sections -->
                        @if(in_array($chapterIndex, $expandedChapters) && !empty($chapter['sections']))
                            <div class="border-t border-gray-200 pt-6">
                                <div class="flex items-center justify-between mb-4">
                                    <h5 class="text-lg font-semibold text-gray-900 flex items-center">
                                        <svg class="w-5 h-5 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                        Sections ({{ count($chapter['sections']) }})
                                    </h5>
                                    <div class="text-sm text-gray-500 bg-gray-100 px-3 py-1 rounded-full">
                                        Chapter spans pages {{ $chapter['page_start'] }}-{{ $chapter['page_end'] }}
                                    </div>
                                </div>

                                <div class="space-y-4">
                                    @foreach($chapter['sections'] as $sectionIndex => $section)
                                        <div class="bg-gradient-to-r from-indigo-50 to-purple-50 border border-indigo-200 rounded-xl p-4"
                                             wire:key="section-{{ $chapterIndex }}-{{ $sectionIndex }}">
                                            <div class="flex items-center justify-between mb-3">
                                                <div class="flex items-center space-x-2">
                                                    <div class="flex items-center justify-center w-6 h-6 bg-indigo-600 text-white rounded-full text-xs font-bold">
                                                        {{ $sectionIndex + 1 }}
                                                    </div>
                                                    <span class="font-medium text-gray-900">Section {{ $sectionIndex + 1 }}</span>
                                                </div>
                                                <button type="button" wire:click="removeSection({{ $chapterIndex }}, {{ $sectionIndex }})"
                                                        class="p-1 text-red-500 hover:text-red-700 transition-colors"
                                                        title="Remove this section"
                                                        onclick="return confirm('Are you sure you want to remove this section?')">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                    </svg>
                                                </button>
                                            </div>

                                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                                <div class="md:col-span-2">
                                                    <label class="block text-sm font-medium text-gray-700 mb-1">
                                                        Section Title <span class="text-red-500">*</span>
                                                    </label>
                                                    <input type="text" wire:model.live="tableOfContents.{{ $chapterIndex }}.sections.{{ $sectionIndex }}.title"
                                                           placeholder="Enter section title"
                                                           class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                                                    @error("tableOfContents.{$chapterIndex}.sections.{$sectionIndex}.title")
                                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                                    @enderror
                                                </div>

                                                <div class="grid grid-cols-2 gap-2">
                                                    <div>
                                                        <label class="block text-sm font-medium text-gray-700 mb-1">
                                                            Start <span class="text-red-500">*</span>
                                                        </label>
                                                        <input type="number"
                                                               wire:model.live="tableOfContents.{{ $chapterIndex }}.sections.{{ $sectionIndex }}.page_start"
                                                               min="{{ $chapter['page_start'] }}"
                                                               max="{{ $chapter['page_end'] }}"
                                                               class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                                                        @error("tableOfContents.{$chapterIndex}.sections.{$sectionIndex}.page_start")
                                                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                                        @enderror
                                                    </div>

                                                    <div>
                                                        <label class="block text-sm font-medium text-gray-700 mb-1">
                                                            End <span class="text-red-500">*</span>
                                                        </label>
                                                        <input type="number"
                                                               wire:model.live="tableOfContents.{{ $chapterIndex }}.sections.{{ $sectionIndex }}.page_end"
                                                               min="{{ $chapter['page_start'] }}"
                                                               max="{{ $chapter['page_end'] }}"
                                                               class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                                                        @error("tableOfContents.{$chapterIndex}.sections.{$sectionIndex}.page_end")
                                                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Section Description -->
                                            <div class="mt-3">
                                                <label class="block text-sm font-medium text-gray-700 mb-1">Section Description</label>
                                                <textarea wire:model.live="tableOfContents.{{ $chapterIndex }}.sections.{{ $sectionIndex }}.description"
                                                          rows="2"
                                                          placeholder="Brief description of what this section covers..."
                                                          class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 resize-none text-sm"></textarea>
                                                @error("tableOfContents.{$chapterIndex}.sections.{$sectionIndex}.description")
                                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                                @enderror
                                            </div>

                                            <!-- Section Validation Warnings -->
                                            @php
                                                $sectionStart = $section['page_start'] ?? 0;
                                                $sectionEnd = $section['page_end'] ?? 0;
                                                $chapterStart = $chapter['page_start'] ?? 0;
                                                $chapterEnd = $chapter['page_end'] ?? 0;
                                            @endphp

                                            @if($sectionStart >= $sectionEnd)
                                                <div class="mt-2 bg-red-50 border border-red-200 rounded-lg p-2 flex items-start">
                                                    <svg class="w-4 h-4 text-red-500 mt-0.5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                    </svg>
                                                    <div class="text-xs text-red-800">
                                                        <strong>Invalid range:</strong> Section end page must be greater than start page.
                                                    </div>
                                                </div>
                                            @elseif($sectionStart < $chapterStart || $sectionEnd > $chapterEnd)
                                                <div class="mt-2 bg-amber-50 border border-amber-200 rounded-lg p-2 flex items-start">
                                                    <svg class="w-4 h-4 text-amber-500 mt-0.5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                                    </svg>
                                                    <div class="text-xs text-amber-800">
                                                        <strong>Out of range:</strong> Section pages must be within chapter range ({{ $chapterStart }}-{{ $chapterEnd }}).
                                                    </div>
                                                </div>
                                            @endif

                                            <!-- Custom validation error from backend -->
                                            @error("tableOfContents.{$chapterIndex}.sections.{$sectionIndex}.page_range")
                                            <div class="mt-2 bg-red-50 border border-red-200 rounded-lg p-2">
                                                <p class="text-xs text-red-800">{{ $message }}</p>
                                            </div>
                                            @enderror
                                        </div>
                                    @endforeach
                                </div>

                                <!-- Add Section Button for expanded chapter -->
                                <div class="mt-4">
                                    <button type="button" wire:click="addSection({{ $chapterIndex }})"
                                            class="w-full flex items-center justify-center py-3 border-2 border-dashed border-indigo-300 rounded-xl text-indigo-600 hover:border-indigo-400 hover:text-indigo-700 hover:bg-indigo-50 transition-all text-sm">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                        </svg>
                                        Add Section to Chapter {{ $chapter['chapter'] }}
                                    </button>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach

            <!-- Add New Chapter Button -->
            <div class="flex space-x-4">
                <button type="button" wire:click="addChapter"
                        class="flex-1 flex items-center justify-center py-6 border-2 border-dashed border-blue-300 rounded-2xl text-blue-600 hover:border-blue-400 hover:text-blue-700 hover:bg-blue-50 transition-all">
                    <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    <div>
                        <div class="font-medium">Add New Chapter</div>
                        <div class="text-sm text-gray-500">Create another chapter for your book</div>
                    </div>
                </button>

                @if($pages && count($tableOfContents) < 10)
                    <button type="button" wire:click="generateTableOfContents"
                            class="px-6 py-6 bg-green-100 text-green-700 hover:bg-green-200 rounded-2xl transition-colors">
                        <div class="text-center">
                            <svg class="w-6 h-6 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                            </svg>
                            <div class="font-medium text-sm">Regenerate TOC</div>
                            <div class="text-xs text-gray-600">Based on {{ $pages }} pages</div>
                        </div>
                    </button>
                @endif
            </div>

            <!-- Table of Contents Summary -->
            @if(!empty($tableOfContents))
                <div class="bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 rounded-2xl p-6">
                    <h5 class="font-semibold text-gray-900 mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                        Table of Contents Summary
                    </h5>

                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 text-sm mb-4">
                        <div class="bg-white rounded-lg p-4 border border-blue-200">
                            <div class="font-medium text-gray-700">Total Chapters</div>
                            <div class="text-2xl font-bold text-blue-600">{{ count($tableOfContents) }}</div>
                        </div>
                        <div class="bg-white rounded-lg p-4 border border-blue-200">
                            <div class="font-medium text-gray-700">Total Sections</div>
                            <div class="text-2xl font-bold text-indigo-600">
                                {{ collect($tableOfContents)->sum(function($chapter) { return count($chapter['sections'] ?? []); }) }}
                            </div>
                        </div>
                        <div class="bg-white rounded-lg p-4 border border-blue-200">
                            <div class="font-medium text-gray-700">Page Coverage</div>
                            <div class="text-2xl font-bold text-purple-600">
                                @if(!empty($tableOfContents))
                                    {{ collect($tableOfContents)->min('page_start') }}-{{ collect($tableOfContents)->max('page_end') }}
                                @else
                                    N/A
                                @endif
                            </div>
                        </div>
                        <div class="bg-white rounded-lg p-4 border border-blue-200">
                            <div class="font-medium text-gray-700">Completion</div>
                            <div class="text-2xl font-bold text-green-600">
                                @php
                                    $completedChapters = collect($tableOfContents)->filter(function($chapter) {
                                        return !empty($chapter['title']) &&
                                               !empty($chapter['page_start']) &&
                                               !empty($chapter['page_end']);
                                    })->count();
                                @endphp
                                {{ $completedChapters }}/{{ count($tableOfContents) }}
                            </div>
                        </div>
                    </div>

                    <!-- TOC Preview -->
                    <div class="bg-white rounded-lg p-4 border border-blue-200">
                        <h6 class="font-medium text-gray-700 mb-3">Preview Structure:</h6>
                        <div class="space-y-2 max-h-48 overflow-y-auto text-sm">
                            @foreach($tableOfContents as $chapterIndex => $chapter)
                                <div class="border-l-2 border-blue-300 pl-3">
                                    <div class="font-medium text-gray-900">
                                        {{ $chapter['chapter'] }}. {{ $chapter['title'] ?? 'Untitled Chapter' }}
                                        <span class="text-gray-500 font-normal">(Pages {{ $chapter['page_start'] ?? '?' }}-{{ $chapter['page_end'] ?? '?' }})</span>
                                    </div>
                                    @if(!empty($chapter['sections']))
                                        <div class="ml-4 mt-1 space-y-1">
                                            @foreach($chapter['sections'] as $sectionIndex => $section)
                                                <div class="text-gray-600 text-xs border-l-2 border-indigo-200 pl-2">
                                                    {{ $chapterIndex + 1 }}.{{ $sectionIndex + 1 }} {{ $section['title'] ?? 'Untitled Section' }}
                                                    <span class="text-gray-400">(Pages {{ $section['page_start'] ?? '?' }}-{{ $section['page_end'] ?? '?' }})</span>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="mt-4 flex flex-wrap gap-2">
                        <button type="button"
                                onclick="document.querySelectorAll('.toc-item').forEach(item => item.classList.remove('hidden'))"
                                class="px-3 py-1 text-xs bg-blue-100 text-blue-700 rounded-full hover:bg-blue-200 transition-colors">
                            Expand All Chapters
                        </button>
                        <button type="button"
                                onclick="document.querySelectorAll('.toc-children').forEach(child => child.classList.add('hidden'))"
                                class="px-3 py-1 text-xs bg-gray-100 text-gray-700 rounded-full hover:bg-gray-200 transition-colors">
                            Collapse All Sections
                        </button>
                        @if(count($tableOfContents) > 3)
                            <button type="button" wire:click="$set('tableOfContents', [])"
                                    onclick="return confirm('Are you sure you want to clear all chapters and sections?')"
                                    class="px-3 py-1 text-xs bg-red-100 text-red-700 rounded-full hover:bg-red-200 transition-colors">
                                Clear All
                            </button>
                        @endif
                    </div>
                </div>
            @endif

            <!-- Help Text -->
            <div class="bg-gray-50 border border-gray-200 rounded-xl p-4">
                <h6 class="font-medium text-gray-800 mb-2 flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Table of Contents Tips:
                </h6>
                <ul class="text-sm text-gray-600 space-y-1">
                    <li>• <strong>Auto-generate:</strong> Set the total page count first, then use "Auto-Generate" for a basic structure</li>
                    <li>• <strong>Page ranges:</strong> Ensure chapter page ranges don't overlap and sections stay within their chapter bounds</li>
                    <li>• <strong>Navigation:</strong> Users can click on chapters and sections to jump directly to those pages when reading</li>
                    <li>• <strong>SEO benefits:</strong> A well-structured table of contents improves book discoverability</li>
                    <li>• <strong>Optional:</strong> You can leave this section empty if you prefer a simpler book structure</li>
                </ul>
            </div>
        </div>
    @endif
</div>
