<section>
    <div class="h-full flex flex-col bg-white">
        <!-- Header with toolbar -->
        <div class="border-b border-gray-200 p-4">
            <!-- Breadcrumb -->
            <nav class="flex mb-4" aria-label="Breadcrumb">
                <ol class="flex items-center space-x-2">
                    @foreach($breadcrumb as $crumb)
                        <li class="flex items-center">
                            @if(!$loop->first)
                                <svg class="flex-shrink-0 h-4 w-4 text-gray-400 mx-2" fill="currentColor"
                                     viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                          d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                                          clip-rule="evenodd"/>
                                </svg>
                            @endif

                            @if($loop->last)
                                <span class="text-gray-700 font-medium">{{ $crumb['name'] }}</span>
                            @else
                                <button
                                    wire:click="navigateToFolder({{ $crumb['id'] }})"
                                    class="text-blue-600 hover:text-blue-800 font-medium"
                                >
                                    {{ $crumb['name'] }}
                                </button>
                            @endif
                        </li>
                    @endforeach
                </ol>
            </nav>

            <!-- Toolbar -->
            <div class="flex justify-between items-center">
                <div class="flex items-center space-x-4">
                    <!-- Upload button -->
                    <button
                        wire:click="startUpload"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center space-x-2"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 4v16m8-8H4"></path>
                        </svg>
                        <span>Upload</span>
                    </button>

                    <!-- Create folder button -->
                    <button
                        wire:click="$set('showCreateFolderModal', true)"
                        class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg flex items-center space-x-2"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        <span>New Folder</span>
                    </button>

                    <!-- Bulk actions -->
                    @if(count($selectedFiles) > 0 || count($selectedFolders) > 0)
                        <div class="flex items-center space-x-2 border-l pl-4">
                        <span class="text-sm text-gray-600">
                            {{ count($selectedFiles) + count($selectedFolders) }} selected
                        </span>
                            <button
                                wire:click="startMove"
                                class="text-blue-600 hover:text-blue-800 text-sm"
                            >
                                Move
                            </button>
                            <button
                                wire:click="startDelete"
                                class="text-red-600 hover:text-red-800 text-sm"
                            >
                                Delete
                            </button>
                            <button
                                wire:click="deselectAll"
                                class="text-gray-600 hover:text-gray-800 text-sm"
                            >
                                Clear
                            </button>
                        </div>
                    @endif
                </div>

                <div class="flex items-center space-x-4">
                    <!-- Search -->
                    <div class="relative">
                        <input
                            type="text"
                            wire:model.debounce.300ms="search"
                            placeholder="Search files..."
                            class="w-64 pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        >
                        <svg class="absolute left-3 top-2.5 h-5 w-5 text-gray-400" fill="none" stroke="currentColor"
                             viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>

                    <!-- Filter -->
                    <select
                        wire:model="filterMimeType"
                        class="border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    >
                        <option value="">All Files</option>
                        <option value="image">Images</option>
                        <option value="video">Videos</option>
                        <option value="application">Documents</option>
                    </select>

                    <!-- View toggle -->
                    <div class="flex bg-gray-100 rounded-lg p-1">
                        <button
                            wire:click="switchView('grid')"
                            class="p-2 rounded {{ $view === 'grid' ? 'bg-white shadow text-blue-600' : 'text-gray-600' }}"
                        >
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path
                                    d="M5 3a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2V5a2 2 0 00-2-2H5zM5 11a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2v-2a2 2 0 00-2-2H5zM11 5a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V5zM11 13a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path>
                            </svg>
                        </button>
                        <button
                            wire:click="switchView('list')"
                            class="p-2 rounded {{ $view === 'list' ? 'bg-white shadow text-blue-600' : 'text-gray-600' }}"
                        >
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                      d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z"
                                      clip-rule="evenodd"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Content area -->
        <div class="flex-1 overflow-auto p-4">
            @if($view === 'grid')
                <!-- Grid view -->
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 xl:grid-cols-8 gap-4">
                    <!-- Folders -->
                    @foreach($folders as $folder)
                        <div class="group relative">
                            <div
                                class="border-2 rounded-lg p-4 cursor-pointer transition-all duration-200 hover:shadow-md {{ in_array($folder->id, $selectedFolders) ? 'border-blue-500 bg-blue-50' : 'border-gray-200 hover:border-gray-300' }}"
                                x-on:click="$wire.navigateToFolder({{ $folder->id }})"
                                x-on:click.ctrl="$wire.toggleFolderSelection({{ $folder->id }})"
                            >
                                <div class="flex flex-col items-center">
                                    <svg class="w-12 h-12 text-blue-500 mb-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path
                                            d="M2 6a2 2 0 012-2h5l2 2h5a2 2 0 012 2v6a2 2 0 01-2 2H4a2 2 0 01-2-2V6z"></path>
                                    </svg>
                                    <span class="text-sm text-center break-words">{{ $folder->name }}</span>
                                </div>
                            </div>
                        </div>
                    @endforeach

                    <!-- Files -->
                    @foreach($files as $file)
                        <div class="group relative">
                            <div
                                class="border-2 rounded-lg p-2 cursor-pointer transition-all duration-200 hover:shadow-md {{ in_array($file->id, $selectedFiles) ? 'border-blue-500 bg-blue-50' : 'border-gray-200 hover:border-gray-300' }}"
                                x-on:click="$wire.toggleFileSelection({{ $file->id }})"
                                x-on:dblclick="$wire.showFileDetails({{ $file->id }})"
                            >
                                <div class="aspect-square mb-2 bg-gray-100 rounded overflow-hidden">
                                    @if($file->isImage())
                                        <img
                                            src="{{ $file->url }}"
                                            alt="{{ $file->alt_text }}"
                                            class="w-full h-full object-cover"
                                        >
                                    @else
                                        <div class="w-full h-full flex items-center justify-center">
                                            @if($file->isVideo())
                                                <svg class="w-8 h-8 text-purple-500" fill="currentColor"
                                                     viewBox="0 0 20 20">
                                                    <path
                                                        d="M2 6a2 2 0 012-2h6a2 2 0 012 2v8a2 2 0 01-2 2H4a2 2 0 01-2-2V6zM14.553 7.106A1 1 0 0014 8v4a1 1 0 00.553.894l2 1A1 1 0 0018 13V7a1 1 0 00-1.447-.894l-2 1z"></path>
                                                </svg>
                                            @elseif($file->isDocument())
                                                <svg class="w-8 h-8 text-red-500" fill="currentColor"
                                                     viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd"
                                                          d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z"
                                                          clip-rule="evenodd"></path>
                                                </svg>
                                            @else
                                                <svg class="w-8 h-8 text-gray-400" fill="currentColor"
                                                     viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd"
                                                          d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z"
                                                          clip-rule="evenodd"></path>
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
            @else
                <!-- List view -->
                <div class="bg-white shadow rounded-lg overflow-hidden">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                        <tr>
                            <th class="w-8 px-6 py-3">
                                <input
                                    type="checkbox"
                                    wire:click="selectAllFiles"
                                    class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                >
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Name
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Type
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Size
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Modified
                            </th>
                            <th class="w-16 px-6 py-3"></th>
                        </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                        <!-- Folders -->
                        @foreach($folders as $folder)
                            <tr class="hover:bg-gray-50 cursor-pointer"
                                wire:click="navigateToFolder({{ $folder->id }})">
                                <td class="px-6 py-4">
                                    <input
                                        type="checkbox"
                                        wire:click.stop="toggleFolderSelection({{ $folder->id }})"
                                        {{ in_array($folder->id, $selectedFolders) ? 'checked' : '' }}
                                        class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                    >
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <svg class="w-6 h-6 text-blue-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                            <path
                                                d="M2 6a2 2 0 012-2h5l2 2h5a2 2 0 012 2v6a2 2 0 01-2 2H4a2 2 0 01-2-2V6z"></path>
                                        </svg>
                                        <span class="text-sm font-medium text-gray-900">{{ $folder->name }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Folder</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">—</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $folder->updated_at->format('M j, Y') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium"></td>
                            </tr>
                        @endforeach

                        <!-- Files -->
                        @foreach($files as $file)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4">
                                    <input
                                        type="checkbox"
                                        wire:click="toggleFileSelection({{ $file->id }})"
                                        {{ in_array($file->id, $selectedFiles) ? 'checked' : '' }}
                                        class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                    >
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        @if($file->isImage())
                                            <img class="w-8 h-8 rounded mr-3 object-cover" src="{{ $file->url }}"
                                                 alt="{{ $file->alt_text }}">
                                        @else
                                            <div class="w-8 h-8 mr-3 flex items-center justify-center">
                                                @if($file->isVideo())
                                                    <svg class="w-6 h-6 text-purple-500" fill="currentColor"
                                                         viewBox="0 0 20 20">
                                                        <path
                                                            d="M2 6a2 2 0 012-2h6a2 2 0 012 2v8a2 2 0 01-2 2H4a2 2 0 01-2-2V6zM14.553 7.106A1 1 0 0014 8v4a1 1 0 00.553.894l2 1A1 1 0 0018 13V7a1 1 0 00-1.447-.894l-2 1z"></path>
                                                    </svg>
                                                @elseif($file->isDocument())
                                                    <svg class="w-6 h-6 text-red-500" fill="currentColor"
                                                         viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd"
                                                              d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z"
                                                              clip-rule="evenodd"></path>
                                                    </svg>
                                                @else
                                                    <svg class="w-6 h-6 text-gray-500" fill="currentColor"
                                                         viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd"
                                                              d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z"
                                                              clip-rule="evenodd"></path>
                                                    </svg>
                                                @endif
                                            </div>
                                        @endif
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">{{ $file->name }}</div>
                                            <div class="text-sm text-gray-500">{{ $file->original_name }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ ucfirst(explode('/', $file->mime_type)[0]) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $file->human_size }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $file->created_at->format('M j, Y') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <button
                                        wire:click="showFileDetails({{ $file }})"
                                        class="text-blue-600 hover:text-blue-900"
                                    >
                                        Edit
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            @endif

            @if($folders->isEmpty() && $files->isEmpty())
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 48 48">
                        <path
                            d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02"
                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No files</h3>
                    <p class="mt-1 text-sm text-gray-500">Get started by uploading a file.</p>
                    <div class="mt-6">
                        <button
                            wire:click="startUpload"
                            class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700"
                        >
                            Upload File
                        </button>
                    </div>
                </div>
            @endif
        </div>
<!-- Upload Modal -->
@if($showUploadModal)
    <div class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
                 wire:click="$set('showUploadModal', false)"></div>
            <div
                class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form wire:submit.prevent="uploadFiles">
                    @csrf
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Upload Files</h3>

                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Select Files</label>
                                <input
                                    type="file"
                                    wire:model="uploadFiles"
                                    multiple
                                    class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"
                                >
                                @error('uploadFiles.*')
                                    <span class="text-red-500 text-xs">{{ $message }}</span>
                                @enderror

                                <!-- Show selected files count -->
                                @if(count($uploadFiles))
                                    <p class="text-sm text-gray-500 mt-2">{{ count($uploadFiles) }} file(s) selected</p>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button
                            type="submit"
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 sm:ml-3 sm:w-auto sm:text-sm"
                            wire:loading.attr="disabled"
                        >
                            <span wire:loading.remove>Upload</span>
                            <span wire:loading>Uploading...</span>
                        </button>
                        <button
                            type="button"
                            wire:click="$set('showUploadModal', false)"
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
                        >
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endif


        <!-- Move Modal -->
        @if($showMoveModal)
            <div class="fixed inset-0 z-50 overflow-y-auto">
                <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                    <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
                         wire:click="$set('showMoveModal', false)"></div>
                    <div
                        class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                        <form wire:submit.prevent="moveSelected">
                            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Move Items</h3>
                                <p class="text-sm text-gray-600 mb-4">
                                    Move {{ count($selectedFiles) + count($selectedFolders) }} selected items to:
                                </p>

                                <div class="space-y-2 max-h-64 overflow-y-auto border rounded p-3">
                                    <!-- Root option -->
                                    <label class="flex items-center">
                                        <input
                                            type="radio"
                                            wire:model="moveToFolderId"
                                            value=""
                                            class="mr-3"
                                        >
                                        <svg class="w-5 h-5 text-blue-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                            <path
                                                d="M2 6a2 2 0 012-2h5l2 2h5a2 2 0 012 2v6a2 2 0 01-2 2H4a2 2 0 01-2-2V6z"></path>
                                        </svg>
                                        Root (Media Library)
                                    </label>

                                    <!-- Show all folders recursively -->
                                    @foreach($this->getAllFolders() as $folder)
                                        <label class="flex items-center">
                                            <input
                                                type="radio"
                                                wire:model="moveToFolderId"
                                                value="{{ $folder->id }}"
                                                class="mr-3"
                                            >
                                            <svg class="w-5 h-5 text-blue-500 mr-2" fill="currentColor"
                                                 viewBox="0 0 20 20">
                                                <path
                                                    d="M2 6a2 2 0 012-2h5l2 2h5a2 2 0 012 2v6a2 2 0 01-2 2H4a2 2 0 01-2-2V6z"></path>
                                            </svg>
                                            {{ str_repeat('—', substr_count($folder->path, '/')) }} {{ $folder->name }}
                                        </label>
                                    @endforeach
                                </div>
                            </div>

                            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                                <button
                                    type="submit"
                                    class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 sm:ml-3 sm:w-auto sm:text-sm"
                                >
                                    Move Items
                                </button>
                                <button
                                    type="button"
                                    wire:click="$set('showMoveModal', false)"
                                    class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
                                >
                                    Cancel
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endif

        <!-- Delete Confirmation Modal -->
        @if($showDeleteModal)
            <div class="fixed inset-0 z-50 overflow-y-auto">
                <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                    <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
                         wire:click="$set('showDeleteModal', false)"></div>
                    <div
                        class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <div class="sm:flex sm:items-start">
                                <div
                                    class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                                    <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24"
                                         stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                    </svg>
                                </div>
                                <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                                    <h3 class="text-lg leading-6 font-medium text-gray-900">Delete Items</h3>
                                    <div class="mt-2">
                                        <p class="text-sm text-gray-500">
                                            Are you sure you want to
                                            delete {{ count($selectedFiles) + count($selectedFolders) }} selected items?
                                            This action cannot be undone.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                            <button
                                type="button"
                                wire:click="deleteSelected"
                                class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 sm:ml-3 sm:w-auto sm:text-sm"
                            >
                                Delete
                            </button>
                            <button
                                type="button"
                                wire:click="$set('showDeleteModal', false)"
                                class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
                            >
                                Cancel
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Handle drag and drop
            const dropZone = document.querySelector('[x-data]');

            if (dropZone) {
                dropZone.addEventListener('dragover', function (e) {
                    e.preventDefault();
                    e.dataTransfer.dropEffect = 'copy';
                    this.classList.add('bg-blue-50', 'border-blue-300');
                });

                dropZone.addEventListener('dragleave', function (e) {
                    this.classList.remove('bg-blue-50', 'border-blue-300');
                });

                dropZone.addEventListener('drop', function (e) {
                    e.preventDefault();
                    this.classList.remove('bg-blue-50', 'border-blue-300');

                    const files = Array.from(e.dataTransfer.files);
                    if (files.length > 0) {
                        @this.
                        set('uploadFiles', files);
                        @this.
                        set('showUploadModal', true);
                    }
                });
            }

            // Handle keyboard shortcuts
            document.addEventListener('keydown', function (e) {
                // Ctrl/Cmd + A to select all
                if ((e.ctrlKey || e.metaKey) && e.key === 'a') {
                    e.preventDefault();
                    @this.
                    call('selectAllFiles');
                }

                // Escape to deselect all
                if (e.key === 'Escape') {
                    @this.
                    call('deselectAll');
                }

                // Delete key to delete selected
                if (e.key === 'Delete' || e.key === 'Backspace') {
                    if (@this.
                    get('selectedFiles').length > 0 || @this.
                    get('selectedFolders').length > 0
                )
                    {
                        @this.
                        call('startDelete');
                    }
                }
            });
        });
    </script>
    {{--<h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Upload Files</h3>--}}
    {{--

    <div class="space-y-4">
        <div>
            <label class="block text-sm font-medium text-gray-700">Select Files</label>
            <input
                type="file"
                wire:model="uploadFiles"
                multiple
                class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"
            >
            @error('uploadFiles.*') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
        </div>
    </div>


    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
        <button
            type="submit"
            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 sm:ml-3 sm:w-auto sm:text-sm"
            wire:loading.attr="disabled"
        >
            <span wire:loading.remove>Upload</span>
            <span wire:loading>Uploading...</span>
        </button>
        <button
            type="button"
            wire:click="$set('showUploadModal', false)"
            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
        >
            Cancel
        </button>
    </div>

    --}}

    <!-- Create Folder Modal -->
    @if($showCreateFolderModal)
        <div class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
                     wire:click="$set('showCreateFolderModal', false)"></div>
                <div
                    class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <form wire:submit.prevent="createFolder">
                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Create New Folder</h3>

                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Folder Name</label>
                                    <input
                                        type="text"
                                        wire:model="newFolderName"
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                        placeholder="Enter folder name"
                                    >
                                    @error('newFolderName') <span
                                        class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Description
                                        (Optional)</label>
                                    <textarea
                                        wire:model="newFolderDescription"
                                        rows="3"
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                        placeholder="Enter folder description"
                                    ></textarea>
                                </div>
                            </div>
                        </div>

                        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                            <button
                                type="submit"
                                class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 sm:ml-3 sm:w-auto sm:text-sm"
                            >
                                Create Folder
                            </button>
                            <button
                                type="button"
                                wire:click="$set('showCreateFolderModal', false)"
                                class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
                            >
                                Cancel
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <!-- File Details Modal -->
    @if($showFileDetailsModal && $selectedFile)
        <div class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
                     wire:click="$set('showFileDetailsModal', false)"></div>
                <div
                    class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
                    <form wire:submit.prevent="updateFileDetails">
                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">File Details</h3>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- File preview -->
                                <div>
                                    <div class="border rounded-lg p-4 bg-gray-50">
                                        @if($selectedFile->isImage())
                                            <img
                                                src="{{ $selectedFile->url }}"
                                                alt="{{ $selectedFile->alt_text }}"
                                                class="max-w-full h-auto rounded"
                                            >
                                        @else
                                            <div class="flex items-center justify-center h-32">
                                                @if($selectedFile->isVideo())
                                                    <svg class="w-16 h-16 text-purple-500" fill="currentColor"
                                                         viewBox="0 0 20 20">
                                                        <path
                                                            d="M2 6a2 2 0 012-2h6a2 2 0 012 2v8a2 2 0 01-2 2H4a2 2 0 01-2-2V6zM14.553 7.106A1 1 0 0014 8v4a1 1 0 00.553.894l2 1A1 1 0 0018 13V7a1 1 0 00-1.447-.894l-2 1z"></path>
                                                    </svg>
                                                @elseif($selectedFile->isDocument())
                                                    <svg class="w-16 h-16 text-red-500" fill="currentColor"
                                                         viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd"
                                                              d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z"
                                                              clip-rule="evenodd"></path>
                                                    </svg>
                                                @else
                                                    <svg class="w-16 h-16 text-gray-500" fill="currentColor"
                                                         viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd"
                                                              d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z"
                                                              clip-rule="evenodd"></path>
                                                    </svg>
                                                @endif
                                            </div>
                                        @endif
                                    </div>

                                    <!-- File info -->
                                    <div class="mt-4 space-y-2 text-sm text-gray-600">
                                        <div><strong>Name:</strong> {{ $selectedFile->name }}</div>
                                        <div><strong>Original:</strong> {{ $selectedFile->original_name }}</div>
                                        <div><strong>Size:</strong> {{ $selectedFile->human_size }}</div>
                                        <div><strong>Type:</strong> {{ $selectedFile->mime_type }}</div>
                                        @if($selectedFile->width && $selectedFile->height)
                                            <div><strong>Dimensions:</strong> {{ $selectedFile->width }}
                                                × {{ $selectedFile->height }}</div>
                                        @endif
                                        <div>
                                            <strong>Uploaded:</strong> {{ $selectedFile->created_at->format('M j, Y g:i A') }}
                                        </div>
                                        <div><strong>URL:</strong> <a href="{{ $selectedFile->url }}" target="_blank"
                                                                      class="text-blue-600 hover:text-blue-800 break-all">{{ $selectedFile->url }}</a>
                                        </div>
                                    </div>
                                </div>

                                <!-- Editable fields -->
                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Alt Text</label>
                                        <input
                                            type="text"
                                            wire:model="fileAltText"
                                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                            placeholder="Describe this file for accessibility"
                                            value="{{ $selectedFile->alt_text ?? '' }}"
                                        >
                                        @error('fileAltText') <span
                                            class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Description</label>
                                        <textarea
                                            wire:model="fileDescription"
                                            rows="4"
                                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                            placeholder="Additional details about this file"
                                        >{{ $selectedFile->description ?? '' }}</textarea>
                                        @error('fileDescription') <span
                                            class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                            <button
                                type="submit"
                                class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 sm:ml-3 sm:w-auto sm:text-sm"
                            >
                                Save Changes
                            </button>
                            <button
                                type="button"
                                wire:click="$set('showFileDetailsModal', false)"
                                class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
                            >
                                Cancel
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Handle drag and drop
            const dropZone = document.querySelector('[x-data]');

            if (dropZone) {
                dropZone.addEventListener('dragover', function (e) {
                    e.preventDefault();
                    e.dataTransfer.dropEffect = 'copy';
                    this.classList.add('bg-blue-50', 'border-blue-300');
                });

                dropZone.addEventListener('dragleave', function (e) {
                    this.classList.remove('bg-blue-50', 'border-blue-300');
                });

                dropZone.addEventListener('drop', function (e) {
                    e.preventDefault();
                    this.classList.remove('bg-blue-50', 'border-blue-300');

                    const files = Array.from(e.dataTransfer.files);
                    if (files.length > 0) {
                        @this.
                        set('uploadFiles', files);
                        @this.
                        set('showUploadModal', true);
                    }
                });
            }

            // Handle keyboard shortcuts
            document.addEventListener('keydown', function (e) {
                // Ctrl/Cmd + A to select all
                if ((e.ctrlKey || e.metaKey) && e.key === 'a') {
                    e.preventDefault();
                    @this.
                    call('selectAllFiles');
                }

                // Escape to deselect all
                if (e.key === 'Escape') {
                    @this.
                    call('deselectAll');
                }

                // Delete key to delete selected
                if (e.key === 'Delete' || e.key === 'Backspace') {
                    if (@this.
                    get('selectedFiles').length > 0 || @this.
                    get('selectedFolders').length > 0
                )
                    {
                        @this.
                        call('startDelete');
                    }
                }
            });
        });
    </script>
</section>
