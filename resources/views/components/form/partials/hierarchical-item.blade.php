@php
    $value = $this->getItemValue($item);
    $label = $this->getItemLabel($item);
    $isSelected = in_array($value, $this->selected);
    $hasChildren = $this->hasChildren($item);
    $children = $hasChildren ? $this->getChildren($item) : [];
@endphp

<div class="hierarchical-item" x-data="{ expanded: false }">
    <div class="flex items-center justify-between px-4 py-2 cursor-pointer hover:bg-gray-100
        {{ $isSelected ? 'bg-blue-50 text-blue-700' : 'text-gray-900' }}"
         style="padding-left: {{ 1 + ($level * 1.5) }}rem;">

        <div class="flex items-center flex-1 min-w-0">
            @if($hasChildren)
                <button type="button"
                        class="flex-shrink-0 w-4 h-4 mr-2 text-gray-400 hover:text-gray-600 focus:outline-none"
                        x-on:click.stop="expanded = !expanded">
                    <svg x-show="!expanded" class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                    </svg>
                    <svg x-show="expanded" class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                    </svg>
                </button>
            @else
                <div class="w-4 h-4 mr-2"></div>
            @endif

            <span class="flex-1 cursor-pointer"
                  wire:click.stop="selectItem('{{ $value }}')">{{ $label }}</span>
        </div>

        @if($isSelected)
            <svg class="flex-shrink-0 w-4 h-4 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
            </svg>
        @endif
    </div>

    @if($hasChildren)
        <div x-show="expanded" x-collapse>
            @foreach($children as $childItem)
                @include('components.form.partials.hierarchical-item', [
                    'item' => $childItem,
                    'level' => $level + 1
                ])
            @endforeach
        </div>
    @endif
</div>
