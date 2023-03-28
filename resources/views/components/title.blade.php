@props(['name', 'image'])

<div class="flex items-center">
    <img src="{{ $image }}" alt="{{ $name }}" class="w-8 h-8">
    <h1 class="ml-3 font-semibold text-gray-800 tracking-wide leading-tight">{{ $name }}</h1>
</div>