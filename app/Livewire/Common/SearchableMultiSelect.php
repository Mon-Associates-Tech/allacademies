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
    public string $size = 'md';

    // Lazy loading options
    public ?string $modelClass = null;
    public string|array $searchColumn = 'name';
    public int $chunkSize = 50;
    public bool $lazyLoad = false;
    public string $customQuery = '';
    public string $labelFormatter = '';

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
        string $childrenKey = 'children',
        // Lazy loading parameters
        ?string $modelClass = null,
        string|array $searchColumn = 'name',
        int $chunkSize = 50,
        bool $lazyLoad = false,
        string $customQuery = '',
        string $labelFormatter = '',
    ): void
    {
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
        $this->modelClass = $modelClass;
        $this->searchColumn = $searchColumn;
        $this->chunkSize = $chunkSize;
        $this->lazyLoad = $lazyLoad;
        $this->customQuery = $customQuery;
        $this->labelFormatter = $labelFormatter;
    }

    #[Computed]
    public function filteredItems()
    {
        // Lazy load from database when model class is provided
        if ($this->lazyLoad && $this->modelClass) {
            return $this->loadItemsFromDatabase();
        }

        // Standard filtering for pre-loaded items
        if (empty($this->search)) {
            return $this->hierarchical ? $this->buildHierarchy($this->items) : $this->items;
        }

        $filtered = array_filter($this->items, function ($item) {
            $label = is_array($item) ? ($item[$this->labelKey] ?? '') : $item->label ?? $item->name ?? '';
            return stripos($label, $this->search) !== false;
        });

        return $this->hierarchical ? $this->buildHierarchy($filtered) : array_values($filtered);
    }

    private function loadItemsFromDatabase(): array
    {
        if (!class_exists($this->modelClass)) {
            return [];
        }

        $query = $this->modelClass::query();

        // Apply custom query method via parent component
        if ($this->customQuery) {
            try {
                // Call parent component's method via event/dispatch
                $result = $this->dispatch('apply-query-filter', [
                    'method' => $this->customQuery,
                    'modelClass' => $this->modelClass,
                ])->to($this->getParentName());

                // Alternative: call directly on parent if accessible
                $parentComponent = $this->getParentComponent();
                if ($parentComponent && method_exists($parentComponent, $this->customQuery)) {
                    $query = $parentComponent->{$this->customQuery}($query);
                }
            } catch (\Exception $e) {
                \Log::warning('Failed to apply custom query', [
                    'method' => $this->customQuery,
                    'error' => $e->getMessage()
                ]);
            }
        }

        // Apply search filter - only load when user searches
        if (!empty($this->search)) {
            $searchColumns = is_array($this->searchColumn) ? $this->searchColumn : [$this->searchColumn];

            $query->where(function($q) use ($searchColumns) {
                foreach ($searchColumns as $column) {
                    $q->orWhere($column, 'LIKE', '%' . $this->search . '%');
                }
            });
        } elseif (!$this->dropdownOpen) {
            return [];
        }

        $query->orderBy($this->labelKey);
        $items = $query->limit($this->chunkSize)->get();

        // Convert to array format with custom label formatting
        return $items->map(function ($item) {
            $label = $this->formatLabel($item);

            return [
                $this->valueKey => $item->{$this->valueKey},
                $this->labelKey => $label,
            ];
        })->toArray();
    }

    private function loadSelectedItemsFromDatabase(): array
    {
        if (!class_exists($this->modelClass) || empty($this->selected)) {
            return [];
        }

        $query = $this->modelClass::whereIn($this->valueKey, $this->selected);

        // Apply custom query for selected items too
        if ($this->customQuery) {
            try {
                $parentComponent = $this->getParentComponent();
                if ($parentComponent && method_exists($parentComponent, $this->customQuery)) {
                    $query = $parentComponent->{$this->customQuery}($query);
                }
            } catch (\Exception $e) {
                \Log::warning('Failed to apply custom query for selected items', [
                    'method' => $this->customQuery,
                    'error' => $e->getMessage()
                ]);
            }
        }

        $items = $query->get();

        return $items->map(function ($item) {
            $label = $this->formatLabel($item);

            return [
                $this->valueKey => $item->{$this->valueKey},
                $this->labelKey => $label,
            ];
        })->toArray();
    }

    private function formatLabel($item): string
    {
        // Use custom formatter if provided
        if ($this->labelFormatter) {
            try {
                $parentComponent = $this->getParentComponent();
                if ($parentComponent && method_exists($parentComponent, $this->labelFormatter)) {
                    return $parentComponent->{$this->labelFormatter}($item);
                }
            } catch (\Exception $e) {
                \Log::warning('Failed to format label', [
                    'method' => $this->labelFormatter,
                    'error' => $e->getMessage()
                ]);
            }
        }

        // Default: just use the label key
        return $item->{$this->labelKey} ?? '';
    }

    /**
     * Get parent component using Livewire's parent-child relationship
     */
    private function getParentComponent()
    {
        try {
            // In Livewire v3, we can access parent through the component tree
            return $this->parent ?? null;
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Get parent component name for dispatching events
     */
    private function getParentName(): ?string
    {
        try {
            $parent = $this->getParentComponent();
            return $parent ? $parent->getName() : null;
        } catch (\Exception $e) {
            return null;
        }
    }

    #[Computed]
    public function selectedItems(): array
    {
        // When lazy loading, fetch selected items separately
        if ($this->lazyLoad && $this->modelClass && !empty($this->selected)) {
            return $this->loadSelectedItemsFromDatabase();
        }

        return array_values(array_filter($this->items, function ($item) {
            $value = is_array($item) ? ($item[$this->valueKey] ?? '') : $item->id ?? $item->value ?? '';
            return in_array($value, $this->selected);
        }));
    }

    public function toggleDropdown(): void
    {
        if (!$this->disabled) {
            $this->dropdownOpen = !$this->dropdownOpen;
        }
    }

    public function closeDropdown(): void
    {
        $this->dropdownOpen = false;
        $this->search = '';
    }

    public function selectItem($value): void
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
            $this->search = '';
        } else {
            $this->selected = [$value];
            $this->dropdownOpen = false;
            $this->search = '';
        }

        $this->dispatch('selection-changed', [
            'name' => $this->name,
            'selected' => $this->selected,
            'value' => $this->multiple ? $this->selected : ($this->selected[0] ?? null)
        ]);

        $this->dispatch('update-' . $this->name, $this->multiple ? $this->selected : ($this->selected[0] ?? null));
    }

    public function removeItem($value): void
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

    public function clearAll(): void
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

    public function updatedSearch(): void
    {
        if (!$this->dropdownOpen) {
            $this->dropdownOpen = true;
        }
    }

    public function resetComponent(): void
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
