<x-layouts.app>
    <div class="relative overflow-hidden" style="border-radius: 2px;">
        <div class="absolute inset-x-0 top-0 h-1 bg-purple-500"></div>
        <div class="bg-gradient-to-r from-[#0c1f3f] via-[#132a52] to-[#0c1f3f] px-6 py-10 sm:px-10">
            <h1 class="text-2xl sm:text-3xl font-semibold text-white">Create a Custom Exam</h1>
            <p class="mt-2 max-w-2xl text-sm text-slate-300">
                Build your own exam from scratch — pick a subject, configure sections, select topics, and generate a printable PDF.
            </p>
        </div>
    </div>

    @if ($subscriptions->isEmpty())
        <div class="mt-6 flex items-start gap-3 border border-amber-300 bg-amber-50 px-4 py-3 dark:border-amber-700 dark:bg-amber-900/20" style="border-radius: 2px;">
            <svg class="mt-0.5 h-5 w-5 shrink-0 text-amber-600 dark:text-amber-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
            </svg>
            <div class="text-sm text-amber-800 dark:text-amber-200">
                <p class="font-medium">No active subscription yet</p>
                <p class="mt-1">You'll need an active mock exam subscription for a subject before you can create a custom exam.</p>
                <a href="{{ route('mock-exams.subscription.dashboard') }}" class="mt-2 inline-block font-medium text-amber-900 underline dark:text-amber-100">
                    View subscription plans →
                </a>
            </div>
        </div>
    @else
        <div class="mt-8">
            <div class="max-w-2xl">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Select a Subject</h2>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                    Choose a subject from your active subscriptions to start building your custom exam.
                </p>

                <div class="mt-6 grid grid-cols-1 gap-4">
                    @foreach ($subjects as $subject)
                        <div class="flex items-center justify-between border border-slate-200 bg-white p-5 dark:border-slate-700 dark:bg-slate-900" style="border-radius: 2px;">
                            <div>
                                <h3 class="text-base font-semibold text-slate-900 dark:text-white">
                                    {{ $subject->name }}
                                </h3>
                                <div class="mt-1 flex flex-wrap gap-2 text-xs text-slate-500 dark:text-slate-400">
                                    <span>{{ $subject->code }}</span>
                                    <span>·</span>
                                    <span>{{ $subject->academicLevel->name }}</span>
                                    <span>·</span>
                                    <span>{{ $subject->academicLevel->academicGroup->name }}</span>
                                </div>
                            </div>
                            <a href="{{ route('mock-exams.generate.custom.configure', ['academicSubject' => $subject]) }}"
                               class="inline-flex items-center gap-1 bg-[#0c1f3f] px-4 py-2 text-sm font-medium text-white transition hover:bg-purple-700 dark:bg-purple-600 dark:hover:bg-purple-500"
                               style="border-radius: 2px;">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                                </svg>
                                Configure Exam
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif
</x-layouts.app>
