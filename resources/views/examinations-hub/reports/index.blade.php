<x-layouts.app>
    <x-examinations-hub.navigation active="reports" />
    {{-- ═══════════════════════════════════════════════════════════
     PAGE SHELL
═══════════════════════════════════════════════════════════ --}}
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-7"
     style="font-family: 'system-ui', -apple-system, sans-serif;">

    {{-- ── PAGE HEADER ── --}}
    <div class="overflow-hidden"
         style="border-radius: 2px; background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); box-shadow: 0 4px 24px rgba(0,0,0,0.15);">
        <div class="h-1 w-full" style="background: linear-gradient(90deg, #065f46, #059669, #10b981);"></div>
        <div class="px-7 py-6">
            <h1 class="text-2xl font-bold text-white leading-snug" style="letter-spacing: -0.02em; font-family: 'Georgia', serif;">
                Performance Reports
            </h1>
            <p class="text-slate-400 mt-2 text-sm">
                Generate comprehensive performance analysis with AI or standard reporting
            </p>
        </div>
    </div>

    {{-- ── INFO CARD ── --}}
    <div class="bg-white dark:bg-slate-900 overflow-hidden"
         style="border-radius: 2px; border: 1px solid rgba(37,99,235,0.2); box-shadow: 0 1px 6px rgba(37,99,235,0.08);">
        <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center gap-2"
             style="background: linear-gradient(135deg, #eff6ff, #dbeafe);">
            <div class="w-1 h-5" style="background: linear-gradient(180deg, #2563eb, #60a5fa); border-radius: 1px;"></div>
            <h2 class="font-bold text-slate-900 dark:text-white text-xs uppercase tracking-wider" style="letter-spacing: 0.1em;">About Performance Reports</h2>
        </div>
        <div class="p-5">
            <div class="flex items-start gap-3">
                <div class="w-8 h-8 flex-shrink-0 flex items-center justify-center"
                     style="border-radius: 2px; background: linear-gradient(135deg, #2563eb, #60a5fa);">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="text-sm text-slate-700 dark:text-slate-300">
                    <p>Our reporting system analyzes your examination data and generates comprehensive insights including:</p>
                    <ul class="list-disc list-inside mt-2 space-y-1 ml-2">
                        <li>Subject performance analysis with high and low performers</li>
                        <li>Grade distribution and trends</li>
                        <li>Configured participants vs actual turnout analysis</li>
                        <li>Daily submission patterns and engagement metrics</li>
                        <li>Top and bottom performer identification</li>
                        <li>Actionable recommendations for improvement</li>
                    </ul>
                    <p class="mt-3 font-semibold text-slate-900 dark:text-white"><strong>Choose between:</strong></p>
                    <ul class="list-disc list-inside mt-1 space-y-1 ml-2">
                        <li><strong>AI-Powered:</strong> Advanced insights with natural language analysis (uses AI tokens)</li>
                        <li><strong>Standard:</strong> Structured data-driven report (no AI tokens required)</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    {{-- ── REPORT GENERATION FORM ── --}}
    <div class="bg-white dark:bg-slate-900 overflow-hidden"
         style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 1px 6px rgba(0,0,0,0.04);">
        <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center gap-2">
            <div class="w-1 h-5" style="background: linear-gradient(180deg, #7c3aed, #a78bfa); border-radius: 1px;"></div>
            <h2 class="font-bold text-slate-900 dark:text-white text-xs uppercase tracking-wider" style="letter-spacing: 0.1em;">Generate New Report</h2>
        </div>

        <form method="POST" action="{{ route('examinations-hub.reports.generate') }}" class="p-5 space-y-6">
            @csrf

            {{-- Errors --}}
            @if($errors->any())
                <div class="bg-white dark:bg-slate-900 overflow-hidden"
                     style="border-radius: 2px; border: 1px solid rgba(220,38,38,0.2); box-shadow: 0 1px 6px rgba(220,38,38,0.08);">
                    <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center gap-2"
                         style="background: linear-gradient(135deg, #fef2f2, #fee2e2);">
                        <div class="w-1 h-5" style="background: linear-gradient(180deg, #dc2626, #ef4444); border-radius: 1px;"></div>
                        <h2 class="font-bold text-red-800 dark:text-red-300 text-xs uppercase tracking-wider" style="letter-spacing: 0.1em;">Validation Error</h2>
                    </div>
                    <div class="p-5">
                        <p class="text-sm text-red-700 dark:text-red-400">{{ $errors->first() }}</p>
                    </div>
                </div>
            @endif

            {{-- Date Range --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label for="start_date" class="block text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2" style="letter-spacing: 0.08em;">
                        Start Date <span class="text-red-500">*</span>
                    </label>
                    <input type="date" 
                           name="start_date" 
                           id="start_date" 
                           value="{{ old('start_date', now()->subMonth()->format('Y-m-d')) }}"
                           required
                           class="w-full px-4 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-none focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 dark:bg-slate-800 dark:text-white transition-all"
                           style="border-radius: 2px;">
                </div>

                <div>
                    <label for="end_date" class="block text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2" style="letter-spacing: 0.08em;">
                        End Date <span class="text-red-500">*</span>
                    </label>
                    <input type="date" 
                           name="end_date" 
                           id="end_date" 
                           value="{{ old('end_date', now()->format('Y-m-d')) }}"
                           required
                           class="w-full px-4 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-none focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 dark:bg-slate-800 dark:text-white transition-all"
                           style="border-radius: 2px;">
                </div>
            </div>

            {{-- Report Type --}}
            <div>
                <label class="block text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-3" style="letter-spacing: 0.08em;">
                    Report Type
                </label>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    {{-- AI-Powered --}}
                    <label class="relative flex items-start p-4 border cursor-pointer transition-all"
                           style="border-radius: 2px; border-color: rgba(0,0,0,0.06); background: linear-gradient(135deg, #f8fafc, #f1f5f9);"
                           onmouseover="this.style.borderColor='rgba(124,58,237,0.4)'"
                           onmouseout="this.style.borderColor='rgba(0,0,0,0.06)'">
                        <input type="radio" name="use_ai" value="1" checked class="mt-1 h-4 w-4 text-amber-600 border-slate-300 dark:border-slate-600 focus:ring-amber-500">
                        <div class="ml-3">
                            <div class="flex items-center gap-2">
                                <svg class="h-5 w-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                                </svg>
                                <span class="font-semibold text-slate-900 dark:text-white">AI-Powered Report</span>
                            </div>
                            <p class="text-sm text-slate-600 dark:text-slate-400 mt-1">Advanced insights with natural language analysis and contextual recommendations</p>
                            <p class="text-xs text-amber-700 dark:text-amber-400 mt-2 font-medium">⚠️ Uses 2,000-4,000 AI tokens</p>
                        </div>
                    </label>
                    
                    {{-- Standard --}}
                    <label class="relative flex items-start p-4 border cursor-pointer transition-all"
                           style="border-radius: 2px; border-color: rgba(0,0,0,0.06); background: linear-gradient(135deg, #f8fafc, #f1f5f9);"
                           onmouseover="this.style.borderColor='rgba(5,150,105,0.4)'"
                           onmouseout="this.style.borderColor='rgba(0,0,0,0.06)'">
                        <input type="radio" name="use_ai" value="0" class="mt-1 h-4 w-4 text-emerald-600 border-slate-300 dark:border-slate-600 focus:ring-emerald-500">
                        <div class="ml-3">
                            <div class="flex items-center gap-2">
                                <svg class="h-5 w-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                <span class="font-semibold text-slate-900 dark:text-white">Standard Report</span>
                            </div>
                            <p class="text-sm text-slate-600 dark:text-slate-400 mt-1">Structured data-driven report with key metrics and performance indicators</p>
                            <p class="text-xs text-emerald-700 dark:text-emerald-400 mt-2 font-medium">✓ No AI tokens required</p>
                        </div>
                    </label>
                </div>
            </div>

            {{-- Tip Card --}}
            <div class="bg-white dark:bg-slate-900 px-4 py-3 flex items-start gap-3"
                 style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06); background: linear-gradient(135deg, #f8fafc, #f1f5f9);">
                <svg class="h-4 w-4 text-slate-500 dark:text-slate-400 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <p class="text-sm text-slate-700 dark:text-slate-300">
                    <strong>Tip:</strong> Use standard reports for quick overviews and AI-powered reports for deeper insights and recommendations.
                </p>
            </div>

            {{-- Actions --}}
            <div class="flex items-center justify-between pt-2">
                <a href="{{ route('examinations-hub.dashboard') }}" 
                   class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-semibold text-slate-700 dark:text-slate-200 transition-all border"
                   style="border-radius: 2px; border-color: rgba(0,0,0,0.06); background: linear-gradient(135deg, #f8fafc, #f1f5f9);">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Cancel
                </a>
                <button type="submit" 
                        class="inline-flex items-center gap-2 px-6 py-2.5 text-sm font-semibold text-white transition-all"
                        style="border-radius: 2px; background: linear-gradient(135deg, #7c3aed, #a78bfa); box-shadow: 0 2px 10px rgba(124,58,237,0.3);">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Generate Report
                </button>
            </div>
        </form>
    </div>

    {{-- ── QUICK DATE PRESETS ── --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <button onclick="setDateRange(7)" 
                class="px-4 py-3 text-sm font-semibold text-slate-700 dark:text-slate-200 transition-all border text-center"
                style="border-radius: 2px; border-color: rgba(0,0,0,0.06); background: linear-gradient(135deg, #f8fafc, #f1f5f9);">
            Last 7 Days
        </button>
        <button onclick="setDateRange(30)" 
                class="px-4 py-3 text-sm font-semibold text-slate-700 dark:text-slate-200 transition-all border text-center"
                style="border-radius: 2px; border-color: rgba(0,0,0,0.06); background: linear-gradient(135deg, #f8fafc, #f1f5f9);">
            Last 30 Days
        </button>
        <button onclick="setDateRange(90)" 
                class="px-4 py-3 text-sm font-semibold text-slate-700 dark:text-slate-200 transition-all border text-center"
                style="border-radius: 2px; border-color: rgba(0,0,0,0.06); background: linear-gradient(135deg, #f8fafc, #f1f5f9);">
            Last 3 Months
        </button>
        <button onclick="setDateRange(365)" 
                class="px-4 py-3 text-sm font-semibold text-slate-700 dark:text-slate-200 transition-all border text-center"
                style="border-radius: 2px; border-color: rgba(0,0,0,0.06); background: linear-gradient(135deg, #f8fafc, #f1f5f9);">
            Last Year
        </button>
    </div>

</div>{{-- /container --}}

<script>
function setDateRange(days) {
    const endDate = new Date();
    const startDate = new Date();
    startDate.setDate(startDate.getDate() - days);
    
    document.getElementById('start_date').value = startDate.toISOString().split('T')[0];
    document.getElementById('end_date').value = endDate.toISOString().split('T')[0];
}
</script>
</x-layouts.app>