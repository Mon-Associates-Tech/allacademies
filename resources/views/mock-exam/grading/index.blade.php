<x-layouts.app>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-7 font-sans">

        {{-- ── PAGE HEADER ── --}}
        <div class="overflow-hidden rounded-[2px] bg-gradient-to-br from-slate-900 to-slate-800 shadow-xl">
            <div class="h-1 w-full bg-gradient-to-r from-violet-600 via-violet-400 to-indigo-300"></div>
            <div class="px-7 py-6 flex items-start justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-white leading-snug tracking-tight font-serif">
                        Grading Scales
                    </h1>
                    <p class="text-slate-400 mt-2 text-sm">
                        Custom grade scales applied to all your mock exams.
                    </p>
                </div>
                <x-ui.button
                    href="{{ route('mock-exams.index') }}"
                    variant="ghost"
                    size="sm"
                    icon="arrow-left"
                >
                    Back
                </x-ui.button>
            </div>
        </div>

        {{-- ── FLASH MESSAGES ── --}}
        @if(session('success'))
            <x-ui.card variant="accent" accent="success" shadow="true">
                <div class="px-5 py-3 flex items-center gap-2">
                    <x-heroicon-o-check-circle class="w-4 h-4 text-emerald-500 shrink-0"/>
                    <p class="text-sm text-emerald-800 dark:text-emerald-200">{{ session('success') }}</p>
                </div>
            </x-ui.card>
        @endif
        @if(session('info'))
            <x-ui.card variant="accent" accent="info" shadow="true">
                <div class="px-5 py-3 flex items-center gap-2">
                    <x-heroicon-o-information-circle class="w-4 h-4 text-blue-500 shrink-0"/>
                    <p class="text-sm text-blue-800 dark:text-blue-200">{{ session('info') }}</p>
                </div>
            </x-ui.card>
        @endif

        <div class="grid md:grid-cols-3 gap-6">

            {{-- Left: Add / current list --}}
            <div class="md:col-span-2 space-y-4">
                <x-ui.card variant="default" shadow="true">
                    <x-ui.card-header title="Current Scales" accent="primary" />

                    @if(!$hasScales)
                        <div class="p-6 text-center">
                            <p class="text-sm text-slate-500 dark:text-slate-400 mb-4">
                                No custom scales yet. Use the defaults or add your own.
                            </p>
                            <form method="POST" action="{{ route('mock-exams.grade-scales.initialize') }}">
                                @csrf
                                <x-ui.button type="submit" variant="info" icon="sparkles">
                                    Initialize Default A+–F Scale
                                </x-ui.button>
                            </form>
                        </div>
                    @else
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead>
                                    <tr class="border-b border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/30">
                                        <th class="text-left px-5 py-3 text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-widest">Grade</th>
                                        <th class="text-left px-5 py-3 text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-widest">Range</th>
                                        <th class="text-left px-5 py-3 text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-widest">GPA</th>
                                        <th class="text-left px-5 py-3 text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-widest">Passing</th>
                                        <th class="text-left px-5 py-3 text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-widest">Active</th>
                                        <th class="px-5 py-3"></th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 dark:divide-slate-800/50">
                                    @foreach($scales as $scale)
                                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/30 transition-colors" x-data="{ editing: false }">
                                            <td class="px-5 py-3">
                                                <div class="flex items-center gap-2">
                                                    <span class="inline-block w-3 h-3 rounded-full" style="background: {{ $scale->color_code }}"></span>
                                                    <span class="font-bold text-slate-800 dark:text-slate-200">{{ $scale->grade_label }}</span>
                                                </div>
                                            </td>
                                            <td class="px-5 py-3 text-slate-500 dark:text-slate-400">
                                                {{ $scale->min_percentage }}% – {{ $scale->max_percentage }}%
                                            </td>
                                            <td class="px-5 py-3 text-slate-500 dark:text-slate-400">
                                                {{ $scale->grade_point ?? '—' }}
                                            </td>
                                            <td class="px-5 py-3">
                                                <span class="{{ $scale->is_passing ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-500 dark:text-red-400' }} text-xs font-medium">
                                                    {{ $scale->is_passing ? 'Yes' : 'No' }}
                                                </span>
                                            </td>
                                            <td class="px-5 py-3">
                                                <span class="{{ $scale->is_active ? 'text-emerald-600 dark:text-emerald-400' : 'text-slate-400' }} text-xs font-medium">
                                                    {{ $scale->is_active ? 'Active' : 'Inactive' }}
                                                </span>
                                            </td>
                                            <td class="px-5 py-3 text-right space-x-2">
                                                <button @click="editing = !editing" class="text-xs text-violet-600 hover:text-violet-800 dark:text-violet-400 dark:hover:text-violet-300 font-medium">
                                                    Edit
                                                </button>
                                                <form method="POST" action="{{ route('mock-exams.grade-scales.destroy', $scale) }}" class="inline">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="text-xs text-red-500 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300" onclick="return confirm('Delete this grade?')">
                                                        Delete
                                                    </button>
                                                </form>
                                            </td>

                                            {{-- Inline edit row --}}
                                            <td colspan="6" x-show="editing" class="px-5 pb-4">
                                                <form method="POST" action="{{ route('mock-exams.grade-scales.update', $scale) }}"
                                                      class="grid grid-cols-6 gap-2 items-end mt-2">
                                                    @csrf @method('PUT')
                                                    <input name="grade_label" value="{{ $scale->grade_label }}" placeholder="Label" required maxlength="10"
                                                           class="px-3 py-2 text-sm border border-slate-300 dark:border-slate-700 rounded-[2px] dark:bg-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-violet-500/20 focus:border-violet-500">
                                                    <input type="number" name="min_percentage" value="{{ $scale->min_percentage }}" min="0" max="100" placeholder="Min%"
                                                           class="px-3 py-2 text-sm border border-slate-300 dark:border-slate-700 rounded-[2px] dark:bg-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-violet-500/20 focus:border-violet-500">
                                                    <input type="number" name="max_percentage" value="{{ $scale->max_percentage }}" min="0" max="100" placeholder="Max%"
                                                           class="px-3 py-2 text-sm border border-slate-300 dark:border-slate-700 rounded-[2px] dark:bg-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-violet-500/20 focus:border-violet-500">
                                                    <input type="number" name="grade_point" value="{{ $scale->grade_point }}" min="0" max="4" step="0.01" placeholder="GPA"
                                                           class="px-3 py-2 text-sm border border-slate-300 dark:border-slate-700 rounded-[2px] dark:bg-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-violet-500/20 focus:border-violet-500">
                                                    <input type="color" name="color_code" value="{{ $scale->color_code }}"
                                                           class="h-9 w-full border border-slate-300 dark:border-slate-700 rounded-[2px] cursor-pointer">
                                                    <x-ui.button type="submit" variant="success" size="sm">Save</x-ui.button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </x-ui.card>
            </div>

            {{-- Right: Add new --}}
            <div>
                <x-ui.card variant="default" shadow="true">
                    <x-ui.card-header title="Add Grade" accent="success" />
                    <form method="POST" action="{{ route('mock-exams.grade-scales.store') }}" class="p-5 space-y-4">
                        @csrf

                        @error('grade_label')
                            <p class="text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                        @error('min_percentage')
                            <p class="text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror

                        <x-ui.input name="grade_label" label="Grade Label" required placeholder="e.g. A+" maxlength="10" />

                        <div class="grid grid-cols-2 gap-3">
                            <x-ui.input type="number" name="min_percentage" label="Min %" required min="0" max="100" />
                            <x-ui.input type="number" name="max_percentage" label="Max %" required min="0" max="100" />
                        </div>

                        <x-ui.input type="number" name="grade_point" label="Grade Point (optional)" min="0" max="4" step="0.01" />

                        <div class="space-y-2">
                            <label class="text-xs text-slate-500 dark:text-slate-400 uppercase tracking-widest">Colour</label>
                            <input type="color" name="color_code" value="#6B7280"
                                   class="w-full h-9 border border-slate-300 dark:border-slate-700 rounded-[2px] cursor-pointer">
                        </div>

                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="is_passing" value="1" checked class="accent-violet-600 rounded border-slate-300 dark:border-slate-600">
                            <span class="text-sm text-slate-700 dark:text-slate-300">Passing grade</span>
                        </label>

                        <x-ui.button type="submit" variant="success" icon="plus" fullWidth="true">
                            Add Grade
                        </x-ui.button>
                    </form>
                </x-ui.card>
            </div>
        </div>
    </div>
</x-layouts.app>