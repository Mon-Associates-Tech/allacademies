(function() {
    'use strict';

    const AnnotationSync = {
        bookId: null,
        annotationsApiUrl: null,
        csrfToken: null,
        pdfApp: null,
        pdfDocument: null,
        annotations: new Map(), // Map annotation ID to data
        syncQueue: [],
        isSyncing: false,

        init(bookId, annotationsApiUrl, csrfToken) {
            this.bookId = bookId;
            this.annotationsApiUrl = annotationsApiUrl;
            this.csrfToken = csrfToken;

            // Wait for PDF.js to be ready
            const checkReady = setInterval(() => {
                if (window.PDFViewerApplication && window.PDFViewerApplication.initialized) {
                    clearInterval(checkReady);
                    this.onViewerReady();
                }
            }, 100);
        },

        async onViewerReady() {
            this.pdfApp = window.PDFViewerApplication;
            this.pdfDocument = this.pdfApp.pdfDocument;

            // Load existing annotations from Laravel
            await this.loadAnnotations();

            // Inject annotations into PDF pages
            await this.injectAnnotations();

            // Set up event listeners for annotation changes
            this.setupEventListeners();

            console.log('Annotation sync initialized');
        },

        async loadAnnotations() {
            try {
                const response = await fetch(this.annotationsApiUrl, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                });

                const data = await response.json();
                
                this.annotations.clear();
                data.annotations.forEach(ann => {
                    this.annotations.set(ann.id, ann);
                });

                console.log(`Loaded ${this.annotations.size} annotations`);
            } catch (error) {
                console.error('Failed to load annotations:', error);
            }
        },

        async injectAnnotations() {
            // PDF.js annotations are page-specific
            for (let pageNum = 1; pageNum <= this.pdfDocument.numPages; pageNum++) {
                const pageAnnotations = Array.from(this.annotations.values())
                    .filter(ann => ann.page === pageNum);

                if (pageAnnotations.length === 0) continue;

                try {
                    const page = await this.pdfDocument.getPage(pageNum);
                    
                    // Convert our annotations to PDF.js annotation format
                    const pdfAnnotations = pageAnnotations.map(ann => this.toPdfJsAnnotation(ann));
                    
                    // Note: PDF.js doesn't have a direct API to inject annotations
                    // We'll use the annotation layer overlay approach instead
                    this.renderAnnotationOverlay(pageNum, pageAnnotations);
                    
                } catch (error) {
                    console.error(`Failed to inject annotations for page ${pageNum}:`, error);
                }
            }
        },

        toPdfJsAnnotation(annotation) {
            const page = this.pdfDocument.getPage(annotation.page);
            
            // Convert percentage coordinates to PDF coordinates
            // This is a simplified conversion - actual implementation depends on page dimensions
            return {
                id: `ann-${annotation.id}`,
                type: annotation.type || 'Highlight',
                rect: this.pctToRect(annotation, page),
                color: this.hexToRgb(annotation.color || '#f59e0b'),
                contents: annotation.contents || '',
                creationDate: annotation.created_at,
                modificationDate: annotation.updated_at,
            };
        },

        pctToRect(annotation, page) {
            // This would need actual page dimensions
            // Simplified version - you'd need to get viewport and convert properly
            const viewport = page.getViewport({ scale: 1.0 });
            const width = viewport.width;
            const height = viewport.height;

            const x = (annotation.x_pct / 100) * width;
            const y = (annotation.y_pct / 100) * height;
            const w = (annotation.width_pct / 100) * width;
            const h = (annotation.height_pct / 100) * height;

            // PDF coordinates are bottom-left origin
            return [x, height - y - h, x + w, height - y];
        },

        hexToRgb(hex) {
            const result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex);
            return result ? {
                r: parseInt(result[1], 16) / 255,
                g: parseInt(result[2], 16) / 255,
                b: parseInt(result[3], 16) / 255,
            } : { r: 1, g: 0.62, b: 0.04 }; // Default amber
        },

        renderAnnotationOverlay(pageNum, annotations) {
            // Since PDF.js doesn't easily allow injecting annotations into the document,
            // we render them as overlays on the annotation layer
            const viewer = this.pdfApp.pdfViewer;
            const pageView = viewer.getPageView(pageNum - 1);
            
            if (!pageView || !pageView.div) return;

            // Create or get annotation overlay container
            let overlay = pageView.div.querySelector('.custom-annotation-overlay');
            if (!overlay) {
                overlay = document.createElement('div');
                overlay.className = 'custom-annotation-overlay';
                overlay.style.cssText = 'position: absolute; top: 0; left: 0; width: 100%; height: 100%; pointer-events: none; z-index: 10;';
                pageView.div.appendChild(overlay);
            }

            // Render each annotation
            annotations.forEach(ann => {
                const el = document.createElement('div');
                el.className = 'custom-annotation';
                el.dataset.annotationId = ann.id;
                el.style.cssText = `
                    position: absolute;
                    left: ${ann.x_pct}%;
                    top: ${ann.y_pct}%;
                    width: ${ann.width_pct}%;
                    height: ${ann.height_pct}%;
                    background-color: ${ann.color}40;
                    border: 2px solid ${ann.color};
                    cursor: pointer;
                    pointer-events: auto;
                `;

                el.addEventListener('click', (e) => {
                    e.stopPropagation();
                    this.showAnnotationDetails(ann);
                });

                overlay.appendChild(el);
            });
        },

        setupEventListeners() {
            // Listen for PDF.js annotation events
            if (this.pdfApp.eventBus) {
                // When user creates an annotation in PDF.js
                this.pdfApp.eventBus.on('annotationeditorlayer', (event) => {
                    console.log('Annotation editor event:', event);
                    // Handle annotation creation/editing
                });

                // Listen for page changes to re-render overlays
                this.pdfApp.eventBus.on('pagechanging', () => {
                    // Re-render annotations for visible pages
                    this.refreshVisibleAnnotations();
                });
            }

            // Intercept annotation saves from PDF.js UI
            this.interceptAnnotationSaves();
        },

        interceptAnnotationSaves() {
            // Override PDF.js annotation save functionality
            const originalSave = this.pdfApp.save;
            
            this.pdfApp.save = async () => {
                console.log('Intercepting PDF.js save...');
                
                // Extract annotations from PDF.js
                const annotations = await this.extractAnnotations();
                
                // Sync to Laravel
                await this.syncAnnotations(annotations);
                
                // Call original save if needed
                if (originalSave) {
                    return originalSave.call(this.pdfApp);
                }
            };
        },

        async extractAnnotations() {
            const annotations = [];
            
            for (let pageNum = 1; pageNum <= this.pdfDocument.numPages; pageNum++) {
                try {
                    const page = await this.pdfDocument.getPage(pageNum);
                    const pageAnnotations = await page.getAnnotations();
                    
                    pageAnnotations.forEach(ann => {
                        if (ann.id && ann.id.startsWith('ann-')) {
                            // This is one of our custom annotations
                            const id = parseInt(ann.id.replace('ann-', ''));
                            const existing = this.annotations.get(id);
                            
                            if (existing) {
                                annotations.push({
                                    ...existing,
                                    // Update with any changes from PDF.js
                                });
                            }
                        } else {
                            // New annotation created in PDF.js
                            annotations.push(this.fromPdfJsAnnotation(ann, pageNum));
                        }
                    });
                } catch (error) {
                    console.error(`Failed to extract annotations from page ${pageNum}:`, error);
                }
            }
            
            return annotations;
        },

        fromPdfJsAnnotation(pdfAnnotation, pageNum) {
            // Convert PDF.js annotation back to our format
            const rect = pdfAnnotation.rect;
            const page = this.pdfDocument.getPage(pageNum);
            const viewport = page.getViewport({ scale: 1.0 });
            
            const width = viewport.width;
            const height = viewport.height;

            const x = rect[0];
            const y = height - rect[3];
            const w = rect[2] - rect[0];
            const h = rect[3] - rect[1];

            return {
                page: pageNum,
                x_pct: (x / width) * 100,
                y_pct: (y / height) * 100,
                width_pct: (w / width) * 100,
                height_pct: (h / height) * 100,
                color: this.rgbToHex(pdfAnnotation.color),
                contents: pdfAnnotation.contents || '',
                type: pdfAnnotation.subtype || 'Highlight',
            };
        },

        rgbToHex(color) {
            if (!color) return '#f59e0b';
            const r = Math.round(color.r * 255);
            const g = Math.round(color.g * 255);
            const b = Math.round(color.b * 255);
            return `#${r.toString(16).padStart(2, '0')}${g.toString(16).padStart(2, '0')}${b.toString(16).padStart(2, '0')}`;
        },

        async syncAnnotations(annotations) {
            // Compare with existing and determine creates/updates/deletes
            const existingIds = new Set(this.annotations.keys());
            const newIds = new Set(annotations.filter(a => a.id).map(a => a.id));

            // Find deletions
            for (const id of existingIds) {
                if (!newIds.has(id)) {
                    await this.deleteAnnotation(id);
                }
            }

            // Find creates and updates
            for (const ann of annotations) {
                if (ann.id && existingIds.has(ann.id)) {
                    await this.updateAnnotation(ann);
                } else if (!ann.id) {
                    await this.createAnnotation(ann);
                }
            }

            // Reload annotations
            await this.loadAnnotations();
        },

        async createAnnotation(annotation) {
            try {
                const response = await fetch(this.annotationsApiUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': this.csrfToken,
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: JSON.stringify(annotation),
                });

                const data = await response.json();
                
                if (data.success) {
                    this.annotations.set(data.annotation.id, {
                        ...annotation,
                        id: data.annotation.id,
                    });
                    
                    // Re-render overlay
                    this.refreshVisibleAnnotations();
                }
            } catch (error) {
                console.error('Failed to create annotation:', error);
            }
        },

        async updateAnnotation(annotation) {
            try {
                await fetch(`${this.annotationsApiUrl}/${annotation.id}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': this.csrfToken,
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: JSON.stringify({
                        contents: annotation.contents,
                        color: annotation.color,
                    }),
                });

                this.annotations.set(annotation.id, annotation);
            } catch (error) {
                console.error('Failed to update annotation:', error);
            }
        },

        async deleteAnnotation(id) {
            try {
                await fetch(`${this.annotationsApiUrl}/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': this.csrfToken,
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                });

                this.annotations.delete(id);
                this.refreshVisibleAnnotations();
            } catch (error) {
                console.error('Failed to delete annotation:', error);
            }
        },

        refreshVisibleAnnotations() {
            // Re-render annotations for currently visible pages
            const viewer = this.pdfApp.pdfViewer;
            const visiblePages = viewer.getVisiblePages();
            
            visiblePages.views.forEach(view => {
                const pageNum = view.id;
                const pageAnnotations = Array.from(this.annotations.values())
                    .filter(ann => ann.page === pageNum);
                
                this.renderAnnotationOverlay(pageNum, pageAnnotations);
            });
        },

        showAnnotationDetails(annotation) {
            // Show a modal or panel with annotation details and comments
            console.log('Show details for annotation:', annotation);
            
            // You can integrate this with your existing comment system
            // or create a new UI component
        },
    };

    // Expose globally so the parent page can initialize it
    window.AnnotationSync = AnnotationSync;
})();