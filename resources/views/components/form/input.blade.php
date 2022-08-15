@props(['name', 'type' => 'text', 'label' => null, 'full' => false, 'value' => ''])

<div class="space-y-1">
    <label class="text-gray-800 font-medium text-sm">{{ $label ?? ucfirst($name) }}</label>
    <input @class([
        'block px-4 py-2 text-gray-700 focus:outline-none focus:border-gray-700 bg-white border border-gray-300',
        'w-full' => $full
        ]) id="{{ $name }}" name="{{ $name }}" type="{{ $type }}" value="{{ old($name) }}" >
    @error($name)
        <p class="text-xs text-red-700">{{ $message }}</p>
    @enderror
</div>