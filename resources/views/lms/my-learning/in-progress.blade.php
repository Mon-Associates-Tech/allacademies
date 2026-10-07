<x-layouts.app>
    <x-exam.page>

        <x-exam.crumbs :items="[
            ['label' => 'My Learning', 'href' => route('my-learning.index')],
            ['label' => 'In Progress'],
        ]" />

        <x-exam.header
            eyebrow="LEARNING RECORDS · ACTIVE STUDY"
            title="Courses In Progress"
            sub="Continue where you left off and complete your courses."
            :lines="['NOW', 'READING']"
        />

        <x-exam.flash />

        <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
            <p class="font-mono text-[10.5px] tracking-[0.14em] text-muted">ACTIVE STUDY · {{ $enrollments->total() }} IN SESSION</p>
            <a href="{{ route('lms.courses.index') }}"
               class="inline-flex items-center gap-1.5 whitespace-nowrap rounded bg-primary px-5 py-2.5 text-[13.5px] font-semibold tracking-wide text-on-primary shadow-[0_1px_0_rgba(20,24,34,0.2),0_8px_18px_-10px_rgba(20,24,34,0.5)] transition hover:-translate-y-px hover:bg-primary-hi motion-reduce:transition-none">
                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M12 4v16m8-8H4"/></svg>
                Find More Courses
            </a>
        </div>

        <x-exam.learning-tabs :counts="['in_progress' => $enrollments->total()]" />

        @if($enrollments->count() > 0)
            <div class="grid grid-cols-1 gap-6 md:grid-cols-2 xl:grid-cols-3">
                @foreach($enrollments as $enrollment)
                    <article class="animate-rise overflow-hidden rounded-md border border-line bg-surface shadow-[0_1px_2px_rgba(20,24,34,0.04),0_18px_40px_-30px_rgba(20,24,34,0.25)] transition hover:-translate-y-0.5 hover:shadow-[0_10px_30px_-12px_rgba(20,24,34,0.30)] motion-reduce:transition-none"
                             style="animation-delay: {{ $loop->index * 40 }}ms">

                        {{-- Cover --}}
                        <div class="relative aspect-video bg-surface-2">
                            @if($enrollment->course->thumbnail)
                                <img src="{{ asset('storage/' . $enrollment->course->thumbnail) }}" alt="{{ $enrollment->course->title }}" class="h-full w-full object-cover">
                            @else
                                <div class="grid h-full w-full place-items-center">
                                    <svg class="h-10 w-10 text-line-strong" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                                </div>
                            @endif

                            <div class="absolute right-3 top-3 rounded-full bg-surface/90 p-0.5 shadow-sm backdrop-blur-[2px]">
                                <x-exam.badge tone="warn">In Progress</x-exam.badge>
                            </div>

                            {{-- Progress overlay --}}
                            <div class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-black/70 to-transparent p-4">
                                <div class="mb-1.5 flex items-center justify-between font-mono text-[10px] tracking-wider text-white">
                                    <span>PROGRESS</span>
                                    <span>{{ number_format($enrollment->progress_percentage, 0) }}%</span>
                                </div>
                                <div class="h-1.5 w-full overflow-hidden rounded-full bg-white/30">
                                    <div class="h-full rounded-full bg-white transition-all duration-300" style="width: {{ $enrollment->progress_percentage }}%"></div>
                                </div>
                            </div>
                        </div>

                        <div class="p-5">
                            <h3 class="line-clamp-2 font-display text-base font-bold leading-snug text-ink">{{ $enrollment->course->title }}</h3>

                            @if($enrollment->course->creator)
                                <div class="mt-2 flex items-center gap-2">
                                    @if($enrollment->course->creator->avatar)
                                        <img class="h-6 w-6 rounded-full object-cover ring-1 ring-line" src="{{ asset('storage/' . $enrollment->course->creator->avatar) }}" alt="{{ $enrollment->course->creator->name }}">
                                    @else
                                        <div class="grid h-6 w-6 place-items-center rounded-full bg-surface-2 font-mono text-[10px] font-bold text-muted ring-1 ring-line">
                                            {{ strtoupper(substr($enrollment->course->creator->name, 0, 1)) }}
                                        </div>
                                    @endif
                                    <span class="text-xs text-ink-soft">{{ $enrollment->course->creator->name }}</span>
                                </div>
                            @endif

                            <div class="mt-4 flex items-center justify-between border-t border-dashed border-line pt-3 font-mono text-[10.5px] tracking-wide text-muted">
                                <span class="flex items-center gap-1.5">
                                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
                                    {{ $enrollment->course->chapters->count() }} {{ strtoupper(Str::plural('chapter', $enrollment->course->chapters->count())) }}
                                </span>
                                <span class="flex items-center gap-1.5">
                                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    STARTED {{ strtoupper($enrollment->started_at ? $enrollment->started_at->diffForHumans() : 'RECENTLY') }}
                                </span>
                            </div>

                            <div class="mt-4">
                                <a href="{{ route('lms.courses.learn', $enrollment->course->slug) }}"
                                   class="inline-flex w-full items-center justify-center gap-1.5 whitespace-nowrap rounded bg-primary px-4 py-2.5 text-[13px] font-semibold tracking-wide text-on-primary transition hover:-translate-y-px hover:bg-primary-hi motion-reduce:transition-none">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    Continue Learning
                                </a>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>

            @if($enrollments->hasPages())
                <div class="mt-8">{{ $enrollments->links() }}</div>
            @endif
        @else
            <x-exam.empty
                title="No courses in progress"
                description="Start learning a course to see it here. Your progress will be tracked automatically."
            >
                <div class="mt-5 flex flex-col justify-center gap-2 sm:flex-row">
                    <a href="{{ route('my-learning.index') }}"
                       class="inline-flex items-center justify-center whitespace-nowrap rounded border border-line-strong px-5 py-2.5 text-[13.5px] font-semibold text-ink-soft transition hover:bg-surface-2 hover:text-ink">
                        View All Courses
                    </a>
                    <a href="{{ route('lms.courses.index') }}"
                       class="inline-flex items-center justify-center gap-1.5 whitespace-nowrap rounded bg-primary px-5 py-2.5 text-[13.5px] font-semibold tracking-wide text-on-primary transition hover:-translate-y-px hover:bg-primary-hi motion-reduce:transition-none">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        Browse Courses
                    </a>
                </div>
            </x-exam.empty>
        @endif

    </x-exam.page>
</x-layouts.app>
