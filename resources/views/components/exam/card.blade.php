@props(['padded' => true])

<section {{ $attributes->merge(['class' => 'overflow-hidden rounded-md border border-line bg-surface shadow-[0_1px_2px_rgba(20,24,34,0.04),0_18px_40px_-30px_rgba(20,24,34,0.25)]']) }}>
    @isset($header)
        <header class="flex items-center justify-between gap-3 border-b border-line bg-surface-2 px-5 py-3.5">
            {{ $header }}
        </header>
    @endisset

    <div class="{{ $padded ? 'p-5' : '' }}">{{ $slot }}</div>

    @isset($footer)
        <footer class="border-t border-line bg-surface-2 px-5 py-4">
            {{ $footer }}
        </footer>
    @endisset
</section>