<x-layouts.app>
    <x-examination-hub.navigation active="grading-system"/>

    {{-- ═══════════════════════════════════════════════════════════
         PAGE SHELL
    ═══════════════════════════════════════════════════════════ --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-7"
         style="font-family: 'system-ui', -apple-system, sans-serif;">

        {{-- ── PAGE HEADER ── --}}
        <div class="overflow-hidden"
             style="border-radius: 2px; background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); box-shadow: 0 4px 24px rgba(0,0,0,0.15);">
            <div class="h-1 w-full" style="background: linear-gradient(90deg, #7c3aed, #a78bfa, #c4b5fd);"></div>
            <div class="px-7 py-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-white leading-snug"
                        style="letter-spacing: -0.02em; font-family: 'Georgia', serif;">
                        Examinations Grading System
                    </h1>
                    <p class="text-slate-400 mt-2 text-sm">
                        Percentage-based grade scales for exam results — independent of the academic grading system
                    </p>
                </div>
                <div class="flex items-center gap-3">
                    @if(!$hasScales)
                        <form method="POST" action="{{ route('examination-hub.grading-system.initialize') }}">
                            @csrf
                            <button type="submit"
                                    class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-semibold text-white transition-all"
                                    style="border-radius: 2px; background: linear-gradient(135deg, #065f46, #059669); box-shadow: 0 2px 10px rgba(5,150,105,0.3);">
                                <x-heroicon-o-arrow-down-tray class="w-4 h-4"/>
                                Load Defaults
                            </button>
                        </form>
                    @endif
                        <x-ui.button
                            variant="primary"
                            size="md"
                            icon="plus"
                            onclick="document.getElementById('createModal').classList.remove('hidden')"
                        >
                            Add Grade Scale
                        </x-ui.button>
                </div>
            </div>
        </div>

        {{-- ── ALERTS ── --}}
        @if(session('success'))
            <div class="bg-white dark:bg-slate-900 overflow-hidden"
                 style="border-radius: 2px; border: 1px solid rgba(5,150,105,0.2); box-shadow: 0 1px 6px rgba(5,150,105,0.08);">
                <div class="px-5 py-4 flex items-center gap-3">
                    <div class="w-8 h-8 flex-shrink-0 flex items-center justify-center"
                         style="border-radius: 2px; background: linear-gradient(135deg, #065f46, #059669);">
                        <x-heroicon-o-check class="w-4 h-4 text-white"/>
                    </div>
                    <p class="text-sm text-emerald-700 dark:text-emerald-300">{{ session('success') }}</p>
                </div>
            </div>
        @endif

        @if(session('info'))
            <div class="bg-white dark:bg-slate-900 overflow-hidden"
                 style="border-radius: 2px; border: 1px solid rgba(37,99,235,0.2); box-shadow: 0 1px 6px rgba(37,99,235,0.08);">
                <div class="px-5 py-4 flex items-center gap-3">
                    <div class="w-8 h-8 flex-shrink-0 flex items-center justify-center"
                         style="border-radius: 2px; background: linear-gradient(135deg, #2563eb, #60a5fa);">
                        <x-heroicon-o-information-circle class="w-4 h-4 text-white"/>
                    </div>
                    <p class="text-sm text-blue-700 dark:text-blue-300">{{ session('info') }}</p>
                </div>
            </div>
        @endif

        @if($errors->any())
            <div class="bg-white dark:bg-slate-900 overflow-hidden"
                 style="border-radius: 2px; border: 1px solid rgba(220,38,38,0.2); box-shadow: 0 1px 6px rgba(220,38,38,0.08);">
                <div class="px-5 py-4 flex items-start gap-3">
                    <div class="w-8 h-8 flex-shrink-0 flex items-center justify-center"
                         style="border-radius: 2px; background: linear-gradient(135deg, #b91c1c, #ef4444);">
                        <x-heroicon-o-x-circle class="w-4 h-4 text-white"/>
                    </div>
                    <ul class="text-sm text-red-700 dark:text-red-300 space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        {{-- ── INFO CARD ── --}}
        <x-ui.card variant="accent" accent="info" shadow="true">
            <x-ui.card-header title="About This Grading System" accent="info" />

            <div class="p-5">
                <div class="flex items-start gap-3">
                    <div class="w-8 h-8 flex-shrink-0 flex items-center justify-center rounded-[2px] bg-gradient-to-br from-blue-600 to-blue-400">
                        <x-heroicon-o-information-circle class="w-4 h-4 text-white" />
                    </div>
                    <div class="text-sm text-slate-700 dark:text-slate-300">
                        <p>Grade scales here are <strong>exclusive to the Examinations Hub</strong> and do not affect
                            academic transcripts or the school-wide grading system.</p>
                        <ul class="list-disc list-inside mt-2 space-y-1 ml-2">
                            <li>Grades are resolved by matching a submission's <strong>percentage score</strong> to the
                                closest range.
                            </li>
                            <li>Ranges must not overlap — the system will reject conflicting entries.</li>
                            <li>When no custom scales exist, the built-in A+–F fallback is used automatically.</li>
                        </ul>
                    </div>
                </div>
            </div>
        </x-ui.card>
        {{-- ── GRADE SCALES TABLE ── --}}
        <div class="bg-white dark:bg-slate-900 overflow-hidden"
             style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 1px 6px rgba(0,0,0,0.04);">
            <div
                class="px-5 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between gap-2">
                <div class="flex items-center gap-2">
                    <div class="w-1 h-5"
                         style="background: linear-gradient(180deg, #7c3aed, #a78bfa); border-radius: 1px;"></div>
                    <h2 class="font-bold text-slate-900 dark:text-white text-xs uppercase tracking-wider"
                        style="letter-spacing: 0.1em;">Grade Scales</h2>
                    @if($hasScales)
                        <span class="text-xs text-slate-400 dark:text-slate-500 font-normal normal-case">
                            {{ $scales->count() }} {{ Str::plural('entry', $scales->count()) }}
                        </span>
                    @endif
                </div>
                @if($hasScales)
                    <form method="POST" action="{{ route('examination-hub.grading-system.initialize') }}">
                        @csrf
                        <button type="submit"
                                onclick="return confirm('Load defaults? This only adds scales if none currently exist.')"
                                class="inline-flex items-center gap-1.5 text-xs font-medium text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 transition-colors">
                            <x-heroicon-o-arrow-path class="w-3.5 h-3.5"/>
                            Load defaults if empty
                        </button>
                    </form>
                @endif
            </div>

            @if($scales->isEmpty())
                <div class="p-12 text-center">
                    <div class="w-12 h-12 mx-auto flex items-center justify-center mb-4"
                         style="border-radius: 2px; background: linear-gradient(135deg, #64748b, #94a3b8);">
                        <x-heroicon-o-clipboard-document-list class="w-6 h-6 text-white"/>
                    </div>
                    <h3 class="text-sm font-semibold text-slate-900 dark:text-white">No grade scales yet</h3>
                    <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                        Load the default A+–F scale or create a custom one using the button above.
                    </p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                        <tr class="border-b border-slate-100 dark:border-slate-800 bg-slate-50 dark:bg-slate-800/50">
                            <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider"
                                style="letter-spacing: 0.08em;">Grade
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider"
                                style="letter-spacing: 0.08em;">Range
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider"
                                style="letter-spacing: 0.08em;">GPA
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider"
                                style="letter-spacing: 0.08em;">Status
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider"
                                style="letter-spacing: 0.08em;">Result
                            </th>
                            <th class="px-6 py-3 text-right text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider"
                                style="letter-spacing: 0.08em;">Actions
                            </th>
                        </tr>
                        </thead>
                        {{-- One <tbody x-data> per scale keeps Alpine scope isolated
                             and lets x-show target entire <tr> rows, avoiding the
                             column-misalignment caused by hiding individual <td> cells. --}}
                        @foreach($scales as $scale)
                            <tbody x-data="{ editing: false }"
                                   class="divide-y divide-slate-50 dark:divide-slate-800">

                            {{-- ── VIEW ROW ── --}}
                            <tr x-show="!editing"
                                class="hover:bg-amber-50/40 dark:hover:bg-slate-800/40 transition-colors">

                                {{-- Grade label --}}
                                <td class="px-6 py-4">
                                        <span
                                            class="inline-flex items-center justify-center w-9 h-9 text-sm font-bold text-white"
                                            style="border-radius: 2px; background-color: {{ $scale->color_code }};">
                                            {{ $scale->grade_label }}
                                        </span>
                                </td>

                                {{-- Percentage range --}}
                                <td class="px-6 py-4">
    <span class="inline-flex items-center justify-center text-xs font-medium px-2.5 py-1
                 border border-slate-200 dark:border-slate-600
                 bg-gradient-to-br from-slate-50 to-slate-100
                 dark:from-slate-700 dark:to-slate-800
                 text-slate-700 dark:text-slate-300"
          style="border-radius: 2px;">
        {{ $scale->min_percentage }}% – {{ $scale->max_percentage }}%
    </span>
                                </td>

                                {{-- GPA --}}
                                <td class="px-6 py-4 text-slate-700 dark:text-slate-300">
                                    {{ $scale->grade_point !== null ? number_format($scale->grade_point, 2) : '—' }}
                                </td>

                                {{-- Active status --}}
                                <td class="px-6 py-4">
                                    @if($scale->is_active)
                                        <x-ui.badge variant="success" size="sm">Active</x-ui.badge>
                                    @else
                                        <x-ui.badge variant="warning" size="sm">Inactive</x-ui.badge>
                                    @endif
                                </td>

                                {{-- Pass / Fail --}}
                                <td class="px-6 py-4">
                                    @if($scale->is_passing)
                                        <x-ui.badge variant="success" size="sm">Pass</x-ui.badge>
                                    @else
                                        <x-ui.badge variant="danger" size="sm">Fail</x-ui.badge>
                                    @endif
                                </td>

                                {{-- Actions --}}
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-3">
                                        <button @click="editing = true"
                                                class="inline-flex items-center gap-1 text-xs font-semibold text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300 transition-colors">
                                            Edit
                                            <x-heroicon-o-pencil class="w-3.5 h-3.5"/>
                                        </button>
                                        <form method="POST"
                                              action="{{ route('examination-hub.grading-system.destroy', $scale) }}"
                                              class="inline"
                                              onsubmit="return confirm('Delete grade {{ $scale->grade_label }}?')">
                                            @csrf @method('DELETE')
                                            <button type="submit"
                                                    class="inline-flex items-center gap-1 text-xs font-semibold text-red-700 dark:text-red-400 hover:text-red-900 dark:hover:text-red-300 transition-colors">
                                                Delete
                                                <x-heroicon-o-trash class="w-3.5 h-3.5"/>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>

                            {{-- ── EDIT ROW (full-width, spans all 6 columns) ── --}}
                            <tr x-show="editing" x-cloak>
                                <td colspan="6"
                                    class="px-6 py-5 bg-slate-50 dark:bg-slate-800/60"
                                    style="border-top: 2px solid #a78bfa;">
                                    <form method="POST"
                                          action="{{ route('examination-hub.grading-system.update', $scale) }}">
                                        @csrf @method('PUT')

                                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-4">
                                            <div>
                                                <label
                                                    class="block text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2"
                                                    style="letter-spacing: 0.08em;">Grade Label</label>
                                                <input type="text" name="grade_label"
                                                       value="{{ $scale->grade_label }}"
                                                       required maxlength="10"
                                                       class="w-full px-4 py-2.5 text-sm border border-slate-200 dark:border-slate-700 focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 dark:bg-slate-800 dark:text-white transition-all"
                                                       style="border-radius: 2px;">
                                            </div>
                                            <div>
                                                <label
                                                    class="block text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2"
                                                    style="letter-spacing: 0.08em;">Min %</label>
                                                <input type="number" name="min_percentage"
                                                       value="{{ $scale->min_percentage }}"
                                                       min="0" max="100" required
                                                       class="w-full px-4 py-2.5 text-sm border border-slate-200 dark:border-slate-700 focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 dark:bg-slate-800 dark:text-white transition-all"
                                                       style="border-radius: 2px;">
                                            </div>
                                            <div>
                                                <label
                                                    class="block text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2"
                                                    style="letter-spacing: 0.08em;">Max %</label>
                                                <input type="number" name="max_percentage"
                                                       value="{{ $scale->max_percentage }}"
                                                       min="0" max="100" required
                                                       class="w-full px-4 py-2.5 text-sm border border-slate-200 dark:border-slate-700 focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 dark:bg-slate-800 dark:text-white transition-all"
                                                       style="border-radius: 2px;">
                                            </div>
                                            <div>
                                                <label
                                                    class="block text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2"
                                                    style="letter-spacing: 0.08em;">GPA</label>
                                                <input type="number" name="grade_point"
                                                       value="{{ $scale->grade_point }}"
                                                       min="0" max="4" step="0.01"
                                                       class="w-full px-4 py-2.5 text-sm border border-slate-200 dark:border-slate-700 focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 dark:bg-slate-800 dark:text-white transition-all"
                                                       style="border-radius: 2px;"
                                                       placeholder="e.g. 3.50">
                                            </div>
                                        </div>

                                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-5">
                                            <div>
                                                <label
                                                    class="block text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2"
                                                    style="letter-spacing: 0.08em;">Badge Colour</label>
                                                <input type="color" name="color_code"
                                                       value="{{ $scale->color_code }}"
                                                       class="h-10 w-full cursor-pointer border border-slate-200 dark:border-slate-700"
                                                       style="border-radius: 2px;">
                                            </div>
                                            <div class="flex flex-col justify-end gap-2 pb-1">
                                                <label
                                                    class="flex items-center gap-2 text-sm text-slate-700 dark:text-slate-300 cursor-pointer select-none">
                                                    <input type="checkbox" name="is_passing" value="1"
                                                           {{ $scale->is_passing ? 'checked' : '' }}
                                                           class="rounded border-slate-300 text-amber-500 focus:ring-amber-500">
                                                    Passing grade
                                                </label>
                                                <label
                                                    class="flex items-center gap-2 text-sm text-slate-700 dark:text-slate-300 cursor-pointer select-none">
                                                    <input type="checkbox" name="is_active" value="1"
                                                           {{ $scale->is_active ? 'checked' : '' }}
                                                           class="rounded border-slate-300 text-amber-500 focus:ring-amber-500">
                                                    Active
                                                </label>
                                            </div>
                                        </div>

                                        <div class="flex items-center gap-3">
                                            <button type="submit"
                                                    class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-semibold text-white transition-all"
                                                    style="border-radius: 2px; background: linear-gradient(135deg, #7c3aed, #a78bfa); box-shadow: 0 2px 10px rgba(124,58,237,0.3);">
                                                Save changes
                                            </button>
                                            <button type="button" @click="editing = false"
                                                    class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-semibold text-slate-700 dark:text-slate-200 transition-all border"
                                                    style="border-radius: 2px; border-color: rgba(0,0,0,0.06); background: linear-gradient(135deg, #f8fafc, #f1f5f9);">
                                                Cancel
                                            </button>
                                        </div>
                                    </form>
                                </td>
                            </tr>

                            </tbody>
                        @endforeach
                    </table>
                </div>
            @endif
        </div>

    </div>{{-- /container --}}

    {{-- ═══════════════════════════════════════════════════════════
         CREATE MODAL
    ═══════════════════════════════════════════════════════════ --}}
    <div id="createModal"
         class="hidden fixed inset-0 z-50 overflow-y-auto"
         aria-labelledby="modal-title"
         role="dialog"
         aria-modal="true">

        {{-- Backdrop --}}
        <div class="fixed inset-0 bg-slate-900/75 transition-opacity"
             onclick="document.getElementById('createModal').classList.add('hidden')"></div>

        <div class="flex min-h-full items-center justify-center p-4">
            <div class="relative w-full max-w-2xl transform overflow-hidden rounded-[2px] border border-slate-200/50 dark:border-slate-800 bg-white dark:bg-slate-900 text-left shadow-xl transition-all">

                {{-- Modal header --}}
                <div class="px-6 py-4 border-b border-slate-200/50 dark:border-slate-800 flex items-center justify-between bg-gradient-to-br from-slate-50 to-slate-100 dark:from-slate-800/50 dark:to-slate-800">
                    <div class="flex items-center gap-2">
                        <div class="w-1 h-5 rounded-[1px] bg-gradient-to-b from-violet-600 to-violet-400"></div>
                        <h3 class="font-bold text-slate-900 dark:text-white text-sm tracking-tight" id="modal-title">
                            Add Grade Scale
                        </h3>
                    </div>
                    <button type="button"
                            onclick="document.getElementById('createModal').classList.add('hidden')"
                            class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 transition-colors">
                        <x-heroicon-o-x-mark class="w-5 h-5"/>
                    </button>
                </div>

                {{-- Modal body --}}
                <form method="POST"
                      action="{{ route('examination-hub.grading-system.store') }}"
                      class="p-6 space-y-5">
                    @csrf

                    <div class="grid grid-cols-2 gap-5">
                        <div>
                            <label class="block text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-widest mb-2">
                                Grade Label <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="grade_label" required maxlength="10"
                                   value="{{ old('grade_label') }}"
                                   placeholder="e.g. A+"
                                   class="w-full px-4 py-2.5 text-sm border border-slate-300 dark:border-slate-700 rounded-[2px] focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 dark:bg-slate-800 dark:text-white transition-all">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-widest mb-2">
                                Badge Colour
                            </label>
                            <input type="color" name="color_code"
                                   value="{{ old('color_code', '#6B7280') }}"
                                   class="h-10 w-full cursor-pointer border border-slate-300 dark:border-slate-700 rounded-[2px]">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-5">
                        <div>
                            <label class="block text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-widest mb-2">
                                Min % <span class="text-red-500">*</span>
                            </label>
                            <input type="number" name="min_percentage"
                                   value="{{ old('min_percentage') }}"
                                   min="0" max="100" required
                                   placeholder="0"
                                   class="w-full px-4 py-2.5 text-sm border border-slate-300 dark:border-slate-700 rounded-[2px] focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 dark:bg-slate-800 dark:text-white transition-all">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-widest mb-2">
                                Max % <span class="text-red-500">*</span>
                            </label>
                            <input type="number" name="max_percentage"
                                   value="{{ old('max_percentage') }}"
                                   min="0" max="100" required
                                   placeholder="100"
                                   class="w-full px-4 py-2.5 text-sm border border-slate-300 dark:border-slate-700 rounded-[2px] focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 dark:bg-slate-800 dark:text-white transition-all">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-widest mb-2">
                            GPA Value <span class="text-slate-400 font-normal normal-case">(optional)</span>
                        </label>
                        <input type="number" name="grade_point"
                               value="{{ old('grade_point') }}"
                               min="0" max="4" step="0.01"
                               placeholder="e.g. 4.00"
                               class="w-full px-4 py-2.5 text-sm border border-slate-300 dark:border-slate-700 rounded-[2px] focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 dark:bg-slate-800 dark:text-white transition-all">
                    </div>

                    <div class="flex items-center gap-6">
                        <label class="flex items-center gap-2 text-sm text-slate-700 dark:text-slate-300 cursor-pointer select-none">
                            <input type="checkbox" name="is_passing" value="1" checked
                                   class="rounded border-slate-300 text-amber-500 focus:ring-amber-500">
                            This is a passing grade
                        </label>
                    </div>

                    {{-- Modal footer --}}
                    <div class="flex justify-end gap-3 pt-4 border-t border-slate-200/50 dark:border-slate-800">
                        <x-ui.button
                            variant="ghost"
                            size="md"
                            onclick="document.getElementById('createModal').classList.add('hidden')"
                        >
                            Cancel
                        </x-ui.button>

                        <x-ui.button
                            variant="primary"
                            size="md"
                            type="submit"
                        >
                            Create Grade Scale
                        </x-ui.button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</x-layouts.app>
