@props(['name'])

<div>
    <span class="block text-sm tracking-wide font-medium text-gray-700">{{ $name }}</span>
    <div class="space-y-2 mt-2">
        {{ $slot }}
    </div>
</div>