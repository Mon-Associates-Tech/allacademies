<div>
    <x-exam.callout tone="primary" class="mb-8">
        <div class="flex items-center gap-3">
            <span class="flex items-center justify-center w-8 h-8 bg-primary text-on-primary rounded-full text-sm font-bold shrink-0">3</span>
            <div>
                <h3 class="font-bold text-ink">Table of Contents</h3>
                <p class="text-sm text-ink-soft">Organize your book into chapters (Optional)</p>
            </div>
        </div>
    </x-exam.callout>

    @if(empty($tableOfContents))
        <x-exam.empty title="No chapters yet" description="Get started by auto-generating chapters from your page count or adding them manually">
            <div class="mt-6 flex justify-center gap-3">
                <x-exam.button wire:click="generateTableOfContents">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                    Auto-generate
                </x-exam.button>
                <x-exam.button variant="ghost" wire:click="addChapter">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Add Manually
                </x-exam.button>
            </div>
        </x-exam.empty>
        @else
            <div class="flex justify-between items-center mb-4">
                <h4 class="text-lg font-semibold text-ink">Chapters ({{ count($tableOfContents) }})</h4>
                <div class="flex items-center gap-2">
                    <x-exam.button variant="ghost" wire:click="generateTableOfContents">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                        Regenerate
                    </x-exam.button>
                    <x-exam.button wire:click="addChapter">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        Add Chapter
                    </x-exam.button>
                </div>
            </div>

            @foreach($tableOfContents as $index => $chapter)
                <x-exam.card class="mb-3">
                    <div class="flex items-center justify-between cursor-pointer"
                         wire:click="toggleChapter({{ $index }})">
                        <div class="flex items-center gap-3">
                            <x-exam.badge tone="primary">Chapter {{ $chapter['chapter'] }}</x-exam.badge>
                            <span class="text-sm font-medium text-ink">{{ $chapter['title'] }}</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="font-mono text-[11px] text-muted">Pages {{ $chapter['page_start'] }}–{{ $chapter['page_end'] }}</span>
                            <x-exam.button variant="danger" wire:click.stop="removeChapter({{ $index }})" class="p-1.5">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                            </x-exam.button>
                            <svg class="w-4 h-4 text-muted transition-transform {{ in_array($index, $expandedChapters) ? 'rotate-180' : '' }}"
                                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </div>
                    </div>

                    @if(in_array($index, $expandedChapters))
                        <div class="border-t border-line pt-4 mt-4 space-y-4">
                            <div class="grid grid-cols-2 gap-0">
                                <x-exam.field label="Chapter Title" variant="bare">
                                    <x-exam.input type="text" wire:model="tableOfContents.{{ $index }}.title" />
                                </x-exam.field>
                                <div class="grid grid-cols-2 gap-0">
                                    <x-exam.field label="Start Page" variant="bare">
                                        <x-exam.input type="number" wire:model="tableOfContents.{{ $index }}.page_start" />
                                    </x-exam.field>
                                    <x-exam.field label="End Page" variant="bare">
                                        <x-exam.input type="number" wire:model="tableOfContents.{{ $index }}.page_end" />
                                    </x-exam.field>
                                </div>
                            </div>
                            <x-exam.field label="Description" variant="bare">
                                <x-exam.input type="text" wire:model="tableOfContents.{{ $index }}.description" placeholder="Brief chapter description..." />
                            </x-exam.field>
                        </div>
                    @endif
                </x-exam.card>
            @endforeach
        @endif
</div>
