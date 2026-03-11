<div>
    @if($isOpen)
        <div class="fixed inset-0 z-50 overflow-y-auto" x-data="{ open: @entangle('isOpen') }">
            <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" wire:click="close"></div>

                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-6xl sm:w-full">
                    <div class="bg-white">
                        <!-- Header -->
                        <div class="border-b border-gray-200 px-6 py-4">
                            <div class="flex items-center justify-between">
                                <h3 class="text-lg leading-6 font-medium text-gray-900">
                                    Select Media {{ $multiple ? '(Multiple)' : '' }}
                                </h3>
                                <button
                                    wire:click="close"
                                    class="text-gray-400 hover:text-gray-600"
                                >
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            </div>

                            <!-- Breadcrumb -->
                            <nav class="flex mt-4" aria-label="Breadcrumb">
                                <ol class="flex items-center space-x-2">
                                    @foreach($breadcrumb as $crumb)
                                        <li class="flex items-center">
                                            @if(!$loop->first)
                                                <svg class="flex-shrink-0 h-4 w-4 text-gray-400 mx-2" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                                                </svg>
                                            @endif

                                            @if($loop->last)
                                                <span class="text-gray-700 font-medium text-sm">{{ $crumb['name'] }}</span>
                                            @else
                                                <button
                                                    wire:click="navigateToFolder({{ $crumb['id'] }})"
                                                    class="text-blue-600 hover:text-blue-800 font-medium text-sm"
                                                >
                                                    {{ $crumb['name'] }}
                                                </button>
                                            @endif
                                        </li>
                                    @endforeach
                                </ol>
                            </nav>

                            <!-- Search and filters -->
                            <div class="flex items-center space-x-4 mt-4">
                                <div class="flex-1 relative">
                                    <input
                                        type="text"
                                        wire:model.debounce.300ms="search"
                                        placeholder="Search files..."
                                        class="w-full pl-10 pr-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    >
                                    <svg class="absolute left-3 top-2.5 h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                    </svg>
                                </div>

                                @if(empty($acceptedTypes))
                                    <select
                                        wire:model="filterMimeType"
                                        class="border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    >
                                        <option value="">All Files</option>
                                        <option value="image">Images</option>
                                        <option value="video">Videos</option>
                                        <option value="application">Documents</option>
                                    </select>
                                @endif
                            </div>
                        </div>

                        <!-- Content -->
                        <div class="px-6 py-4" style="height: 60vh; overflow-y: auto;">
                            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
                                <!-- Folders -->
                                @foreach($folders as $folder)
                                    <div
                                        class="border-2 border-gray-200 rounded-lg p-4 cursor-pointer hover:border-gray-300 hover:shadow-md transition-all duration-200"
                                        wire:click="navigateToFolder({{ $folder->id }})"
                                    >
                                        <div class="flex flex-col items-center">
                                            <svg class="w-12 h-12 text-blue-500 mb-2" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M2 6a2 2 0 012-2h5l2 2h5a2 2 0 012 2v6a2 2 0 01-2 2H4a2 2 0 01-2-2V6z"></path>
                                            </svg>
                                            <span class="text-sm text-center break-words">{{ $folder->name }}</span>
                                        </div>
                                    </div>
                                @endforeach

                                <!-- Files -->
                                @foreach($files as $file)
                                    <div class="relative group">
                                        <div
                                            class="border-2 rounded-lg p-2 cursor-pointer transition-all duration-200 hover:shadow-md {{ in_array($file->id, $selectedMediaIds) ? 'border-blue-500 bg-blue-50' : 'border-gray-200 hover:border-gray-300' }}"
                                            wire:click="toggleMediaSelection({{ $file->id }})"
                                        >
                                            <!-- Selection indicator -->
                                            @if(in_array($file->id, $selectedMediaIds))
                                                <div class="absolute -top-2 -right-2 w-6 h-6 bg-blue-500 rounded-full flex items-center justify-center z-10">
                                                    <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                                    </svg>
                                                </div>
                                            @endif

                                            <div class="aspect-square mb-2 bg-gray-100 rounded overflow-hidden">
                                                @if($file->isImage())
                                                    <img
                                                        src="{{ $file->url }}"
                                                        alt="{{ $file->alt_text }}"
                                                        class="w-full h-full object-cover"
                                                        loading="lazy"
                                                    >
                                                @else
                                                    <div class="w-full h-full flex items-center justify-center">
                                                        @if($file->isVideo())
                                                            <svg class="w-8 h-8 text-purple-500" fill="currentColor" viewBox="0 0 20 20">
                                                                <path d="M2 6a2 2 0 012-2h6a2 2 0 012 2v8a2 2 0 01-2 2H4a2 2 0 01-2-2V6zM14.553 7.106A1 1 0 0014 8v4a1 1 0 00.553.894l2 1A1 1 0 0018 13V7a1 1 0 00-1.447-.894l-2 1z"></path>
                                                            </svg>
                                                        @elseif($file->isDocument())
                                                            <svg class="w-8 h-8 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                                                <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"></path>
                                                            </svg>
                                                        @else
                                                            <svg class="w-8 h-8 text-gray-500" fill="currentColor" viewBox="0 0 20 20">
                                                                <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"></path>
                                                            </svg>
                                                        @endif
                                                    </div>
                                                @endif
                                            </div>
                                            <p class="text-xs text-center break-words">{{ $file->name }}</p>
                                            <p class="text-xs text-gray-500 text-center">{{ $file->human_size }}</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            @if($folders->isEmpty() && $files->isEmpty())
                                <div class="text-center py-12">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 48 48">
                                        <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                                    </svg>
                                    <h3 class="mt-2 text-sm font-medium text-gray-900">No media found</h3>
                                    <p class="mt-1 text-sm text-gray-500">No files match your current filters.</p>
                                </div>
                            @endif
                        </div>

                        <!-- Footer -->
                        <div class="bg-gray-50 px-6 py-4 flex items-center justify-between">
                            <div class="text-sm text-gray-600">
                                @if(count($selectedMediaIds) > 0)
                                    {{ count($selectedMediaIds) }} file(s) selected
                                @else
                                    No files selected
                                @endif
                            </div>

                            <div class="flex space-x-3">
                                <button
                                    wire:click="close"
                                    class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50"
                                >
                                    Cancel
                                </button>
                                <button
                                    wire:click="selectMedia"
                                    class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed"
                                    {{ empty($selectedMediaIds) ? 'disabled' : '' }}
                                >
                                    Select {{ $multiple ? 'Files' : 'File' }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
