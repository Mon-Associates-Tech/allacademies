@props(['sortable' => false, 'sort' => null, 'direction' => null])

<th {{ $attributes->class([
    'px-6 py-3 text-left text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider',
    'cursor-pointer hover:text-amber-600 dark:hover:text-amber-400 transition-colors' => $sortable,
]) }}
style="letter-spacing: 0.08em;">
    @if($sortable)
        <a href="?sort_by={{ $sort }}&sort_order={{ $direction === 'asc' ? 'desc' : 'asc' }}"
           class="inline-flex items-center gap-1">
            {{ $slot }}
            @if($sort)
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    @if($direction === 'asc')
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/>
                    @else
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    @endif
                </svg>
            @endif
        </a>
    @else
        {{ $slot }}
    @endif
</th>