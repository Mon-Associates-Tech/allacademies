<div class="container">
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        {{-- Header --}}
        <div class="mb-6">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Selective Database Backup</h2>
            <p class="text-gray-600 dark:text-gray-400 mt-1">
                Select specific tables to backup. Parent tables will be automatically included to ensure data can be restored.
            </p>
        </div>

        {{-- Search & Actions Bar --}}
        <div class="flex flex-wrap gap-4 mb-6">
            <div class="flex-1 min-w-64">
                <input
                    type="text"
                    wire:model.live="searchQuery"
                    placeholder="Search tables..."
                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                >
            </div>
            <div class="flex gap-2">
                <span class="px-3 py-2 bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 rounded-lg">
                    {{ count($selectedTables) }} selected
                </span>
                <button
                    wire:click="selectAll"
                    class="px-4 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600"
                >
                    Select All
                </button>
                <button
                    wire:click="deselectAll"
                    class="px-4 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600"
                >
                    Deselect All
                </button>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Table List --}}
            <div class="lg:col-span-2">
                <div class="border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden">
                    <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 border-b border-gray-200 dark:border-gray-600">
                        <h3 class="font-semibold text-gray-900 dark:text-white">Tables</h3>
                    </div>
                    <div class="max-h-96 overflow-y-auto">
                        @forelse($filteredTables as $table)
                            <label class="flex items-center px-4 py-3 border-b border-gray-100 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer">
                                <input
                                    type="checkbox"
                                    value="{{ $table }}"
                                    wire:change="toggleTable('{{ $table }}')"
                                    {{ in_array($table, $selectedTables) ? 'checked' : '' }}
                                    class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                                >
                                <span class="ml-3 flex-1 text-gray-900 dark:text-white">{{ $table }}</span>
                                @if(isset($tableInfo[$table]))
                                    <span class="text-xs text-gray-500 dark:text-gray-400">
                                        {{ number_format($tableInfo[$table]['count']) }} rows
                                    </span>
                                @endif
                                <button
                                    wire:click="viewDependencies('{{ $table }}')"
                                    class="ml-2 text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300"
                                    title="View dependencies"
                                >
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </button>
                            </label>
                        @empty
                            <div class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">
                                No tables found matching "{{ $searchQuery }}"
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- Dependency Panel --}}
            <div>
                @if($dependencyTree && $selectedTableForTree)
                    <div class="border border-gray-200 dark:border-gray-700 rounded-lg">
                        <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 border-b border-gray-200 dark:border-gray-600 flex justify-between items-center">
                            <h3 class="font-semibold text-gray-900 dark:text-white">Dependencies</h3>
                            <button
                                wire:click="closeDependencies"
                                class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200"
                            >
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>
                        <div class="p-4 space-y-4">
                            {{-- Selected Table --}}
                            <div class="bg-blue-50 dark:bg-blue-900/30 border border-blue-200 dark:border-blue-800 rounded-lg p-3">
                                <p class="text-sm font-medium text-blue-800 dark:text-blue-200">Selected: {{ $selectedTableForTree }}</p>
                            </div>

                            {{-- Parent Tables (Required) --}}
                            @if(!empty($dependencyTree['parents']))
                                <div>
                                    <p class="text-sm font-medium text-green-800 dark:text-green-200 mb-2">
                                        Parent Tables (Required)
                                    </p>
                                    <div class="space-y-1">
                                        @foreach($dependencyTree['parents'] as $parent)
                                            <div class="flex items-center text-sm text-gray-600 dark:text-gray-400">
                                                <svg class="w-4 h-4 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                                </svg>
                                                {{ $parent }}
                                                @if(in_array($parent, $selectedTables))
                                                    <span class="ml-2 text-xs text-green-600 dark:text-green-400">(included)</span>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            {{-- Child Tables (Optional) --}}
                            @if(!empty($childrenForSelected))
                                <div>
                                    <div class="flex justify-between items-center mb-2">
                                        <p class="text-sm font-medium text-orange-800 dark:text-orange-200">
                                            Child Tables (Optional)
                                        </p>
                                        <button
                                            wire:click="includeChildren"
                                            class="text-xs px-2 py-1 bg-orange-100 dark:bg-orange-900 text-orange-800 dark:text-orange-200 rounded hover:bg-orange-200 dark:hover:bg-orange-800"
                                        >
                                            Include All
                                        </button>
                                    </div>
                                    <div class="space-y-1 max-h-48 overflow-y-auto">
                                        @foreach($childrenForSelected as $child)
                                            <label class="flex items-center text-sm text-gray-600 dark:text-gray-400">
                                                <input
                                                    type="checkbox"
                                                    {{ in_array($child, $selectedTables) ? 'checked' : '' }}
                                                    wire:change="toggleTable('{{ $child }}')"
                                                    class="w-3 h-3 text-blue-600 border-gray-300 rounded"
                                                >
                                                <span class="ml-2">{{ $child }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                @else
                    <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-6 text-center">
                        <svg class="w-12 h-12 mx-auto text-gray-400 dark:text-gray-500 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <p class="text-gray-500 dark:text-gray-400">
                            Select a table to view its dependencies
                        </p>
                    </div>
                @endif

                {{-- Selected Tables Summary --}}
                <div class="mt-4 border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                    <h3 class="font-semibold text-gray-900 dark:text-white mb-3">Selected Tables</h3>
                    @if(!empty($selectedTables))
                        <div class="flex flex-wrap gap-2">
                            @foreach($selectedTables as $table)
                                <span class="inline-flex items-center px-2 py-1 bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 rounded text-xs">
                                    {{ $table }}
                                    <button
                                        wire:click="toggleTable('{{ $table }}')"
                                        class="ml-1 text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300"
                                    >
                                        ×
                                    </button>
                                </span>
                            @endforeach
                        </div>
                    @else
                        <p class="text-sm text-gray-500 dark:text-gray-400">No tables selected</p>
                    @endif
                </div>
            </div>
        </div>

        {{-- Backup Actions --}}
        <div class="mt-6 flex flex-wrap gap-4 justify-end border-t border-gray-200 dark:border-gray-700 pt-6">
            <button
                wire:click="downloadBackup"
                wire:loading.attr="disabled"
                {{ empty($selectedTables) || $isBackingUp ? 'disabled' : '' }}
                class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2"
            >
                <svg wire:loading.class="animate-spin" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                </svg>
                Download Backup
            </button>
            <button
                wire:click="performBackup"
                wire:loading.attr="disabled"
                {{ empty($selectedTables) || $isBackingUp ? 'disabled' : '' }}
                class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2"
            >
                <svg wire:loading.class="animate-spin" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                Save to Server
            </button>
        </div>

        {{-- Progress --}}
        @if($isBackingUp || $backupMessage)
            <div class="mt-4">
                <div class="flex items-center gap-3">
                    <svg wire:loading class="animate-spin w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                    <span class="text-sm text-gray-600 dark:text-gray-400">{{ $backupMessage }}</span>
                </div>
                @if($backupProgress > 0)
                    <div class="mt-2 w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                        <div
                            class="bg-blue-600 h-2 rounded-full transition-all duration-300"
                            style="width: {{ $backupProgress }}%"
                        ></div>
                    </div>
                @endif
            </div>
        @endif

        {{-- Restore Section --}}
        <div class="mt-8 border-t border-gray-200 dark:border-gray-700 pt-6">
            <button
                wire:click="toggleRestore"
                type="button"
                class="flex items-center gap-2 text-lg font-semibold text-gray-900 dark:text-white hover:text-blue-600 dark:hover:text-blue-400"
            >
                @if($showRestore)
                    <svg class="w-5 h-5 transform rotate-90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                @else
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                @endif
                Restore Database from Backup
            </button>

            @if($showRestore)
                <div class="mt-4 space-y-6">
                    {{-- Warning --}}
                    <div class="bg-yellow-50 dark:bg-yellow-900/30 border border-yellow-200 dark:border-yellow-800 rounded-lg p-4">
                        <div class="flex gap-3">
                            <svg class="w-5 h-5 text-yellow-600 dark:text-yellow-400 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM6.5 12a1 1 0 120 2 1 1 0 020-2zm-1.5-6a1 1 0 102 0 1 1 0 00-2 0z" clip-rule="evenodd"></path>
                            </svg>
                            <div>
                                <p class="text-sm font-medium text-yellow-800 dark:text-yellow-200">Warning: This will overwrite your current database data.</p>
                                <p class="text-sm text-yellow-700 dark:text-yellow-300 mt-1">Please proceed with caution. Foreign key checks will be temporarily disabled during restore.</p>
                            </div>
                        </div>
                    </div>

                    {{-- Mode Tabs --}}
                    <div class="flex gap-2 border-b border-gray-200 dark:border-gray-700">
                        <button
                            wire:click="$set('restoreMode', 'server')"
                            class="px-4 py-2 text-sm font-medium border-b-2 transition-colors
                                @if($restoreMode === 'server')
                                    border-blue-600 text-blue-600 dark:text-blue-400
                                @else
                                    border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300
                                @endif"
                        >
                            From Server Backups
                        </button>
                        <button
                            wire:click="$set('restoreMode', 'upload')"
                            class="px-4 py-2 text-sm font-medium border-b-2 transition-colors
                                @if($restoreMode === 'upload')
                                    border-blue-600 text-blue-600 dark:text-blue-400
                                @else
                                    border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300
                                @endif"
                        >
                            Upload SQL File
                        </button>
                    </div>

                    {{-- Server Backups Mode --}}
                    @if($restoreMode === 'server')
                        <div>
                            @if(!empty($backupFiles))
                                <div class="max-h-64 overflow-y-auto border border-gray-200 dark:border-gray-700 rounded-lg">
                                    @foreach($backupFiles as $file)
                                        <label class="flex items-center px-4 py-3 border-b border-gray-100 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer">
                                            <input
                                                type="radio"
                                                name="selectedBackupFile"
                                                value="{{ $file['path'] }}"
                                                wire:change="$set('selectedBackupFile', '{{ $file['path'] }}')"
                                                {{ $selectedBackupFile === $file['path'] ? 'checked' : '' }}
                                                class="w-4 h-4 text-blue-600 border-gray-300 focus:ring-blue-500"
                                            >
                                            <svg class="w-4 h-4 text-gray-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                            </svg>
                                            <div class="flex-1">
                                                <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $file['name'] }}</p>
                                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                                    {{ $formattedSizes[$file['path']] ?? '' }} • {{ date('M d, Y H:i', $file['modified']) }}
                                                </p>
                                            </div>
                                            <button
                                                wire:click="deleteBackupFile('{{ $file['path'] }}')"
                                                wire:confirm="Are you sure you want to delete this backup?"
                                                class="ml-2 text-red-500 hover:text-red-700 dark:hover:text-red-400 p-1"
                                                title="Delete backup"
                                            >
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                </svg>
                                            </button>
                                        </label>
                                    @endforeach
                                </div>
                                <div class="mt-4 flex justify-end">
                                    <button
                                        wire:click="restoreFromServer"
                                        wire:loading.attr="disabled"
                                        {{ empty($selectedBackupFile) || $isRestoring ? 'disabled' : '' }}
                                        class="px-6 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700 disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2"
                                    >
                                        <svg wire:loading.class="animate-spin" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                        </svg>
                                        Restore Selected Backup
                                    </button>
                                </div>
                            @else
                                <div class="text-center py-8 text-gray-500 dark:text-gray-400">
                                    <svg class="w-12 h-12 mx-auto text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                    </svg>
                                    <p>No backup files found on server.</p>
                                    <p class="text-sm mt-1">Create a backup first using the backup feature above.</p>
                                </div>
                            @endif
                        </div>
                    @endif

                    {{-- Upload Mode --}}
                    @if($restoreMode === 'upload')
                        <div class="space-y-4">
                            <div class="border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg p-8 text-center">
                                <input
                                    type="file"
                                    wire:model="uploadFile"
                                    accept=".sql,.sql.gz"
                                    class="hidden"
                                    id="uploadBackupFile"
                                >
                                <label for="uploadBackupFile" class="cursor-pointer inline-flex flex-col items-center">
                                    <svg class="w-10 h-10 text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                    </svg>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">
                                        <span class="font-medium text-blue-600 dark:text-blue-400">Click to upload</span> or drag and drop
                                    </p>
                                    <p class="text-xs text-gray-500 dark:text-gray-500 mt-1">SQL files only</p>
                                </label>
                            </div>

                            @if($uploadFile)
                                <div class="bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800 rounded-lg p-3 flex items-center gap-3">
                                    <svg class="w-5 h-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                    </svg>
                                    <div class="flex-1">
                                        <p class="text-sm font-medium text-green-800 dark:text-green-200">{{ $uploadFile->getClientOriginalName() }}</p>
                                        <p class="text-xs text-green-600 dark:text-green-400">{{ round($uploadFile->getSize() / 1024, 1) }} KB</p>
                                    </div>
                                    <button
                                        wire:click="$set('uploadFile', null)"
                                        class="text-green-600 dark:text-green-400 hover:text-green-800 dark:hover:text-green-200"
                                    >
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    </button>
                                </div>
                            @endif

                            <div class="flex justify-end">
                                <button
                                    wire:click="restoreFromUpload"
                                    wire:loading.attr="disabled"
                                    {{ empty($uploadFile) || $isRestoring ? 'disabled' : '' }}
                                    class="px-6 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700 disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2"
                                >
                                    <svg wire:loading.class="animate-spin" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                    </svg>
                                    Restore Uploaded File
                                </button>
                            </div>
                        </div>
                    @endif

                    {{-- Restore Progress --}}
                    @if($isRestoring || $restoreMessage)
                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                            <div class="flex items-center gap-3">
                                <svg wire:loading class="animate-spin w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                </svg>
                                <span class="text-sm text-gray-600 dark:text-gray-400">{{ $restoreMessage }}</span>
                            </div>
                            @if($restoreProgress > 0)
                                <div class="mt-2 w-full bg-gray-200 dark:bg-gray-600 rounded-full h-2">
                                    <div
                                        class="bg-orange-600 h-2 rounded-full transition-all duration-300"
                                        style="width: {{ $restoreProgress }}%"
                                    ></div>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>
