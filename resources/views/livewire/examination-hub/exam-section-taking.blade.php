{{-- ─────────────────────────────────────────────────────────────────────────
COMPONENT SCRIPT  — runs once when this Livewire component is initialised.
$wire is available here and refers to this component instance.
 NOTE: Do NOT use @push or @assets inside a Livewire component view.
 @push requires the parent layout's View Factory context (startPush state)
 which does not exist during Livewire's isolated AJAX re-renders, causing
 "Undefined property: Illuminate\View\Factory::$startPush".
 Load all external scripts from the parent controller view (section.blade.php).

 ExamSessionSync is already initialised by section.blade.php; no need
 to duplicate it here.
────────────────────────────────────────────────────────────────────────── --}}
@script
<script>
// ── Initialize component reference for vanilla JS ──────────────────────
// Make the Livewire component available to section.blade.php timer code
window.examSectionComponent = $wire;

// ── Auto-submit redirect ─────────────────────────────────────────────────
// Fired by ExamSectionTaking::performAutoSubmit() after the submission has
// been written to the database and the grading job has been queued.
//
// The parent section view (section_blade.php) already handles showing the
// countdown modal via Livewire.on('examAutoSubmitted', ...).  This $wire
// listener is the Livewire 3 idiomatic layer that actually performs the
// navigation, guaranteeing it fires even if the parent-view listener
// registers late (e.g. slow livewire:initialized event).
$wire.on('examAutoSubmitted', (payload) => {
    // Suppress the beforeunload confirmation so the redirect is not blocked.
    window.hasUnsavedChanges = false;

    const redirectUrl = payload?.redirectUrl;
    if (!redirectUrl) return;

    // 3.1 s: the parent modal counts down 3 s before navigating, so this
    // fires 100 ms after the modal countdown ends as a safety net.  If the
    // parent already navigated away this is a no-op.
    setTimeout(() => {
        window.location.href = redirectUrl;
    }, 3100);
});
</script>
@endscript

