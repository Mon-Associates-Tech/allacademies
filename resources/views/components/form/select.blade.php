@props(['name', 'label' => null, 'full' => false, 'value' => '', 'options' => []])

@php
    $value = old($name, $value);
@endphp

<div class="space-y-1 relative"
    {{-- x-data="{{ json_encode(['open' => false, 'label' => '', 'search' => '', 'filtered' => $options, 'value' => $value, 'options' => $options, 'searchable' => count($options) > 15]) }}" --}}
    x-data="{ open: false, label: '', search: '', filtered: @js($options), value: @if($attributes->has('wire:model')) @entangle($attributes->wire('model')) @else @js($value) @endif, options: @js($options), searchable: @js(count($options) > 15) }"
    x-init="value = value ? options.find(o => o.value == value).value : value;label = value ? options.find(o => o.value == value).label : label"
    x-effect="filtered = search ? options.filter(o => o.label.toLowerCase().includes(search)) : options;">
    <label class="text-gray-800 font-medium text-sm">{{ $label ?? ucfirst($name) }}</label>
    <div class="relative">
        <input x-model="value" class="hidden" type="text" id="{{ $name }}" name="{{ $name }}" type="text">
        <input readonly
            @class([ 'block mr-10 pl-4 pr-12 py-2 text-gray-700 focus:outline-none focus:border-gray-700 bg-white border border-gray-300 cursor-default'
            , 'w-full'=> $full,
        ]) type="text"
        x-on:click="open = !open" x-model="label">
        <span class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none">
            <svg class="w-5 h-5 text-gray-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"
                class="feather feather-chevron-down">
                <polyline points="6 9 12 15 18 9"></polyline>
            </svg>
        </span>
    </div>
    <ul x-cloak class="absolute z-10 mt-1 w-full bg-white max-h-60 overflow-x-auto focus:outline-none text-sm py-2 border border-gray-200"
        role="listbox" x-on:click.outside="open = false" x-show="open" x-transition:leave="transition ease-in duration-100" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
        <template x-if="searchable">
            <li><input x-model="search" placeholder="Search..." class="block w-full px-4 pt-1 pb-3 mb-2 focus:outline-none border-b border-gray-200 text-gray-700" type="search"></li>
        </template>
        <template x-for="option in filtered" x-bind:key="option.value">
            <li x-on:click="value = option.value;label = option.label;open = false;search = ''" x-bind:class="option.value == value && 'bg-primary-600 text-white'" class="pl-4 pr-12 py-2 cursor-default select-none relative hover:bg-primary-600 hover:text-white" role="option">
                <span x-text="option.label" class="block truncate"></span>

                <template x-if="option.value == value">
                    <span class="absolute inset-y-0 right-0 flex items-center pr-4">
                        <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"
                            class="feather feather-check">
                            <polyline points="20 6 9 17 4 12"></polyline>
                        </svg>
                    </span>
                </template>
            </li>
        </template>
    </ul>
    @error($name)
    <p class="text-xs text-red-700">{{ $message }}</p>
    @enderror
</div>