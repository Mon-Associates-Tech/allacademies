<section>
    <div class="fixed top-4 right-4 z-50 space-y-3 max-w-sm w-full pointer-events-none">
        @foreach ($flashMessages as $message)
            @php
                $colors = $this->getMessageColors($message['type']);
                $icon = $this->getMessageIcon($message['type']);
            @endphp

            <div
                wire:key="message-{{ $message['id'] }}"
                class="pointer-events-auto rounded-lg border backdrop-blur-md
                    {{ $colors['border'] }} {{ $colors['bg'] }}
                    dark:bg-opacity-95 dark:backdrop-brightness-50 dark:backdrop-contrast-125
                    p-4 shadow-lg transform transition-all duration-300 ease-in-out"
                x-data="{
                    show: true,
                    autoHide: {{ $message['autoHide'] ? 'true' : 'false' }},
                    hideTimeout: null,
                    progressWidth: '100%',
                    startHideTimer() {
                        const duration = {{ $hideDelay }};
                        this.progressWidth = '100%';

                        // Trigger reflow to ensure CSS transition works
                        this.$refs.progress.offsetHeight;

                        // Start progress animation
                        requestAnimationFrame(() => {
                            this.progressWidth = '0%';
                        });

                        // Set timeout to hide message
                        this.hideTimeout = setTimeout(() => {
                            this.show = false;
                            setTimeout(() => $wire.dismissMessage('{{ $message['id'] }}'), 200);
                        }, duration);
                    },
                    pauseTimer() {
                        if (this.hideTimeout) {
                            clearTimeout(this.hideTimeout);
                            const progressBar = this.$refs.progress;
                            const width = getComputedStyle(progressBar).width;
                            this.progressWidth = width;
                        }
                    },
                    resumeTimer() {
                        const progressBar = this.$refs.progress;
                        const currentWidth = parseInt(getComputedStyle(progressBar).width);
                        const totalWidth = parseInt(getComputedStyle(progressBar.parentElement).width);
                        const remainingTime = (currentWidth / totalWidth) * 2000;

                        this.progressWidth = '0%';
                        this.hideTimeout = setTimeout(() => {
                            this.show = false;
                            setTimeout(() => $wire.dismissMessage('{{ $message['id'] }}'), 200);
                        }, remainingTime);
                    }
                }"
                x-show="show"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-x-full"
                x-transition:enter-end="opacity-100 translate-x-0"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-x-0"
                x-transition:leave-end="opacity-0 translate-x-full"
                x-init="if (autoHide) { startHideTimer() }"
                @mouseenter="if (autoHide) { pauseTimer() }"
                @mouseleave="if (autoHide) { resumeTimer() }"
                role="alert"
            >
                <div class="flex items-start gap-3">
                    <!-- Icon -->
                    <div class="flex-shrink-0 p-1">
                        <svg class="h-5 w-5 {{ $colors['icon'] }} dark:drop-shadow-glow"
                             fill="none"
                             stroke="currentColor"
                             viewBox="0 0 24 24"
                             aria-hidden="true">
                            <path stroke-linecap="round"
                                  stroke-linejoin="round"
                                  stroke-width="2"
                                  d="{{ $icon }}"/>
                        </svg>
                    </div>

                    <!-- Content -->
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium {{ $colors['text'] }} dark:text-opacity-90 break-words">
                            {{ $message['message'] }}
                        </p>
                        <p class="mt-1 text-xs {{ $colors['text'] }} dark:text-opacity-75">
                            {{ \Carbon\Carbon::parse($message['timestamp'])->diffForHumans() }}
                        </p>
                    </div>

                    <!-- Close Button -->
                    <div class="flex-shrink-0">
                        <button
                            wire:click="dismissMessage('{{ $message['id'] }}')"
                            class="group rounded-full p-1.5
                                hover:bg-black/10 dark:hover:bg-white/10
                                focus:outline-none focus:ring-2 focus:ring-offset-2
                                {{ $colors['button'] }}
                                transition-all duration-200"
                            aria-label="Dismiss message"
                        >
                            <svg class="h-4 w-4 opacity-75 group-hover:opacity-100 transition-opacity dark:drop-shadow-glow"
                                 fill="none"
                                 stroke="currentColor"
                                 viewBox="0 0 24 24"
                                 aria-hidden="true">
                                <path stroke-linecap="round"
                                      stroke-linejoin="round"
                                      stroke-width="2"
                                      d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Progress bar -->
                @if ($message['autoHide'])
                    <div class="mt-3 bg-black/10 dark:bg-white/20 rounded-full h-0.5 overflow-hidden">
                        <div
                            x-ref="progress"
                            class="h-full {{ $colors['icon'] }} dark:opacity-75"
                            :style="{ width: progressWidth, transition: `width ${$hideDelay}ms linear` }"
                        ></div>
                    </div>
                @endif
            </div>
        @endforeach
    </div>

    <style>
        .dark .dark\:drop-shadow-glow {
            filter: drop-shadow(0 0 2px rgba(255, 255, 255, 0.3));
        }
    </style>
</section>
