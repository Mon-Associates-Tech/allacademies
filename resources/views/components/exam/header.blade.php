@props([
    'eyebrow' => null,
    'title',
    'sub' => null,
    'lines' => [],     // stamp words, e.g. ['EXAM','IDENTITY']
    'fresh' => false,  // re-stamp animation (e.g. right after saving)
    'variant' => 'full', // 'full' or 'compact'
    'color' => null,   // 'ok', 'warn', 'danger', 'primary', 'muted' or null for default
])

@php
    $isCompact = $variant === 'compact';

    $colorMap = [
        'ok'      => ['bg' => 'bg-ok/10', 'border' => 'border-ok/30', 'accent' => 'bg-ok'],
        'warn'    => ['bg' => 'bg-warn/10', 'border' => 'border-warn/30', 'accent' => 'bg-warn'],
        'danger'  => ['bg' => 'bg-accent/10', 'border' => 'border-accent/30', 'accent' => 'bg-accent'],
        'primary' => ['bg' => 'bg-primary/10', 'border' => 'border-primary/30', 'accent' => 'bg-primary'],
        'muted'   => ['bg' => 'bg-muted/10', 'border' => 'border-muted/30', 'accent' => 'bg-muted'],
    ];

    $colorStyle = $color !== null ? ($colorMap[$color] ?? $colorMap['muted']) : null;
@endphp

@php
    $baseClasses = 'flex animate-rise items-start justify-between gap-6';
    if ($isCompact) {
        $compactClasses = 'md:-ml-[36px] py-4 pr-6 pl-6 md:pl-[44px] border-t border-b';
        if ($colorStyle) {
            $compactClasses .= ' ' . $colorStyle['bg'] . ' ' . $colorStyle['border'];
        } else {
            $compactClasses .= ' border-line bg-surface-2';
        }
        $headerClasses = $compactClasses . ' ' . $baseClasses . ' mb-4';
    } else {
        $headerClasses = 'mb-8 ' . $baseClasses;
    }
@endphp

<header {{ $attributes->merge(['class' => $headerClasses]) }}>
    <div>
        @if($eyebrow)
            <p class="mb-2.5 flex items-center gap-2 font-mono text-[11px] tracking-[0.14em] text-ink-soft">
                <span class="h-2 w-2 {{ $colorStyle ? $colorStyle['accent'] : 'bg-accent' }}"></span>{{ $eyebrow }}
            </p>
        @endif

        <h1 class="font-display font-bold leading-tight tracking-tight {{ $isCompact ? 'text-xl md:text-2xl' : 'text-2xl md:text-3xl' }}">{{ $title }}</h1>

        @if($sub)
            <p class="mt-2.5 max-w-[52ch] text-sm leading-relaxed text-ink-soft">{{ $sub }}</p>
        @endif
    </div>

    @if(isset($actions))
        <div class="shrink-0 my-auto">
            {{ $actions }}
        </div>
    @elseif(count($lines))
        <x-exam.stamp :lines="$lines" :fresh="$fresh" />
    @endif
</header>
