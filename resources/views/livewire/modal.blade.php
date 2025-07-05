<section>
@if($isOpen)
<div>
    <div
        class="fixed inset-0 z-50 flex items-end justify-center p-0 sm:items-center sm:p-4"
        x-data="{
            show: true,
            escapeHandler: null,
            init() {
                // Immediately show modal on init
                document.body.style.overflow = 'hidden';
                document.body.style.position = 'fixed';
                document.body.style.width = '100%';

                this.escapeHandler = (e) => {
                    if (e.key === 'Escape' && @js($closeOnEsc) && !@js($persistent)) {
                        @this.call('close');
                    }
                };
                document.addEventListener('keydown', this.escapeHandler);
            },
            destroy() {
                document.body.style.removeProperty('overflow');
                document.body.style.removeProperty('position');
                document.body.style.removeProperty('width');
                if (this.escapeHandler) {
                    document.removeEventListener('keydown', this.escapeHandler);
                }
            }
        }"
        x-show="show"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
    >
        <!-- Backdrop -->
        <div
            class="absolute inset-0 bg-black bg-opacity-50 backdrop-blur-sm"
            @click="if(@js($closeOnBackdrop) && !@js($persistent)) { @this.call('close'); }"
        ></div>

        <!-- Modal Container -->
        <div
            class="relative w-full {{ $this->getSizeClasses() }} max-h-[95vh] sm:max-h-[90vh] overflow-hidden rounded-t-xl sm:rounded-xl shadow-2xl transform {{ $this->getThemeClasses() }} border-0 sm:border"
            x-show="show"
            x-transition:enter="transition ease-out duration-400"
            x-transition:enter-start="opacity-0 translate-y-full sm:translate-y-4 sm:scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave="transition ease-in duration-300"
            x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave-end="opacity-0 translate-y-full sm:translate-y-4 sm:scale-95"
            @click.stop
        >
            <!-- Mobile Handle -->
            <div class="flex justify-center pt-3 pb-1 sm:hidden">
                <div class="w-10 h-1 bg-gray-300 dark:bg-gray-600 rounded-full"></div>
            </div>

            <!-- Modal Header -->
            <div class="flex items-center justify-between p-4 sm:p-6 border-b {{ $theme === 'dark' ? 'border-gray-600' : 'border-gray-200' }}">
                <div class="flex-1">
                    @if($slot ?? false)
                        {{ $slot }}
                    @elseif($title)
                        <h3 class="text-lg sm:text-xl font-semibold {{ $theme === 'dark' ? 'text-white' : 'text-gray-900' }}">
                            {{ $title }}
                        </h3>
                    @endif
                </div>

                @if($showCloseButton)
                    <button
                        wire:click="close"
                        class="ml-4 p-2 rounded-full hover:bg-opacity-10 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 {{ $theme === 'dark' ? 'text-gray-400 hover:text-white hover:bg-gray-700 focus:ring-gray-500' : 'text-gray-500 hover:text-gray-700 hover:bg-gray-100 focus:ring-gray-500' }}"
                        aria-label="Close modal"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                @endif
            </div>

            <!-- Modal Content -->
            <div class="flex-1 overflow-y-auto p-4 sm:p-6 max-h-[calc(95vh-8rem)] sm:max-h-[calc(90vh-8rem)] {{ $theme === 'dark' ? 'text-gray-100' : 'text-gray-700' }}">
                @if(isset($content_slot))
                    {{ $content_slot }}
                @elseif($content)
                    <div class="prose {{ $theme === 'dark' ? 'prose-invert' : '' }} max-w-none">
                        {!! $content !!}
                    </div>
                @else
                    <div class="text-center py-8">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <p class="mt-2 text-sm {{ $theme === 'dark' ? 'text-gray-400' : 'text-gray-500' }}">
                            No content provided
                        </p>
                    </div>
                @endif
            </div>

            <!-- Modal Footer -->
            @if(isset($footer_slot))
                <div class="flex items-center justify-end gap-3 p-4 sm:p-6 border-t {{ $theme === 'dark' ? 'border-gray-600' : 'border-gray-200' }} bg-gray-50 dark:bg-gray-800/50">
                    {{ $footer_slot }}
                </div>
            @endif
        </div>
    </div>

    @script
    <script>
        // Simple cleanup on page unload
        window.addEventListener('beforeunload', function() {
            document.body.style.removeProperty('overflow');
            document.body.style.removeProperty('position');
            document.body.style.removeProperty('width');
        });
    </script>
    @endscript

    <style>
        /* Custom scrollbar styles */
        .overflow-y-auto::-webkit-scrollbar {
            width: 6px;
        }

        .overflow-y-auto::-webkit-scrollbar-track {
            background: transparent;
        }

        .overflow-y-auto::-webkit-scrollbar-thumb {
            background: rgba(156, 163, 175, 0.5);
            border-radius: 3px;
        }

        .overflow-y-auto::-webkit-scrollbar-thumb:hover {
            background: rgba(156, 163, 175, 0.7);
        }

        /* Dark mode scrollbar */
        @media (prefers-color-scheme: dark) {
            .overflow-y-auto::-webkit-scrollbar-thumb {
                background: rgba(75, 85, 99, 0.5);
            }

            .overflow-y-auto::-webkit-scrollbar-thumb:hover {
                background: rgba(75, 85, 99, 0.7);
            }
        }

        /* Enhanced shadow for better depth perception */
        .shadow-2xl {
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        }

        @media (prefers-color-scheme: dark) {
            .shadow-2xl {
                box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.4);
            }
        }
    </style>
</div>
@endif
</section>
