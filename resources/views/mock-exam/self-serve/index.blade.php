<x-layouts.app>
    <div class="relative overflow-hidden" style="border-radius: 2px;">
        <div class="absolute inset-x-0 top-0 h-1 bg-purple-500"></div>
        <div class="bg-gradient-to-r from-[#0c1f3f] via-[#132a52] to-[#0c1f3f] px-6 py-10 sm:px-10">
            <h1 class="text-2xl sm:text-3xl font-semibold text-white">Generate a Practice Exam</h1>
            <p class="mt-2 max-w-2xl text-sm text-slate-300">
                Pick a template below and we'll build a printable exam paper for you in seconds — pulled fresh from the question bank, ready to download as a PDF.
            </p>
        </div>
    </div>

    @if ($subscribedSubjectIds->isEmpty())
        <div class="mt-6 flex items-start gap-3 border border-amber-300 bg-amber-50 px-4 py-3 dark:border-amber-700 dark:bg-amber-900/20" style="border-radius: 2px;">
            <svg class="mt-0.5 h-5 w-5 shrink-0 text-amber-600 dark:text-amber-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
            </svg>
            <div class="text-sm text-amber-800 dark:text-amber-200">
                <p class="font-medium">No active subscription yet</p>
                <p class="mt-1">You'll need an active mock exam subscription for a subject before you can generate an exam in it.</p>
                <a href="{{ route('mock-exams.subscription.dashboard') }}" class="mt-2 inline-block font-medium text-amber-900 underline dark:text-amber-100">
                    View subscription plans →
                </a>
            </div>
        </div>
    @endif

    <div class="mt-8" x-data="{ activeSubject: 'all' }">
        @php $subjects = $templates->pluck('academicSubject')->filter()->unique('id')->values(); @endphp

        @if ($subjects->count() > 1)
            <div class="flex flex-wrap gap-2">
                <button
                    type="button"
                    @click="activeSubject = 'all'"
                    :class="activeSubject === 'all' ? 'bg-[#0c1f3f] text-white dark:bg-purple-600' : 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300'"
                    class="px-3 py-1.5 text-sm font-medium transition"
                    style="border-radius: 2px;"
                >
                    All subjects
                </button>
                @foreach ($subjects as $subject)
                    <button
                        type="button"
                        @click="activeSubject = '{{ $subject->id }}'"
                        :class="activeSubject === '{{ $subject->id }}' ? 'bg-[#0c1f3f] text-white dark:bg-purple-600' : 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300'"
                        class="px-3 py-1.5 text-sm font-medium transition"
                        style="border-radius: 2px;"
                    >
                        {{ $subject->name }}
                    </button>
                @endforeach
            </div>
        @endif

        <div class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
            @forelse ($templates as $template)
                @php
                    $questionCount = collect($template->sections_config ?? [])->sum(fn ($s) => (int) ($s['question_count'] ?? 0));
                    $sectionCount = count($template->sections_config ?? []);
                    $canGenerate =  true; // $template->academic_subject_id && $subscribedSubjectIds->contains($template->academic_subject_id);
                @endphp
                <div
                    x-show="activeSubject === 'all' || activeSubject === '{{ $template->academic_subject_id }}'"
                    class="flex flex-col border border-slate-200 bg-white p-5 dark:border-slate-700 dark:bg-slate-900"
                    style="border-radius: 2px;"
                >
                    <div class="flex items-center gap-2 text-xs font-medium uppercase tracking-wide text-slate-500 dark:text-slate-400">
                        <span>{{ $template->academicGroup?->name }}</span>
                        @if ($template->academicLevel)
                            <span>&middot;</span>
                            <span>{{ $template->academicLevel->name }}</span>
                        @endif
                    </div>

                    <h3 class="mt-2 text-base font-semibold text-slate-900 dark:text-white">
                        {{ $template->getDisplayName() }}
                    </h3>

                    @if ($template->description)
                        <p class="mt-1 text-sm text-slate-600 dark:text-slate-400 line-clamp-2">
                            {{ $template->description }}
                        </p>
                    @endif

                    <div class="mt-3 flex flex-wrap gap-3 text-xs text-slate-500 dark:text-slate-400">
                        <span>{{ $sectionCount }} section{{ $sectionCount === 1 ? '' : 's' }}</span>
                        <span>&middot;</span>
                        <span>{{ $questionCount }} question{{ $questionCount === 1 ? '' : 's' }}</span>
                        @if ($template->default_duration_minutes)
                            <span>&middot;</span>
                            <span>{{ $template->default_duration_minutes }} min</span>
                        @endif
                    </div>

                    <span class="mt-3 inline-flex w-fit items-center gap-1 bg-slate-100 px-2 py-0.5 text-[11px] font-medium uppercase tracking-wide text-slate-600 dark:bg-slate-800 dark:text-slate-300" style="border-radius: 2px;">
                        Print &middot; PDF
                    </span>

                    <div class="mt-4">
                        @if ($canGenerate)
                            <form method="POST" action="{{ route('mock-exams.generate.store', $template) }}">
                                @csrf
                                <button
                                    type="submit"
                                    class="w-full bg-[#0c1f3f] px-4 py-2 text-sm font-medium text-white transition hover:bg-purple-700 dark:bg-purple-600 dark:hover:bg-purple-500"
                                    style="border-radius: 2px;"
                                >
                                    Quick Generate
                                </button>
                            </form>
                        @else
                            <a
                            href="{{ route('mock-exams.subscription.dashboard') }}"
                            class="block w-full border border-slate-300 px-4 py-2 text-center text-sm font-medium text-slate-500 dark:border-slate-700 dark:text-slate-400"
                            style="border-radius: 2px;"
                            >
                            Subscribe to unlock
                            </a>
                        @endif
                    </div>
                </div>
            @empty
                <div class="col-span-full py-12 text-center text-sm text-slate-500 dark:text-slate-400">
                    No templates are available to generate from right now.
                </div>
            @endforelse
        </div>
    </div>
</x-layouts.app>
