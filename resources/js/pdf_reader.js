import * as pdfjsLib from 'pdfjs-dist/build/pdf.min';
import pdfjsWorker from 'pdfjs-dist/build/pdf.worker.min?url';

export class PDFReader {
    constructor(config) {
        this.config = {
            container: null,
            pdfUrl: null,
            initialPage: 1,
            bookId: null,
            book: null,
            onPageChange: null,
            onProgressUpdate: null,
            onError: null,
            continuousMode: true,
            maxConcurrentRenders: 3,
            showTableOfContents: true, // New option
            tableOfContents: null, // TOC data
            ...config
        };

        this.pdfDocument = null;
        this.currentPage = this.config.initialPage;
        this.totalPages = 0;
        this.scale = 1.2;
        this.viewerContainer = null;
        this.tocSidebar = null;
        this.tocVisible = false;

        // Render management
        this.renderTasks = new Map();
        this.renderQueue = [];
        this.activeRenders = 0;
        this.pageElements = new Map();
        this.renderedPages = new Set();

        // State management
        this.progressSaveTimeout = null;
        this.scrollTimeout = null;
        this.intersectionObserver = null;
        this.isDestroyed = false;

        this.init();
    }

    async init() {
        try {
            if (typeof pdfjsLib !== 'undefined') {
                pdfjsLib.GlobalWorkerOptions.workerSrc = pdfjsWorker;
            }

            await this.createUI();
            await this.loadPDF();
            await this.loadTableOfContents();
        } catch (error) {
            this.handleError('Failed to initialize PDF reader', error);
        }
    }


    getPageFromDest(dest) {
        // This is a simplified version - you might need to enhance this
        if (Array.isArray(dest) && dest.length > 0) {
            return dest[0] + 1; // PDF.js uses 0-based page numbers
        }
        return 1;
    }

