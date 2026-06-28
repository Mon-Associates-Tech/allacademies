@php
    $seed = $formData ?? old();

    // When editing, use formData sections; when creating new or after validation error, use old() or default
    if (!empty($seed['sections']) && is_array($seed['sections'])) {
        // Filter out completely empty sections that might come from old() or defaults
        $seedSections = array_values(array_filter($seed['sections'], function($section) {
            // Keep sections with IDs (existing sections)
            if (!empty($section['id'])) {
                return true;
            }
            // For new sections, require at least a title
            return !empty($section['title']);
        }));

        // If filtering resulted in no sections, add one default
        if (empty($seedSections)) {
            $seedSections = [['title'=>'','description'=>'','instructions'=>'','time_limit_minutes'=>'','source_type'=>'database','question_type'=>'multiple_choice','question_count'=>10,'database_count'=>0,'ai_count'=>0,'manual_count'=>0,'is_randomized'=>false,'topic_ids'=>[],'subtopic_ids'=>[],'has_document'=>false]];
        }
    } else {
        // No sections in seed, use default
        $seedSections = [['title'=>'','description'=>'','instructions'=>'','time_limit_minutes'=>'','source_type'=>'database','question_type'=>'multiple_choice','question_count'=>10,'database_count'=>0,'ai_count'=>0,'manual_count'=>0,'is_randomized'=>false,'topic_ids'=>[],'subtopic_ids'=>[],'has_document'=>false]];
    }

    // Set default dates if not provided
    $defaultStartsAt = $seed['starts_at'] ?? now()->format('Y-m-d\TH:i');
    $defaultEndsAt = $seed['ends_at'] ?? now()->addWeek()->format('Y-m-d\TH:i');
@endphp

