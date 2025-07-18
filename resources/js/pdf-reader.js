// Use a more compatible approach with PDF.js 3.x
import * as pdfjsLib from 'pdfjs-dist';

// It's recommended to host the worker yourself, but for simplicity, CDN is fine.
// Make sure the version matches the library version (e.g., 3.11.174)
pdfjsLib.GlobalWorkerOptions.workerSrc = `//cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js`;

document.addEventListener('alpine:init', () => {
    console.log('Alpine.js initialized, setting up PDF reader data');

    Alpine.data('pdfReader', () => ({
        show: false,
        pdfDoc: null,
        pageNum: 1,
        pageCount: 0,
        scale: 1.5,
        title: 'PDF Document',
        isLoading: false,
        error: null,

        // Alpine.js will automatically call this when the component is initialized
        init() {
            console.log('PDF Reader Alpine component initialized');

            // Listen for Livewire events to open the reader
            if (window.Livewire) {
                Livewire.on('openPdfReader', (data) => this.open(data));
                // Optional: For backward compatibility with your old event name
                Livewire.on('pdf-reader-open', (data) => this.open(data));
            }
        },

        // This method handles opening the PDF viewer
        async open(data) {
            console.log('Received open request with data:', data);

            if (this.isLoading) {
                console.warn('Already loading a PDF, request ignored.');
                return;
            }

            // Reset previous state
            this.error = null;
            this.isLoading = true;

            let pdfUrl, currentPage, title;

            // --- Simplified Data Handling ---
            if (typeof data === 'string') {
                pdfUrl = data;
            } else if (typeof data === 'object' && data !== null) {
                pdfUrl = data.pdfUrl || data.url;
                title = data.title;
                currentPage = data.currentPage;
            }

            if (!pdfUrl) {
                this.error = 'No PDF URL provided.';
                this.isLoading = false;
                this.show = true;
                return;
            }

            this.title = title || 'PDF Document';
            this.pageNum = parseInt(currentPage, 10) || 1;
            this.show = true;

            // Wait for Alpine to render the modal and the canvas element
            await this.$nextTick();

            // Now that the DOM is updated, load the PDF
            this.loadPDF(pdfUrl);
        },

        // This method loads the PDF document
        async loadPDF(url) {
            console.log('Loading PDF from:', url);

            // Ensure canvas is available via x-ref
            const canvas = this.$refs.pdfCanvas;
            if (!canvas) {
                this.error = 'Canvas element reference (`x-ref="pdfCanvas"`) not found.';
                this.isLoading = false;
                return;
            }

            // --- Simplified PDF.js Configuration ---
            const loadingTask = pdfjsLib.getDocument({
                url: url,
                cMapUrl: 'https://cdn.jsdelivr.net/npm/pdfjs-dist@3.11.174/cmaps/',
                cMapPacked: true,
            });

            try {
                this.pdfDoc = await loadingTask.promise;
                this.pageCount = this.pdfDoc.numPages;
                console.log(`PDF loaded with ${this.pageCount} pages.`);
                await this.renderPage();
            } catch (err) {
                console.error('Error loading PDF:', err);
                this.error = `Failed to load PDF. ${err.message}`;
                // Hide the canvas on error
                if(this.$refs.pdfCanvas) {
                    this.$refs.pdfCanvas.style.display = 'none';
                }
            } finally {
                this.isLoading = false;
            }
        },

        // This method renders the current page onto the canvas
        async renderPage() {
            if (!this.pdfDoc) return;

            this.isLoading = true;

            try {
                const page = await this.pdfDoc.getPage(this.pageNum);
                const viewport = page.getViewport({ scale: this.scale });

                const canvas = this.$refs.pdfCanvas;
                const context = canvas.getContext('2d');

                canvas.height = viewport.height;
                canvas.width = viewport.width;
                // Make canvas visible if it was hidden
                canvas.style.display = 'block';

                const renderContext = {
                    canvasContext: context,
                    viewport: viewport
                };

                await page.render(renderContext).promise;
                console.log(`Page ${this.pageNum} rendered.`);
            } catch (err) {
                console.error(`Error rendering page ${this.pageNum}:`, err);
                this.error = `Failed to render page ${this.pageNum}.`;
            } finally {
                this.isLoading = false;
            }
        },

        // --- Navigation ---
        nextPage() {
            if (this.pageNum >= this.pageCount) return;
            this.pageNum++;
            this.renderPage();
        },

        prevPage() {
            if (this.pageNum <= 1) return;
            this.pageNum--;
            this.renderPage();
        },

        // --- Cleanup and Close ---
        close() {
            console.log('Closing PDF reader.');
            this.show = false;
            this.error = null;
            this.isLoading = false;

            // Cleanup PDF.js resources
            if (this.pdfDoc) {
                this.pdfDoc.destroy();
                this.pdfDoc = null;
            }

            // Notify Livewire if needed
            if (window.Livewire) {
                Livewire.dispatch('pdfReaderClosed');
            }
        }
    }));
});

// Optional: Global test function for debugging without Livewire
window.testPdfReader = (url) => {
    window.dispatchEvent(new CustomEvent('alpine:init', {
        detail: {
            pdfUrl: url,
            title: 'Test PDF'
        }
    }));
    // A bit of a hack for testing, direct dispatch is better.
    // A better way is to get the component and call open()
    const readerEl = document.querySelector('[x-data^="pdfReader"]');
    if (readerEl && readerEl.__x) {
        readerEl.__x.dataStack[0].open({ pdfUrl: url, title: 'Test PDF' });
    }
};
