@props(['action' => false, 'bold' => false])

<td {{ $attributes->merge(['class' => $action ? 'px-6 py-4 text-right text-sm font-medium space-x-2' : ($bold ? 'px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900' : 'px-6 py-4 whitespace-nowrap text-sm text-gray-500')]) }}>
    {{ $slot }}
</td>