<div>
    @if($isVisible && $hasAccess)
        <div class="fixed inset-0 z-50 bg-black bg-opacity-95 flex items-center justify-center"
             x-data="pdfReaderAlpine()"
             x-init="init()"
             @keydown.escape="handleEscape()"
             @fullscreenchange="handleFullscreenChange()">

            <div class="w-full h-full max-w-full mx-auto p-4t flex flex-col"
                 :class="{ 'max-w-none p-0': isFullscreen }">

                <!-- Reader Header -->
                <div class="hidden items-center justify-between bg-gray-800 text-white p-4 rounded-t-lg mb-2"
                     :class="{ 'rounded-none mb-0': isFullscreen }">

                    <div class="flex items-center space-x-4">
                        <h2 class="text-lg font-semibold truncate max-w-md" title="{{ $bookTitle }}">
                            {{ $bookTitle }}
                        </h2>
                        <span class="text-sm text-gray-300 hidden md:inline">
                            by {{ $bookAuthor }}
                        </span>

                        @if($isLoading)
                            <div class="flex items-center space-x-2 text-yellow-400">
                                <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-yellow-400"></div>
                                <span class="text-sm">Loading...</span>
                            </div>
                        @endif
                    </div>

                    <!-- Quick Navigation -->
                    <div class="flex items-center space-x-2 hidden md:flex">
                        <button
                            wire:click="previousPage"
                            class="px-3 py-1 bg-blue-600 rounded-md hover:bg-blue-700 disabled:bg-gray-500 disabled:cursor-not-allowed transition-colors"
                            :disabled="!{{ $canGoPrevious ? 'false' : 'true' }}"
                            title="Previous Page">
                            ←
                        </button>

                        <div class="flex items-center space-x-1 text-sm">
                            <input
                                type="number"
                                wire:model.lazy="currentPage"
                                wire:change="goToPage($event.target.value)"
                                class="w-16 px-2 py-1 text-center bg-gray-700 text-white rounded border-0 focus:bg-gray-600 focus:outline-none"
                                min="1"
                                max="{{ $totalPages }}"
                                @keydown.enter="$wire.goToPage($event.target.value)">
                            <span>of {{ $totalPages }}</span>
                        </div>

                        <button
                            wire:click="nextPage"
                            class="px-3 py-1 bg-blue-600 rounded-md hover:bg-blue-700 disabled:bg-gray-500 disabled:cursor-not-allowed transition-colors"
                            :disabled="!{{ $canGoNext ? 'false' : 'true' }}"
                            title="Next Page">
                            →
                        </button>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex items-center space-x-2">
                        <!-- Zoom Controls -->
                        <div class="hidden lg:flex items-center space-x-1">
                            <button
                                wire:click="zoomOut"
                                class="px-2 py-1 bg-gray-700 hover:bg-gray-600 rounded-md transition-colors disabled:opacity-50"
                                :disabled="!{{ $canZoomOut ? 'false' : 'true' }}"
                                title="Zoom Out">
                                -
                            </button>
                            <span class="text-sm px-2">{{ $scalePercentage }}%</span>
                            <button
                                wire:click="zoomIn"
                                class="px-2 py-1 bg-gray-700 hover:bg-gray-600 rounded-md transition-colors disabled:opacity-50"
                                :disabled="!{{ $canZoomIn ? 'false' : 'true' }}"
                                title="Zoom In">
                                +
                            </button>
                            <button
                                wire:click="fitToWidth"
                                class="px-2 py-1 bg-gray-700 hover:bg-gray-600 rounded-md transition-colors text-xs"
                                title="Fit Width">
                                Fit
                            </button>
                        </div>

                        <!-- Fullscreen Toggle -->
                        <button
                            @click="toggleFullscreen()"
                            class="px-3 py-2 bg-gray-700 hover:bg-gray-600 rounded-md transition-colors"
                            title="Toggle Fullscreen">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                      d="M3 4a1 1 0 011-1h4a1 1 0 010 2H6.414l2.293 2.293a1 1 0 11-1.414 1.414L5 6.414V8a1 1 0 01-2 0V4zm9 1a1 1 0 010-2h4a1 1 0 011 1v4a1 1 0 01-2 0V6.414l-2.293 2.293a1 1 0 11-1.414-1.414L13.586 5H12zm-9 7a1 1 0 012 0v1.586l2.293-2.293a1 1 0 111.414 1.414L6.414 15H8a1 1 0 010 2H4a1 1 0 01-1-1v-4zm13-1a1 1 0 011 1v4a1 1 0 01-1 1h-4a1 1 0 010-2h1.586l-2.293-2.293a1 1 0 111.414-1.414L15 13.586V12a1 1 0 011-1z"
                                      clip-rule="evenodd"/>
                            </svg>
                        </button>

                        <!-- Close Button -->
                        <button
                            wire:click="closeReader"
                            class="px-3 py-2 bg-red-600 hover:bg-red-700 text-white rounded-md transition-colors"
                            title="Close Reader">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                      d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                      clip-rule="evenodd"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- PDF Container -->
                <div id="pdf-reader-container"
                     class="flex-1 bg-gray-900 rounded-b-sm overflow-hidden relative"
                     :class="{ 'rounded-none': isFullscreen }">

                    <!-- Loading Overlay -->
                    @if($isLoading)
                        <div class="absolute inset-0 flex items-center justify-center bg-gray-900 bg-opacity-75 z-20">
                            <div class="text-center text-white">
                                <div
                                    class="animate-spin rounded-full h-12 w-12 border-b-2 border-white mx-auto mb-4"></div>
                                <div class="text-xl font-semibold">Loading PDF...</div>
                                <div class="text-sm text-gray-300 mt-2">Please wait while we prepare your document</div>
                            </div>
                        </div>
                    @endif

                    <!-- Error State -->
                    @if($errorMessage)
                        <div class="absolute inset-0 flex items-center justify-center bg-red-50 z-20">
                            <div class="text-center p-8">
                                <div class="text-red-600 mb-4">
                                    <svg class="w-16 h-16 mx-auto mb-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                              d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                              clip-rule="evenodd"/>
                                    </svg>
                                </div>
                                <h3 class="text-lg font-semibold text-red-800 mb-2">PDF Reader Error</h3>
                                <p class="text-red-600 mb-4">{{ $errorMessage }}</p>
                                <div class="space-x-4">
                                    <button
                                        wire:click="closeReader"
                                        class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700">
                                        Close
                                    </button>
                                    <button
                                        onclick="location.reload()"
                                        class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">
                                        Retry
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Mobile Controls -->
                <div class="bg-gray-800 text-white p-3 rounded-b-lg md:hidden"
                     :class="{ 'rounded-none': isFullscreen }">
                    <div class="flex items-center justify-between">
                        <button
                            wire:click="previousPage"
                            class="px-4 py-2 bg-blue-600 rounded-md hover:bg-blue-700 disabled:bg-gray-500 disabled:cursor-not-allowed transition-colors"
                            :disabled="!{{ $canGoPrevious ? 'false' : 'true' }}">
                            Previous
                        </button>

                        <div class="flex items-center space-x-2">
                            <input
                                type="number"
                                wire:model.lazy="currentPage"
                                wire:change="goToPage($event.target.value)"
                                class="w-16 px-2 py-1 text-center bg-gray-700 text-white rounded border-0 focus:bg-gray-600 focus:outline-none"
                                min="1"
                                max="{{ $totalPages }}">
                            <span class="text-sm">of {{ $totalPages }}</span>
                        </div>

                        <button
                            wire:click="nextPage"
                            class="px-4 py-2 bg-blue-600 rounded-md hover:bg-blue-700 disabled:bg-gray-500 disabled:cursor-not-allowed transition-colors"
                            :disabled="!{{ $canGoNext ? 'false' : 'true' }}">
                            Next
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
    <script>
        window.pdfReaderAlpine = function () {
            return {
                isFullscreen: false,

                init() {
                    this.$nextTick(() => {
                        this.isFullscreen = @json($isFullscreen ?? false);
                    });
                },

                toggleFullscreen() {
                    const container = this.$el.querySelector('.fixed.inset-0.z-50') || this.$el;
                    if (!document.fullscreenElement) {
                        container.requestFullscreen().then(() => {
                            this.isFullscreen = true;
                        }).catch(err => {
                            console.warn('Failed to enter fullscreen:', err);
                        });
                    } else {
                        document.exitFullscreen().then(() => {
                            this.isFullscreen = false;
                        }).catch(err => {
                            console.warn('Failed to exit fullscreen:', err);
                        });
                    }
                },

                handleFullscreenChange() {
                    this.isFullscreen = !!document.fullscreenElement;
                    @this.
                    set('isFullscreen', this.isFullscreen);
                },

                handleEscape() {
                    if (document.fullscreenElement) {
                        document.exitFullscreen();
                    } else {
                        @this.
                        call('closeReader');
                    }
                }
            }
        };
    </script>

    <!-- Use the global PDFReaderWrapper -->
    <script defer>
        let pdfReaderWrapper = null;

        // Initialize the PDF reader wrapper
        document.addEventListener('DOMContentLoaded', () => {

            setTimeout(() => {
                if (window.PDFReaderWrapper) {
                    pdfReaderWrapper = new window.PDFReaderWrapper();
                }
            }, 10000);
            if (window.PDFReaderWrapper) {
                pdfReaderWrapper = new window.PDFReaderWrapper();

            } else {
                console.error('PDFReaderWrapper not found on window object');
            }
        });

        // Listen for Livewire events
        window.addEventListener('initializePDFReader', event => {
            console.log('initializePDFReader event received:', event.detail);

            // Add a small delay to ensure DOM is rendered
            setTimeout(() => {
                const container = document.querySelector('#pdf-reader-container');
                console.log('Container element when initializing:', container);

                if (container && pdfReaderWrapper) {
                    console.log('Initializing PDFReaderWrapper with event details:', event.detail);

                    pdfReaderWrapper.initialize(event.detail);
                } else {
                    console.error('PDF container not found or wrapper not initialized');
                }
            }, 1000);
        });


        window.addEventListener('destroyPDFReader', event => {
            console.log('destroyPDFReader event received');
            if (pdfReaderWrapper) {
                pdfReaderWrapper.destroy();
            }
        });

        // PDF Control Events
        window.addEventListener('pdfGoToPage', event => {
            if (pdfReaderWrapper) {
                pdfReaderWrapper.goToPage(event.detail.page);
            }
        });

        window.addEventListener('pdfNextPage', () => {
            if (pdfReaderWrapper) {
                pdfReaderWrapper.nextPage();
            }
        });

        window.addEventListener('pdfPreviousPage', () => {
            if (pdfReaderWrapper) {
                pdfReaderWrapper.previousPage();
            }
        });

        window.addEventListener('pdfZoomIn', () => {
            if (pdfReaderWrapper) {
                pdfReaderWrapper.zoomIn();
            }
        });

        window.addEventListener('pdfZoomOut', () => {
            if (pdfReaderWrapper) {
                pdfReaderWrapper.zoomOut();
            }
        });

        window.addEventListener('pdfFitToWidth', () => {
            if (pdfReaderWrapper) {
                pdfReaderWrapper.fitToWidth();
            }
        });

        window.addEventListener('pdfResetZoom', () => {
            if (pdfReaderWrapper && pdfReaderWrapper.pdfReader) {
                pdfReaderWrapper.pdfReader.scale = 1.2;
                pdfReaderWrapper.pdfReader.renderPage(pdfReaderWrapper.pdfReader.currentPage);
            }
        });

        window.addEventListener('pdfToggleFullscreen', () => {
            const container = document.querySelector('.fixed.inset-0.z-50');
            if (container) {
                if (!document.fullscreenElement) {
                    container.requestFullscreen();
                } else {
                    document.exitFullscreen();
                }
            }
        });

        // Cleanup on page unload
        window.addEventListener('beforeunload', () => {
            if (pdfReaderWrapper) {
                pdfReaderWrapper.destroy();
            }
        });
    </script>

        <style>
            /* PDF Reader Styles */
            .pdf-reader {
                font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            }

            .pdf-viewer-area {
                position: relative;
                background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            }

            /* Continuous Mode Styles */
            .pdf-pages-container {
                padding: 1rem;
                max-width: 100%;
            }

            .page-container {
                margin-bottom: 1rem;
                scroll-margin-top: 80px; /* Account for toolbar height */
            }

            .page-canvas {
                box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
                border: 1px solid rgba(255, 255, 255, 0.2);
                transition: box-shadow 0.3s ease;
            }

            .page-canvas:hover {
                box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            }

            .page-loading {
                background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
                background-size: 200% 100%;
                animation: loading 1.5s infinite;
            }

            @keyframes loading {
                0% {
                    background-position: 200% 0;
                }
                100% {
                    background-position: -200% 0;
                }
            }

            /* Single Page Mode Styles */
            .pdf-container {
                padding: 1rem;
            }

            #pdf-canvas {
                box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
                border: 1px solid rgba(255, 255, 255, 0.2);
            }

            /* Toolbar Enhancements */
            .pdf-toolbar {
                backdrop-filter: blur(10px);
                border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            }

            /* Scrollbar Styling */
            .pdf-viewer-area::-webkit-scrollbar {
                width: 12px;
            }

            .pdf-viewer-area::-webkit-scrollbar-track {
                background: rgba(0, 0, 0, 0.1);
                border-radius: 6px;
            }

            .pdf-viewer-area::-webkit-scrollbar-thumb {
                background: rgba(0, 0, 0, 0.3);
                border-radius: 6px;
                transition: background 0.3s ease;
            }

            .pdf-viewer-area::-webkit-scrollbar-thumb:hover {
                background: rgba(0, 0, 0, 0.5);
            }

            /* Responsive Design */
            @media (max-width: 768px) {
                .pdf-pages-container {
                    padding: 0.5rem;
                }

                .page-container {
                    margin-bottom: 0.5rem;
                    padding: 0.5rem;
                }
            }

            /* Progress Bar Animation */
            #progress-bar {
                transition: width 0.3s ease-in-out;
            }

            /* Loading States */
            .pdf-reader .hidden {
                display: none !important;
            }

            /* Focus and Accessibility */
            .pdf-reader button:focus,
            .pdf-reader input:focus {
                outline: 2px solid #3b82f6;
                outline-offset: 2px;
            }

            /* Print Styles */
            @media print {
                .pdf-toolbar,
                .pdf-progress {
                    display: none !important;
                }

                .pdf-viewer-area {
                    overflow: visible !important;
                    height: auto !important;
                }
            }

            /* Enhanced Page Input Styling */
            #page-input {
                /* Remove number input spinners */
                -webkit-appearance: none;
                -moz-appearance: textfield;
                appearance: none;
            }

            #page-input::-webkit-outer-spin-button,
            #page-input::-webkit-inner-spin-button {
                -webkit-appearance: none;
                margin: 0;
            }

            /* Enhanced focus state with subtle glow */
            #page-input:focus {
                box-shadow:
                    0 0 0 2px rgba(59, 130, 246, 0.1),
                    0 0 0 4px rgba(59, 130, 246, 0.05),
                    0 1px 3px 0 rgba(0, 0, 0, 0.1);
            }

            /* Smooth hover transition */
            #page-input:hover:not(:focus) {
                background-color: rgb(55, 65, 81); /* gray-700 */
                border-color: rgb(107, 114, 128); /* gray-500 */
                transform: translateY(-1px);
                box-shadow: 0 2px 4px -1px rgba(0, 0, 0, 0.1);
            }

            /* Active state */
            #page-input:active {
                transform: translateY(0);
                box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.1);
            }

            /* Placeholder styling */
            #page-input::placeholder {
                color: rgb(156, 163, 175); /* gray-400 */
                opacity: 0.7;
            }

            /* Selection styling */
            #page-input::selection {
                background-color: rgba(59, 130, 246, 0.3);
                color: white;
            }

            /* Invalid state for better UX */
            #page-input:invalid {
                border-color: rgb(239, 68, 68); /* red-500 */
                box-shadow: 0 0 0 2px rgba(239, 68, 68, 0.1);
            }

            #page-input:invalid:focus {
                border-color: rgb(239, 68, 68);
                box-shadow:
                    0 0 0 2px rgba(239, 68, 68, 0.1),
                    0 0 0 4px rgba(239, 68, 68, 0.05);
            }
        </style>
</div>
