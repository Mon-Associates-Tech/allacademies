<div
    x-data="{
        show: @js($show),
        name: @js($name),
        title: @js($title),
        size: @js($size),
        maxWidth: @js($maxWidth),
        closable: @js($closable),
        persistent: @js($persistent),
        loading: false,
        fullheight: @js($fullheight),
        height: @js($height),
        fixedFooter: @js($fixedFooter ?? true),
        modalData: {},

        // Methods
        open(data = {}) {
            if (data.name && data.name !== this.name && this.name !== '') {
                return;
            }

            if (data.title) {
                this.title = data.title;
            }

            if (data.size) {
                this.size = data.size;
                this.setMaxWidth(data.size);
            }

            // Store all passed data except for reserved properties
            const reservedProps = ['name', 'title', 'size'];
            this.modalData = Object.fromEntries(
                Object.entries(data).filter(([key]) => !reservedProps.includes(key))
            );

            this.show = true;
            this.$dispatch('modal-opened', { name: this.name, data: this.modalData });
        },

        close() {
            this.show = false;
            this.$dispatch('modal-closed', { name: this.name });
{{--            this.destroy();--}}
        },

        toggle() {
            this.show ? this.close() : this.open();
        },

        closeOnBackdrop() {
            if (this.closable && !this.persistent) {
                this.close();
            }
        },

        setMaxWidth(size) {
            const sizes = {
                'xs': 'max-w-xs',
                'sm': 'max-w-sm',
                'md': 'max-w-md',
                'lg': 'max-w-lg',
                'xl': 'max-w-xl',
                '2xl': 'max-w-2xl',
                '3xl': 'max-w-3xl',
                '4xl': 'max-w-4xl',
                '5xl': 'max-w-5xl',
                '6xl': 'max-w-6xl',
                '7xl': 'max-w-7xl',
                'full': 'max-w-full'
            };
            this.maxWidth = sizes[size] || 'max-w-lg';
        }
    }"
    x-init="

     $nextTick(() => {
        // Set footer height for padding calculation
        if (!fixedFooter && $refs.footer) {
            document.documentElement.style.setProperty('--footer-height', `${$refs.footer.offsetHeight}px`);
        }
    });
        // Listen for show changes
        $watch('show', value => {
            if (value) {
                document.body.style.overflow = 'hidden';
            } else {
                document.body.style.overflow = '';
            }
        });
    "
    @open-modal.window="
    if ($event.detail.name === name || (name === '' && $event.detail.name === '')) {
        open($event.detail);
    }
"
    @close-modal.window="
    if ($event.detail.name === name || (name === '' && $event.detail.name === '')) {
        close();
    }
"
    @toggle-modal.window="
    if ($event.detail.name === name || (name === '' && $event.detail.name === '')) {
        toggle();
    }
"
    @keydown.escape.window="
        if (show && closable) {
            close();
        }
    "
    x-show="show"
    x-transition:enter="transition ease-out duration-300 transform"
    x-transition:enter-start="opacity-0 {{ $position === 'bottom' ? 'translate-y-full' : ($position === 'center' ? 'scale-95' : '-translate-y-4') }}"
    x-transition:enter-end="opacity-100 {{ $position === 'bottom' ? 'translate-y-0' : ($position === 'center' ? 'scale-100' : 'translate-y-0') }}"
    x-transition:leave="transition ease-in duration-200 transform"
    x-transition:leave-start="opacity-100 {{ $position === 'bottom' ? 'translate-y-0' : ($position === 'center' ? 'scale-100' : 'translate-y-0') }}"
    x-transition:leave-end="opacity-0 {{ $position === 'bottom' ? 'translate-y-full' : ($position === 'center' ? 'scale-95' : '-translate-y-4') }}"
    x-cloak
    @class([
        'fixed inset-0 z-50 overflow-hidden',
        'flex items-end justify-center' => $position === 'bottom',
        'flex items-center justify-center' => $position === 'center',
        'flex items-start justify-center pt-16' => $position === 'top',
    ])
    style="display: none;"
