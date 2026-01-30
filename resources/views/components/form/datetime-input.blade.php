@props(['name', 'value' => null, 'label' => null, 'error' => null, 'hasLabel' => true, 'required' => false, 'info' => null, 'infoPosition' => 'top', 'wireModel' => null])

@php
    // Support both wireModel prop and wire:model/wire-model attributes
    $livewireModel = $wireModel ?? $attributes->get('wire-model') ?? $attributes->get('wire:model');
    $wireDirective = $livewireModel ? "wire:model={$livewireModel}" : '';
@endphp

<div class="space-y-1">
    @if ($hasLabel)
        <label for="{{ $name }}" class="block text-sm tracking-tighter font-medium text-gray-700 dark:text-gray-300">{{ $label ?? ucfirst(str_replace('_', ' ', $name)) }}
            @if(!empty($required))
                <span class="text-red-500">*</span>
            @endif
        </label>
    @endif
    @if(!empty($info) && $infoPosition === 'top')
        <p class="text-xs tracking-tight !-mt-0 pb-1 text-gray-500 dark:text-gray-400">{{ $info }}</p>
    @endif
    <input
        name="{{ $name }}"
        id="{{ $name }}"
        type="datetime-local"
        value="{{ old($name, $value) }}"
        @if($livewireModel) wire:model="{{ $livewireModel }}" @endif
        {{ $attributes->merge([
            'class' => 'w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent shadow-sm leading-tight'
        ])->except(['wire-model', 'wire:model', 'wireModel']) }}
    >
    @if(!empty($info) && $infoPosition === 'bottom')
        <p class="text-xs tracking-tight !-mt-0 pb-1 pt-1 text-gray-500 dark:text-gray-400">{{ $info }}</p>
    @endif
    @error($error ?? $name)
        <div class="text-xs font-medium text-red-600 dark:text-red-400">{{ $message }}</div>
    @enderror
</div>
