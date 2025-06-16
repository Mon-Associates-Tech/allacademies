{{--@vite('resources/js/pdf-reader.js')--}}
<div x-data="pdfReader"
     x-show="show"
     class="fixed inset-0 z-50 overflow-hidden"
     x-cloak
     @keydown.escape.window="closeReader">
    <div class="absolute inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>

    <div class="fixed inset-0 z-10 overflow-y-auto">
        <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
            <div
                class="relative transform rounded-lg bg-white dark:bg-gray-800 px-4 pb-4 pt-5 text-left shadow-xl transition-all w-full max-w-6xl max-h-[90vh]">
                <!-- Close button -->
                <div class="absolute top-0 right-0 pt-4 pr-4">
                    <button @click="closeReader"
                            class="rounded-md bg-white dark:bg-gray-800 text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <span class="sr-only">Close</span>
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <!-- PDF Canvas -->
                <div class="mt-10 h-[calc(90vh-8rem)] overflow-auto">
                    <canvas id="pdf-canvas" class="mx-auto"></canvas>
                </div>

                <!-- Controls -->
                <div class="mt-4 flex justify-between items-center">
                    <button @click="prevPage"
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                            :disabled="pageNum <= 1">
                        Previous
                    </button>

                    <span class="text-sm text-gray-600" x-text="`Page ${pageNum} of ${pageCount}`"></span>

                    <button @click="nextPage"
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                            :disabled="pageNum >= pageCount">
                        Next
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

