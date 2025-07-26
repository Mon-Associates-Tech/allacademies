<div class="overflow-x-auto" wire:poll.10s>
    <table class="min-w-full bg-white rounded-lg overflow-hidden">
        <thead class="bg-gray-100">
        <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Device</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">IP & Location
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Session Started
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Duration</th>
        </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
        @foreach($activities as $activity)
            <tr class="hover:bg-gray-50">
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center">
                        <div class="text-sm flex  font-medium text-gray-900">
                            <x-avatar text-size="text-xs" :name="$activity->user->name"
                                      avatar="{{ $activity->user->avatar }}"
                                      class="w-6 h-6 mr-2  rounded-full mx-auto"/>
                            <span class="my-auto"> {{ $activity->user->name }}</span>
                        </div>
                    </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-sans">
                    {{ $activity->browser }} on {{ $activity->platform }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $activity->ip_address }}
                    ({{ $activity->country }})
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"> {{ $activity->created_at->format('M d, Y h:i A') }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    <x-login-status-indicator
                        :isActive="is_null($activity->logout_at)"
                        :activity="$activity"
                    />
                    {{ $activity->duration }}
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
