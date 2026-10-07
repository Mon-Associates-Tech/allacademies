@props(['items' => []])

<nav {{ $attributes->merge(['class' => 'mb-6 flex flex-wrap items-center gap-2 font-mono text-[10.5px] tracking-[0.14em] text-muted']) }} aria-label="Breadcrumb">
    @foreach($items as $crumb)
        @if(!$loop->first)
            <span class="text-line-strong">/</span>
        @endif
        @if(!empty($crumb['href']))
            <a href="{{ $crumb['href'] }}" class="transition hover:text-ink">{{ strtoupper($crumb['label']) }}</a>
        @else
            <span class="text-ink-soft">{{ strtoupper($crumb['label']) }}</span>
        @endif
    @endforeach
</nav>
