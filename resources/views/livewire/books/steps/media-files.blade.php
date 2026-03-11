<div>
    <div class="flex items-center mb-8">
        <div class="flex items-center justify-center w-10 h-10 bg-blue-600 text-white rounded-full text-sm font-bold mr-4">4</div>
        <div>
            <h3 class="text-xl font-bold text-gray-900">Media Files</h3>
            <p class="text-gray-600">Upload cover image and multimedia content</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Cover Image - Featured -->
        <div class="lg:col-span-3">
            <div class="bg-gradient-to-br from-purple-50 to-pink-50 rounded-2xl p-6 border-2 border-purple-200">
                <div class="flex items-center mb-4">
                    <div class="p-2 bg-purple-600 rounded-lg mr-3">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <div>
                        <h4 class="text-lg font-bold text-gray-900">Book Cover Image</h4>
                        <p class="text-sm text-gray-600">Upload an eye-catching cover (PNG, JPG, GIF up to 2MB)</p>
                    </div>
                </div>
                <div class="flex items-start space-x-6">
                    @if($existingCoverImage && !$coverImage)
                        <div class="flex-shrink-0">
                            <img src="{{ asset('storage/' . $existingCoverImage) }}" alt="Current cover"
                                 class="h-48 w-36 object-cover rounded-xl shadow-lg border-4 border-white">
                            <p class="text-xs text-center text-gray-600 mt-2 font-medium">Current Cover</p>
                        </div>
                    @endif
                    <div class="flex-1">
                        <label class="relative block cursor-pointer group">
                            <div class="border-2 border-dashed border-purple-300 rounded-xl p-8 text-center hover:border-purple-500 hover:bg-purple-50 transition-all">
                                <svg class="mx-auto h-12 w-12 text-purple-400 group-hover:text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                </svg>
                                <p class="mt-2 text-sm font-medium text-gray-700 dark:text-gray-300">Click to upload or drag and drop</p>
                                <p class="text-xs text-gray-500 mt-1">Recommended: 600x900px</p>
                            </div>
                            <input type="file" wire:model="coverImage" accept="image/*" class="hidden">
                        </label>
                        @error('coverImage') <p class="mt-2 text-sm text-red-600 font-medium">{{ $message }}</p> @enderror
                        @if($coverImage)
                            <div class="mt-4 p-4 bg-white rounded-xl shadow-sm border border-green-200">
                                <div class="flex items-center space-x-3">
                                    <img src="{{ $coverImage->temporaryUrl() }}" alt="Preview" class="h-24 w-18 object-cover rounded-lg">
                                    <div class="flex-1">
                                        <p class="text-sm font-semibold text-green-700">✓ New cover ready</p>
                                        <p class="text-xs text-gray-500">{{ $coverImage->getClientOriginalName() }}</p>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- PDF Files -->
        <div class="lg:col-span-2 space-y-4">
            <!-- Main PDF -->
            <div class="bg-white rounded-xl p-5 border-2 border-gray-200 hover:border-blue-300 transition-all">
                <div class="flex items-center mb-3">
                    <div class="p-2 bg-blue-100 rounded-lg mr-3">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h5 class="font-bold text-gray-900">Book PDF @if($hasSoftcopy)<span class="text-red-500">*</span>@endif</h5>
                        <p class="text-xs text-gray-500">Main book content (PDF up to 10MB)</p>
                    </div>
                </div>
                @if($existingPdfFile && !$pdfFile)
                    <div class="mb-3 p-3 bg-green-50 rounded-lg flex items-center justify-between">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-green-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="text-sm font-medium text-green-800">PDF uploaded</span>
                        </div>
                        <a href="{{ asset('storage/' . $existingPdfFile) }}" target="_blank" class="text-sm text-blue-600 hover:text-blue-800 font-medium">View</a>
                    </div>
                @endif
                <input type="file" wire:model="pdfFile" accept=".pdf" class="block w-full text-sm text-gray-600 file:mr-4 file:py-2.5 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-pointer">
                @error('pdfFile') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <!-- Sample PDF -->
            <div class="bg-white rounded-xl p-5 border-2 border-gray-200 hover:border-amber-300 transition-all">
                <div class="flex items-center mb-3">
                    <div class="p-2 bg-amber-100 rounded-lg mr-3">
                        <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h5 class="font-bold text-gray-900">Sample PDF</h5>
                        <p class="text-xs text-gray-500">Preview for readers (PDF up to 5MB)</p>
                    </div>
                </div>
                @if($existingSamplePdfFile && !$samplePdfFile && !$removeSamplePdfFile)
                    <div class="mb-3 p-3 bg-green-50 rounded-lg flex items-center justify-between">
                        <span class="text-sm font-medium text-green-800">Sample uploaded</span>
                        <div class="flex items-center space-x-2">
                            <a href="{{ asset('storage/' . $existingSamplePdfFile) }}" target="_blank" class="text-sm text-blue-600 hover:text-blue-800 font-medium">View</a>
                            <button type="button" wire:click="removeExistingSamplePdfFile" class="text-sm text-red-600 hover:text-red-800 font-medium">Remove</button>
                        </div>
                    </div>
                @endif
                <input type="file" wire:model="samplePdfFile" accept=".pdf" class="block w-full text-sm text-gray-600 file:mr-4 file:py-2.5 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-amber-50 file:text-amber-700 hover:file:bg-amber-100 cursor-pointer">
                @error('samplePdfFile') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
        </div>

        <!-- Single Audio/Video -->
        <div class="space-y-4">
            <!-- Single Audio -->
            <div class="bg-gradient-to-br from-indigo-50 to-purple-50 rounded-xl p-5 border-2 border-indigo-200">
                <div class="flex items-center mb-3">
                    <div class="p-2 bg-indigo-600 rounded-lg mr-3">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3"></path>
                        </svg>
                    </div>
                    <div>
                        <h5 class="font-bold text-gray-900">Audio Book</h5>
                        <p class="text-xs text-gray-600">MP3, WAV, M4A (50MB)</p>
                    </div>
                </div>
                @if($existingSingleAudio && !$singleAudio)
                    <audio controls class="w-full mb-3 rounded-lg">
                        <source src="{{ asset('storage/' . $existingSingleAudio) }}">
                    </audio>
                @endif
                <input type="file" wire:model="singleAudio" accept=".mp3,.wav,.m4a" class="block w-full text-sm text-gray-600 file:mr-4 file:py-2.5 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-indigo-100 file:text-indigo-700 hover:file:bg-indigo-200 cursor-pointer">
                @error('singleAudio') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <!-- Single Video -->
            <div class="bg-gradient-to-br from-pink-50 to-rose-50 rounded-xl p-5 border-2 border-pink-200">
                <div class="flex items-center mb-3">
                    <div class="p-2 bg-pink-600 rounded-lg mr-3">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <div>
                        <h5 class="font-bold text-gray-900">Video Book</h5>
                        <p class="text-xs text-gray-600">MP4, AVI, MOV (100MB)</p>
                    </div>
                </div>
                @if($existingSingleVideo && !$singleVideo)
                    <a href="{{ asset('storage/' . $existingSingleVideo) }}" target="_blank" class="block mb-3 p-3 bg-white rounded-lg text-center text-sm text-blue-600 hover:text-blue-800 font-medium border border-pink-200">
                        📹 View Current Video
                    </a>
                @endif
                <input type="file" wire:model="singleVideo" accept=".mp4,.avi,.mov" class="block w-full text-sm text-gray-600 file:mr-4 file:py-2.5 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-pink-100 file:text-pink-700 hover:file:bg-pink-200 cursor-pointer">
                @error('singleVideo') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
        </div>

        <!-- Chapter Audios -->
        <div class="lg:col-span-3">
            <div class="bg-white rounded-2xl p-6 border-2 border-gray-200">
                <div class="flex items-center justify-between mb-5">
                    <div class="flex items-center">
                        <div class="p-2 bg-indigo-100 rounded-lg mr-3">
                            <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z"></path>
                            </svg>
                        </div>
                        <div>
                            <h4 class="text-lg font-bold text-gray-900">Chapter Audio Files</h4>
                            <p class="text-sm text-gray-600">Upload audio for individual chapters (Udemy-style)</p>
                        </div>
                    </div>
                </div>

                @if(empty($tableOfContents))
                    <div class="bg-amber-50 border-2 border-amber-200 rounded-xl p-6 text-center">
                        <svg class="mx-auto h-12 w-12 text-amber-500 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                        <p class="text-amber-800 font-semibold mb-2">Table of Contents Required</p>
                        <p class="text-sm text-amber-700">Please configure the Table of Contents in Step 3 before uploading chapter-based media.</p>
                        <button type="button" wire:click="goToStep(3)" class="mt-4 px-4 py-2 bg-amber-600 text-white rounded-lg hover:bg-amber-700 font-medium text-sm">
                            Go to Step 3
                        </button>
                    </div>
                @else
                    <!-- Chapter Media Summary -->
                    @if(!empty($this->chapterMediaSummary))
                        <div class="mb-5 bg-gradient-to-r from-indigo-50 to-purple-50 rounded-xl p-4 border border-indigo-200">
                            <h5 class="text-sm font-bold text-gray-900 mb-3">📊 Media Summary by Chapter</h5>
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                                @foreach($this->chapterMediaSummary as $chapterNum => $summary)
                                    <div class="bg-white rounded-lg p-3 border border-indigo-100">
                                        <div class="text-xs font-semibold text-gray-600 mb-1">Ch. {{ $chapterNum }}</div>
                                        <div class="text-xs text-gray-500 truncate mb-2">{{ $summary['title'] }}</div>
                                        <div class="flex items-center space-x-3 text-xs">
                                            <span class="flex items-center {{ $summary['audios'] > 0 ? 'text-indigo-600 font-semibold' : 'text-gray-400' }}">
                                                🎵 {{ $summary['audios'] }}
                                            </span>
                                            <span class="flex items-center {{ $summary['videos'] > 0 ? 'text-pink-600 font-semibold' : 'text-gray-400' }}">
                                                🎬 {{ $summary['videos'] }}
                                            </span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Upload Section -->
                    <div class="bg-gradient-to-br from-indigo-50 to-blue-50 rounded-xl p-5 border-2 border-indigo-200 mb-4">
                        <div class="flex items-center mb-4">
                            <svg class="w-5 h-5 text-indigo-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                            </svg>
                            <span class="text-sm font-bold text-gray-900">Upload New Chapter Audio</span>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Select Chapter</label>
                                <select wire:model.live="selectedChapterForAudio" 
                                        class="w-full px-4 py-2.5 border-2 border-indigo-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                                    <option value="">Choose a chapter...</option>
                                    @foreach($this->availableChapters as $chapter)
                                        <option value="{{ $chapter['value'] }}">{{ $chapter['label'] }}</option>
                                    @endforeach
                                </select>
                                @error('selectedChapterForAudio') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Upload Audio File(s)</label>
                                <input type="file" wire:model="newChapterAudios" accept=".mp3,.wav,.m4a" multiple
                                       class="block w-full text-sm text-gray-600 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-indigo-100 file:text-indigo-700 hover:file:bg-indigo-200 cursor-pointer">
                                @error('newChapterAudios.*') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                                <div wire:loading wire:target="newChapterAudios" class="mt-2 text-xs text-indigo-600">
                                    <svg class="inline w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    Uploading...
                                </div>
                            </div>
                        </div>
                        
                        @if(!$selectedChapterForAudio)
                            <p class="mt-3 text-xs text-amber-600 bg-amber-50 rounded-lg p-2 border border-amber-200">
                                ⚠️ Please select a chapter first before uploading audio files
                            </p>
                        @endif
                    </div>

                    <!-- Existing Chapter Audios -->
                    <div class="space-y-3">
                        @forelse($chapterAudios as $index => $audio)
                            @php
                                $audioData = is_array($audio) ? $audio : ['file' => $audio];
                                $chapterNum = $audioData['chapter'] ?? ($index + 1);
                                $title = $audioData['title'] ?? "Chapter {$chapterNum}";
                            @endphp
                            <div class="bg-gradient-to-r from-indigo-50 to-blue-50 rounded-xl p-4 border-2 border-indigo-200 hover:border-indigo-400 transition-all">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-3 flex-1">
                                        <span class="inline-flex items-center justify-center w-10 h-10 bg-indigo-600 text-white text-sm font-bold rounded-full">
                                            {{ $chapterNum }}
                                        </span>
                                        <div class="flex-1 min-w-0">
                                            <h6 class="text-sm font-bold text-gray-900 truncate">{{ $title }}</h6>
                                            @if(isset($audioData['description']) && $audioData['description'])
                                                <p class="text-xs text-gray-600 truncate">{{ $audioData['description'] }}</p>
                                            @endif
                                            @if(isset($audioData['file']))
                                                <p class="text-xs text-gray-500 mt-1">📁 {{ basename($audioData['file']) }}</p>
                                            @endif
                                        </div>
                                    </div>
                                    <button type="button" wire:click="removeChapterAudio({{ $index }})"
                                            class="ml-3 p-2 bg-red-100 text-red-600 rounded-lg hover:bg-red-200 transition-all">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                </div>
                                @if(isset($audioData['file']))
                                    <audio controls class="w-full mt-3 rounded-lg">
                                        <source src="{{ str_starts_with($audioData['file'], 'http') ? $audioData['file'] : asset('storage/' . $audioData['file']) }}">
                                    </audio>
                                @endif
                            </div>
                        @empty
                            <div class="text-center py-8 text-gray-400 bg-gray-50 rounded-xl border-2 border-dashed border-gray-300 dark:border-gray-600">
                                <svg class="mx-auto h-12 w-12 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3"></path>
                                </svg>
                                <p class="text-sm font-medium">No chapter audios uploaded yet</p>
                                <p class="text-xs mt-1">Select a chapter and upload audio files above</p>
                            </div>
                        @endforelse
                    </div>
                @endif
            </div>
        </div>

        <!-- Chapter Videos -->
        <div class="lg:col-span-3">
            <div class="bg-white rounded-2xl p-6 border-2 border-gray-200">
                <div class="flex items-center justify-between mb-5">
                    <div class="flex items-center">
                        <div class="p-2 bg-pink-100 rounded-lg mr-3">
                            <svg class="w-6 h-6 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4v16M17 4v16M3 8h4m10 0h4M3 12h18M3 16h4m10 0h4M4 20h16a1 1 0 001-1V5a1 1 0 00-1-1H4a1 1 0 00-1 1v14a1 1 0 001 1z"></path>
                            </svg>
                        </div>
                        <div>
                            <h4 class="text-lg font-bold text-gray-900">Chapter Video Files</h4>
                            <p class="text-sm text-gray-600">Upload video for individual chapters (Udemy-style)</p>
                        </div>
                    </div>
                </div>

                @if(empty($tableOfContents))
                    <div class="bg-amber-50 border-2 border-amber-200 rounded-xl p-6 text-center">
                        <svg class="mx-auto h-12 w-12 text-amber-500 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                        <p class="text-amber-800 font-semibold mb-2">Table of Contents Required</p>
                        <p class="text-sm text-amber-700">Please configure the Table of Contents in Step 3 before uploading chapter-based media.</p>
                        <button type="button" wire:click="goToStep(3)" class="mt-4 px-4 py-2 bg-amber-600 text-white rounded-lg hover:bg-amber-700 font-medium text-sm">
                            Go to Step 3
                        </button>
                    </div>
                @else
                    <!-- Upload Section -->
                    <div class="bg-gradient-to-br from-pink-50 to-rose-50 rounded-xl p-5 border-2 border-pink-200 mb-4">
                        <div class="flex items-center mb-4">
                            <svg class="w-5 h-5 text-pink-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                            </svg>
                            <span class="text-sm font-bold text-gray-900">Upload New Chapter Video</span>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Select Chapter</label>
                                <select wire:model.live="selectedChapterForVideo" 
                                        class="w-full px-4 py-2.5 border-2 border-pink-200 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-pink-500 text-sm">
                                    <option value="">Choose a chapter...</option>
                                    @foreach($this->availableChapters as $chapter)
                                        <option value="{{ $chapter['value'] }}">{{ $chapter['label'] }}</option>
                                    @endforeach
                                </select>
                                @error('selectedChapterForVideo') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Upload Video File(s)</label>
                                <input type="file" wire:model="newChapterVideos" accept=".mp4,.avi,.mov" multiple
                                       class="block w-full text-sm text-gray-600 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-pink-100 file:text-pink-700 hover:file:bg-pink-200 cursor-pointer">
                                @error('newChapterVideos.*') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                                <div wire:loading wire:target="newChapterVideos" class="mt-2 text-xs text-pink-600">
                                    <svg class="inline w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    Uploading...
                                </div>
                            </div>
                        </div>
                        
                        @if(!$selectedChapterForVideo)
                            <p class="mt-3 text-xs text-amber-600 bg-amber-50 rounded-lg p-2 border border-amber-200">
                                ⚠️ Please select a chapter first before uploading video files
                            </p>
                        @endif
                    </div>

                    <!-- Existing Chapter Videos -->
                    <div class="space-y-3">
                        @forelse($chapterVideos as $index => $video)
                            @php
                                $videoData = is_array($video) ? $video : ['file' => $video];
                                $chapterNum = $videoData['chapter'] ?? ($index + 1);
                                $title = $videoData['title'] ?? "Chapter {$chapterNum}";
                            @endphp
                            <div class="bg-gradient-to-r from-pink-50 to-rose-50 rounded-xl p-4 border-2 border-pink-200 hover:border-pink-400 transition-all">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-3 flex-1">
                                        <span class="inline-flex items-center justify-center w-10 h-10 bg-pink-600 text-white text-sm font-bold rounded-full">
                                            {{ $chapterNum }}
                                        </span>
                                        <div class="flex-1 min-w-0">
                                            <h6 class="text-sm font-bold text-gray-900 truncate">{{ $title }}</h6>
                                            @if(isset($videoData['description']) && $videoData['description'])
                                                <p class="text-xs text-gray-600 truncate">{{ $videoData['description'] }}</p>
                                            @endif
                                            @if(isset($videoData['file']))
                                                <p class="text-xs text-gray-500 mt-1">📁 {{ basename($videoData['file']) }}</p>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="flex items-center space-x-2">
                                        @if(isset($videoData['file']))
                                            <a href="{{ str_starts_with($videoData['file'], 'http') ? $videoData['file'] : asset('storage/' . $videoData['file']) }}" 
                                               target="_blank"
                                               class="p-2 bg-blue-100 text-blue-600 rounded-lg hover:bg-blue-200 transition-all">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                            </a>
                                        @endif
                                        <button type="button" wire:click="removeChapterVideo({{ $index }})"
                                                class="p-2 bg-red-100 text-red-600 rounded-lg hover:bg-red-200 transition-all">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-8 text-gray-400 bg-gray-50 rounded-xl border-2 border-dashed border-gray-300 dark:border-gray-600">
                                <svg class="mx-auto h-12 w-12 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                </svg>
                                <p class="text-sm font-medium">No chapter videos uploaded yet</p>
                                <p class="text-xs mt-1">Select a chapter and upload video files above</p>
                            </div>
                        @endforelse
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
