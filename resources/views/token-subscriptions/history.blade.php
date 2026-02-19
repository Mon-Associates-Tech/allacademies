<x-layouts.app>
    <div class="min-h-screen bg-gradient-to-br from-gray-50 to-blue-50 dark:from-gray-900 dark:to-blue-900/20">
        <div class="container mx-auto px-4 py-8">
            <div class="max-w-7xl mx-auto">
                {{-- Header --}}
                <div class="mb-8">
                    <div class="flex items-center gap-4 mb-4">
                        <a href="{{ route('token-subscriptions.index') }}"
                           class="inline-flex items-center text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition-colors">
                            <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                            </svg>
                            Back to Subscriptions
                        </a>
                    </div>
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Subscription History</h1>
                        <p class="text-gray-600 dark:text-gray-400 mt-1">Your past subscriptions and upgrades</p>
                    </div>
                </div>

                {{-- Subscription History --}}
                @if($subscriptionHistory->count() > 0)
                    <div
                        class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl overflow-hidden border border-gray-200 dark:border-gray-700">
                        {{-- Desktop Table View --}}
                        <div class="hidden md:block overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Subscription
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Duration
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Total Cost
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Tokens Allocated
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Tokens Used
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Period
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Status
                                    </th>
                                </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach($subscriptionHistory as $subscription)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900 dark:text-white" title="{{ $subscription->pricing_tier?->description ?? 'Subscription Plan' }}">{{ $subscription->pricing_tier?->name ?? 'Unknown' }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $subscription->months_count }} {{ $subscription->months_count == 1 ? 'month' : 'months' }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900 dark:text-white">${{ number_format($subscription->total_cost, 2) }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            <div class="text-gray-900 dark:text-white font-medium">{{ number_format($subscription->total_tokens) }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            <div class="text-gray-900 dark:text-white font-medium">{{ number_format($subscription->tokens_used) }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-400">
                                            <div>{{ \Carbon\Carbon::parse($subscription->group_start_date)->format('M d, Y') }}</div>
                                            <div class="text-xs">to {{ \Carbon\Carbon::parse($subscription->group_end_date)->format('M d, Y') }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="inline-flex items-center px-2.5 py-1 text-xs font-semibold rounded-full {{ $subscription->is_active ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200' }}">
                                                {{ $subscription->is_active ? 'Active' : 'Expired' }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>

                        {{-- Mobile Card View --}}
                        <div class="md:hidden space-y-4 p-4">
                            @foreach($subscriptionHistory as $index => $subscription)
                                <div class="bg-gray-50 dark:bg-gray-700/50 rounded-xl border border-gray-200 dark:border-gray-600">
                                    {{-- Collapsible Header --}}
                                    <button onclick="toggleCard({{ $index }})" class="w-full p-4 text-left focus:outline-none">
                                        <div class="flex items-center justify-between">
                                            <div>
                                                <h4 class="font-semibold text-gray-900 dark:text-white">{{ $subscription->pricing_tier?->name ?? 'Unknown' }}</h4>
                                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $subscription->months_count }} {{ $subscription->months_count == 1 ? 'month' : 'months' }} • ${{ number_format($subscription->total_cost, 2) }}</p>
                                            </div>
                                            <div class="flex items-center gap-2">
                                                <span class="inline-flex items-center px-2 py-1 text-xs font-medium rounded-full {{ $subscription->is_active ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200' }}">
                                                    {{ $subscription->is_active ? 'Active' : 'Expired' }}
                                                </span>
                                                <svg id="chevron-{{ $index }}" class="w-5 h-5 text-gray-400 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                                </svg>
                                            </div>
                                        </div>
                                    </button>

                                    {{-- Collapsible Content --}}
                                    <div id="card-content-{{ $index }}" class="hidden px-4 pb-4">
                                        @if($subscription->pricing_tier?->description)
                                            <div class="mb-3 p-2 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                                                <p class="text-xs text-blue-700 dark:text-blue-300">{{ $subscription->pricing_tier->description }}</p>
                                            </div>
                                        @endif
                                        
                                        <div class="grid grid-cols-2 gap-3 mb-3">
                                            <div class="bg-white dark:bg-gray-800 rounded-lg p-3">
                                                <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Tokens Allocated</p>
                                                <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ number_format($subscription->total_tokens) }}</p>
                                            </div>
                                            <div class="bg-white dark:bg-gray-800 rounded-lg p-3">
                                                <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Tokens Used</p>
                                                <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ number_format($subscription->tokens_used) }}</p>
                                            </div>
                                        </div>

                                        <div class="bg-white dark:bg-gray-800 rounded-lg p-3">
                                            <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Period</p>
                                            <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ \Carbon\Carbon::parse($subscription->group_start_date)->format('M d, Y') }}</p>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">to {{ \Carbon\Carbon::parse($subscription->group_end_date)->format('M d, Y') }}</p>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @else
                    {{-- No History --}}
                    <div
                        class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-12 text-center border border-gray-200 dark:border-gray-700">
                        <div
                            class="inline-flex items-center justify-center w-20 h-20 bg-gray-100 dark:bg-gray-700 rounded-full mb-6">
                            <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">No Subscription History</h3>
                        <p class="text-gray-600 dark:text-gray-400 mb-6 max-w-md mx-auto">
                            You don't have any past subscriptions yet. Once you've had active subscriptions, they will appear here.
                        </p>
                        <a href="{{ route('token-subscriptions.index') }}"
                           class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white rounded-lg font-medium transition-all shadow-lg">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                            </svg>
                            Back to Subscriptions
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
    
    <script>
        function toggleCard(index) {
            const content = document.getElementById(`card-content-${index}`);
            const chevron = document.getElementById(`chevron-${index}`);
            
            if (content.classList.contains('hidden')) {
                content.classList.remove('hidden');
                chevron.style.transform = 'rotate(180deg)';
            } else {
                content.classList.add('hidden');
                chevron.style.transform = 'rotate(0deg)';
            }
        }
    </script>
</x-layouts.app>
