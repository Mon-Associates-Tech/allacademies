<div>
    <div class="flex items-center mb-8">
        <div class="flex items-center justify-center w-10 h-10 bg-blue-600 text-white rounded-full text-sm font-bold mr-4">5</div>
        <div>
            <h3 class="text-xl font-bold text-gray-900">Review & Publish</h3>
            <p class="text-gray-600">Review all details and set publication status</p>
        </div>
    </div>

    <!-- Comprehensive Summary -->
    <div class="space-y-6 mb-8">
        <!-- Step 1: Basic Information -->
        <div class="bg-white rounded-xl p-6 border-2 border-gray-200">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center">
                    <div class="flex items-center justify-center w-8 h-8 bg-blue-100 text-blue-600 rounded-full text-sm font-bold mr-3">1</div>
                    <h4 class="text-lg font-bold text-gray-900">Basic Information</h4>
                </div>
                <button type="button" wire:click="goToStep(1)" class="text-sm text-blue-600 hover:text-blue-800 font-medium">Edit</button>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                <div>
                    <span class="text-gray-600 font-medium">Title:</span>
                    <p class="text-gray-900 mt-1">{{ $title ?: 'Not set' }}</p>
                </div>
                <div>
                    <span class="text-gray-600 font-medium">Author:</span>
                    <p class="text-gray-900 mt-1">{{ $authorId ? ($authors->find($authorId)?->name ?? 'Unknown') : 'Not set' }}</p>
                </div>
                <div>
                    <span class="text-gray-600 font-medium">Categories:</span>
                    <p class="text-gray-900 mt-1">
                        @if(!empty($bookCategoryIds))
                            {{ $bookCategories->whereIn('id', $bookCategoryIds)->pluck('name')->join(', ') }}
                        @else
                            Not set
                        @endif
                    </p>
                </div>
                <div>
                    <span class="text-gray-600 font-medium">Age Groups:</span>
                    <p class="text-gray-900 mt-1">
                        @if(!empty($ageGroups))
                            {{ collect($ageGroups)->map(fn($key) => $this->ageGroupOptions[$key] ?? $key)->join(', ') }}
                        @else
                            Not specified
                        @endif
                    </p>
                </div>
                <div>
                    <span class="text-gray-600 font-medium">Academic Groups:</span>
                    <p class="text-gray-900 mt-1">
                        @if(!empty($academicGroupIds))
                            {{ $academicGroups->whereIn('id', $academicGroupIds)->pluck('name')->join(', ') }}
                        @else
                            Not specified
                        @endif
                    </p>
                </div>
                <div>
                    <span class="text-gray-600 font-medium">Academic Levels:</span>
                    <p class="text-gray-900 mt-1">
                        @if(!empty($academicLevelIds))
                            {{ $academicLevels->whereIn('id', $academicLevelIds)->pluck('name')->join(', ') }}
                        @else
                            Not specified
                        @endif
                    </p>
                </div>
                <div class="md:col-span-2">
                    <span class="text-gray-600 font-medium">Academic Subjects:</span>
                    <p class="text-gray-900 mt-1">
                        @if(!empty($academicSubjectIds))
                            {{ $academicSubjects->whereIn('id', $academicSubjectIds)->pluck('name')->join(', ') }}
                        @else
                            Not specified
                        @endif
                    </p>
                </div>
            </div>
        </div>

        <!-- Step 2: Book Details -->
        <div class="bg-white rounded-xl p-6 border-2 border-gray-200">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center">
                    <div class="flex items-center justify-center w-8 h-8 bg-blue-100 text-blue-600 rounded-full text-sm font-bold mr-3">2</div>
                    <h4 class="text-lg font-bold text-gray-900">Book Details</h4>
                </div>
                <button type="button" wire:click="goToStep(2)" class="text-sm text-blue-600 hover:text-blue-800 font-medium">Edit</button>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                <div>
                    <span class="text-gray-600 font-medium">Edition:</span>
                    <p class="text-gray-900 mt-1">{{ $edition ?: 'Not specified' }}</p>
                </div>
                <div>
                    <span class="text-gray-600 font-medium">Publisher:</span>
                    <p class="text-gray-900 mt-1">{{ $publisher ?: 'Not specified' }}</p>
                </div>
                <div>
                    <span class="text-gray-600 font-medium">Pages:</span>
                    <p class="text-gray-900 mt-1">{{ $pages ?: 'Not specified' }}</p>
                </div>
                <div>
                    <span class="text-gray-600 font-medium">Subscription Fee:</span>
                    <p class="text-gray-900 mt-1">
                        @if($annualSubscriptionFee > 0)
                            <span class="inline-flex items-center px-3 py-1 bg-green-100 text-green-800 text-xs font-bold rounded-full">
                                GHS {{ number_format($annualSubscriptionFee, 2) }}/year
                            </span>
                        @else
                            <span class="inline-flex items-center px-3 py-1 bg-blue-100 text-blue-800 text-xs font-bold rounded-full">
                                FREE
                            </span>
                        @endif
                    </p>
                </div>
                <div class="md:col-span-2">
                    <span class="text-gray-600 font-medium">Available Formats:</span>
                    <div class="flex items-center space-x-2 mt-1">
                        @if($hasHardcopy)
                            <span class="inline-flex items-center px-3 py-1 bg-green-100 text-green-800 text-xs font-semibold rounded-full">
                                📚 Hardcopy
                            </span>
                        @endif
                        @if($hasSoftcopy)
                            <span class="inline-flex items-center px-3 py-1 bg-purple-100 text-purple-800 text-xs font-semibold rounded-full">
                                📄 Softcopy
                            </span>
                        @endif
                        @if(!$hasHardcopy && !$hasSoftcopy)
                            <span class="text-gray-500">None selected</span>
                        @endif
                    </div>
                </div>
                @if($additionalInfo)
                    <div class="md:col-span-2">
                        <span class="text-gray-600 font-medium">Additional Info:</span>
                        <p class="text-gray-900 mt-1">{{ $additionalInfo }}</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Step 3: Table of Contents -->
        <div class="bg-white rounded-xl p-6 border-2 border-gray-200">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center">
                    <div class="flex items-center justify-center w-8 h-8 bg-blue-100 text-blue-600 rounded-full text-sm font-bold mr-3">3</div>
                    <h4 class="text-lg font-bold text-gray-900">Table of Contents</h4>
                </div>
                <button type="button" wire:click="goToStep(3)" class="text-sm text-blue-600 hover:text-blue-800 font-medium">Edit</button>
            </div>
            @if(!empty($tableOfContents))
                <div class="space-y-2">
                    @foreach($tableOfContents as $chapter)
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div>
                                <span class="font-semibold text-gray-900">Chapter {{ $chapter['chapter'] }}:</span>
                                <span class="text-gray-700 ml-2">{{ $chapter['title'] }}</span>
                            </div>
                            <span class="text-sm text-gray-500">Pages {{ $chapter['page_start'] }}-{{ $chapter['page_end'] }}</span>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-500 text-sm">No table of contents added</p>
            @endif
        </div>

        <!-- Step 4: Media Files -->
        <div class="bg-white rounded-xl p-6 border-2 border-gray-200">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center">
                    <div class="flex items-center justify-center w-8 h-8 bg-blue-100 text-blue-600 rounded-full text-sm font-bold mr-3">4</div>
                    <h4 class="text-lg font-bold text-gray-900">Media Files</h4>
                </div>
                <button type="button" wire:click="goToStep(4)" class="text-sm text-blue-600 hover:text-blue-800 font-medium">Edit</button>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="text-center">
                    <div class="text-gray-600 font-medium text-sm mb-2">Cover Image</div>
                    @if($coverImage || $existingCoverImage)
                        <div class="inline-flex items-center px-3 py-1 bg-green-100 text-green-800 text-xs font-semibold rounded-full">
                            ✓ Uploaded
                        </div>
                    @else
                        <div class="text-gray-400 text-xs">Not uploaded</div>
                    @endif
                </div>
                <div class="text-center">
                    <div class="text-gray-600 font-medium text-sm mb-2">Book PDF</div>
                    @if($pdfFile || $existingPdfFile)
                        <div class="inline-flex items-center px-3 py-1 bg-green-100 text-green-800 text-xs font-semibold rounded-full">
                            ✓ Uploaded
                        </div>
                    @else
                        <div class="text-gray-400 text-xs">Not uploaded</div>
                    @endif
                </div>
                <div class="text-center">
                    <div class="text-gray-600 font-medium text-sm mb-2">Sample PDF</div>
                    @if($samplePdfFile || ($existingSamplePdfFile && !$removeSamplePdfFile))
                        <div class="inline-flex items-center px-3 py-1 bg-green-100 text-green-800 text-xs font-semibold rounded-full">
                            ✓ Uploaded
                        </div>
                    @else
                        <div class="text-gray-400 text-xs">Not uploaded</div>
                    @endif
                </div>
                <div class="text-center">
                    <div class="text-gray-600 font-medium text-sm mb-2">Audio/Video</div>
                    @php
                        $mediaCount = 0;
                        if($singleAudio || $existingSingleAudio) $mediaCount++;
                        if($singleVideo || $existingSingleVideo) $mediaCount++;
                        $mediaCount += count($chapterAudios) + count($chapterVideos);
                    @endphp
                    @if($mediaCount > 0)
                        <div class="inline-flex items-center px-3 py-1 bg-green-100 text-green-800 text-xs font-semibold rounded-full">
                            {{ $mediaCount }} file(s)
                        </div>
                    @else
                        <div class="text-gray-400 text-xs">None uploaded</div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Publishing Status -->
    <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-2xl p-6 border-2 border-blue-200">
        <h4 class="text-lg font-bold text-gray-900 mb-4">Publication Status</h4>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            @foreach($this->publishingStatusOptions as $value => $label)
                <label class="relative flex items-center p-4 bg-white border-2 rounded-xl cursor-pointer transition-all
                    {{ $status === $value ? 'border-blue-500 bg-blue-50 shadow-md' : 'border-gray-200 hover:border-blue-300' }}">
                    <input type="radio" wire:model="status" value="{{ $value }}"
                           class="h-4 w-4 text-blue-600 dark:text-blue-500 focus:ring-blue-500 dark:focus:ring-blue-400 border-gray-300 dark:border-gray-600 dark:bg-gray-700">
                    <div class="ml-3">
                        <span class="block text-sm font-medium text-gray-900">{{ $label }}</span>
                        <span class="block text-xs text-gray-500 mt-1">
                            @if($value === 'draft')
                                Not visible to readers
                            @elseif($value === 'published')
                                Visible to all readers
                            @elseif($value === 'pending_review')
                                Awaiting approval
                            @endif
                        </span>
                    </div>
                </label>
            @endforeach
        </div>
    </div>
</div>
