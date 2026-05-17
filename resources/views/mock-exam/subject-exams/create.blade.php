<x-layouts.app>
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-7"
     style="font-family: 'system-ui', -apple-system, sans-serif;"
     x-data="subjectExamForm()">

    {{-- ── PAGE HEADER ── --}}
    <div class="overflow-hidden"
         style="border-radius: 2px; background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); box-shadow: 0 4px 24px rgba(0,0,0,0.15);">
        <div class="h-1 w-full" style="background: linear-gradient(90deg, #7c3aed, #a78bfa, #c4b5fd);"></div>
        <div class="px-7 py-6 flex items-center gap-4">
            <a href="{{ route('mock-exams.show', $mockExam) }}"
               class="flex items-center justify-center w-8 h-8 text-slate-400 hover:text-white border border-slate-700 hover:border-slate-500 transition-all shrink-0"
               style="border-radius: 2px;">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-white leading-snug"
                    style="letter-spacing: -0.02em; font-family: 'Georgia', serif;">
                    {{ $subjectExam ? 'Edit Subject Exam' : 'Add Subject Exam' }}
                </h1>
                <p class="text-slate-400 mt-1 text-sm">{{ $mockExam->title }}</p>
            </div>
        </div>
    </div>

    {{-- ── ERRORS ── --}}
    @if($errors->any())
        <div class="px-5 py-4 text-sm"
             style="border-radius: 2px; background: #fef2f2; border: 1px solid #fecaca;">
            <p class="font-semibold text-red-700 mb-2">Please fix the following errors:</p>
            <ul class="list-disc list-inside space-y-1 text-red-600">
                @foreach($errors->all() as $error) <li>{{ $error }}</li> @endforeach
            </ul>
        </div>
    @endif

    <form method="POST"
          action="{{ $subjectExam
              ? route('mock-exams.subject-exams.update', [$mockExam, $subjectExam])
              : route('mock-exams.subject-exams.store', $mockExam) }}"
          class="space-y-7">
        @csrf
        @if($subjectExam) @method('PUT') @endif

        {{-- ── ACADEMIC HIERARCHY ── --}}
        <div class="bg-white dark:bg-slate-900 overflow-hidden"
             style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 1px 6px rgba(0,0,0,0.04);">
            <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center gap-2">
                <div class="w-1 h-5" style="background: linear-gradient(180deg, #7c3aed, #a78bfa); border-radius: 1px;"></div>
                <h2 class="font-bold text-slate-900 dark:text-white text-xs uppercase tracking-wider" style="letter-spacing: 0.1em;">Academic Hierarchy</h2>
            </div>
            <div class="p-6 space-y-5">

                <div class="grid md:grid-cols-3 gap-4">
                    {{-- Group --}}
                    <div>
                        <label class="block text-xs font-medium text-slate-500 uppercase tracking-wider mb-2" style="letter-spacing: 0.08em;">
                            Academic Group <span class="text-red-500">*</span>
                        </label>
                        {{--
                            Group options are server-side rendered so they exist in the DOM
                            before Alpine initialises, avoiding the x-for + x-model timing
                            race that leaves the select visually blank on edit.
                        --}}
                        <select name="academic_group_id" x-model="selectedGroupId"
                                @change="selectedLevelId = ''; selectedSubjectId = ''; selectedTopicIds = []; selectedSubtopicIds = []"
                                class="w-full px-4 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-none focus:outline-none focus:ring-2 focus:ring-violet-500/20 focus:border-violet-500 dark:bg-slate-800 dark:text-white transition-all"
                                style="border-radius: 2px;">
                            <option value="">Select group…</option>
                            @foreach($hierarchyTree as $group)
                                <option value="{{ $group['id'] }}">{{ $group['name'] }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Level --}}
                    <div>
                        <label class="block text-xs font-medium text-slate-500 uppercase tracking-wider mb-2" style="letter-spacing: 0.08em;">
                            Academic Level <span class="text-red-500">*</span>
                        </label>
                        {{-- Use CSS opacity instead of :disabled so init() can write to the element --}}
                        <select name="academic_level_id" x-model="selectedLevelId"
                                @change="selectedSubjectId = ''; selectedTopicIds = []; selectedSubtopicIds = []"
                                :class="!selectedGroupId ? 'opacity-40 pointer-events-none' : ''"
                                class="w-full px-4 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-none focus:outline-none focus:ring-2 focus:ring-violet-500/20 focus:border-violet-500 dark:bg-slate-800 dark:text-white transition-all"
                                style="border-radius: 2px;">
                            <option value="">Select level…</option>
                            <template x-for="l in levels" :key="l.id">
                                <option :value="String(l.id)" x-text="l.name"></option>
                            </template>
                        </select>
                    </div>

                    {{-- Subject --}}
                    <div>
                        <label class="block text-xs font-medium text-slate-500 uppercase tracking-wider mb-2" style="letter-spacing: 0.08em;">
                            Subject <span class="text-red-500">*</span>
                        </label>
                        <select name="academic_subject_id" x-model="selectedSubjectId"
                                @change="selectedTopicIds = []; selectedSubtopicIds = []"
                                :class="!selectedLevelId ? 'opacity-40 pointer-events-none' : ''"
                                class="w-full px-4 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-none focus:outline-none focus:ring-2 focus:ring-violet-500/20 focus:border-violet-500 dark:bg-slate-800 dark:text-white transition-all"
                                style="border-radius: 2px;">
                            <option value="">Select subject…</option>
                            <template x-for="s in subjects" :key="s.id">
                                <option :value="String(s.id)" x-text="s.name"></option>
                            </template>
                        </select>
                        {{-- Duplicate warning --}}
                        <p x-show="existingSubjectIds.includes(Number(selectedSubjectId))"
                           class="mt-1.5 text-xs text-amber-600 dark:text-amber-400 flex items-center gap-1">
                            <svg class="w-3 h-3 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                            This subject already exists in this mock. You may still proceed.
                        </p>
                    </div>
                </div>

                {{-- Topics --}}
                <div x-show="selectedSubjectId && availableTopics.length > 0" x-transition>
                    <label class="block text-xs font-medium text-slate-500 uppercase tracking-wider mb-2" style="letter-spacing: 0.08em;">
                        Topics <span class="text-slate-400 normal-case font-normal">(optional — leave blank for all topics)</span>
                    </label>
                    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-2 p-3 max-h-44 overflow-y-auto border border-slate-200 dark:border-slate-700"
                         style="border-radius: 2px;">
                        <template x-for="t in availableTopics" :key="t.id">
                            <label class="flex items-start gap-2 cursor-pointer p-2 hover:bg-violet-50 dark:hover:bg-violet-900/20 transition-colors"
                                   style="border-radius: 2px;">
                                <input type="checkbox" name="topic_ids[]" :value="String(t.id)"
                                       x-model="selectedTopicIds"
                                       @change="selectedSubtopicIds = []"
                                       class="mt-0.5 accent-violet-600 shrink-0">
                                <span class="text-xs text-slate-700 dark:text-slate-300 leading-tight" x-text="t.name"></span>
                            </label>
                        </template>
                    </div>
                </div>

                {{-- Subtopics --}}
                <div x-show="selectedTopicIds.length > 0 && availableSubtopics.length > 0" x-transition>
                    <label class="block text-xs font-medium text-slate-500 uppercase tracking-wider mb-2" style="letter-spacing: 0.08em;">
                        Subtopics <span class="text-slate-400 normal-case font-normal">(optional — leave blank for all subtopics)</span>
                    </label>
                    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-2 p-3 max-h-44 overflow-y-auto border border-slate-200 dark:border-slate-700"
                         style="border-radius: 2px;">
                        <template x-for="st in availableSubtopics" :key="st.id">
                            <label class="flex items-start gap-2 cursor-pointer p-2 hover:bg-violet-50 dark:hover:bg-violet-900/20 transition-colors"
                                   style="border-radius: 2px;">
                                <input type="checkbox" name="subtopic_ids[]" :value="String(st.id)"
                                       x-model="selectedSubtopicIds"
                                       class="mt-0.5 accent-violet-600 shrink-0">
                                <span class="text-xs text-slate-700 dark:text-slate-300 leading-tight" x-text="st.name"></span>
                            </label>
                        </template>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── SUBJECT EXAM DETAILS ── --}}
        <div class="bg-white dark:bg-slate-900 overflow-hidden"
             style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 1px 6px rgba(0,0,0,0.04);">
            <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center gap-2">
                <div class="w-1 h-5" style="background: linear-gradient(180deg, #2563eb, #60a5fa); border-radius: 1px;"></div>
                <h2 class="font-bold text-slate-900 dark:text-white text-xs uppercase tracking-wider" style="letter-spacing: 0.1em;">Subject Exam Details</h2>
            </div>
            <div class="p-6 space-y-4">
                <div class="grid sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-slate-500 uppercase tracking-wider mb-2" style="letter-spacing: 0.08em;">Custom Title (optional)</label>
                        <input type="text" name="title"
                               value="{{ old('title', $subjectExam->title ?? '') }}"
                               placeholder="Defaults to subject name"
                               class="w-full px-4 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-none focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 dark:bg-slate-800 dark:text-white transition-all"
                               style="border-radius: 2px;">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-500 uppercase tracking-wider mb-2" style="letter-spacing: 0.08em;">Duration (minutes, optional)</label>
                        <input type="number" name="duration_in_minutes" min="1" max="600"
                               value="{{ old('duration_in_minutes', $subjectExam->duration_in_minutes ?? '') }}"
                               placeholder="e.g. 60"
                               class="w-full px-4 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-none focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 dark:bg-slate-800 dark:text-white transition-all"
                               style="border-radius: 2px;">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-500 uppercase tracking-wider mb-2" style="letter-spacing: 0.08em;">Candidate Instructions</label>
                    <textarea name="instructions" rows="3"
                              placeholder="Instructions shown before this subject exam begins"
                              class="w-full px-4 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-none focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 dark:bg-slate-800 dark:text-white transition-all resize-none"
                              style="border-radius: 2px;">{{ old('instructions', $subjectExam->instructions ?? '') }}</textarea>
                </div>
            </div>
        </div>

        {{-- ── SECTIONS BUILDER ── --}}
        <div class="bg-white dark:bg-slate-900 overflow-hidden"
             style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 1px 6px rgba(0,0,0,0.04);">
            <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <div class="w-1 h-5" style="background: linear-gradient(180deg, #d97706, #fbbf24); border-radius: 1px;"></div>
                    <h2 class="font-bold text-slate-900 dark:text-white text-xs uppercase tracking-wider" style="letter-spacing: 0.1em;">Sections</h2>
                    <span class="text-xs text-slate-400" x-text="'(' + sections.length + ' section' + (sections.length !== 1 ? 's' : '') + ')'"></span>
                </div>
                <button type="button" @click="addSection()"
                        class="inline-flex items-center gap-1.5 px-4 py-2 text-xs font-semibold transition-all"
                        style="border-radius: 2px; background: linear-gradient(135deg, #d97706, #fbbf24); color: white; box-shadow: 0 2px 6px rgba(217,119,6,0.3);">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Add Section
                </button>
            </div>

            <div class="p-5 space-y-4">
                <template x-for="(section, idx) in sections" :key="idx">
                    <div class="border border-slate-200 dark:border-slate-700 overflow-hidden relative"
                         style="border-radius: 2px;">
                        {{-- Section header --}}
                        <div class="px-4 py-3 bg-slate-50 dark:bg-slate-800/50 border-b border-slate-200 dark:border-slate-700 flex items-center justify-between">
                            <span class="text-xs font-bold text-slate-600 dark:text-slate-400 uppercase tracking-wider"
                                  style="letter-spacing: 0.08em;"
                                  x-text="'Section ' + (idx + 1)"></span>
                            <button type="button" @click="removeSection(idx)"
                                    x-show="sections.length > 1"
                                    class="flex items-center gap-1 text-xs text-red-400 hover:text-red-600 transition-colors">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                                Remove
                            </button>
                        </div>

                        <div class="p-5 space-y-4">
                            {{-- Title & Instructions --}}
                            <div class="grid sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-medium text-slate-500 uppercase tracking-wider mb-2" style="letter-spacing: 0.08em;">Section Title <span class="text-red-500">*</span></label>
                                    <input type="text" :name="'sections[' + idx + '][title]'"
                                           x-model="section.title" required
                                           placeholder="e.g. Section A – Objectives"
                                           class="w-full px-4 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-none focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 dark:bg-slate-800 dark:text-white transition-all"
                                           style="border-radius: 2px;">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-slate-500 uppercase tracking-wider mb-2" style="letter-spacing: 0.08em;">Instructions</label>
                                    <input type="text" :name="'sections[' + idx + '][instructions]'"
                                           x-model="section.instructions"
                                           placeholder="e.g. Answer ALL questions in this section"
                                           class="w-full px-4 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-none focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 dark:bg-slate-800 dark:text-white transition-all"
                                           style="border-radius: 2px;">
                                </div>
                            </div>

                            {{-- Config row --}}
                            <div class="grid grid-cols-3 gap-4">
                                <div>
                                    <label class="block text-xs font-medium text-slate-500 uppercase tracking-wider mb-2" style="letter-spacing: 0.08em;">Question Type <span class="text-red-500">*</span></label>
                                    <select :name="'sections[' + idx + '][question_type]'"
                                            x-model="section.question_type"
                                            class="w-full px-3 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-none focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 dark:bg-slate-800 dark:text-white transition-all"
                                            style="border-radius: 2px;">
                                        <option value="multiple_choice">Multiple Choice</option>
                                        <option value="true_false">True / False</option>
                                        <option value="essay">Essay</option>
                                        <option value="mixed">Mixed (all types)</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-slate-500 uppercase tracking-wider mb-2" style="letter-spacing: 0.08em;">No. of Questions <span class="text-red-500">*</span></label>
                                    <input type="number" :name="'sections[' + idx + '][question_count]'"
                                           x-model.number="section.question_count"
                                           min="1" max="200" required
                                           class="w-full px-3 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-none focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 dark:bg-slate-800 dark:text-white transition-all"
                                           style="border-radius: 2px;">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-slate-500 uppercase tracking-wider mb-2" style="letter-spacing: 0.08em;">Marks Each</label>
                                    <input type="number" :name="'sections[' + idx + '][marks_per_question]'"
                                           x-model.number="section.marks_per_question"
                                           min="0.5" max="100" step="0.5"
                                           class="w-full px-3 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-none focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 dark:bg-slate-800 dark:text-white transition-all"
                                           style="border-radius: 2px;">
                                </div>
                            </div>

                            {{-- Bottom row --}}
                            <div class="flex items-center justify-between">
                                <label class="flex items-center gap-2 cursor-pointer text-sm text-slate-700 dark:text-slate-300">
                                    <input type="hidden" :name="'sections[' + idx + '][is_randomized]'" value="0">
                                    <input type="checkbox" :name="'sections[' + idx + '][is_randomized]'" value="1"
                                           x-model="section.is_randomized"
                                           class="accent-violet-600">
                                    Randomise question order per participant
                                </label>
                                <div class="text-xs text-slate-400">
                                    Estimated:
                                    <span class="font-semibold text-slate-600 dark:text-slate-300"
                                          x-text="(section.question_count * section.marks_per_question).toFixed(1) + ' marks'"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>

                {{-- Add section trigger --}}
                <button type="button" @click="addSection()"
                        class="w-full py-3.5 text-sm text-slate-400 border-2 border-dashed border-slate-200 dark:border-slate-700 hover:border-amber-400 hover:text-amber-500 transition-all"
                        style="border-radius: 2px;">
                    + Add another section
                </button>
            </div>
        </div>

        {{-- ── SUBMIT ── --}}
        <div class="flex items-center gap-3">
            <button type="submit"
                    class="inline-flex items-center gap-2 px-6 py-2.5 text-sm font-semibold text-white transition-all"
                    style="border-radius: 2px; background: linear-gradient(135deg, #7c3aed, #a78bfa); box-shadow: 0 2px 10px rgba(124,58,237,0.3);">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                {{ $subjectExam ? 'Save Changes' : 'Add Subject Exam &amp; Pull Questions' }}
            </button>
            <a href="{{ route('mock-exams.show', $mockExam) }}"
               class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-medium text-slate-600 dark:text-slate-400 border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800 transition-all"
               style="border-radius: 2px;">
                Cancel
            </a>
        </div>
    </form>
