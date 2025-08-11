@props([
    'href' => null,
    'icon' => null,
    'click' => null
])

@if($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => 'block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700']) }}>
        <div class="flex items-center">
            @if($icon)
                {!! $icon !!}
            @endif
            {{ $slot }}
        </div>
    </a>
@else
    <button
        @if($click) @click="{{ $click }}" @endif
        {{ $attributes->merge(['class' => 'block w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700']) }}
    >
        <div class="flex items-center">
            @if($icon)
                {!! $icon !!}
            @endif
            {{ $slot }}
        </div>
    </button>
@endif
