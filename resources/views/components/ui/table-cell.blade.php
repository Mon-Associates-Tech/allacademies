@props(['align' => 'left'])

<td {{ $attributes->class([
    'px-6 py-3.5',
    'text-left' => $align === 'left',
    'text-center' => $align === 'center',
    'text-right' => $align === 'right',
    'text-slate-700 dark:text-slate-300',
]) }}>
    {{ $slot }}
</td>