<div class="fixed top-4 right-4 z-50 space-y-2 max-w-sm w-full pointer-events-none">
    @foreach ($messages as $message)
        @php
            $colors = $this->getMessageColors($message['type']);
            $icon = $this->getMessageIcon($message['type']);
        @endphp

        <div
            wire:key="message-{{ $message['id'] }}"
            class="pointer-events-auto transform transition-all duration-300 ease-in-out opacity-100 translate-x-0"
            x-data="{
                show: true,
                autoHide: {{ $message['autoHide'] ? 'true' : 'false' }},
                hideTimeout: null
            }"
            x-show="show"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-x-full"
            x-transition:enter-end="opacity-100 translate-x-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-x-0"
            x-transition:leave-end="opacity-0 translate-x-full"
            x-init="
                if (autoHide) {
                    hideTimeout = setTimeout(() => {
                        show = false;
                        setTimeout(() => $wire.dismissMessage('{{ $message['id'] }}'), 200);
                    }, {{ $hideDelay }});
                }
            "
            @mouseenter="if (autoHide && hideTimeout) { clearTimeout(hideTimeout); }"
            @mouseleave="
                if (autoHide && show) {
                    hideTimeout = setTimeout(() => {
                        show = false;
                        setTimeout(() => $wire.dismissMessage('{{ $message['id'] }}'), 200);
                    }, 2000);
                }
            "
        >
            <div class="rounded-lg border {{ $colors['border'] }} {{ $colors['bg'] }} p-4 shadow-lg">
                <div class="flex items-start  h-auto">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 {{ $colors['icon'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $icon }}"/>
                        </svg>
                    </div>
                    <div class="ml-3 flex-1 h-auto">
                        <div class="text-sm font-medium {{ $colors['text'] }}">
                            {{ $message['message'] }}
                        </div>
                    </div>
                    <div class="ml-4 flex-shrink-0">
                        <button
                            wire:click="dismissMessage('{{ $message['id'] }}')"
                            class="inline-flex {{ $colors['button'] }} hover:bg-black/5 dark:hover:bg-white/5 rounded-md p-1.5 transition-colors"
                        >
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Progress bar for auto-hide -->
                @if ($message['autoHide'])
                    <div class="mt-2 bg-black/10 dark:bg-white/10 rounded-full h-1 overflow-hidden">
                        <div
                            class="h-full bg-current opacity-30 transition-all ease-linear"
                            x-data="{ width: 100 }"
                            x-init="
                                if (autoHide) {
                                    setTimeout(() => {
                                        width = 0;
                                    }, 100);
                                }
                            "
                            :style="`width: ${width}%; transition-duration: {{ $hideDelay - 100 }}ms`"
                        ></div>
                    </div>
                @endif
            </div>
        </div>
    @endforeach
</div>

<script>
    // Listen for auto-hide events
    document.addEventListener('livewire:initialized', () => {
        Livewire.on('autoHideMessage', (data) => {
            // This is handled by Alpine.js in the component
        });
    });
</script>
