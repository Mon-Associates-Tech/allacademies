<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

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
    
    @stack('exam-sync-scripts')
    @livewireScriptConfig
</body>
</html>