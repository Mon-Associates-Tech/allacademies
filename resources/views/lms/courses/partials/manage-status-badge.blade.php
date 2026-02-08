@php
    $statusConfig = [
        'published' => [
            'bg' => 'bg-emerald-100 dark:bg-emerald-900/50',
            'text' => 'text-emerald-700 dark:text-emerald-400',
            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>',
        ],
        'draft' => [
            'bg' => 'bg-amber-100 dark:bg-amber-900/50',
            'text' => 'text-amber-700 dark:text-amber-400',
            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>',
        ],
        'unpublished' => [
            'bg' => 'bg-gray-100 dark:bg-gray-700',
            'text' => 'text-gray-700 dark:text-gray-400',
            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>',
        ],
        'archived' => [
            'bg' => 'bg-red-100 dark:bg-red-900/50',
            'text' => 'text-red-700 dark:text-red-400',
            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/>',
        ],
    ];

    $config = $statusConfig[$status] ?? $statusConfig['draft'];
@endphp

<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium {{ $config['bg'] }} {{ $config['text'] }}">
    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        {!! $config['icon'] !!}
    </svg>
    {{ ucfirst($status) }}
</span>
