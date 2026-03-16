<x-layouts.app>
    <div class="max-w-3xl mx-auto py-10">
        <div class="bg-white dark:bg-gray-900 shadow rounded-lg p-6">
            <h1 class="text-2xl font-semibold text-gray-900 dark:text-white mb-4">Location Debug</h1>
            <p class="text-sm text-gray-600 dark:text-gray-300 mb-6">Countries fetched from LocationService::getCountries()</p>
            <div class="overflow-hidden border border-gray-200 dark:border-gray-700 rounded-lg">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-800">
                    <tr>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Code</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Name</th>
                    </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($countries as $code => $name)
                        <tr>
                            <td class="px-4 py-2 font-mono text-sm text-gray-700 dark:text-gray-200">{{ $code }}</td>
                            <td class="px-4 py-2 text-sm text-gray-900 dark:text-white">{{ $name }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="2" class="px-4 py-4 text-center text-sm text-red-600 dark:text-red-400">No countries returned</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-layouts.app>
