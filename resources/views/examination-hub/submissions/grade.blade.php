<x-layouts.app>
    <div class="container mx-auto px-4 py-8">
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Grade Submission</h1>
            <p class="text-slate-600 dark:text-slate-400 mt-1">
                Exam: <span class="font-semibold">{{ $exam->title }}</span> |
                Participant: <span class="font-semibold">{{ $submission->getParticipantName() }}</span>
            </p>
        </div>

        <div class="bg-white dark:bg-slate-800 rounded-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
            <div class="p-6">
                <livewire:teachers.grade-general-exam-submission :submission="$submission" />
            </div>
        </div>
    </div>
</x-layouts.app>