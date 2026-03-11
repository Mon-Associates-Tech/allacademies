@props([
    'width' => 'w-48',
    'position' => 'right-0',
    'fixed' => false  // New prop to enable fixed positioning
])

<div class="relative" x-data="{
    open: false,
    useFixed: {{ $fixed ? 'true' : 'false' }},
    init() {
        // Auto-detect if we're inside an overflow container
        if (!this.useFixed) {
            let parent = this.$el.parentElement;
            while (parent && parent !== document.body) {
                const overflow = window.getComputedStyle(parent).overflow;
                if (overflow === 'auto' || overflow === 'scroll' || overflow === 'hidden') {
                    this.useFixed = true;
                    break;
                }
                parent = parent.parentElement;
            }
        }
    }
}" x-init="
    $watch('open', value => {
        if (value) {
            $nextTick(() => {
                const button = $refs.button;
                const dropdown = $refs.dropdown;
                if (button && dropdown) {
                    const rect = button.getBoundingClientRect();
                    const viewportHeight = window.innerHeight;
                    const dropdownHeight = dropdown.offsetHeight;

                    // If using fixed positioning, calculate absolute position
                    if (useFixed) {
                        dropdown.style.position = 'fixed';

                        // Horizontal positioning
                        @if($position === 'right-0')
                            dropdown.style.right = (window.innerWidth - rect.right) + 'px';
                            dropdown.style.left = 'auto';
                        @elseif($position === 'left-0')
                            dropdown.style.left = rect.left + 'px';
                            dropdown.style.right = 'auto';
                        @else
                            // Center or custom positioning
                            dropdown.style.left = rect.left + 'px';
                            dropdown.style.right = 'auto';
                        @endif

                        // Vertical positioning - check if dropdown fits below
                        const spaceBelow = viewportHeight - rect.bottom;
                        const spaceAbove = rect.top;

                        if (spaceBelow >= dropdownHeight || spaceBelow >= spaceAbove) {
                            // Position below
                            dropdown.style.top = (rect.bottom + 8) + 'px';
                            dropdown.style.bottom = 'auto';
                        } else {
                            // Position above
                            dropdown.style.bottom = (viewportHeight - rect.top + 8) + 'px';
                            dropdown.style.top = 'auto';
                        }
                    } else {
                        // Use relative positioning with class-based positioning
                        dropdown.style.position = 'absolute';

                        // Auto-position based on available space
                        if (rect.top < viewportHeight / 2 || (viewportHeight - rect.bottom) >= dropdownHeight) {
                            dropdown.classList.remove('bottom-full', 'mb-2');
                            dropdown.classList.add('top-full', 'mt-2');
                        } else {
                            dropdown.classList.remove('top-full', 'mt-2');
                            dropdown.classList.add('bottom-full', 'mb-2');
                        }
                    }
                }
            });
        }
    })
">
    <!-- Trigger Button -->
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
         :class="useFixed ? '' : 'absolute {{ $position }} bottom-full mb-2'"
         class="{{ $width }} rounded-md bg-white dark:bg-gray-800 py-1 shadow-lg ring-1 ring-black ring-opacity-5 dark:ring-gray-600 focus:outline-none"
         style="z-index: 9999; display: none;"
         x-cloak>

        @isset($content)
            {{ $content }}
        @endisset
    </div>
</div>