    createUI() {
        const container = document.querySelector(this.config.container);
        if (!container) {
            throw new Error('Container element not found');
        }

        container.innerHTML = `
            <div class="pdf-reader bg-gray-900 text-white rounded-lg shadow-lg flex flex-col h-full">
                <!-- Toolbar -->
                <div class="pdf-toolbar flex items-center justify-between bg-gray-800 p-3 rounded-t-lg">
                    <div class="flex items-center space-x-4">
                        <span>${this.config.book.title}</span>
                        ${this.config.showTableOfContents ? `
                        <button id="toggle-toc" class="px-3 py-1 bg-purple-600 rounded-md hover:bg-purple-700 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"></path>
                            </svg>
                        </button>
                        ` : ''}
                    </div>

                    <!-- Page Navigation -->
                    <div class="flex items-center space-x-4">
                        <button id="prev-page" class="px-2 py-2 bg-blue-600 rounded-md hover:bg-blue-700 disabled:bg-gray-500 disabled:cursor-not-allowed transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                            </svg>
                        </button>
                        <div class="flex items-center space-x-2">
                            <input type="number" id="page-input" class="w-16 px-3 py-2 text-center bg-gray-800 text-white rounded-lg border border-gray-600 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 focus:bg-gray-700 outline-none transition-all duration-200 hover:border-gray-500 shadow-sm font-medium text-sm" min="1" value="${this.currentPage}" onwheel="this.blur()">
                            <span>of</span>
                            <span id="total-pages">--</span>
                        </div>
                        <button id="next-page" class="px-2 py-2 bg-blue-600 rounded-md hover:bg-blue-700 disabled:bg-gray-500 disabled:cursor-not-allowed transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </button>
                    </div>

                    <!-- View Mode Toggle -->
                    <div class="flex items-center space-x-2">
                        <button id="toggle-continuous" class="px-3 py-1 bg-green-600 rounded-md hover:bg-green-700 transition-colors">
                            <span class="continuous-text">Continuous</span>
                            <span class="single-text hidden">Single Page</span>
                        </button>
                    </div>

                    <!-- Zoom Controls -->
                    <div class="flex items-center space-x-4">
                        <button id="zoom-out" class="px-3 py-1 bg-gray-700 rounded-md hover:bg-gray-600 disabled:opacity-50">-</button>
                        <span id="zoom-level">${Math.round(this.scale * 100)}%</span>
                        <button id="zoom-in" class="px-3 py-1 bg-gray-700 rounded-md hover:bg-gray-600 disabled:opacity-50">+</button>
                        <button id="fit-width" class="px-3 py-1 bg-gray-700 rounded-md hover:bg-gray-600">Fit Width</button>
                    </div>

                    <!-- Actions -->
                    <div class="flex items-center space-x-2">
                        <button id="fullscreen" class="px-3 py-2 bg-gray-700 hover:bg-gray-600 rounded-md transition-colors" title="Toggle Fullscreen">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h4a1 1 0 010 2H6.414l2.293 2.293a1 1 0 11-1.414 1.414L5 6.414V8a1 1 0 01-2 0V4zm9 1a1 1 0 010-2h4a1 1 0 011 1v4a1 1 0 01-2 0V6.414l-2.293 2.293a1 1 0 11-1.414-1.414L13.586 5H12zm-9 7a1 1 0 012 0v1.586l2.293-2.293a1 1 0 111.414 1.414L6.414 15H8a1 1 0 010 2H4a1 1 0 01-1-1v-4zm13-1a1 1 0 011 1v4a1 1 0 01-1 1h-4a1 1 0 010-2h1.586l-2.293-2.293a1 1 0 111.414-1.414L15 13.586V12a1 1 0 011-1z" clip-rule="evenodd"/>
                            </svg>
                        </button>
                        <button id="close-reader" class="px-2 py-2 bg-red-600 rounded-md hover:bg-red-700">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Main Content Area -->
                <div class="flex flex-1 overflow-hidden">
                    <!-- Table of Contents Sidebar -->
                    ${this.config.showTableOfContents ? `
                    <div id="toc-sidebar" class="hidden w-80 bg-gray-800 border-r border-gray-700 flex flex-col">
                        <!-- TOC Header -->
                        <div class="p-4 border-b border-gray-700">
                            <div class="flex items-center justify-between">
                                <h3 class="text-lg font-semibold text-white">Table of Contents</h3>
                                <button id="close-toc" class="text-gray-400 hover:text-white">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            </div>

                            <!-- TOC Search -->
                            <div class="mt-3 relative">
                                <input type="text" id="toc-search" placeholder="Search chapters..."
                                       class="w-full px-3 py-2 bg-gray-700 text-white rounded-lg border border-gray-600 focus:border-blue-500 focus:outline-none text-sm">
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                    <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                    </svg>
                                </div>
                            </div>
                        </div>

                        <!-- TOC Content -->
                        <div id="toc-content" class="flex-1 overflow-y-auto p-4">
                            <div class="text-gray-400 text-center py-8">
                                <svg class="w-8 h-8 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                <p>Loading table of contents...</p>
                            </div>
                        </div>

                        <!-- TOC Actions -->
                        <div class="p-4 border-t border-gray-700 space-y-2">
                            <button id="expand-all-toc" class="w-full px-3 py-2 text-sm bg-gray-700 hover:bg-gray-600 text-white rounded-lg transition-colors">
                                Expand All
                            </button>
                            <button id="collapse-all-toc" class="w-full px-3 py-2 text-sm bg-gray-700 hover:bg-gray-600 text-white rounded-lg transition-colors">
                                Collapse All
                            </button>
                        </div>
                    </div>
                    ` : ''}

                    <!-- PDF Viewer Area -->
                    <div class="pdf-viewer-area flex-1 overflow-auto bg-gray-200 relative" id="viewer-container">
                        <div id="loading-indicator" class="absolute inset-0 flex items-center justify-center bg-gray-200 bg-opacity-75 z-10">
                            <div class="text-gray-800 font-bold text-xl">Loading PDF...</div>
                        </div>

                        <!-- Single Page Mode Container -->
                        <div id="single-page-container" class="pdf-container flex justify-center p-4 hidden">
                            <canvas id="pdf-canvas" class="rounded-md shadow-lg max-w-full"></canvas>
                        </div>

                        <!-- Continuous Mode Container -->
                        <div id="continuous-container" class="pdf-pages-container">
                            <!-- Pages will be dynamically inserted here -->
                        </div>
                    </div>
                </div>

                <!-- Progress Bar -->
                <div class="pdf-progress hidden p-2 bg-gray-800 rounded-b-lg">
                    <div class="w-full bg-gray-700 rounded-full h-2">
                        <div id="progress-bar" class="bg-green-500 h-2 rounded-full transition-all duration-300" style="width: 0%"></div>
                    </div>
                    <p class="text-center text-sm mt-2">
                        Reading Progress: <span id="progress-text">0%</span>
                    </p>
                </div>
            </div>
        `;

        this.viewerContainer = document.getElementById('viewer-container');
        this.tocSidebar = document.getElementById('toc-sidebar');
        this.attachEventListeners();
    }

    attachEventListeners() {
        // Original event listeners
        document.getElementById('prev-page').addEventListener('click', () => this.previousPage());
        document.getElementById('next-page').addEventListener('click', () => this.nextPage());
        document.getElementById('page-input').addEventListener('change', (e) => this.goToPage(parseInt(e.target.value)));
        document.getElementById('page-input').addEventListener('keypress', (e) => {
            if (e.key === 'Enter') {
                this.goToPage(parseInt(e.target.value));
            }
        });

        document.getElementById('toggle-continuous').addEventListener('click', () => this.toggleViewMode());
        document.getElementById('zoom-in').addEventListener('click', () => this.zoomIn());
        document.getElementById('zoom-out').addEventListener('click', () => this.zoomOut());
        document.getElementById('fit-width').addEventListener('click', () => this.fitToWidth());
        document.getElementById('fullscreen').addEventListener('click', () => this.toggleFullscreen());
        document.getElementById('close-reader').addEventListener('click', () => this.close());

        // New TOC event listeners
        if (this.config.showTableOfContents) {
            const toggleTocBtn = document.getElementById('toggle-toc');
            const closeTocBtn = document.getElementById('close-toc');
            const tocSearch = document.getElementById('toc-search');
            const expandAllToc = document.getElementById('expand-all-toc');
            const collapseAllToc = document.getElementById('collapse-all-toc');

            if (toggleTocBtn) {
                toggleTocBtn.addEventListener('click', () => this.toggleTableOfContents());
            }
            if (closeTocBtn) {
                closeTocBtn.addEventListener('click', () => this.hideTableOfContents());
            }
            if (tocSearch) {
                tocSearch.addEventListener('input', (e) => this.filterTableOfContents(e.target.value));
            }
            if (expandAllToc) {
                expandAllToc.addEventListener('click', () => this.expandAllTocItems());
            }
            if (collapseAllToc) {
                collapseAllToc.addEventListener('click', () => this.collapseAllTocItems());
            }
        }

        // Keyboard shortcuts
        document.addEventListener('keydown', (e) => this.handleKeyboard(e));

        // Scroll handling
        this.viewerContainer.addEventListener('scroll', () => this.handleScroll());

        // Auto-save progress
        window.addEventListener('beforeunload', () => this.saveProgress());
        setInterval(() => this.saveProgress(), 30000);
    }

