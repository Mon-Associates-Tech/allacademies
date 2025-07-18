<div x-data="pdfReader()" x-init="init()">
    <div x-show="show"
         x-cloak
         class="fixed inset-0 bg-black/90 backdrop-blur-sm flex items-center justify-center z-50 p-4"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">

        <div class="bg-white dark:bg-gray-800 rounded-2xl w-full max-w-6xl h-[90vh] flex flex-col shadow-2xl"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 transform scale-90"
             x-transition:enter-end="opacity-100 transform scale-100">

            <!-- Debug Info -->
            <div x-show="!show" class="p-4 bg-red-100 text-red-800 text-sm">
                Component initialized: <span x-text="isInitialized ? 'Yes' : 'No'"></span>
            </div>

            <!-- PDF Reader Header -->
            <div class="flex items-center justify-between p-4 border-b border-gray-200 dark:border-gray-700 bg-gradient-to-r from-red-600 to-red-700 text-white rounded-t-2xl">
                <h3 class="text-xl font-bold" x-text="title || 'PDF Document'"></h3>
                <div class="flex items-center space-x-4">
                    <div class="text-sm">
                        <span x-text="pageCount || 0"></span> pages
                    </div>
                    <button @click="closeReader()"
                            class="text-white hover:text-gray-200 p-2 rounded-lg hover:bg-white/10 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- PDF Reader Content -->
            <div class="flex-1 p-4 overflow-hidden">
                <div class="w-full h-full bg-gray-100 dark:bg-gray-900 rounded-lg overflow-y-auto flex justify-center items-center">
                    <!-- Loading indicator -->
                    <div x-show="isLoading && show" class="text-center">
                        <div class="animate-spin rounded-full h-32 w-32 border-b-2 border-red-600 mx-auto"></div>
                        <p class="mt-4 text-gray-600 dark:text-gray-400">Loading PDF...</p>
                    </div>

                    <!-- Error message -->
                    <div x-show="error && show" class="text-center p-4">
                        <div class="text-red-600 dark:text-red-400 mb-4">
                            <svg class="w-16 h-16 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <p class="text-lg font-semibold">Error loading PDF</p>
                            <p class="text-sm mt-2" x-text="error"></p>
                        </div>
                        <button @click="closeReader()" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                            Close
                        </button>
                    </div>

                    <!-- PDF Canvas -->
                    <div x-show="pdfDoc && !isLoading && !error" class="w-full h-full flex justify-center items-center overflow-auto">
                        <canvas x-ref="pdfCanvas" class="shadow-lg bg-white max-w-full max-h-full"></canvas>
                    </div>

                    <!-- No PDF loaded message -->
                    <div x-show="!pdfDoc && !isLoading && !error && show" class="text-center p-4">
                        <p class="text-gray-600 dark:text-gray-400">No PDF loaded</p>
                    </div>
                </div>
            </div>

            <!-- PDF Controls -->
            <div x-show="pdfDoc && !isLoading && !error" class="p-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700 rounded-b-2xl">
                <div class="flex items-center justify-center space-x-4">
                    <button @click="prevPage()"
                            :disabled="pageNum <= 1 || isLoading"
                            class="px-4 py-2 bg-gray-200 dark:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-500 disabled:opacity-50 transition-colors">
                        Previous
                    </button>

                    <div class="flex items-center space-x-2 text-sm text-gray-600 dark:text-gray-400">
                        <span>Page</span>
                        <span x-text="pageNum || 1"></span>
                        <span>of</span>
                        <span x-text="pageCount || 0"></span>
                    </div>

                    <button @click="nextPage()"
                            :disabled="pageNum >= pageCount || isLoading"
                            class="px-4 py-2 bg-gray-200 dark:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-500 disabled:opacity-50 transition-colors">
                        Next
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
