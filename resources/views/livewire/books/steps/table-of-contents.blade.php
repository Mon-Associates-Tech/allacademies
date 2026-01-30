<div>
    <div class="flex items-center mb-8">
        <div class="flex items-center justify-center w-10 h-10 bg-blue-600 text-white rounded-full text-sm font-bold mr-4">3</div>
        <div>
            <h3 class="text-xl font-bold text-gray-900">Table of Contents</h3>
            <p class="text-gray-600">Organize your book into chapters (Optional)</p>
        </div>
    </div>

    <div class="space-y-4">
        @if(empty($tableOfContents))
            <div class="text-center py-12 bg-gray-50 rounded-xl">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No chapters yet</h3>
                <p class="mt-1 text-sm text-gray-500">Get started by generating or adding chapters</p>
                <div class="mt-6 flex justify-center gap-4">
                    <button type="button" wire:click="generateTableOfContents"
                            class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                        Auto-generate Chapters
                    </button>
                    <button type="button" wire:click="addChapter"
                            class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                        Add Chapter Manually
                    </button>
                </div>
            </div>
        @else
            <div class="flex justify-between items-center mb-4">
                <h4 class="text-lg font-semibold text-gray-900">Chapters ({{ count($tableOfContents) }})</h4>
                <button type="button" wire:click="addChapter"
                        class="inline-flex items-center px-3 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Add Chapter
                </button>
            </div>

            @foreach($tableOfContents as $index => $chapter)
                <div class="border border-gray-200 rounded-lg overflow-hidden">
                    <div class="bg-gray-50 px-4 py-3 flex items-center justify-between cursor-pointer"
                         wire:click="toggleChapter({{ $index }})">
                        <div class="flex items-center space-x-3">
                            <span class="font-semibold text-gray-900">Chapter {{ $chapter['chapter'] }}</span>
                            <span class="text-gray-600">{{ $chapter['title'] }}</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <span class="text-sm text-gray-500">Pages {{ $chapter['page_start'] }}-{{ $chapter['page_end'] }}</span>
                            <button type="button" wire:click.stop="removeChapter({{ $index }})"
                                    class="text-red-600 hover:text-red-800">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                            </button>
                            <svg class="w-5 h-5 text-gray-400 transition-transform {{ in_array($index, $expandedChapters) ? 'rotate-180' : '' }}"
                                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </div>
                    </div>

                    @if(in_array($index, $expandedChapters))
                        <div class="p-4 space-y-4">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Chapter Title</label>
                                    <input type="text" wire:model="tableOfContents.{{ $index }}.title"
                                           class="block w-full px-3 py-2 border border-gray-300 rounded-md">
                                </div>
                                <div class="grid grid-cols-2 gap-2">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Start Page</label>
                                        <input type="number" wire:model="tableOfContents.{{ $index }}.page_start"
                                               class="block w-full px-3 py-2 border border-gray-300 rounded-md">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">End Page</label>
                                        <input type="number" wire:model="tableOfContents.{{ $index }}.page_end"
                                               class="block w-full px-3 py-2 border border-gray-300 rounded-md">
                                    </div>
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                                <textarea wire:model="tableOfContents.{{ $index }}.description" rows="2"
                                          class="block w-full px-3 py-2 border border-gray-300 rounded-md"></textarea>
                            </div>
                        </div>
                    @endif
                </div>
            @endforeach
        @endif
    </div>
</div>