    toggleTableOfContents() {
        if (this.tocVisible) {
            this.hideTableOfContents();
        } else {
            this.showTableOfContents();
        }
    }

    showTableOfContents() {
        if (this.tocSidebar) {
            this.tocSidebar.classList.remove('hidden');
            this.tocVisible = true;
        }
    }

    hideTableOfContents() {
        if (this.tocSidebar) {
            this.tocSidebar.classList.add('hidden');
            this.tocVisible = false;
        }
    }

    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }


    attachTocItemListeners() {
        // Remove the global pdfReader reference approach
        const tocContent = document.getElementById('toc-content');
        if (!tocContent) return;

        // Use event delegation for better performance and reliability
        tocContent.addEventListener('click', (e) => {
            const tocToggle = e.target.closest('.toc-toggle');
            const tocItemContent = e.target.closest('.toc-item-content');

            if (tocToggle) {
                e.preventDefault();
                e.stopPropagation();
                this.handleTocToggle(tocToggle);
            } else if (tocItemContent) {
                e.preventDefault();
                const tocItem = tocItemContent.closest('.toc-item');
                const pageNum = tocItem.dataset.page;
                const title = tocItem.dataset.title;

                if (pageNum && pageNum !== 'null' && pageNum !== 'undefined') {
                    this.goToTocItem(pageNum, title);
                }
            }
        });
    }

    handleTocToggle(toggleButton) {
        const tocItem = toggleButton.closest('.toc-item');
        const children = tocItem.querySelector('.toc-children');
        const icon = toggleButton.querySelector('svg');

        if (children) {
            if (children.classList.contains('hidden')) {
                children.classList.remove('hidden');
                icon.classList.add('rotate-90');
            } else {
                children.classList.add('hidden');
                icon.classList.remove('rotate-90');
            }
        }
    }


    updateTableOfContents() {
        if (!this.config.showTableOfContents) return;

        const tocContent = document.getElementById('toc-content');
        if (!tocContent) return;

        let tocHtml = '';
        let tocData = this.config.tableOfContents;

        // Debug logging
        console.log('Updating TOC with data:', tocData);

        if (Array.isArray(tocData) && tocData.length > 0) {
            tocHtml = this.renderTocItems(tocData);
        } else if (tocData && typeof tocData === 'object') {
            // Handle case where TOC might be a single object
            tocHtml = this.renderTocItems([tocData]);
        }

        if (tocHtml) {
            tocContent.innerHTML = `<div class="space-y-1">${tocHtml}</div>`;
            this.attachTocItemListeners();
            console.log('TOC rendered successfully with', tocData.length, 'items');
        } else {
            tocContent.innerHTML = `
            <div class="text-gray-400 text-center py-8">
                <svg class="w-8 h-8 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <p>No table of contents available</p>
                <p class="text-xs mt-2">PDF outline could not be extracted</p>
            </div>
        `;
            console.log('No TOC data available');
        }
    }

    processPdfOutline(outline, level = 0) {
        return outline.map((item, index) => {
            let pageNum = null;

            // Handle PDF.js destination format
            if (item.dest) {
                if (Array.isArray(item.dest) && item.dest.length > 0) {
                    // PDF.js destinations are 0-based, convert to 1-based
                    if (typeof item.dest[0] === 'object' && item.dest[0].num !== undefined) {
                        // Handle reference objects
                        pageNum = item.dest[0].num;
                    } else if (typeof item.dest[0] === 'number') {
                        // Handle direct page numbers
                        pageNum = item.dest[0] + 1;
                    }
                }
            }

            // Fallback to index-based page if no destination found
            if (!pageNum) {
                pageNum = index + 1;
            }

            const tocItem = {
                title: item.title || `Chapter ${index + 1}`,
                page: pageNum,
                level: level,
                children: item.items && item.items.length > 0 ?
                    this.processPdfOutline(item.items, level + 1) : []
            };

            console.log('Processed PDF outline item:', tocItem);
            return tocItem;
        });
    }

    renderTocItems(items, level = 0) {
        return items.map((item, index) => {
            const indent = level * 16;
            const hasChildren = item.children && item.children.length > 0;

            // Extract page number - handle different possible formats
            let pageNum = null;
            if (item.page) {
                if (typeof item.page === 'number') {
                    pageNum = item.page;
                } else if (typeof item.page === 'string') {
                    // Extract number from string like "Page 15" or "15-20"
                    const match = item.page.match(/(\d+)/);
                    pageNum = match ? parseInt(match[1]) : null;
                }
            }

            // Try other possible properties
            if (!pageNum) {
                pageNum = item.start_page || item.page_number || item.pageNumber;
                if (typeof pageNum === 'string') {
                    const match = pageNum.match(/(\d+)/);
                    pageNum = match ? parseInt(match[1]) : null;
                }
            }

            // Final fallback
            if (!pageNum || pageNum < 1) {
                pageNum = 1;
            }

            console.log('Rendering TOC item:', {
                title: item.title,
                originalPage: item.page,
                extractedPage: pageNum
            });

            return `
            <div class="toc-item" data-level="${level}" data-page="${pageNum}" data-title="${this.escapeHtml(item.title)}">
                <div class="toc-item-content flex items-start py-2 px-2 hover:bg-gray-700 rounded cursor-pointer transition-colors"
                     style="padding-left: ${indent + 8}px">
                    ${hasChildren ? `
                        <button class="toc-toggle w-4 h-4 mt-0.5 mr-2 text-gray-400 hover:text-white flex-shrink-0">
                            <svg class="w-3 h-3 transform transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </button>
                    ` : `<div class="w-6 h-4 flex-shrink-0"></div>`}

                    <div class="flex-1 min-w-0">
                        <div class="text-sm text-white truncate">${this.escapeHtml(item.title)}</div>
                        <div class="text-xs text-gray-400">Page ${pageNum}</div>
                    </div>
                </div>

                ${hasChildren ? `
                    <div class="toc-children hidden">
                        ${this.renderTocItems(item.children, level + 1)}
                    </div>
                ` : ''}
            </div>
        `;
        }).join('');
    }

    goToTocItem(page, title) {
        console.log('=== TOC NAVIGATION ===');
        console.log('Original page parameter:', page);
        console.log('Title:', title);

        let pageNum;

        // Handle different page formats
        if (typeof page === 'number') {
            pageNum = page;
        } else if (typeof page === 'string') {
            // Extract first number from string
            const match = page.match(/(\d+)/);
            pageNum = match ? parseInt(match[1]) : null;
        } else {
            pageNum = parseInt(page);
        }

        console.log('Extracted page number:', pageNum);
        console.log('Total pages:', this.totalPages);
        console.log('Is valid page?', pageNum >= 1 && pageNum <= this.totalPages);

        if (pageNum && pageNum >= 1 && pageNum <= this.totalPages) {
            console.log(`Navigating to page ${pageNum}`);
            this.goToPage(pageNum);

            // Highlight the current TOC item
            this.highlightTocItem(title);

            // Auto-hide TOC after navigation on small screens
            setTimeout(() => {
                if (this.tocVisible && window.innerWidth < 1024) {
                    this.hideTableOfContents();
                }
            }, 1500);
        } else {
            console.error(`Invalid page number: ${pageNum} (should be between 1 and ${this.totalPages})`);
            alert(`Invalid page number: ${pageNum}. Please check the table of contents.`);
        }
    }

    async getActualPageNumber(dest) {
        if (!this.pdfDocument || !dest) return 1;

        try {
            if (Array.isArray(dest) && dest.length > 0) {
                const pageRef = dest[0];

                if (typeof pageRef === 'number') {
                    // Direct page index (0-based)
                    return pageRef + 1;
                } else if (pageRef && typeof pageRef === 'object' && pageRef.num !== undefined) {
                    // Page reference object - need to resolve it
                    const pageIndex = await this.pdfDocument.getPageIndex(pageRef);
                    return pageIndex + 1;
                }
            } else if (typeof dest === 'string') {
                // Named destination
                const resolvedDest = await this.pdfDocument.getDestination(dest);
                return this.getActualPageNumber(resolvedDest);
            }
        } catch (error) {
            console.warn('Failed to resolve page number from destination:', dest, error);
        }

        return 1; // Fallback
    }

    async loadTableOfContents() {
        console.log('Loading table of contents...');

        // First try to extract TOC from PDF
        try {
            if (this.pdfDocument) {
                const outline = await this.pdfDocument.getOutline();
                console.log('PDF outline extracted:', outline);

                if (outline && outline.length > 0) {
                    // Process the outline with proper page resolution
                    const processedOutline = [];
                    for (const item of outline) {
                        const pageNum = await this.getActualPageNumber(item.dest);
                        processedOutline.push({
                            title: item.title,
                            page: pageNum,
                            level: 0,
                            children: item.items ? await this.processChildItems(item.items, 1) : []
                        });
                    }

                    this.config.tableOfContents = processedOutline;
                    console.log('Processed PDF outline:', this.config.tableOfContents);
                }
            }
        } catch (error) {
            console.warn('Could not extract PDF outline:', error);
        }

        // If no PDF outline and book has TOC data, use that
        if (!this.config.tableOfContents && this.config.book?.formatted_table_of_contents) {
            console.log('Using book TOC data:', this.config.book.formatted_table_of_contents);
            this.config.tableOfContents = this.config.book.formatted_table_of_contents;
        }

        // Update TOC sidebar
        this.updateTableOfContents();
    }

    async processChildItems(items, level) {
        const processedItems = [];
        for (const item of items) {
            const pageNum = await this.getActualPageNumber(item.dest);
            processedItems.push({
                title: item.title,
                page: pageNum,
                level: level,
                children: item.items ? await this.processChildItems(item.items, level + 1) : []
            });
        }
        return processedItems;
    }

    debugTocData() {
        console.log('=== TOC DEBUG INFO ===');
        console.log('Book TOC data:', this.config.book?.formatted_table_of_contents);
        console.log('Processed TOC data:', this.config.tableOfContents);
        console.log('Total pages:', this.totalPages);

        if (this.config.tableOfContents) {
            this.config.tableOfContents.forEach((item, index) => {
                console.log(`TOC Item ${index}:`, {
                    title: item.title,
                    page: item.page,
                    type: typeof item.page
                });
            });
        }
        console.log('=== END TOC DEBUG ===');
    }

    async getPageFromNamedDest(destName) {
        if (!this.pdfDocument) return 1;

        try {
            const dest = await this.pdfDocument.getDestination(destName);
            if (dest && Array.isArray(dest) && dest.length > 0) {
                return dest[0] + 1; // Convert to 1-based indexing
            }
        } catch (error) {
            console.warn('Could not resolve named destination:', destName, error);
        }

        return 1; // Fallback to page 1
    }

    toggleTocItem(button) {
        const tocItem = button.closest('.toc-item');
        const children = tocItem.querySelector('.toc-children');
        const icon = button.querySelector('svg');

        if (children) {
            if (children.classList.contains('hidden')) {
                children.classList.remove('hidden');
                icon.classList.add('rotate-90');
            } else {
                children.classList.add('hidden');
                icon.classList.remove('rotate-90');
            }
        }
    }

    highlightTocItem(title) {
        // Remove previous highlights
        document.querySelectorAll('.toc-item-highlight').forEach(el => {
            el.classList.remove('toc-item-highlight', 'bg-blue-600');
        });

        // Add highlight to current item
        const tocItems = document.querySelectorAll('.toc-item > div');
        tocItems.forEach(item => {
            const titleEl = item.querySelector('.text-white');
            if (titleEl && titleEl.textContent.trim() === title.trim()) {
                item.classList.add('toc-item-highlight', 'bg-blue-600');
            }
        });
    }

    filterTableOfContents(searchTerm) {
        const tocItems = document.querySelectorAll('.toc-item');

        if (!searchTerm.trim()) {
            // Show all items
            tocItems.forEach(item => item.style.display = '');
            return;
        }

        const term = searchTerm.toLowerCase();

        tocItems.forEach(item => {
            const titleEl = item.querySelector('.text-white');
            const title = titleEl ? titleEl.textContent.toLowerCase() : '';

            if (title.includes(term)) {
                item.style.display = '';
                // Show parent items
                let parent = item.parentElement;
                while (parent && parent.classList.contains('toc-children')) {
                    parent.style.display = '';
                    parent.classList.remove('hidden');
                    parent = parent.parentElement?.parentElement;
                }
            } else {
                item.style.display = 'none';
            }
        });
    }

    expandAllTocItems() {
        document.querySelectorAll('.toc-children').forEach(children => {
            children.classList.remove('hidden');
        });
        document.querySelectorAll('.toc-toggle svg').forEach(icon => {
            icon.classList.add('rotate-90');
        });
    }

    collapseAllTocItems() {
        document.querySelectorAll('.toc-children').forEach(children => {
            children.classList.add('hidden');
        });
        document.querySelectorAll('.toc-toggle svg').forEach(icon => {
            icon.classList.remove('rotate-90');
        });
    }

    handleKeyboard(event) {
        if (event.target.tagName.toLowerCase() === 'input' || this.isDestroyed) return;

        switch (event.key) {
            case 'ArrowRight':
            case ' ':
                event.preventDefault();
                this.nextPage();
                break;
            case 'ArrowLeft':
                event.preventDefault();
                this.previousPage();
                break;
            case '+':
            case '=':
                event.preventDefault();
                this.zoomIn();
                break;
            case '-':
                event.preventDefault();
                this.zoomOut();
                break;
            case 'c':
            case 'C':
                event.preventDefault();
                this.toggleViewMode();
                break;
            case 't':
            case 'T':
                if (this.config.showTableOfContents) {
                    event.preventDefault();
                    this.toggleTableOfContents();
                }
                break;
            case 'f':
            case 'F11':
                event.preventDefault();
                this.toggleFullscreen();
                break;
            case 'Escape':
                if (document.fullscreenElement) {
                    document.exitFullscreen();
                } else if (this.tocVisible) {
                    this.hideTableOfContents();
                }
                break;
        }
    }

    // Rest of the methods remain the same...
    async loadPDF() {
        try {
            this.showLoading(true);

            const loadingTask = pdfjsLib.getDocument({
                url: this.config.pdfUrl,
                withCredentials: true,
            });

            this.pdfDocument = await loadingTask.promise;
            this.totalPages = this.pdfDocument.numPages;

            document.getElementById('total-pages').textContent = this.totalPages;

            if (this.config.continuousMode) {
                await this.initializeContinuousMode();
            } else {
                await this.initializeSinglePageMode();
            }

            this.updateProgress();
            this.showLoading(false);

        } catch (error) {
            this.handleError('Failed to load PDF', error);
        }
    }

    async initializeContinuousMode() {
        const container = document.getElementById('continuous-container');
        const singleContainer = document.getElementById('single-page-container');

        container.classList.remove('hidden');
        singleContainer.classList.add('hidden');

        // Clear existing pages if any
        container.innerHTML = '';
        this.pageElements.clear();
        this.renderedPages.clear();
        this.cancelAllRenderTasks();

        // Create page containers
        for (let pageNum = 1; pageNum <= this.totalPages; pageNum++) {
            const pageContainer = document.createElement('div');
            pageContainer.className = 'page-container flex justify-center mb-4 p-4';
            pageContainer.id = `page-container-${pageNum}`;
            pageContainer.setAttribute('data-page', pageNum);

            const canvas = document.createElement('canvas');
            canvas.className = 'page-canvas rounded-md shadow-lg max-w-full hidden';
            canvas.id = `page-canvas-${pageNum}`;

            const loadingDiv = document.createElement('div');
            loadingDiv.className = 'page-loading flex items-center justify-center h-96 bg-gray-300 rounded-md';
            loadingDiv.innerHTML = `<span class="text-gray-600">Loading page ${pageNum}...</span>`;

            pageContainer.appendChild(loadingDiv);
            pageContainer.appendChild(canvas);
            container.appendChild(pageContainer);

            this.pageElements.set(pageNum, {
                container: pageContainer,
                canvas: canvas,
                loading: loadingDiv,
                context: canvas.getContext('2d'),
                rendered: false
            });
        }

        // Set up intersection observer for lazy loading
        this.setupIntersectionObserver();

        // Start rendering pipeline
        this.processRenderQueue();

        // Scroll to current page
        this.scrollToPage(this.currentPage);
    }

    async initializeSinglePageMode() {
        const container = document.getElementById('continuous-container');
        const singleContainer = document.getElementById('single-page-container');

        container.classList.add('hidden');
        singleContainer.classList.remove('hidden');

        // Cancel all continuous mode renders
        this.cancelAllRenderTasks();

        this.canvas = document.getElementById('pdf-canvas');
        this.context = this.canvas.getContext('2d');

        await this.renderPage(this.currentPage);
    }

    setupIntersectionObserver() {
        if (this.intersectionObserver) {
            this.intersectionObserver.disconnect();
        }

        this.intersectionObserver = new IntersectionObserver(
            (entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting && !this.isDestroyed) {
                        const pageNum = parseInt(entry.target.getAttribute('data-page'));
                        this.queuePageRender(pageNum);

                        // Update current page based on visibility
                        if (entry.intersectionRatio > 0.5) {
                            this.updateCurrentPage(pageNum);
                        }
                    }
                });
            },
            {
                root: this.viewerContainer,
                rootMargin: '200px',
                threshold: [0.1, 0.5, 0.9]
            }
        );

        // Observe all page containers
        this.pageElements.forEach((elements, pageNum) => {
            this.intersectionObserver.observe(elements.container);
        });
    }

    queuePageRender(pageNum) {
        // Don't queue if already rendered or in queue
        if (this.renderedPages.has(pageNum) || this.renderQueue.includes(pageNum)) {
            return;
        }

        this.renderQueue.push(pageNum);
        this.processRenderQueue();
    }

    async processRenderQueue() {
        // Process render queue with concurrency limit
        while (this.renderQueue.length > 0 && this.activeRenders < this.config.maxConcurrentRenders && !this.isDestroyed) {
            const pageNum = this.renderQueue.shift();
            if (pageNum && !this.renderedPages.has(pageNum)) {
                this.renderPageLazy(pageNum);
            }
        }
    }

    async renderPageLazy(pageNum) {
        const pageElements = this.pageElements.get(pageNum);
        if (!pageElements || pageElements.rendered || this.renderedPages.has(pageNum) || this.isDestroyed) {
            return;
        }

        // Check if there's already an active render task for this page
        if (this.renderTasks.has(pageNum)) {
            return;
        }

        this.activeRenders++;

        try {
            const page = await this.pdfDocument.getPage(pageNum);

            // Check again if destroyed during async operation
            if (this.isDestroyed) {
                this.activeRenders--;
                return;
            }

            const viewport = page.getViewport({scale: this.scale});
            const canvas = pageElements.canvas;
            const context = pageElements.context;

            // Set canvas dimensions
            canvas.width = viewport.width;
            canvas.height = viewport.height;

            // Clear canvas
            context.clearRect(0, 0, canvas.width, canvas.height);

            // Create render context
            const renderContext = {
                canvasContext: context,
                viewport: viewport
            };

            // Start render and track the task
            const renderTask = page.render(renderContext);
            this.renderTasks.set(pageNum, renderTask);

            await renderTask.promise;

            // Check if still valid after render
            if (!this.isDestroyed && this.renderTasks.get(pageNum) === renderTask) {
                // Hide loading, show canvas
                pageElements.loading.classList.add('hidden');
                canvas.classList.remove('hidden');
                pageElements.rendered = true;
                this.renderedPages.add(pageNum);

                console.log(`Page ${pageNum} rendered successfully`);
            }

        } catch (error) {
            if (error.name !== 'RenderingCancelledException') {
                console.error(`Failed to render page ${pageNum}:`, error);
            }
        } finally {
            // Clean up
            this.renderTasks.delete(pageNum);
            this.activeRenders--;

            // Continue processing queue
            if (!this.isDestroyed) {
                this.processRenderQueue();
            }
        }
    }

    async renderPage(pageNumber) {
        if (!this.pdfDocument || !this.canvas || this.isDestroyed) return;

        // Cancel any existing single page render
        const existingTask = this.renderTasks.get('single-page');
        if (existingTask) {
            try {
                existingTask.cancel();
            } catch (e) {
                // Ignore cancellation errors
            }
            this.renderTasks.delete('single-page');
        }

        try {
            const page = await this.pdfDocument.getPage(pageNumber);

            if (this.isDestroyed) return;

            const viewport = page.getViewport({scale: this.scale});

            // Set canvas dimensions
            this.canvas.width = viewport.width;
            this.canvas.height = viewport.height;

            // Clear canvas
            this.context.clearRect(0, 0, this.canvas.width, this.canvas.height);

            // Render page
            const renderContext = {
                canvasContext: this.context,
                viewport: viewport
            };

            const renderTask = page.render(renderContext);
            this.renderTasks.set('single-page', renderTask);

            await renderTask.promise;

            if (!this.isDestroyed) {
                // Update UI
                this.currentPage = pageNumber;
                document.getElementById('page-input').value = pageNumber;
                this.updateNavigationButtons();
                this.updateProgress();

                // Trigger callback
                if (this.config.onPageChange) {
                    this.config.onPageChange(pageNumber, this.totalPages);
                }

                // Schedule progress save
                this.scheduleProgressSave();
            }

        } catch (error) {
            if (error.name !== 'RenderingCancelledException') {
                this.handleError('Failed to render page', error);
            }
        } finally {
            this.renderTasks.delete('single-page');
        }
    }

    cancelAllRenderTasks() {
        // Cancel all active render tasks
        this.renderTasks.forEach((renderTask, pageNum) => {
            try {
                renderTask.cancel();
            } catch (e) {
                // Ignore cancellation errors
            }
        });
        this.renderTasks.clear();
        this.activeRenders = 0;
        this.renderQueue.length = 0;
    }

    toggleViewMode() {
        this.config.continuousMode = !this.config.continuousMode;

        const toggleBtn = document.getElementById('toggle-continuous');
        const continuousText = toggleBtn.querySelector('.continuous-text');
        const singleText = toggleBtn.querySelector('.single-text');

        if (this.config.continuousMode) {
            toggleBtn.classList.remove('bg-blue-600');
            toggleBtn.classList.add('bg-green-600');
            continuousText.classList.remove('hidden');
            singleText.classList.add('hidden');
            this.initializeContinuousMode();
        } else {
            toggleBtn.classList.remove('bg-green-600');
            toggleBtn.classList.add('bg-blue-600');
            continuousText.classList.add('hidden');
            singleText.classList.remove('hidden');
            this.initializeSinglePageMode();
        }
    }

    handleScroll() {
        if (!this.config.continuousMode || this.isDestroyed) return;

        // Throttle scroll handling
        if (this.scrollTimeout) {
            clearTimeout(this.scrollTimeout);
        }

        this.scrollTimeout = setTimeout(() => {
            if (!this.isDestroyed) {
                this.processRenderQueue();
            }
        }, 100);
    }

    updateCurrentPage(pageNum) {
        if (this.currentPage !== pageNum && !this.isDestroyed) {
            this.currentPage = pageNum;
            document.getElementById('page-input').value = pageNum;
            this.updateNavigationButtons();
            this.updateProgress();

            if (this.config.onPageChange) {
                this.config.onPageChange(pageNum, this.totalPages);
            }

            this.scheduleProgressSave();
        }
    }

    scrollToPage(pageNum) {
        if (!this.config.continuousMode || this.isDestroyed) return;

        const pageElements = this.pageElements.get(pageNum);
        if (pageElements) {
            pageElements.container.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        }
    }

    // Navigation methods
    async nextPage() {
        if (this.currentPage < this.totalPages && !this.isDestroyed) {
            if (this.config.continuousMode) {
                this.scrollToPage(this.currentPage + 1);
            } else {
                await this.renderPage(this.currentPage + 1);
            }
        }
    }

    async previousPage() {
        if (this.currentPage > 1 && !this.isDestroyed) {
            if (this.config.continuousMode) {
                this.scrollToPage(this.currentPage - 1);
            } else {
                await this.renderPage(this.currentPage - 1);
            }
        }
    }

    async goToPage(pageNumber) {
        if (pageNumber >= 1 && pageNumber <= this.totalPages && pageNumber !== this.currentPage && !this.isDestroyed) {
            if (this.config.continuousMode) {
                this.scrollToPage(pageNumber);
            } else {
                await this.renderPage(pageNumber);
            }
        }
    }

    async zoomIn() {
        if (this.scale < 3.0 && !this.isDestroyed) {
            this.scale += 0.2;
            await this.reRenderAllPages();
            this.updateZoomDisplay();
        }
    }

    async zoomOut() {
        if (this.scale > 0.5 && !this.isDestroyed) {
            this.scale -= 0.2;
            await this.reRenderAllPages();
            this.updateZoomDisplay();
        }
    }

    async fitToWidth() {
        if (this.isDestroyed) return;

        const containerWidth = this.viewerContainer.offsetWidth - 40;

        if (this.pdfDocument) {
            const page = await this.pdfDocument.getPage(this.currentPage);
            if (this.isDestroyed) return;

            const viewport = page.getViewport({scale: 1.0});
            this.scale = containerWidth / viewport.width;
            await this.reRenderAllPages();
            this.updateZoomDisplay();
        }
    }

    async reRenderAllPages() {
        if (this.isDestroyed) return;

        this.cancelAllRenderTasks();

        if (this.config.continuousMode) {
            // Mark all pages as not rendered and reset them
            this.renderedPages.clear();
            this.pageElements.forEach((elements, pageNum) => {
                elements.rendered = false;
                elements.canvas.classList.add('hidden');
                elements.loading.classList.remove('hidden');
                elements.loading.innerHTML = `<span class="text-gray-600">Re-rendering page ${pageNum}...</span>`;
            });

            // Restart render process
            this.processRenderQueue();
        } else {
            await this.renderPage(this.currentPage);
        }
    }

    // Progress tracking
    updateProgress() {
        if (this.isDestroyed) return;

        const progressPercentage = Math.round((this.currentPage / this.totalPages) * 100);
        const progressBar = document.getElementById('progress-bar');
        const progressText = document.getElementById('progress-text');

        if (progressBar && progressText) {
            progressBar.style.width = `${progressPercentage}%`;
            progressText.textContent = `${progressPercentage}%`;
        }

        if (this.config.onProgressUpdate) {
            this.config.onProgressUpdate(this.currentPage, this.totalPages, progressPercentage);
        }
    }

    scheduleProgressSave() {
        if (this.progressSaveTimeout) {
            clearTimeout(this.progressSaveTimeout);
        }

        this.progressSaveTimeout = setTimeout(() => {
            if (!this.isDestroyed) {
                this.saveProgress();
            }
        }, 5000);
    }

    async saveProgress() {
        if (!this.config.bookId || this.isDestroyed) return;

        try {
            await fetch('/books/update-progress', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                },
                body: JSON.stringify({
                    book_id: this.config.bookId,
                    current_page: this.currentPage,
                    total_pages: this.totalPages
                })
            });
        } catch (error) {
            console.warn('Failed to save reading progress:', error);
        }
    }


    updateNavigationButtons() {
        if (this.isDestroyed) return;

        const prevBtn = document.getElementById('prev-page');
        const nextBtn = document.getElementById('next-page');

        if (prevBtn && nextBtn) {
            prevBtn.disabled = this.currentPage <= 1;
            nextBtn.disabled = this.currentPage >= this.totalPages;
        }
    }

    updateZoomDisplay() {
        if (this.isDestroyed) return;

        const zoomLevel = document.getElementById('zoom-level');
        const zoomOut = document.getElementById('zoom-out');
        const zoomIn = document.getElementById('zoom-in');

        if (zoomLevel) {
            zoomLevel.textContent = `${Math.round(this.scale * 100)}%`;
        }
        if (zoomOut) {
            zoomOut.disabled = this.scale <= 0.5;
        }
        if (zoomIn) {
            zoomIn.disabled = this.scale >= 3.0;
        }
    }

    showLoading(show) {
        const loadingIndicator = document.getElementById('loading-indicator');
        if (loadingIndicator) {
            loadingIndicator.style.display = show ? 'flex' : 'none';
        }
    }

    toggleFullscreen() {
        if (this.isDestroyed) return;

        const container = document.querySelector(this.config.container + ' .pdf-reader');

        if (container) {
            if (!document.fullscreenElement) {
                container.requestFullscreen().catch(err => {
                    console.warn('Failed to enter fullscreen:', err);
                });
            } else {
                document.exitFullscreen();
            }
        }
    }

    handleError(message, error) {
        console.error(message, error);
        this.showLoading(false);

        if (this.config.onError) {
            this.config.onError(message, error);
        }

        const container = document.querySelector(this.config.container);
        if (container) {
            container.innerHTML = `
                <div class="pdf-reader bg-red-50 border border-red-200 rounded-lg p-8 text-center">
                    <div class="text-red-600 mb-4">
                        <svg class="w-16 h-16 mx-auto mb-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-red-800 mb-2">${message}</h3>
                    <p class="text-red-600 mb-4">${error.message || 'An unexpected error occurred'}</p>
                    <button onclick="location.reload()" class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">
                        Retry
                    </button>
                </div>
            `;
        }
    }

    close() {
        this.isDestroyed = true;

        this.saveProgress();

        // Cancel all render tasks
        this.cancelAllRenderTasks();

        // Disconnect intersection observer
        if (this.intersectionObserver) {
            this.intersectionObserver.disconnect();
            this.intersectionObserver = null;
        }

        // Destroy PDF document
        if (this.pdfDocument) {
            this.pdfDocument.destroy();
        }

        // Clear timeouts
        if (this.progressSaveTimeout) {
            clearTimeout(this.progressSaveTimeout);
        }
        if (this.scrollTimeout) {
            clearTimeout(this.scrollTimeout);
        }

        // Clear data structures
        this.renderedPages.clear();
        this.pageElements.clear();
        this.renderTasks.clear();

        // Clean up global reference
        if (window.pdfReader === this) {
            delete window.pdfReader;
        }

        if (this.config.onClose) {
            this.config.onClose();
        } else {
            window.history.back();
        }
    }


    getCurrentPage() {
        return this.currentPage;
    }

    getTotalPages() {
        return this.totalPages;
    }

    getProgress() {
        return Math.round((this.currentPage / this.totalPages) * 100);
    }
}