</div>

{{-- Pre-compute sections JSON safely outside the script tag --}}
@php
    $sectionsJson = $subjectExam
        ? json_encode($subjectExam->sections->map(fn($s) => [
            'title'              => $s->title,
            'instructions'       => $s->instructions ?? '',
            'question_type'      => $s->question_type,
            'question_count'     => $s->question_count,
            'marks_per_question' => $s->marks_per_question,
            'is_randomized'      => (bool) $s->is_randomized,
        ])->values(), JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT)
        : 'null';
@endphp

<script>
function subjectExamForm() {
    // tree is still needed by the levels/subjects/topics getters
    const tree = @json($hierarchyTree);
    const existingSubjectIds = @json($existingSubjectIds);

    @if($subjectExam)
    const existing = {
        groupId:     {{ $subjectExam->academic_group_id }},
        levelId:     {{ $subjectExam->academic_level_id }},
        subjectId:   {{ $subjectExam->academic_subject_id }},
        topicIds:    @json(array_map('intval', $subjectExam->topic_ids ?? [])),
        subtopicIds: @json(array_map('intval', $subjectExam->subtopic_ids ?? [])),
        sections:    {!! $sectionsJson !!},
    };
    @else
    const existing = null;
    @endif

    return {
        tree,
        existingSubjectIds,

        // ── Always start empty so x-for can render before we set the value.
        // For edit mode, init() cascades the selections via $nextTick chains.
        selectedGroupId:     '',
        selectedLevelId:     '',
        selectedSubjectId:   '',
        selectedTopicIds:    [],
        selectedSubtopicIds: [],

        sections: existing ? existing.sections : [
            { title: '', instructions: '', question_type: 'multiple_choice', question_count: 10, marks_per_question: 1, is_randomized: false }
        ],

        // ── Alpine calls init() after the component is mounted and x-for has
        // had its first render pass. We then cascade each level with $nextTick
        // so that every dropdown's options exist in the DOM before we select one.
        init() {
            if (!existing) return;

            // Step 1: set group → levels getter re-evaluates → x-for queues a
            // DOM update to render level options.
            this.selectedGroupId = String(existing.groupId);

            this.$nextTick(() => {
                // Level options are now in the DOM.
                this.selectedLevelId = String(existing.levelId);

                this.$nextTick(() => {
                    // Subject options are now in the DOM.
                    this.selectedSubjectId = String(existing.subjectId);

                    this.$nextTick(() => {
                        // Topic checkboxes are now visible (x-show passed).
                        this.selectedTopicIds = existing.topicIds.map(String);

                        this.$nextTick(() => {
                            // Subtopic checkboxes are now visible.
                            this.selectedSubtopicIds = existing.subtopicIds.map(String);
                        });
                    });
                });
            });
        },

        // ── Computed cascades ─────────────────────────────────────────────────
        get levels() {
            return this.tree.find(g => String(g.id) === String(this.selectedGroupId))?.levels ?? [];
        },
        get subjects() {
            return this.levels.find(l => String(l.id) === String(this.selectedLevelId))?.subjects ?? [];
        },
        get availableTopics() {
            return this.subjects.find(s => String(s.id) === String(this.selectedSubjectId))?.topics ?? [];
        },
        get availableSubtopics() {
            return this.availableTopics
                .filter(t => this.selectedTopicIds.includes(String(t.id)))
                .flatMap(t => t.subtopics ?? []);
        },

        // ── Section helpers ───────────────────────────────────────────────────
        addSection() {
            this.sections.push({
                title: '', instructions: '',
                question_type: 'multiple_choice',
                question_count: 10,
                marks_per_question: 1,
                is_randomized: false,
            });
        },
        removeSection(idx) {
            if (this.sections.length > 1) this.sections.splice(idx, 1);
        },
    };
}
</script>
</x-layouts.app>