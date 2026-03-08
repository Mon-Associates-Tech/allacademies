<div class="overflow-x-auto">
    <table class="min-w-full bg-white dark:bg-gray-800 rounded-lg overflow-hidden">
        <thead class="bg-gray-100 dark:bg-gray-700">
        <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">User</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Device</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">IP & Location</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Session Started</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Duration</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Actions</th>
        </tr>
        </thead>
        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
        @foreach($activities as $activity)
            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center">
                        <div class="text-sm flex font-medium text-gray-900 dark:text-white">
                            <x-avatar text-size="text-xs" :name="$activity->user->name"
                                      avatar="{{ $activity->user->avatar }}"
                                      class="w-6 h-6 mr-2 rounded-full mx-auto"/>
                            <span class="my-auto">{{ $activity->user->name }}</span>
                        </div>
                    </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-sans text-gray-900 dark:text-gray-300">
                    {{ $activity->browser }} on {{ $activity->platform }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                    {{ $activity->ip_address }} ({{ $activity->country }})
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                    {{ $activity->login_at?->format('M d, Y h:i A') }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                    <x-login-status-indicator
                        :isActive="is_null($activity->logout_at)"
                        :activity="$activity"
                    />
                    {{ $activity->duration }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                    <a href="{{ route('admin.login-activities.show', $activity->user) }}"
                       class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300">
                        View Details
                    </a>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
