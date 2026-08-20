<div>
    {{-- Toast Container (Alpine driven) --}}
    <div x-data="{ toasts: [] }"
         x-on:show-toast.window="toasts.push({ message: $event.detail.message, type: $event.detail.type, id: Date.now() }); setTimeout(() => toasts = toasts.filter(t => t.id !== $event.detail.id), 3000)"
         class="fixed bottom-20 right-4 z-50 flex flex-col gap-2">
        <template x-for="toast in toasts" :key="toast.id">
            <div x-show="true" x-transition class="px-4 py-3 rounded-lg shadow-lg text-white text-sm font-medium flex items-center gap-2"
                 :class="toast.type === 'success' ? 'bg-green-600' : 'bg-red-600'">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                <span x-text="toast.message"></span>
            </div>
        </template>
    </div>

    <div class="min-h-screen bg-gray-50 dark:bg-gray-950 pb-28">
        <div class="container mx-auto px-4 py-6 max-w-4xl">

            <div class="mb-6 flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Bulk Edit Questions</h1>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        {{ $academicTopic->name }} &mdash; <span class="text-indigo-500">Drafts auto-save in session</span>
                    </p>
                </div>
                {{-- wire:navigate prevents full page reload --}}
{{-- Use the public ID properties directly from the Livewire component --}}
<a href="{{ route('multiple-choice-questions.index', [
    'academic_group'   => $academicGroupId,
    'academic_level'   => $academicLevelId,
    'academic_subject' => $academicSubjectId,
    'academic_topic'   => $academicTopicId,
]) }}" wire:navigate class="text-sm text-indigo-600 hover:text-indigo-800 font-medium">
    &larr; Back to Index
