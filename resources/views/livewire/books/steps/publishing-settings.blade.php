<div>
    <x-exam.callout tone="primary" class="mb-8">
        <div class="flex items-center gap-3">
            <span class="flex items-center justify-center w-8 h-8 bg-primary text-on-primary rounded-full text-sm font-bold shrink-0">5</span>
            <div>
                <h3 class="font-bold text-ink">Review & Publish</h3>
                <p class="text-sm text-ink-soft">Review all details and set publication status</p>
            </div>
        </div>
    </x-exam.callout>

    <!-- Comprehensive Summary -->
    <div class="space-y-4 mb-8">
        <!-- Step 1: Basic Information -->
        <x-exam.card>
            <x-slot:header>
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="flex items-center justify-center w-7 h-7 bg-primary/10 text-primary rounded-full text-xs font-bold">1</span>
                        <h4 class="font-semibold text-ink">Basic Information</h4>
                    </div>
                    <x-exam.button variant="ghost" wire:click="goToStep(1)">Edit</x-exam.button>
                </div>
            </x-slot:header>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                <div>
                    <span class="text-muted font-medium">Title:</span>
                    <p class="text-ink mt-1">{{ $title ?: 'Not set' }}</p>
                </div>
                <div>
                    <span class="text-muted font-medium">Author:</span>
                    <p class="text-ink mt-1">{{ $authorId ? ($authors->find($authorId)?->name ?? 'Unknown') : 'Not set' }}</p>
                </div>
                <div>
                    <span class="text-muted font-medium">Categories:</span>
                    <p class="text-ink mt-1">
                        @if(!empty($bookCategoryIds))
                            {{ $bookCategories->whereIn('id', $bookCategoryIds)->pluck('name')->join(', ') }}
                        @else
                            Not set
                        @endif
                    </p>
                </div>
                <div>
                    <span class="text-muted font-medium">Age Groups:</span>
                    <p class="text-ink mt-1">
                        @if(!empty($ageGroups))
                            {{ collect($ageGroups)->map(fn($key) => $this->ageGroupOptions[$key] ?? $key)->join(', ') }}
                        @else
                            Not specified
                        @endif
                    </p>
                </div>
                <div>
                    <span class="text-muted font-medium">Academic Groups:</span>
                    <p class="text-ink mt-1">
                        @if(!empty($academicGroupIds))
                            {{ $academicGroups->whereIn('id', $academicGroupIds)->pluck('name')->join(', ') }}
                        @else
                            Not specified
                        @endif
                    </p>
                </div>
                <div>
                    <span class="text-muted font-medium">Academic Levels:</span>
                    <p class="text-ink mt-1">
                        @if(!empty($academicLevelIds))
                            {{ $academicLevels->whereIn('id', $academicLevelIds)->pluck('name')->join(', ') }}
                        @else
                            Not specified
                        @endif
                    </p>
                </div>
                <div class="md:col-span-2">
                    <span class="text-muted font-medium">Academic Subjects:</span>
                    <p class="text-ink mt-1">
                        @if(!empty($academicSubjectIds))
                            {{ $academicSubjects->whereIn('id', $academicSubjectIds)->pluck('name')->join(', ') }}
                        @else
                            Not specified
                        @endif
                    </p>
                </div>
            </div>
        </x-exam.card>

        <!-- Step 2: Book Details -->
        <x-exam.card>
            <x-slot:header>
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="flex items-center justify-center w-7 h-7 bg-primary/10 text-primary rounded-full text-xs font-bold">2</span>
                        <h4 class="font-semibold text-ink">Book Details</h4>
                    </div>
                    <x-exam.button variant="ghost" wire:click="goToStep(2)">Edit</x-exam.button>
                </div>
            </x-slot:header>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                <div>
                    <span class="text-muted font-medium">Edition:</span>
                    <p class="text-ink mt-1">{{ $edition ?: 'Not specified' }}</p>
                </div>
                <div>
                    <span class="text-muted font-medium">Publisher:</span>
                    <p class="text-ink mt-1">{{ $publisher ?: 'Not specified' }}</p>
                </div>
                <div>
                    <span class="text-muted font-medium">Pages:</span>
                    <p class="text-ink mt-1">{{ $pages ?: 'Not specified' }}</p>
                </div>
                <div>
                    <span class="text-muted font-medium">Subscription Fee:</span>
                    <p class="text-ink mt-1">
                        @if($annualSubscriptionFee > 0)
                            <x-exam.badge tone="ok">GHS {{ number_format($annualSubscriptionFee, 2) }}/year</x-exam.badge>
                        @else
                            <x-exam.badge tone="info">FREE</x-exam.badge>
                        @endif
                    </p>
                </div>
                <div class="md:col-span-2">
                    <span class="text-muted font-medium">Available Formats:</span>
                    <div class="flex items-center space-x-2 mt-1">
                        @if($hasHardcopy)
                            <x-exam.badge tone="ok">📚 Hardcopy</x-exam.badge>
                        @endif
                        @if($hasSoftcopy)
                            <x-exam.badge tone="info">📄 Softcopy</x-exam.badge>
                        @endif
                        @if(!$hasHardcopy && !$hasSoftcopy)
                            <span class="text-muted">None selected</span>
                        @endif
                    </div>
                </div>
                @if($additionalInfo)
                    <div class="md:col-span-2">
                        <span class="text-muted font-medium">Additional Info:</span>
                        <p class="text-ink mt-1">{{ $additionalInfo }}</p>
                    </div>
                @endif
            </div>
        </x-exam.card>

        <!-- Step 3: Table of Contents -->
        <x-exam.card>
            <x-slot:header>
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="flex items-center justify-center w-7 h-7 bg-primary/10 text-primary rounded-full text-xs font-bold">3</span>
                        <h4 class="font-semibold text-ink">Table of Contents</h4>
                    </div>
                    <x-exam.button variant="ghost" wire:click="goToStep(3)">Edit</x-exam.button>
                </div>
            </x-slot:header>
            @if(!empty($tableOfContents))
                <div class="space-y-2">
                    @foreach($tableOfContents as $chapter)
                        <div class="flex items-center justify-between p-3 bg-surface-2 rounded-lg">
                            <div>
                                <span class="font-semibold text-ink">Chapter {{ $chapter['chapter'] }}:</span>
                                <span class="text-ink-soft ml-2">{{ $chapter['title'] }}</span>
                            </div>
                            <span class="text-sm text-muted">Pages {{ $chapter['page_start'] }}-{{ $chapter['page_end'] }}</span>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-muted text-sm">No table of contents added</p>
            @endif
        </x-exam.card>

        <!-- Step 4: Media Files -->
        <x-exam.card>
            <x-slot:header>
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="flex items-center justify-center w-7 h-7 bg-primary/10 text-primary rounded-full text-xs font-bold">4</span>
                        <h4 class="font-semibold text-ink">Media Files</h4>
                    </div>
                    <x-exam.button variant="ghost" wire:click="goToStep(4)">Edit</x-exam.button>
                </div>
            </x-slot:header>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="text-center">
                    <div class="text-muted font-medium text-sm mb-2">Cover Image</div>
                    @if($coverImage || $existingCoverImage || $coverImageUploaded)
                        <x-exam.badge tone="ok">✓ Uploaded</x-exam.badge>
                    @else
                        <span class="text-muted text-xs">Not uploaded</span>
                    @endif
                </div>
                <div class="text-center">
                    <div class="text-muted font-medium text-sm mb-2">Book PDF</div>
                    @if($pdfFile || $existingPdfFile || $pdfFileUploaded)
                        <x-exam.badge tone="ok">✓ Uploaded</x-exam.badge>
                    @else
                        <span class="text-muted text-xs">Not uploaded</span>
                    @endif
                </div>
                <div class="text-center">
                    <div class="text-muted font-medium text-sm mb-2">Sample PDF</div>
                    @if($samplePdfFile || ($existingSamplePdfFile && !$removeSamplePdfFile) || $samplePdfFileUploaded)
                        <x-exam.badge tone="ok">✓ Uploaded</x-exam.badge>
                    @else
                        <span class="text-muted text-xs">Not uploaded</span>
                    @endif
                </div>
                <div class="text-center">
                    <div class="text-muted font-medium text-sm mb-2">Audio/Video</div>
                    @php
                        $mediaCount = 0;
                        if($singleAudio || $existingSingleAudio || $singleAudioUploaded) $mediaCount++;
                        if($singleVideo || $existingSingleVideo || $singleVideoUploaded) $mediaCount++;
                        $mediaCount += count($chapterAudios) + count($chapterVideos);
                    @endphp
                    @if($mediaCount > 0)
                        <x-exam.badge tone="ok">{{ $mediaCount }} file(s)</x-exam.badge>
                    @else
                        <span class="text-muted text-xs">None uploaded</span>
                    @endif
                </div>
            </div>
        </x-exam.card>
    </div>

    <!-- Publishing Status -->
    <x-exam.card>
        <x-slot:header>
            <h4 class="font-semibold text-ink">Publication Status</h4>
        </x-slot:header>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
            @foreach($this->publishingStatusOptions as $value => $label)
                <label class="relative flex items-start p-4 rounded-md border border-line-strong cursor-pointer transition-all
                    {{ $status === $value ? 'border-primary bg-primary/5 ring-1 ring-primary/20' : 'hover:border-primary hover:bg-surface-2' }}">
                    <input type="radio" wire:model="status" value="{{ $value }}"
                           class="h-4 w-4 text-primary focus:ring-primary border-line-strong rounded mt-0.5">
                    <div class="ml-3">
                        <span class="block text-sm font-medium text-ink">{{ $label }}</span>
                        <span class="block text-xs text-ink-soft mt-0.5">
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
    </x-exam.card>
</div>
