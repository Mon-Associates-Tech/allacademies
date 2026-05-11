@props([
    'striped' => true,
    'hover' => true,
    'compact' => false,
])

<div class="overflow-x-auto">
    <table {{ $attributes->class([
        'w-full text-sm',
        'divide-y divide-slate-50 dark:divide-slate-800' => $striped,
    ]) }}>
        @if($slot->isNotEmpty())
            {{ $slot }}
        @else
            <thead>
                <tr class="border-b border-slate-100 dark:border-slate-800 bg-slate-50 dark:bg-slate-800/50">
                    {{ $header ?? '' }}
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50 dark:divide-slate-800">
                {{ $body ?? '' }}
            </tbody>
        @endif
    </table>
</div>