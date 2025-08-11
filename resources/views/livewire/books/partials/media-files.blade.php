<!-- Step 4: Media Files -->
<div class="mb-12">
    <div class="flex items-center mb-8">
        <div class="flex items-center justify-center w-10 h-10 bg-blue-600 text-white rounded-full text-sm font-bold mr-4">4</div>
        <div>
            <h3 class="text-xl font-bold text-gray-900">Media & Files</h3>
            <p class="text-gray-600">Upload cover image and PDF file</p>
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
</div>
