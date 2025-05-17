@props(['href', 'active'])

<a href="{{ $href }}"
    {{ $attributes->merge(['class' => $active ? 'text-orange-800 font-semibold' : 'text-gray-600']) }}>
    {{ $slot }}
</a>
