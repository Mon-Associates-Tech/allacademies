@props([
    'to',
    'size' => 'md',            // sm, md, lg
    'variant' => 'primary',    // primary, subtle
    'withShadow' => true,      // boolean
])

@php
    $baseClasses = join(' ', [
        'inline-flex items-center justify-center border font-medium rounded-lg',
        'focus:outline-none focus:ring-2 focus:ring-offset-2',
        $withShadow ? 'shadow-lg hover:shadow-xl' : '',
        'transition-all duration-200 transform hover:scale-105',
    ]);

    $variantClasses = match($variant) {
        'subtle' => 'text-indigo-700 bg-indigo-50 hover:bg-indigo-100 border-indigo-100 hover:border-indigo-200 focus:ring-indigo-500',
        'primary' => 'text-white border-transparent bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 focus:ring-indigo-500',
        default => 'text-white border-transparent bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 focus:ring-indigo-500',
    };

    $sizeClasses = match($size) {
        'sm' => 'px-3 py-1.5 text-xs',
        'md' => 'px-4 py-2 text-sm',
        'lg' => 'px-7 py-4 text-base',
        default => 'px-6 py-3 text-sm',
    };

    $classes = "$baseClasses $variantClasses $sizeClasses";
@endphp
<a href="{{ $to }}" {{ $attributes->merge(['class' => $classes]) }}>
    @if (isset($icon))
        <span class="@if($variant === 'subtle') text-indigo-500 @endif -ml-1 mr-1 pt-1 my-auto h-5 w-5 flex-shrink-0">
            {{ $icon }}
        </span>
    @endif

    {{ $slot }}

    @if (isset($iconRight))
        <span class="@if($variant === 'subtle') text-indigo-500 @endif ml-2 -mr-1 h-5 w-5 flex-shrink-0">
            {{ $iconRight }}
        </span>
    @endif
</a>
