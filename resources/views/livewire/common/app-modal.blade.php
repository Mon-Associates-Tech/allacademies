<section>
    <div
        x-data="{
        show: @entangle('show'),
        escapeHandler: (e) => {
            if (e.key === 'Escape' && {{ $closable ? 'true' : 'false' }}) {
                $wire.close();
            }
        }
    }"
        x-init="
        $watch('show', value => {
            if (value) {
                document.body.style.overflow = 'hidden';
                document.addEventListener('keydown', escapeHandler);
            } else {
                document.body.style.overflow = '';
                document.removeEventListener('keydown', escapeHandler);
            }
        });

        // Cleanup on destroy
        $destroy(() => {
            document.body.style.overflow = '';
            document.removeEventListener('keydown', escapeHandler);
        });
    "
        x-show="show"
        x-transition:enter="transition ease-out duration-300 transform"
        x-transition:enter-start="opacity-0 translate-y-full"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-200 transform"
        x-transition:leave-start="opacity-0 translate-y-0"
        x-transition:leave-end="opacity-100 translate-y-full"
        x-cloak
        @class([
            'fixed inset-0 z-50 overflow-hidden',
            'flex items-end justify-center' => $position === 'bottom',
            'flex items-center justify-center' => $position === 'center',
            'flex items-start justify-center pt-16' => $position === 'top',
        ])
    >
        <!-- Backdrop -->
        <div
            x-show="show"

            @click="$wire.closeOnBackdrop()"
            @class([
                'absolute inset-0',
                'bg-black/50 backdrop-blur-sm' => $backdrop === 'blur',
                'bg-black/75' => $backdrop === 'dark',
                'bg-black/25' => $backdrop === 'light',
                'bg-white/10 backdrop-blur-md' => $backdrop === 'glass',
            ])
        ></div>

        <!-- Modal Container -->
        <div
            x-show="show"
            x-trap.noscroll.inert="show"
            @click.stop
            x-trap.noscroll.inert="show"
            @class([
                'relative w-full mx-4 mb-4 mt-auto',
                $maxWidth,
                'h-[calc(100vh-2rem)]' => $position === 'bottom',
                'max-h-[90vh]' => $position !== 'bottom',
            ])
        >
            <!-- Modal Content -->
            <div
                class="bg-white dark:bg-gray-900 shadow-2xl rounded-t-3xl sm:rounded-2xl overflow-hidden h-full flex flex-col">

                <!-- Header -->
                <div class="flex-shrink-0 bg-white dark:bg-gray-900 border-b border-gray-200 dark:border-gray-700">
                    <!-- Handle bar for mobile -->
                    @if($position === 'bottom')
                        <div class="flex justify-center py-2">
                            <div class="w-10 h-1 bg-gray-300 dark:bg-gray-600 rounded-full"></div>
                        </div>
                    @endif

                    <div class="flex items-center justify-between px-6 py-4">
                        <div class="flex-1">
                            @if($title || $header ?? false)
                                <div class="flex items-center space-x-3">
                                    @isset($headerIcon)
                                        <div class="flex-shrink-0">
                                            {{ $headerIcon }}
                                        </div>
                                    @endisset

                                    <div>
                                        @if($title)
                                            <h2 class="text-xl font-semibold text-gray-900 dark:text-white leading-tight">
                                                {{ $title }}
                                            </h2>
                                        @endif

                                        @isset($header)
                                            {{ $header }}
                                        @endisset
                                    </div>
                                </div>
                            @endif
                        </div>

                        @if($closable)
                            <button
                                type="button"
                                @click="$wire.close()"
                                class="ml-4 flex-shrink-0 rounded-full p-2 text-gray-400 hover:text-gray-600 dark:text-gray-500 dark:hover:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-900"
                                aria-label="Close modal"
                            >
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        @endif
                    </div>
                </div>

                <!-- Body -->
                <div class="flex-1 overflow-y-auto overscroll-contain">
                    <div class="px-6 py-6">
                        @isset($body)
                            {!! $body !!}
                        @else
                            {{ $slot ?? '' }}
                        @endisset
                    </div>
                </div>

                <!-- Footer -->
                @if(isset($footer) || isset($actions))
                    <div
                        class="flex-shrink-0 bg-gray-50 dark:bg-gray-800/50 border-t border-gray-200 dark:border-gray-700 px-6 py-4">
                        @isset($footer)
                            {{ $footer }}
                        @endisset

                        @isset($actions)
                            <div class="flex items-center justify-end space-x-3">
                                {{ $actions }}
                            </div>
                        @endisset
                    </div>
                @endif
            </div>
        </div>
    </div>

    <style>
        /* Modal Component Custom Styles */

        /* Ensure smooth scrolling in modal body */
        .modal-body {
            scroll-behavior: smooth;
        }

        /* Custom scrollbar for modal body */
        .modal-body::-webkit-scrollbar {
            width: 6px;
        }

        .modal-body::-webkit-scrollbar-track {
            background: rgb(243 244 246);
        }

        .modal-body::-webkit-scrollbar-thumb {
            background: rgb(156 163 175);
            border-radius: 3px;
        }

        .modal-body::-webkit-scrollbar-thumb:hover {
            background: rgb(107 114 128);
        }

        /* Dark mode scrollbar */
        .dark .modal-body::-webkit-scrollbar-track {
            background: rgb(55 65 81);
        }

        .dark .modal-body::-webkit-scrollbar-thumb {
            background: rgb(75 85 99);
        }

        .dark .modal-body::-webkit-scrollbar-thumb:hover {
            background: rgb(107 114 128);
        }

        /* Enhanced backdrop blur effect */
        .modal-backdrop-glass {
            backdrop-filter: blur(12px) saturate(180%);
            background: rgba(255, 255, 255, 0.1);
        }

        /* Focus trap styles */
        .modal-content:focus {
            outline: none;
        }

        /* Handle bar animation */
        .modal-handle {
            transition: all 0.2s ease;
        }

        .modal-handle:hover {
            transform: scaleY(1.2);
            background-color: rgb(156 163 175);
        }

        .dark .modal-handle:hover {
            background-color: rgb(107 114 128);
        }

        /* Button hover animations */
        .modal-button {
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            transform: translateY(0);
        }

        .modal-button:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .modal-button:active {
            transform: translateY(0);
        }

        /* Loading state styles */
        .modal-button-loading {
            position: relative;
            overflow: hidden;
        }

        .modal-button-loading::after {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(
                90deg,
                transparent,
                rgba(255, 255, 255, 0.2),
                transparent
            );
            animation: shimmer 1.5s infinite;
        }

        @keyframes shimmer {
            0% {
                left: -100%;
            }
            100% {
                left: 100%;
            }
        }

        /* Enhanced animations */
        @keyframes slideInUp {
            from {
                transform: translateY(100%);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        @keyframes slideOutDown {
            from {
                transform: translateY(0);
                opacity: 1;
            }
            to {
                transform: translateY(100%);
                opacity: 0;
            }
        }

        @keyframes fadeInScale {
            from {
                opacity: 0;
                transform: scale(0.95);
            }
            to {
                opacity: 1;
                transform: scale(1);
            }
        }

        @keyframes fadeOutScale {
            from {
                opacity: 1;
                transform: scale(1);
            }
            to {
                opacity: 0;
                transform: scale(0.95);
            }
        }

        /* Responsive modal adjustments */
        @media (max-width: 640px) {
            .modal-content {
                margin: 0;
                border-radius: 1.5rem 1.5rem 0 0;
            }

            .modal-actions {
                flex-direction: column-reverse;
                gap: 0.75rem;
            }

            .modal-actions button {
                width: 100%;
                justify-content: center;
            }
        }

        /* Accessibility improvements */
        @media (prefers-reduced-motion: reduce) {
            .modal-content,
            .modal-backdrop,
            .modal-button {
                transition: none;
                animation: none;
            }
        }

        /* High contrast mode support */
        @media (prefers-contrast: high) {
            .modal-content {
                border: 2px solid;
            }

            .modal-button {
                border-width: 2px;
            }
        }

        /* Print styles */
        @media print {
            .modal-backdrop,
            .modal-content {
                display: none;
            }
        }
    </style>
</section>
