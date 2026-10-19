<!-- Step 4: Media Files -->
<div>
    <x-exam.callout tone="primary" class="mb-8">
        <div class="flex items-center gap-3">
            <span class="flex items-center justify-center w-8 h-8 bg-primary text-on-primary rounded-full text-sm font-bold shrink-0">4</span>
            <div>
                <h3 class="font-bold text-ink">Media & Files</h3>
                <p class="text-sm text-ink-soft">Upload cover image, PDF file, and additional media</p>
            </div>
        </div>
    </x-exam.callout>

    <!-- Main Uploads: Vertical Layout -->
    <div class="space-y-6">
        <!-- Cover Image Upload -->
        <x-exam.field label="Cover Image">
            @if($existingCoverImage && !$removeCoverImage && !$coverImageUploaded)
                <x-exam.callout tone="ok" class="mb-3">
                    <p class="text-sm font-medium mb-2">Current Cover:</p>
                    <div class="flex items-start flex-col space-y-3">
                        <img src="{{ $existingCoverImage }}" alt="Current Cover" class="h-40 w-28 object-cover rounded-lg border-2 border-line-strong shadow-sm">
                        <x-exam.button variant="danger" wire:click="removeExistingCoverImage">Remove Current Cover</x-exam.button>
                    </div>
                </x-exam.callout>
            @endif

            <x-exam.dropzone id="coverImage" model="coverImage"
                :current="$coverImageUploaded ? null : ($existingCoverImage && !$removeCoverImage ? $existingCoverImage : null)"
                :pending="(bool)$coverImage" tag="COVER IMAGE" error-key="coverImage" />

            @if ($coverImage)
                <x-exam.callout tone="primary" class="mt-3">
                    <div class="flex items-center space-x-3">
                        <div class="animate-spin rounded-full h-5 w-5 border-b-2 border-primary"></div>
                        <span class="text-sm text-muted">Processing cover image...</span>
                    </div>
                </x-exam.callout>
            @endif
            @if ($coverImageUploaded && $existingCoverImage)
                <x-exam.callout tone="ok" class="mt-3">
                    <div class="flex items-center space-x-2">
                        <svg class="w-5 h-5 text-ok" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        <span class="text-sm font-medium">New cover image uploaded successfully</span>
                    </div>
                </x-exam.callout>
            @endif
        </x-exam.field>

        <!-- PDF Upload -->
        <x-exam.field label="PDF File" tag="REQUIRED FOR SOFTCOPY">
            @if($existingPdfFile && !$removePdfFile && !$pdfFileUploaded)
                <x-exam.callout tone="primary" class="mb-3">
                    <p class="text-sm font-medium mb-2 flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        Current PDF File: <span class="ml-1">{{ basename($existingPdfFile) }}</span>
                    </p>
                    <x-exam.button variant="danger" wire:click="removeExistingPdfFile">Remove Current PDF</x-exam.button>
                </x-exam.callout>
            @endif

            <x-exam.file-upload id="pdfFile" model="pdfFile" accept=".pdf" icon="pdf"
                :current="$existingPdfFile && !$removePdfFile && !$pdfFileUploaded ? $existingPdfFile : null"
                :pending="(bool)$pdfFile" :uploaded="$pdfFileUploaded"
                remove-action="removeExistingPdfFile" tag="PDF FILE" error-key="pdfFile" />
            <p class="mt-1 font-mono text-[11px] text-muted">
                Upload the full PDF of the book (max 100MB). Required for softcopy books.
            </p>

            @if ($pdfFile)
                <x-exam.callout tone="primary" class="mt-3">
                    <div class="flex items-center space-x-3">
                        <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-primary"></div>
                        <span class="text-sm text-muted">Processing PDF...</span>
                    </div>
                </x-exam.callout>
            @endif
            @if ($pdfFileUploaded)
                <x-exam.callout tone="ok" class="mt-3">
                    <div class="flex items-center space-x-2">
                        <svg class="w-5 h-5 text-ok" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        <span class="text-sm font-medium">PDF uploaded successfully</span>
                    </div>
                </x-exam.callout>
            @endif
        </x-exam.field>

        <!-- Sample PDF -->
        <x-exam.field label="Sample PDF" tag="OPTIONAL">
            @if($existingSamplePdfFile && !$removeSamplePdfFile && !$samplePdfFileUploaded)
                <x-exam.callout tone="primary" class="mb-3">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <svg class="w-8 h-8 text-accent mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <div>
                                <p class="text-sm font-medium text-ink">Current Sample PDF</p>
                                <p class="text-xs text-muted">Click "View" to preview</p>
                            </div>
                        </div>
                        <div class="flex space-x-2">
                            <a href="{{ asset('storage/' . $existingSamplePdfFile) }}" target="_blank"
                               class="px-3 py-1 bg-primary/10 text-primary rounded-lg text-xs font-medium hover:bg-primary/20 transition-colors">
                                View
                            </a>
                            <x-exam.button variant="danger" wire:click="removeExistingSamplePdfFile">Remove</x-exam.button>
                        </div>
                    </div>
                </x-exam.callout>
            @endif

            <x-exam.file-upload id="samplePdfFile" model="samplePdfFile" accept=".pdf" icon="pdf"
                :current="$existingSamplePdfFile && !$removeSamplePdfFile && !$samplePdfFileUploaded ? $existingSamplePdfFile : null"
                :pending="(bool)$samplePdfFile" :uploaded="$samplePdfFileUploaded"
                remove-action="removeExistingSamplePdfFile" tag="SAMPLE PDF" error-key="samplePdfFile" />
            <p class="mt-1 font-mono text-[11px] text-muted">
                Upload a sample PDF of the book (max 10MB). If not provided, the first chapter will be extracted from the full PDF.
            </p>
        </x-exam.field>
    </div>
    <div class="mt-8 space-y-8">
        <!-- Audio Content -->
        <x-exam.card>
            <x-slot:header>
                <div class="flex items-center justify-between gap-4">
                    <div class="flex items-center gap-2 min-w-0">
                        <svg class="w-5 h-5 text-primary shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3"/>
                        </svg>
                        <h4 class="font-semibold text-ink truncate">Audio Content</h4>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer shrink-0">
                        <input type="checkbox" wire:model.live="hasAudio" class="sr-only peer">
                        <div class="w-11 h-6 bg-line peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-primary/30 rounded-full peer peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary"></div>
                    </label>
                </div>
            </x-slot:header>

            @if($hasAudio)
                <div class="space-y-6">
                    <!-- Single Audio Upload -->
                    <x-exam.field label="Main Audio File">
                        @if($existingSingleAudio && !$removeSingleAudioFile && !$singleAudioUploaded)
                            <x-exam.callout tone="primary" class="mb-3">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-2">
                                        <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3"/>
                                        </svg>
                                        <span class="text-sm font-medium text-ink">{{ basename($existingSingleAudio) }}</span>
                                    </div>
                                    <x-exam.button variant="danger" wire:click="removeExistingSingleAudioFile">Remove</x-exam.button>
                                </div>
                            </x-exam.callout>
                        @endif

                        <x-exam.file-upload id="singleAudio" model="singleAudio" accept="audio/*" icon="audio"
                            :current="$existingSingleAudio && !$removeSingleAudioFile && !$singleAudioUploaded ? $existingSingleAudio : null"
                            :pending="(bool)$singleAudio" :uploaded="$singleAudioUploaded"
                            remove-action="removeExistingSingleAudioFile" tag="AUDIO FILE" error-key="singleAudio" />
                        <p class="mt-1 font-mono text-[11px] text-muted">MP3, WAV, M4A up to 50MB.</p>

                        @if ($singleAudio)
                            <x-exam.callout tone="primary" class="mt-3">
                                <div class="flex items-center space-x-3">
                                    <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-primary"></div>
                                    <span class="text-sm text-muted">Uploading audio...</span>
                                </div>
                            </x-exam.callout>
                        @endif
                    </x-exam.field>

                    <!-- Chapter Audio Files -->
                    @if($tableOfContents && count($tableOfContents) > 0)
                        <div class="border-t border-line-strong pt-6">
                            <h5 class="text-sm font-medium text-ink mb-4">Chapter Audio Files (Optional)</h5>
                            <div class="space-y-4">
                                @foreach($tableOfContents as $index => $chapter)
                                    <x-exam.card>
                                        <div class="mb-2">
                                            <h6 class="text-sm font-medium text-ink">Chapter {{ $chapter['chapter'] }}: {{ $chapter['title'] }}</h6>
                                        </div>
                                        <x-exam.file-upload id="chapterAudio{{ $index }}" model="chapterAudios.{{ $index }}" accept="audio/*" icon="audio"
                                            :pending="(bool)$chapterAudios[$index] ?? false"
                                            tag="CHAPTER AUDIO" :error-key="'chapterAudios.'.$index" />
                                    </x-exam.card>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            @endif
        </x-exam.card>

        <!-- Video Content -->
        <x-exam.card>
            <x-slot:header>
                <div class="flex items-center justify-between gap-4">
                    <div class="flex items-center gap-2 min-w-0">
                        <svg class="w-5 h-5 text-primary shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                        </svg>
                        <h4 class="font-semibold text-ink truncate">Video Content</h4>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer shrink-0">
                        <input type="checkbox" wire:model.live="hasVideo" class="sr-only peer">
                        <div class="w-11 h-6 bg-line peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-primary/30 rounded-full peer peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary"></div>
                    </label>
                </div>
            </x-slot:header>

            @if($hasVideo)
                <div class="space-y-6">
                    <!-- Single Video Upload -->
                    <x-exam.field label="Main Video File">
                        @if($existingSingleVideo && !$removeSingleVideoFile && !$singleVideoUploaded)
                            <x-exam.callout tone="primary" class="mb-3">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-2">
                                        <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                        </svg>
                                        <span class="text-sm font-medium text-ink">{{ basename($existingSingleVideo) }}</span>
                                    </div>
                                    <x-exam.button variant="danger" wire:click="removeExistingSingleVideoFile">Remove</x-exam.button>
                                </div>
                            </x-exam.callout>
                        @endif

                        <x-exam.file-upload id="singleVideo" model="singleVideo" accept="video/*" icon="video"
                            :current="$existingSingleVideo && !$removeSingleVideoFile && !$singleVideoUploaded ? $existingSingleVideo : null"
                            :pending="(bool)$singleVideo" :uploaded="$singleVideoUploaded"
                            remove-action="removeExistingSingleVideoFile" tag="VIDEO FILE" error-key="singleVideo" />
                        <p class="mt-1 font-mono text-[11px] text-muted">MP4, AVI, MOV up to 100MB.</p>

                        @if ($singleVideo)
                            <x-exam.callout tone="primary" class="mt-3">
                                <div class="flex items-center space-x-3">
                                    <div class="animate-spin rounded-full h-5 w-5 border-b-2 border-primary"></div>
                                    <span class="text-sm text-muted">Uploading video...</span>
                                </div>
                            </x-exam.callout>
                        @endif
                        @if ($singleVideoUploaded)
                            <x-exam.callout tone="ok" class="mt-3">
                                <div class="flex items-center space-x-2">
                                    <svg class="w-5 h-5 text-ok" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    <span class="text-sm font-medium">Video uploaded successfully</span>
                                </div>
                            </x-exam.callout>
                        @endif
                    </x-exam.field>

                    <!-- Chapter Video Files -->
                    @if($tableOfContents && count($tableOfContents) > 0)
                        <div class="border-t border-line-strong pt-6">
                            <h5 class="text-sm font-medium text-ink mb-4">Chapter Video Files (Optional)</h5>
                            <div class="space-y-4">
                                @foreach($tableOfContents as $index => $chapter)
                                    <x-exam.card>
                                        <div class="mb-2">
                                            <h6 class="text-sm font-medium text-ink">Chapter {{ $chapter['chapter'] }}: {{ $chapter['title'] }}</h6>
                                        </div>
                                        <x-exam.file-upload id="chapterVideo{{ $index }}" model="chapterVideos.{{ $index }}" accept="video/*" icon="video"
                                            :pending="(bool)$chapterVideos[$index] ?? false"
                                            tag="CHAPTER VIDEO" :error-key="'chapterVideos.'.$index" />
                                    </x-exam.card>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            @endif
        </x-exam.card>
    </div>
</div>
