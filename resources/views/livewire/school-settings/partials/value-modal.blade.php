@if($showValueModal && $currentSetting)

    <!-- Modern Modal Overlay -->
    <div class="fixed inset-0 bg-black/50 backdrop-blur-sm overflow-y-auto h-full w-full z-50 flex items-center justify-center p-4"
         x-data="{ show: true }"
         x-show="show"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">

        <!-- Modal Container -->
        <div class="relative w-full max-w-2xl mx-auto"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 transform scale-90"
             x-transition:enter-end="opacity-100 transform scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 transform scale-100"
             x-transition:leave-end="opacity-0 transform scale-90">

            <!-- Modal Content -->
            <div class="bg-white rounded-2xl shadow-2xl border border-white/20 overflow-hidden">
                <!-- Header -->
                <div class="bg-gradient-to-r from-indigo-500 to-purple-600 px-6 py-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 bg-white/20 rounded-lg flex items-center justify-center">
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-white">Edit Setting Value</h3>
                                <p class="text-sm text-white/80">{{ $currentSetting->label }}</p>
                            </div>
                        </div>
                        <button type="button"
                                wire:click="closeValueModal"
                                class="text-white/80 hover:text-white bg-white/10 hover:bg-white/20 rounded-lg p-2 transition-colors duration-200">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Form Content -->
                <div class="p-6">
                    <form wire:submit.prevent="saveValue" class="space-y-6">
                        <!-- Setting Info -->
                        <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl p-4 border border-blue-200/50">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-lg flex items-center justify-center">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        @switch($currentSetting->type)
                                            @case('text')
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"/>
                                                @break
                                            @case('longtext')
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                                @break
                                            @case('number')
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"/>
                                                @break
                                            @case('boolean')
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                @break
                                            @case('select')
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                                                @break
                                            @case('image')
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                                @break
                                            @case('pdf')
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                                @break
                                            @case('json')
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/>
                                                @break
                                            @default
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4"/>
                                        @endswitch
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <h4 class="font-medium text-gray-900">{{ $currentSetting->label }}</h4>
                                    <div class="flex items-center space-x-4 mt-1">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            {{ ucfirst(str_replace('_', ' ', $currentSetting->type)) }}
                                        </span>
                                        @if($currentSetting->required)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                Required
                                            </span>
                                        @endif
                                    </div>
                                    @if($currentSetting->description)
                                        <p class="text-sm text-gray-600 mt-2">{{ $currentSetting->description }}</p>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Input Fields -->
                        <div class="space-y-4">
                            @if($currentSetting->type === 'text')
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Value</label>
                                    <input type="text"
                                           wire:model="value"
                                           class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl shadow-sm dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200"
                                           placeholder="Enter text value..."
                                        {{ $currentSetting->required ? 'required' : '' }}>
                                </div>

                            @elseif($currentSetting->type === 'longtext')
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Value</label>
                                    <textarea wire:model="value"
                                              rows="4"
                                              class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl shadow-sm dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200 resize-none"
                                              placeholder="Enter long text value..."
                                              {{ $currentSetting->required ? 'required' : '' }}></textarea>
                                </div>

                            @elseif($currentSetting->type === 'number')
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Value</label>
                                    <input type="number"
                                           wire:model="value"
                                           class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl shadow-sm dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200"
                                           placeholder="Enter numeric value..."
                                        {{ $currentSetting->required ? 'required' : '' }}>
                                </div>

                            @elseif($currentSetting->type === 'boolean')
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Value</label>
                                    <div class="grid grid-cols-2 gap-3">
                                        <label class="flex items-center p-4 border-2 border-gray-200 rounded-xl hover:border-indigo-300 cursor-pointer transition-colors duration-200 {{ $value == '1' ? 'border-indigo-500 bg-indigo-50' : '' }}">
                                            <input type="radio"
                                                   wire:model="value"
                                                   value="1"
                                                   class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300">
                                            <div class="ml-3">
                                                <div class="flex items-center space-x-2">
                                                    <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                                    </svg>
                                                    <span class="font-medium text-gray-900">Yes</span>
                                                </div>
                                                <p class="text-sm text-gray-500">Enable this setting</p>
                                            </div>
                                        </label>
                                        <label class="flex items-center p-4 border-2 border-gray-200 rounded-xl hover:border-indigo-300 cursor-pointer transition-colors duration-200 {{ $value == '0' ? 'border-indigo-500 bg-indigo-50' : '' }}">
                                            <input type="radio"
                                                   wire:model="value"
                                                   value="0"
                                                   class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300">
                                            <div class="ml-3">
                                                <div class="flex items-center space-x-2">
                                                    <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                                    </svg>
                                                    <span class="font-medium text-gray-900">No</span>
                                                </div>
                                                <p class="text-sm text-gray-500">Disable this setting</p>
                                            </div>
                                        </label>
                                    </div>
                                </div>

                            @elseif($currentSetting->type === 'select')
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Value</label>
                                    <select wire:model="value"
                                            class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl shadow-sm dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200 bg-white">
                                        @if(!$currentSetting->required)
                                            <option value="">Select an option</option>
                                        @endif
                                        @if($currentSetting->options)
                                            @foreach($currentSetting->options as $option)
                                                <option value="{{ $option }}">{{ $option }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>

                            @elseif($currentSetting->type === 'image')
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Image Upload</label>

                                    <!-- File Upload Area -->
                                    <div class="border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-xl p-6 text-center hover:border-indigo-400 transition-colors duration-200">
                                        <input type="file"
                                               wire:model="fileValue"
                                               accept="image/*"
                                               class="hidden"
                                               id="imageUpload">
                                        <label for="imageUpload" class="cursor-pointer">
                                            <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                            </svg>
                                            <p class="text-sm text-gray-600">Click to upload or drag and drop</p>
                                            <p class="text-xs text-gray-500">PNG, JPG, GIF up to 2MB</p>
                                        </label>
                                    </div>

                                    @if($currentSetting->value)
                                        <div class="mt-4 p-4 bg-gray-50 rounded-xl">
                                            <p class="text-sm font-medium text-gray-700 mb-2">Current Image:</p>
                                            <img src="{{ $currentSetting->value }}"
                                                 alt="Current image"
                                                 class="h-32 w-32 object-cover rounded-lg border border-gray-200">
                                        </div>
                                    @endif
                                </div>

                            @elseif($currentSetting->type === 'pdf')
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">PDF Upload</label>

                                    <!-- File Upload Area -->
                                    <div class="border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-xl p-6 text-center hover:border-indigo-400 transition-colors duration-200">
                                        <input type="file"
                                               wire:model="fileValue"
                                               accept=".pdf"
                                               class="hidden"
                                               id="pdfUpload">
                                        <label for="pdfUpload" class="cursor-pointer">
                                            <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                            </svg>
                                            <p class="text-sm text-gray-600">Click to upload PDF</p>
                                            <p class="text-xs text-gray-500">PDF files up to 10MB</p>
                                        </label>
                                    </div>

                                    @if($currentSetting->value)
                                        <div class="mt-4 p-4 bg-gray-50 rounded-xl">
                                            <p class="text-sm font-medium text-gray-700 mb-2">Current PDF:</p>
                                            <a href="{{ $currentSetting->value }}"
                                               target="_blank"
                                               class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors duration-200">
                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                </svg>
                                                View Current PDF
                                            </a>
                                        </div>
                                    @endif
                                </div>

                            @elseif($currentSetting->type === 'json')
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">JSON Value</label>
                                    <div class="relative">
                                        <textarea wire:model="value"
                                                  rows="8"
                                                  placeholder='{"key": "value", "example": true}'
                                                  class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl shadow-sm dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200 font-mono text-sm bg-gray-50"
                                                  {{ $currentSetting->required ? 'required' : '' }}></textarea>
                                        <div class="absolute top-2 right-2">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-200 text-gray-800">
                                                JSON
                                            </span>
                                        </div>
                                    </div>
                                    <p class="mt-2 text-xs text-gray-500">Enter valid JSON format</p>
                                </div>
                            @endif

                            <!-- Error Messages -->
                            @error('value')
                            <div class="flex items-center space-x-2 text-red-600 text-sm bg-red-50 p-3 rounded-lg">
                                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <span>{{ $message }}</span>
                            </div>
                            @enderror

                            @error('fileValue')
                            <div class="flex items-center space-x-2 text-red-600 text-sm bg-red-50 p-3 rounded-lg">
                                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <span>{{ $message }}</span>
                            </div>
                            @enderror
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200">
                            <button type="button"
                                    wire:click="closeValueModal"
                                    class="px-6 py-3 border border-gray-300 dark:border-gray-600 rounded-xl text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-colors duration-200">
                                Cancel
                            </button>
                            <button type="submit"
                                    class="px-6 py-3 border border-transparent rounded-xl text-sm font-medium text-white bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 shadow-lg hover:shadow-xl transition-all duration-200 transform hover:scale-105">
                                <span class="flex items-center">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    Save Value
                                </span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endif
