@props(['field', 'label', 'sortBy', 'direction', 'action' => 'toggleSort'])

<th class="whitespace-nowrap px-4 py-3 text-left">
    <button type="button" wire:click="{{ $action }}('{{ $field }}')"
            class="inline-flex items-center gap-1.5 font-mono text-[10px] font-semibold uppercase tracking-[0.14em] transition {{ $sortBy === $field ? 'text-ink' : 'text-muted hover:text-ink' }}">
        {{ $label }}
        <span class="flex flex-col items-center gap-px leading-none" aria-hidden="true">
            <svg class="h-1.5 w-1.5 {{ $sortBy === $field && $direction === 'asc' ? 'text-accent' : 'text-line-strong' }}" viewBox="0 0 8 6" fill="currentColor"><path d="M4 0l4 6H0z"/></svg>
            <svg class="h-1.5 w-1.5 {{ $sortBy === $field && $direction === 'desc' ? 'text-accent' : 'text-line-strong' }}" viewBox="0 0 8 6" fill="currentColor"><path d="M4 6L0 0h8z"/></svg>
        </span>
    </button>
</th>
