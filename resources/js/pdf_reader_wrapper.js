
import { PDFReader } from './pdf_reader.js';

export class PDFReaderWrapper {
    constructor() {
        this.pdfReader = null;
        this.isInitialized = false;
    }

    initialize(config) {
        try {
            const payload = Array.isArray(config) ? (config[0] ?? {}) : (config ?? {});
            // Destroy existing reader if any

            console.log('about to instantiate::', payload)
            // Create new PDF reader instance
            this.pdfReader = new PDFReader({
                container: '#pdf-reader-container',
                pdfUrl: payload.pdfUrl,
                initialPage: payload.currentPage || 1,
                bookId: payload.bookId,
                book: payload.book,
                onPageChange: this.handlePageChange.bind(this),
                onProgressUpdate: this.handleProgressUpdate.bind(this),
                onError: this.handleError.bind(this),
                onClose: this.handleClose.bind(this)
            });

            this.isInitialized = true;
            console.log('PDF Reader initialized successfully', this.pdfReader);

        } catch (error) {
            console.error('Failed to initialize PDF Reader:', error);
            this.handleError('Initialization failed', error);
        }
    }

    destroy() {
        if (this.pdfReader) {
            try {
                this.pdfReader.close();
            } catch (error) {
                console.warn('Error while closing PDF reader:', error);
            }
            this.pdfReader = null;
        }
        this.isInitialized = false;
    }

    handlePageChange(currentPage, totalPages) {
        // Dispatch Livewire event to update component state
        if (window.Livewire) {
            window.Livewire.dispatch('updatePageProgress', {
                currentPage,
                totalPages,
                progressPercentage: Math.round((currentPage / totalPages) * 100)
            });
        }
    }

    handleProgressUpdate(currentPage, totalPages, progressPercentage) {
        // This can be used for additional progress tracking if needed
        console.log(`Reading progress: ${progressPercentage}% (Page ${currentPage} of ${totalPages})`);

        // Backup sync path: some navigation patterns update progress without triggering onPageChange consistently.
        if (window.Livewire) {
            window.Livewire.dispatch('updatePageProgress', {
                currentPage,
                totalPages,
                progressPercentage
            });
        }
    }

    handleError(message, error) {
        console.error('PDF Reader Error:', message, error);

        // Show user-friendly error message
        if (window.Livewire) {
            window.Livewire.dispatch('show-error', {
                message: `PDF Reader Error: ${message}`
            });
        }

        // Optionally close the reader on critical errors
        if (message.includes('Failed to load') || message.includes('Initialization failed')) {
            setTimeout(() => {
                if (window.Livewire) {
                    window.Livewire.dispatch('closePDFReader');
                }
            }, 3000);
        }
    }

    handleClose() {
        // Dispatch Livewire event to close the reader
        if (window.Livewire) {
            window.Livewire.dispatch('closePDFReader');
        }
    }

    // Public API methods for external control
    goToPage(pageNumber) {
        if (this.pdfReader && this.isInitialized) {
            this.pdfReader.goToPage(pageNumber);
        }
    }

    nextPage() {
        if (this.pdfReader && this.isInitialized) {
            this.pdfReader.nextPage();
        }
    }

    previousPage() {
        if (this.pdfReader && this.isInitialized) {
            this.pdfReader.previousPage();
        }
    }

    zoomIn() {
        if (this.pdfReader && this.isInitialized) {
            this.pdfReader.zoomIn();
        }
    }

    zoomOut() {
        if (this.pdfReader && this.isInitialized) {
            this.pdfReader.zoomOut();
        }
    }

    getCurrentPage() {
        return this.pdfReader ? this.pdfReader.getCurrentPage() : 0;
    }

    getTotalPages() {
        return this.pdfReader ? this.pdfReader.getTotalPages() : 0;
    }

    getProgress() {
        return this.pdfReader ? this.pdfReader.getProgress() : 0;
    }
}

window.PDFReaderWrapper = PDFReaderWrapper;