{{-- ═══════════════════════════════════════════════════════════
     PAGE SHELL
═══════════════════════════════════════════════════════════ --}}
<x-layouts.app>
    <x-examination-hub.navigation active="create" />
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-7"
     style="font-family: 'system-ui', -apple-system, sans-serif;">

    {{-- ── PAGE HEADER ── --}}
    <div class="overflow-hidden"
         style="border-radius: 2px; background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); box-shadow: 0 4px 24px rgba(0,0,0,0.15);">
        <div class="h-1 w-full" style="background: linear-gradient(90deg, #7c3aed, #a78bfa, #c4b5fd);"></div>
        <div class="px-7 py-6 flex items-center gap-4">
            <div class="w-12 h-12 flex-shrink-0 flex items-center justify-center"
                 style="border-radius: 2px; background: linear-gradient(135deg, #7c3aed, #a78bfa);">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-white leading-snug" style="letter-spacing: -0.02em; font-family: 'Georgia', serif;">
                    {{ isset($editingExam) && $editingExam ? 'Edit Examination' : 'Create Examination' }}
                </h1>
                <p class="text-slate-400 mt-1 text-sm">
                    Configure your examination with sections, question sources, and participant settings
                </p>
            </div>
        </div>
    </div>

    <form method="POST" action="{{ route('examination-hub.create.preview') }}" enctype="multipart/form-data" class="space-y-7">
        @csrf
        <input type="hidden" name="exam_id" value="{{ $seed['exam_id'] ?? '' }}">

        {{-- ── WARNING BANNER ── --}}
        @if(isset($editingExam) && $editingExam)
            @php
                $hasStarted = $editingExam->starts_at && now()->gte($editingExam->starts_at);
                $hasSubmissions = $editingExam->submissions_count > 0;
            @endphp
            @if($hasStarted || $hasSubmissions)
                <div class="flex items-start gap-3 px-5 py-4 border-l-4 border-amber-500 bg-amber-50 dark:bg-amber-950/30"
                     style="border-radius: 2px;">
                    <svg class="w-5 h-5 text-amber-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    <div>
                        <p class="text-sm font-semibold text-amber-800 dark:text-amber-300">Warning: Editing Active Examination</p>
                        <p class="text-sm text-amber-700 dark:text-amber-400 mt-1">
                            This exam has {{ $hasStarted ? 'already started' : '' }}{{ $hasStarted && $hasSubmissions ? ' and has ' : '' }}{{ $hasSubmissions ? $editingExam->submissions_count . ' submission(s)' : '' }}.
                            Changes may affect participants or invalidate existing results. Proceed with caution.
                        </p>
                    </div>
                </div>
            @endif
        @endif

        {{-- ── EXAM CONFIGURATION ── --}}
        <div class="bg-white dark:bg-slate-900 overflow-hidden"
             style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 1px 6px rgba(0,0,0,0.04);">
            <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center gap-2">
                <div class="w-1 h-5" style="background: linear-gradient(180deg, #7c3aed, #a78bfa); border-radius: 1px;"></div>
                <h2 class="font-bold text-slate-900 dark:text-white text-xs uppercase tracking-wider" style="letter-spacing: 0.1em;">Exam Configuration</h2>
            </div>
            <div class="p-5">
                <div class="grid md:grid-cols-2 gap-5">
                    <div class="md:col-span-2">
                        <label class="block text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2" style="letter-spacing: 0.08em;">Exam Title <span class="text-red-500">*</span></label>
                        <input name="title" value="{{ $seed['title'] ?? '' }}" placeholder="e.g., Final Mathematics Examination 2024"
                               class="w-full px-4 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-none focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 dark:bg-slate-800 dark:text-white transition-all"
                               style="border-radius: 2px;" required>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2" style="letter-spacing: 0.08em;">Status <span class="text-red-500">*</span></label>
                        <select name="status"
                                class="w-full px-4 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-none focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 dark:bg-slate-800 dark:text-white transition-all"
                                style="border-radius: 2px;" required>
                            <option value="draft" @selected(($seed['status'] ?? 'draft')==='draft')>📝 Draft</option>
                            <option value="published" @selected(($seed['status'] ?? '')==='published')>✅ Published</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2" style="letter-spacing: 0.08em;">Exam Mode</label>
                        <select name="hardened_mode"
                                class="w-full px-4 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-none focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 dark:bg-slate-800 dark:text-white transition-all"
                                style="border-radius: 2px;">
                            <option value="0" @selected(($seed['hardened_mode'] ?? '0')==='0')>👁️ Normal (Preview Questions)</option>
                            <option value="1" @selected(($seed['hardened_mode'] ?? '')==='1')>🔒 Hardened (No Preview)</option>
                        </select>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-2 flex items-start gap-1.5">
                            <svg class="w-3.5 h-3.5 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                            </svg>
                            <span>Hardened mode prevents viewing questions before exam creation</span>
                        </p>
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2" style="letter-spacing: 0.08em;">Description</label>
                        <textarea name="description" placeholder="Brief description of the examination"
                                  class="w-full px-4 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-none focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 dark:bg-slate-800 dark:text-white transition-all"
                                  style="border-radius: 2px;" rows="2">{{ $seed['description'] ?? '' }}</textarea>
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2" style="letter-spacing: 0.08em;">General Instructions</label>
                        <textarea name="instructions" placeholder="Instructions and rules for the entire examination"
                                  class="w-full px-4 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-none focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 dark:bg-slate-800 dark:text-white transition-all"
                                  style="border-radius: 2px;" rows="3">{{ $seed['instructions'] ?? '' }}</textarea>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2" style="letter-spacing: 0.08em;">Total Duration (minutes)</label>
                        <div class="relative">
                            <input type="number" min="1" name="duration_in_minutes" value="{{ $seed['duration_in_minutes'] ?? '' }}" placeholder="e.g., 120"
                                   class="w-full px-4 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-none focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 dark:bg-slate-800 dark:text-white transition-all pl-4 pr-10"
                                   style="border-radius: 2px;">
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2" style="letter-spacing: 0.08em;">Randomize Questions</label>
                        <label class="inline-flex items-center px-4 py-2.5 w-full text-sm border border-slate-200 dark:border-slate-700 rounded-none focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 dark:bg-slate-800 transition-all"
                               style="border-radius: 2px;">
                            <input type="checkbox" name="is_randomized" value="1"
                                   {{ ($seed['is_randomized'] ?? false) ? 'checked' : '' }}
                                   class="mr-3 h-4 w-4 text-purple-600 border-slate-300 dark:border-slate-600 rounded focus:ring-purple-500">
                            <span class="text-slate-700 dark:text-slate-300">Randomize question order for each participant</span>
                        </label>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2" style="letter-spacing: 0.08em;">Start Date & Time <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <input type="datetime-local" name="starts_at" value="{{ $defaultStartsAt }}"
                                   class="w-full px-4 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-none focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 dark:bg-slate-800 dark:text-white transition-all pl-4 pr-10"
                                   style="border-radius: 2px;" required>
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2" style="letter-spacing: 0.08em;">End Date & Time <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <input type="datetime-local" name="ends_at" value="{{ $defaultEndsAt }}"
                                   class="w-full px-4 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-none focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 dark:bg-slate-800 dark:text-white transition-all pl-4 pr-10"
                                   style="border-radius: 2px;" required>
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── ACADEMIC CLASSIFICATION ── --}}
        <div class="bg-white dark:bg-slate-900"
             style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 1px 6px rgba(0,0,0,0.04);">
            <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center gap-2">
                <div class="w-1 h-5" style="background: linear-gradient(180deg, #059669, #10b981); border-radius: 1px;"></div>
                <h2 class="font-bold text-slate-900 dark:text-white text-xs uppercase tracking-wider" style="letter-spacing: 0.1em;">Academic Classification</h2>
            </div>
            <div class="p-5">
                @livewire('examination-hub.academic-classification', [
                    'academicGroupId' => $seed['academic_group_id'] ?? null,
                    'academicLevelId' => $seed['academic_level_id'] ?? null,
                    'academicSubjectId' => $seed['academic_subject_id'] ?? null,
                    'hierarchyTree' => $hierarchyTree,
                ], key('exam-academic-classification-' . md5(json_encode([$seed['academic_group_id'] ?? null, $seed['academic_level_id'] ?? null, $seed['academic_subject_id'] ?? null]))))
            </div>
        </div>

        {{-- ── RESULTS AVAILABILITY ── --}}
        <div class="bg-white dark:bg-slate-900 overflow-hidden"
             style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 1px 6px rgba(0,0,0,0.04);">
            <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center gap-2">
                <div class="w-1 h-5" style="background: linear-gradient(180deg, #b45309, #d97706); border-radius: 1px;"></div>
                <h2 class="font-bold text-slate-900 dark:text-white text-xs uppercase tracking-wider" style="letter-spacing: 0.1em;">Results Availability</h2>
            </div>
            <div class="p-5">
                <div class="grid md:grid-cols-2 gap-5">
                    <div class="md:col-span-2">
                        <label class="block text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2" style="letter-spacing: 0.08em;">When should results be available? <span class="text-red-500">*</span></label>
                        <select name="result_visibility" id="result_visibility"
                                class="w-full px-4 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-none focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 dark:bg-slate-800 dark:text-white transition-all"
                                style="border-radius: 2px;" required
                                onchange="document.getElementById('scheduled_datetime_field').style.display = this.value === 'scheduled' ? 'block' : 'none';">
                            <option value="immediate" @selected(($seed['result_visibility'] ?? 'immediate')==='immediate')>⚡ Immediately after submission</option>
                            <option value="after_due_date" @selected(($seed['result_visibility'] ?? '')==='after_due_date')>📅 After exam end date</option>
                            <option value="scheduled" @selected(($seed['result_visibility'] ?? '')==='scheduled')>🕐 Scheduled date & time</option>
                            <option value="manual_release" @selected(($seed['result_visibility'] ?? '')==='manual_release')>🔒 Manual release by administrator</option>
                        </select>
                    </div>

                    <div id="scheduled_datetime_field" class="md:col-span-2" style="display: {{ ($seed['result_visibility'] ?? 'immediate') === 'scheduled' ? 'block' : 'none' }};">
                        <label class="block text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2" style="letter-spacing: 0.08em;">Results Release Date & Time</label>
                        <div class="relative">
                            <input type="datetime-local" name="results_release_datetime" value="{{ $seed['results_release_datetime'] ?? '' }}"
                                   class="w-full px-4 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-none focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 dark:bg-slate-800 dark:text-white transition-all pl-4 pr-10"
                                   style="border-radius: 2px;">
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                        </div>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-2">Results will automatically become available at this date and time</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── PARTICIPANT ACCESS CONTROL ── --}}
        <div class="bg-white dark:bg-slate-900 overflow-hidden" x-data="{ selectedGroupCount: {{ ($seed['participant_group_id'] ?? 0) ? ($participantGroups->find($seed['participant_group_id'] ?? 0)?->members_count ?? 0) : 0 }} }"
             style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 1px 6px rgba(0,0,0,0.04);">
            <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center gap-2">
                <div class="w-1 h-5" style="background: linear-gradient(180deg, #065f46, #059669); border-radius: 1px;"></div>
                <h2 class="font-bold text-slate-900 dark:text-white text-xs uppercase tracking-wider" style="letter-spacing: 0.1em;">Participant Access Control</h2>
            </div>
            <div class="p-5">
                <div class="grid md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2" style="letter-spacing: 0.08em;">Participant Mode <span class="text-red-500">*</span></label>
                        <select name="participant_mode"
                                class="w-full px-4 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-none focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 dark:bg-slate-800 dark:text-white transition-all"
                                style="border-radius: 2px;" required>
                            <option value="general" @selected(($seed['participant_mode'] ?? 'general')==='general')>🌐 General (Anyone with code)</option>
                            <option value="configured" @selected(($seed['participant_mode'] ?? '')==='configured')>🎯 Configured (Pre-registered only)</option>
                            <option value="both" @selected(($seed['participant_mode'] ?? '')==='both')>🔀 Both</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2" style="letter-spacing: 0.08em;">Configured Match Rule <span class="text-red-500">*</span></label>
                        <select name="configured_match_mode"
                                class="w-full px-4 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-none focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 dark:bg-slate-800 dark:text-white transition-all"
                                style="border-radius: 2px;" required>
                            <option value="any" @selected(($seed['configured_match_mode'] ?? 'any')==='any')>Match email OR code</option>
                            <option value="both" @selected(($seed['configured_match_mode'] ?? '')==='both')>Match email AND code</option>
                        </select>
                    </div>

                    {{-- Participant Group Selector --}}
                    @if($participantGroups->isNotEmpty())
                    <div class="md:col-span-2">
                        <label class="block text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2" style="letter-spacing: 0.08em;">Import from Participant Group</label>
                        <select name="participant_group_id"
                                class="w-full px-4 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-none focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 dark:bg-slate-800 dark:text-white transition-all"
                                style="border-radius: 2px;"
                                x-on:change="selectedGroupCount = $event.target.selectedOptions[0]?.dataset.members || 0">
                            <option value="">— No group (add participants manually) —</option>
                            @php
                                $parentGroups = $participantGroups->whereNull('parent_id')->keyBy('id');
                                $programmeGroups = $participantGroups->whereNotNull('parent_id')->sortBy('name');
                                $groupedProgrammes = $programmeGroups->groupBy('parent_id');
                            @endphp
                            @foreach($parentGroups->sortBy('name') as $parentId => $parentGroup)
                                @if(isset($groupedProgrammes[$parentId]))
                                    <optgroup label="{{ $parentGroup->name }}">
                                        @foreach($groupedProgrammes[$parentId] as $group)
                                            <option value="{{ $group->id }}"
                                                    data-members="{{ $group->members_count ?? $group->members()->count() }}"
                                                    @selected(($seed['participant_group_id'] ?? '') == $group->id)>
                                                {{ $group->name }} ({{ $group->members_count ?? $group->members()->count() }} participants)
                                            </option>
                                        @endforeach
                                    </optgroup>
                                @endif
                            @endforeach
                        </select>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1.5" x-show="selectedGroupCount > 0">
                            <span x-text="selectedGroupCount"></span> participants will be added as configured participants when the exam is created.
                        </p>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1.5" x-show="!selectedGroupCount">
                            Selecting a group will copy all its members as configured participants for this exam.
                        </p>
                    </div>
                    @endif

                    <div class="md:col-span-2">
                        <label class="block text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2" style="letter-spacing: 0.08em;">Required Participant Fields</label>
                        @php $oldFields = $seed['participant_required_fields'] ?? ['name','email']; @endphp
                        @livewire('common.searchable-multi-select', [
                            'items' => [
                                ['id' => 'name', 'name' => 'Name'],
                                ['id' => 'email', 'name' => 'Email'],
                                ['id' => 'code', 'name' => 'Unique Code'],
                            ],
                            'selected' => $oldFields,
                            'name' => 'participant_required_fields',
                            'multiple' => true,
                            'placeholder' => 'Select required fields',
                        ], key('participant-required-fields-'.md5(json_encode($oldFields))))
                    </div>
                </div>
            </div>
        </div>

        {{-- ── SECTION BUILDER ── --}}
        @livewire('examination-hub.section-builder', [
            'sections' => $seedSections,
            'hierarchyTree' => $hierarchyTree,
        ], key('exam-section-builder-'.md5(json_encode($seedSections))))

        {{-- ── ERRORS ── --}}
        @if($errors->any())
            <div class="bg-white dark:bg-slate-900 overflow-hidden"
                 style="border-radius: 2px; border: 1px solid rgba(220,38,38,0.2); box-shadow: 0 1px 6px rgba(220,38,38,0.08);">
                <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center gap-2"
                     style="background: linear-gradient(135deg, #fef2f2, #fee2e2);">
                    <div class="w-1 h-5" style="background: linear-gradient(180deg, #dc2626, #ef4444); border-radius: 1px;"></div>
                    <h2 class="font-bold text-red-800 dark:text-red-300 text-xs uppercase tracking-wider" style="letter-spacing: 0.1em;">Validation Errors</h2>
                </div>
                <div class="p-5">
                    <ul class="space-y-2">
                        @foreach($errors->all() as $error)
                            <li class="flex items-start gap-2 text-sm text-red-700 dark:text-red-400">
                                <svg class="w-4 h-4 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                                </svg>
                                <span>{{ $error }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        {{-- ── FORM ACTIONS ── --}}
        <div class="flex flex-col sm:flex-row justify-between items-center gap-4 pt-2">
            <a href="{{ route('examination-hub.dashboard') }}"
               class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-semibold text-slate-700 dark:text-slate-200 transition-all border border-slate-200/50 rounded-sm bg-gradient-to-br from-slate-50 to-slate-100 dark:from-slate-800/50 dark:to-slate-800 hover:bg-slate-100 dark:hover:bg-slate-800">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Cancel
            </a>
            <div class="flex flex-col sm:flex-row items-center gap-3">
                @if(isset($editingExam) && $editingExam)
                    <div class="flex flex-col items-end gap-1">
                        <button type="submit"
                                formaction="{{ route('examination-hub.create.quick-save') }}"
                                class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-semibold transition-all border"
                                style="border-radius: 2px; color:#166534; border-color:rgba(22,101,52,0.2); background: linear-gradient(135deg, #f0fdf4, #dcfce7);">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path>
                            </svg>
                            Save Without Preview
                        </button>
                        <p class="text-xs text-slate-400 dark:text-slate-500">Saves settings only — questions unchanged</p>
                    </div>
                @endif
                <button type="submit"
                        class="inline-flex mb-5 items-center gap-2 px-6 py-2.5 text-sm font-semibold text-white transition-all"
                        style="border-radius: 2px; background: linear-gradient(135deg, #7c3aed, #a78bfa); box-shadow: 0 2px 10px rgba(124,58,237,0.3);">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                    </svg>
                    {{ isset($editingExam) && $editingExam ? 'Preview Updates' : 'Preview Examination' }}
                </button>
            </div>
        </div>
    </form>

</div>{{-- /container --}}
</x-layouts.app>
