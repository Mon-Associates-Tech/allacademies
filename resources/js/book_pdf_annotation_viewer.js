import * as pdfjsLib from 'pdfjs-dist/legacy/build/pdf.mjs';
import pdfjsWorker from 'pdfjs-dist/legacy/build/pdf.worker.mjs?url';

pdfjsLib.GlobalWorkerOptions.workerSrc = pdfjsWorker;

window.bookPdfViewer = function bookPdfViewer(config) {
    const internal = {
        pdfDocument: null,
        progressSaveTimeout: null,
    };

    return {
        currentPage: 1,
        totalPages: 0,
        zoomLevel: 1.25,
        annotationMode: false,
        isDrawing: false,
        drawingStart: null,
        tempBounds: null,
        showCommentsPanel: false,
        selectedAnnotationId: null,
        annotations: Array.isArray(config.initialAnnotations) ? config.initialAnnotations : [],

        init() {
            this.registerLivewireEvents();
            this.loadPdf();
        },

        async loadPdf() {
            try {
                await this.loadPdfDocument(false);
            } catch (error) {
                if (error instanceof TypeError && String(error.message || '').includes('private field')) {
                    console.warn('Retrying PDF load using compatibility mode (no worker)');
                    try {
                        await this.loadPdfDocument(true);
                        return;
                    } catch (fallbackError) {
                        console.error('Failed to load PDF in compatibility mode', fallbackError);
                    }
                }

                console.error('Failed to load PDF', error);
            }
        },

        async loadPdfDocument(disableWorker) {
            const loadingTask = pdfjsLib.getDocument({
                url: config.streamUrl,
                withCredentials: true,
                disableWorker,
            });

            internal.pdfDocument = await loadingTask.promise;
            this.totalPages = internal.pdfDocument.numPages;
            this.currentPage = 1;
            await this.renderPage();
            this.scheduleProgressSave();
        },

        async renderPage() {
            if (!internal.pdfDocument) {
                return;
            }

            const page = await internal.pdfDocument.getPage(this.currentPage);
            const viewport = page.getViewport({ scale: this.zoomLevel });

            const canvas = this.$refs.pdfCanvas;
            const context = canvas.getContext('2d');
            canvas.width = viewport.width;
            canvas.height = viewport.height;
            canvas.style.width = `${viewport.width}px`;
            canvas.style.height = `${viewport.height}px`;

            const wrapper = this.$refs.pageWrapper;
            wrapper.style.width = `${viewport.width}px`;
            wrapper.style.height = `${viewport.height}px`;

            await page.render({ canvasContext: context, viewport }).promise;
        },

        async nextPage() {
            if (this.currentPage >= this.totalPages) {
                return;
            }
            this.currentPage += 1;
            await this.renderPage();
            this.scheduleProgressSave();
        },

        async previousPage() {
            if (this.currentPage <= 1) {
                return;
            }
            this.currentPage -= 1;
            await this.renderPage();
            this.scheduleProgressSave();
        },

        async zoomIn() {
            this.zoomLevel = Math.min(3, this.zoomLevel + 0.1);
            await this.renderPage();
        },

        async zoomOut() {
            this.zoomLevel = Math.max(0.5, this.zoomLevel - 0.1);
            await this.renderPage();
        },

        toggleAnnotationMode() {
            this.annotationMode = !this.annotationMode;
            this.cancelDraw();
        },

        startDraw(event) {
            if (!this.annotationMode) {
                return;
            }

            const point = this.pointerPosition(event);
            this.isDrawing = true;
            this.drawingStart = point;
            this.tempBounds = {
                x: point.x,
                y: point.y,
                width: 0,
                height: 0,
            };
        },

        moveDraw(event) {
            if (!this.annotationMode || !this.isDrawing || !this.drawingStart) {
                return;
            }

            const point = this.pointerPosition(event);
            const x = Math.min(this.drawingStart.x, point.x);
            const y = Math.min(this.drawingStart.y, point.y);
            const width = Math.abs(point.x - this.drawingStart.x);
            const height = Math.abs(point.y - this.drawingStart.y);

            this.tempBounds = { x, y, width, height };
        },

        endDraw(event) {
            if (!this.annotationMode || !this.isDrawing || !this.tempBounds) {
                return;
            }

            this.moveDraw(event);

            const wrapper = this.$refs.pageWrapper;
            const minSizePx = 8;
            if (this.tempBounds.width < minSizePx || this.tempBounds.height < minSizePx) {
                this.cancelDraw();
                return;
            }

            const payload = {
                bookId: config.bookId,
                pageNumber: this.currentPage,
                xPct: (this.tempBounds.x / wrapper.clientWidth) * 100,
                yPct: (this.tempBounds.y / wrapper.clientHeight) * 100,
                widthPct: (this.tempBounds.width / wrapper.clientWidth) * 100,
                heightPct: (this.tempBounds.height / wrapper.clientHeight) * 100,
                color: '#f59e0b',
            };

            if (window.Livewire) {
                window.Livewire.dispatch('book-annotation-create', payload);
            }

            this.cancelDraw();
        },

        cancelDraw() {
            this.isDrawing = false;
            this.drawingStart = null;
            this.tempBounds = null;
        },

        tempBoundsStyle() {
            if (!this.tempBounds) {
                return '';
            }

            return `left:${this.tempBounds.x}px;top:${this.tempBounds.y}px;width:${this.tempBounds.width}px;height:${this.tempBounds.height}px;`;
        },

        pageAnnotations() {
            return this.annotations.filter((annotation) => Number(annotation.page_number) === Number(this.currentPage));
        },

        annotationStyle(annotation) {
            return `left:${annotation.x_pct}%;top:${annotation.y_pct}%;width:${annotation.width_pct}%;height:${annotation.height_pct}%;`;
        },

        openAnnotation(annotation) {
            this.selectedAnnotationId = annotation.id;
            this.showCommentsPanel = true;

            if (window.Livewire) {
                window.Livewire.dispatch('book-annotation-selected', {
                    annotationId: annotation.id,
                });
            }
        },

        registerLivewireEvents() {
            window.addEventListener('book-annotations-updated', (event) => {
                const detail = Array.isArray(event.detail) ? (event.detail[0] ?? {}) : (event.detail ?? {});
                if (Number(detail.bookId) !== Number(config.bookId)) {
                    return;
                }

                this.annotations = Array.isArray(detail.annotations) ? detail.annotations : [];
            });
        },

        scheduleProgressSave() {
            clearTimeout(internal.progressSaveTimeout);
            internal.progressSaveTimeout = setTimeout(() => {
                this.persistProgress();
            }, 1200);
        },

        async persistProgress() {
            if (!config.progressUrl || this.totalPages <= 0) {
                return;
            }

            const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            if (!token) {
                return;
            }

            try {
                await fetch(config.progressUrl, {
                    method: 'POST',
                    credentials: 'same-origin',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': token,
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: JSON.stringify({
                        book_id: config.bookId,
                        current_page: this.currentPage,
                        total_pages: this.totalPages,
                    }),
                });
            } catch (error) {
                console.error('Failed to persist reading progress', error);
            }
        },

        pointerPosition(event) {
            const wrapperRect = this.$refs.pageWrapper.getBoundingClientRect();
            const point = event.touches ? event.touches[0] : event;
            const x = Math.max(0, Math.min(point.clientX - wrapperRect.left, wrapperRect.width));
            const y = Math.max(0, Math.min(point.clientY - wrapperRect.top, wrapperRect.height));

            return { x, y };
        },
    };
};
