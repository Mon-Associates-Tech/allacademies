@props([
    'width' => 'w-48',
    'position' => 'right-0'
])

<div class="relative" x-data="{ open: false }" x-init="
    $watch('open', value => {
        if (value) {
            $nextTick(() => {
                const button = $refs.button;
                const dropdown = $refs.dropdown;
                if (button && dropdown) {
                    const rect = button.getBoundingClientRect();
                    const viewportHeight = window.innerHeight;

                    // Auto-position based on available space
                    if (rect.top < viewportHeight / 2) {
                        dropdown.classList.remove('bottom-full', 'mb-2');
                        dropdown.classList.add('top-full', 'mt-2');
                    } else {
                        dropdown.classList.remove('top-full', 'mt-2');
                        dropdown.classList.add('bottom-full', 'mb-2');
                    }
                }
            });
        }
    })
">
    <!-- Trigger Button - Simplified to just use the slot content -->
    <div @click="open = !open" x-ref="button">
        {{ $slot }}
    </div>

    <!-- Dropdown Menu -->
    <div x-show="open"
         x-ref="dropdown"
         @click.away="open = false"
         x-transition:enter="transition ease-out duration-100"
         x-transition:enter-start="transform opacity-0 scale-95"
         x-transition:enter-end="transform opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-75"
         x-transition:leave-start="transform opacity-100 scale-100"
         x-transition:leave-end="transform opacity-0 scale-95"
         class="absolute {{ $position }} bottom-full mb-2 {{ $width }} rounded-md bg-white dark:bg-gray-800 py-1 shadow-lg ring-1 ring-black ring-opacity-5 dark:ring-gray-600 focus:outline-none"
         style="z-index: 9999;">

        @isset($content)
            {{ $content }}
        @endisset
    </div>
</div>
