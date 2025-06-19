import * as pdfjsLib from 'pdfjs-dist';
import pdfjsWorker from 'pdfjs-dist/build/pdf.worker?url';

// Configure worker for PDF.js v4
pdfjsLib.GlobalWorkerOptions.workerSrc = pdfjsWorker;

document.addEventListener('alpine:init', () => {

    if (!Alpine) {
        console.error('Alpine is not defined');
        return;
    }
    Alpine.data('pdfReader', () => ({
        show: false,
        pdfDoc: null,
        pageNum: 1,
        pageCount: 0,
        canvas: null,
        ctx: null,
        scale: 1.5,
        loadingTask: null,

        init() {
            console.log('PDF Reader initialized');

            // Listen for the openPdfReader event
            Livewire.on('openPdfReader', async (data) => {
                console.log('Received openPdfReader event:', data);

                const pdfUrl = data[0]?.pdfUrl || data.pdfUrl;
                const currentPage = data[0]?.currentPage || data.currentPage || 1;

                console.log('Extracted pdfUrl:', pdfUrl);
                console.log('Extracted currentPage:', currentPage);

                if (!pdfUrl) {
                    console.error('No PDF URL provided');
                    alert('Error: No PDF URL provided');
                    return;
                }

                this.show = true;
                this.pageNum = currentPage;

                // Wait for DOM to update before initializing canvas
                await this.$nextTick();
                this.initializeCanvas();
                await this.loadPDF(pdfUrl);
            });
        },

        initializeCanvas() {
            this.canvas = document.getElementById('pdf-canvas');
            if (!this.canvas) {
                console.error('Canvas element not found');
                return false;
            }
            this.ctx = this.canvas.getContext('2d');
            if (!this.ctx) {
                console.error('Could not get canvas context');
                return false;
            }
            console.log('Canvas initialized successfully');
            return true;
        },

        async loadPDF(url) {
            console.log('Loading PDF from URL:', url);
            try {
                if (!this.canvas || !this.ctx) {
                    if (!this.initializeCanvas()) {
                        throw new Error('Canvas not available');
                    }
                }

                // Clean up previous resources
                this.cleanup();

                console.log('Creating loading task...');

                // Use simple URL string approach for PDF.js v4
                this.loadingTask = pdfjsLib.getDocument(url);

                console.log('Awaiting PDF document...');
                this.pdfDoc = await this.loadingTask.promise;

                // Validate the PDF document
                if (!this.pdfDoc) {
                    throw new Error('Failed to load PDF document');
                }

                this.pageCount = this.pdfDoc.numPages;
                console.log('PDF loaded successfully, total pages:', this.pageCount);

                // Ensure page number is valid
                if (this.pageNum < 1 || this.pageNum > this.pageCount) {
                    this.pageNum = 1;
                }

                await this.renderPage();

                // Save initial state
                this.savePage();
            } catch (error) {
                console.error('Error loading PDF:', error);
                console.error('Error details:', {
                    url: url,
                    error: error.message,
                    stack: error.stack
                });
                alert('Error loading PDF: ' + error.message);
                this.closeReader();
            }
        },

        async renderPage() {
            if (!this.pdfDoc || !this.canvas || !this.ctx) {
                console.error('PDF document or canvas not ready');
                return;
            }

            try {
                console.log('Rendering page:', this.pageNum, 'of', this.pageCount);

                // Validate page number
                if (this.pageNum < 1 || this.pageNum > this.pageCount) {
                    console.error('Invalid page number:', this.pageNum);
                    this.pageNum = Math.max(1, Math.min(this.pageNum, this.pageCount));
                    console.log('Corrected page number to:', this.pageNum);
                }

                // For PDF.js v4, use the direct approach
                const page = await this.pdfDoc.getPage(this.pageNum);
                console.log('Page object retrieved successfully');

                // Get viewport with scale
                const viewport = page.getViewport({scale: this.scale});
                console.log('Viewport created:', viewport.width, 'x', viewport.height);

                // Set canvas dimensions
                this.canvas.height = viewport.height;
                this.canvas.width = viewport.width;

                // Clear canvas before rendering
                this.ctx.clearRect(0, 0, this.canvas.width, this.canvas.height);

                // Render PDF page - PDF.js v4 approach
                const renderContext = {
                    canvasContext: this.ctx,
                    viewport: viewport
                };

                console.log('Starting render...');
                await page.render(renderContext).promise;
                console.log('Page rendered successfully');

            } catch (error) {
                console.error('Error rendering page:', error);
                console.error('PDF Doc state:', {
                    pdfDoc: !!this.pdfDoc,
                    numPages: this.pdfDoc?.numPages,
                    currentPage: this.pageNum,
                    pageCount: this.pageCount
                });
                alert('Error rendering page: ' + error.message);
            }
        },

        async nextPage() {
            if (this.pageNum >= this.pageCount) return;

            this.pageNum++;
            await this.renderPage();
            this.savePage();
        },

        async prevPage() {
            if (this.pageNum <= 1) return;

            this.pageNum--;
            await this.renderPage();
            this.savePage();
        },

        savePage() {
            if (!this.pageCount || !this.pageNum) return;

            // Save progress via Livewire
            Livewire.dispatch('saveReadingProgress', {
                page: this.pageNum,
                totalPages: this.pageCount
            });
        },

        cleanup() {
            // Clean up previous resources
            if (this.loadingTask) {
                try {
                    this.loadingTask.destroy();
                } catch (e) {
                    console.warn('Error destroying loading task:', e);
                }
                this.loadingTask = null;
            }

            if (this.pdfDoc) {
                try {
                    this.pdfDoc.destroy();
                } catch (e) {
                    console.warn('Error destroying document:', e);
                }
                this.pdfDoc = null;
            }

            this.pageCount = 0;
        },

        async closeReader() {
            this.show = false;
            this.savePage();

            // Clean up resources
            this.cleanup();

            this.canvas = null;
            this.ctx = null;
            this.pageNum = 1;
        }
    }));
});
