<x-layouts.app>
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-7"
     style="font-family: 'system-ui', -apple-system, sans-serif;">

    {{-- ── PAGE HEADER ── --}}
    <div class="overflow-hidden"
         style="border-radius: 2px; background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); box-shadow: 0 4px 24px rgba(0,0,0,0.15);">
        <div class="h-1 w-full" style="background: linear-gradient(90deg, #7c3aed, #a78bfa, #c4b5fd);"></div>
        <div class="px-7 py-6 flex items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <a href="{{ route('mock-exams.index') }}"
                   class="flex items-center justify-center w-8 h-8 text-slate-400 hover:text-white border border-slate-700 hover:border-slate-500 transition-all"
                   style="border-radius: 2px;">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                </a>
                <div>
                    <h1 class="text-2xl font-bold text-white leading-snug"
                        style="letter-spacing: -0.02em; font-family: 'Georgia', serif;">
                        {{ isset($exam) ? 'Edit Mock Exam' : 'New Mock Exam' }}
                    </h1>
                    <p class="text-slate-400 mt-1 text-sm">
                        Configure settings. Subject exams and questions are added after saving.
                    </p>
                </div>
            </div>
        </div>
    </div>

    {{-- ── VALIDATION ERRORS ── --}}
    @if($errors->any())
        <div class="px-5 py-4 text-sm"
             style="border-radius: 2px; background: #fef2f2; border: 1px solid #fecaca;">
            <p class="font-semibold text-red-700 mb-2">Please fix the following errors:</p>
            <ul class="list-disc list-inside space-y-1 text-red-600">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST"
          action="{{ isset($exam) ? route('mock-exams.update', $exam) : route('mock-exams.store') }}"
          x-data="mockExamForm()"
          class="space-y-7">
        @csrf
        @if(isset($exam)) @method('PUT') @endif

        {{-- ── BASIC INFORMATION ── --}}
        <div class="bg-white dark:bg-slate-900 overflow-hidden"
             style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 1px 6px rgba(0,0,0,0.04);">
            <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center gap-2">
                <div class="w-1 h-5" style="background: linear-gradient(180deg, #7c3aed, #a78bfa); border-radius: 1px;"></div>
                <h2 class="font-bold text-slate-900 dark:text-white text-xs uppercase tracking-wider" style="letter-spacing: 0.1em;">Basic Information</h2>
            </div>
            <div class="p-6 space-y-5">

                {{-- Title --}}
                <div>
                    <label class="block text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2" style="letter-spacing: 0.08em;">
                        Title <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="title"
                           value="{{ old('title', $exam->title ?? '') }}"
                           placeholder="e.g. BECE Mock 2025 – Term 2"
                           required
                           class="w-full px-4 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-none focus:outline-none focus:ring-2 focus:ring-violet-500/20 focus:border-violet-500 dark:bg-slate-800 dark:text-white transition-all"
                           style="border-radius: 2px;">
                </div>

                {{-- Description --}}
                <div>
                    <label class="block text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2" style="letter-spacing: 0.08em;">Description</label>
                    <textarea name="description" rows="3"
                              placeholder="Optional description visible to instructors"
                              class="w-full px-4 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-none focus:outline-none focus:ring-2 focus:ring-violet-500/20 focus:border-violet-500 dark:bg-slate-800 dark:text-white transition-all resize-none"
                              style="border-radius: 2px;">{{ old('description', $exam->description ?? '') }}</textarea>
                </div>

                {{-- Instructions --}}
                <div>
                    <label class="block text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2" style="letter-spacing: 0.08em;">Candidate Instructions</label>
                    <textarea name="instructions" rows="4"
                              placeholder="Instructions shown to candidates before they start the exam"
                              class="w-full px-4 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-none focus:outline-none focus:ring-2 focus:ring-violet-500/20 focus:border-violet-500 dark:bg-slate-800 dark:text-white transition-all resize-none"
                              style="border-radius: 2px;">{{ old('instructions', $exam->instructions ?? '') }}</textarea>
                </div>
            </div>
        </div>

        {{-- ── DELIVERY & SCHEDULE ── --}}
        <div class="bg-white dark:bg-slate-900 overflow-hidden"
             style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 1px 6px rgba(0,0,0,0.04);">
            <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center gap-2">
                <div class="w-1 h-5" style="background: linear-gradient(180deg, #2563eb, #60a5fa); border-radius: 1px;"></div>
                <h2 class="font-bold text-slate-900 dark:text-white text-xs uppercase tracking-wider" style="letter-spacing: 0.1em;">Delivery &amp; Schedule</h2>
            </div>
            <div class="p-6 space-y-5">

                {{-- Delivery type --}}
                <div>
                    <label class="block text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-3" style="letter-spacing: 0.08em;">
                        Delivery Type <span class="text-red-500">*</span>
                    </label>
                    <div class="grid sm:grid-cols-2 gap-3">
                        @foreach(['online' => ['label' => 'Online', 'desc' => 'Candidates take the exam on the platform', 'icon' => 'M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z'],
                                    'print'  => ['label' => 'Print',  'desc' => 'Download as PDF for printing',           'icon' => 'M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z']] as $val => $opt)
                            <label class="flex items-start gap-3 p-4 cursor-pointer border transition-all"
                                   :class="deliveryType === '{{ $val }}'
                                       ? 'border-violet-400 bg-violet-50 dark:bg-violet-900/20 dark:border-violet-600'
                                       : 'border-slate-200 dark:border-slate-700 hover:border-slate-300'"
                                   style="border-radius: 2px;">
                                <input type="radio" name="delivery_type" value="{{ $val }}" x-model="deliveryType"
                                       class="mt-0.5 accent-violet-600">
                                <div>
                                    <p class="text-sm font-semibold text-slate-800 dark:text-slate-200">{{ $opt['label'] }}</p>
                                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">{{ $opt['desc'] }}</p>
                                </div>
                            </label>
                        @endforeach
                    </div>
                </div>

                {{-- Dates --}}
                <div class="grid sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2" style="letter-spacing: 0.08em;">Starts At</label>
                        <input type="datetime-local" name="starts_at"
                               value="{{ old('starts_at', isset($exam->starts_at) ? $exam->starts_at->format('Y-m-d\TH:i') : '') }}"
                               class="w-full px-4 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-none focus:outline-none focus:ring-2 focus:ring-violet-500/20 focus:border-violet-500 dark:bg-slate-800 dark:text-white"
                               style="border-radius: 2px;">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2" style="letter-spacing: 0.08em;">Ends At</label>
                        <input type="datetime-local" name="ends_at"
                               value="{{ old('ends_at', isset($exam->ends_at) ? $exam->ends_at->format('Y-m-d\TH:i') : '') }}"
                               class="w-full px-4 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-none focus:outline-none focus:ring-2 focus:ring-violet-500/20 focus:border-violet-500 dark:bg-slate-800 dark:text-white"
                               style="border-radius: 2px;">
                    </div>
                </div>

                {{-- Status --}}
                <div>
                    <label class="block text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2" style="letter-spacing: 0.08em;">
                        Status <span class="text-red-500">*</span>
                    </label>
                    <select name="status"
                            class="w-full px-4 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-none focus:outline-none focus:ring-2 focus:ring-violet-500/20 focus:border-violet-500 dark:bg-slate-800 dark:text-white"
                            style="border-radius: 2px;">
                        <option value="draft"     {{ old('status', $exam->status ?? 'draft') === 'draft'     ? 'selected' : '' }}>Draft</option>
                        <option value="published" {{ old('status', $exam->status ?? '') === 'published' ? 'selected' : '' }}>Published</option>
                    </select>
                </div>
            </div>
        </div>

        {{-- ── ONLINE-ONLY SETTINGS ── --}}
        <div x-show="deliveryType === 'online'" x-transition
             class="bg-white dark:bg-slate-900 overflow-hidden"
             style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 1px 6px rgba(0,0,0,0.04);">
            <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center gap-2">
                <div class="w-1 h-5" style="background: linear-gradient(180deg, #d97706, #fbbf24); border-radius: 1px;"></div>
                <h2 class="font-bold text-slate-900 dark:text-white text-xs uppercase tracking-wider" style="letter-spacing: 0.1em;">Participant &amp; Access Settings</h2>
            </div>
            <div class="p-6 space-y-6">

                {{-- Participant mode --}}
                <div>
                    <label class="block text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-3" style="letter-spacing: 0.08em;">
                        Participant Mode <span class="text-red-500">*</span>
                    </label>
                    <div class="grid sm:grid-cols-2 gap-3">
                        @foreach(['general' => ['label' => 'General', 'desc' => 'Anyone with the access code can join'],
                                   'configured' => ['label' => 'Configured', 'desc' => 'Only pre-registered participants can join']] as $val => $opt)
                            <label class="flex items-start gap-3 p-4 cursor-pointer border transition-all"
                                   :class="participantMode === '{{ $val }}'
                                       ? 'border-violet-400 bg-violet-50 dark:bg-violet-900/20 dark:border-violet-600'
                                       : 'border-slate-200 dark:border-slate-700 hover:border-slate-300'"
                                   style="border-radius: 2px;">
                                <input type="radio" name="participant_mode" value="{{ $val }}" x-model="participantMode"
                                       class="mt-0.5 accent-violet-600">
                                <div>
                                    <p class="text-sm font-semibold text-slate-800 dark:text-slate-200">{{ $opt['label'] }}</p>
                                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">{{ $opt['desc'] }}</p>
                                </div>
                            </label>
                        @endforeach
                    </div>
                </div>

                {{-- Configured-mode options --}}
                <div x-show="participantMode === 'configured'" x-transition
                     class="space-y-5 pl-5 border-l-2 border-violet-200 dark:border-violet-800">

                    <div>
                        <label class="block text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2" style="letter-spacing: 0.08em;">
                            Match Participants By
                        </label>
                        <div class="flex gap-4">
                            @foreach(['any' => 'Email OR unique code', 'both' => 'Email AND unique code (both required)'] as $val => $label)
                                <label class="flex items-center gap-2 cursor-pointer text-sm text-slate-700 dark:text-slate-300">
                                    <input type="radio" name="configured_match_mode" value="{{ $val }}"
                                           {{ old('configured_match_mode', $exam->configured_match_mode ?? 'any') === $val ? 'checked' : '' }}
                                           class="accent-violet-600">
                                    {{ $label }}
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2" style="letter-spacing: 0.08em;">
                            Required Fields at Join
                        </label>
                        <div class="flex gap-4">
                            @foreach(['name' => 'Name', 'email' => 'Email', 'code' => 'Unique Code'] as $val => $lbl)
                                @php $checked = in_array($val, old('participant_required_fields', $exam->participant_required_fields ?? ['name','email'])); @endphp
                                <label class="flex items-center gap-2 cursor-pointer text-sm text-slate-700 dark:text-slate-300">
                                    <input type="checkbox" name="participant_required_fields[]" value="{{ $val }}"
                                           {{ $checked ? 'checked' : '' }}
                                           class="accent-violet-600">
                                    {{ $lbl }}
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <label class="flex items-center gap-2 cursor-pointer text-sm text-slate-700 dark:text-slate-300">
                        <input type="hidden" name="email_verification_required" value="0">
                        <input type="checkbox" name="email_verification_required" value="1"
                               {{ old('email_verification_required', $exam->email_verification_required ?? false) ? 'checked' : '' }}
                               class="accent-violet-600">
                        Require email verification before exam starts
                    </label>
                </div>

                {{-- Result visibility --}}
                <div>
                    <label class="block text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2" style="letter-spacing: 0.08em;">
                        Result Visibility <span class="text-red-500">*</span>
                    </label>
                    <select name="result_visibility" x-model="resultVisibility"
                            class="w-full px-4 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-none focus:outline-none focus:ring-2 focus:ring-violet-500/20 focus:border-violet-500 dark:bg-slate-800 dark:text-white"
                            style="border-radius: 2px;">
                        <option value="immediate"      {{ old('result_visibility', $exam->result_visibility ?? '') === 'immediate'      ? 'selected' : '' }}>Immediately after submission</option>
                        <option value="after_due_date" {{ old('result_visibility', $exam->result_visibility ?? '') === 'after_due_date' ? 'selected' : '' }}>After exam closes</option>
                        <option value="manual_release" {{ old('result_visibility', $exam->result_visibility ?? 'manual_release') === 'manual_release' ? 'selected' : '' }}>Manual release by instructor</option>
                        <option value="scheduled"      {{ old('result_visibility', $exam->result_visibility ?? '') === 'scheduled'      ? 'selected' : '' }}>Scheduled date &amp; time</option>
                    </select>
                </div>

                <div x-show="resultVisibility === 'scheduled'" x-transition>
                    <label class="block text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2" style="letter-spacing: 0.08em;">Release Results At</label>
                    <input type="datetime-local" name="results_release_datetime"
                           value="{{ old('results_release_datetime', isset($exam->results_release_datetime) ? $exam->results_release_datetime->format('Y-m-d\TH:i') : '') }}"
                           class="w-full px-4 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-none focus:outline-none focus:ring-2 focus:ring-violet-500/20 focus:border-violet-500 dark:bg-slate-800 dark:text-white"
                           style="border-radius: 2px;">
                </div>

                {{-- Misc --}}
                <div class="grid sm:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2" style="letter-spacing: 0.08em;">Max Attempts</label>
                        <input type="number" name="max_attempts" min="1" max="10"
                               value="{{ old('max_attempts', $exam->max_attempts ?? 1) }}"
                               class="w-full px-4 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-none focus:outline-none focus:ring-2 focus:ring-violet-500/20 focus:border-violet-500 dark:bg-slate-800 dark:text-white"
                               style="border-radius: 2px;">
                    </div>
                    <div class="flex items-end pb-1">
                        <label class="flex items-center gap-2 cursor-pointer text-sm text-slate-700 dark:text-slate-300">
                            <input type="hidden" name="is_randomized" value="0">
                            <input type="checkbox" name="is_randomized" value="1"
                                   {{ old('is_randomized', $exam->is_randomized ?? false) ? 'checked' : '' }}
                                   class="accent-violet-600">
                            Randomise question order for each participant
                        </label>
                    </div>
                </div>
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
                {{ isset($exam) ? 'Save Changes' : 'Create Mock Exam' }}
            </button>
            <a href="{{ route('mock-exams.index') }}"
               class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-medium text-slate-600 dark:text-slate-400 border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800 transition-all"
               style="border-radius: 2px;">
                Cancel
            </a>
        </div>
    </form>
</div>

<script>
    function mockExamForm() {
        return {
            deliveryType:     '{{ old('delivery_type', $exam->delivery_type ?? 'online') }}',
            participantMode:  '{{ old('participant_mode', $exam->participant_mode ?? 'general') }}',
            resultVisibility: '{{ old('result_visibility', $exam->result_visibility ?? 'manual_release') }}',
        };
    }
</script>
</x-layouts.app>