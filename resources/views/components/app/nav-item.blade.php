@props([
    'href'   => '#',
    'active' => false,
    'icon'   => null,   // heroicon / any x-dynamic-component name e.g. "heroicon-o-home"
    // For raw SVG icons, use <x-slot:iconSlot> — color is handled automatically
])

<li {{ $attributes->only(['class', 'title']) }}>
    <a
        :class="sidebarExpanded ? 'py-2' : ''"
        href="{{ $href }}"
        class="block pl-3 rounded-lg transition {{ $active
            ? 'bg-violet-500 text-white font-bold'
            : 'text-gray-800 dark:text-gray-100 hover:bg-gray-200 dark:hover:bg-gray-700' }}"
    >
        <div class="flex items-center">
            {{-- Icon: either a raw SVG slot or a heroicon dynamic component --}}
            @if (isset($iconSlot) && $iconSlot->isNotEmpty())
                {{-- Wrapper span applies the correct color; SVGs inside just need fill-current / stroke-current --}}
                <span class="{{ $active ? 'text-white' : 'text-gray-400 dark:text-gray-500' }}">
                    {{ $iconSlot }}
                </span>
            @elseif ($icon)
                <x-dynamic-component
                    :component="$icon"
                    class="shrink-0 w-4 h-4 {{ $active ? 'text-white' : 'text-gray-400 dark:text-gray-500' }}"
                />
            @endif

            <span class="text-sm ml-2 sidebar-text duration-200">{{ $slot }}</span>
        </div>
    </a>
</li>