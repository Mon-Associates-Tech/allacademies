<x-layouts.app>
    <x-exam.page>

        <x-exam.crumbs :items="[
            ['label' => 'My Learning', 'href' => route('my-learning.index')],
            ['label' => 'Completed'],
        ]" />

        <x-exam.header
            eyebrow="LEARNING RECORDS · FINISHED STUDY"
            title="Completed Courses"
            sub="Congratulations on your achievements! View your certificates below."
            :lines="['ALL', 'DONE']"
        />

        <x-exam.flash />

        <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
            <p class="font-mono text-[10.5px] tracking-[0.14em] text-muted">COMPLETED RECORDS · {{ $enrollments->total() }}</p>
            <a href="{{ route('my-learning.certificates') }}"
               class="inline-flex items-center gap-1.5 whitespace-nowrap rounded bg-ok px-5 py-2.5 text-[13.5px] font-semibold tracking-wide text-white shadow-[0_1px_0_rgba(20,24,34,0.2),0_8px_18px_-10px_rgba(30,122,77,0.5)] transition hover:-translate-y-px hover:opacity-90 motion-reduce:transition-none">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/></svg>
                View All Certificates
            </a>
        </div>

        <x-exam.learning-tabs :counts="['completed' => $enrollments->total()]" />

        @if($enrollments->count() > 0)
            <div class="grid grid-cols-1 gap-6 md:grid-cols-2 xl:grid-cols-3">
                @foreach($enrollments as $enrollment)
                    @php
                        $gradeTone = $enrollment->final_grade !== null
                            ? ($enrollment->final_grade >= 90 ? 'ok' : ($enrollment->final_grade >= 70 ? 'primary' : ($enrollment->final_grade >= 50 ? 'warn' : 'danger')))
                            : null;
                    @endphp

                    <article class="animate-rise overflow-hidden rounded-md border border-line bg-surface shadow-[0_1px_2px_rgba(20,24,34,0.04),0_18px_40px_-30px_rgba(20,24,34,0.25)] transition hover:-translate-y-0.5 hover:shadow-[0_10px_30px_-12px_rgba(20,24,34,0.30)] motion-reduce:transition-none"
                             style="animation-delay: {{ $loop->index * 40 }}ms">

                        {{-- Cover --}}
                        <div class="relative aspect-video bg-surface-2">
                            @if($enrollment->course->thumbnail)
                                <img src="{{ asset('storage/' . $enrollment->course->thumbnail) }}" alt="{{ $enrollment->course->title }}" class="h-full w-full object-cover">
                            @else
                                <div class="grid h-full w-full place-items-center">
                                    <svg class="h-10 w-10 text-ok/50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                </div>
                            @endif

                            <div class="absolute right-3 top-3 rounded-full bg-surface/90 p-0.5 shadow-sm backdrop-blur-[2px]">
                                <x-exam.badge tone="ok">Completed</x-exam.badge>
                            </div>

                            @if($enrollment->final_grade !== null)
                                <div class="absolute bottom-3 left-3 rounded-full bg-surface/90 p-0.5 shadow-sm backdrop-blur-[2px]">
                                    <x-exam.badge :tone="$gradeTone">Grade {{ number_format($enrollment->final_grade, 0) }}%</x-exam.badge>
                                </div>
                            @endif
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

                            <div class="mt-4 flex items-center gap-1.5 border-t border-dashed border-line pt-3 font-mono text-[10.5px] tracking-wide text-muted">
                                <svg class="h-3.5 w-3.5 text-ok" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                COMPLETED {{ $enrollment->completed_at ? strtoupper($enrollment->completed_at->format('M d, Y')) : '—' }}
                            </div>

                            <div class="mt-4 flex gap-2">
                                <a href="{{ route('lms.courses.certificate', $enrollment->course) }}"
                                   class="inline-flex flex-1 items-center justify-center gap-1.5 whitespace-nowrap rounded bg-ok px-4 py-2.5 text-[13px] font-semibold tracking-wide text-white transition hover:-translate-y-px hover:opacity-90 motion-reduce:transition-none">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/></svg>
                                    Certificate
                                </a>
                                <a href="{{ route('lms.courses.learn', $enrollment->course->slug) }}"
                                   class="inline-flex items-center justify-center gap-1.5 whitespace-nowrap rounded border border-line-strong px-4 py-2.5 text-[13px] font-semibold text-ink-soft transition hover:bg-surface-2 hover:text-ink">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                    Review
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
                title="No completed courses yet"
                description="Complete a course to earn your certificate. Keep learning and you'll see your achievements here."
            >
                <div class="mt-5 flex flex-col justify-center gap-2 sm:flex-row">
                    <a href="{{ route('my-learning.in-progress') }}"
                       class="inline-flex items-center justify-center whitespace-nowrap rounded border border-line-strong px-5 py-2.5 text-[13.5px] font-semibold text-ink-soft transition hover:bg-surface-2 hover:text-ink">
                        View In Progress
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
