<x-layouts.app>
    {{-- 5b-3: config now carries the annotation endpoints --}}
    @php
        $firefoxPdfConfig = [
            'bookId' => (int) $book->id,
            'streamUrl' => $streamUrl,
            'progressUrl' => $progressUrl,
            'pdfJsViewerUrl' => $pdfJsViewerUrl,
            'annotationsUrl' => $annotationsUrl,
            'annotationsStoreUrl' => $annotationsStoreUrl,
            'annotationsSyncUrl' => $annotationsSyncUrl,
        ];
    @endphp

    @once
        <script>
            window.bookFirefoxPdfViewer = function (config) {
                let progressTimer = null;
                let saveTimeout = null;
                let lastSentPage = 0;
                let pdfApp = null;

                return {
                    /* ---------- state ---------- */
                    loading: true,
                    iframeSrc: '',

                    /* 5a: annotation state */
                    annotations: [],
                    selectedAnnotation: null,
                    showCommentsPanel: false,
                    drawMode: false,
                    newComment: '',
                    bridgeReady: false,

                    /* ---------- lifecycle ---------- */
                    init() {
                        const params = new URLSearchParams();
                        params.set('file', config.streamUrl);
                        this.iframeSrc = `${config.pdfJsViewerUrl}?${params.toString()}#toolbar=1`;
                    },

                    onIframeLoad() {
                        const iframe = this.$refs.pdfFrame;
                        if (!iframe) return;

                        try {
                            const doc = iframe.contentDocument;
                            const win = iframe.contentWindow;
                            if (!doc || !win) return;

                            this.injectDisabledToolbarStyles(doc);
                            this.blockCommonShortcuts(win, doc);
                            this.bindPdfJsProgress(win);
                            this.listenBridge();   /* ← 5b-1 */
                        } catch (error) {
                            console.error('Unable to customize PDF.js viewer:', error);
                        } finally {
                            this.loading = false;
                        }
                    },

                    /* ---------- toolbar restrictions ---------- */
                    injectDisabledToolbarStyles(doc) {
                        const style = doc.createElement('style');
                        style.textContent = `
                        #print, #download, #secondaryPrint, #secondaryDownload,
                        #printButton, #downloadButton,
                        .toolbarButton[data-l10n-id="print"],
                        .toolbarButton[data-l10n-id="download"],
                        .toolbarButton[data-l10n-id="pdfjs-print-button"],
                        .toolbarButton[data-l10n-id="pdfjs-download-button"] {
                            display: none !important;
                            visibility: hidden !important;
                            pointer-events: none !important;
                        }
                    `;
                        doc.head.appendChild(style);
                    },

                    blockCommonShortcuts(win, doc) {
                        doc.addEventListener('contextmenu', (e) => e.preventDefault(), true);
                        win.addEventListener('keydown', (e) => {
                            const key = (e.key || '').toLowerCase();
                            if ((e.ctrlKey || e.metaKey) && ['p', 's'].includes(key)) e.preventDefault();
                        }, true);
                    },

                    /* ---------- reading progress ---------- */
                    bindPdfJsProgress(win) {
                        pdfApp = win.PDFViewerApplication || win.pdfjsWebPDFViewerApplication || null;
                        if (!pdfApp) return;

                        const reportProgress = () => {
                            const currentPage = Number(pdfApp.page || 0);
                            const totalPages = Number(pdfApp.pagesCount || 0);
                            if (!currentPage || !totalPages) return;

                            if (currentPage !== lastSentPage) {
                                lastSentPage = currentPage;
                                clearTimeout(saveTimeout);
                                saveTimeout = setTimeout(() => {
                                    this.saveProgress(currentPage, totalPages);
                                }, 1000);
                            }
                        };

                        if (pdfApp.eventBus) pdfApp.eventBus.on('pagechanging', reportProgress);
                        if (pdfApp.initializedPromise) pdfApp.initializedPromise.then(reportProgress).catch(() => {
                        });
                        if (progressTimer) clearInterval(progressTimer);
                        progressTimer = setInterval(reportProgress, 1500);

                        win.addEventListener('pagehide', () => {
                            const currentPage = Number(pdfApp.page || 0);
                            const totalPages = Number(pdfApp.pagesCount || 0);
                            if (currentPage && totalPages) this.saveProgress(currentPage, totalPages, true);
                        });
                    },

                    async saveProgress(currentPage, totalPages, keepalive = false) {
                        if (!config.progressUrl) return;
                        const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                        if (!token) return;

                        try {
                            await fetch(config.progressUrl, {
                                method: 'POST',
                                credentials: 'same-origin',
                                keepalive,
                                headers: {
                                    'Content-Type': 'application/json',
                                    'Accept': 'application/json',
                                    'X-CSRF-TOKEN': token,
                                    'X-Requested-With': 'XMLHttpRequest',
                                },
                                body: JSON.stringify({
                                    book_id: config.bookId,
                                    current_page: currentPage,
                                    total_pages: totalPages,
                                }),
                            });
                        } catch (error) {
                            console.error('Failed to save reading progress:', error);
                        }
                    },

                    bridgePost(type, payload) {
                        const iframe = this.$refs.pdfFrame;
                        if (!iframe || !iframe.contentWindow) return;

                        // postMessage uses structured clone, which CANNOT clone Alpine's
                        // reactive Proxy objects. Round-trip through JSON to get plain data.
                        let message;
                        try {
                            message = JSON.parse(JSON.stringify(
                                Object.assign({source: 'aa-parent', type: type}, payload || {})
                            ));
                        } catch (e) {
                            console.error('bridgePost: payload not serializable', e);
                            return;
                        }

                        iframe.contentWindow.postMessage(message, window.location.origin);
                    },

                    listenBridge() {
                        if (this._bridgeListening) return;
                        this._bridgeListening = true;
                        window.addEventListener('message', async (ev) => {
                            if (ev.origin !== window.location.origin) return;
                            const msg = ev.data;
                            if (!msg || msg.source !== 'aa-bridge') return;
                            console.info('[bridge]', msg.type);

                            if (msg.type === 'ready') {
                                this.bridgeReady = true;
                                await this.refreshAnnotations();
                            }
                            if (msg.type === 'annotation-selected') this.openAnnotation(msg.id);
                            if (msg.type === 'editor-selected') {
                                const match = this.annotations.find(a => a.external_id === msg.external_id);
                                if (match) this.openAnnotation(match.id);
                                else console.warn('[bridge] editor-selected but no saved row for', msg.external_id);
                            }
                            if (msg.type === 'rect-created') await this.createRectAnnotation(msg);
                            if (msg.type === 'editors-changed') await this.syncEditors(msg.editors || []);
                        });
                    },

                    async syncEditors(editors) {
                        if (!config.annotationsSyncUrl) {
                            console.error('[sync] annotationsSyncUrl missing from config');
                            return;
                        }
                        const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                        try {
                            const res = await fetch(config.annotationsSyncUrl, {
                                method: 'POST',
                                credentials: 'same-origin',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'Accept': 'application/json',
                                    'X-CSRF-TOKEN': token,
                                    'X-Requested-With': 'XMLHttpRequest',
                                },
                                body: JSON.stringify({editors}),
                            });
                            if (!res.ok) {
                                console.error('[sync] HTTP', res.status, await res.text());
                                return;
                            }
                            const data = await res.json();
                            this.annotations = data.annotations || [];
                            this.bridgePost('refresh-overlays', {});
                        } catch (e) {
                            console.error('[sync] network error', e);
                        }
                    },

                    async postComment() {
                        if (!this.selectedAnnotation || !this.newComment.trim()) return;
                        const url = `${config.annotationsUrl}/${this.selectedAnnotation.id}/comments`;
                        const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                        const res = await fetch(url, {
                            method: 'POST',
                            credentials: 'same-origin',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': token,
                                'X-Requested-With': 'XMLHttpRequest',
                            },
                            body: JSON.stringify({message: this.newComment}),
                        });
                        if (!res.ok) {
                            console.error('[comment] HTTP', res.status, url, await res.text());
                            return;
                        }
                        this.newComment = '';
                        await this.refreshAnnotations();
                        this.openAnnotation(this.selectedAnnotation.id);
                    },

                    async refreshAnnotations() {
                        try {
                            const res = await fetch(config.annotationsUrl, {
                                headers: {'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest'},
                                credentials: 'same-origin',
                            });
                            const data = await res.json();
                            this.annotations = data.annotations || [];
                            this.bridgePost('load-annotations', {annotations: this.annotations});
                        } catch (e) {
                            console.error('Failed to load annotations', e);
                        }
                    },

                    openAnnotation(id) {
                        this.selectedAnnotation = this.annotations.find(a => a.id === id) || null;
                        this.showCommentsPanel = !!this.selectedAnnotation;
                    },

                    toggleDrawMode() {
                        this.drawMode = !this.drawMode;
                        this.bridgePost('draw-mode', {on: this.drawMode});
                    },

                    async createRectAnnotation(rect) {
                        const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                        try {
                            await fetch(config.annotationsStoreUrl, {
                                method: 'POST',
                                credentials: 'same-origin',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'Accept': 'application/json',
                                    'X-CSRF-TOKEN': token,
                                    'X-Requested-With': 'XMLHttpRequest',
                                },
                                body: JSON.stringify({
                                    page_number: rect.page,
                                    x_pct: rect.x_pct,
                                    y_pct: rect.y_pct,
                                    width_pct: rect.width_pct,
                                    height_pct: rect.height_pct,
                                    color: rect.color,
                                }),
                            });
                            this.toggleDrawMode();
                            await this.refreshAnnotations();
                        } catch (e) {
                            console.error('Failed to create annotation', e);
                        }
                    },

                    // async syncEditors(editors) {
                    //     const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                    //     try {
                    //         const res = await fetch(config.annotationsSyncUrl, {
                    //             method: 'POST',
                    //             credentials: 'same-origin',
                    //             headers: {
                    //                 'Content-Type': 'application/json',
                    //                 'Accept': 'application/json',
                    //                 'X-CSRF-TOKEN': token,
                    //                 'X-Requested-With': 'XMLHttpRequest',
                    //             },
                    //             body: JSON.stringify({ editors }),
                    //         });
                    //         const data = await res.json();
                    //         this.annotations = data.annotations || [];
                    //         this.bridgePost('refresh-overlays', {});
                    //     } catch (e) {
                    //         console.error('Failed to sync PDF.js editors', e);
                    //     }
                    // },

                    // async postComment() {
                    //     if (!this.selectedAnnotation || !this.newComment.trim()) return;
                    //     const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

                    //     await fetch(config.annotationsUrl + '/' + this.selectedAnnotation.id + '/comments', {
                    //         method: 'POST',
                    //         credentials: 'same-origin',
                    //         headers: {
                    //             'Content-Type': 'application/json',
                    //             'Accept': 'application/json',
                    //             'X-CSRF-TOKEN': token,
                    //             'X-Requested-With': 'XMLHttpRequest',
                    //         },
                    //         body: JSON.stringify({ message: this.newComment }),
                    //     });

                    //     this.newComment = '';
                    //     await this.refreshAnnotations();
                    //     this.openAnnotation(this.selectedAnnotation.id);
                    // },

                    async deleteComment(commentId) {
                        const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

                        await fetch(config.annotationsUrl + '/' + this.selectedAnnotation.id + '/comments/' + commentId, {
                            method: 'DELETE',
                            credentials: 'same-origin',
                            headers: {
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': token,
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                        });

                        await this.refreshAnnotations();
                        this.openAnnotation(this.selectedAnnotation.id);
                    },

                    async toggleResolved() {
                        if (!this.selectedAnnotation) return;
                        const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

                        await fetch(config.annotationsUrl + '/' + this.selectedAnnotation.id + '/resolve', {
                            method: 'POST',
                            credentials: 'same-origin',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': token,
                                'X-Requested-With': 'XMLHttpRequest',
                            },
                            body: JSON.stringify({resolved: !this.selectedAnnotation.resolved_at}),
                        });

                        await this.refreshAnnotations();
                        this.openAnnotation(this.selectedAnnotation.id);
                    },
                };
            };
        </script>
    @endonce

    <div x-data="bookFirefoxPdfViewer(@js($firefoxPdfConfig))"
         class="flex flex-col h-[calc(100vh-4rem)] bg-gray-100 dark:bg-gray-900">

        {{-- 6: annotation toolbar row --}}
        <div
            class="flexed hidden items-center gap-2 px-4 py-2 border-b border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800">
            <button @click="toggleDrawMode()"
                    :class="drawMode ? 'bg-amber-500 text-white' : 'bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-200 border border-gray-300 dark:border-gray-600'"
                    class="px-3 py-1.5 text-xs font-medium rounded-lg">
                <span x-text="drawMode ? 'Drawing… click page to place' : 'Add highlight box'"></span>
            </button>
            <span class="text-xs hidden text-gray-500 dark:text-gray-400">
                Use the PDF.js toolbar for highlight / ink / text — they auto-save.
            </span>
        </div>

        {{-- viewer (unchanged) --}}
        <div class="relative flex-1">
            <div x-show="loading" x-cloak
                 class="absolute inset-0 z-10 flex items-center justify-center bg-white/80 dark:bg-gray-900/80">
                <span class="text-sm text-gray-700 dark:text-gray-200">Loading PDF reader...</span>
            </div>

            <iframe x-show="iframeSrc" x-cloak
                    x-ref="pdfFrame"
                    :src="iframeSrc"
                    @load="onIframeLoad()"
                    class="absolute inset-0 w-full h-full border-0"
                    title="PDF Reader"></iframe>
        </div>

        {{-- 6: comments panel --}}
        <aside x-show="showCommentsPanel" x-cloak
               class="fixed right-0 top-0 h-full w-96 max-w-full bg-white dark:bg-gray-800 shadow-2xl z-50 flex flex-col">
            <div class="flex items-center justify-between px-4 py-3 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-sm font-semibold text-gray-900 dark:text-white">
                    Comments — page <span x-text="selectedAnnotation?.page_number"></span>
                </h3>
                <div class="flex gap-2">
                    <button @click="toggleResolved()"
                            class="text-xs px-2 py-1 rounded border border-gray-300 dark:border-gray-600 text-gray-600 dark:text-gray-300">
                        <span x-text="selectedAnnotation?.resolved_at ? 'Reopen' : 'Resolve'"></span>
                    </button>
                    <button @click="showCommentsPanel = false"
                            class="text-xs px-2 py-1 rounded border border-gray-300 dark:border-gray-600 text-gray-600 dark:text-gray-300">
                        Close
                    </button>
                </div>
            </div>

            <div class="flex-1 overflow-y-auto p-4 space-y-3">
                <template x-for="comment in (selectedAnnotation?.comments || [])" :key="comment.id">
                    <div class="text-sm bg-gray-50 dark:bg-gray-700 rounded-lg p-3"
                         :class="comment.parent_id ? 'ml-6' : ''">
                        <div class="flex items-center justify-between">
                            <span class="font-medium text-gray-900 dark:text-white" x-text="comment.user_name"></span>
                            <button x-show="comment.user_id === {{ auth()->id() }}"
                                    @click="deleteComment(comment.id)"
                                    class="text-xs text-red-500">Delete
                            </button>
                        </div>
                        <p class="mt-1 text-gray-700 dark:text-gray-200" x-text="comment.message"></p>
                    </div>
                </template>
                <div x-show="!(selectedAnnotation?.comments || []).length" class="text-sm text-gray-500">No comments
                    yet.
                </div>
            </div>

            <div class="p-4 border-t border-gray-200 dark:border-gray-700">
                <textarea x-model="newComment" rows="2" placeholder="Write a comment…"
                          class="w-full text-sm border border-gray-300 dark:border-gray-600 rounded-lg p-2 dark:bg-gray-700 dark:text-white"></textarea>
                <button @click="postComment()"
                        class="mt-2 w-full px-3 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-lg">
                    Comment
                </button>
            </div>
        </aside>
    </div>
</x-layouts.app>
