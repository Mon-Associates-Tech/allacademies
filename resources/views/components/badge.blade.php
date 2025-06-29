@props(['color' => 'gray'])

<span {{ $attributes->merge([
    'class' => "px-3 py-1 rounded-full text-xs font-medium bg-{$color}-100 text-{$color}-800 dark:bg-{$color}-800 dark:text-{$color}-100"
]) }}>
    {{ $slot }}
</span>
