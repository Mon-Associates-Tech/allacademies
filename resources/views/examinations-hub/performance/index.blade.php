<x-layouts.app>
    <x-examinations-hub.navigation active="performance" />
    
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Participant Performance</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-1">View examination performance and metrics for all participants</p>
        </div>

        <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
            <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                <form method="GET" class="flex gap-4">
                    <div class="flex-1">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Search</label>
                        <input 
                            type="text" 
                            name="search" 
                            value="{{ request('search') }}"
                            placeholder="Name or Email..."
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white"
                        >
                    </div>

                    <div class="flex items-end">
                        <button type="submit" class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                            Search
                        </button>
                    </div>
                </form>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Participant</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Email</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Type</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Submissions</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Avg Score</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($participants as $participant)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="h-10 w-10 flex-shrink-0">
                                            <div class="h-10 w-10 rounded-full bg-indigo-100 dark:bg-indigo-900 flex items-center justify-center">
                                                <span class="text-indigo-600 dark:text-indigo-300 font-medium text-sm">
                                                    {{ strtoupper(substr($participant->participant_name, 0, 2)) }}
                                                </span>
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900 dark:text-white">
                                                {{ $participant->participant_name }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                    {{ $participant->participant_email ?? 'N/A' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <span class="px-2 py-1 text-xs font-medium rounded-full
                                        @if($participant->participant_type === 'configured') bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200
                                        @else bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200
                                        @endif">
                                        {{ ucfirst($participant->participant_type) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-900 dark:text-white">
                                    {{ $participant->submission_count }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <span class="px-2 py-1 rounded-full text-xs font-medium
                                        @if($participant->avg_percentage >= 80) bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                        @elseif($participant->avg_percentage >= 60) bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200
                                        @elseif($participant->avg_percentage >= 50) bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200
                                        @else bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200
                                        @endif">
                                        {{ round($participant->avg_percentage, 1) }}%
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                                    <a href="{{ route('examinations-hub.performance.show', [$participant->participant_type, $participant->participant_id]) }}" 
                                       class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300 font-medium">
                                        View Performance
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                                    No participants found
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($participants->hasPages())
                <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                    {{ $participants->links() }}
                </div>
            @endif
        </div>
    </div>
</x-layouts.app>
