@props(['items', 'removeAction' => 'removeSubject', 'withMeta' => false])

@if($items->isNotEmpty())
    <div class="mb-2 flex flex-wrap gap-1.5">
        @foreach($items as $subject)
            <span wire:key="sel-{{ $subject->id }}"
                  class="inline-flex items-center gap-1.5 rounded-full bg-primary/10 py-0.5 pl-2.5 pr-1.5 text-xs font-medium text-primary ring-1 ring-inset ring-primary/20">
                {{ $subject->name }}
                @if($withMeta && $subject->academicLevel)
                    <span class="opacity-70">({{ $subject->academicLevel->academicGroup->name ?? 'N/A' }} • {{ $subject->academicLevel->name }})</span>
                @endif
                <button type="button" wire:click="{{ $removeAction }}({{ $subject->id }})"
                        aria-label="Remove {{ $subject->name }}"
                        class="grid h-4 w-4 place-items-center rounded-full transition hover:bg-accent/15 hover:text-accent">&times;</button>
            </span>
        @endforeach
    </div>
@endif