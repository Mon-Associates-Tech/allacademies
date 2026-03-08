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
        $withShadow ? 'shadow-sm hover:shadow-md' : '',
        'transition-all duration-150',
        'disabled:opacity-50 disabled:cursor-not-allowed'
    ]);
        $variantClasses = match($variant) {
            'subtle' => 'text-slate-700 dark:text-slate-200 bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 border-slate-200 dark:border-slate-600 hover:border-slate-300 dark:hover:border-slate-500 focus:ring-slate-500',
            'primary' => 'text-white border-transparent bg-slate-700 dark:bg-slate-600 hover:bg-slate-800 dark:hover:bg-slate-500 focus:ring-slate-500 dark:focus:ring-offset-gray-800',
            default => 'text-white border-transparent bg-slate-700 dark:bg-slate-600 hover:bg-slate-800 dark:hover:bg-slate-500 focus:ring-slate-500 dark:focus:ring-offset-gray-800',
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
        <span class="@if($variant === 'subtle') text-slate-500 dark:text-slate-400 @endif -ml-1 mr-1 pt-1 my-auto h-5 w-5 flex-shrink-0">
            {{ $icon }}
        </span>
    @endif

    @if ($loading)
        <svg class="animate-spin -ml-1 mr-2 h-5 w-5 @if($variant === 'subtle') text-slate-500 dark:text-slate-400 @else text-white @endif flex-shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"/>
        </svg>
    @endif

    {{ $slot }}

    @if (isset($iconRight))
        <span class="@if($variant === 'subtle') text-slate-500 dark:text-slate-400 @endif ml-2 -mr-1 h-5 w-5 flex-shrink-0">
            {{ $iconRight }}
        </span>
    @endif
</button>
