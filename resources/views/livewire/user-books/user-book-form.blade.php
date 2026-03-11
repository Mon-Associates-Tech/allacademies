<section>
    <div class="min-h-screen bg-gray-50 dark:from-gray-900 dark:via-gray-900 dark:to-gray-900 py-8">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">

            <!-- Header Section -->
            <div class="mb-8">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">
                            {{ $mode === 'edit' ? 'Edit Book' : 'Upload New Book' }}
                        </h1>
                        <p class="text-gray-600 dark:text-gray-400">
                            {{ $mode === 'edit' ? 'Update your book details and settings' : 'Share your knowledge with the world' }}
                        </p>
                    </div>
                    <a href="{{ route('user-books.index') }}"
                       class="hidden sm:inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 border border-gray-300 dark:border-gray-700 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-750 transition-colors">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        Back
                    </a>
                </div>
            </div>

            <!-- Success Message -->
            @if (session()->has('message'))
                <div x-data="{ show: true }"
                     x-show="show"
                     x-init="setTimeout(() => show = false, 5000)"
                     x-transition:enter="transform ease-out duration-300"
                     x-transition:enter-start="translate-y-2 opacity-0"
                     x-transition:enter-end="translate-y-0 opacity-100"
                     class="mb-6 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-4 shadow-sm">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="w-10 h-10 rounded-lg bg-green-100 dark:bg-green-900/40 flex items-center justify-center mr-3">
                                <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-semibold text-green-900 dark:text-green-100">Success!</h3>
                                <p class="text-sm text-green-700 dark:text-green-300">{{ session('message') }}</p>
                            </div>
                        </div>
                        <button @click="show = false" class="text-green-500 hover:text-green-700 dark:text-green-400 dark:hover:text-green-200">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                            </svg>
                        </button>
                    </div>
                </div>
            @endif

            <!-- Error Messages -->
            @if ($errors->any())
                <div class="mb-6 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4 shadow-sm">
                    <div class="flex items-start">
                        <div class="w-10 h-10 rounded-lg bg-red-100 dark:bg-red-900/40 flex items-center justify-center mr-3 flex-shrink-0">
                            <svg class="w-5 h-5 text-red-600 dark:text-red-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <h3 class="font-semibold text-red-900 dark:text-red-100 mb-2">Please fix the following errors:</h3>
                            <ul class="list-disc list-inside space-y-1">
                                @foreach ($errors->all() as $error)
                                    <li class="text-sm text-red-700 dark:text-red-300">{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Main Form Card -->
            <form wire:submit.prevent="submit">
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">

                    <div class="p-6 lg:p-8 space-y-8">

                        <!-- Section 1: Basic Information -->
                        <div>
                            <div class="flex items-center mb-6 pb-4 border-b border-gray-200 dark:border-gray-700">
                                <div class="w-10 h-10 rounded-lg bg-purple-100 dark:bg-purple-900/30 flex items-center justify-center text-purple-600 dark:text-purple-400 font-bold mr-3">
                                    1
                                </div>
                                <div>
                                    <h2 class="text-xl font-bold text-gray-900 dark:text-white">Basic Information</h2>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Tell us about your book</p>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                                <!-- Title -->
                                <div class="lg:col-span-2">
                                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                        Book Title <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text"
                                           wire:model.live="title"
                                           class="w-full px-4 py-2.5 bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-700 rounded-lg focus:border-purple-500 focus:ring-2 focus:ring-purple-500/20 dark:text-white transition-colors"
                                           placeholder="Enter your book title">
                                    @error('title')
                                    <p class="mt-1.5 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Status -->
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                        Publication Status <span class="text-red-500">*</span>
                                    </label>
                                    <select wire:model="status"
                                            class="w-full px-4 py-2.5 bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-700 rounded-lg focus:border-purple-500 focus:ring-2 focus:ring-purple-500/20 dark:text-white transition-colors">
                                        <option value="draft">📝 Draft</option>
                                        <option value="published">✅ Published</option>
                                        <option value="archived">📦 Archived</option>
                                    </select>
                                    @error('status')
                                    <p class="mt-1.5 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Pages -->
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                        Number of Pages
                                    </label>
                                    <input type="number"
                                           wire:model="pages"
                                           class="w-full px-4 py-2.5 bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-700 rounded-lg focus:border-purple-500 focus:ring-2 focus:ring-purple-500/20 dark:text-white transition-colors"
                                           placeholder="e.g., 250">
                                    @error('pages')
                                    <p class="mt-1.5 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Description -->
                                <div class="lg:col-span-2">
                                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                        Description
                                    </label>
                                    <textarea wire:model="description"
                                              rows="4"
                                              class="w-full px-4 py-2.5 bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-700 rounded-lg focus:border-purple-500 focus:ring-2 focus:ring-purple-500/20 dark:text-white transition-colors resize-none"
                                              placeholder="Describe your book, its content, and what readers can learn from it..."></textarea>
                                    @error('description')
                                    <p class="mt-1.5 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Edition -->
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                        Edition
                                    </label>
                                    <input type="text"
                                           wire:model="edition"
                                           class="w-full px-4 py-2.5 bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-700 rounded-lg focus:border-purple-500 focus:ring-2 focus:ring-purple-500/20 dark:text-white transition-colors"
                                           placeholder="e.g., First Edition">
                                    @error('edition')
                                    <p class="mt-1.5 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Publisher -->
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                        Publisher
                                    </label>
                                    <input type="text"
                                           wire:model="publisher"
                                           class="w-full px-4 py-2.5 bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-700 rounded-lg focus:border-purple-500 focus:ring-2 focus:ring-purple-500/20 dark:text-white transition-colors"
                                           placeholder="Publisher name">
                                    @error('publisher')
                                    <p class="mt-1.5 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Section 2: Files Upload -->
                        <div>
                            <div class="flex items-center mb-6 pb-4 border-b border-gray-200 dark:border-gray-700">
                                <div class="w-10 h-10 rounded-lg bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center text-blue-600 dark:text-blue-400 font-bold mr-3">
                                    2
                                </div>
                                <div>
                                    <h2 class="text-xl font-bold text-gray-900 dark:text-white">Upload Files</h2>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Add your book cover and PDF content</p>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                                <!-- Cover Image -->
                                <div class="bg-gray-50 dark:bg-gray-900/50 rounded-lg p-6 border-2 border-dashed border-gray-300 dark:border-gray-600 dark:border-gray-700 hover:border-purple-400 dark:hover:border-purple-600 transition-colors">
                                    <div class="text-center">
                                        <div class="w-12 h-12 mx-auto mb-3 rounded-lg bg-purple-100 dark:bg-purple-900/30 flex items-center justify-center">
                                            <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                            </svg>
                                        </div>
                                        <h3 class="font-semibold text-gray-900 dark:text-white mb-1">Cover Image</h3>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 mb-4">JPG, PNG (Max 2MB)</p>

                                        @if($existingCoverImage)
                                            <div class="mb-4">
                                                <img src="{{ asset('storage/' . $existingCoverImage) }}"
                                                     alt="Cover"
                                                     class="w-24 h-32 object-cover rounded-lg mx-auto shadow-sm border border-gray-200 dark:border-gray-700">
                                                <button type="button"
                                                        wire:click="removeExistingCoverImage"
                                                        class="mt-2 text-sm text-red-600 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300 font-medium">
                                                    Remove
                                                </button>
                                            </div>
                                        @endif

                                        <input type="file"
                                               wire:model="coverImage"
                                               id="coverImage"
                                               class="hidden">
                                        <label for="coverImage"
                                               class="inline-flex items-center px-4 py-2 bg-purple-600 hover:bg-purple-700 dark:bg-purple-600 dark:hover:bg-purple-700 text-white text-sm font-medium rounded-lg cursor-pointer transition-colors">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                            </svg>
                                            Choose Image
                                        </label>
                                        @error('coverImage')
                                        <p class="mt-2 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>

                                <!-- PDF File -->
                                <div class="bg-gray-50 dark:bg-gray-900/50 rounded-lg p-6 border-2 border-dashed border-gray-300 dark:border-gray-600 dark:border-gray-700 hover:border-blue-400 dark:hover:border-blue-600 transition-colors">
                                    <div class="text-center">
                                        <div class="w-12 h-12 mx-auto mb-3 rounded-lg bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center">
                                            <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                            </svg>
                                        </div>
                                        <h3 class="font-semibold text-gray-900 dark:text-white mb-1">Full PDF</h3>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 mb-4">Complete book content</p>

                                        @if($existingPdfFile)
                                            <div class="mb-4 p-3 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
                                                <svg class="w-8 h-8 mx-auto text-blue-600 dark:text-blue-400 mb-2" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"/>
                                                </svg>
                                                <a href="{{ asset('storage/' . $existingPdfFile) }}"
                                                   target="_blank"
                                                   class="text-sm text-blue-600 hover:text-blue-700 dark:text-blue-400 font-medium">
                                                    View PDF
                                                </a>
                                                <button type="button"
                                                        wire:click="removeExistingPdfFile"
                                                        class="block mt-2 mx-auto text-sm text-red-600 hover:text-red-700 dark:text-red-400 font-medium">
                                                    Remove
                                                </button>
                                            </div>
                                        @endif

                                        <input type="file"
                                               wire:model="pdfFile"
                                               id="pdfFile"
                                               accept="application/pdf"
                                               class="hidden">
                                        <label for="pdfFile"
                                               class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 dark:bg-blue-600 dark:hover:bg-blue-700 text-white text-sm font-medium rounded-lg cursor-pointer transition-colors">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                            </svg>
                                            Upload PDF
                                        </label>
                                        @error('pdfFile')
                                        <p class="mt-2 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Sample PDF -->
                                <div class="bg-gray-50 dark:bg-gray-900/50 rounded-lg p-6 border-2 border-dashed border-gray-300 dark:border-gray-600 dark:border-gray-700 hover:border-indigo-400 dark:hover:border-indigo-600 transition-colors">
                                    <div class="text-center">
                                        <div class="w-12 h-12 mx-auto mb-3 rounded-lg bg-indigo-100 dark:bg-indigo-900/30 flex items-center justify-center">
                                            <svg class="w-6 h-6 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                        </div>
                                        <h3 class="font-semibold text-gray-900 dark:text-white mb-1">Sample PDF</h3>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 mb-4">Preview for readers</p>

                                        @if($existingSamplePdfFile)
                                            <div class="mb-4 p-3 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
                                                <svg class="w-8 h-8 mx-auto text-indigo-600 dark:text-indigo-400 mb-2" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"/>
                                                </svg>
                                                <a href="{{ asset('storage/' . $existingSamplePdfFile) }}"
                                                   target="_blank"
                                                   class="text-sm text-indigo-600 hover:text-indigo-700 dark:text-indigo-400 font-medium">
                                                    View Sample
                                                </a>
                                                <button type="button"
                                                        wire:click="removeExistingSamplePdfFile"
                                                        class="block mt-2 mx-auto text-sm text-red-600 hover:text-red-700 dark:text-red-400 font-medium">
                                                    Remove
                                                </button>
                                            </div>
                                        @endif

                                        <input type="file"
                                               wire:model="samplePdfFile"
                                               id="samplePdfFile"
                                               accept="application/pdf"
                                               class="hidden">
                                        <label for="samplePdfFile"
                                               class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 dark:bg-indigo-600 dark:hover:bg-indigo-700 text-white text-sm font-medium rounded-lg cursor-pointer transition-colors">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                            </svg>
                                            Upload Sample
                                        </label>
                                        @error('samplePdfFile')
                                        <p class="mt-2 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Section 3: Table of Contents -->
                        <div>
                            <div class="flex items-center justify-between mb-6 pb-4 border-b border-gray-200 dark:border-gray-700">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 rounded-lg bg-emerald-100 dark:bg-emerald-900/30 flex items-center justify-center text-emerald-600 dark:text-emerald-400 font-bold mr-3">
                                        3
                                    </div>
                                    <div>
                                        <h2 class="text-xl font-bold text-gray-900 dark:text-white">Table of Contents</h2>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">Organize your book chapters (optional)</p>
                                    </div>
                                </div>
                                <button type="button"
                                        wire:click="toggleTableOfContents"
                                        class="inline-flex items-center px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium rounded-lg transition-colors">
                                    {{ $showTableOfContents ? 'Hide' : 'Show' }}
                                </button>
                            </div>

                            @if($showTableOfContents)
                                <div class="space-y-4">
                                    @foreach($tableOfContents as $index => $chapter)
                                        <div class="bg-gray-50 dark:bg-gray-900/50 rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
                                            <!-- Chapter Header -->
                                            <div class="p-4 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
                                                <div class="flex items-center justify-between">
                                                    <div class="flex items-center flex-1">
                                                        <div class="w-8 h-8 rounded-lg bg-emerald-100 dark:bg-emerald-900/30 flex items-center justify-center text-emerald-600 dark:text-emerald-400 font-bold text-sm mr-3">
                                                            {{ $chapter['chapter'] }}
                                                        </div>
                                                        <h4 class="font-semibold text-gray-900 dark:text-white">{{ $chapter['title'] ?: 'Untitled Chapter' }}</h4>
                                                    </div>
                                                    <div class="flex items-center space-x-2">
                                                        <button type="button"
                                                                wire:click="toggleChapter({{ $index }})"
                                                                class="px-3 py-1.5 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
                                                            {{ in_array($index, $expandedChapters) ? 'Collapse' : 'Expand' }}
                                                        </button>
                                                        @if(count($tableOfContents) > 1)
                                                            <button type="button"
                                                                    wire:click="removeChapter({{ $index }})"
                                                                    class="px-3 py-1.5 bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400 text-sm font-medium rounded-lg hover:bg-red-200 dark:hover:bg-red-900/50 transition-colors">
                                                                Remove
                                                            </button>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Chapter Details (Expanded) -->
                                            @if(in_array($index, $expandedChapters))
                                                <div class="p-6 space-y-4">
                                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                        <div>
                                                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Chapter Title *</label>
                                                            <input type="text"
                                                                   wire:model="tableOfContents.{{ $index }}.title"
                                                                   class="w-full px-3 py-2 bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-700 rounded-lg focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 dark:text-white text-sm">
                                                            @error("tableOfContents.{$index}.title")
                                                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                                            @enderror
                                                        </div>
                                                        <div>
                                                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Chapter Number *</label>
                                                            <input type="number"
                                                                   wire:model="tableOfContents.{{ $index }}.chapter"
                                                                   class="w-full px-3 py-2 bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-700 rounded-lg focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 dark:text-white text-sm">
                                                            @error("tableOfContents.{$index}.chapter")
                                                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                                            @enderror
                                                        </div>
                                                        <div>
                                                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Start Page *</label>
                                                            <input type="number"
                                                                   wire:model="tableOfContents.{{ $index }}.page_start"
                                                                   class="w-full px-3 py-2 bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-700 rounded-lg focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 dark:text-white text-sm">
                                                            @error("tableOfContents.{$index}.page_start")
                                                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                                            @enderror
                                                        </div>
                                                        <div>
                                                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">End Page *</label>
                                                            <input type="number"
                                                                   wire:model="tableOfContents.{{ $index }}.page_end"
                                                                   class="w-full px-3 py-2 bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-700 rounded-lg focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 dark:text-white text-sm">
                                                            @error("tableOfContents.{$index}.page_end")
                                                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                                            @enderror
                                                        </div>
                                                        <div class="md:col-span-2">
                                                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Description</label>
                                                            <textarea wire:model="tableOfContents.{{ $index }}.description"
                                                                      rows="2"
                                                                      class="w-full px-3 py-2 bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-700 rounded-lg focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 dark:text-white text-sm resize-none"></textarea>
                                                        </div>
                                                    </div>

                                                    <!-- Sections -->
                                                    @if(!empty($chapter['sections']))
                                                        <div class="mt-6 space-y-3">
                                                            <div class="flex items-center justify-between mb-3">
                                                                <h5 class="font-semibold text-gray-900 dark:text-white text-sm">Sections</h5>
                                                                <div class="space-x-2">
                                                                    <button type="button"
                                                                            wire:click="generateSections({{ $index }})"
                                                                            class="px-3 py-1.5 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors text-xs font-medium">
                                                                        Auto-Generate
                                                                    </button>
                                                                    <button type="button"
                                                                            wire:click="addSection({{ $index }})"
                                                                            class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg transition-colors text-xs font-medium">
                                                                        + Add Section
                                                                    </button>
                                                                </div>
                                                            </div>
                                                            @foreach($chapter['sections'] as $sectionIndex => $section)
                                                                <div class="bg-white dark:bg-gray-800 rounded-lg p-4 border border-gray-200 dark:border-gray-700">
                                                                    <div class="flex items-center justify-between mb-3">
                                                                        <span class="font-medium text-gray-900 dark:text-white text-sm">{{ $section['title'] }}</span>
                                                                        <button type="button"
                                                                                wire:click="removeSection({{ $index }}, {{ $sectionIndex }})"
                                                                                class="text-red-600 hover:text-red-700 dark:text-red-400 text-xs font-medium">
                                                                            Remove
                                                                        </button>
                                                                    </div>
                                                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                                                                        <div>
                                                                            <input type="text"
                                                                                   wire:model="tableOfContents.{{ $index }}.sections.{{ $sectionIndex }}.title"
                                                                                   placeholder="Section title"
                                                                                   class="w-full px-3 py-2 bg-gray-50 dark:bg-gray-900 border border-gray-300 dark:border-gray-600 rounded-lg text-xs dark:text-white">
                                                                        </div>
                                                                        <div>
                                                                            <input type="number"
                                                                                   wire:model="tableOfContents.{{ $index }}.sections.{{ $sectionIndex }}.page_start"
                                                                                   placeholder="Start page"
                                                                                   class="w-full px-3 py-2 bg-gray-50 dark:bg-gray-900 border border-gray-300 dark:border-gray-600 rounded-lg text-xs dark:text-white">
                                                                        </div>
                                                                        <div>
                                                                            <input type="number"
                                                                                   wire:model="tableOfContents.{{ $index }}.sections.{{ $sectionIndex }}.page_end"
                                                                                   placeholder="End page"
                                                                                   class="w-full px-3 py-2 bg-gray-50 dark:bg-gray-900 border border-gray-300 dark:border-gray-600 rounded-lg text-xs dark:text-white">
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    @else
                                                        <button type="button"
                                                                wire:click="addSection({{ $index }})"
                                                                class="w-full py-3 bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors font-medium text-sm border-2 border-dashed border-gray-300 dark:border-gray-600 dark:border-gray-600">
                                                            + Add First Section
                                                        </button>
                                                    @endif
                                                </div>
                                            @endif
                                        </div>
                                    @endforeach

                                    <button type="button"
                                            wire:click="addChapter"
                                            class="w-full py-3 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg transition-colors font-medium">
                                        + Add New Chapter
                                    </button>
                                </div>
                            @endif
                        </div>

                        <!-- Section 4: Sharing -->
                        <div>
                            <div class="flex items-center mb-6 pb-4 border-b border-gray-200 dark:border-gray-700">
                                <div class="w-10 h-10 rounded-lg bg-pink-100 dark:bg-pink-900/30 flex items-center justify-center text-pink-600 dark:text-pink-400 font-bold mr-3">
                                    4
                                </div>
                                <div>
                                    <h2 class="text-xl font-bold text-gray-900 dark:text-white">Share Your Book</h2>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Invite friends to read (max {{ $maxShares }} users)</p>
                                </div>
                            </div>

                            <div class="bg-pink-50 dark:bg-pink-900/10 rounded-lg p-6 border border-pink-200 dark:border-pink-900/50">
                                <div class="flex items-start mb-4">
                                    <svg class="w-5 h-5 text-pink-600 dark:text-pink-400 mr-3 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <div>
                                        <h3 class="font-semibold text-gray-900 dark:text-white mb-1 text-sm">Share with Email</h3>
                                        <p class="text-xs text-gray-600 dark:text-gray-400">Enter email addresses separated by commas</p>
                                    </div>
                                </div>
                                <textarea wire:model="emails"
                                          rows="4"
                                          placeholder="friend1@example.com, friend2@example.com, friend3@example.com"
                                          class="w-full px-4 py-3 bg-white dark:bg-gray-900 border border-pink-300 dark:border-pink-900/50 rounded-lg focus:border-pink-500 focus:ring-2 focus:ring-pink-500/20 dark:text-white transition-colors resize-none text-sm"></textarea>
                                @error('emails')
                                <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Submit Buttons -->
                        <div class="flex flex-col sm:flex-row gap-3 pt-6 border-t border-gray-200 dark:border-gray-700">
                            <button type="button"
                                    wire:click="cancel"
                                    class="flex-1 sm:flex-none px-6 py-2.5 bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-650 transition-colors font-medium">
                                Cancel
                            </button>
                            <button type="submit"
                                    class="flex-1 px-6 py-2.5 bg-purple-600 hover:bg-purple-700 dark:bg-purple-600 dark:hover:bg-purple-700 text-white rounded-lg transition-colors font-medium">
                                {{ $mode === 'edit' ? 'Update Book' : 'Publish Book' }}
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</section>
