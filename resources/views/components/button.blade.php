@props(['href' => null, 'color' => 'primary'])

@php
    $classes = [
        'primary' => 'bg-blue-600 hover:bg-blue-700 text-white',
        'success' => 'bg-green-600 hover:bg-green-700 text-white',
        'neutral' => 'bg-gray-600 hover:bg-gray-700 text-white',
        'outline' => 'border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700',
    ];
@endphp

@if($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => "inline-flex items-center justify-center px-5 py-2.5 rounded-lg font-semibold text-sm transition-colors {$classes[$color]}"]) }}>
        {{ $slot }}
    </a>
@else
    <button {{ $attributes->merge(['class' => "inline-flex items-center justify-center px-5 py-2.5 rounded-lg font-semibold text-sm transition-colors {$classes[$color]}"]) }}>
        {{ $slot }}
    </button>
@endif
