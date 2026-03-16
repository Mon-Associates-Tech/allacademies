<x-layouts.app page-name="Pricing Audit Log" :has-action="false">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Pricing Audit Log</h1>
                <p class="text-sm text-gray-600 mt-1">Track pricing changes made by administrators.</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.pricing-settings.edit') }}" class="text-sm font-semibold text-blue-600 hover:text-blue-700">Back to Pricing Settings</a>
                <a href="{{ route('dashboard') }}" class="text-sm font-semibold text-gray-600 hover:text-gray-800">Dashboard</a>
            </div>
        </div>

        <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider px-6 py-3">Date</th>
                        <th class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider px-6 py-3">Key</th>
                        <th class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider px-6 py-3">Old</th>
                        <th class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider px-6 py-3">New</th>
                        <th class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider px-6 py-3">Changed By</th>
                        <th class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider px-6 py-3">IP</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($audits as $audit)
                        <tr>
                            <td class="px-6 py-4 text-sm text-gray-700">{{ $audit->created_at?->format('Y-m-d H:i') }}</td>
                            <td class="px-6 py-4 text-sm text-gray-700">{{ $audit->key }}</td>
                            <td class="px-6 py-4 text-sm text-gray-700">{{ $audit->old_value !== null ? number_format((float) $audit->old_value, 2) : '—' }}</td>
                            <td class="px-6 py-4 text-sm text-gray-900 font-semibold">{{ number_format((float) $audit->new_value, 2) }}</td>
                            <td class="px-6 py-4 text-sm text-gray-700">
                                {{ $audit->user?->name ?? 'System' }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-700">{{ $audit->ip_address ?? '—' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-sm text-gray-500">No audit records yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($audits->hasPages())
            <div class="mt-6">
                {{ $audits->links() }}
            </div>
        @endif
    </div>
</x-layouts.app>
