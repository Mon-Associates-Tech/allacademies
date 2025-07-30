<section>
<div class="fixed top-4 right-4 z-50 space-y-3 max-w-sm w-full pointer-events-none">

    @foreach ($messages as $message)
        @php
            $colors = $this->getMessageColors($message['type']);
            $icon = $this->getMessageIcon($message['type']);
        @endphp

        <div
            wire:key="message-{{ $message['id'] }}"
            class="pointer-events-auto rounded-xl border {{ $colors['border'] }} {{ $colors['bg'] }} p-4 shadow-lg transform transition-all duration-300 ease-in-out"
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
            role="alert"
        >
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 {{ $colors['icon'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $icon }}" />
                    </svg>
                </div>
                <div class="ml-3 flex-1">
                    <p class="text-sm font-medium {{ $colors['text'] }}">
                        {{ $message['message'] }}
                    </p>
                </div>
                <div class="ml-4 flex-shrink-0">
                    <button
                        wire:click="dismissMessage('{{ $message['id'] }}')"
                        class="inline-flex {{ $colors['button'] }} hover:bg-black/10 dark:hover:bg-white/10 rounded-full p-1.5 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors"
                        aria-label="Dismiss message"
                    >
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Progress bar for auto-hide -->
            @if ($message['autoHide'])
                <div class="mt-3 bg-black/10 dark:bg-white/10 rounded-full h-1 overflow-hidden">
                    <div
                        class="h-full {{ $colors['icon'] }} opacity-50 transition-all ease-linear"
                        x-data="{ width: 100 }"
                        x-init="
                            if (autoHide) {
                                setTimeout(() => {
                                    width = 0;
                                }, 50);
                            }
                        "
                        :style="`width: ${width}%; transition-duration: {{ $hideDelay - 50 }}ms`"
                    ></div>
                </div>
            @endif
        </div>
    @endforeach
</div>

<style>
    @keyframes slide-up {
        from { opacity: 0; transform: translateX(20px); }
        to { opacity: 1; transform: translateX(0); }
    }
    .animate-slide-up {
        animation: slide-up 0.5s ease-out forwards;
    }
</style>
</section>
