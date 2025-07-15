import * as pdfjsLib from 'pdfjs-dist';
import pdfjsWorker from 'pdfjs-dist/build/pdf.worker?url';

// Configure worker for PDF.js v4
pdfjsLib.GlobalWorkerOptions.workerSrc = pdfjsWorker;

document.addEventListener('alpine:init', () => {
    Alpine.data('pdfReader', () => ({
        show: false,
        pdfDoc: null,
        pageNum: 1,
        pageCount: 0,
        canvas: null,
        ctx: null,
        scale: 1.5,
        loadingTask: null,
        title: 'PDF Document',
        isLoading: false,

        init() {
            console.log('PDF Reader initialized');

            // Listen for Livewire events
            Livewire.on('openPdfReader', async (data) => {
                console.log('Received openPdfReader event:', data);
                await this.handleOpenPdfReader(data);
            });

            // Listen for pdf-reader-open event (from PdfReader component)
            Livewire.on('pdf-reader-open', async (data) => {
                console.log('Received pdf-reader-open event:', data);
                await this.handleOpenPdfReader(data);
            });
        },

        async handleOpenPdfReader(data) {
            console.log('handleOpenPdfReader called with:', data);

            // Prevent multiple simultaneous loads
            if (this.isLoading) {
                console.log('Already loading, ignoring request');
                return;
            }

            let pdfUrl, currentPage, title;

            // Handle different data structures
            if (Array.isArray(data)) {
                console.log('Processing array data:', data);
                if (data.length > 0) {
                    if (typeof data[0] === 'object' && data[0].pdfUrl) {
                        console.log('Array contains object with pdfUrl:', data[0]);
                        pdfUrl = data[0].pdfUrl;
                        title = data[0].title || 'PDF Document';
                        currentPage = data[0].currentPage || 1;
                    } else {
                        pdfUrl = data[0];
                        title = data[1] || 'PDF Document';
                        currentPage = data[2] || 1;
                    }
                }
            } else if (data && typeof data === 'object') {
                console.log('Processing object data:', data);
                if (data.pdfUrl) {
                    pdfUrl = data.pdfUrl;
                    title = data.title || 'PDF Document';
                    currentPage = data.currentPage || 1;
                } else {
                    pdfUrl = data.url;
                    title = data.title || 'PDF Document';
                    currentPage = data.currentPage || 1;
                }
            } else if (typeof data === 'string') {
                console.log('Processing string data:', data);
                pdfUrl = data;
                title = 'PDF Document';
                currentPage = 1;
            }

            console.log('Extracted data:', { pdfUrl, title, currentPage });

            if (!pdfUrl || typeof pdfUrl !== 'string' || pdfUrl.trim() === '') {
                console.error('Invalid PDF URL:', pdfUrl);
                alert('Error: Invalid PDF URL provided');
                return;
            }

            pdfUrl = pdfUrl.trim().replace(/^['"]|['"]$/g, '');

            this.title = title;
            this.pageNum = currentPage;
            this.show = true;

            // Wait for DOM to update
            await this.$nextTick();

            setTimeout(async () => {
                if (this.initializeCanvas()) {
                    await this.loadPDF(pdfUrl);
                }
            }, 100);
        },

        initializeCanvas() {
            console.log('Initializing canvas...');

            let attempts = 0;
            const maxAttempts = 20;

            const tryInitialize = () => {
                this.canvas = document.getElementById('pdf-canvas');
                if (this.canvas) {
                    this.ctx = this.canvas.getContext('2d');
                    if (this.ctx) {
                        console.log('Canvas initialized successfully');
                        return true;
                    }
                }

                attempts++;
                if (attempts < maxAttempts) {
                    setTimeout(tryInitialize, 50);
                    return false;
                } else {
                    console.error('Canvas initialization failed');
                    return false;
                }
            };

            return tryInitialize();
        },

        async loadPDF(url) {
            console.log('Loading PDF from URL:', url);

            if (this.isLoading) {
                console.log('Already loading PDF, aborting');
                return;
            }

            this.isLoading = true;

            try {
                // Clean up any existing resources first
                await this.cleanup();

                console.log('Creating new PDF loading task...');

                // Create loading task with proper error handling
                this.loadingTask = pdfjsLib.getDocument({
                    url: url,
                    withCredentials: false,
                    isEvalSupported: false,
                    disableAutoFetch: true,
                    disableStream: true
                });

                console.log('Awaiting PDF document...');
                this.pdfDoc = await this.loadingTask.promise;

                if (!this.pdfDoc) {
                    throw new Error('PDF document is null');
                }

                this.pageCount = this.pdfDoc.numPages;
                console.log('PDF loaded successfully, pages:', this.pageCount);

                // Validate page number
                if (this.pageNum < 1 || this.pageNum > this.pageCount) {
                    this.pageNum = 1;
                }

                // Show canvas and render first page
                if (this.canvas) {
                    this.canvas.style.display = 'block';
                }

                await this.renderPage();
                this.savePage();

            } catch (error) {
                console.error('Error loading PDF:', error);

                if (error.name === 'AbortException') {
                    console.log('PDF loading was aborted - this is normal during cleanup');
                } else {
                    console.error('PDF loading failed:', error.message);
                    alert('Error loading PDF: ' + error.message + '\n\nPlease check if the PDF file exists and is accessible.');
                    this.closeReader();
                }
            } finally {
                this.isLoading = false;
            }
        },

        async renderPage() {
            if (!this.pdfDoc || !this.canvas || !this.ctx) {
                console.error('PDF document or canvas not ready');
                return;
            }

            try {
                console.log('Rendering page:', this.pageNum);

                // Validate page number
                if (this.pageNum < 1 || this.pageNum > this.pageCount) {
                    this.pageNum = Math.max(1, Math.min(this.pageNum, this.pageCount));
                }

                const page = await this.pdfDoc.getPage(this.pageNum);
                const viewport = page.getViewport({ scale: this.scale });

                this.canvas.height = viewport.height;
                this.canvas.width = viewport.width;

                // Clear canvas
                this.ctx.clearRect(0, 0, this.canvas.width, this.canvas.height);

                // Render page
                const renderContext = {
                    canvasContext: this.ctx,
                    viewport: viewport
                };

                await page.render(renderContext).promise;
                console.log('Page rendered successfully');

            } catch (error) {
                console.error('Error rendering page:', error);
                if (error.name !== 'AbortException') {
                    alert('Error rendering page: ' + error.message);
                }
            }
        },

        async nextPage() {
            if (this.pageNum >= this.pageCount || this.isLoading) return;
            this.pageNum++;
            await this.renderPage();
            this.savePage();
        },

        async prevPage() {
            if (this.pageNum <= 1 || this.isLoading) return;
            this.pageNum--;
            await this.renderPage();
            this.savePage();
        },

        savePage() {
            if (!this.pageCount || !this.pageNum) return;

            try {
                Livewire.dispatch('saveReadingProgress', {
                    page: this.pageNum,
                    totalPages: this.pageCount
                });
            } catch (error) {
                console.warn('Failed to save progress:', error);
            }
        },

        async cleanup() {
            console.log('Cleaning up PDF resources...');

            // Cancel loading task if it exists
            if (this.loadingTask) {
                try {
                    await this.loadingTask.destroy();
                    console.log('Loading task destroyed');
                } catch (error) {
                    console.warn('Error destroying loading task:', error);
                }
                this.loadingTask = null;
            }

            // Destroy PDF document if it exists
            if (this.pdfDoc) {
                try {
                    await this.pdfDoc.destroy();
                    console.log('PDF document destroyed');
                } catch (error) {
                    console.warn('Error destroying PDF document:', error);
                }
                this.pdfDoc = null;
            }

            this.pageCount = 0;
        },

        async closeReader() {
            console.log('Closing PDF reader...');

            this.show = false;
            this.savePage();

            // Clean up all resources
            await this.cleanup();

            // Reset state
            if (this.canvas) {
                this.canvas.style.display = 'none';
            }

            this.canvas = null;
            this.ctx = null;
            this.pageNum = 1;
            this.title = 'PDF Document';
            this.isLoading = false;

            // Notify Livewire
            try {
                Livewire.dispatch('closePdfReader');
            } catch (error) {
                console.warn('Failed to dispatch close event:', error);
            }
        }
    }));
});
