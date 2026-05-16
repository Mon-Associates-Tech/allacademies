<x-layouts.exam>
    <div class="min-h-screen bg-slate-50 dark:bg-slate-950">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 py-8 sm:py-10">
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-sm overflow-hidden">
                <div class="px-6 py-5 border-b border-slate-200 dark:border-slate-800">
                    <p class="text-xs uppercase tracking-wider text-slate-500 dark:text-slate-400">Examination</p>
                    <h1 class="mt-1 text-xl font-semibold text-slate-900 dark:text-white">{{ $exam->title }}</h1>
                    @if($exam->instructions)
                        <p class="mt-3 text-sm text-slate-600 dark:text-slate-300 whitespace-pre-line">{{ $exam->instructions }}</p>
                    @endif
                </div>

                <div class="px-6 py-5 grid grid-cols-1 sm:grid-cols-3 gap-3 text-sm">
                    <div class="p-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-sm">
                        <p class="text-slate-500 dark:text-slate-400">Sections</p>
                        <p class="mt-1 font-semibold text-slate-900 dark:text-white">{{ $exam->sections->count() }}</p>
                    </div>
                    <div class="p-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-sm">
                        <p class="text-slate-500 dark:text-slate-400">Questions</p>
                        <p class="mt-1 font-semibold text-slate-900 dark:text-white">{{ $exam->sections->sum('questions_count') }}</p>
                    </div>
                    <div class="p-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-sm">
                        <p class="text-slate-500 dark:text-slate-400">Duration</p>
                        <p class="mt-1 font-semibold text-slate-900 dark:text-white">
                            {{ $exam->duration_in_minutes ? $exam->duration_in_minutes . ' minutes' : 'No limit' }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="mt-5 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-800">
                    <h2 class="text-sm font-semibold text-slate-900 dark:text-white">Sections</h2>
                </div>

                <div class="divide-y divide-slate-200 dark:divide-slate-800">
                    @foreach($exam->sections as $index => $section)
                        <div class="px-6 py-4 flex items-center justify-between gap-4">
                            <div>
                                <p class="text-sm font-medium text-slate-900 dark:text-white">
                                    Section {{ $index + 1 }}: {{ $section->title }}
                                </p>
                                <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">
                                    {{ $section->questions_count }} questions
                                    @if($section->time_limit_minutes)
                                        · {{ $section->time_limit_minutes }} min limit
                                    @endif
                                </p>
                            </div>

                            <a href="{{ route('examination-hub.take.section', [$exam, $index]) }}"
                               class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-slate-800 hover:bg-slate-700 rounded-sm">
                                {{ $index === 0 ? 'Start' : 'Open' }}
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</x-layouts.exam>