<div class="min-h-screen bg-slate-50 dark:bg-slate-950" style="font-family: 'Georgia', 'Times New Roman', serif;">
    {{-- ═══════════════════════════════════════════════════════════
         STATE 1 · SECTION INFO OVERLAY
    ════════════════════════════════════════════════════════════ --}}
    @if($showSectionInfo)
        <div class="flex items-center justify-center min-h-screen px-4 py-8 bg-slate-100 dark:bg-slate-950">
            <div class="dark:bg-slate-900 bg-white w-full max-w-lg overflow-hidden"
                style="border-radius: 2px; box-shadow: 0 0 0 1px rgba(0,0,0,0.06), 0 20px 60px -10px rgba(0,0,0,0.15), 0 4px 16px rgba(0,0,0,0.06);">

                {{-- Accent bar --}}
                <div class="h-1 w-full" style="background: linear-gradient(90deg, #b45309, #d97706, #fbbf24);"></div>

                {{-- Header --}}
                <div class="px-8 pt-7 pb-5 border-b border-slate-100 dark:border-slate-800 flex items-start justify-between gap-4">
                    <div>
                        <p class="text-xs font-medium tracking-widest uppercase text-amber-600 dark:text-amber-500 mb-1"
                          style="font-family: 'system-ui', sans-serif; letter-spacing: 0.15em;">Examination Section</p>
                        <h2 class="text-2xl font-bold text-slate-900 dark:text-white leading-snug" style="letter-spacing: -0.02em;">
                            {{ $this->section->title }}
                        </h2>
                    </div>
                    <button wire:click="toggleSectionInfo"
                           class="mt-1 flex-shrink-0 w-8 h-8 flex items-center justify-center text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 border border-slate-200 dark:border-slate-700 transition-colors"
                           style="border-radius: 2px;">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <div class="px-8 py-7 space-y-6" style="font-family: 'system-ui', sans-serif;">
                    {{-- Error Display --}}
                    @if($errors->any())
                        <div class="p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded" style="border-radius: 2px;">
                            <div class="flex items-start gap-3">
                                <svg class="w-5 h-5 text-red-600 dark:text-red-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <div>
                                    <h4 class="text-sm font-semibold text-red-800 dark:text-red-300 mb-1">Error</h4>
                                    <ul class="text-sm text-red-700 dark:text-red-400 space-y-1">
                                        @foreach($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    @endif

                    @if($this->section->description)
                        <p class="text-sm text-slate-600 dark:text-slate-400 leading-relaxed">
                            {{ $this->section->description }}
                        </p>
                    @endif

                    {{-- Stats grid --}}
                    <div class="grid grid-cols-2 gap-3">
                        <div class="px-4 py-3 bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700" style="border-radius: 2px;">
                            <p class="text-xs text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1" style="font-size: 10px; letter-spacing: 0.1em;">Questions</p>
                            <p class="text-xl font-bold text-slate-900 dark:text-white" style="letter-spacing: -0.03em;">{{ $this->questions->count() }}</p>
                        </div>
                        <div class="px-4 py-3 bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700" style="border-radius: 2px;">
                            <p class="text-xs text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1" style="font-size: 10px; letter-spacing: 0.1em;">Section</p>
                            <p class="text-xl font-bold text-slate-900 dark:text-white" style="letter-spacing: -0.03em;">
                                {{ $sectionIndex + 1 }} <span class="text-sm font-normal text-slate-500">of {{ $this->exam->sections->count() }}</span>
                            </p>
                        </div>
                        @if($this->section->time_limit_minutes)
                            <div class="px-4 py-3 bg-amber-50 dark:bg-amber-900/20" style="border-radius: 2px; border: 1px solid rgba(180,83,9,0.15);">
                                <p class="text-xs text-amber-700 dark:text-amber-500 uppercase tracking-wider mb-1" style="font-size: 10px; letter-spacing: 0.1em;">Time Limit</p>
                                <p class="text-xl font-bold text-amber-800 dark:text-amber-400" style="letter-spacing: -0.03em;">{{ $this->section->time_limit_minutes }} <span class="text-sm font-normal">min</span></p>
                            </div>
                        @endif
                        <div class="px-4 py-3 bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700" style="border-radius: 2px;">
                            <p class="text-xs text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1" style="font-size: 10px; letter-spacing: 0.1em;">Type</p>
                            <p class="text-sm font-semibold text-slate-900 dark:text-white mt-1">{{ str_replace('_', ' ', ucfirst($this->section->question_type)) }}</p>
                        </div>
                    </div>

                    @if($this->section->instructions)
                        <div style="border-left: 3px solid #d97706; padding-left: 1rem;">
                            <h4 class="text-xs font-semibold text-amber-700 dark:text-amber-500 uppercase tracking-wider mb-2" style="font-size: 10px; letter-spacing: 0.12em;">Instructions</h4>
                            <div class="text-sm text-slate-700 dark:text-slate-300 leading-relaxed whitespace-pre-line">{{ $this->section->instructions }}</div>
                        </div>
                    @endif

                    <div class="pt-2 flex justify-end items-center gap-3">
                        @if($this->section->instructions)
                            <label class="inline-flex items-center gap-2 text-sm">
                                <input type="checkbox" wire:model="instructionsAcknowledged" class="h-4 w-4"/>
                                <span>I have read and understood the section instructions</span>
                            </label>
                        @endif
                        <div>
                            <button wire:click="startSection"
                                   @if($this->section->instructions) wire:loading.attr="disabled" @endif
                                   @if($this->section->instructions && !$this->instructionsAcknowledged) disabled @endif
                                   class="group relative inline-flex items-center gap-2 px-7 py-3 text-sm font-semibold text-white bg-slate-800 hover:bg-slate-700 dark:bg-slate-700 dark:hover:bg-slate-600 transition-all duration-200"
                                   style="border-radius: 2px; letter-spacing: 0.02em; box-shadow: 0 2px 8px rgba(0,0,0,0.2);">
                                Begin Section
                                <svg class="w-4 h-4 group-hover:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    @else

    {{-- ═══════════════════════════════════════════════════════════
         STATE 2 · EMPTY SECTION
    ════════════════════════════════════════════════════════════ --}}
    @if($this->questions->count() === 0)
        <div class="max-w-3xl mx-auto px-4 py-12" style="font-family: 'system-ui', sans-serif;">
            <div class="bg-white dark:bg-slate-900 overflow-hidden"
                style="border-radius: 2px; box-shadow: 0 0 0 1px rgba(0,0,0,0.06), 0 8px 32px rgba(0,0,0,0.08);">

                <div class="h-1" style="background: linear-gradient(90deg, #b45309, #d97706, #fbbf24);"></div>

                {{-- Exam title strip --}}
                <div class="px-8 py-6 border-b border-slate-200 dark:border-slate-800 bg-slate-800 dark:bg-slate-900">
                    <p class="text-xs tracking-widest text-amber-400 uppercase mb-1" style="letter-spacing: 0.15em;">Online Examination</p>
                    <h1 class="text-2xl font-bold text-white" style="letter-spacing: -0.02em; font-family: 'Georgia', serif;">{{ $this->exam->title }}</h1>
                    <p class="text-slate-400 text-sm mt-1">Section {{ $sectionIndex + 1 }} — {{ $this->section->title }}</p>
                </div>

                <div class="p-8 space-y-6">
                    @if($this->section->description)
                        <p class="text-sm text-slate-600 dark:text-slate-400 leading-relaxed">{{ $this->section->description }}</p>
                    @endif

                    @if($this->section->instructions)
                        <div class="p-4 bg-blue-50 dark:bg-blue-950/30 border-l-4 border-blue-400">
                            <h3 class="text-xs font-bold text-blue-800 dark:text-blue-300 uppercase tracking-wider mb-2">Section Instructions</h3>
                            <div class="text-sm text-blue-700 dark:text-blue-300 whitespace-pre-line leading-relaxed">{{ $this->section->instructions }}</div>
                        </div>
                    @endif

                    <div class="grid md:grid-cols-3 gap-3">
                        <div class="p-4 bg-slate-50 dark:bg-slate-800 border border-slate-100 dark:border-slate-700" style="border-radius: 2px;">
                            <p class="text-xs text-slate-500 uppercase tracking-wider mb-1">Question Type</p>
                            <p class="font-semibold text-slate-900 dark:text-white">{{ str_replace('_', ' ', ucfirst($this->section->question_type)) }}</p>
                        </div>
                        @if($this->section->time_limit_minutes)
                            <div class="p-4 bg-slate-50 dark:bg-slate-800 border border-slate-100 dark:border-slate-700" style="border-radius: 2px;">
                                <p class="text-xs text-slate-500 uppercase tracking-wider mb-1">Time Limit</p>
                                <p class="font-semibold text-slate-900 dark:text-white">{{ $this->section->time_limit_minutes }} minutes</p>
                            </div>
                        @endif
                        <div class="p-4 bg-slate-50 dark:bg-slate-800 border border-slate-100 dark:border-slate-700" style="border-radius: 2px;">
                            <p class="text-xs text-slate-500 uppercase tracking-wider mb-1">Expected</p>
                            <p class="font-semibold text-slate-900 dark:text-white">{{ $this->section->question_count }} Questions</p>
                        </div>
                    </div>

                    <div class="flex items-start gap-4 p-5 bg-amber-50 dark:bg-amber-950/30 border border-amber-200 dark:border-amber-800" style="border-radius: 2px;">
                        <div class="flex-shrink-0 w-10 h-10 rounded-full bg-amber-100 dark:bg-amber-900 flex items-center justify-center">
                            <svg class="h-5 w-5 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-semibold text-amber-900 dark:text-amber-200 mb-1">No Questions Available</h3>
                            <p class="text-sm text-amber-700 dark:text-amber-400 leading-relaxed">This section does not have any questions yet. The exam administrator may still be preparing the questions. Please check back later or contact support.</p>
                        </div>
                    </div>

                    @if($this->exam->instructions)
                        <div class="p-4 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700" style="border-radius: 2px;">
                            <h3 class="text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">General Exam Instructions</h3>
                            <div class="text-sm text-slate-600 dark:text-slate-400 whitespace-pre-line leading-relaxed">{{ $this->exam->instructions }}</div>
                        </div>
                    @endif

                    <div class="flex justify-between items-center pt-4 border-t border-slate-100 dark:border-slate-800">
                        <a href="{{ route('examination-hub.take.start', $this->exam) }}"
                           class="inline-flex items-center gap-2 px-4 py-2 text-sm text-slate-600 dark:text-slate-400 border border-slate-300 dark:border-slate-600 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors"
                           style="border-radius: 2px;">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"> <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/> </svg>
                            Back to Overview
                        </a>
                        @if($sectionIndex < $this->exam->sections->count() - 1)
                            <a href="{{ route('examination-hub.take.section', [$this->exam, $sectionIndex + 1]) }}"
                               class="inline-flex items-center gap-2 px-5 py-2 text-sm font-medium text-white bg-slate-800 hover:bg-slate-700 transition-colors"
                               style="border-radius: 2px;">
                                Skip to Next Section
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"> <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/> </svg>
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>

    @else



{{-- ═══════════════════════════════════════════════════════════
     STATE 3 · ACTIVE EXAMINATION VIEW
═══════════════════════════════════════════════════════════ --}}
<div class="flex flex-col h-screen" style="font-family: 'system-ui', -apple-system, sans-serif;">

    {{-- ── TOP HEADER BAR ── --}}
    <div class="flex-shrink-0 bg-white dark:bg-slate-950 border-b border-slate-200 dark:border-slate-800"
        style="box-shadow: 0 1px 0 rgba(0,0,0,0.06);">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 py-3">
            {{-- Desktop: single row layout --}}
            <div class="hidden sm:flex items-center justify-between gap-4">
                {{-- Exam / Section title --}}
                <div class="min-w-0 flex-1">
                    <h2 class="text-base font-bold text-slate-900 dark:text-white truncate" style="letter-spacing: -0.01em; font-family: 'Georgia', serif;">
                        {{ $this->exam->title }}
                    </h2>
                    <div class="flex items-center gap-2 mt-0.5">
                        <span class="text-xs text-slate-500 dark:text-slate-400">Section {{ $sectionIndex + 1 }}</span>
                        <span class="text-slate-300 dark:text-slate-700">·</span>
                        <span class="text-xs text-amber-600 dark:text-amber-400 font-medium">
                            Q{{ $currentQuestionIndex + 1 }} / {{ $this->questions->count() }}
                        </span>
                    </div>
                </div>

                <div class="flex items-center gap-3 flex-shrink-0">
                    {{-- TIMER (Desktop) --}}
                    <x-examination-hub.timer :timeRemaining="$timeRemaining ?? 0" />

                    {{-- Progress pill --}}
                    <div class="flex flex-col items-end gap-1">
                        <span class="text-xs text-slate-500 dark:text-slate-400">{{ $this->getAnsweredCount() }} answered</span>
                        <div class="w-28 h-1.5 bg-slate-200 dark:bg-slate-700 rounded-full overflow-hidden">
                            <div class="h-full rounded-full transition-all duration-500"
                                style="width: {{ $this->questions->count() > 0 ? ($this->getAnsweredCount() / $this->questions->count()) * 100 : 0 }}%; background: linear-gradient(90deg, #d97706, #fbbf24);"></div>
                        </div>
                    </div>
                
                    {{-- Section info toggle --}}
                    <button wire:click="toggleSectionInfo"
                           class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white border border-slate-300 dark:border-slate-700 hover:border-slate-400 dark:hover:border-slate-500 transition-all"
                           style="border-radius: 2px;">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Section Info
                    </button>

                    <x-snippets.theme-toggle />
                </div>
            </div>
            
            {{-- Mobile: two-row layout --}}
            <div class="sm:hidden space-y-2">
                {{-- Row 1: Title & Timer --}}
                <div class="flex items-center justify-between gap-2">
                    <div class="min-w-0 flex-1">
                        <h2 class="text-sm font-bold text-slate-900 dark:text-white truncate" style="letter-spacing: -0.01em; font-family: 'Georgia', serif;">
                            {{ $this->exam->title }}
                        </h2>
                        <div class="flex items-center gap-2 mt-0.5">
                            <span class="text-xs text-slate-500 dark:text-slate-400">Sec {{ $sectionIndex + 1 }}</span>
                            <span class="text-slate-300 dark:text-slate-700">·</span>
                            <span class="text-xs text-amber-600 dark:text-amber-400 font-medium">
                                Q{{ $currentQuestionIndex + 1 }}/{{ $this->questions->count() }}
                            </span>
                        </div>
                    </div>
                    {{-- TIMER (Mobile) --}}
                    <x-examination-hub.timer :timeRemaining="$timeRemaining ?? 0" :isMobile="true" />
                </div>
                
                {{-- Row 2: Actions --}}
                <div class="flex items-center justify-end gap-2">
                    <button wire:click="toggleSectionInfo"
                           class="inline-flex items-center gap-1 px-2 py-1.5 text-xs font-medium text-slate-600 dark:text-slate-300 border border-slate-300 dark:border-slate-700"
                           style="border-radius: 2px;">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </button>
                    <x-snippets.theme-toggle />
                </div>
            </div>
        </div>
    </div>

{{-- ... [Keep the rest of STATE 3 (Question Card, Navigator, Bottom Nav) exactly as it was] ... --}}

        {{-- ── SCROLLABLE CONTENT AREA ── --}}
        <div class="flex-1 overflow-y-auto bg-slate-100 dark:bg-slate-950"
            style="scrollbar-gutter: stable;">
            <div class="max-w-3xl mx-auto px-4 sm:px-6 py-4 sm:py-8">

                @php
                    $question = $this->questions[$currentQuestionIndex];
                @endphp

                {{-- ── QUESTION CARD ── --}}
                <div class="bg-white dark:bg-slate-900 mb-5 overflow-hidden"
                    wire:key="question-container-{{ $question->id }}"
                    style="border-radius: 2px; box-shadow: 0 0 0 1px rgba(0,0,0,0.06), 0 4px 24px rgba(0,0,0,0.06);">

                    {{-- Question header strip --}}
                    <div class="px-6 py-3 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between bg-slate-50 dark:bg-slate-800/50">
                        <div class="flex items-center gap-3">
                            <div class="w-7 h-7 flex items-center justify-center text-xs font-bold text-white"
                                style="background: linear-gradient(135deg, #b45309, #d97706); border-radius: 2px;">
                                {{ $currentQuestionIndex + 1 }}
                            </div>
                            <span class="text-xs hidden sm:inline font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider" style="letter-spacing: 0.08em;">
                                <span class="hidden sm:inline">Question </span> {{ $currentQuestionIndex + 1 }} of {{ $this->questions->count() }}
                            </span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="text-xs px-2 py-1 text-slate-600 dark:text-slate-300 bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700"
                                style="border-radius: 2px;">
                                {{ str_replace('_', ' ', ucfirst($question->type)) }}
                            </span>
                            <span class="text-xs px-2 py-1 text-amber-700 dark:text-amber-400 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800"
                                style="border-radius: 2px;">
                                {{ $question->marks }} {{ $question->marks === 1 ? 'mark' : 'marks' }}
                            </span>
                            {{-- Flag/Unflag button --}}
                            <button wire:click.stop="toggleFlagQuestion({{ $question->id }})"
                                   class="ml-2 inline-flex items-center gap-2 px-2 py-1 text-xs font-medium border rounded"
                                   :class="{ 'text-amber-600 border-amber-200 bg-amber-50': @js($this->submission->isFlagged($question->id)), 'text-slate-600 border-slate-200 bg-white': !@js($this->submission->isFlagged($question->id)) }">
                                @if($this->submission->isFlagged($question->id))
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"> <path d="M5 3a1 1 0 00-1 1v12l6-3 6 3V4a1 1 0 00-1-1H5z"/> </svg>
                                    <span class="sr-only">Flagged</span>
                                @else
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"> <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v14l7-3 7 3V3H5z"/> </svg>
                                    <span class="sr-only">Flag</span>
                                @endif
                            </button>
                        </div>
                    </div>

                    {{-- Question body --}}
                    <div class="px-4 sm:px-6 pt-4 sm:pt-6 pb-4 sm:pb-5">
                        {{-- Question text --}}
                        <div class="text-slate-800 dark:text-slate-200 mb-5 sm:mb-7 lh-base leading-base text-[1rem] sm:text-[1.05rem] font-serif"
                            wire:key="question-text-{{ $question->id }}">
                            <x-form.markdown-with-math :content="$question->getFormattedQuestion()" class="prose dark:prose-invert max-w-none"/>
                        </div>

                        {{-- ── MULTIPLE CHOICE ── --}}
                        @if($question->isMultipleChoice())
                            <div class="space-y-2" wire:key="options-{{ $question->id }}">
                                @foreach($question->getOptionsForDisplay() as $key => $optionText)
                                    @php
                                        $isSelected = isset($responses[$question->id]) && $responses[$question->id] === $key;
                                    @endphp
                                    <label
                                        wire:key="opt-{{ $question->id }}-{{ $key }}"
                                        class="flex items-start gap-2 sm:gap-3 p-2.5 sm:p-3.5 cursor-pointer transition-all duration-150 group rounded-[2px] border {{ $isSelected ? 'border-amber-500 bg-amber-50 dark:bg-amber-900/20' : 'border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800' }}"
                                    >
                                        <input
                                            type="radio"
                                            name="question_{{ $question->id }}"
                                            value="{{ $key }}"
                                            wire:model.live="responses.{{ $question->id }}"
                                            class="h-4 w-4 text-amber-600 border-slate-300 focus:ring-amber-500 flex-shrink-0 mt-0.5"
                                        >
                                        <div class="flex-1 flex items-start gap-3">
                                            <span class="inline-flex items-center justify-center w-6 h-6 text-xs font-bold flex-shrink-0 mt-0.5 transition-colors rounded-[2px] {{ $isSelected ? 'bg-amber-500 text-white' : 'bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-300' }}">
                                                {{ $key }}
                                            </span>
                                            <div class="flex-1 text-sm text-slate-700 dark:text-slate-300 leading-relaxed pt-0.5">
                                                <x-form.markdown-with-math :content="$optionText" class="text-slate-800 dark:text-slate-200"/>
                                            </div>
                                        </div>
                                    </label>
                                @endforeach
                            </div>

                            {{-- ── TRUE / FALSE ── --}}
                        @elseif($question->isTrueFalse())
                            <div class="grid grid-cols-2 gap-3">
                                @foreach(['True', 'False'] as $tfValue)
                                    @php
                                        $isSelected = isset($responses[$question->id]) && $responses[$question->id] === $tfValue;
                                    @endphp
                                    <label
                                        class="flex items-center justify-center gap-3 py-5 cursor-pointer transition-all duration-150 rounded-[2px] {{ $isSelected ? 'border-amber-500 bg-amber-50 dark:bg-amber-900/20' : 'border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800' }}"
                                    >
                                        <input
                                            type="radio"
                                            name="question_{{ $question->id }}"
                                            value="{{ $tfValue }}"
                                            wire:model.live="responses.{{ $question->id }}"
                                            class="h-4 w-4 text-amber-600 focus:ring-amber-500 border-slate-300 flex-shrink-0"
                                        >
                                        <span class="text-base font-semibold text-slate-700 dark:text-slate-200">{{ $tfValue }}</span>
                                    </label>
                                @endforeach
                            </div>

                            {{-- ── OPEN / ESSAY ── --}}
                        @else
                            <div>
                                <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-widest mb-2">Your Answer</label>
                                <textarea
                                    wire:model.live.debounce.500ms="responses.{{ $question->id }}"
                                    rows="10"
                                    class="w-full px-4 py-3 text-sm text-slate-800 dark:text-white bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500 dark:focus:border-amber-500 transition-all resize-none leading-relaxed rounded-[2px] font-serif"
                                    placeholder="Type your answer here…"
                                ></textarea>
                                <p class="text-xs text-slate-400 dark:text-slate-500 mt-1.5">Responses are saved automatically as you type.</p>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- ── QUESTION NAVIGATOR ── --}}
                <div class="bg-white dark:bg-slate-900 overflow-hidden"
                    style="border-radius: 2px; box-shadow: 0 0 0 1px rgba(0,0,0,0.06), 0 2px 12px rgba(0,0,0,0.04);">

                    <div class="px-5 py-3 border-b border-slate-100 dark:border-slate-800">
                        <h4 class="text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider" style="letter-spacing: 0.1em;">Question Navigator</h4>
                    </div>

                    <div class="p-3 sm:p-4">
                        <div class="flex flex-wrap gap-1 sm:gap-1.5">
                            @foreach($this->questions as $index => $q)
                                <button
                                    wire:key="nav-btn-{{ $q->id }}"
                                    wire:click="goToQuestion({{ $index }})"
                                    title="Question {{ $index + 1 }}"
                                    class="w-7 h-7 sm:w-8 sm:h-8 text-xs font-semibold transition-all duration-150 flex items-center justify-center relative"
                                    style="border-radius: 2px;
                                    @if($currentQuestionIndex === $index)
                                        background: linear-gradient(135deg, #b45309, #d97706); color: #fff; box-shadow: 0 2px 8px rgba(180,83,9,0.35); transform: scale(1.1);
                                    @elseif(!empty($responses[$q->id]))
                                        background: #059669; color: #fff;
                                    @else
                                        background: #f1f5f9; color: #475569;
                                    @endif">
                                    {{ $index + 1 }}
                                </button>
                            @endforeach
                        </div>

                        <div class="flex items-center gap-5 mt-4 pt-3 border-t border-slate-100 dark:border-slate-800">
                            <div class="flex items-center gap-1.5">
                                <div class="w-4 h-4" style="border-radius: 2px; background: linear-gradient(135deg, #b45309, #d97706);"></div>
                                <span class="text-xs text-slate-500 dark:text-slate-400">Current</span>
                            </div>
                            <div class="flex items-center gap-1.5">
                                <div class="w-4 h-4 bg-emerald-600" style="border-radius: 2px;"></div>
                                <span class="text-xs text-slate-500 dark:text-slate-400">Answered</span>
                            </div>
                            <div class="flex items-center gap-1.5">
                                <div class="w-4 h-4 bg-slate-200 dark:bg-slate-700" style="border-radius: 2px;"></div>
                                <span class="text-xs text-slate-500 dark:text-slate-400">Unanswered</span>
                            </div>
                        </div>
                    </div>
                </div>

            </div>{{-- /max-w-3xl --}}
        </div>{{-- /overflow-y-auto --}}

        {{-- ── BOTTOM NAVIGATION BAR ── --}}
        <div class="flex-shrink-0 bg-white dark:bg-slate-900 border-t border-slate-200 dark:border-slate-800"
            style="box-shadow: 0 -2px 12px rgba(0,0,0,0.06);">
            <div class="max-w-3xl mx-auto px-3 sm:px-6 py-2 sm:py-3 flex items-center justify-between gap-2 sm:gap-3">

                {{-- Previous --}}
                <button
                    wire:click="previousQuestion"
                    @if($currentQuestionIndex === 0) disabled @endif
                    class="inline-flex items-center gap-1 sm:gap-2 px-2.5 sm:px-4 py-2 sm:py-2.5 text-xs sm:text-sm font-medium border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 bg-slate-50 dark:bg-slate-800 transition-all disabled:opacity-40 disabled:cursor-not-allowed"
                    style="border-radius: 2px;">
                    <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                    <span class="hidden sm:inline">Previous</span> <span class="sm:hidden">Prev</span>
                </button>

                {{-- Centre action --}}
                <div class="flex items-center gap-1.5 sm:gap-2">
                    @if($sectionIndex < $this->exam->sections->count() - 1)
                        <a href="{{ route('examination-hub.take.section', [$this->exam, $sectionIndex + 1]) }}"
                           class="inline-flex items-center gap-1.5 sm:gap-2 px-3 sm:px-5 py-2 sm:py-2.5 text-xs sm:text-sm font-semibold text-white transition-all"
                           style="border-radius: 2px; background: #334155; box-shadow: 0 2px 6px rgba(0,0,0,0.15);">
                            <span class="hidden sm:inline">Next Section</span> <span class="sm:hidden">Next Sec</span>
                            <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </a>
                    @else
                        <form id="exam-submit-form" method="POST" action="{{ route('examination-hub.take.submit', $this->exam) }}">
                            @csrf
                            <button type="submit"
                                   class="inline-flex items-center gap-1.5 sm:gap-2 px-4 sm:px-7 py-2 sm:py-2.5 text-xs sm:text-sm font-bold text-white transition-all"
                                   style="border-radius: 2px; background: linear-gradient(135deg, #065f46, #059669); box-shadow: 0 2px 12px rgba(5,150,105,0.35);">
                                <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <span class="hidden sm:inline">Submit Examination</span> <span class="sm:hidden">Submit</span>
                            </button>
                        </form>
                    @endif
                </div>

                {{-- Next --}}
                <button
                    wire:click="nextQuestion"
                    @if($currentQuestionIndex === $this->questions->count() - 1) disabled @endif
                    class="inline-flex items-center gap-1 sm:gap-2 px-3 sm:px-5 py-2 sm:py-2.5 text-xs sm:text-sm font-semibold text-white transition-all disabled:opacity-40 disabled:cursor-not-allowed"
                    style="border-radius: 2px; background: linear-gradient(135deg, #b45309, #d97706); box-shadow: 0 2px 8px rgba(180,83,9,0.3);">
                    <span class="hidden sm:inline">Next</span> <span class="sm:hidden">Nxt</span>
                    <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </button>

            </div>
        </div>

    </div>{{-- /flex flex-col h-screen --}}
    @endif
@endif
</div>