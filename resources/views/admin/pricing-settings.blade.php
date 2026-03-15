<x-layouts.app page-name="Pricing Settings" :has-action="false">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Pricing Settings</h1>
                <p class="text-sm text-gray-600 mt-1">Update subscription pricing values used across the platform.</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.pricing-settings.audits') }}" class="text-sm font-semibold text-gray-700 hover:text-gray-900">View Audit Log</a>
                <a href="{{ route('dashboard') }}" class="text-sm font-semibold text-blue-600 hover:text-blue-700">Back to Dashboard</a>
            </div>
        </div>

        @if(session('success'))
            <div class="mb-6 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-green-800">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="mb-6 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-red-800">
                <div class="font-semibold">Please fix the highlighted fields.</div>
            </div>
        @endif

        @php($oldPrices = old('prices', []))
        <form method="POST" action="{{ route('admin.pricing-settings.update') }}" class="space-y-8">
            @csrf
            @method('PUT')

            @foreach($groups as $groupLabel => $rows)
                <div class="bg-white border border-gray-200 rounded-xl shadow-sm">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-800">{{ $groupLabel }}</h2>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider px-6 py-3">Item</th>
                                    <th class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider px-6 py-3">Amount (GHS)</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @foreach($rows as $row)
                                    <tr>
                                        <td class="px-6 py-4 text-sm text-gray-700">{{ $row['label'] }}</td>
                                        <td class="px-6 py-4">
                                            <input
                                                type="number"
                                                step="0.01"
                                                min="0"
                                                name="prices[{{ $row['key'] }}]"
                                                value="{{ $oldPrices[$row['key']] ?? $row['value'] }}"
                                                class="w-full max-w-xs rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 text-sm"
                                            />
                                            @error('prices.'.$row['key'])
                                                <div class="text-xs text-red-600 mt-1">{{ $message }}</div>
                                            @enderror
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endforeach

            <div class="flex justify-end">
                <button type="submit" class="inline-flex items-center px-6 py-3 rounded-lg bg-blue-600 text-white font-semibold hover:bg-blue-700">
                    Save Changes
                </button>
            </div>
        </form>
    </div>
</x-layouts.app>
