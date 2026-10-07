@props(['counts' => []])

@php
    $current = Route::currentRouteName();
    $tabs = [
        ['route' => 'my-learning.index',        'label' => 'All Courses',  'key' => null],
        ['route' => 'my-learning.in-progress',  'label' => 'In Progress',  'key' => 'in_progress'],
        ['route' => 'my-learning.completed',    'label' => 'Completed',    'key' => 'completed'],
        ['route' => 'my-learning.certificates', 'label' => 'Certificates', 'key' => 'certificates'],
    ];
@endphp

<nav {{ $attributes->merge(['class' => 'mb-8 flex flex-wrap gap-1 border-b border-line']) }} aria-label="Learning sections">
    @foreach($tabs as $tab)
        @php($active = $current === $tab['route'])
        @php($count = $tab['key'] ? ($counts[$tab['key']] ?? 0) : 0)
        <a href="{{ route($tab['route']) }}"
           class="relative -mb-px inline-flex items-center gap-1.5 rounded-t border px-4 py-2.5 font-mono text-[11px] font-semibold uppercase tracking-[0.12em] transition
                  {{ $active ? 'border-line border-b-transparent bg-paper text-ink' : 'border-transparent text-muted hover:text-ink' }}">
            @if($active)
                <span class="absolute inset-x-0 top-0 h-0.5 bg-accent"></span>
            @endif
            {{ $tab['label'] }}
            @if($count > 0)
                <span class="grid h-4 min-w-[16px] place-items-center rounded-full bg-accent px-1 font-mono text-[9px] font-bold text-white">{{ $count }}</span>
            @endif
        </a>
    @endforeach
</nav>
