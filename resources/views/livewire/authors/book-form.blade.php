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
                            <i class="fas fa-book text-white"></i>
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
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
            </div>

            <!-- Modal Body -->
            <form wire:submit="saveBook" class="p-6">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Title -->
                    <div class="lg:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            <i class="fas fa-heading mr-2 text-blue-500"></i>Book Title *
                        </label>
                        <input type="text"
                               wire:model="title"
                               class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white transition-colors"
                               placeholder="Enter book title">
                        @error('title') <span class="text-red-500 text-sm mt-1 block"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</span> @enderror
                    </div>

                    <!-- Category -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            <i class="fas fa-tags mr-2 text-purple-500"></i>Category *
                        </label>
                        <select wire:model="book_category_id"
                                class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white transition-colors">
                            <option value="">Select Category</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                        @error('book_category_id') <span class="text-red-500 text-sm mt-1 block"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</span> @enderror
                    </div>

                    <!-- Edition -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            <i class="fas fa-bookmark mr-2 text-green-500"></i>Edition
                        </label>
                        <input type="text"
                               wire:model="edition"
                               class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white transition-colors"
                               placeholder="e.g., 1st Edition">
                        @error('edition') <span class="text-red-500 text-sm mt-1 block"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</span> @enderror
                    </div>

                    <!-- Publisher -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            <i class="fas fa-building mr-2 text-indigo-500"></i>Publisher
                        </label>
                        <input type="text"
                               wire:model="publisher"
                               class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white transition-colors"
                               placeholder="Publisher name">
                        @error('publisher') <span class="text-red-500 text-sm mt-1 block"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</span> @enderror
                    </div>

                    <!-- Pages -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            <i class="fas fa-file-alt mr-2 text-orange-500"></i>Pages
                        </label>
                        <input type="number"
                               wire:model="pages"
                               class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white transition-colors"
                               placeholder="Number of pages">
                        @error('pages') <span class="text-red-500 text-sm mt-1 block"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</span> @enderror
                    </div>

                    <!-- Subscription Fee -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            <i class="fas fa-dollar-sign mr-2 text-emerald-500"></i>Annual Subscription Fee (GHS)
                        </label>
                        <input type="number"
                               step="0.01"
                               wire:model="annual_subscription_fee"
                               class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white transition-colors"
                               placeholder="0.00">
                        @error('annual_subscription_fee') <span class="text-red-500 text-sm mt-1 block"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</span> @enderror
                    </div>

                    <!-- Book Format -->
                    <div class="lg:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
                            <i class="fas fa-layer-group mr-2 text-cyan-500"></i>Book Format
                        </label>
                        <div class="flex flex-wrap gap-4">
                            <label class="flex items-center p-3 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors cursor-pointer">
                                <input type="checkbox"
                                       wire:model="has_hardcopy"
                                       class="rounded border-gray-300 dark:border-gray-600 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                <span class="ml-3 text-sm font-medium text-gray-700 dark:text-gray-300">
                                            <i class="fas fa-book mr-2"></i>Has Hardcopy
                                        </span>
                            </label>
                            <label class="flex items-center p-3 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors cursor-pointer">
                                <input type="checkbox"
                                       wire:model="has_softcopy"
                                       class="rounded border-gray-300 dark:border-gray-600 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                <span class="ml-3 text-sm font-medium text-gray-700 dark:text-gray-300">
                                            <i class="fas fa-file-pdf mr-2"></i>Has Softcopy
                                        </span>
                            </label>
                        </div>
                    </div>

                    <!-- File Uploads -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            <i class="fas fa-image mr-2 text-pink-500"></i>Cover Image
                        </label>
                        <div class="border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg p-6 text-center hover:border-blue-500 dark:hover:border-blue-400 transition-colors">
                            <input type="file"
                                   wire:model="cover_image"
                                   accept="image/*"
                                   class="hidden"
                                   id="cover-image-upload">
                            <label for="cover-image-upload" class="cursor-pointer">
                                <i class="fas fa-cloud-upload-alt text-3xl text-gray-400 mb-2"></i>
                                <p class="text-sm text-gray-600 dark:text-gray-400">Click to upload cover image</p>
                                <p class="text-xs text-gray-500 dark:text-gray-500">PNG, JPG up to 2MB</p>
                            </label>
                        </div>
                        @error('cover_image') <span class="text-red-500 text-sm mt-1 block"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            <i class="fas fa-file-pdf mr-2 text-red-500"></i>PDF File
                        </label>
                        <div class="border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg p-6 text-center hover:border-blue-500 dark:hover:border-blue-400 transition-colors">
                            <input type="file"
                                   wire:model="pdf_file"
                                   accept=".pdf"
                                   class="hidden"
                                   id="pdf-file-upload">
                            <label for="pdf-file-upload" class="cursor-pointer">
                                <i class="fas fa-file-pdf text-3xl text-gray-400 mb-2"></i>
                                <p class="text-sm text-gray-600 dark:text-gray-400">Click to upload PDF file</p>
                                <p class="text-xs text-gray-500 dark:text-gray-500">PDF up to 10MB</p>
                            </label>
                        </div>
                        @error('pdf_file') <span class="text-red-500 text-sm mt-1 block"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</span> @enderror
                    </div>

                    <!-- Additional Information -->
                    <div class="lg:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            <i class="fas fa-info-circle mr-2 text-blue-500"></i>Additional Information
                        </label>
                        <textarea wire:model="additional_info"
                                  rows="4"
                                  class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white transition-colors resize-none"
                                  placeholder="Any additional information about the book..."></textarea>
                        @error('additional_info') <span class="text-red-500 text-sm mt-1 block"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</span> @enderror
                    </div>

                    <!-- Subscription Conditions -->
                    <div class="lg:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            <i class="fas fa-scroll mr-2 text-amber-500"></i>Subscription Conditions
                        </label>
                        <textarea wire:model="subscription_conditions"
                                  rows="4"
                                  class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white transition-colors resize-none"
                                  placeholder="Enter custom subscription conditions or leave blank for defaults..."></textarea>
                        @error('subscription_conditions') <span class="text-red-500 text-sm mt-1 block"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</span> @enderror
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="flex justify-end space-x-4 mt-8 pt-6 border-t border-gray-200 dark:border-gray-700">
                    <button type="button"
                            wire:click="closeBookModal"
                            class="px-6 py-3 text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-lg font-medium transition-colors">
                        <i class="fas fa-times mr-2"></i>Cancel
                    </button>
                    <button type="submit"
                            class="px-6 py-3 bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white rounded-lg font-medium shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-200">
                        <i class="fas fa-save mr-2"></i>
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

