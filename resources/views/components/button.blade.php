@props(['full' => false, 'to' => false])

@if ($to)
<a href="{{ $to }}" @class([
    'bg-primary-700 hover:bg-primary-600 text-white font-medium text-sm px-4 py-2',
    'w-full' => $full,
])>
    {{ $slot }}
</a>
@else
<button @class([
    'bg-primary-700 hover:bg-primary-600 text-white font-medium text-sm px-4 py-2',
    'w-full' => $full,
])>
    {{ $slot }}
</button>
@endif
