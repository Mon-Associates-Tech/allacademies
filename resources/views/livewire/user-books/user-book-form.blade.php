
<div class="max-w-4xl mx-auto py-6">
    <div class="bg-white shadow rounded-lg p-6">
        <h2 class="text-2xl font-bold mb-6">{{ $mode === 'edit' ? 'Edit Book' : 'Upload New Book' }}</h2>

        @if (session()->has('message'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                {{ session('message') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form wire:submit.prevent="submit">
            <!-- Basic Information -->
            <div class="mb-8">
                <h3 class="text-lg font-semibold mb-4">Basic Information</h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Title *</label>
                        <input type="text" wire:model.live="title" id="title"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        @error('title') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status *</label>
                        <select wire:model="status" id="status"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="draft">Draft</option>
                            <option value="published">Published</option>
                            <option value="archived">Archived</option>
                        </select>
                        @error('status') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                        <textarea wire:model="description" id="description" rows="3"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                        @error('description') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label for="edition" class="block text-sm font-medium text-gray-700 mb-1">Edition</label>
                        <input type="text" wire:model="edition" id="edition"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        @error('edition') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label for="publisher" class="block text-sm font-medium text-gray-700 mb-1">Publisher</label>
                        <input type="text" wire:model="publisher" id="publisher"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        @error('publisher') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label for="pages" class="block text-sm font-medium text-gray-700 mb-1">Pages</label>
                        <input type="number" wire:model="pages" id="pages"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        @error('pages') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            <!-- Files Section -->
            <div class="mb-8">
                <h3 class="text-lg font-semibold mb-4">Files</h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Cover Image -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Cover Image</label>
                        @if($existingCoverImage)
                            <div class="mb-2">
                                <img src="{{ asset('storage/' . $existingCoverImage) }}"
                                     alt="Cover Image" class="w-32 h-40 object-cover rounded">
                                <button type="button" wire:click="removeExistingCoverImage"
                                        class="mt-1 text-sm text-red-600 hover:text-red-800">
                                    Remove
                                </button>
                            </div>
                        @endif
                        <input type="file" wire:model="coverImage"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        @error('coverImage') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <!-- PDF File -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">PDF File</label>
                        @if($existingPdfFile)
                            <div class="mb-2">
                                <a href="{{ asset('storage/' . $existingPdfFile) }}" target="_blank"
                                   class="text-blue-600 hover:text-blue-800">View PDF</a>
                                <button type="button" wire:click="removeExistingPdfFile"
                                        class="ml-2 text-sm text-red-600 hover:text-red-800">
                                    Remove
                                </button>
                            </div>
                        @endif
                        <input type="file" wire:model="pdfFile"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                               accept="application/pdf">
                        @error('pdfFile') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <!-- Sample PDF -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Sample PDF</label>
                        @if($existingSamplePdfFile)
                            <div class="mb-2">
                                <a href="{{ asset('storage/' . $existingSamplePdfFile) }}" target="_blank"
                                   class="text-blue-600 hover:text-blue-800">View Sample</a>
                                <button type="button" wire:click="removeExistingSamplePdfFile"
                                        class="ml-2 text-sm text-red-600 hover:text-red-800">
                                    Remove
                                </button>
                            </div>
                        @endif
                        <input type="file" wire:model="samplePdfFile"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                               accept="application/pdf">
                        @error('samplePdfFile') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            <!-- Table of Contents -->
            <div class="mb-8">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold">Table of Contents</h3>
                    <button type="button" wire:click="toggleTableOfContents"
                            class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                        {{ $showTableOfContents ? 'Hide' : 'Show' }} Table of Contents
                    </button>
                </div>

                @if($showTableOfContents)
                    <div class="border border-gray-200 rounded-lg p-4">
                        @foreach($tableOfContents as $index => $chapter)
                            <div class="mb-4 border-b border-gray-200 pb-4">
                                <div class="flex justify-between items-center mb-2">
                                    <h4 class="font-medium">Chapter {{ $chapter['chapter'] }}: {{ $chapter['title'] }}</h4>
                                    <div>
                                        <button type="button" wire:click="toggleChapter({{ $index }})"
                                                class="text-blue-600 hover:text-blue-800 mr-2">
                                            {{ in_array($index, $expandedChapters) ? 'Collapse' : 'Expand' }}
                                        </button>
                                        @if(count($tableOfContents) > 1)
                                            <button type="button" wire:click="removeChapter({{ $index }})"
                                                    class="text-red-600 hover:text-red-800">
                                                Remove
                                            </button>
                                        @endif
                                    </div>
                                </div>

                                @if(in_array($index, $expandedChapters))
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 ml-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Title *</label>
                                            <input type="text" wire:model="tableOfContents.{{ $index }}.title"
                                                   class="w-full px-3 py-2 border border-gray-300 rounded-md">
                                            @error("tableOfContents.{$index}.title")
                                                <span class="text-red-500 text-sm">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Chapter Number *</label>
                                            <input type="number" wire:model="tableOfContents.{{ $index }}.chapter"
                                                   class="w-full px-3 py-2 border border-gray-300 rounded-md">
                                            @error("tableOfContents.{$index}.chapter")
                                                <span class="text-red-500 text-sm">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Start Page *</label>
                                            <input type="number" wire:model="tableOfContents.{{ $index }}.page_start"
                                                   class="w-full px-3 py-2 border border-gray-300 rounded-md">
                                            @error("tableOfContents.{$index}.page_start")
                                                <span class="text-red-500 text-sm">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">End Page *</label>
                                            <input type="number" wire:model="tableOfContents.{{ $index }}.page_end"
                                                   class="w-full px-3 py-2 border border-gray-300 rounded-md">
                                            @error("tableOfContents.{$index}.page_end")
                                                <span class="text-red-500 text-sm">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        <div class="md:col-span-2">
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                                            <textarea wire:model="tableOfContents.{{ $index }}.description" rows="2"
                                                      class="w-full px-3 py-2 border border-gray-300 rounded-md"></textarea>
                                        </div>
                                    </div>

                                    <!-- Sections -->
                                    <div class="ml-8 mt-4">
                                        <div class="flex justify-between items-center mb-2">
                                            <h5 class="font-medium">Sections</h5>
                                            <div>
                                                <button type="button" wire:click="generateSections({{ $index }})"
                                                        class="px-3 py-1 bg-gray-200 text-gray-700 rounded-md text-sm mr-2">
                                                    Generate Sections
                                                </button>
                                                <button type="button" wire:click="addSection({{ $index }})"
                                                        class="px-3 py-1 bg-blue-600 text-white rounded-md text-sm">
                                                    Add Section
                                                </button>
                                            </div>
                                        </div>

                                        @if(!empty($chapter['sections']))
                                            @foreach($chapter['sections'] as $sectionIndex => $section)
                                                <div class="border border-gray-200 rounded p-3 mb-3">
                                                    <div class="flex justify-between items-center mb-2">
                                                        <h6 class="font-medium">Section: {{ $section['title'] }}</h6>
                                                        <button type="button" wire:click="removeSection({{ $index }}, {{ $sectionIndex }})"
                                                                class="text-red-600 hover:text-red-800">
                                                            Remove
                                                        </button>
                                                    </div>

                                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                                        <div>
                                                            <label class="block text-sm text-gray-700 mb-1">Title *</label>
                                                            <input type="text" wire:model="tableOfContents.{{ $index }}.sections.{{ $sectionIndex }}.title"
                                                                   class="w-full px-2 py-1 border border-gray-300 rounded text-sm">
                                                            @error("tableOfContents.{$index}.sections.{$sectionIndex}.title")
                                                                <span class="text-red-500 text-xs">{{ $message }}</span>
                                                            @enderror
                                                        </div>

                                                        <div>
                                                            <label class="block text-sm text-gray-700 mb-1">Start Page *</label>
                                                            <input type="number" wire:model="tableOfContents.{{ $index }}.sections.{{ $sectionIndex }}.page_start"
                                                                   class="w-full px-2 py-1 border border-gray-300 rounded text-sm">
                                                            @error("tableOfContents.{$index}.sections.{$sectionIndex}.page_start")
                                                                <span class="text-red-500 text-xs">{{ $message }}</span>
                                                            @enderror
                                                        </div>

                                                        <div>
                                                            <label class="block text-sm text-gray-700 mb-1">End Page *</label>
                                                            <input type="number" wire:model="tableOfContents.{{ $index }}.sections.{{ $sectionIndex }}.page_end"
                                                                   class="w-full px-2 py-1 border border-gray-300 rounded text-sm">
                                                            @error("tableOfContents.{$index}.sections.{$sectionIndex}.page_end")
                                                                <span class="text-red-500 text-xs">{{ $message }}</span>
                                                            @enderror
                                                        </div>

                                                        <div>
                                                            <label class="block text-sm text-gray-700 mb-1">Description</label>
                                                            <input type="text" wire:model="tableOfContents.{{ $index }}.sections.{{ $sectionIndex }}.description"
                                                                   class="w-full px-2 py-1 border border-gray-300 rounded text-sm">
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        @endif
                                    </div>
                                @endif
                            </div>
                        @endforeach

                        <button type="button" wire:click="addChapter"
                                class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                            Add Chapter
                        </button>
                    </div>
                @endif
            </div>

            <!-- Sharing Section -->
            <div class="mb-8">
                <h3 class="text-lg font-semibold mb-4">Share with Others</h3>
                <p class="text-sm text-gray-600 mb-2">
                    Enter email addresses separated by commas (max {{ $maxShares }} users)
                </p>
                <textarea wire:model="emails" rows="3" placeholder="user1@example.com, user2@example.com"
                          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                @error('emails') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <!-- Submit Buttons -->
            <div class="flex justify-end space-x-3">
                <button type="button" wire:click="cancel"
                        class="px-6 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                    Cancel
                </button>
                <button type="submit"
                        class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                    {{ $mode === 'edit' ? 'Update Book' : 'Upload Book' }}
                </button>
            </div>
        </form>
    </div>
</div>
