@extends('layouts.app')

@section('title', 'Reading: ' . $book->title)

@section('content')
    <div class="h-screen bg-gray-900 text-white overflow-hidden">
        <!-- Reader Header -->
        <div class="bg-gray-800 border-b border-gray-700 px-4 py-3 flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <a href="{{ route('books.show', $book) }}" class="text-gray-300 hover:text-white transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                </a>
                <div>
                    <h1 class="font-semibold text-lg truncate max-w-md">{{ $book->title }}</h1>
                    <p class="text-sm text-gray-400">by {{ $book->author->name ?? 'Unknown Author' }}</p>
                </div>
            </div>

            <div class="flex items-center space-x-4">
                <!-- Reading Progress -->
                <div class="text-sm text-gray-400">
                    <span id="currentPage">1</span> of <span id="totalPages">--</span>
                </div>

                <!-- Reading Controls -->
                <div class="flex items-center space-x-2">
                    <button onclick="toggleFullscreen()" class="p-2 text-gray-300 hover:text-white transition-colors" title="Toggle Fullscreen">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"/>
                        </svg>
                    </button>

                    <button onclick="toggleSettings()" class="p-2 text-gray-300 hover:text-white transition-colors" title="Reading Settings">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Reader Content -->
        <div class="flex h-full">
            <!-- PDF Viewer Container -->
            <div class="flex-1 relative">
                <div id="pdfContainer" class="w-full h-full bg-gray-800 flex items-center justify-center">
                    <div id="loadingSpinner" class="text-center">
                        <svg class="animate-spin h-12 w-12 text-blue-500 mx-auto mb-4" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <p class="text-gray-400">Loading book...</p>
                    </div>
                    <canvas id="pdfCanvas" class="hidden max-w-full max-h-full"></canvas>
                </div>

                <!-- Navigation Controls -->
                <div class="absolute bottom-4 left-1/2 transform -translate-x-1/2 bg-gray-800/90 backdrop-blur-sm rounded-lg px-4 py-2 flex items-center space-x-4">
                    <button onclick="previousPage()" id="prevBtn" class="p-2 text-gray-300 hover:text-white transition-colors disabled:opacity-50" disabled>
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                        </svg>
                    </button>

                    <div class="flex items-center space-x-2">
                        <input type="number" id="pageInput" min="1" class="w-16 px-2 py-1 bg-gray-700 text-white text-center rounded border border-gray-600 focus:border-blue-500 focus:outline-none">
                        <span class="text-gray-400">of</span>
                        <span id="totalPagesBottom" class="text-gray-300">--</span>
                    </div>

                    <button onclick="nextPage()" id="nextBtn" class="p-2 text-gray-300 hover:text-white transition-colors disabled:opacity-50">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Settings Panel -->
            <div id="settingsPanel" class="w-80 bg-gray-800 border-l border-gray-700 p-4 transform translate-x-full transition-transform duration-300">
                <h3 class="text-lg font-semibold mb-4">Reading Settings</h3>

                <div class="space-y-4">
                    <!-- Zoom Control -->
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Zoom Level</label>
                        <div class="flex items-center space-x-2">
                            <button onclick="adjustZoom(-0.1)" class="p-1 bg-gray-700 hover:bg-gray-600 rounded">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                                </svg>
                            </button>
                            <span id="zoomLevel" class="text-sm text-gray-300 w-12 text-center">100%</span>
                            <button onclick="adjustZoom(0.1)" class="p-1 bg-gray-700 hover:bg-gray-600 rounded">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Brightness Control -->
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Brightness</label>
                        <input type="range" id="brightnessSlider" min="0.5" max="1.5" step="0.1" value="1"
                               class="w-full h-2 bg-gray-700 rounded-lg appearance-none cursor-pointer slider">
                    </div>

                    <!-- Theme Toggle -->
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Theme</label>
                        <div class="flex space-x-2">
                            <button onclick="setTheme('dark')" class="flex-1 px-3 py-2 bg-gray-700 hover:bg-gray-600 rounded text-sm">Dark</button>
                            <button onclick="setTheme('light')" class="flex-1 px-3 py-2 bg-gray-700 hover:bg-gray-600 rounded text-sm">Light</button>
                        </div>
                    </div>

                    <!-- Reading Progress -->
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Reading Progress</label>
                        <div class="bg-gray-700 rounded-full h-2">
                            <div id="progressBar" class="bg-blue-500 h-2 rounded-full" style="width: 0%"></div>
                        </div>
                        <p class="text-xs text-gray-400 mt-1">0% completed</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
    <script>
        let pdfDoc = null;
        let pageNum = 1;
        let pageRendering = false;
        let pageNumPending = null;
        let scale = 1.0;
        let canvas = document.getElementById('pdfCanvas');
        let ctx = canvas.getContext('2d');

        // Initialize PDF.js
        pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';

        // Load PDF
        async function loadPDF() {
            try {
                const loadingTask = pdfjsLib.getDocument('{{ $book->content_url }}');
                pdfDoc = await loadingTask.promise;

                document.getElementById('totalPages').textContent = pdfDoc.numPages;
                document.getElementById('totalPagesBottom').textContent = pdfDoc.numPages;
                document.getElementById('pageInput').max = pdfDoc.numPages;

                // Hide loading spinner and show canvas
                document.getElementById('loadingSpinner').classList.add('hidden');
                canvas.classList.remove('hidden');

                // Render first page
                renderPage(pageNum);
                updateProgress();

            } catch (error) {
                console.error('Error loading PDF:', error);
                document.getElementById('loadingSpinner').innerHTML = '<p class="text-red-400">Error loading book. Please try again.</p>';
            }
        }

        // Render page
        function renderPage(num) {
            pageRendering = true;

            pdfDoc.getPage(num).then(function(page) {
                const viewport = page.getViewport({ scale: scale });
                canvas.height = viewport.height;
                canvas.width = viewport.width;

                const renderContext = {
                    canvasContext: ctx,
                    viewport: viewport
                };

                const renderTask = page.render(renderContext);

                renderTask.promise.then(function() {
                    pageRendering = false;
                    if (pageNumPending !== null) {
                        renderPage(pageNumPending);
                        pageNumPending = null;
                    }

                    // Update UI
                    document.getElementById('currentPage').textContent = num;
                    document.getElementById('pageInput').value = num;
                    updateNavigationButtons();
                    updateProgress();

                    // Save reading progress
                    saveReadingProgress(num);
                });
            });
        }

        function queueRenderPage(num) {
            if (pageRendering) {
                pageNumPending = num;
            } else {
                renderPage(num);
            }
        }

        function previousPage() {
            if (pageNum <= 1) return;
            pageNum--;
            queueRenderPage(pageNum);
        }

        function nextPage() {
            if (pageNum >= pdfDoc.numPages) return;
            pageNum++;
            queueRenderPage(pageNum);
        }

        function goToPage() {
            const inputPage = parseInt(document.getElementById('pageInput').value);
            if (inputPage >= 1 && inputPage <= pdfDoc.numPages) {
                pageNum = inputPage;
                queueRenderPage(pageNum);
            }
        }

        function updateNavigationButtons() {
            document.getElementById('prevBtn').disabled = pageNum <= 1;
            document.getElementById('nextBtn').disabled = pageNum >= pdfDoc.numPages;
        }

        function updateProgress() {
            if (pdfDoc) {
                const progress = (pageNum / pdfDoc.numPages) * 100;
                document.getElementById('progressBar').style.width = progress + '%';
                document.querySelector('#settingsPanel .text-xs').textContent = Math.round(progress) + '% completed';
            }
        }

        function adjustZoom(delta) {
            scale += delta;
            if (scale < 0.5) scale = 0.5;
            if (scale > 3.0) scale = 3.0;

            document.getElementById('zoomLevel').textContent = Math.round(scale * 100) + '%';
            queueRenderPage(pageNum);
        }

        function toggleSettings() {
            const panel = document.getElementById('settingsPanel');
            if (panel.classList.contains('translate-x-full')) {
                panel.classList.remove('translate-x-full');
            } else {
                panel.classList.add('translate-x-full');
            }
        }

        function toggleFullscreen() {
            if (!document.fullscreenElement) {
                document.documentElement.requestFullscreen();
            } else {
                document.exitFullscreen();
            }
        }

        function setTheme(theme) {
            if (theme === 'light') {
                document.body.classList.add('bg-white', 'text-gray-900');
                document.body.classList.remove('bg-gray-900', 'text-white');
            } else {
                document.body.classList.add('bg-gray-900', 'text-white');
                document.body.classList.remove('bg-white', 'text-gray-900');
            }
        }

        function saveReadingProgress(page) {
            // Save reading progress to backend
            fetch(`/books/{{ $book->id }}/progress`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    current_page: page,
                    total_pages: pdfDoc.numPages
                })
            }).catch(error => console.error('Error saving progress:', error));
        }

        // Event listeners
        document.getElementById('pageInput').addEventListener('change', goToPage);
        document.getElementById('brightnessSlider').addEventListener('input', function() {
            canvas.style.filter = `brightness(${this.value})`;
        });

        // Keyboard navigation
        document.addEventListener('keydown', function(e) {
            if (e.key === 'ArrowLeft' || e.key === 'PageUp') {
                previousPage();
                e.preventDefault();
            } else if (e.key === 'ArrowRight' || e.key === 'PageDown') {
                nextPage();
                e.preventDefault();
            } else if (e.key === 'Escape') {
                toggleSettings();
            }
        });

        // Initialize
        document.addEventListener('DOMContentLoaded', function() {
            loadPDF();
        });
    </script>
@endpush
