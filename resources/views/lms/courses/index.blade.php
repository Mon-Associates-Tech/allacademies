<x-layouts.app>
    <x-exam.page>

        <x-exam.header
            eyebrow="COURSE CATALOG · EXPLORE & ENROLL"
            title="Explore Courses"
            sub="Discover courses to enhance your skills and knowledge."
            :lines="['OPEN', 'SHELF']"
        />

        <x-exam.flash />

        {{-- ============ Filters ============ --}}
        <x-exam.card class="mb-6" :padded="false">
            <x-slot:header>
                <p class="font-mono text-[10.5px] tracking-[0.14em] text-muted">CATALOG FILTERS · SEARCH THE REGISTRY</p>
            </x-slot:header>

            <form method="GET" action="{{ route('lms.courses.index') }}" class="space-y-5 p-5">
                {{-- Search --}}
                <div class="relative">
                    <svg class="pointer-events-none absolute left-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    <x-exam.input type="text" name="search" value="{{ request('search') }}"
                                  placeholder="Search for courses, topics, or instructors…" class="pl-11" />
                </div>

                <div class="flex flex-wrap items-center gap-x-6 gap-y-4">
                    {{-- Level pills --}}
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="mr-1 font-mono text-[10px] uppercase tracking-[0.14em] text-muted">Level</span>
                        @php
                            $difficulties = [
                                ['value' => '',             'label' => 'All',          'active' => 'bg-primary text-on-primary'],
                                ['value' => 'beginner',     'label' => 'Beginner',     'active' => 'bg-ok text-white'],
                                ['value' => 'intermediate', 'label' => 'Intermediate', 'active' => 'bg-warn text-white'],
                                ['value' => 'advanced',     'label' => 'Advanced',     'active' => 'bg-accent text-white'],
                            ];
                        @endphp
                        @foreach($difficulties as $diff)
                            @php
                            $isActive = $diff['value'] === '' ? !request('difficulty') : request('difficulty') === $diff['value']
                            @endphp
                            <a href="{{ route('lms.courses.index', array_merge(request()->except('difficulty'), ['difficulty' => $diff['value']])) }}"
                               class="rounded px-3 py-1.5 font-mono text-[11px] font-semibold uppercase tracking-[0.10em] transition motion-reduce:transition-none
                                      {{ $isActive ? $diff['active'] : 'bg-surface-2 text-muted hover:bg-line/60 hover:text-ink' }}">
                                {{ $diff['label'] }}
                            </a>
                        @endforeach
                    </div>

                    {{-- Price pills --}}
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="mr-1 font-mono text-[10px] uppercase tracking-[0.14em] text-muted">Price</span>
                        @php
                            $prices = [
                                ['value' => '',     'label' => 'All',     'active' => 'bg-primary text-on-primary'],
                                ['value' => 'free', 'label' => 'Free',    'active' => 'bg-ok text-white'],
                                ['value' => 'paid', 'label' => 'Premium', 'active' => 'bg-warn text-white'],
                            ];
                        @endphp
                        @foreach($prices as $price)
                            @php($isActive = $price['value'] === '' ? !request('price') : request('price') === $price['value'])
                            <a href="{{ route('lms.courses.index', array_merge(request()->except('price'), ['price' => $price['value']])) }}"
                               class="rounded px-3 py-1.5 font-mono text-[11px] font-semibold uppercase tracking-[0.10em] transition motion-reduce:transition-none
                                      {{ $isActive ? $price['active'] : 'bg-surface-2 text-muted hover:bg-line/60 hover:text-ink' }}">
                                {{ $price['label'] }}
                            </a>
                        @endforeach
                    </div>

                    <div class="ml-auto">
                        <x-exam.button type="submit">
                            <svg class="mr-1.5 h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            Search
                        </x-exam.button>
                    </div>
                </div>
            </form>
        </x-exam.card>

        {{-- ============ Results ============ --}}
        @if($courses->count() > 0)
            <p class="mb-5 font-mono text-[10.5px] tracking-[0.14em] text-muted">
                CATALOG · SHOWING <span class="font-semibold text-ink">{{ $courses->count() }}</span>
                OF <span class="font-semibold text-ink">{{ $courses->total() }}</span> COURSES
            </p>

            <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
                @foreach($courses as $course)


                    <a href="{{ route('lms.courses.show', $course->slug) }}"
                       class="group animate-rise overflow-hidden rounded-md border border-line bg-surface shadow-[0_1px_2px_rgba(20,24,34,0.04),0_18px_40px_-30px_rgba(20,24,34,0.25)] transition hover:-translate-y-0.5 hover:shadow-[0_10px_30px_-12px_rgba(20,24,34,0.30)] motion-reduce:transition-none"
                       style="animation-delay: {{ $loop->index * 40 }}ms">

                        {{-- Cover --}}
                        <div class="relative aspect-[16/10] bg-surface-2">
                            @if($course->thumbnail)
                                <img src="{{ asset('storage/' . $course->thumbnail) }}" alt="{{ $course->title }}" class="h-full w-full object-cover">
                            @else
                                <div class="grid h-full w-full place-items-center">
                                    <svg class="h-10 w-10 text-line-strong" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                                </div>
                            @endif

                            <div class="absolute left-3 top-3 rounded-full bg-surface/90 p-0.5 shadow-sm backdrop-blur-[2px]">
                                <x-exam.badge>{{ ucfirst($course->difficulty_level) }}</x-exam.badge>
                            </div>

                            <div class="absolute right-3 top-3 rounded-full bg-surface/90 p-0.5 shadow-sm backdrop-blur-[2px]">
                                @if($course->is_free)
                                    <x-exam.badge tone="ok">Free</x-exam.badge>
                                @else
                                    <x-exam.badge tone="bare">GHS{{ number_format($course->price) }}</x-exam.badge>
                                @endif
                            </div>
                        </div>

                        <div class="p-5">
                            <h3 class="line-clamp-2 font-display text-base font-bold leading-snug text-ink transition group-hover:text-primary">
                                {{ $course->title }}
                            </h3>

                            @if($course->description)
                                <p class="mt-2 line-clamp-2 text-sm leading-relaxed text-ink-soft">{{ $course->description }}</p>
                            @endif

                            {{-- Instructor --}}
                            <div class="mt-3 flex items-center gap-2">
                                @if($course->creator->avatar)
                                    <img class="h-6 w-6 rounded-full object-cover ring-1 ring-line" src="{{ asset('storage/' . $course->creator->avatar) }}" alt="{{ $course->creator->name }}">
                                @else
                                    <div class="grid h-6 w-6 place-items-center rounded-full bg-surface-2 font-mono text-[10px] font-bold text-muted ring-1 ring-line">
                                        {{ strtoupper(substr($course->creator->name, 0, 1)) }}
                                    </div>
                                @endif
                                <span class="text-xs text-ink-soft">{{ $course->creator->name }}</span>
                            </div>

                            {{-- School --}}
                            @if($course->school)
                                <div class="mt-2 flex items-center gap-1.5 font-mono text-[10.5px] tracking-wide text-muted">
                                    <svg class="h-3.5 w-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                    {{ $course->school->name }}
                                </div>
                            @endif

                            {{-- Stats --}}
                            <div class="mt-4 flex items-center justify-between border-t border-dashed border-line pt-3 font-mono text-[10.5px] tracking-wide text-muted">
                                <span class="flex items-center gap-1.5">
                                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
                                    {{ $course->chapters->count() }} {{ strtoupper(Str::plural('chapter', $course->chapters->count())) }}
                                </span>
                                <span class="flex items-center gap-1.5">
                                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                                    {{ $course->enrollments_count }} ENROLLED
                                </span>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>

            @if($courses->hasPages())
                <div class="mt-8">{{ $courses->links() }}</div>
            @endif

            {{-- ============ Empty state ============ --}}
        @else
            <x-exam.empty title="No courses found" description="Try adjusting your search or filter criteria.">
                <a href="{{ route('lms.courses.index') }}"
                   class="mt-5 inline-flex items-center gap-1.5 whitespace-nowrap rounded bg-primary px-5 py-2.5 text-[13.5px] font-semibold tracking-wide text-on-primary transition hover:-translate-y-px hover:bg-primary-hi motion-reduce:transition-none">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    Clear Filters
                </a>
            </x-exam.empty>
        @endif

    </x-exam.page>
</x-layouts.app>
