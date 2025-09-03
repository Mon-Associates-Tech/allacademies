@props([
    'type' => 'button',
    'size' => 'md',            // sm, md, lg
    'disabled' => false,       // boolean
    'loading' => false,        // boolean or Livewire loading binding
    'wireTarget' => null,      // for Livewire loading target
    'variant' => 'primary',    // primary, subtle
    'withShadow' => true,      // boolean
])

@php
    $baseClasses = join(' ', [
        'inline-flex items-center justify-center border font-medium rounded-lg',
        'focus:outline-none focus:ring-2 focus:ring-offset-2',
        $withShadow ? 'shadow-lg hover:shadow-xl' : '',
        'transition-all duration-200 transform hover:scale-105',
        'disabled:opacity-50 disabled:cursor-not-allowed'
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

        $isDisabled = $disabled || $loading;
@endphp

<button
    type="{{ $type }}"
    {{ $attributes->merge([
        'class' => $classes,
        'disabled' => $isDisabled,
        'wire:loading.attr' => $wireTarget ? 'disabled' : null,
        'wire:target' => $wireTarget,
    ]) }}
>
    @if (isset($icon))
        <span class="@if($variant === 'subtle') text-indigo-500 @endif -ml-1 mr-1 pt-1 my-auto h-5 w-5 flex-shrink-0">
            {{ $icon }}
        </span>
    @endif

    @if ($loading)
        <svg class="animate-spin -ml-1 mr-2 h-5 w-5 @if($variant === 'subtle') text-indigo-500 @else text-white @endif flex-shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"/>
        </svg>
    @endif

    {{ $slot }}

    @if (isset($iconRight))
        <span class="@if($variant === 'subtle') text-indigo-500 @endif ml-2 -mr-1 h-5 w-5 flex-shrink-0">
            {{ $iconRight }}
        </span>
    @endif
</button>
