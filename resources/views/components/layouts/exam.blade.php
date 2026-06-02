<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    {{-- PWA Meta Tags --}}
    <meta name="theme-color" content="#3b82f6">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="Examination Hub">
    <link rel="manifest" href="/manifest.json">
    <link rel="apple-touch-icon" href="/apple-touch-icon.png">

    <title>{{ config('app.name') }} - Examination</title>

    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}">

    <style>
        [x-cloak] { display: none !important; }
    </style>

    <script>
        if (localStorage.getItem('dark-mode') === 'true' ||
            (localStorage.getItem('dark-mode') === null &&
                window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        }
    </script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])


    
    {{-- Enhanced dark mode styles --}}
    <link rel="stylesheet" href="{{ asset('css/exam-dark-mode.css') }}">
    
    {{-- PWA Service Worker Registration --}}
    <script>
        // Register service worker for PWA support
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/sw.js', { scope: '/' })
                    .then((registration) => {
                        console.log('[PWA] Service Worker registered successfully:', registration.scope);
                        
                        // Check for updates periodically
                        setInterval(() => {
                            registration.update();
                        }, 60 * 60 * 1000); // Check every hour
                        
                        // Listen for updates
                        registration.addEventListener('updatefound', () => {
                            const newWorker = registration.installing;
                            console.log('[PWA] New version available');
                            
                            newWorker.addEventListener('statechange', () => {
                                if (newWorker.state === 'installed' && navigator.serviceWorker.controller) {
                                    // New version is ready, show notification
                                    if (confirm('A new version of Examination Hub is available. Reload to update?')) {
                                        window.location.reload();
                                    }
                                }
                            });
                        });
                    })
                    .catch((error) => {
                        console.error('[PWA] Service Worker registration failed:', error);
                    });
            });
        }
        
        // Handle online/offline events
        window.addEventListener('online', () => {
            console.log('[PWA] Back online');
            // Sync pending data
            if ('sync' in window.SyncManager) {
                navigator.serviceWorker.ready.then((registration) => {
                    return registration.sync.register('save-exam-response');
                }).catch((err) => {
                    console.log('[PWA] Background sync not available');
                });
            }
        });
        
        window.addEventListener('offline', () => {
            console.log('[PWA] Offline mode activated');
            // Show offline indicator
            const indicator = document.createElement('div');
            indicator.id = 'offline-indicator';
            indicator.style.cssText = `
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                background: #ef4444;
                color: white;
                text-align: center;
                padding: 8px;
                font-size: 14px;
                font-weight: 600;
                z-index: 9999;
            `;
            indicator.textContent = '⚠️ You are offline. Your work will sync when reconnected.';
            document.body.appendChild(indicator);
        });
        
        // Remove offline indicator when back online
        window.addEventListener('online', () => {
            const indicator = document.getElementById('offline-indicator');
            if (indicator) {
                indicator.remove();
            }
        });
    </script>
    
    <!-- Conditionally load exam-specific JavaScript -->
    @stack('exam-scripts')
    
    @livewireStyles

    <!-- Exam synchronization component -->
    <div x-data="examSync()" class="fixed bottom-4 right-4 z-50">
        <template x-if="isSyncing">
            <div class="flex items-center bg-blue-500 text-white px-4 py-2 rounded shadow-lg">
                <svg class="animate-spin -ml-1 mr-2 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span x-text="syncMessage"></span>
            </div>
        </template>
    </div>

    <script>
        function examSync() {
            return {
                isSyncing: false,
                syncMessage: 'Syncing exam data...',
                
                init() {
                    this.$watch('isSyncing', value => {
                        if (value) {
                            // Show syncing message for at least 1 second
                            setTimeout(() => {
                                this.isSyncing = false;
                            }, 1000);
                        }
                    });
                    
                    // Listen for Livewire sync events
                    Livewire.on('examDataSyncing', () => {
                        this.isSyncing = true;
                    });
                    
                    Livewire.on('examDataSynced', () => {
                        this.isSyncing = false;
                    });
                }
            }
        }
    </script>
</head>
<body class="font-sans antialiased bg-gray-50 dark:bg-gray-900" x-data>
    {{ $slot }}

    <script>
        document.addEventListener('alpine:initializing', () => {
            Alpine.store('darkMode', {
                on: false,
                init() {
                    this.on = localStorage.getItem('dark-mode') === 'true' ||
                        (localStorage.getItem('dark-mode') === null &&
                            window.matchMedia('(prefers-color-scheme: dark)').matches);
                },
                toggle(value = null) {
                    this.on = value !== null ? value : !this.on;
                    localStorage.setItem('dark-mode', this.on);
                    document.documentElement.classList.toggle('dark', this.on);
                }
            });
        });
    </script>
    @stack('scripts')
    
    @stack('exam-scripts')
    @stack('exam-sync-scripts')
    @livewireScriptConfig
</body>
</html>