<x-layouts.app :title="$subjectExam ? 'Edit Subject Exam' : 'Add Subject Exam'">

    {{-- Header --}}
    <div class="flex items-center gap-3 mb-6">
        <x-ui.button href="{{ route('mock-exams.show', $mockExam) }}" variant="ghost" size="sm" icon="arrow-left">Back</x-ui.button>
        <div>
            <h1 class="text-xl font-bold text-slate-900 dark:text-white">
                {{ $subjectExam ? 'Edit Subject Exam' : 'Add Subject Exam' }}
            </h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-0.5">
                {{ $mockExam->title }}
            </p>
        </div>
    </div>

    @if($errors->any())
        <div class="mb-4 p-4 rounded-[2px] bg-red-50 border border-red-200 dark:bg-red-900/20 dark:border-red-800">
            <ul class="text-sm text-red-600 dark:text-red-400 list-disc list-inside space-y-1">
                @foreach($errors->all() as $error) <li>{{ $error }}</li> @endforeach
            </ul>
        </div>
    @endif

    <form method="POST"
          action="{{ $subjectExam
              ? route('mock-exams.subject-exams.update', [$mockExam, $subjectExam])
              : route('mock-exams.subject-exams.store', $mockExam) }}"
          x-data="subjectExamForm()"
          class="space-y-6 max-w-3xl">
        @csrf
        @if($subjectExam) @method('PUT') @endif

        {{-- ── Academic Hierarchy ─────────────────────────────────────────── --}}
        <x-ui.card>
            <x-ui.card-header title="Academic Hierarchy" accent="primary" />
            <div class="p-5 space-y-4">

                {{-- Group --}}
                <div class="space-y-2">
                    <label class="block text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                        Academic Group <span class="text-red-500">*</span>
                    </label>
                    <select name="academic_group_id" x-model="selectedGroupId"
                            @change="selectedLevelId = null; selectedSubjectId = null; selectedTopicIds = []; selectedSubtopicIds = []"
                            class="w-full px-4 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-none focus:outline-none focus:ring-2 focus:ring-violet-500/20 focus:border-violet-500 dark:bg-slate-800 dark:text-white">
                        <option value="">Select group…</option>
                        <template x-for="g in tree" :key="g.id">
                            <option :value="g.id" x-text="g.name"></option>
                        </template>
                    </select>
                </div>

                {{-- Level --}}
                <div class="space-y-2" x-show="selectedGroupId" x-transition>
                    <label class="block text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                        Academic Level <span class="text-red-500">*</span>
                    </label>
                    <select name="academic_level_id" x-model="selectedLevelId"
                            @change="selectedSubjectId = null; selectedTopicIds = []; selectedSubtopicIds = []"
                            class="w-full px-4 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-none focus:outline-none focus:ring-2 focus:ring-violet-500/20 focus:border-violet-500 dark:bg-slate-800 dark:text-white">
                        <option value="">Select level…</option>
                        <template x-for="l in levels" :key="l.id">
                            <option :value="l.id" x-text="l.name"></option>
                        </template>
                    </select>
                </div>

                {{-- Subject --}}
                <div class="space-y-2" x-show="selectedLevelId" x-transition>
                    <label class="block text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                        Subject <span class="text-red-500">*</span>
                    </label>
                    <select name="academic_subject_id" x-model="selectedSubjectId"
                            @change="selectedTopicIds = []; selectedSubtopicIds = []"
                            class="w-full px-4 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-none focus:outline-none focus:ring-2 focus:ring-violet-500/20 focus:border-violet-500 dark:bg-slate-800 dark:text-white">
                        <option value="">Select subject…</option>
                        <template x-for="s in subjects" :key="s.id">
                            <option :value="s.id" x-text="s.name"></option>
                        </template>
                    </select>
                    {{-- Duplicate warning --}}
                    <p x-show="existingSubjectIds.includes(Number(selectedSubjectId))"
                       class="text-xs text-amber-600 dark:text-amber-400">
                        ⚠ This subject already exists in this mock exam. You can still proceed.
                    </p>
                </div>

                {{-- Topics --}}
                <div class="space-y-2" x-show="selectedSubjectId && availableTopics.length" x-transition>
                    <label class="block text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                        Topics (optional – leave empty for all)
                    </label>
                    <div class="grid grid-cols-2 gap-1 max-h-48 overflow-y-auto p-2 border border-slate-200 dark:border-slate-700 rounded-[2px]">
                        <template x-for="t in availableTopics" :key="t.id">
                            <label class="flex items-center gap-2 cursor-pointer p-1 hover:bg-slate-50 dark:hover:bg-slate-800 rounded">
                                <input type="checkbox" name="topic_ids[]" :value="t.id"
                                       x-model="selectedTopicIds"
                                       @change="selectedSubtopicIds = []"
                                       class="accent-violet-600 shrink-0">
                                <span class="text-xs text-slate-700 dark:text-slate-300 truncate" x-text="t.name"></span>
                            </label>
                        </template>
                    </div>
                </div>

                {{-- Subtopics --}}
                <div class="space-y-2" x-show="selectedTopicIds.length && availableSubtopics.length" x-transition>
                    <label class="block text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                        Subtopics (optional – leave empty for all under selected topics)
                    </label>
                    <div class="grid grid-cols-2 gap-1 max-h-48 overflow-y-auto p-2 border border-slate-200 dark:border-slate-700 rounded-[2px]">
                        <template x-for="st in availableSubtopics" :key="st.id">
                            <label class="flex items-center gap-2 cursor-pointer p-1 hover:bg-slate-50 dark:hover:bg-slate-800 rounded">
                                <input type="checkbox" name="subtopic_ids[]" :value="st.id"
                                       x-model="selectedSubtopicIds"
                                       class="accent-violet-600 shrink-0">
                                <span class="text-xs text-slate-700 dark:text-slate-300 truncate" x-text="st.name"></span>
                            </label>
                        </template>
                    </div>
                </div>
            </div>
        </x-ui.card>

        {{-- ── Subject Exam Meta ──────────────────────────────────────────── --}}
        <x-ui.card>
            <x-ui.card-header title="Subject Exam Details" accent="info" />
            <div class="p-5 space-y-4">
                <x-ui.input name="title" label="Custom Title (optional)"
                    :value="old('title', $subjectExam->title ?? '')"
                    placeholder="Defaults to subject name" />
                <div class="space-y-2">
                    <label class="block text-xs font-medium text-slate-500 uppercase tracking-wider">Instructions</label>
                    <textarea name="instructions" rows="3"
                        class="w-full px-4 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-none focus:outline-none focus:ring-2 focus:ring-violet-500/20 focus:border-violet-500 dark:bg-slate-800 dark:text-white resize-none"
                        placeholder="Instructions for candidates before this subject exam">{{ old('instructions', $subjectExam->instructions ?? '') }}</textarea>
                </div>
                <x-ui.input type="number" name="duration_in_minutes" label="Duration (minutes, optional)"
                    :value="old('duration_in_minutes', $subjectExam->duration_in_minutes ?? '')"
                    min="1" max="600" />
            </div>
        </x-ui.card>

        {{-- ── Sections Builder ──────────────────────────────────────────── --}}
        <x-ui.card>
            <div class="px-5 py-4 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <div class="w-1 h-5 rounded-[1px] bg-gradient-to-b from-amber-600 to-amber-400"></div>
                    <h2 class="font-bold text-slate-900 dark:text-white text-xs uppercase tracking-widest">Sections</h2>
                </div>
                <x-ui.button type="button" @click="addSection()" variant="ghost" size="sm" icon="plus">
                    Add Section
                </x-ui.button>
            </div>

            <div class="p-5 space-y-4">
                <template x-for="(section, idx) in sections" :key="idx">
                    <div class="border border-slate-200 dark:border-slate-700 rounded-[2px] p-4 space-y-3 relative">
                        {{-- Remove --}}
                        <button type="button" @click="removeSection(idx)"
                                x-show="sections.length > 1"
                                class="absolute top-3 right-3 text-slate-400 hover:text-red-500 transition-colors">
                            <x-heroicon-o-x-mark class="w-4 h-4" />
                        </button>

                        <p class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider" x-text="'Section ' + (idx + 1)"></p>

                        {{-- Title --}}
                        <div class="space-y-1">
                            <label class="text-xs text-slate-500 uppercase tracking-wider">Title *</label>
                            <input type="text" :name="'sections[' + idx + '][title]'"
                                   x-model="section.title" required
                                   class="w-full px-4 py-2 text-sm border border-slate-200 dark:border-slate-700 rounded-none focus:outline-none focus:ring-2 focus:ring-violet-500/20 focus:border-violet-500 dark:bg-slate-800 dark:text-white"
                                   placeholder="e.g. Objectives">
                        </div>

                        {{-- Instructions --}}
                        <div class="space-y-1">
                            <label class="text-xs text-slate-500 uppercase tracking-wider">Instructions</label>
                            <textarea :name="'sections[' + idx + '][instructions]'"
                                      x-model="section.instructions" rows="2"
                                      class="w-full px-4 py-2 text-sm border border-slate-200 dark:border-slate-700 rounded-none focus:outline-none focus:ring-2 focus:ring-violet-500/20 focus:border-violet-500 dark:bg-slate-800 dark:text-white resize-none"
                                      placeholder="e.g. Answer ALL questions in this section"></textarea>
                        </div>

                        <div class="grid grid-cols-3 gap-3">
                            {{-- Question type --}}
                            <div class="space-y-1">
                                <label class="text-xs text-slate-500 uppercase tracking-wider">Question Type *</label>
                                <select :name="'sections[' + idx + '][question_type]'"
                                        x-model="section.question_type"
                                        class="w-full px-3 py-2 text-sm border border-slate-200 dark:border-slate-700 rounded-none focus:outline-none focus:ring-2 focus:ring-violet-500/20 focus:border-violet-500 dark:bg-slate-800 dark:text-white">
                                    <option value="multiple_choice">Multiple Choice</option>
                                    <option value="true_false">True / False</option>
                                    <option value="essay">Essay</option>
                                    <option value="mixed">Mixed (all types)</option>
                                </select>
                            </div>

                            {{-- Count --}}
                            <div class="space-y-1">
                                <label class="text-xs text-slate-500 uppercase tracking-wider">No. of Questions *</label>
                                <input type="number" :name="'sections[' + idx + '][question_count]'"
                                       x-model="section.question_count" min="1" max="200" required
                                       class="w-full px-3 py-2 text-sm border border-slate-200 dark:border-slate-700 rounded-none focus:outline-none focus:ring-2 focus:ring-violet-500/20 focus:border-violet-500 dark:bg-slate-800 dark:text-white">
                            </div>

                            {{-- Marks per question --}}
                            <div class="space-y-1">
                                <label class="text-xs text-slate-500 uppercase tracking-wider">Marks Each</label>
                                <input type="number" :name="'sections[' + idx + '][marks_per_question]'"
                                       x-model="section.marks_per_question" min="0.5" max="100" step="0.5"
                                       class="w-full px-3 py-2 text-sm border border-slate-200 dark:border-slate-700 rounded-none focus:outline-none focus:ring-2 focus:ring-violet-500/20 focus:border-violet-500 dark:bg-slate-800 dark:text-white">
                            </div>
                        </div>

                        {{-- Randomise --}}
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="hidden" :name="'sections[' + idx + '][is_randomized]'" value="0">
                            <input type="checkbox" :name="'sections[' + idx + '][is_randomized]'" value="1"
                                   x-model="section.is_randomized" class="accent-violet-600">
                            <span class="text-sm text-slate-600 dark:text-slate-300">Randomise question order per participant</span>
                        </label>

                        {{-- Estimated marks total --}}
                        <p class="text-xs text-slate-400">
                            Estimated marks: <span class="font-medium text-slate-600 dark:text-slate-300" x-text="(section.question_count * section.marks_per_question).toFixed(1)"></span>
                        </p>
                    </div>
                </template>

                <button type="button" @click="addSection()"
                        class="w-full py-3 text-sm text-slate-400 border-2 border-dashed border-slate-200 dark:border-slate-700 rounded-[2px] hover:border-violet-400 hover:text-violet-500 transition-colors">
                    + Add another section
                </button>
            </div>
        </x-ui.card>

        {{-- Submit --}}
        <div class="flex gap-3">
            <x-ui.button type="submit" variant="primary" icon="check">
                {{ $subjectExam ? 'Save Changes' : 'Add Subject Exam &amp; Pull Questions' }}
            </x-ui.button>
            <x-ui.button href="{{ route('mock-exams.show', $mockExam) }}" variant="ghost">Cancel</x-ui.button>
        </div>
    </form>

    <script>
        function subjectExamForm() {
            const tree = @json($hierarchyTree);
            const existingSubjectIds = @json($existingSubjectIds);

            @if($subjectExam)
            @php
                $existingData = [
                    'groupId'     => $subjectExam->academic_group_id,
                    'levelId'     => $subjectExam->academic_level_id,
                    'subjectId'   => $subjectExam->academic_subject_id,
                    'topicIds'    => array_map('intval', $subjectExam->topic_ids ?? []),
                    'subtopicIds' => array_map('intval', $subjectExam->subtopic_ids ?? []),
                    'sections'    => $subjectExam->sections->map(function($s) {
                        return [
                            'title'              => $s->title,
                            'instructions'       => $s->instructions ?? '',
                            'question_type'      => $s->question_type,
                            'question_count'     => $s->question_count,
                            'marks_per_question' => $s->marks_per_question,
                            'is_randomized'      => (bool) $s->is_randomized,
                        ];
                    })->values()->toArray()
                ];
            @endphp
            const existing = @json($existingData);
            @else
            const existing = null;
            @endif

            return {
                tree,
                existingSubjectIds,
                selectedGroupId:    existing ? String(existing.groupId)   : '',
                selectedLevelId:    existing ? String(existing.levelId)   : '',
                selectedSubjectId:  existing ? String(existing.subjectId) : '',
                selectedTopicIds:   existing ? existing.topicIds.map(String)    : [],
                selectedSubtopicIds:existing ? existing.subtopicIds.map(String) : [],

                sections: existing ? existing.sections : [
                    { title: '', instructions: '', question_type: 'multiple_choice', question_count: 10, marks_per_question: 1, is_randomized: false }
                ],

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
                        question_type: 'multiple_choice', question_count: 10,
                        marks_per_question: 1, is_randomized: false
                    });
                },
                removeSection(idx) {
                    if (this.sections.length > 1) this.sections.splice(idx, 1);
                },
            };
        }
    </script>

</x-layouts.app>
