<x-layouts.app>
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-7"
     style="font-family: 'system-ui', -apple-system, sans-serif;"
     x-data="templateForm()">

    {{-- ── STEP INDICATOR ──────────────────────────────────────────────────── --}}
    <div class="flex items-center gap-0">
        {{-- Step 1 (complete / back link) --}}
        <a href="{{ $template
                ? route('mock-exams.templates.front-page.edit', $template)
                : route('mock-exams.templates.create') }}"
           class="flex items-center gap-2.5 px-4 py-2.5 transition-all group"
           style="border-radius: 2px 0 0 2px; background: #4c1d95;"
           title="Go back to Front Page designer">
            <span class="flex items-center justify-center w-5 h-5 rounded-full bg-violet-300 text-violet-900 text-xs font-bold shrink-0">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                </svg>
            </span>
            <span class="text-violet-300 group-hover:text-white text-sm font-medium whitespace-nowrap transition-colors">
                Front Page
            </span>
        </a>
        {{-- Arrow --}}
        <div class="w-0 h-0 shrink-0" style="border-top: 20px solid transparent; border-bottom: 20px solid transparent; border-left: 12px solid #4c1d95;"></div>
        {{-- Step 2 (active) --}}
        <div class="flex items-center gap-2.5 px-5 py-2.5 ml-0"
             style="border-radius: 0 2px 2px 0; background: linear-gradient(135deg, #7c3aed, #6d28d9);">
            <span class="flex items-center justify-center w-5 h-5 rounded-full bg-white text-violet-700 text-xs font-bold shrink-0">2</span>
            <span class="text-white text-sm font-semibold whitespace-nowrap">Template Details</span>
        </div>
    </div>

    {{-- ── PAGE HEADER ─────────────────────────────────────────────────────── --}}
    <div class="overflow-hidden"
         style="border-radius: 2px; background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); box-shadow: 0 4px 24px rgba(0,0,0,0.15);">
        <div class="h-1 w-full" style="background: linear-gradient(90deg, #7c3aed, #a78bfa, #c4b5fd);"></div>
        <div class="px-7 py-6 flex items-center gap-4">
            <a href="{{ $template
                    ? route('mock-exams.templates.front-page.edit', $template)
                    : route('mock-exams.templates.create') }}"
               class="flex items-center justify-center w-8 h-8 text-slate-400 hover:text-white border border-slate-700 hover:border-slate-500 transition-all shrink-0"
               style="border-radius: 2px;">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-white leading-snug"
                    style="letter-spacing: -0.02em; font-family: 'Georgia', serif;">
                    {{ $template ? 'Edit Template' : 'Create Template' }}
                </h1>
                <p class="text-slate-400 mt-1 text-sm">
                    Configure the academic scope, sections, and question settings for this template.
                </p>
            </div>
        </div>
    </div>

    {{-- ── ERRORS ───────────────────────────────────────────────────────────── --}}
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
          action="{{ $template
              ? route('mock-exams.templates.update', $template)
              : route('mock-exams.templates.store') }}"
          class="space-y-7">
        @csrf
        @if($template) @method('PUT') @endif

        {{-- Hidden: carries the front_page_config JSON through the POST --}}
        <input type="hidden" name="front_page_config" value="{{ $frontPageConfigJson }}">

        {{-- ── TEMPLATE INFO ──────────────────────────────────────────────── --}}
        <div class="bg-white dark:bg-slate-900 overflow-hidden"
             style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 1px 6px rgba(0,0,0,0.04);">
            <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center gap-2">
                <div class="w-1 h-5" style="background: linear-gradient(180deg, #7c3aed, #a78bfa); border-radius: 1px;"></div>
                <h2 class="font-bold text-slate-900 dark:text-white text-xs uppercase tracking-wider" style="letter-spacing: 0.1em;">Template Information</h2>
            </div>
            <div class="p-6 space-y-5">
                <div class="grid md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-slate-500 uppercase tracking-wider mb-2" style="letter-spacing: 0.08em;">
                            Template Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="name"
                               value="{{ old('name', $template->name ?? '') }}"
                               placeholder="e.g. BECE Mathematics Standard Format"
                               required
                               class="w-full px-4 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-none focus:outline-none focus:ring-2 focus:ring-violet-500/20 focus:border-violet-500 dark:bg-slate-800 dark:text-white transition-all"
                               style="border-radius: 2px;">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-500 uppercase tracking-wider mb-2" style="letter-spacing: 0.08em;">Default Duration (minutes)</label>
                        <input type="number" name="default_duration_minutes" min="1" max="600"
                               value="{{ old('default_duration_minutes', $template->default_duration_minutes ?? '') }}"
                               placeholder="e.g. 120"
                               class="w-full px-4 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-none focus:outline-none focus:ring-2 focus:ring-violet-500/20 focus:border-violet-500 dark:bg-slate-800 dark:text-white transition-all"
                               style="border-radius: 2px;">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-500 uppercase tracking-wider mb-2" style="letter-spacing: 0.08em;">Description</label>
                    <textarea name="description" rows="2"
                              placeholder="Optional description of this template"
                              class="w-full px-4 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-none focus:outline-none focus:ring-2 focus:ring-violet-500/20 focus:border-violet-500 dark:bg-slate-800 dark:text-white transition-all resize-none"
                              style="border-radius: 2px;">{{ old('description', $template->description ?? '') }}</textarea>
                </div>
                <div class="flex items-center gap-2">
                    <input type="checkbox" name="is_active" id="is_active" value="1"
                           {{ old('is_active', $template->is_active ?? true) ? 'checked' : '' }}
                           class="accent-violet-600">
                    <label for="is_active" class="text-sm text-slate-700 dark:text-slate-300">Template is active and available for use</label>
                </div>
            </div>
        </div>

        {{-- ── ACADEMIC HIERARCHY ──────────────────────────────────────────── --}}
        <div class="bg-white dark:bg-slate-900 overflow-hidden"
             style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 1px 6px rgba(0,0,0,0.04);">
            <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center gap-2">
                <div class="w-1 h-5" style="background: linear-gradient(180deg, #2563eb, #60a5fa); border-radius: 1px;"></div>
                <h2 class="font-bold text-slate-900 dark:text-white text-xs uppercase tracking-wider" style="letter-spacing: 0.1em;">Academic Scope</h2>
            </div>
            <div class="p-6 space-y-5">
                <div class="grid md:grid-cols-3 gap-4">
                    {{-- Group --}}
                    <div>
                        <label class="block text-xs font-medium text-slate-500 uppercase tracking-wider mb-2" style="letter-spacing: 0.08em;">
                            Academic Group (optional)
                        </label>
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
                            Academic Level (optional)
                        </label>
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
                                required
                                class="w-full px-4 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-none focus:outline-none focus:ring-2 focus:ring-violet-500/20 focus:border-violet-500 dark:bg-slate-800 dark:text-white transition-all"
                                style="border-radius: 2px;">
                            <option value="">Select subject…</option>
                            <template x-for="s in subjects" :key="s.id">
                                <option :value="String(s.id)" x-text="s.name"></option>
                            </template>
                        </select>
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

        {{-- ── SECTIONS BUILDER ──────────────────────────────────────────────── --}}
        <div class="bg-white dark:bg-slate-900 overflow-hidden"
             style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 1px 6px rgba(0,0,0,0.04);">
            <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <div class="w-1 h-5" style="background: linear-gradient(180deg, #d97706, #fbbf24); border-radius: 1px;"></div>
                    <h2 class="font-bold text-slate-900 dark:text-white text-xs uppercase tracking-wider" style="letter-spacing: 0.1em;">Sections Configuration</h2>
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
                            <div class="grid sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-medium text-slate-500 uppercase tracking-wider mb-2" style="letter-spacing: 0.08em;">
                                        Section Title <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" :name="'sections_config[' + idx + '][title]'"
                                           x-model="section.title" required
                                           placeholder="e.g. Section A – Objectives"
                                           class="w-full px-4 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-none focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 dark:bg-slate-800 dark:text-white transition-all"
                                           style="border-radius: 2px;">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-slate-500 uppercase tracking-wider mb-2" style="letter-spacing: 0.08em;">Instructions</label>
                                    <input type="text" :name="'sections_config[' + idx + '][instructions]'"
                                           x-model="section.instructions"
                                           placeholder="Optional instructions for this section"
                                           class="w-full px-4 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-none focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 dark:bg-slate-800 dark:text-white transition-all"
                                           style="border-radius: 2px;">
                                </div>
                            </div>

                            <div class="grid sm:grid-cols-3 gap-4">
                                <div>
                                    <label class="block text-xs font-medium text-slate-500 uppercase tracking-wider mb-2" style="letter-spacing: 0.08em;">
                                        Question Type <span class="text-red-500">*</span>
                                    </label>
                                    <select :name="'sections_config[' + idx + '][question_type]'"
                                            x-model="section.question_type" required
                                            class="w-full px-4 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-none focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 dark:bg-slate-800 dark:text-white transition-all"
                                            style="border-radius: 2px;">
                                        <option value="multiple_choice">Multiple Choice</option>
                                        <option value="true_false">True / False</option>
                                        <option value="essay">Essay</option>
                                        <option value="mixed">Mixed</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-slate-500 uppercase tracking-wider mb-2" style="letter-spacing: 0.08em;">
                                        No. of Questions <span class="text-red-500">*</span>
                                    </label>
                                    <input type="number" :name="'sections_config[' + idx + '][question_count]'"
                                           x-model.number="section.question_count" min="1" max="200" required
                                           class="w-full px-4 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-none focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 dark:bg-slate-800 dark:text-white transition-all"
                                           style="border-radius: 2px;">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-slate-500 uppercase tracking-wider mb-2" style="letter-spacing: 0.08em;">Marks per Question</label>
                                    <input type="number" :name="'sections_config[' + idx + '][marks_per_question]'"
                                           x-model.number="section.marks_per_question" min="0.5" max="100" step="0.5"
                                           class="w-full px-4 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-none focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 dark:bg-slate-800 dark:text-white transition-all"
                                           style="border-radius: 2px;">
                                </div>
                            </div>

                            <div class="flex items-center gap-2">
                                <input type="hidden" :name="'sections_config[' + idx + '][is_randomized]'" value="0">
                                <input type="checkbox" :name="'sections_config[' + idx + '][is_randomized]'" :value="1"
                                       x-model="section.is_randomized"
                                       class="accent-amber-600">
                                <label class="text-sm text-slate-700 dark:text-slate-300">Randomize question order within this section</label>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        {{-- ── SUMMARY & ACTIONS ──────────────────────────────────────────────── --}}
        <div class="bg-white dark:bg-slate-900 overflow-hidden"
             style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 1px 6px rgba(0,0,0,0.04);">
            <div class="p-6">
                <div class="flex items-center justify-between flex-wrap gap-4">
                    <div class="text-sm text-slate-600 dark:text-slate-400">
                        <span x-text="sections.length"></span> section(s),
                        <span x-text="sections.reduce((s, x) => s + (parseInt(x.question_count) || 0), 0)"></span> total questions,
                        <span x-text="sections.reduce((s, x) => s + ((parseInt(x.question_count) || 0) * (parseFloat(x.marks_per_question) || 1)), 0).toFixed(1)"></span> total marks
                    </div>
                    <div class="flex gap-3">
                        <a href="{{ route('mock-exams.templates.index') }}"
                           class="px-5 py-2.5 text-sm font-medium text-slate-700 bg-slate-100 hover:bg-slate-200 transition-colors dark:bg-slate-800 dark:text-slate-300 dark:hover:bg-slate-700"
                           style="border-radius: 2px;">
                            Cancel
                        </a>
                        <button type="submit"
                                class="px-6 py-2.5 text-sm font-semibold text-white transition-all hover:shadow-lg"
                                style="border-radius: 2px; background: linear-gradient(135deg, #7c3aed, #6d28d9);">
                            {{ $template ? 'Update Template' : 'Create Template' }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
function templateForm() {
    const tree = @json($hierarchyTree);

    @if($template)
    const existing = {
        groupId:     {{ $template->academic_group_id ?? 'null' }},
        levelId:     {{ $template->academic_level_id ?? 'null' }},
        subjectId:   {{ $template->academic_subject_id }},
        topicIds:    @json(array_map('intval', $template->topic_ids ?? [])),
        subtopicIds: @json(array_map('intval', $template->subtopic_ids ?? [])),
        sections:    @json($template->sections_config ?? []),
    };
    @else
    const existing = null;
    @endif

    return {
        tree,

        selectedGroupId:     '',
        selectedLevelId:     '',
        selectedSubjectId:   '',
        selectedTopicIds:    [],
        selectedSubtopicIds: [],

        sections: existing ? existing.sections : [
            { title: '', instructions: '', question_type: 'multiple_choice', question_count: 10, marks_per_question: 1, is_randomized: false }
        ],

        init() {
            if (!existing) return;

            if (existing.groupId) {
                this.selectedGroupId = String(existing.groupId);
                this.$nextTick(() => {
                    this.selectedLevelId = String(existing.levelId);
                    this.$nextTick(() => {
                        this.selectedSubjectId = String(existing.subjectId);
                        this.$nextTick(() => {
                            this.selectedTopicIds = existing.topicIds.map(String);
                            this.$nextTick(() => {
                                this.selectedSubtopicIds = existing.subtopicIds.map(String);
                            });
                        });
                    });
                });
            } else {
                this.selectedSubjectId = String(existing.subjectId);
                this.$nextTick(() => {
                    this.selectedTopicIds = existing.topicIds.map(String);
                    this.$nextTick(() => {
                        this.selectedSubtopicIds = existing.subtopicIds.map(String);
                    });
                });
            }
        },

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
