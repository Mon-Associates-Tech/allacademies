@props(['name', 'value' => null, 'label' => null, 'description' => null, 'inline' => false])

<div class="space-y-1">
    @unless ($inline)
    <label for="{{ $name }}" class="block text-sm tracking-wide font-medium text-gray-700">{{ $label ?? ucfirst($name) }}</label>
    @endunless

    <div class="flex items-center">
        <input name="{{ $name }}" id="{{ $name }}" type="checkbox" @if(old($name) || $value) checked @endif {{ $attributes->merge(['class' => 'focus:ring-primary-500 h-4 w-4 text-primary-600 border-gray-300 rounded']) }}>
        <span class="ml-3 block text-sm text-gray-700">{{ $description ?? ucfirst($name) }}</span>
    </div>

    @error($name)
    <div class="text-xs font-medium text-red-600">{{ $message }}</div>
    @enderror
</div>