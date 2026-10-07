<x-layouts.app>
    <x-exam.page>

        <x-exam.header
            eyebrow="LEARNING RECORDS · COURSE ENROLLMENTS"
            title="My Learning"
            sub="Track your progress and continue your learning journey."
            :lines="['STUDY', 'SHELF']"
        />

        <x-exam.flash />

        {{-- Toolbar --}}
        <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
            <p class="font-mono text-[10.5px] tracking-[0.14em] text-muted">
                COURSE REGISTRY · {{ $enrollments->total() }} ENROLLMENT{{ $enrollments->total() === 1 ? '' : 'S' }}
            </p>
            <a href="{{ route('lms.courses.index') }}"
               class="inline-flex items-center gap-1.5 whitespace-nowrap rounded bg-primary px-5 py-2.5 text-[13.5px] font-semibold tracking-wide text-on-primary shadow-[0_1px_0_rgba(20,24,34,0.2),0_8px_18px_-10px_rgba(20,24,34,0.5)] transition hover:-translate-y-px hover:bg-primary-hi motion-reduce:transition-none">
                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M12 4v16m8-8H4"/></svg>
                Browse Courses
            </a>
        </div>

        {{-- Stats --}}
        <div class="mb-8 grid grid-cols-1 gap-4 sm:grid-cols-3">
            <x-exam.stat label="Enrolled Courses" :value="$stats['total_enrolled']" tone="primary" />
            <x-exam.stat label="In Progress" :value="$stats['in_progress']" tone="warn" />
            <x-exam.stat label="Completed" :value="$stats['completed']" tone="ok" />
        </div>

        {{-- Route tabs --}}
        @php
            $current = Route::currentRouteName();
            $tabDefs = [
                ['route' => 'my-learning.index',       'label' => 'All Courses',  'count' => null],
                ['route' => 'my-learning.in-progress', 'label' => 'In Progress',  'count' => $stats['in_progress']],
                ['route' => 'my-learning.completed',   'label' => 'Completed',    'count' => $stats['completed']],
                ['route' => 'my-learning.certificates','label' => 'Certificates', 'count' => null],
            ];
        @endphp

        <nav class="mb-8 flex flex-wrap gap-1 border-b border-line" aria-label="Learning sections">
            @foreach($tabDefs as $tab)
                @php($active = $current === $tab['route'])
                <a href="{{ route($tab['route']) }}"
                   class="relative -mb-px inline-flex items-center gap-1.5 rounded-t border px-4 py-2.5 font-mono text-[11px] font-semibold uppercase tracking-[0.12em] transition
                          {{ $active ? 'border-line border-b-transparent bg-paper text-ink' : 'border-transparent text-muted hover:text-ink' }}">
                    @if($active)
                        <span class="absolute inset-x-0 top-0 h-0.5 bg-accent"></span>
                    @endif
                    {{ $tab['label'] }}
                    @if(($tab['count'] ?? 0) > 0)
                        <span class="grid h-4 min-w-[16px] place-items-center rounded-full bg-accent px-1 font-mono text-[9px] font-bold text-white">{{ $tab['count'] }}</span>
                    @endif
                </a>
            @endforeach
        </nav>

        {{-- ============ Courses grid ============ --}}
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
                                @if($enrollment->isCompleted())
                                    <x-exam.badge tone="ok">Completed</x-exam.badge>
                                @elseif($enrollment->isInProgress())
                                    <x-exam.badge tone="warn">In Progress</x-exam.badge>
                                @else
                                    <x-exam.badge tone="primary">Enrolled</x-exam.badge>
                                @endif
                            </div>
                        </div>

                        <div class="p-5">
                            {{-- Title --}}
                            <h3 class="line-clamp-2 font-display text-base font-bold leading-snug text-ink">
                                {{ $enrollment->course->title }}
                            </h3>

                            {{-- Instructor --}}
                            @if($enrollment->course->creator)
                                <div class="mt-2 flex items-center gap-2">
                                    @if($enrollment->course->creator->avatar)
                                        <img class="h-6 w-6 rounded-full object-cover ring-1 ring-line"
                                             src="{{ asset('storage/' . $enrollment->course->creator->avatar) }}"
                                             alt="{{ $enrollment->course->creator->name }}">
                                    @else
                                        <div class="grid h-6 w-6 place-items-center rounded-full bg-surface-2 font-mono text-[10px] font-bold text-muted ring-1 ring-line">
                                            {{ strtoupper(substr($enrollment->course->creator->name, 0, 1)) }}
                                        </div>
                                    @endif
                                    <span class="text-xs text-ink-soft">{{ $enrollment->course->creator->name }}</span>
                                </div>
                            @endif

                            {{-- Progress --}}
                            <div class="mt-4">
                                <div class="mb-1.5 flex items-center justify-between font-mono text-[10px] tracking-wider text-muted">
                                    <span>PROGRESS</span>
                                    <span class="font-semibold text-ink">{{ number_format($enrollment->progress_percentage, 0) }}%</span>
                                </div>
                                <x-exam.progress :value="$enrollment->progress_percentage" :tone="$enrollment->isCompleted() ? 'ok' : 'primary'" />
                            </div>

                            {{-- Meta --}}
                            <div class="mt-4 flex items-center justify-between border-t border-dashed border-line pt-3 font-mono text-[10.5px] tracking-wide text-muted">
                                <span class="flex items-center gap-1.5">
                                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
                                    {{ $enrollment->course->chapters->count() }} {{ strtoupper(Str::plural('chapter', $enrollment->course->chapters->count())) }}
                                </span>
                                <span class="flex items-center gap-1.5">
                                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    {{ $enrollment->enrolled_at->format('M d, Y') }}
                                </span>
                            </div>

                            {{-- Actions --}}
                            <div class="mt-4">
                                @if($enrollment->isCompleted())
                                    <div class="flex gap-2">
                                        <a href="{{ route('lms.courses.certificate', $enrollment->course) }}"
                                           class="inline-flex flex-1 items-center justify-center gap-1.5 whitespace-nowrap rounded bg-ok px-4 py-2.5 text-[13px] font-semibold tracking-wide text-white transition hover:-translate-y-px hover:opacity-90 motion-reduce:transition-none">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/></svg>
                                            Certificate
                                        </a>
                                        <a href="{{ route('lms.courses.learn', $enrollment->course->slug) }}"
                                           class="inline-flex items-center justify-center whitespace-nowrap rounded border border-line-strong px-4 py-2.5 text-[13px] font-semibold text-ink-soft transition hover:bg-surface-2 hover:text-ink">
                                            Review
                                        </a>
                                    </div>
                                @else
                                    <a href="{{ route('lms.courses.learn', $enrollment->course->slug) }}"
                                       class="inline-flex w-full items-center justify-center gap-1.5 whitespace-nowrap rounded bg-primary px-4 py-2.5 text-[13px] font-semibold tracking-wide text-on-primary transition hover:-translate-y-px hover:bg-primary-hi motion-reduce:transition-none">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        {{ $enrollment->isInProgress() ? 'Continue Learning' : 'Start Course' }}
                                    </a>
                                @endif
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>

            @if($enrollments->hasPages())
                <div class="mt-8">{{ $enrollments->links() }}</div>
            @endif

            {{-- ============ Empty state ============ --}}
        @else
            <x-exam.empty
                title="No courses yet"
                description="Start your learning journey by enrolling in a course. Browse our catalog to find courses that interest you."
            >
                <a href="{{ route('lms.courses.index') }}"
                   class="mt-5 inline-flex items-center gap-1.5 whitespace-nowrap rounded bg-primary px-5 py-2.5 text-[13.5px] font-semibold tracking-wide text-on-primary transition hover:-translate-y-px hover:bg-primary-hi motion-reduce:transition-none">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    Browse Courses
                </a>
            </x-exam.empty>
        @endif

    </x-exam.page>
</x-layouts.app>
