<x-layouts.app>
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6"
     style="font-family: 'system-ui', -apple-system, sans-serif;">

    {{-- PAGE HEADER --}}
    <div class="overflow-hidden"
         style="border-radius: 2px; background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); box-shadow: 0 4px 24px rgba(0,0,0,0.15);">
        <div class="h-1 w-full" style="background: linear-gradient(90deg, #7c3aed, #a78bfa, #c4b5fd);"></div>
        <div class="px-7 py-6 flex items-start justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-white leading-snug"
                    style="letter-spacing: -0.02em; font-family: 'Georgia', serif;">
                    Live Monitoring: {{ $mockExam->title }}
                </h1>
                <p class="text-slate-400 mt-2 text-sm">
                    Real-time participant activity and progress tracking
                </p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('mock-exams.show', $mockExam) }}"
                   class="inline-flex items-center gap-2 px-4 py-2 text-xs font-semibold text-slate-300 border border-slate-600 hover:border-slate-400 transition-all"
                   style="border-radius: 2px;">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Back to Exam
                </a>
            </div>
        </div>
    </div>

    {{-- LIVE MONITOR COMPONENT --}}
    <livewire:mock-exam.live-monitor :mock-exam="$mockExam" />

</div>
</x-layouts.app>
