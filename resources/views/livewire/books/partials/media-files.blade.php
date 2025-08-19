<!-- Step 4: Media Files -->
<div class="mb-12">
    <div class="flex items-center mb-8">
        <div class="flex items-center justify-center w-10 h-10 bg-blue-600 text-white rounded-full text-sm font-bold mr-4">4</div>
        <div>
            <h3 class="text-xl font-bold text-gray-900">Media & Files</h3>
            <p class="text-gray-600">Upload cover image, PDF file, and additional media</p>
        </div>
    </div>

    <div class="ml-14 grid grid-cols-1 lg:grid-cols-2 gap-8">
    <!-- Cover Image Upload -->
    <div class="space-y-4">
        <label class="block text-sm font-semibold text-gray-700 mb-3">Cover Image</label>

        @if($existingCoverImage && !$removeCoverImage)
            <div class="bg-green-50 rounded-xl p-4 border border-green-200 mb-4">
                <p class="text-sm font-medium text-green-800 mb-2">Current Cover:</p>
                <div class="flex items-start flex-col space-y-4">
                    <img src="{{  $existingCoverImage}}" alt="Current Cover" class="h-40 w-28 object-cover rounded-lg border-2 border-green-300 shadow-sm">
                    <button type="button" wire:click="removeExistingCoverImage"
                            class="px-3 py-2 bg-red-100 text-red-700 rounded-lg hover:bg-red-200 transition-colors text-sm">
                        <svg class="w-4 h-4 mr-1 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                        Remove Current Cover
                    </button>
                </div>
            </div>
        @endif

        <div class="flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-xl hover:border-blue-400 transition-colors">
            <div class="space-y-1 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                    <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
                <div class="flex text-sm text-gray-600">
                    <label class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500">
                        <span>{{ $mode === 'edit' && $existingCoverImage && !$removeCoverImage ? 'Replace cover image' : 'Upload a file' }}</span>
                        <input type="file" wire:model="coverImage" accept="image/*" class="sr-only">
                    </label>
                    <p class="pl-1">or drag and drop</p>
                </div>
                <p class="text-xs text-gray-500">PNG, JPG, JPEG up to 5MB</p>
            </div>
        </div>

        @if ($coverImage)
            <div class="bg-green-50 rounded-xl p-4 border border-green-200">
                <p class="text-sm font-medium text-green-800 mb-2">New Cover Preview:</p>
                @if(method_exists($coverImage, 'temporaryUrl'))
                    <img src="{{ $coverImage->temporaryUrl() }}" alt="Cover Preview" class="h-40 w-28 object-cover rounded-lg border-2 border-green-300 shadow-sm">
                @else
                    <div class="h-40 w-28 bg-gray-200 rounded-lg border-2 border-green-300 shadow-sm flex items-center justify-center">
                        <span class="text-sm text-gray-600">Preview Loading...</span>
                    </div>
                @endif
            </div>
        @endif
        @error('coverImage') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

        <!-- PDF Upload -->
        <div class="space-y-4">
            <label class="block text-sm font-semibold text-gray-700 mb-3">
                PDF File
                <span class="text-xs bg-purple-100 text-purple-700 px-2 py-1 rounded-full ml-1">Required for softcopy</span>
            </label>

            @if($existingPdfFile && !$removePdfFile)
                <div class="bg-purple-50 rounded-xl p-4 border border-purple-200 mb-4">
                    <p class="text-sm font-medium text-purple-800 mb-2 flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        Current PDF File
                    </p>
                    <div class="flex items-start flex-col space-y-2 justify-between">
                        <p class="text-sm text-purple-600">{{ basename($existingPdfFile) }}</p>
                        <button type="button" wire:click="removeExistingPdfFile"
                                class="px-3 py-2 bg-red-100 text-red-700 rounded-lg hover:bg-red-200 transition-colors text-sm">
                            <svg class="w-4 h-4 mr-1 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                            Remove Current PDF
                        </button>
                    </div>
                </div>
            @endif

            <div class="flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-xl hover:border-purple-400 transition-colors">
                <div class="space-y-1 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 48 48">
                        <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m6 0h6m-6 6v6m-6-6v6m6 0v6"/>
                    </svg>
                    <div class="flex text-sm text-gray-600">
                        <label class="relative cursor-pointer bg-white rounded-md font-medium text-purple-600 hover:text-purple-500">
                            <span>{{ $mode === 'edit' && $existingPdfFile && !$removePdfFile ? 'Replace PDF file' : 'Upload PDF file' }}</span>
                            <input type="file" wire:model="pdfFile" accept=".pdf" class="sr-only">
                        </label>
                    </div>
                    <p class="text-xs text-gray-500">PDF files only, up to 100MB</p>
                </div>
            </div>

            @if ($pdfFile)
                <div class="bg-purple-50 rounded-xl p-4 border border-purple-200">
                    <p class="text-sm font-medium text-purple-800 mb-2 flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        New PDF File Selected: {{ $pdfFile->getClientOriginalName() }}
                    </p>
                    <p class="text-sm text-purple-600">Size: {{ number_format($pdfFile->getSize() / 1024 / 1024, 2) }} MB</p>
                </div>
            @endif
            @error('pdfFile') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>
    </div>
    <div class="mt-8 space-y-8">
        <!-- Audio Content -->
        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
            <div class="p-6 bg-gradient-to-r from-blue-50 to-blue-100">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3"/>
                        </svg>
                        <h4 class="text-lg font-semibold text-gray-900">Audio Content</h4>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" wire:model.live="hasAudio" class="sr-only peer">
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                    </label>
                </div>
            </div>

            @if($hasAudio)
                <div class="p-6 space-y-6">
                    <!-- Single Audio Upload -->
                    <div>
                        <h5 class="text-sm font-medium text-gray-900 mb-4">Main Audio File</h5>
                        @if($existingSingleAudioFile && !$removeSingleAudioFile)
                            <div class="mb-4 p-4 bg-blue-50 rounded-lg border border-blue-200">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-2">
                                        <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3"/>
                                        </svg>
                                        <span class="text-sm font-medium text-blue-900">{{ basename($existingSingleAudioFile) }}</span>
                                    </div>
                                    <button type="button" wire:click="removeExistingSingleAudioFile" class="text-sm text-red-600 hover:text-red-800">Remove</button>
                                </div>
                            </div>
                        @endif

                        <div class="flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg hover:border-blue-400 transition-colors">
                            <div class="space-y-1 text-center">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3"/>
                                </svg>
                                <div class="flex text-sm text-gray-600">
                                    <label class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500">
                                        <span>Upload audio file</span>
                                        <input type="file" wire:model="singleAudioFile" class="sr-only" accept="audio/*">
                                    </label>
                                </div>
                                <p class="text-xs text-gray-500">MP3, WAV up to 50MB</p>
                            </div>
                        </div>
                        @error('singleAudioFile') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <!-- Chapter Audio Files -->
                    @if($tableOfContents && count($tableOfContents) > 0)
                        <div class="border-t border-gray-200 pt-6">
                            <h5 class="text-sm font-medium text-gray-900 mb-4">Chapter Audio Files (Optional)</h5>
                            <div class="space-y-4">
                                @foreach($tableOfContents as $index => $chapter)
                                    <div class="p-4 bg-gray-50 rounded-lg">
                                        <div class="mb-2">
                                            <h6 class="text-sm font-medium text-gray-900">Chapter {{ $chapter['chapter'] }}: {{ $chapter['title'] }}</h6>
                                        </div>
                                        <input type="file" wire:model="chapterAudioFiles.{{ $index }}"
                                               accept="audio/*"
                                               class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                                        @error("chapterAudioFiles.$index") <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            @endif
        </div>

        <!-- Video Content -->
        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
            <div class="p-6 bg-gradient-to-r from-purple-50 to-purple-100">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                        </svg>
                        <h4 class="text-lg font-semibold text-gray-900">Video Content</h4>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" wire:model.live="hasVideo" class="sr-only peer">
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-purple-300 rounded-full peer peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-purple-600"></div>
                    </label>
                </div>
            </div>

            @if($hasVideo)
                <div class="p-6 space-y-6">
                    <!-- Single Video Upload -->
                    <div>
                        <h5 class="text-sm font-medium text-gray-900 mb-4">Main Video File</h5>
                        @if($existingSingleVideoFile && !$removeSingleVideoFile)
                            <div class="mb-4 p-4 bg-purple-50 rounded-lg border border-purple-200">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-2">
                                        <svg class="w-5 h-5 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                        </svg>
                                        <span class="text-sm font-medium text-purple-900">{{ basename($existingSingleVideoFile) }}</span>
                                    </div>
                                    <button type="button" wire:click="removeExistingSingleVideoFile" class="text-sm text-red-600 hover:text-red-800">Remove</button>
                                </div>
                            </div>
                        @endif

                        <div class="flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg hover:border-purple-400 transition-colors">
                            <div class="space-y-1 text-center">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                </svg>
                                <div class="flex text-sm text-gray-600">
                                    <label class="relative cursor-pointer bg-white rounded-md font-medium text-purple-600 hover:text-purple-500">
                                        <span>Upload video file</span>
                                        <input type="file" wire:model="singleVideoFile" class="sr-only" accept="video/*">
                                    </label>
                                </div>
                                <p class="text-xs text-gray-500">MP4, MOV up to 100MB</p>
                            </div>
                        </div>
                        @error('singleVideoFile') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <!-- Chapter Video Files -->
                    @if($tableOfContents && count($tableOfContents) > 0)
                        <div class="border-t border-gray-200 pt-6">
                            <h5 class="text-sm font-medium text-gray-900 mb-4">Chapter Video Files (Optional)</h5>
                            <div class="space-y-4">
                                @foreach($tableOfContents as $index => $chapter)
                                    <div class="p-4 bg-gray-50 rounded-lg">
                                        <div class="mb-2">
                                            <h6 class="text-sm font-medium text-gray-900">Chapter {{ $chapter['chapter'] }}: {{ $chapter['title'] }}</h6>
                                        </div>
                                        <input type="file" wire:model="chapterVideoFiles.{{ $index }}"
                                               accept="video/*"
                                               class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-purple-50 file:text-purple-700 hover:file:bg-purple-100">
                                        @error("chapterVideoFiles.$index") <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>
