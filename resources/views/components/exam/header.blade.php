@props([
    'eyebrow' => null,
    'title',
    'sub' => null,
    'lines' => [],     // stamp words, e.g. ['EXAM','IDENTITY']
    'fresh' => false,  // re-stamp animation (e.g. right after saving)
])

<header {{ $attributes->merge(['class' => 'mb-8 flex animate-rise items-start justify-between gap-6']) }}>
    <div>
        @if($eyebrow)
            <p class="mb-2.5 flex items-center gap-2 font-mono text-[11px] tracking-[0.14em] text-ink-soft">
                <span class="h-2 w-2 bg-accent"></span>{{ $eyebrow }}
            </p>
        @endif

        <h1 class="font-display text-3xl font-bold leading-tight tracking-tight md:text-4xl">{{ $title }}</h1>

        @if($sub)
            <p class="mt-2.5 max-w-[52ch] text-sm leading-relaxed text-ink-soft">{{ $sub }}</p>
        @endif
    </div>

    @if(count($lines))
        <x-exam.stamp :lines="$lines" :fresh="$fresh" />
    @endif
</header>