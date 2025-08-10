<?php

namespace App\Livewire\Common;

use Livewire\Attributes\Computed;
use Livewire\Component;

class SearchableMultiSelect extends Component
{
    public string $search = '';
    public array $selected = [];
    public array $items = [];
    public string $placeholder = 'Search and select...';
    public string $emptyMessage = 'No items found';
    public bool $multiple = true;
    public bool $clearable = true;
    public string $maxHeight = '256px';
    public string $name = '';
    public bool $required = false;
    public bool $disabled = false;
    public string $size = 'md'; // sm, md, lg

    // Hierarchical options
    public bool $hierarchical = false;
    public string $parentKey = 'parent_id';
    public string $valueKey = 'id';
    public string $labelKey = 'name';
    public string $childrenKey = 'children';

    public bool $dropdownOpen = false;

    protected $listeners = [
        'reset-component' => 'resetComponent',
    ];

    public function mount(
        array $items = [],
        array $selected = [],
        string $placeholder = 'Search and select...',
        string $emptyMessage = 'No items found',
        bool $multiple = true,
        bool $clearable = true,
        string $maxHeight = '256px',
        string $name = '',
        bool $required = false,
        bool $disabled = false,
        string $size = 'md',
        bool $hierarchical = false,
        string $parentKey = 'parent_id',
        string $valueKey = 'id',
        string $labelKey = 'name',
        string $childrenKey = 'children'
    ) {
        $this->items = $items;
        $this->selected = $selected;
        $this->placeholder = $placeholder;
        $this->emptyMessage = $emptyMessage;
        $this->multiple = $multiple;
        $this->clearable = $clearable;
        $this->maxHeight = $maxHeight;
        $this->name = $name;
        $this->required = $required;
        $this->disabled = $disabled;
        $this->size = $size;
        $this->hierarchical = $hierarchical;
        $this->parentKey = $parentKey;
        $this->valueKey = $valueKey;
        $this->labelKey = $labelKey;
        $this->childrenKey = $childrenKey;
    }

    #[Computed]
    public function filteredItems()
    {
        if (empty($this->search)) {
            return $this->hierarchical ? $this->buildHierarchy($this->items) : $this->items;
        }

        $filtered = array_filter($this->items, function ($item) {
            $label = is_array($item) ? ($item[$this->labelKey] ?? '') : $item->label ?? $item->name ?? '';
            return stripos($label, $this->search) !== false;
        });

        return $this->hierarchical ? $this->buildHierarchy($filtered) : $filtered;
    }

    #[Computed]
    public function selectedItems()
    {
        return array_filter($this->items, function ($item) {
            $value = is_array($item) ? ($item[$this->valueKey] ?? '') : $item->id ?? $item->value ?? '';
            return in_array($value, $this->selected);
        });
    }

    public function toggleDropdown()
    {
        if (!$this->disabled) {
            $this->dropdownOpen = !$this->dropdownOpen;
        }
    }

    public function closeDropdown()
    {
        $this->dropdownOpen = false;
    }

    public function selectItem($value)
    {
        if ($this->disabled) {
            return;
        }

        if ($this->multiple) {
            if (in_array($value, $this->selected)) {
                $this->selected = array_values(array_filter($this->selected, fn($v) => $v !== $value));
            } else {
                $this->selected[] = $value;
            }
        } else {
            $this->selected = [$value];
            $this->dropdownOpen = false;
        }

        $this->dispatch('selection-changed', [
            'name' => $this->name,
            'selected' => $this->selected,
        ]);
    }

    public function removeItem($value)
    {
        if ($this->disabled) {
            return;
        }

        $this->selected = array_values(array_filter($this->selected, fn($v) => $v !== $value));

        $this->dispatch('selection-changed', [
            'name' => $this->name,
            'selected' => $this->selected,
        ]);
    }

    public function clearAll()
    {
        if ($this->disabled) {
            return;
        }

        $this->selected = [];
        $this->search = '';

        $this->dispatch('selection-changed', [
            'name' => $this->name,
            'selected' => $this->selected,
        ]);
    }

    public function updatedSearch()
    {
        if (!$this->dropdownOpen) {
            $this->dropdownOpen = true;
        }
    }

    public function resetComponent()
    {
        $this->selected = [];
        $this->search = '';
        $this->dropdownOpen = false;
    }

    private function buildHierarchy(array $items, $parentId = null): array
    {
        $hierarchy = [];

        foreach ($items as $item) {
            $itemParentId = is_array($item) ? ($item[$this->parentKey] ?? null) : $item->{$this->parentKey} ?? null;

            if ($itemParentId === $parentId) {
                $children = $this->buildHierarchy($items, is_array($item) ? $item[$this->valueKey] : $item->{$this->valueKey});

                if ($children) {
                    if (is_array($item)) {
                        $item[$this->childrenKey] = $children;
                    } else {
                        $item->{$this->childrenKey} = $children;
                    }
                }

                $hierarchy[] = $item;
            }
        }

        return $hierarchy;
    }

    private function getItemValue($item)
    {
        return is_array($item) ? ($item[$this->valueKey] ?? '') : $item->{$this->valueKey} ?? $item->id ?? $item->value ?? '';
    }

    private function getItemLabel($item)
    {
        return is_array($item) ? ($item[$this->labelKey] ?? '') : $item->{$this->labelKey} ?? $item->label ?? $item->name ?? '';
    }

    private function hasChildren($item): bool
    {
        if (!$this->hierarchical) {
            return false;
        }

        $children = is_array($item) ? ($item[$this->childrenKey] ?? []) : $item->{$this->childrenKey} ?? [];
        return !empty($children);
    }

    private function getChildren($item): array
    {
        return is_array($item) ? ($item[$this->childrenKey] ?? []) : $item->{$this->childrenKey} ?? [];
    }

    public function render()
    {
        return view('livewire.common.searchable-multiselect');
    }
}
