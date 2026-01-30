<section class="">
    @if(isset($editingBook))
    <div class="fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center z-50 p-4"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">
        <div class="bg-white dark:bg-gray-800 rounded-2xl max-w-4xl w-full max-h-[90vh] overflow-y-auto shadow-2xl"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 transform scale-90"
             x-transition:enter-end="opacity-100 transform scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 transform scale-100"
             x-transition:leave-end="opacity-0 transform scale-90">

            <!-- Modal Header -->
            <div class="p-6 border-b border-gray-200 dark:border-gray-700 bg-gradient-to-r from-blue-600 to-purple-600 text-white rounded-t-2xl">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-white/20 rounded-lg flex items-center justify-center">
                            <!-- fas fa-book -->
                            <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 3C7.03 3 3 5.03 3 9v6c0 3.97 4.03 7 9 7s9-3.03 9-7V9c0-3.97-4.03-6-9-6zm0 16c-3.87 0-7-2.13-7-5V9c0-2.87 3.13-5 7-5s7 2.13 7 5v5c0 2.87-3.13 5-7 5z"/>
                                <circle cx="12" cy="12" r="2"/>
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-2xl font-bold">
                                {{ $editingBook ? 'Edit Book' : 'Add New Book' }}
                            </h2>
                            <p class="text-blue-100 text-sm">
                                {{ $editingBook ? 'Update your book details' : 'Create a new book for your readers' }}
                            </p>
                        </div>
                    </div>
                    <button wire:click="closeBookModal"
                            class="text-white hover:text-gray-200 p-2 rounded-lg hover:bg-white/10 transition-colors">
                        <!-- fas fa-times -->
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Modal Body -->
            <form wire:submit="saveBook" class="p-6">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Title -->
                    <div class="lg:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            <!-- fas fa-heading -->
                            <svg class="w-4 h-4 inline mr-2 text-blue-500" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M3 4h18v2H3V4zm0 15h18v2H3v-2zm0-7h18v2H3v-2z"/>
                            </svg>Book Title *
                        </label>
                        <input type="text"
                               wire:model="title"
                               class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white transition-colors"
                               placeholder="Enter book title">
                        @error('title') <span class="text-red-500 text-sm mt-1 block"><!-- fas fa-exclamation-circle --><svg class="w-4 h-4 inline mr-1" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/></svg>{{ $message }}</span> @enderror
                    </div>

                    <!-- Category -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            <!-- fas fa-tags -->
                            <svg class="w-4 h-4 inline mr-2 text-purple-500" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M21.41 11.58l-9-9C12.05 2.22 11.55 2 11 2H4c-1.1 0-2 .9-2 2v7c0 .55.22 1.05.59 1.42l9 9c.36.36.86.58 1.41.58.55 0 1.05-.22 1.41-.59l7-7c.37-.36.59-.86.59-1.41 0-.55-.23-1.06-.59-1.42zM5.5 7C4.67 7 4 6.33 4 5.5S4.67 4 5.5 4 7 4.67 7 5.5 6.33 7 5.5 7z"/>
                            </svg>Category *
                        </label>
                        <select wire:model="book_category_id"
                                class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white transition-colors">
                            <option value="">Select Category</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                        @error('book_category_id') <span class="text-red-500 text-sm mt-1 block"><!-- fas fa-exclamation-circle --><svg class="w-4 h-4 inline mr-1" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/></svg>{{ $message }}</span> @enderror
                    </div>

                    <!-- Edition -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            <!-- fas fa-bookmark -->
                            <svg class="w-4 h-4 inline mr-2 text-green-500" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M17 3H7c-1.1 0-2 .9-2 2v16l7-3 7 3V5c0-1.1-.9-2-2-2z"/>
                            </svg>Edition
                        </label>
                        <input type="text"
                               wire:model="edition"
                               class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white transition-colors"
                               placeholder="e.g., 1st Edition">
                        @error('edition') <span class="text-red-500 text-sm mt-1 block"><!-- fas fa-exclamation-circle --><svg class="w-4 h-4 inline mr-1" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/></svg>{{ $message }}</span> @enderror
                    </div>

                    <!-- Publisher -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            <!-- fas fa-building -->
                            <svg class="w-4 h-4 inline mr-2 text-indigo-500" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 4l-8 4v12h16V8l-8-4zm6 14H6v-8.5l6-3 6 3V18z"/>
                            </svg>Publisher
                        </label>
                        <input type="text"
                               wire:model="publisher"
                               class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white transition-colors"
                               placeholder="Publisher name">
                        @error('publisher') <span class="text-red-500 text-sm mt-1 block"><!-- fas fa-exclamation-circle --><svg class="w-4 h-4 inline mr-1" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/></svg>{{ $message }}</span> @enderror
                    </div>

                    <!-- Pages -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            <!-- fas fa-file-alt -->
                            <svg class="w-4 h-4 inline mr-2 text-orange-500" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M14 2H6c-1.1 0-2 .9-2 2v16c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V8l-6-6zm4 18H6V4h7v5h5v11z"/>
                            </svg>Pages
                        </label>
                        <input type="number"
                               wire:model="pages"
                               class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white transition-colors"
                               placeholder="Number of pages">
                        @error('pages') <span class="text-red-500 text-sm mt-1 block"><!-- fas fa-exclamation-circle --><svg class="w-4 h-4 inline mr-1" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/></svg>{{ $message }}</span> @enderror
                    </div>

                    <!-- Subscription Fee -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            <!-- fas fa-dollar-sign -->
                            <svg class="w-4 h-4 inline mr-2 text-emerald-500" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1.41 15.5v-2.45c1.45-.16 2.23-.86 2.23-1.78 0-.95-.82-1.57-2.23-1.78v-2.1h-2.2v2.1c-1.41.21-2.23.83-2.23 1.78 0 .92.78 1.62 2.23 1.78v2.45h2.2zm-1.41-6.5c.55 0 1-.45 1-1s-.45-1-1-1-1 .45-1 1 .45 1 1 1z"/>
                            </svg>Annual Subscription Fee (GHS)
                        </label>
                        <input type="number"
                               step="0.01"
                               wire:model="annual_subscription_fee"
                               class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white transition-colors"
                               placeholder="0.00">
                        @error('annual_subscription_fee') <span class="text-red-500 text-sm mt-1 block"><!-- fas fa-exclamation-circle --><svg class="w-4 h-4 inline mr-1" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/></svg>{{ $message }}</span> @enderror
                    </div>

                    <!-- Book Format -->
                    <div class="lg:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
                            <!-- fas fa-layer-group -->
                            <svg class="w-4 h-4 inline mr-2 text-cyan-500" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 16l-6-6 1.41-1.41L12 13.17l4.59-4.58L18 10l-6 6zm-6-8l-4 4 4 4 1.41-1.41L5.83 12l2.58-2.59L7 8zm12 0l-1.41 1.41L18.17 12l-2.58 2.59L17 16l4-4-4-4z"/>
                            </svg>Book Format
                        </label>
                        <div class="flex flex-wrap gap-4">
                            <label class="flex items-center p-3 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors cursor-pointer">
                                <input type="checkbox"
                                       wire:model="has_hardcopy"
                                       class="rounded border-gray-300 dark:border-gray-600 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                <span class="ml-3 text-sm font-medium text-gray-700 dark:text-gray-300">
                                    <!-- fas fa-book -->
                                    <svg class="w-4 h-4 inline mr-2" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12 3C7.03 3 3 5.03 3 9v6c0 3.97 4.03 7 9 7s9-3.03 9-7V9c0-3.97-4.03-6-9-6zm0 16c-3.87 0-7-2.13-7-5V9c0-2.87 3.13-5 7-5s7 2.13 7 5v5c0 2.87-3.13 5-7 5z"/>
                                        <circle cx="12" cy="12" r="2"/>
                                    </svg>Has Hardcopy
                                </span>
                            </label>
                            <label class="flex items-center p-3 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors cursor-pointer">
                                <input type="checkbox"
                                       wire:model="has_softcopy"
                                       class="rounded border-gray-300 dark:border-gray-600 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                <span class="ml-3 text-sm font-medium text-gray-700 dark:text-gray-300">
                                    <!-- fas fa-file-pdf -->
                                    <svg class="w-4 h-4 inline mr-2" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M14 2H6c-1.1 0-2 .9-2 2v16c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V8l-6-6zm4 18H6V4h7v5h5v11z"/>
                                        <path d="M12 12h2l-1 4h-1v-4z"/>
                                    </svg>Has Softcopy
                                </span>
                            </label>
                        </div>
                    </div>

                    <!-- File Uploads -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            <!-- fas fa-image -->
                            <svg class="w-4 h-4 inline mr-2 text-pink-500" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M21 19V5c0-1.1-.9-2-2-2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2zM8.5 13.5l2.5 3.01L14.5 12l4.5 6H5l3.5-4.5z"/>
                            </svg>Cover Image
                        </label>
                        <div class="border-2 border-dashed border-gray-300 dark:border-gray-600 dark:border-gray-600 rounded-lg p-6 text-center hover:border-blue-500 dark:hover:border-blue-400 transition-colors">
                            <input type="file"
                                   wire:model="cover_image"
                                   accept="image/*"
                                   class="hidden"
                                   id="cover-image-upload">
                            <label for="cover-image-upload" class="cursor-pointer">
                                <!-- fas fa-cloud-upload-alt -->
                                <svg class="w-8 h-8 text-gray-400 mx-auto mb-2" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M19.35 10.04C18.67 6.59 15.64 4 12 4 9.11 4 6.6 5.64 5.35 8.04 2.34 8.36 0 10.91 0 14c0 3.31 2.69 6 6 6h13c2.76 0 5-2.24 5-5 0-2.64-2.05-4.78-4.65-4.96zM14 13v4h-4v-4H7l5-5 5 5h-3z"/>
                                </svg>
                                <p class="text-sm text-gray-600 dark:text-gray-400">Click to upload cover image</p>
                                <p class="text-xs text-gray-500 dark:text-gray-500">PNG, JPG up to 2MB</p>
                            </label>
                        </div>
                        @error('cover_image') <span class="text-red-500 text-sm mt-1 block"><!-- fas fa-exclamation-circle --><svg class="w-4 h-4 inline mr-1" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/></svg>{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            <!-- fas fa-file-pdf -->
                            <svg class="w-4 h-4 inline mr-2 text-red-500" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M14 2H6c-1.1 0-2 .9-2 2v16c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V8l-6-6zm4 18H6V4h7v5h5v11z"/>
                                <path d="M12 12h2l-1 4h-1v-4z"/>
                            </svg>PDF File
                        </label>
                        <div class="border-2 border-dashed border-gray-300 dark:border-gray-600 dark:border-gray-600 rounded-lg p-6 text-center hover:border-blue-500 dark:hover:border-blue-400 transition-colors">
                            <input type="file"
                                   wire:model="pdf_file"
                                   accept=".pdf"
                                   class="hidden"
                                   id="pdf-file-upload">
                            <label for="pdf-file-upload" class="cursor-pointer">
                                <!-- fas fa-file-pdf -->
                                <svg class="w-8 h-8 text-gray-400 mx-auto mb-2" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M14 2H6c-1.1 0-2 .9-2 2v16c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V8l-6-6zm4 18H6V4h7v5h5v11z"/>
                                    <path d="M12 12h2l-1 4h-1v-4z"/>
                                </svg>
                                <p class="text-sm text-gray-600 dark:text-gray-400">Click to upload PDF file</p>
                                <p class="text-xs text-gray-500 dark:text-gray-500">PDF up to 10MB</p>
                            </label>
                        </div>
                        @error('pdf_file') <span class="text-red-500 text-sm mt-1 block"><!-- fas fa-exclamation-circle --><svg class="w-4 h-4 inline mr-1" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/></svg>{{ $message }}</span> @enderror
                    </div>

                    <!-- Additional Information -->
                    <div class="lg:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            <!-- fas fa-info-circle -->
                            <svg class="w-4 h-4 inline mr-2 text-blue-500" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"/>
                            </svg>Additional Information
                        </label>
                        <textarea wire:model="additional_info"
                                  rows="4"
                                  class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white transition-colors resize-none"
                                  placeholder="Any additional information about the book..."></textarea>
                        @error('additional_info') <span class="text-red-500 text-sm mt-1 block"><!-- fas fa-exclamation-circle --><svg class="w-4 h-4 inline mr-1" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/></svg>{{ $message }}</span> @enderror
                    </div>

                    <!-- Subscription Conditions -->
                    <div class="lg:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            <!-- fas fa-scroll -->
                            <svg class="w-4 h-4 inline mr-2 text-amber-500" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M15 21h-2v-2h2v2zm-2-7h2v5h-2v-5zm4-4h2v9h-2v-9zM7 2v2h2V2H7zm0 18h2v2H7v-2zm-4-4h2v2H3v-2zm0-4h2v2H3v-2zm0-4h2v2H3V8zm0-4h2v2H3V4zm18 8h2v2h-2v-2zm0-4h2v2h-2V8zm0-4h2v2h-2V4zm0-4h2v2h-2V0zm-4 12h2v2h-2v-2zm0-4h2v2h-2V8zm0-4h2v2h-2V4z"/>
                            </svg>Subscription Conditions
                        </label>
                        <textarea wire:model="subscription_conditions"
                                  rows="4"
                                  class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white transition-colors resize-none"
                                  placeholder="Enter custom subscription conditions or leave blank for defaults..."></textarea>
                        @error('subscription_conditions') <span class="text-red-500 text-sm mt-1 block"><!-- fas fa-exclamation-circle --><svg class="w-4 h-4 inline mr-1" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/></svg>{{ $message }}</span> @enderror
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="flex justify-end space-x-4 mt-8 pt-6 border-t border-gray-200 dark:border-gray-700">
                    <button type="button"
                            wire:click="closeBookModal"
                            class="px-6 py-3 text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-lg font-medium transition-colors">
                        <!-- fas fa-times -->
                        <svg class="w-4 h-4 inline mr-2" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/>
                        </svg>Cancel
                    </button>
                    <button type="submit"
                            class="px-6 py-3 bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white rounded-lg font-medium shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-200">
                        <!-- fas fa-save -->
                        <svg class="w-4 h-4 inline mr-2" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M17 3H5c-1.11 0-2 .9-2 2v14c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V7l-4-4zm-5 16c-1.66 0-3-1.34-3-3s1.34-3 3-3 3 1.34 3 3-1.34 3-3 3zm3-10H5V5h10v4z"/>
                        </svg>
                        {{ $editingBook ? 'Update Book' : 'Create Book' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
    @else
        <div class="">
            <div class="text-center">
                <h2 class="text-2xl font-semibold text-gray-800 dark:text-gray-200">No Books Found</h2>
                <p class="text-gray-600 dark:text-gray-400 mt-2">
                    Use the dashboard to add books
                </p>
            </div>
        </div>
    @endif
</section>