@props(['name', 'type' => 'text', 'value' => null, 'label' => null, 'error' => null, 'hasLabel' => true, 'required' =>false])

<div class="space-y-1">
    @if ($hasLabel ?? false)
        <label class="block text-sm tracking-tighter font-medium text-gray-700 dark:text-gray-300">{{ $label ?? ucfirst($name) }}
            @if(!empty($required))
                <span class="text-red-500">*</span>
            @endif
        </label>
        @if(!empty($info))
            <p class="text-xs tracking-tight !-mt-0 pb-1 text-gray-500 dark:text-gray-400">{{$info}}</p>
        @endif
    @endif
    <input name="{{ $name }}" id="{{ $name }}" type="{{ $type }}" value="{{ old($name, $value) }}" {{ $attributes->merge(['class' => 'border-gray-300 rounded-lg shadow-sm w-full leading-tight']) }}>
    @error($error ?? $name)
    <div class="text-xs font-medium text-red-600">{{ $message }}</div>
    @enderror
</div>
