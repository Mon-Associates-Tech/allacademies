@props(['tabs' => [], 'active' => null, 'property' => 'activeTab'])

<nav {{ $attributes->merge(['class' => 'mb-8 flex flex-wrap gap-1 border-b border-line']) }}>
    @foreach($tabs as $key => $label)
        <button type="button"
                wire:click="$set('{{ $property }}', '{{ $key }}')"
                class="relative -mb-px rounded-t border px-4 py-2.5 font-mono text-[11px] font-semibold uppercase tracking-[0.12em] transition
                    {{ $active === $key
                        ? 'border-line border-b-transparent bg-paper text-ink'
                        : 'border-transparent text-muted hover:text-ink' }}">
            @if($active === $key)
                <span class="absolute inset-x-0 top-0 h-0.5 bg-accent"></span>
            @endif
            {{ $label }}
        </button>
    @endforeach
</nav>