>
    <!-- Backdrop -->
    <div
        x-show="show"
        @click="closeOnBackdrop()"
        @class([
            'absolute inset-0 transition-opacity duration-300',
            'bg-black/50 backdrop-blur-sm' => $backdrop === 'blur',
            'bg-black/75' => $backdrop === 'dark',
            'bg-black/25' => $backdrop === 'light',
            'bg-white/10 backdrop-blur-md' => $backdrop === 'glass',
        ])
        x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
    ></div>

    <!-- Modal Container -->
    <div
        x-show="show"
        x-trap.noscroll.inert="show"
        @click.stop
        :class="maxWidth"
        @class([
            'relative w-full mx-4 mb-4 mt-auto',
            'h-[calc(100vh-2rem)]' => $position === 'bottom',
            'max-h-[90vh]' => $position !== 'bottom',
            'h-full' => $fullheight,
        ])
    >
        <!-- Modal Content -->
        <div
            class="bg-white dark:bg-gray-900 shadow-2xl rounded-t-md sm:rounded-xl overflow-hidden h-full flex flex-col">

            <!-- Header -->
            <div class="flex-shrink-0 !bg-gray-100 dark:bg-gray-800/50 border-b w-full border-gray-200 dark:border-gray-700">
                <!-- Handle bar for mobile -->
                @if($position === 'bottom')
                    <div class="flex justify-center py-2">
                        <div class="w-10 h-1 bg-gray-300 dark:bg-gray-600 rounded-full modal-handle"></div>
                    </div>
                @endif

                <div class="flex items-center justify-between px-4 !bg-gray-100 dark:bg-gray-800/50 {{$headerBackground}}">
                    <div class="flex-1">
                        @if($title || isset($header))
                            <div class="flex items-center space-x-3 pb-3">
                                @isset($headerIcon)
                                    <div class="flex-shrink-0 text-gray-700 dark:text-gray-300">
                                        {{ $headerIcon }}
                                    </div>
                                @endisset

                                <div>
                                    <h2
                                        x-show="title"
                                        x-text="title"
                                        class="text-xl font-semibold text-gray-900 dark:text-gray-100 leading-tight"
                                    ></h2>

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
                            @click="close()"
                            class="ml-4 -mt-5 flex-shrink-0 rounded-lg p-2 bg-gray-200 dark:bg-gray-700 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 hover:bg-gray-300 dark:hover:bg-gray-600 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800"
                            aria-label="Close modal"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    @endif
                </div>
            </div>

            <!-- Body -->
            <div
                class="flex flex-col flex-1"
                :class="{
            'overflow-hidden': fixedFooter,
            'overflow-auto': !fixedFooter
        }"
            >
                <!-- Body -->
                <div
                    class="flex-1"
                    :class="{
                'overflow-y-auto': fixedFooter,
                'overflow-visible': !fixedFooter
            }"
                >
                    <div class="px-6 py-6">
                        @isset($body)
                            {{ $body }}
                        @else
                            {{ $slot }}
                        @endisset
                    </div>
                </div>

                <!-- Footer -->
                @if(isset($footer) || isset($actions))
                    <div
                        class="bg-gray-50 dark:bg-gray-800/50 border-t border-gray-200 dark:border-gray-700 px-4 py-4"
                        :class="{
                    'sticky bottom-0 flex-shrink-0': fixedFooter,
                    'relative': !fixedFooter
                }"
                    >
                        @isset($footer)
                            {{ $footer }}
                        @endisset

                        @isset($actions)
                            <div class="flex items-center justify-end space-x-3 modal-actions">
                                {{ $actions }}
                            </div>
                        @endisset
                    </div>
                @endif
            </div>
            <!-- Loading Overlay -->
            <div
                x-show="loading"
                class="absolute inset-0 bg-white/75 dark:bg-gray-900/75 flex items-center justify-center z-10"
                x-transition
            >
                <div class="flex items-center space-x-2">
                    <svg class="animate-spin h-5 w-5 text-gray-600 dark:text-gray-400"
                         xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor"
                              d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span class="text-gray-600 dark:text-gray-400">Loading...</span>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* Base Modal Styles */
    .modal-container {
        position: relative;
        display: flex;
        flex-direction: column;
        max-height: 100%;
        --footer-height: auto;
    }

    /* Modal Body Styles */
    .modal-body {
        scroll-behavior: smooth;
        min-height: 0;
        position: relative;
    }

    /* Scrollbar Styles */
    .modal-body::-webkit-scrollbar,
    .overflow-y-auto::-webkit-scrollbar {
        width: 6px;
    }

    .modal-body::-webkit-scrollbar-track,
    .overflow-y-auto::-webkit-scrollbar-track {
        background: rgb(243 244 246);
    }

    .modal-body::-webkit-scrollbar-thumb,
    .overflow-y-auto::-webkit-scrollbar-thumb {
        background: rgb(156 163 175);
        border-radius: 3px;
    }

    .modal-body::-webkit-scrollbar-thumb:hover,
    .overflow-y-auto::-webkit-scrollbar-thumb:hover {
        background: rgb(107 114 128);
    }

    /* Dark Mode Scrollbar */
    .dark .modal-body::-webkit-scrollbar-track,
    .dark .overflow-y-auto::-webkit-scrollbar-track {
        background: rgb(55 65 81);
    }

    .dark .modal-body::-webkit-scrollbar-thumb,
    .dark .overflow-y-auto::-webkit-scrollbar-thumb {
        background: rgb(75 85 99);
    }

    .dark .modal-body::-webkit-scrollbar-thumb:hover,
    .dark .overflow-y-auto::-webkit-scrollbar-thumb:hover {
        background: rgb(107 114 128);
    }

    /* Firefox Scrollbar */
    .overflow-y-auto {
        scrollbar-width: thin;
        scrollbar-color: rgb(156 163 175) rgb(243 244 246);
        scroll-behavior: smooth;
    }

    /* Handle Bar Styles */
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

    /* Button Styles */
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

    /* Footer Styles */
    .modal-footer-fixed {
        position: sticky;
        bottom: 0;
        z-index: 10;
        width: 100%;
    }

    /* Height Transition */
    .modal-height-transition {
        transition: height 0.3s ease-in-out;
    }

    /* Mobile Styles */
    @media (max-width: 640px) {
        .modal-actions {
            flex-direction: column-reverse;
            gap: 0.75rem;
        }

        .modal-actions button {
            width: 100%;
            justify-content: center;
        }
    }

    /* Reduced Motion Preferences */
    @media (prefers-reduced-motion: reduce) {
        * {
            transition: none !important;
            animation: none !important;
        }
    }
</style>
