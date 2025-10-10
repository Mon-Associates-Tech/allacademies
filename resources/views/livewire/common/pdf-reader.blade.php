<div>
{{--    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.16.105/pdf.min.js"></script>--}}

{{--    <div
        x-data="pdfViewer({
            url: '{{ $pdfUrl }}',
            initialPage: {{ $currentPage }},
            bookId: {{ $bookId }}
        })"
        x-init="init()"
        @beforeunload.window="saveProgress()"
        class="bg-gray-900 text-white p-4 rounded-lg shadow-lg flex flex-col h-[90vh]"
    >
    </div>--}}

    <script>
        // Wait for PDF.js to be fully loaded
        document.addEventListener('DOMContentLoaded', function() {
            // Ensure PDF.js global worker is set up correctly
            if (typeof pdfjsLib !== 'undefined') {
                pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.16.105/pdf.worker.min.js';
            }
        });

        function pdfViewer(config) {
            // Store references outside Alpine to avoid proxy issues
            const pdfState = {
                document: null,
                renderTask: null,
                isDestroyed: false
            };

            return {
                pageNum: config.initialPage,
                totalPages: 0,
                pageRendering: false,
                pageNumPending: null,
                scale: 1.2,
                viewerWidth: 100,
                loading: true,
                progressPercentage: @entangle('progressPercentage').defer,
                saveTimeout: null,
                canvasId: 'pdf-canvas',

                init() {
                    // Validate PDF.js availability
                    if (typeof pdfjsLib === 'undefined') {
                        console.error("PDF.js library is not loaded.");
                        this.loading = false;
                        return;
                    }

                    // Use a try-catch to handle potential initialization errors
                    try {
                        // Set worker with error handling
                        if (!pdfjsLib.GlobalWorkerOptions.workerSrc) {
                            pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.16.105/pdf.worker.min.js';
                        }

                        // Delay to ensure DOM readiness
                        this.$nextTick(() => {
                            setTimeout(() => {
                                this.loadPdf();
                            }, 200);
                        });

                    } catch (error) {
                        console.error('Error during PDF viewer initialization:', error);
                        this.loading = false;
                    }
                },

                loadPdf() {
                    if (pdfState.isDestroyed) return;

                    this.loading = true;

                    // Create loading task with explicit error handling
                    const loadingTask = pdfjsLib.getDocument({
                        url: config.url,
                        withCredentials: true,
                        // Add these options for better compatibility
                        disableAutoFetch: false,
                        disableStream: false,
                        disableRange: false
                    });

                    // Handle the promise directly without async/await to avoid context issues
                    loadingTask.promise.then((pdf) => {
                        if (pdfState.isDestroyed) {
                            pdf.destroy();
                            return;
                        }

                        pdfState.document = pdf;
                        this.totalPages = pdf.numPages;
                        this.loading = false;

                        // Render the initial page
                        this.renderPage(this.pageNum);
                        this.updateProgressState();

                    }).catch((error) => {
                        console.error('Error loading PDF:', error);
                        this.loading = false;

                        // Provide user-friendly error message
                        const canvas = document.getElementById(this.canvasId);
                        if (canvas) {
                            const ctx = canvas.getContext('2d');
                            ctx.fillStyle = '#ff0000';
                            ctx.font = '16px Arial';
                            ctx.fillText('Error loading PDF: ' + error.message, 10, 30);
                        }
                    });
                },

                renderPage(num) {
                    if (!pdfState.document || pdfState.isDestroyed) {
                        console.warn('PDF document not available for rendering');
                        return;
                    }

                    this.pageRendering = true;
                    this.pageNum = num;

                    // Get page with proper error handling
                    pdfState.document.getPage(num).then((page) => {
                        if (pdfState.isDestroyed) {
                            return;
                        }

                        // Ensure canvas exists
                        const canvas = document.getElementById(this.canvasId);
                        if (!canvas) {
                            console.error("Canvas element not found");
                            this.pageRendering = false;
                            return;
                        }

                        const ctx = canvas.getContext('2d');

                        // Cancel any ongoing render task
                        if (pdfState.renderTask && !pdfState.renderTask._internalRenderTask.cancelled) {
                            pdfState.renderTask.cancel();
                        }

                        // Clear canvas
                        ctx.clearRect(0, 0, canvas.width, canvas.height);

                        // Get viewport
                        const viewport = page.getViewport({ scale: this.scale });

                        // Set canvas dimensions
                        canvas.width = viewport.width;
                        canvas.height = viewport.height;

                        // Responsive sizing
                        const container = canvas.parentElement;
                        if (container) {
                            const containerWidth = container.offsetWidth;
                            const displayWidth = Math.min(containerWidth * (this.viewerWidth / 100), viewport.width);
                            const displayHeight = (displayWidth / viewport.width) * viewport.height;

                            canvas.style.width = displayWidth + 'px';
                            canvas.style.height = displayHeight + 'px';
                        }

                        // Create render context
                        const renderContext = {
                            canvasContext: ctx,
                            viewport: viewport
                        };

                        // Start rendering
                        pdfState.renderTask = page.render(renderContext);

                        pdfState.renderTask.promise.then(() => {
                            if (pdfState.isDestroyed) return;

                            pdfState.renderTask = null;
                            this.pageRendering = false;

                            console.log(`Page ${num} rendered successfully`);

                            // Handle queued page render
                            if (this.pageNumPending !== null) {
                                const pendingPage = this.pageNumPending;
                                this.pageNumPending = null;
                                setTimeout(() => this.renderPage(pendingPage), 100);
                            }

                        }).catch((error) => {
                            if (pdfState.isDestroyed) return;

                            if (error?.name !== 'RenderingCancelledException') {
                                console.error("Error rendering page:", error);
                            }
                            this.pageRendering = false;
                        });

                    }).catch((error) => {
                        console.error("Error getting page:", error);
                        this.pageRendering = false;
                    });

                    this.updateProgressState();
                },

                queueRenderPage(num) {
                    if (this.pageRendering) {
                        this.pageNumPending = num;
                    } else {
                        this.renderPage(num);
                    }
                },

                prevPage() {
                    if (this.pageNum <= 1) return;
                    this.pageNum--;
                    this.queueRenderPage(this.pageNum);
                },

                nextPage() {
                    if (!pdfState.document || this.pageNum >= this.totalPages) return;
                    this.pageNum++;
                    this.queueRenderPage(this.pageNum);
                },

                zoomIn() {
                    if (this.scale >= 3.0) return;
                    this.scale += 0.2;
                    this.queueRenderPage(this.pageNum);
                },

                zoomOut() {
                    if (this.scale <= 0.5) return;
                    this.scale -= 0.2;
                    this.queueRenderPage(this.pageNum);
                },

                updateProgressState() {
                    if (!pdfState.document) return;
                    this.progressPercentage = Math.round((this.pageNum / this.totalPages) * 100);

                    clearTimeout(this.saveTimeout);
                    this.saveTimeout = setTimeout(() => this.saveProgress(), 1500);
                },

                saveProgress() {
                    if (!pdfState.document || pdfState.isDestroyed) return;

                    // Use Livewire's wire:call method for better compatibility
                    if (this.$wire && this.$wire.call) {
                        this.$wire.call('updateProgress', this.pageNum, this.totalPages);
                    }
                },

                // Cleanup method
                destroy() {
                    pdfState.isDestroyed = true;

                    if (pdfState.renderTask) {
                        pdfState.renderTask.cancel();
                        pdfState.renderTask = null;
                    }

                    if (pdfState.document) {
                        pdfState.document.destroy();
                        pdfState.document = null;
                    }

                    clearTimeout(this.saveTimeout);
                }
            }
        }

        // Global cleanup on page unload
        window.addEventListener('beforeunload', function() {
            // Clean up any PDF.js resources
            if (window.pdfViewerInstance && window.pdfViewerInstance.destroy) {
                window.pdfViewerInstance.destroy();
            }
        });
    </script>
</div>
