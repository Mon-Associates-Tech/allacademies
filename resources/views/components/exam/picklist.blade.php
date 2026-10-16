@props(['items', 'action', 'labelKey' => 'name', 'subKey' => null, 'empty' => null, 'absolute' => true])

@if($items->isNotEmpty() || $empty !== null)
    <div x-data="{ open: true }" x-show="open" x-transition @click.outside="open = false" @keydown.escape.window="open = false" style="display:none;">
        @if($items->isNotEmpty())
            <div class="{{ $absolute ? 'absolute z-20 mt-1 w-full' : 'mt-2' }} max-h-48 overflow-y-auto rounded-md border border-line bg-surface shadow-lg">
                @foreach($items as $item)
                    <button type="button" wire:click="{{ $action }}({{ $item->id }})"
                            class="flex w-full items-baseline justify-between gap-2 border-b border-line px-3 py-2 text-left text-sm text-ink transition last:border-b-0 hover:bg-primary/5">
                        <span>{{ $item->{$labelKey} }}</span>
                        @if($subKey)
                            <span class="font-mono text-[10.5px] text-muted">{{ $item->{$subKey} }}</span>
                        @endif
                    </button>
                @endforeach
            </div>
        @elseif($empty !== null)
            <div class="{{ $absolute ? 'absolute z-20 mt-1 w-full' : 'mt-2' }} rounded-md border border-line bg-surface px-3 py-2 text-sm text-muted shadow-lg">{{ $empty }}</div>
        @endif
    </div>
@endif