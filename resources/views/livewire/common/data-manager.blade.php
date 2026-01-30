<div class="max-w-6xl mx-auto p-6 space-y-6">
    <!-- Header -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Data Import & Export</h1>
                <p class="text-gray-600 mt-1">Manage your data imports and exports efficiently</p>
            </div>

            <!-- Operation Toggle -->
            <div class="flex bg-gray-100 rounded-lg p-1">
                <button
                    wire:click="$set('activeOperation', 'import')"
                    class="px-4 py-2 rounded-md text-sm font-medium transition-colors {{ $activeOperation === 'import' ? 'bg-white text-blue-600 shadow-sm' : 'text-gray-600 hover:text-gray-900' }}"
                >
                    <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"/>
                    </svg>
                    Import
                </button>
                <button
                    wire:click="$set('activeOperation', 'export')"
                    class="px-4 py-2 rounded-md text-sm font-medium transition-colors {{ $activeOperation === 'export' ? 'bg-white text-blue-600 shadow-sm' : 'text-gray-600 hover:text-gray-900' }}"
                >
                    <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                    </svg>
                    Export
                </button>
            </div>
        </div>

        <!-- Flash Messages -->
        @if (session()->has('success'))
            <div class="mb-4 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-md">
                {{ session('success') }}
            </div>
        @endif

        @if (session()->has('error'))
            <div class="mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-md">
                {{ session('error') }}
            </div>
        @endif

        <!-- Model Selection -->
        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-3">Select Data Type</label>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($availableModels as $modelKey => $model)
                    @php
                        $isSupported = $activeOperation === 'import' ? $model['import_supported'] : $model['export_supported'];
                    @endphp

                    <div class="relative">
                        <input
                            type="radio"
                            id="model_{{ $modelKey }}"
                            wire:model.live="selectedModel"
                            value="{{ $modelKey }}"
                            class="sr-only peer"
                            {{ !$isSupported ? 'disabled' : '' }}
                        >
                        <label
                            for="model_{{ $modelKey }}"
                            class="flex items-start p-4 border-2 rounded-lg cursor-pointer transition-all duration-200 {{ !$isSupported ? 'opacity-50 cursor-not-allowed bg-gray-50' : 'hover:border-blue-300 peer-checked:border-blue-500 peer-checked:bg-blue-50' }}"
                        >
                            <div class="flex items-center">
                                <svg class="w-6 h-6 text-gray-400 mr-3 flex-shrink-0" fill="none" stroke="currentColor"
                                     viewBox="0 0 24 24">
                                    @if($model['icon'] === 'academic-cap')
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M12 14l9-5-9-5-9 5 9 5z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/>
                                    @elseif($model['icon'] === 'user-group')
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                    @elseif($model['icon'] === 'book-open')
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                    @else
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    @endif
                                </svg>
                                <div>
                                    <div class="font-medium text-gray-900">{{ $model['label'] }}</div>
                                    <div class="text-sm text-gray-500 mt-1">{{ $model['description'] }}</div>
                                    @if(!$isSupported)
                                        <div class="text-xs text-red-500 mt-1">{{ ucfirst($activeOperation) }} not
                                            supported
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </label>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    @if($selectedModel && config('app.enable_importers') ===true )
        <!-- Import Section -->
        @if($activeOperation === 'import')
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">
                    Import {{ $availableModels[$selectedModel]['label'] }}</h2>

                <!-- File Upload -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Upload File</label>
                    <div class="flex items-center space-x-4">
                        <div class="flex-1">
                            <input
                                type="file"
                                wire:model="uploadedFile"
                                accept=".csv,.xlsx"
                                class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-medium file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"
                            >
                            @error('uploadedFile')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <button
                            wire:click="downloadSampleFile"
                            class="px-4 py-2 text-sm font-medium text-blue-600 bg-blue-50 rounded-md hover:bg-blue-100 transition-colors"
                        >
                            Download Sample
                        </button>
                    </div>
                    <p class="mt-2 text-sm text-gray-500">Supported formats: CSV, Excel (.xlsx). Maximum file size:
                        10MB</p>
                </div>

                <!-- Import Options -->
                @if(count($importOptions) > 0)
                    <div class="mb-6">
                        <button
                            wire:click="toggleAdvancedOptions"
                            class="flex items-center text-sm font-medium text-gray-700 hover:text-gray-900"
                        >
                            <svg
                                class="w-4 h-4 mr-2 transform transition-transform {{ $showAdvancedOptions ? 'rotate-90' : '' }}"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                            Advanced Import Options
                        </button>

                        @if($showAdvancedOptions)
                            <div class="mt-4 pl-6 space-y-3">
                                @foreach($importOptions as $optionKey => $optionLabel)
                                    <label class="flex items-center">
                                        <input
                                            type="checkbox"
                                            wire:model="importOptions.{{ $optionKey }}"
                                            class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                                        >
                                        <span class="ml-2 text-sm text-gray-700">{{ $optionLabel }}</span>
                                    </label>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @endif

                <!-- Action Buttons -->
                <div class="flex space-x-4">
                    <button
                        wire:click="validateFile"
                        wire:loading.attr="disabled"
                        wire:target="validateFile"
                        class="px-6 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                        {{ !$uploadedFile ? 'disabled' : '' }}
                    >
                        <span wire:loading.remove wire:target="validateFile">Validate File</span>
                        <span wire:loading wire:target="validateFile">Validating...</span>
                    </button>

                    <button
                        wire:click="performImport"
                        wire:loading.attr="disabled"
                        wire:target="performImport"
                        class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                        {{ !$uploadedFile ? 'disabled' : '' }}
                    >
                        <span wire:loading.remove wire:target="performImport">Import Data</span>
                        <span wire:loading wire:target="performImport">Importing...</span>
                    </button>
                </div>
            </div>
        @endif

        <!-- Export Section -->
        @if($activeOperation === 'export')
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">
                    Export {{ $availableModels[$selectedModel]['label'] }}</h2>

                <!-- Export Format -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Export Format</label>
                    <div class="flex space-x-4">
                        <label class="flex items-center">
                            <input type="radio" wire:model.live="exportFormat" value="csv" class="text-blue-600">
                            <span class="ml-2 text-sm text-gray-700">CSV</span>
                        </label>
                        <label class="flex items-center">
                            <input type="radio" wire:model.live="exportFormat" value="xlsx" class="text-blue-600">
                            <span class="ml-2 text-sm text-gray-700">Excel (.xlsx)</span>
                        </label>
                    </div>
                </div>

                <!-- Export Options -->
                <div class="mb-6">
                    <label class="flex items-center">
                        <input
                            type="checkbox"
                            wire:model.live="includeRelations"
                            class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                        >
                        <span class="ml-2 text-sm text-gray-700">Include related data</span>
                    </label>
                </div>

                <!-- Filters -->
                @if(count($modelFilters) > 0)
                    <div class="mb-6">
                        <button
                            wire:click="toggleAdvancedOptions"
                            class="flex items-center text-sm font-medium text-gray-700 hover:text-gray-900 mb-4"
                        >
                            <svg
                                class="w-4 h-4 mr-2 transform transition-transform {{ $showAdvancedOptions ? 'rotate-90' : '' }}"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                            Export Filters
                        </button>

                        @if($showAdvancedOptions)
                            <div class="pl-6 space-y-4">
                                @foreach($modelFilters as $filterKey => $filterLabel)
                                    <div>
                                        <label
                                            class="block text-sm font-medium text-gray-700 mb-1">{{ $filterLabel }}</label>
                                        <input
                                            type="text"
                                            wire:model="exportFilters.{{ $filterKey }}"
                                            class="block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm dark:bg-gray-700 dark:text-white focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                                            placeholder="Enter filter value..."
                                        >
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @endif

                <!-- Export Button -->
                <button
                    wire:click="performExport"
                    wire:loading.attr="disabled"
                    wire:target="performExport"
                    class="px-6 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                >
                    <span wire:loading.remove wire:target="performExport">Export Data</span>
                    <span wire:loading wire:target="performExport">Preparing Export...</span>
                </button>
            </div>
        @endif

    @else
        <div>Imports and exports are currently disabled.</div>

    @endif

    <!-- Processing Indicator -->
    @if($isProcessing)
        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white rounded-lg p-6 max-w-sm w-full mx-4">
                <div class="flex items-center">
                    <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-blue-600" xmlns="http://www.w3.org/2000/svg"
                         fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor"
                              d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span class="text-gray-900">{{ $processingMessage }}</span>
                </div>
            </div>
        </div>
    @endif

    <!-- Results Display -->
    @if($lastResult)
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex justify-between items-start mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Operation Results</h3>
                <button wire:click="clearResults" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            @if($lastResult['type'] === 'import_success')
                <div class="bg-green-50 border border-green-200 rounded-md p-4">
                    <div class="text-green-800 font-medium mb-2">{{ $lastResult['message'] }}</div>
                    @if(isset($lastResult['stats']))
                        <div class="text-sm text-green-700 space-y-1">
                            <div>Imported: {{ $lastResult['stats']['imported'] ?? 0 }} records</div>
                            <div>Skipped: {{ $lastResult['stats']['skipped'] ?? 0 }} records</div>
                            <div>Errors: {{ $lastResult['stats']['errors'] ?? 0 }} records</div>
                        </div>
                    @endif
                </div>
            @elseif($lastResult['type'] === 'export_success')
                <div class="bg-green-50 border border-green-200 rounded-md p-4">
                    <div class="text-green-800 font-medium mb-2">{{ $lastResult['message'] }}</div>
                    @if(isset($lastResult['filename']))
                        <div class="text-sm text-green-700">
                            File: {{ $lastResult['filename'] }}
                        </div>
                    @endif
                </div>
            @elseif($lastResult['type'] === 'validation_success')
                <div class="bg-blue-50 border border-blue-200 rounded-md p-4">
                    <div class="text-blue-800 font-medium">{{ $lastResult['message'] }}</div>
                    @if(isset($lastResult['details']['total_rows']))
                        <div class="text-sm text-blue-700 mt-1">
                            Ready to import {{ $lastResult['details']['total_rows'] }} rows
                        </div>
                    @endif
                </div>
            @else
                <div class="bg-red-50 border border-red-200 rounded-md p-4">
                    <div class="text-red-800 font-medium">{{ $lastResult['message'] }}</div>
                </div>
            @endif
        </div>
    @endif
</div>
