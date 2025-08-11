@props([
    'action' => false
])

<td {{ $attributes->merge([
    'class' => $action
        ? 'px-6 py-4 whitespace-nowrap text-right text-sm font-medium relative'
        : 'px-6 py-4 whitespace-nowrap text-sm text-gray-900'
]) }}>
    {{ $slot }}
</td>
