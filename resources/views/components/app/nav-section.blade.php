@props(['id', 'label', 'open' => true, 'bordered' => false])

@once
    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>
@endonce

<div
    x-data="{ open: {{ $open ? 'true' : 'false' }} }"
    @class([
        'border-t border-gray-200 dark:border-gray-700 pt-4 mt-2' => $bordered,
    ])
>
    <h3 class="mb-2 text-xs font-semibold uppercase text-gray-400 dark:text-gray-500">
        <button
            type="button"
            @click="open = !open"
            :aria-expanded="open ? 'true' : 'false'"
            aria-controls="{{ $id }}"
            class="flex w-full items-center justify-between gap-2 rounded-md px-3 py-2 text-left text-xs font-semibold uppercase text-gray-400 dark:text-gray-500 hover:text-gray-600 dark:hover:text-gray-300 focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500"
            x-bind:class="{
                'lg:justify-center': !$store.sidebar.expanded,
                'lg:justify-between': $store.sidebar.expanded
            }"
        >
            {{-- Collapsed sidebar dots --}}
            <span
                class="hidden lg:block 2xl:hidden w-6 text-center"
                x-show="!$store.sidebar.expanded"
                x-cloak
                aria-hidden="true"
            >•••</span>

            {{-- Label for mobile and 2xl+ --}}
            <span class="lg:hidden 2xl:block">
                {{ $label }}
            </span>

            {{-- Label for lg-xl when sidebar is expanded --}}
            <span
                class="hidden lg:block 2xl:hidden"
                x-show="$store.sidebar.expanded"
                x-cloak
            >
                {{ $label }}
            </span>

            {{-- Chevron for mobile and 2xl+ --}}
            <x-heroicon-o-chevron-down
                class="h-4 w-4 shrink-0 transition-transform duration-200 lg:hidden 2xl:block"
                x-bind:class="open ? 'rotate-180' : ''"
            />

            {{-- Chevron for lg-xl when sidebar is expanded --}}
            <x-heroicon-o-chevron-down
                class="hidden h-4 w-4 shrink-0 transition-transform duration-200 lg:block 2xl:hidden"
                x-show="$store.sidebar.expanded"
                x-cloak
                x-bind:class="open ? 'rotate-180' : ''"
            />
        </button>
    </h3>

    <ul
        id="{{ $id }}"
        class="space-y-0.5"
        x-show="open"
        x-cloak
        x-transition
    >
        {{ $slot }}
    </ul>
</div>
