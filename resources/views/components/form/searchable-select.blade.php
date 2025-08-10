@props([
    'items' => [],
    'selected' => [],
    'name' => '',
    'placeholder' => 'Search and select...',
    'multiple' => true,
    'hierarchical' => false,
    'size' => 'md',
    'required' => false,
    'disabled' => false,
    'clearable' => true,
    'maxHeight' => '256px',
    'emptyMessage' => 'No items found',
    'valueKey' => 'id',
    'labelKey' => 'name',
    'parentKey' => 'parent_id',
    'childrenKey' => 'children'
])

<livewire:common.searchable-multi-select
    :items="$items"
    :selected="$selected"
    :name="$name"
    :placeholder="$placeholder"
    :multiple="$multiple"
    :hierarchical="$hierarchical"
    :size="$size"
    :required="$required"
    :disabled="$disabled"
    :clearable="$clearable"
    :maxHeight="$maxHeight"
    :emptyMessage="$emptyMessage"
    :valueKey="$valueKey"
    :labelKey="$labelKey"
    :parentKey="$parentKey"
    :childrenKey="$childrenKey"
/>
