<div class="overflow-x-auto bg-white p-6 rounded-lg shadow-md">
    <h3 class="text-xl font-semibold mb-4">Recent Session Activity</h3>
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
        <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Device</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">IP & Location</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Login Time</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Duration ⏱️</th>
        </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
        @forelse ($activities as $activity)
            <tr>
                <td class="px-6 py-4 whitespace-nowrap text-sm">
                    @if (is_null($activity->logout_at))
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                🟢 Online
                            </span>
                    @else
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                🔴 Offline
                            </span>
                    @endif
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                    {{ $activity->browser }} on {{ $activity->platform }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    {{ $activity->ip_address }} ({{ $activity->country }})
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    {{ $activity->created_at->format('M d, Y h:i A') }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    {{ $activity->duration }}
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">
                    No session activity found.
                </td>
            </tr>
        @endforelse
        </tbody>
    </table>
</div>
