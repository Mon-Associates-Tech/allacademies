<div class="{{$this->attributes->merge(['class' => 'relative'])}}" x-data="{
    size: '{{ $size }}',
    multiple: {{ $multiple ? 'true' : 'false' }},
    disabled: {{ $disabled ? 'true' : 'false' }}
}" x-on:click.outside="$wire.closeDropdown()">

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
        <div class="relative min-h-0 text-left bg-white border rounded-lg shadow-sm cursor-pointer
            @if($disabled) opacity-50 cursor-not-allowed @endif
            @switch($size)
                @case('sm') text-sm @break
                @case('lg') text-lg @break
                @default text-base
            @endswitch
            {{ $dropdownOpen ? 'border-blue-500 ring-2 ring-blue-200' : 'border-gray-300 hover:border-gray-400' }}"
             wire:click="toggleDropdown">

            <div class="flex flex-wrap items-center gap-1 p-2 min-h-[2.5rem]">
                {{-- Selected items (tags) --}}
                @if($multiple && count($this->selectedItems) > 0)
                    @foreach($this->selectedItems as $item)
                        <span class="inline-flex items-center gap-1 px-2 py-1 text-sm font-medium text-blue-700 bg-blue-100 rounded-md">
                            <span>{{ $this->getItemLabel($item) }}</span>
                            @if(!$disabled)
                                <button type="button"
                                        class="flex-shrink-0 ml-1 text-blue-600 hover:text-blue-800 focus:outline-none"
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
                    <span class="text-gray-900">
                        {{ $this->getItemLabel(collect($this->selectedItems)->first()) }}
                    </span>
                @endif

                {{-- Search input --}}
                <div class="flex-1 min-w-0">
                    <input type="text"
                           class="w-full border-0 p-0 text-gray-900 placeholder-gray-400 focus:ring-0 bg-transparent
                           @switch($size)
                               @case('sm') text-sm @break
                               @case('lg') text-lg @break
                               @default text-base
                           @endswitch"
                           placeholder="{{ count($selected) > 0 ? 'Search more...' : $placeholder }}"
                           wire:model.live.debounce.300ms="search"
                           @if($disabled) disabled @endif
                           wire:focus="$set('dropdownOpen', true)"
                           autocomplete="off">
                </div>

                {{-- Clear button --}}
                @if($clearable && count($selected) > 0 && !$disabled)
                    <button type="button"
                            class="flex-shrink-0 p-1 text-gray-400 hover:text-gray-600 focus:outline-none focus:text-gray-600"
                            wire:click.stop="clearAll()">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                        </svg>
                    </button>
                @endif

                {{-- Dropdown arrow --}}
                <div class="flex-shrink-0 ml-1">
                    <svg class="w-4 h-4 text-gray-400 transition-transform duration-200
                        {{ $dropdownOpen ? 'transform rotate-180' : '' }}"
                         fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                    </svg>
                </div>
            </div>
        </div>

        {{-- Dropdown menu --}}
        @if($dropdownOpen)
            <div class="absolute z-50 w-full mt-1 bg-white border border-gray-300 rounded-lg shadow-lg"
                 style="max-height: {{ $maxHeight }}; overflow-y: auto;"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 translate-y-1"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100 translate-y-0"
                 x-transition:leave-end="opacity-0 translate-y-1">

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
                                <div class="flex items-center justify-between px-4 py-2 cursor-pointer hover:bg-gray-100
                                    {{ $isSelected ? 'bg-blue-50 text-blue-700' : 'text-gray-900' }}"
                                     wire:click.stop="selectItem('{{ $value }}')">
                                    <span class="flex-1 truncate">{{ $label }}</span>
                                    @if($isSelected)
                                        <svg class="flex-shrink-0 w-4 h-4 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                        </svg>
                                    @endif
                                </div>
                            @endforeach
                        @endif
                    </div>
                @else
                    <div class="px-4 py-8 text-center text-gray-500">
                        <svg class="w-8 h-8 mx-auto mb-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <p class="text-sm">{{ $emptyMessage }}</p>
                    </div>
                @endif
            </div>
        @endif
    </div>
</div>
