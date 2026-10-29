<x-layouts.app>
    <x-exam.page>
        <x-exam.header
            eyebrow="TERMINAL EXAM · BUILD FROM SCRATCH"
            title="Create Terminal Exam"
            color="ok"
            sub="Build your own exam from scratch — pick a subject, configure sections, select topics, and generate a printable PDF."
        />

        @if ($subscriptions->isEmpty())
            <x-exam.callout tone="warn">
                <div class="flex items-start gap-3">
                    <svg class="mt-0.5 h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
                    </svg>
                    <div>
                        <p class="font-semibold">No active subscription yet</p>
                        <p class="mt-1 text-sm text-ink-soft">You'll need an active mock exam subscription for a subject before you can create a custom exam.</p>
                        <a href="{{ route('user.mock-exams.subscription.dashboard') }}"
                           class="mt-3 inline-flex items-center gap-1 font-mono text-[11px] font-semibold uppercase tracking-wider text-primary hover:text-primary-hi">
                            View subscription plans
                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                            </svg>
                        </a>
                    </div>
                </div>
            </x-exam.callout>
        @else
            <div class="animate-rise">
                <div class="mb-5 flex items-center justify-between gap-4">
                    <p class="font-mono text-[10.5px] tracking-[0.14em] text-muted">
                        AVAILABLE SUBJECTS · {{ $subjects->count() }}
                    </p>
                </div>

                <div class="grid grid-cols-1 gap-4">
                    @foreach ($subjects as $subject)
                        <x-exam.card :padded="false">
                            <div class="flex items-center justify-between gap-4 p-5">
                                <div class="min-w-0 flex-1">
                                    <h3 class="font-display text-base font-bold text-ink">
                                        {{ $subject->name }}
                                    </h3>
                                    <div class="mt-2 flex flex-wrap items-center gap-2">
                                        <x-exam.badge>{{ $subject->code }}</x-exam.badge>
                                        <x-exam.badge>{{ $subject->academicLevel->name }}</x-exam.badge>
                                        <x-exam.badge>{{ $subject->academicLevel->academicGroup->name }}</x-exam.badge>
                                    </div>
                                </div>
                                <a href="{{ route('mock-exams.generate.custom.configure', ['academicSubject' => $subject]) }}">
                                    <x-exam.button>
                                        <svg class="mr-1.5 h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                                        </svg>
                                        Configure Exam
                                    </x-exam.button>
                                </a>
                            </div>
                        </x-exam.card>
                    @endforeach
                </div>
            </div>
        @endif
    </x-exam.page>
</x-layouts.app>