</a>
            </div>

            {{-- Search --}}
            <div class="mb-5 relative">
                <svg class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 11A6 6 0 1 0 5 11a6 6 0 0 0 12 0z"/>
                </svg>
                <input type="text" wire:model.blur="search" placeholder="Search questions…"
                       class="w-full rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 pl-9 pr-10 py-2.5 text-sm text-gray-900 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
            </div>

            @if($questions->isEmpty())
                <div class="rounded-2xl border border-dashed border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 p-16 text-center">
                    <p class="text-base font-semibold text-gray-700 dark:text-gray-300">No questions found.</p>
                </div>
            @else
                <div class="space-y-5">
                    @foreach($questions as $q)
                        @php
                            $state = $states[$q->id];
                            $isSaved = $state['is_saved'] ?? true;
                        @endphp

                        <div class="relative rounded-2xl border-2 bg-white dark:bg-gray-900 shadow-sm overflow-hidden transition-all duration-300"
                             :class="{{ $isSaved ? "'border-green-500 dark:border-green-500'" : "'border-yellow-400 dark:border-yellow-500'" }}">

                            {{-- Visual Cue: Status Badge --}}
                            <div class="absolute top-4 right-4 flex items-center gap-1.5 text-xs font-semibold z-10"
                                 :class="{{ $isSaved ? "'text-green-600 dark:text-green-400'" : "'text-yellow-600 dark:text-yellow-400'" }}">
                                <template x-if="{{ $isSaved ? 'true' : 'false' }}">
                                    <span class="flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                        Saved
                                    </span>
                                </template>
                                <template x-if="{{ $isSaved ? 'false' : 'true' }}">
                                    <span class="flex items-center gap-1">
                                        <svg class="w-4 h-4 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        Unsaved Draft
                                    </span>
                                </template>
                            </div>

                            <div class="pl-5 pr-6 py-5">
                                <div class="flex items-center gap-3 mb-4">
                                    <span class="flex h-7 w-7 items-center justify-center rounded-full bg-gray-100 dark:bg-gray-800 text-xs font-bold text-gray-500 dark:text-gray-400">
                                        {{ $loop->iteration + ($questions->currentPage() - 1) * $questions->perPage() }}
                                    </span>
                                    <span class="text-sm font-semibold text-gray-700 dark:text-gray-300">ID: {{ $q->id }}</span>
                                </div>

                                {{-- 🌟 RICH TEXT EDITOR INTEGRATION --}}
                                <div class="mb-5">
                                    <x-form.rich-editor
                                        name="question_{{ $q->id }}"
                                        livewire="states.{{ $q->id }}.question"
                                        :value="$state['question']"
                                        label="Question"
                                        :height="200"
                                    />
                                </div>

                                {{-- MCQ options --}}
                                <div class="space-y-3 mb-5">
                                    <p class="text-xs font-semibold uppercase tracking-wide text-gray-400 dark:text-gray-500">Options</p>
                                    @foreach(['a', 'b', 'c', 'd', 'e'] as $opt)
                                        <div class="flex items-start gap-3 rounded-xl border p-3 transition-colors"
                                             :class="{{ strtoupper($state['answer']) === strtoupper($opt) ? "'border-indigo-400 bg-indigo-50 dark:border-indigo-600 dark:bg-indigo-900/30'" : "'border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50'" }}">

                                            <label class="flex h-5 w-5 mt-1 shrink-0 cursor-pointer items-center justify-center">
                                                <input type="radio" wire:model.live="states.{{ $q->id }}.answer" value="{{ strtoupper($opt) }}"
                                                       class="h-4 w-4 cursor-pointer text-indigo-600 border-gray-300 dark:border-gray-600 focus:ring-indigo-500">
                                            </label>

                                            <span class="mt-0.5 flex h-6 w-6 shrink-0 items-center justify-center rounded-full text-xs font-bold"
                                                  :class="{{ strtoupper($state['answer']) === strtoupper($opt) ? "'bg-indigo-600 text-white'" : "'bg-gray-200 dark:bg-gray-700 text-gray-600 dark:text-gray-400'" }}">
                                                {{ strtoupper($opt) }}
                                            </span>

                                            <input type="text" wire:model.blut="states.{{ $q->id }}.option_{{ $opt }}" placeholder="Option {{ strtoupper($opt) }}"
                                                   class="flex-1 bg-transparent text-sm text-gray-900 dark:text-white placeholder-gray-400 border-0 focus:ring-0 p-0">
                                        </div>
                                    @endforeach
                                </div>

                                {{-- Difficulty + Score + Individual Save --}}
                                <div class="flex flex-wrap items-center justify-between gap-4 pt-4 border-t border-gray-100 dark:border-gray-800">
                                    <div class="flex items-center gap-4">
                                        <div class="flex items-center gap-2">
                                            <label class="text-xs font-medium text-gray-500 dark:text-gray-400">Difficulty</label>
                                            <select wire:model.live="states.{{ $q->id }}.difficulty_level"
                                                    class="rounded-lg border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-xs text-gray-700 dark:text-gray-300 py-1.5 pr-7 focus:ring-indigo-500 focus:border-indigo-500">
                                                <option value="easy">Easy</option>
                                                <option value="medium">Medium</option>
                                                <option value="hard">Hard</option>
                                                <option value="difficult">Difficult</option>
                                            </select>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <label class="text-xs font-medium text-gray-500 dark:text-gray-400">Score</label>
                                            <input type="number" wire:model.blur="states.{{ $q->id }}.score" min="0.5" step="0.5"
                                                   class="w-20 rounded-lg border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-xs text-gray-700 dark:text-gray-300 py-1.5 text-center focus:ring-indigo-500 focus:border-indigo-500">
                                        </div>
                                    </div>

                                    <button type="button" wire:click="saveSingle({{ $q->id }})"
                                            wire:loading.attr="disabled"
                                            wire:target="saveSingle({{ $q->id }}), states.{{ $q->id }}.question, states.{{ $q->id }}.option_a, states.{{ $q->id }}.option_b, states.{{ $q->id }}.option_c, states.{{ $q->id }}.option_d, states.{{ $q->id }}.option_e, states.{{ $q->id }}.score">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/></svg>
                                        <span wire:loading.remove wire:target="saveSingle({{ $q->id }})">Save Question</span>
                                        <span wire:loading wire:target="saveSingle({{ $q->id }})">Saving...</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Pagination (No reload) --}}
                <div class="mt-6">
                    {{ $questions->links() }}
                </div>
            @endif
        </div>
    </div>



    {{-- Fixed Bottom Bar --}}
    <div class="fixed bottom-0 inset-x-0 z-40 border-t border-gray-200 dark:border-gray-700 bg-white/95 dark:bg-gray-900/95 backdrop-blur-sm">
        <div class="container mx-auto px-4 max-w-4xl flex items-center justify-between py-3">
            <p class="text-sm text-gray-500 dark:text-gray-400">
                {{ $questions->total() }} total questions
            </p>
            <button type="button" wire:click="saveAll" wire:loading.attr="disabled" wire:target="saveAll"
                    class="inline-flex items-center gap-2 rounded-lg bg-green-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-green-700 transition-colors disabled:opacity-50">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                <span wire:loading.remove wire:target="saveAll">Save All Changes</span>
                <span wire:loading wire:target="saveAll">Saving All...</span>
            </button>
        </div>
    </div>
</div>
