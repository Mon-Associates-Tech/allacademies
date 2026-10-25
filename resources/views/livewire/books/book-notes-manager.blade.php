<x-exam.page variant="compact">
    <x-alert.token-subscription-banner variant="full-page"/>

    {{-- Tabs --}}
    @php
        $examTabs = [
            'book-notes' => Str::limit($book->title, 40),
            'all-notes' => 'All Notes',
        ];
    @endphp
    <x-exam.tabs :tabs="$examTabs" :active="$activeTab" />

    {{-- Search Bar --}}
    <div class="relative mb-6">
        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
            <svg class="h-4 w-4 text-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
        </div>
        <x-exam.input type="text" wire:model.live.debounce.300ms="searchTerm" placeholder="Search notes..." class="pl-10"/>
        @if($searchTerm)
            <button wire:click="$set('searchTerm', '')"
                    class="absolute inset-y-0 right-0 pr-3 flex items-center text-muted hover:text-ink">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        @endif
    </div>

    {{-- Book Notes Tab --}}
    <div class="{{ $activeTab === 'book-notes' ? '' : 'hidden' }}">
        <x-exam.header
            :title="$book->title"
            :sub="$book->author ? 'by ' . $book->author_name : null"
            eyebrow="BOOK NOTES"
            variant="compact"
        />

        {{-- Add New Note Card --}}
        <x-exam.card class="mb-8">
            <x-slot:header>
                <div class="flex items-center">
                    <svg class="w-5 h-5 text-primary mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    <h3 class="font-semibold text-ink">Add New Note</h3>
                </div>
            </x-slot:header>

            <div class="space-y-4">
                <x-exam.field :label="'Title'" tag="optional">
                    <x-exam.input type="text" wire:model="newNoteTitle" placeholder="Note title (optional)"/>
                </x-exam.field>

                <x-exam.field :label="'Background Color'" tag="style">
                    <div class="flex flex-wrap gap-2">
                        @foreach(\App\Models\Note::getBackgroundColors() as $key => $color)
                            <button type="button"
                                    wire:click="$set('newNoteColor', '{{ $key }}')"
                                    class="px-3 py-1.5 rounded-md border text-xs font-medium transition-all {{ $color['class'] }} {{ $newNoteColor === $key ? 'border-primary ring-2 ring-primary/30' : 'border-line-strong' }}">
                                {{ $color['name'] }}
                            </button>
                        @endforeach
                    </div>
                </x-exam.field>

                <x-form.markdown-editor
                    name="newNoteContent"
                    :height="200"
                    label=""
                />

                <div class="flex justify-end">
                    <x-exam.button
                        client-loading
                        @click="
                            const content = tinymce.activeEditor ? tinymce.activeEditor.getContent() : '';
                            $wire.set('newNoteContent', content);
                            $wire.saveNote(content, $wire.newNoteTitle);
                        ">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Save Note
                    </x-exam.button>
                </div>
            </div>
        </x-exam.card>

        {{-- Notes List --}}
        <div class="mb-4 flex items-center justify-between">
            <h3 class="font-display text-base font-bold text-ink">
                Your Notes <span class="font-mono text-[11px] font-semibold text-muted">({{ $this->bookNotes->total() }})</span>
            </h3>
        </div>

        @if($this->bookNotes->isEmpty())
            <x-exam.empty
                :title="$searchTerm ? 'No notes found' : 'No notes yet'"
                :description="$searchTerm ? 'Try adjusting your search terms.' : 'Get started by writing your first note about this book.'"
            />
        @else
            <div class="space-y-4">
                @foreach($this->bookNotes as $note)
                    @php
                        $isExpanded = $this->isNoteExpanded($note->id);
                        $contentLength = strlen(strip_tags($note->content));
                        $hasLongContent = $contentLength > 300;
                    @endphp
                    <x-exam.card :padded="true">
                        <div class="{{ $note->getBackgroundClass() }} -mx-5 -mt-5 -mb-5 p-5">
                            @if($editingNoteId === $note->id)
                                <div class="space-y-4">
                                    <x-exam.field :label="'Title'" tag="optional">
                                        <x-exam.input type="text" wire:model="editingTitle" placeholder="Note title (optional)"/>
                                    </x-exam.field>

                                    <x-exam.field :label="'Background Color'" tag="style">
                                        <div class="flex flex-wrap gap-2">
                                            @foreach(\App\Models\Note::getBackgroundColors() as $key => $color)
                                                <button type="button"
                                                        wire:click="$set('editingColor', '{{ $key }}')"
                                                        class="px-3 py-1.5 rounded-md border text-xs font-medium transition-all {{ $color['class'] }} {{ $editingColor === $key ? 'border-primary ring-2 ring-primary/30' : 'border-line-strong' }}">
                                                    {{ $color['name'] }}
                                                </button>
                                            @endforeach
                                        </div>
                                    </x-exam.field>

                                    <x-form.markdown-editor
                                        name="editingContent"
                                        :value="$editingContent"
                                        :height="200"
                                        label=""
                                    />

                                    <div class="flex justify-end gap-2">
                                        <x-exam.button variant="ghost" @click="$wire.cancelEdit()">
                                            Cancel
                                        </x-exam.button>
                                        <x-exam.button
                                            client-loading
                                            @click="
                                                const content = tinymce.activeEditor ? tinymce.activeEditor.getContent() : '';
                                                $wire.set('editingContent', content);
                                                $wire.updateNote(content, $wire.editingTitle);
                                            ">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                                            </svg>
                                            Update Note
                                        </x-exam.button>
                                    </div>
                                </div>
                            @else
                                <h3 class="font-display text-lg font-bold text-ink mb-3">{{ $note->title ?? 'Untitled' }}</h3>
                                <div class="{{ $hasLongContent && !$isExpanded ? 'max-h-40 overflow-hidden relative' : '' }}">
                                    <x-ui.latex
                                        :content="$note->content"
                                        class="prose prose-sm prose-gray dark:prose-invert max-w-none"
                                    />
                                    @if($hasLongContent && !$isExpanded)
                                        <div class="absolute bottom-0 left-0 right-0 h-20 bg-gradient-to-t from-current to-transparent pointer-events-none opacity-30"></div>
                                    @endif
                                </div>

                                @if($hasLongContent)
                                    <x-exam.link-button wire:click="toggleNote({{ $note->id }})">
                                        {{ $isExpanded ? 'Read less' : 'Read more' }}
                                        <svg class="w-3.5 h-3.5 ml-1 transition-transform {{ $isExpanded ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                        </svg>
                                    </x-exam.link-button>
                                @endif

                                <div class="flex items-center justify-between pt-3 mt-3 border-t border-line">
                                    <div class="flex items-center font-mono text-[10.5px] text-muted">
                                        <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        <span>{{ $note->updated_at->diffForHumans() }}</span>
                                    </div>
                                    <div class="flex gap-2">
                                        <x-exam.link-button wire:click="editNote({{ $note->id }})" tone="primary">
                                            <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                            </svg>
                                            Edit
                                        </x-exam.link-button>
                                        <x-exam.link-button
                                            wire:click="deleteNote({{ $note->id }})"
                                            wire:confirm="Are you sure you want to delete this note? This action cannot be undone."
                                            tone="danger">
                                            <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                            Delete
                                        </x-exam.link-button>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </x-exam.card>
                @endforeach

                {{-- Pagination --}}
                <div class="mt-6">
                    {{ $this->bookNotes->links() }}
                </div>
            </div>
        @endif
    </div>

    {{-- All Notes Tab --}}
    <div class="{{ $activeTab === 'all-notes' ? '' : 'hidden' }}">
        <div class="mb-4 flex items-center justify-between">
            <h3 class="font-display text-base font-bold text-ink">
                All Your Notes <span class="font-mono text-[11px] font-semibold text-muted">({{ $this->userNotes->total() }})</span>
            </h3>
        </div>

        @if($this->userNotes->isEmpty())
            <x-exam.empty
                :title="$searchTerm ? 'No notes found' : 'No notes yet'"
                :description="$searchTerm ? 'Try adjusting your search terms.' : 'Start taking notes on books you read.'"
            />
        @else
            <div class="space-y-4">
                @foreach($this->userNotes as $note)
                    @php
                        $isExpanded = $this->isNoteExpanded($note->id);
                        $contentLength = strlen(strip_tags($note->content));
                        $hasLongContent = $contentLength > 300;
                    @endphp
                    <x-exam.card>
                        @if($editingNoteId === $note->id)
                            <div class="space-y-4">
                                <div class="flex items-center justify-between mb-3">
                                    <h4 class="font-semibold text-ink flex items-center min-w-0">
                                        <svg class="w-5 h-5 mr-2 text-primary flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                        </svg>
                                        <span class="truncate" title="{{ $note->book->title ?? 'Untitled Book' }}">
                                            {{ $note->book->title ?? 'Untitled Book' }}
                                        </span>
                                    </h4>
                                </div>
                                <x-form.markdown-editor
                                    name="editingContent"
                                    :value="$editingContent"
                                    :height="200"
                                    label=""
                                />
                                <div class="flex justify-end gap-2">
                                    <x-exam.button variant="ghost" @click="$wire.cancelEdit()">
                                        Cancel
                                    </x-exam.button>
                                    <x-exam.button
                                        client-loading
                                        @click="
                                            const content = tinymce.activeEditor ? tinymce.activeEditor.getContent() : '';
                                            $wire.set('editingContent', content);
                                            $wire.updateNote(content);
                                        ">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                                        </svg>
                                        Update Note
                                    </x-exam.button>
                                </div>
                            </div>
                        @else
                            <div class="flex items-start justify-between mb-3 gap-3">
                                <h4 class="font-semibold text-ink flex items-center min-w-0 flex-1">
                                    <svg class="w-5 h-5 mr-2 text-primary flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                    </svg>
                                    <span class="truncate" title="{{ $note->book->title ?? 'Untitled Book' }}">
                                        {{ $note->book->title ?? 'Untitled Book' }}
                                    </span>
                                </h4>
                                @if($note->book)
                                    <a href="{{ route('books.show', $note->book) }}"
                                       class="whitespace-nowrap border-b border-dashed border-transparent font-mono text-[11px] font-semibold uppercase tracking-wider text-primary hover:border-current hover:text-primary-hi focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary transition flex-shrink-0 flex items-center">
                                        @if($note->book->getRawOriginal('cover_image'))
                                            <img src="{{ $note->book->cover_image }}"
                                                 alt="{{ $note->book->title }}"
                                                 class="w-5 h-7 object-cover rounded mr-1.5 flex-shrink-0">
                                        @else
                                            <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                            </svg>
                                        @endif
                                        View Book
                                    </a>
                                @endif
                            </div>

                            <div class="{{ $hasLongContent && !$isExpanded ? 'max-h-40 overflow-hidden relative' : '' }}">
                                <x-ui.latex
                                    :content="$note->content"
                                    class="prose prose-sm prose-gray dark:prose-invert max-w-none"
                                />
                                @if($hasLongContent && !$isExpanded)
                                    <div class="absolute bottom-0 left-0 right-0 h-20 bg-gradient-to-t from-surface to-transparent pointer-events-none"></div>
                                @endif
                            </div>

                            @if($hasLongContent)
                                <x-exam.link-button wire:click="toggleNote({{ $note->id }})">
                                    {{ $isExpanded ? 'Read less' : 'Read more' }}
                                    <svg class="w-3.5 h-3.5 ml-1 transition-transform {{ $isExpanded ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </x-exam.link-button>
                            @endif

                            <div class="flex items-center justify-between pt-3 mt-3 border-t border-line">
                                <div class="flex items-center font-mono text-[10.5px] text-muted">
                                    <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <span>{{ $note->updated_at->diffForHumans() }}</span>
                                </div>
                                <div class="flex gap-2">
                                    <x-exam.link-button wire:click="editNote({{ $note->id }})" tone="primary">
                                        <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                        Edit
                                    </x-exam.link-button>
                                    <x-exam.link-button
                                        wire:click="deleteNote({{ $note->id }})"
                                        wire:confirm="Are you sure you want to delete this note? This action cannot be undone."
                                        tone="danger">
                                        <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                        Delete
                                    </x-exam.link-button>
                                </div>
                            </div>
                        @endif
                    </x-exam.card>
                @endforeach

                {{-- Pagination --}}
                <div class="mt-6">
                    {{ $this->userNotes->links() }}
                </div>
            </div>
        @endif
    </div>
</x-exam.page>
