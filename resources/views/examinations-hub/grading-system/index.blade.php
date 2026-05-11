<x-layouts.app>
    <x-examinations-hub.navigation active="grading-system" />

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
                    <h1 class="text-2xl font-bold text-white leading-snug" style="letter-spacing: -0.02em; font-family: 'Georgia', serif;">
                        Grading System
                    </h1>
                    <p class="text-slate-400 mt-2 text-sm">
                        Define and manage grade scales for your examinations
                    </p>
                </div>
                <div class="flex items-center gap-3">
                    @if($gradeScales->where('is_default', true)->isEmpty())
                        <form method="POST" action="{{ route('examinations-hub.grading-system.initialize') }}">
                            @csrf
                            <button type="submit" 
                                    class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-semibold text-white transition-all"
                                    style="border-radius: 2px; background: linear-gradient(135deg, #065f46, #059669); box-shadow: 0 2px 10px rgba(5,150,105,0.3);">
                                <x-heroicon-o-plus class="w-4 h-4" />
                                Initialize Default
                            </button>
                        </form>
                    @endif
                    <button onclick="document.getElementById('createModal').classList.remove('hidden')" 
                            class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-semibold text-white transition-all"
                            style="border-radius: 2px; background: linear-gradient(135deg, #7c3aed, #a78bfa); box-shadow: 0 2px 10px rgba(124,58,237,0.3);">
                        <x-heroicon-o-plus class="w-4 h-4" />
                        Add Grade Scale
                    </button>
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
                        <x-heroicon-o-check class="w-4 h-4 text-white" />
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
                        <x-heroicon-o-information-circle class="w-4 h-4 text-white" />
                    </div>
                    <p class="text-sm text-blue-700 dark:text-blue-300">{{ session('info') }}</p>
                </div>
            </div>
        @endif

        {{-- ── INFO CARD ── --}}
        <div class="bg-white dark:bg-slate-900 overflow-hidden"
             style="border-radius: 2px; border: 1px solid rgba(37,99,235,0.2); box-shadow: 0 1px 6px rgba(37,99,235,0.08);">
            <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center gap-2"
                 style="background: linear-gradient(135deg, #eff6ff, #dbeafe);">
                <div class="w-1 h-5" style="background: linear-gradient(180deg, #2563eb, #60a5fa); border-radius: 1px;"></div>
                <h2 class="font-bold text-slate-900 dark:text-white text-xs uppercase tracking-wider" style="letter-spacing: 0.1em;">About Grading System</h2>
            </div>
            <div class="p-5">
                <div class="flex items-start gap-3">
                    <div class="w-8 h-8 flex-shrink-0 flex items-center justify-center"
                         style="border-radius: 2px; background: linear-gradient(135deg, #2563eb, #60a5fa);">
                        <x-heroicon-o-information-circle class="w-4 h-4 text-white" />
                    </div>
                    <div class="text-sm text-slate-700 dark:text-slate-300">
                        <p>Define custom grade scales for your school. You can create:</p>
                        <ul class="list-disc list-inside mt-2 space-y-1 ml-2">
                            <li><strong>Default Grades:</strong> Applied to all exams when no specific level is set</li>
                            <li><strong>Level-Specific Grades:</strong> Custom scales for specific academic levels</li>
                        </ul>
                        <p class="mt-2">The system automatically selects the most appropriate grade scale based on the exam's academic level.</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── GRADE SCALES TABLE ── --}}
        <div class="bg-white dark:bg-slate-900 overflow-hidden"
             style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 1px 6px rgba(0,0,0,0.04);">
            <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center gap-2">
                <div class="w-1 h-5" style="background: linear-gradient(180deg, #7c3aed, #a78bfa); border-radius: 1px;"></div>
                <h2 class="font-bold text-slate-900 dark:text-white text-xs uppercase tracking-wider" style="letter-spacing: 0.1em;">Grade Scales</h2>
            </div>

            @if($gradeScales->isEmpty())
                <div class="p-12 text-center">
                    <div class="w-12 h-12 mx-auto flex items-center justify-center mb-4"
                         style="border-radius: 2px; background: linear-gradient(135deg, #64748b, #94a3b8);">
                        <x-heroicon-o-clipboard-document-list class="w-6 h-6 text-white" />
                    </div>
                    <h3 class="text-sm font-semibold text-slate-900 dark:text-white">No grade scales</h3>
                    <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Get started by initializing the default grading system or creating a custom one.</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-slate-100 dark:border-slate-800 bg-slate-50 dark:bg-slate-800/50">
                                <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider" style="letter-spacing: 0.08em;">Grade</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider" style="letter-spacing: 0.08em;">Name</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider" style="letter-spacing: 0.08em;">Score Range</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider" style="letter-spacing: 0.08em;">Grade Point</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider" style="letter-spacing: 0.08em;">Level</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider" style="letter-spacing: 0.08em;">Remarks</th>
                                <th class="px-6 py-3 text-right text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider" style="letter-spacing: 0.08em;">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50 dark:divide-slate-800">
                            @foreach($gradeScales as $scale)
                                <tr class="hover:bg-amber-50/40 dark:hover:bg-slate-800/40 transition-colors">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-2">
                                            <span class="text-lg font-bold text-amber-700 dark:text-amber-400">{{ $scale->letter_grade }}</span>
                                            @if($scale->is_default)
                                                <x-ui.badge variant="success" size="sm">Default</x-ui.badge>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-slate-700 dark:text-slate-300">{{ $scale->name }}</td>
                                    <td class="px-6 py-4">
                                        <span class="inline-flex items-center justify-center text-xs font-medium px-2.5 py-1 border text-slate-700 dark:text-slate-300"
                                              style="border-radius: 2px; border-color: rgba(0,0,0,0.06); background: linear-gradient(135deg, #f8fafc, #f1f5f9);">
                                            {{ $scale->min_score }}% - {{ $scale->max_score }}%
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-slate-700 dark:text-slate-300">{{ $scale->grade_point ?? 'N/A' }}</td>
                                    <td class="px-6 py-4 text-slate-700 dark:text-slate-300">
                                        {{ $scale->academicLevel?->name ?? 'All Levels' }}
                                    </td>
                                    <td class="px-6 py-4 text-slate-700 dark:text-slate-300">{{ $scale->remarks }}</td>
                                    <td class="px-6 py-4 text-right">
                                        <form method="POST" action="{{ route('examinations-hub.grading-system.destroy', $scale) }}" class="inline" onsubmit="return confirm('Are you sure you want to delete this grade scale?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="inline-flex items-center gap-1 text-xs font-semibold text-red-700 dark:text-red-400 hover:text-red-900 dark:hover:text-red-300 transition-colors">
                                                Delete
                                                <x-heroicon-o-trash class="w-3.5 h-3.5" />
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

    </div>{{-- /container --}}

    {{-- ── CREATE MODAL ── --}}
    <div id="createModal" class="hidden fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        {{-- Backdrop --}}
        <div class="fixed inset-0 bg-slate-900/75 transition-opacity" onclick="document.getElementById('createModal').classList.add('hidden')"></div>

        <div class="flex min-h-full items-center justify-center p-4">
            <div class="relative w-full max-w-2xl transform overflow-hidden rounded-[2px] bg-white dark:bg-slate-900 text-left shadow-xl transition-all"
                 style="border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 4px 24px rgba(0,0,0,0.15);">
                
                {{-- Modal Header --}}
                <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between"
                     style="background: linear-gradient(135deg, #f8fafc, #f1f5f9);">
                    <div class="flex items-center gap-2">
                        <div class="w-1 h-5" style="background: linear-gradient(180deg, #7c3aed, #a78bfa); border-radius: 1px;"></div>
                        <h3 class="font-bold text-slate-900 dark:text-white text-sm" id="modal-title">Add Grade Scale</h3>
                    </div>
                    <button type="button" onclick="document.getElementById('createModal').classList.add('hidden')" 
                            class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 transition-colors">
                        <x-heroicon-o-x-mark class="w-5 h-5" />
                    </button>
                </div>

                {{-- Modal Body --}}
                <form method="POST" action="{{ route('examinations-hub.grading-system.store') }}" class="p-6 space-y-5">
                    @csrf
                    <div class="grid grid-cols-2 gap-5">
                        <div>
                            <label class="block text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2" style="letter-spacing: 0.08em;">
                                Letter Grade <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="letter_grade" required 
                                   class="w-full px-4 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-none focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 dark:bg-slate-800 dark:text-white transition-all"
                                   style="border-radius: 2px;" placeholder="e.g., A+">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2" style="letter-spacing: 0.08em;">
                                Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="name" required 
                                   class="w-full px-4 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-none focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 dark:bg-slate-800 dark:text-white transition-all"
                                   style="border-radius: 2px;" placeholder="e.g., Excellent">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-5">
                        <div>
                            <label class="block text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2" style="letter-spacing: 0.08em;">
                                Min Score (%) <span class="text-red-500">*</span>
                            </label>
                            <input type="number" name="min_score" min="0" max="100" step="0.01" required 
                                   class="w-full px-4 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-none focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 dark:bg-slate-800 dark:text-white transition-all"
                                   style="border-radius: 2px;">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2" style="letter-spacing: 0.08em;">
                                Max Score (%) <span class="text-red-500">*</span>
                            </label>
                            <input type="number" name="max_score" min="0" max="100" step="0.01" required 
                                   class="w-full px-4 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-none focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 dark:bg-slate-800 dark:text-white transition-all"
                                   style="border-radius: 2px;">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-5">
                        <div>
                            <label class="block text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2" style="letter-spacing: 0.08em;">
                                Grade Point
                            </label>
                            <input type="number" name="grade_point" min="0" max="5" step="0.1" 
                                   class="w-full px-4 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-none focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 dark:bg-slate-800 dark:text-white transition-all"
                                   style="border-radius: 2px;" placeholder="e.g., 4.0">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2" style="letter-spacing: 0.08em;">
                                Academic Level
                            </label>
                            <select name="academic_level_id" 
                                    class="w-full px-4 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-none focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 dark:bg-slate-800 dark:text-white transition-all"
                                    style="border-radius: 2px;">
                                <option value="">All Levels (Default)</option>
                                @foreach($academicLevels as $level)
                                    <option value="{{ $level->id }}">{{ $level->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2" style="letter-spacing: 0.08em;">
                            Remarks
                        </label>
                        <input type="text" name="remarks" 
                               class="w-full px-4 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-none focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 dark:bg-slate-800 dark:text-white transition-all"
                               style="border-radius: 2px;" placeholder="e.g., Outstanding performance">
                    </div>

                    <div class="flex items-center">
                        <input type="checkbox" name="is_default" value="1" id="is_default" 
                               class="h-4 w-4 rounded border-slate-300 dark:border-slate-600 text-amber-600 focus:ring-amber-500">
                        <label for="is_default" class="ml-2 block text-sm text-slate-700 dark:text-slate-300">
                            Set as default (applies to all levels if no specific level is selected)
                        </label>
                    </div>

                    {{-- Modal Footer --}}
                    <div class="flex justify-end gap-3 pt-4 border-t border-slate-100 dark:border-slate-800">
                        <button type="button" onclick="document.getElementById('createModal').classList.add('hidden')" 
                                class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-semibold text-slate-700 dark:text-slate-200 transition-all border"
                                style="border-radius: 2px; border-color: rgba(0,0,0,0.06); background: linear-gradient(135deg, #f8fafc, #f1f5f9);">
                            Cancel
                        </button>
                        <button type="submit" 
                                class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-semibold text-white transition-all"
                                style="border-radius: 2px; background: linear-gradient(135deg, #7c3aed, #a78bfa); box-shadow: 0 2px 10px rgba(124,58,237,0.3);">
                            Create Grade Scale
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</x-layouts.app>