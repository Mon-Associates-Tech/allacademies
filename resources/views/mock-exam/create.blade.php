<x-layouts.app :title="isset($exam) ? 'Edit Mock Exam' : 'New Mock Exam'">

    {{-- Header --}}
    <div class="flex items-center gap-3 mb-6">
        <x-ui.button href="{{ route('mock-exams.index') }}" variant="ghost" size="sm" icon="arrow-left">
            Back
        </x-ui.button>
        <div>
            <h1 class="text-xl font-bold text-slate-900 dark:text-white">
                {{ isset($exam) ? 'Edit Mock Exam' : 'New Mock Exam' }}
            </h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-0.5">
                Configure the exam settings. Subject exams and questions are added after saving.
            </p>
        </div>
    </div>

    <form method="POST"
          action="{{ isset($exam) ? route('mock-exams.update', $exam) : route('mock-exams.store') }}"
          x-data="mockExamForm()"
          class="space-y-6 max-w-3xl">
        @csrf
        @if(isset($exam)) @method('PUT') @endif

        {{-- Validation errors --}}
        @if($errors->any())
            <div class="p-4 rounded-[2px] bg-red-50 border border-red-200 dark:bg-red-900/20 dark:border-red-800">
                <ul class="text-sm text-red-600 dark:text-red-400 space-y-1 list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Basic Info --}}
        <x-ui.card>
            <x-ui.card-header title="Basic Information" accent="primary" />
            <div class="p-5 space-y-4">
                <x-ui.input name="title" label="Title" required
                    :value="old('title', $exam->title ?? '')"
                    placeholder="e.g. BECE Mock 2025 – Term 2" />

                <div class="space-y-2">
                    <label class="block text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                        Description
                    </label>
                    <textarea name="description" rows="3"
                        class="w-full px-4 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-none focus:outline-none focus:ring-2 focus:ring-violet-500/20 focus:border-violet-500 dark:bg-slate-800 dark:text-white transition-all resize-none"
                        placeholder="Optional description visible to instructors">{{ old('description', $exam->description ?? '') }}</textarea>
                </div>

                <div class="space-y-2">
                    <label class="block text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                        Candidate Instructions
                    </label>
                    <textarea name="instructions" rows="4"
                        class="w-full px-4 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-none focus:outline-none focus:ring-2 focus:ring-violet-500/20 focus:border-violet-500 dark:bg-slate-800 dark:text-white transition-all resize-none"
                        placeholder="Instructions shown to candidates before they start">{{ old('instructions', $exam->instructions ?? '') }}</textarea>
                </div>
            </div>
        </x-ui.card>

        {{-- Delivery & Schedule --}}
        <x-ui.card>
            <x-ui.card-header title="Delivery &amp; Schedule" accent="info" />
            <div class="p-5 space-y-5">

                {{-- Delivery type --}}
                <div class="space-y-2">
                    <label class="block text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                        Delivery Type <span class="text-red-500">*</span>
                    </label>
                    <div class="flex gap-3">
                        @foreach(['online' => 'Online (candidates take on platform)', 'print' => 'Print (download as PDF)'] as $val => $label)
                            <label class="flex items-center gap-2 cursor-pointer px-4 py-2.5 border rounded-[2px] transition-all"
                                   :class="deliveryType === '{{ $val }}'
                                       ? 'border-violet-500 bg-violet-50 dark:bg-violet-900/20'
                                       : 'border-slate-200 dark:border-slate-700'">
                                <input type="radio" name="delivery_type" value="{{ $val }}"
                                       x-model="deliveryType"
                                       class="accent-violet-600">
                                <span class="text-sm text-slate-700 dark:text-slate-300">{{ $label }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                {{-- Dates --}}
                <div class="grid grid-cols-2 gap-4">
                    <x-ui.input type="datetime-local" name="starts_at" label="Starts At"
                        :value="old('starts_at', isset($exam->starts_at) ? $exam->starts_at->format('Y-m-d\TH:i') : '')" />
                    <x-ui.input type="datetime-local" name="ends_at" label="Ends At"
                        :value="old('ends_at', isset($exam->ends_at) ? $exam->ends_at->format('Y-m-d\TH:i') : '')" />
                </div>

                {{-- Status --}}
                <div class="space-y-2">
                    <label class="block text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                        Status <span class="text-red-500">*</span>
                    </label>
                    <select name="status"
                        class="w-full px-4 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-none focus:outline-none focus:ring-2 focus:ring-violet-500/20 focus:border-violet-500 dark:bg-slate-800 dark:text-white">
                        <option value="draft" {{ old('status', $exam->status ?? 'draft') === 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="published" {{ old('status', $exam->status ?? '') === 'published' ? 'selected' : '' }}>Published</option>
                    </select>
                </div>
            </div>
        </x-ui.card>

        {{-- Online-only settings --}}
        <div x-show="deliveryType === 'online'" x-transition>
            <x-ui.card>
                <x-ui.card-header title="Participant &amp; Access Settings" accent="warning" />
                <div class="p-5 space-y-5">

                    {{-- Participant mode --}}
                    <div class="space-y-2">
                        <label class="block text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                            Participant Mode <span class="text-red-500">*</span>
                        </label>
                        <div class="flex gap-3">
                            <label class="flex items-center gap-2 cursor-pointer px-4 py-2.5 border rounded-[2px] transition-all"
                                   :class="participantMode === 'general' ? 'border-violet-500 bg-violet-50 dark:bg-violet-900/20' : 'border-slate-200 dark:border-slate-700'">
                                <input type="radio" name="participant_mode" value="general" x-model="participantMode" class="accent-violet-600">
                                <span class="text-sm text-slate-700 dark:text-slate-300">General – anyone with the code</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer px-4 py-2.5 border rounded-[2px] transition-all"
                                   :class="participantMode === 'configured' ? 'border-violet-500 bg-violet-50 dark:bg-violet-900/20' : 'border-slate-200 dark:border-slate-700'">
                                <input type="radio" name="participant_mode" value="configured" x-model="participantMode" class="accent-violet-600">
                                <span class="text-sm text-slate-700 dark:text-slate-300">Configured – pre-registered list only</span>
                            </label>
                        </div>
                    </div>

                    {{-- Configured-mode options --}}
                    <div x-show="participantMode === 'configured'" x-transition class="space-y-4 pl-4 border-l-2 border-violet-200 dark:border-violet-800">
                        <div class="space-y-2">
                            <label class="block text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                                Match Participants By
                            </label>
                            <div class="flex gap-3">
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="radio" name="configured_match_mode" value="any"
                                           {{ old('configured_match_mode', $exam->configured_match_mode ?? 'any') === 'any' ? 'checked' : '' }}
                                           class="accent-violet-600">
                                    <span class="text-sm text-slate-700 dark:text-slate-300">Email OR code (any)</span>
                                </label>
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="radio" name="configured_match_mode" value="both"
                                           {{ old('configured_match_mode', $exam->configured_match_mode ?? '') === 'both' ? 'checked' : '' }}
                                           class="accent-violet-600">
                                    <span class="text-sm text-slate-700 dark:text-slate-300">Email AND code (both required)</span>
                                </label>
                            </div>
                        </div>

                        <div class="space-y-2">
                            <label class="block text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                                Required Fields at Join
                            </label>
                            @foreach(['name' => 'Name', 'email' => 'Email', 'code' => 'Unique Code'] as $fieldVal => $fieldLabel)
                                <label class="flex items-center gap-2 cursor-pointer">
                                    @php $checked = in_array($fieldVal, old('participant_required_fields', $exam->participant_required_fields ?? ['name','email'])); @endphp
                                    <input type="checkbox" name="participant_required_fields[]" value="{{ $fieldVal }}"
                                           {{ $checked ? 'checked' : '' }}
                                           class="accent-violet-600">
                                    <span class="text-sm text-slate-700 dark:text-slate-300">{{ $fieldLabel }}</span>
                                </label>
                            @endforeach
                        </div>

                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="hidden" name="email_verification_required" value="0">
                            <input type="checkbox" name="email_verification_required" value="1"
                                   {{ old('email_verification_required', $exam->email_verification_required ?? false) ? 'checked' : '' }}
                                   class="accent-violet-600">
                            <span class="text-sm text-slate-700 dark:text-slate-300">Require email verification before exam starts</span>
                        </label>
                    </div>

                    {{-- Result visibility --}}
                    <div class="space-y-2">
                        <label class="block text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                            Result Visibility <span class="text-red-500">*</span>
                        </label>
                        <select name="result_visibility" x-model="resultVisibility"
                            class="w-full px-4 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-none focus:outline-none focus:ring-2 focus:ring-violet-500/20 focus:border-violet-500 dark:bg-slate-800 dark:text-white">
                            <option value="immediate"      {{ old('result_visibility', $exam->result_visibility ?? '') === 'immediate'      ? 'selected' : '' }}>Immediately after submission</option>
                            <option value="after_due_date" {{ old('result_visibility', $exam->result_visibility ?? '') === 'after_due_date' ? 'selected' : '' }}>After exam closes</option>
                            <option value="manual_release" {{ old('result_visibility', $exam->result_visibility ?? 'manual_release') === 'manual_release' ? 'selected' : '' }}>Manual release by instructor</option>
                            <option value="scheduled"      {{ old('result_visibility', $exam->result_visibility ?? '') === 'scheduled'      ? 'selected' : '' }}>Scheduled date &amp; time</option>
                        </select>
                    </div>

                    <div x-show="resultVisibility === 'scheduled'" x-transition>
                        <x-ui.input type="datetime-local" name="results_release_datetime" label="Release Results At"
                            :value="old('results_release_datetime', isset($exam->results_release_datetime) ? $exam->results_release_datetime->format('Y-m-d\TH:i') : '')" />
                    </div>

                    {{-- Randomisation & attempts --}}
                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-2">
                            <label class="block text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">Max Attempts</label>
                            <input type="number" name="max_attempts" min="1" max="10"
                                   value="{{ old('max_attempts', $exam->max_attempts ?? 1) }}"
                                   class="w-full px-4 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-none focus:outline-none focus:ring-2 focus:ring-violet-500/20 focus:border-violet-500 dark:bg-slate-800 dark:text-white">
                        </div>
                        <div class="flex items-end pb-2">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="hidden" name="is_randomized" value="0">
                                <input type="checkbox" name="is_randomized" value="1"
                                       {{ old('is_randomized', $exam->is_randomized ?? false) ? 'checked' : '' }}
                                       class="accent-violet-600">
                                <span class="text-sm text-slate-700 dark:text-slate-300">Randomise question order</span>
                            </label>
                        </div>
                    </div>

                </div>
            </x-ui.card>
        </div>

        {{-- Submit --}}
        <div class="flex items-center gap-3">
            <x-ui.button type="submit" variant="primary" icon="check">
                {{ isset($exam) ? 'Save Changes' : 'Create Mock Exam' }}
            </x-ui.button>
            <x-ui.button href="{{ route('mock-exams.index') }}" variant="ghost">
                Cancel
            </x-ui.button>
        </div>
    </form>

    <script>
        function mockExamForm() {
            return {
                deliveryType:     '{{ old('delivery_type', $exam->delivery_type ?? 'online') }}',
                participantMode:  '{{ old('participant_mode', $exam->participant_mode ?? 'general') }}',
                resultVisibility: '{{ old('result_visibility', $exam->result_visibility ?? 'manual_release') }}',
            }
        }
    </script>

</x-layouts.app>
