<div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
    {{-- Flash Messages --}}
    @if (session()->has('success'))
        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
    @endif

    {{-- Header --}}
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Certificate Templates</h1>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                Manage certificate templates for course completions and achievements.
            </p>
        </div>
        <button
            wire:click="openCreateModal"
            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
        >
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Create Template
        </button>
    </div>

    {{-- Templates Grid --}}
    @if($templates->isEmpty())
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-12 text-center">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            <h3 class="mt-4 text-lg font-medium text-gray-900 dark:text-white">No certificate templates</h3>
            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Get started by creating your first certificate template.</p>
            <button
                wire:click="openCreateModal"
                class="mt-4 inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700"
            >
                Create Template
            </button>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($templates as $template)
                <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden" wire:key="template-{{ $template->id }}">
                    {{-- Template Preview --}}
                    <div class="aspect-[1.414/1] bg-gradient-to-br {{ $template->template_file === 'elegant' ? 'from-amber-100 to-amber-200' : ($template->template_file === 'modern' ? 'from-gray-100 to-gray-200' : 'from-blue-100 to-blue-200') }} p-4 relative">
                        {{-- Certificate Preview Content --}}
                        <div class="h-full border-4 {{ $template->template_file === 'elegant' ? 'border-amber-400' : ($template->template_file === 'modern' ? 'border-gray-400' : 'border-blue-400') }} rounded p-3 flex flex-col items-center justify-center text-center">
                            <p class="text-[8px] uppercase tracking-widest text-gray-500">Certificate of</p>
                            <p class="text-sm font-bold text-gray-800">{{ ucfirst($template->type) }}</p>
                            <div class="w-12 h-0.5 bg-gray-400 my-2"></div>
                            <p class="text-[6px] text-gray-500">Recipient Name</p>
                        </div>

                        {{-- Status Badge --}}
                        <div class="absolute top-2 right-2">
                            @if($template->is_active)
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">
                                    Active
                                </span>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800">
                                    Inactive
                                </span>
                            @endif
                        </div>
                    </div>

                    {{-- Template Info --}}
                    <div class="p-4">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">{{ $template->name }}</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ $template->description ?? 'No description' }}</p>

                        <div class="mt-3 flex flex-wrap gap-2">
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                {{ ucfirst($template->type) }}
                            </span>
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">
                                {{ ucfirst($template->template_file) }}
                            </span>
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">
                                {{ ucfirst($template->orientation) }}
                            </span>
                        </div>

                        {{-- Actions --}}
                        <div class="mt-4 flex items-center justify-between">
                            <div class="flex space-x-2">
                                <button
                                    wire:click="openEditModal({{ $template->id }})"
                                    class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300"
                                    title="Edit"
                                >
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </button>
                                <button
                                    wire:click="duplicateTemplate({{ $template->id }})"
                                    class="text-gray-600 hover:text-gray-800 dark:text-gray-400 dark:hover:text-gray-300"
                                    title="Duplicate"
                                >
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                    </svg>
                                </button>
                                <button
                                    wire:click="deleteTemplate({{ $template->id }})"
                                    wire:confirm="Are you sure you want to delete this template?"
                                    class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300"
                                    title="Delete"
                                >
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </div>
                            <button
                                wire:click="toggleTemplateStatus({{ $template->id }})"
                                class="text-sm {{ $template->is_active ? 'text-yellow-600 hover:text-yellow-800' : 'text-green-600 hover:text-green-800' }}"
                            >
                                {{ $template->is_active ? 'Deactivate' : 'Activate' }}
                            </button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    {{-- Create/Edit Modal --}}
    @if($showCreateModal || $showEditModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                {{-- Background overlay --}}
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" wire:click="{{ $showCreateModal ? 'closeCreateModal' : 'closeEditModal' }}"></div>

                {{-- Modal panel --}}
                <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                            {{ $showCreateModal ? 'Create Certificate Template' : 'Edit Certificate Template' }}
                        </h3>
                    </div>

                    <div class="px-6 py-4 max-h-[70vh] overflow-y-auto">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            {{-- Name --}}
                            <div class="col-span-2">
                                <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Template Name *</label>
                                <input type="text" wire:model="name" id="name"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                @error('name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            {{-- Type --}}
                            <div>
                                <label for="type" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Certificate Type *</label>
                                <select wire:model="type" id="type"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                    @foreach($templateTypes as $typeKey => $typeLabel)
                                        <option value="{{ $typeKey }}">{{ $typeLabel }}</option>
                                    @endforeach
                                </select>
                                @error('type') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            {{-- Template File --}}
                            <div>
                                <label for="templateFile" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Design Style *</label>
                                <select wire:model="templateFile" id="templateFile"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                    @foreach($availableTemplateFiles as $fileKey => $fileInfo)
                                        <option value="{{ $fileKey }}">{{ $fileInfo['name'] }}</option>
                                    @endforeach
                                </select>
                                @error('templateFile') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            {{-- Orientation --}}
                            <div>
                                <label for="orientation" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Orientation</label>
                                <select wire:model="orientation" id="orientation"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                    @foreach($orientations as $orientKey => $orientLabel)
                                        <option value="{{ $orientKey }}">{{ $orientLabel }}</option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Paper Size --}}
                            <div>
                                <label for="paperSize" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Paper Size</label>
                                <select wire:model="paperSize" id="paperSize"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                    @foreach($paperSizes as $sizeKey => $sizeLabel)
                                        <option value="{{ $sizeKey }}">{{ $sizeLabel }}</option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Description --}}
                            <div class="col-span-2">
                                <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Description</label>
                                <textarea wire:model="description" id="description" rows="2"
                                          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"></textarea>
                            </div>

                            {{-- Background Image --}}
                            <div class="col-span-2">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Background Image (optional)</label>
                                <input type="file" wire:model="backgroundImage" accept="image/*"
                                       class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                                @error('backgroundImage') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            {{-- Default Fields --}}
                            <div class="col-span-2 border-t border-gray-200 dark:border-gray-700 pt-4 mt-2">
                                <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Default Text Fields</h4>
                                <div class="grid grid-cols-1 gap-3">
                                    @foreach($defaultFields as $fieldKey => $fieldValue)
                                        <div>
                                            <label class="block text-xs font-medium text-gray-500 dark:text-gray-400">
                                                {{ ucwords(str_replace('_', ' ', $fieldKey)) }}
                                            </label>
                                            <input type="text" wire:model="defaultFields.{{ $fieldKey }}"
                                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white text-sm">
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            {{-- Active Status --}}
                            <div class="col-span-2">
                                <label class="flex items-center space-x-2 cursor-pointer">
                                    <input type="checkbox" wire:model="isActive"
                                           class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    <span class="text-sm text-gray-700 dark:text-gray-300">Active (available for use)</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="px-6 py-4 bg-gray-50 dark:bg-gray-700 flex justify-end space-x-3">
                        <button
                            wire:click="{{ $showCreateModal ? 'closeCreateModal' : 'closeEditModal' }}"
                            class="px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 dark:bg-gray-600 dark:text-gray-300 dark:border-gray-500 dark:hover:bg-gray-500"
                        >
                            Cancel
                        </button>
                        <button
                            wire:click="{{ $showCreateModal ? 'createTemplate' : 'updateTemplate' }}"
                            class="px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700"
                        >
                            {{ $showCreateModal ? 'Create Template' : 'Update Template' }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
