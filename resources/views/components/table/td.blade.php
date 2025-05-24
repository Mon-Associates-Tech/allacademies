@props(['action' => false, 'bold' => false])

<td
    {{ $attributes->merge(
    ['class' => $action ? 'px-6 py-2 text-right text-sm font-medium space-x-2' :
    ($bold ? 'px-6 py-2  text-sm font-medium text-gray-900' :
    'px-6 py-2  text-sm text-gray-500')]) }}>
    {{ $slot }}
</td>
