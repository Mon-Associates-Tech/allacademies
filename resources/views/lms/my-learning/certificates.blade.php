<x-layouts.app>
    <x-exam.page>

        <x-exam.crumbs :items="[
            ['label' => 'My Learning', 'href' => route('my-learning.index')],
            ['label' => 'Certificates'],
        ]" />

        <x-exam.header
            eyebrow="LEARNING RECORDS · EARNED CREDENTIALS"
            title="My Certificates"
            sub="Your earned certificates and achievements."
            :lines="['HONOR', 'ROLL']"
        />

        <x-exam.flash />

        {{-- Stats --}}
        <div class="mb-8 grid grid-cols-1 gap-4 sm:grid-cols-3">
            <x-exam.stat label="Total Certificates" :value="$stats['total']" tone="primary" />
            <x-exam.stat label="Valid" :value="$stats['valid']" tone="ok" />
            <x-exam.stat label="Expired" :value="$stats['expired']" tone="danger" />
        </div>

        <x-exam.learning-tabs :counts="['certificates' => $stats['total']]" />

        @if($certificates->count() > 0)
            <div class="grid grid-cols-1 gap-6 md:grid-cols-2 xl:grid-cols-3">
                @foreach($certificates as $certificate)
                    <article class="animate-rise overflow-hidden rounded-md border border-line bg-surface shadow-[0_1px_2px_rgba(20,24,34,0.04),0_18px_40px_-30px_rgba(20,24,34,0.25)] transition hover:-translate-y-0.5 hover:shadow-[0_10px_30px_-12px_rgba(20,24,34,0.30)] motion-reduce:transition-none {{ $certificate->isExpired() ? 'opacity-75' : '' }}"
                             style="animation-delay: {{ $loop->index * 40 }}ms">

                        {{-- Diploma head --}}
                        <div class="relative flex h-44 items-center justify-center border-b border-line bg-surface-2 px-4">
                            <div class="absolute inset-3 rounded-[3px] border border-line-strong"></div>
                            <div class="absolute inset-4 rounded-[2px] border border-line"></div>

                            <div class="relative text-center">
                                <div class="mx-auto mb-2.5 grid h-14 w-14 place-items-center rounded-full border-2 border-accent text-accent {{ $certificate->isExpired() ? 'opacity-50' : '' }}">
                                    <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/></svg>
                                </div>
                                <p class="font-mono text-[9px] tracking-[0.3em] text-muted">CERTIFICATE OF COMPLETION</p>
                            </div>

                            <div class="absolute right-4 top-4 rounded-full bg-surface/90 p-0.5 shadow-sm backdrop-blur-[2px]">
                                @if($certificate->isValid())
                                    <x-exam.badge tone="ok">Valid</x-exam.badge>
                                @else
                                    <x-exam.badge tone="danger">Expired</x-exam.badge>
                                @endif
                            </div>
                        </div>

                        <div class="p-5">
                            <h3 class="line-clamp-2 font-display text-base font-bold leading-snug text-ink">
                                {{ $certificate->course->title ?? 'Course Certificate' }}
                            </h3>

                            <div class="mt-3 space-y-1.5 font-mono text-[10.5px] tracking-wide text-muted">
                                <p><span class="text-ink-soft">CERT NO.:</span> {{ $certificate->certificate_number }}</p>
                                <p><span class="text-ink-soft">ISSUED:</span> {{ $certificate->issue_date->format('M d, Y') }}</p>
                                @if($certificate->expiry_date)
                                    <p class="{{ $certificate->isExpired() ? 'text-accent' : '' }}">
                                        <span class="text-ink-soft">EXPIRES:</span> {{ $certificate->expiry_date->format('M d, Y') }}
                                    </p>
                                @endif
                            </div>

                            @if($certificate->course)
                                <div class="mt-4 flex gap-2">
                                    <a href="{{ route('lms.courses.certificate', $certificate->course) }}"
                                       class="inline-flex flex-1 items-center justify-center gap-1.5 whitespace-nowrap rounded bg-primary px-4 py-2.5 text-[13px] font-semibold tracking-wide text-on-primary transition hover:-translate-y-px hover:bg-primary-hi motion-reduce:transition-none">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        View
                                    </a>
                                    <a href="{{ route('lms.courses.certificate.download', $certificate->course) }}"
                                       class="inline-flex items-center justify-center gap-1.5 whitespace-nowrap rounded border border-line-strong px-4 py-2.5 text-[13px] font-semibold text-ink-soft transition hover:bg-surface-2 hover:text-ink">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                        PDF
                                    </a>
                                </div>
                            @endif

                            <div class="mt-4 border-t border-dashed border-line pt-3">
                                <p class="font-mono text-[9.5px] tracking-[0.14em] text-muted">VERIFICATION</p>
                                <a href="{{ route('certificates.verify', $certificate->verification_code) }}" target="_blank"
                                   class="mt-1 block break-all font-mono text-[10.5px] text-primary transition hover:text-primary-hi">
                                    {{ route('certificates.verify', $certificate->verification_code) }}
                                </a>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>

            @if($certificates->hasPages())
                <div class="mt-8">{{ $certificates->links() }}</div>
            @endif
        @else
            <x-exam.empty
                title="No certificates yet"
                description="Complete a course to earn your first certificate. Your achievements will be displayed here."
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
