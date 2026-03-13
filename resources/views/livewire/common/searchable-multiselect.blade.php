<div class="{{$this->attributes->merge(['class' => 'relative'])}}" x-data="{
    size: '{{ $size }}',
    multiple: {{ $multiple ? 'true' : 'false' }},
    disabled: {{ $disabled ? 'true' : 'false' }},
    dropdownOpen: @entangle('dropdownOpen'),
    componentId: '{{ $this->getId() }}'
}"
x-on:click.outside="$wire.closeDropdown()"
x-on:click="if (!dropdownOpen && !disabled) {
    window.dispatchEvent(new CustomEvent('close-all-dropdowns', { detail: { except: componentId } }));
}"
x-on:close-all-dropdowns.window="if ($event.detail.except !== componentId) { $wire.closeDropdown(); }">

    {{-- Hidden inputs for form submission --}}
    @if($name)
        @if($multiple)
            @foreach($selected as $value)
                <input type="hidden" name="{{ $name }}[]" value="{{ $value }}">
            @endforeach
        @else
            <input type="hidden" name="{{ $name }}" value="{{ collect($selected)->first() }}">
        @endif
    @endif

    {{-- Main input container --}}
    <div class="relative">
        <div class="relative min-h-0 text-left bg-white dark:bg-gray-800 border rounded-lg shadow-sm cursor-pointer
            @if($disabled) opacity-50 cursor-not-allowed @endif
            @switch($size)
                @case('sm') text-sm @break
                @case('lg') text-lg @break
                @default text-base
            @endswitch
            {{ $dropdownOpen ? 'border-blue-500 ring-2 ring-blue-200 dark:ring-blue-800' : 'border-gray-300 dark:border-gray-600 hover:border-gray-400 dark:hover:border-gray-500' }}"
             x-on:click="if (!disabled) { dropdownOpen = true }">

            <div class="flex flex-wrap items-center gap-1
                @switch($size)
                    @case('sm') p-1.5 min-h-[2rem] @break
                    @case('lg') p-3 min-h-[3rem] @break
                    @default p-2 min-h-[2.5rem]
                @endswitch">
                {{-- Selected items (tags) --}}
                @if($multiple && count($this->selectedItems) > 0)
                    @foreach($this->selectedItems as $item)
                        <span class="inline-flex items-center gap-1 px-2 py-1 text-sm font-medium text-blue-700 dark:text-blue-300 bg-blue-100 dark:bg-blue-900/50 rounded-md">
                            <span>{{ $this->getItemLabel($item) }}</span>
                            @if(!$disabled)
                                <button type="button"
                                        class="flex-shrink-0 ml-1 text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-200 focus:outline-none"
                                        wire:click.stop="removeItem('{{ $this->getItemValue($item) }}')">
                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                    </svg>
                                </button>
                            @endif
                        </span>
                    @endforeach
                @endif

                {{-- Single selection display --}}
                @if(!$multiple && count($this->selectedItems) > 0)
                    <span class="text-gray-900 dark:text-gray-100">
                        {{ $this->getItemLabel(collect($this->selectedItems)->first()) }}
                    </span>
                @endif

                {{-- Search input --}}
                <div class="flex-1 min-w-0">
                    <input type="text"
                           class="w-full border-0 p-0 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:ring-0 bg-transparent
                           @switch($size)
                               @case('sm') text-sm @break
                               @case('lg') text-lg @break
                               @default text-base
                           @endswitch"
                           placeholder="{{ count($selected) > 0 ? 'Search more...' : $placeholder }}"
                           wire:model.live.debounce.300ms="search"
                           @if($disabled) disabled @endif
                           x-on:focus="dropdownOpen = true"
                           x-on:click.stop
                           autocomplete="off">
                </div>

                {{-- Clear button --}}
                @if($clearable && count($selected) > 0 && !$disabled)
                    <button type="button"
                            class="flex-shrink-0 p-1 text-gray-400 dark:text-gray-500 hover:text-gray-600 dark:hover:text-gray-300 focus:outline-none focus:text-gray-600 dark:focus:text-gray-300"
                            wire:click.stop="clearAll()">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                        </svg>
                    </button>
                @endif

                {{-- Dropdown arrow --}}
                <div class="flex-shrink-0 ml-1" x-on:click.stop>
                    <svg class="w-4 h-4 text-gray-400 dark:text-gray-500 transition-transform duration-200"
                         :class="{ 'transform rotate-180': dropdownOpen }"
                         fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                    </svg>
                </div>
            </div>
        </div>

        {{-- Dropdown menu --}}
        <div x-show="dropdownOpen"
             x-cloak
             class="absolute z-50 min-w-full w-max mt-1 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg shadow-lg"
             style="max-height: {{ $maxHeight }}; overflow-y: auto;"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 translate-y-1"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 translate-y-1">

            {{-- Loading state --}}
            <div wire:loading wire:target="search,toggleDropdown" class="px-4 py-3 text-center text-gray-500 dark:text-gray-400">
                <svg class="inline w-5 h-5 mr-2 text-gray-400 dark:text-gray-500 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Loading...
            </div>

            <div wire:loading.remove wire:target="search,toggleDropdown">
                @if(count($this->filteredItems) > 0)
                    <div class="py-1">
                        @if($hierarchical)
                            @foreach($this->filteredItems as $item)
                                @include('components.form.partials.hierarchical-item', [
                                    'item' => $item,
                                    'level' => 0
                                ])
                            @endforeach
                        @else
                            @foreach($this->filteredItems as $item)
                                @php
                                    $value = $this->getItemValue($item);
                                    $label = $this->getItemLabel($item);
                                    $isSelected = in_array($value, $selected);
                                @endphp
                                <div class="flex items-center justify-between px-4 py-2 cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-700 text-left
                                    {{ $isSelected ? 'bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300' : 'text-gray-900 dark:text-gray-100' }}"
                                     wire:click.stop="selectItem('{{ $value }}')">
                                    <span class="flex-1 text-left">{{ $label }}</span>
                                    @if($isSelected)
                                        <svg class="flex-shrink-0 w-4 h-4 text-blue-600 dark:text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                        </svg>
                                    @endif
                                </div>
                            @endforeach
                        @endif
                    </div>
                @else
                    <div class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">
                        <svg class="w-8 h-8 mx-auto mb-2 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                        <p class="text-sm">{{ $lazyLoad && empty($search) ? 'Start typing to search...' : $emptyMessage }}</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
