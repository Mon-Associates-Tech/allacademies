<div>
    @if($isVisible && $hasAccess)
        <div class="fixed inset-0 z-40 bg-black/95 flex items-center justify-center"
             x-data="pdfReaderAlpine()"
             x-init="init()"
             @keydown.escape="handleEscape()"
             @fullscreenchange="handleFullscreenChange()">
            <div class="w-full h-full max-w-full mx-auto flex flex-col">
                <div id="pdf-reader-container" class="flex-1 bg-gray-900 overflow-hidden relative">
                    <!-- Livewire Annotation Thread Panel (will be positioned by PDF reader) -->
                    <div id="livewire-comments-container" class="hidden">
                        @livewire('books.annotation-thread-panel', ['bookId' => $bookId])
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
                handleFullscreenChange() {
                    this.isFullscreen = !!document.fullscreenElement;
                    @this.set('isFullscreen', this.isFullscreen);
                },
                handleEscape() {
                    if (document.fullscreenElement) {
                        document.exitFullscreen();
                    } else {
                        @this.call('closeReader');
                    }
                }
            }
        };
    </script>

    <script defer>
        let pdfReaderWrapper = null;

        document.addEventListener('DOMContentLoaded', () => {
            if (window.PDFReaderWrapper) {
                pdfReaderWrapper = new window.PDFReaderWrapper();
            } else {
                console.error('PDFReaderWrapper not found on window object');
            }
        });

        window.addEventListener('initializePDFReader', event => {
            setTimeout(() => {
                const container = document.querySelector('#pdf-reader-container');
                if (container && pdfReaderWrapper) {
                    pdfReaderWrapper.initialize(event.detail);
                } else {
                    console.error('PDF container not found or wrapper not initialized');
                }
            }, 200);
        });

        window.addEventListener('destroyPDFReader', () => {
            if (pdfReaderWrapper) {
                pdfReaderWrapper.destroy();
            }
        });

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
            const container = document.querySelector('.fixed.inset-0.z-40');
            if (!container) return;
            if (!document.fullscreenElement) {
                container.requestFullscreen();
            } else {
                document.exitFullscreen();
            }
        });

        window.addEventListener('beforeunload', () => {
            if (pdfReaderWrapper) {
                pdfReaderWrapper.destroy();
            }
        });
    </script>
</div>
