<x-layouts.app>
    <x-examinations-hub.navigation active="dashboard" />
    
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">Examinations Hub</h1>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-6">
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-4"><div class="text-2xl font-bold">{{ $summary['total_exams'] }}</div><div class="text-sm text-gray-500">Exams</div></div>
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-4"><div class="text-2xl font-bold">{{ $summary['total_submissions'] }}</div><div class="text-sm text-gray-500">Submissions</div></div>
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-4"><div class="text-2xl font-bold">{{ $summary['avg_score'] }}%</div><div class="text-sm text-gray-500">Average Score</div></div>
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-4"><div class="text-2xl font-bold">{{ $summary['auto_gradable'] }}</div><div class="text-sm text-gray-500">Auto-Gradable</div></div>
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-4"><div class="text-2xl font-bold">{{ $summary['manual_review'] }}</div><div class="text-sm text-gray-500">Manual Review</div></div>
        </div>

        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden">
            <table class="w-full">
                <thead class="bg-gray-50 dark:bg-gray-700/50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs uppercase tracking-wider text-gray-500">Title</th>
                        <th class="px-4 py-3 text-left text-xs uppercase tracking-wider text-gray-500">Code</th>
                        <th class="px-4 py-3 text-left text-xs uppercase tracking-wider text-gray-500">Sections</th>
                        <th class="px-4 py-3 text-left text-xs uppercase tracking-wider text-gray-500">Questions</th>
                        <th class="px-4 py-3 text-left text-xs uppercase tracking-wider text-gray-500">Submissions</th>
                        <th class="px-4 py-3 text-right text-xs uppercase tracking-wider text-gray-500">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($exams as $exam)
                        <tr>
                            <td class="px-4 py-3 text-gray-900 dark:text-gray-100">{{ $exam->title }}</td>
                            <td class="px-4 py-3">{{ $exam->access_code }}</td>
                            <td class="px-4 py-3">{{ $exam->sections_count }}</td>
                            <td class="px-4 py-3">{{ $exam->questions_count }}</td>
                            <td class="px-4 py-3">{{ $exam->submissions_count }}</td>
                            <td class="px-4 py-3 text-right">
                                <a class="text-indigo-600 hover:underline" href="{{ route('examinations-hub.exams.show', $exam) }}">Open</a>
                                @if(!$exam->starts_at || now()->lt($exam->starts_at))
                                    <span class="text-gray-300 px-1">|</span>
                                    <a class="text-emerald-600 hover:underline" href="{{ route('examinations-hub.exams.edit', $exam) }}">Edit</a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-4 py-8 text-center text-gray-500">No examinations yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
            <div class="p-4">{{ $exams->links() }}</div>
        </div>
    </div>
</x-layouts.app>
