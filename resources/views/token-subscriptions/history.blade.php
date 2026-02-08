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
                                        Package
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Action
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Messengers
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Period
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Status
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Action
                                    </th>
                                </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach($subscriptionHistory as $subscription)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div
                                                class="text-sm font-medium text-gray-900 dark:text-white">{{ $subscription->package?->name }}</div>
                                            <div
                                                class="text-xs text-gray-500 dark:text-gray-400">{{ $subscription->package?->name === 'Premium' ? 'Advanced Messenger' : 'Basic Messenger' }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="inline-flex items-center px-2.5 py-1 text-xs font-medium rounded-full
                                                    {{ $subscription->action_type === 'trial' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : '' }}
                                                    {{ $subscription->action_type === 'upgrade' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200' : '' }}
                                                    {{ $subscription->action_type === 'downgrade' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200' : '' }}
                                                    {{ $subscription->action_type === 'purchase' ? 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200' : '' }}">
                                                    {{ ucfirst($subscription->action_type) }}
                                                </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            <div
                                                class="text-gray-900 dark:text-white font-medium">{{ number_format($subscription->tokens_used) }}</div>
                                            <div class="text-gray-500 dark:text-gray-400 text-xs">
                                                of {{ number_format($subscription->tokens_purchased) }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-400">
                                            <div>{{ $subscription->activated_at ? $subscription->activated_at->format('M d, Y') : 'N/A' }}</div>
                                            <div class="text-xs">
                                                to {{ $subscription->deactivated_at ? $subscription->deactivated_at->format('M d, Y') : 'N/A' }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="inline-flex items-center px-2.5 py-1 text-xs font-semibold rounded-full
                                                    {{ $subscription->status?->value === 'expired' ? 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200' : '' }}
                                                    {{ $subscription->status?->value === 'depleted' ? 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' : '' }}
                                                    {{ $subscription->status?->value === 'replaced' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200' : '' }}">
                                                    {{ ucfirst($subscription->status?->value ?? 'unknown') }}
                                                </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            {{-- todo fix missing required parameter error --}}
                                            {{-- <a href="{{ route('token-subscriptions.show', ['subscription' => $subscription]) }}"
                                               class="text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300 font-medium">
                                                View Details →
                                            </a> --}}
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>

                        {{-- Mobile Card View --}}
                        <div class="md:hidden space-y-4 p-4">
                            @foreach($subscriptionHistory as $subscription)
                                <div
                                    class="bg-gray-50 dark:bg-gray-700/50 rounded-xl p-4 border border-gray-200 dark:border-gray-600">
                                    {{-- Header with Package and Action --}}
                                    <div class="flex items-start justify-between mb-3">
                                        <div>
                                            <h4 class="font-semibold text-gray-900 dark:text-white">{{ $subscription->package?->name }}</h4>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ $subscription->package?->name === 'Premium' ? 'Advanced Messenger' : 'Basic Messenger' }}</p>
                                        </div>
                                        <span class="inline-flex items-center px-2 py-1 text-xs font-medium rounded-full
                                            {{ $subscription->action_type === 'trial' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : '' }}
                                            {{ $subscription->action_type === 'upgrade' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200' : '' }}
                                            {{ $subscription->action_type === 'downgrade' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200' : '' }}
                                            {{ $subscription->action_type === 'purchase' ? 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200' : '' }}">
                                            {{ ucfirst($subscription->action_type) }}
                                        </span>
                                    </div>

                                    {{-- Stats Grid --}}
                                    <div class="grid grid-cols-2 gap-3 mb-3">
                                        <div class="bg-white dark:bg-gray-800 rounded-lg p-3">
                                            <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Messengers</p>
                                            <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ number_format($subscription->tokens_used) }}</p>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                                of {{ number_format($subscription->tokens_purchased) }}</p>
                                        </div>
                                        <div class="bg-white dark:bg-gray-800 rounded-lg p-3">
                                            <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Period</p>
                                            <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ $subscription->activated_at ? $subscription->activated_at->format('M d') : 'N/A' }}</p>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                                to {{ $subscription->deactivated_at ? $subscription->deactivated_at->format('M d') : 'N/A' }}</p>
                                        </div>
                                    </div>

                                    {{-- Status and Action --}}
                                    <div class="flex items-center justify-between">
                                        <span class="inline-flex items-center px-2.5 py-1 text-xs font-semibold rounded-full
                                            {{ $subscription->status?->value === 'expired' ? 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200' : '' }}
                                            {{ $subscription->status?->value === 'depleted' ? 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' : '' }}
                                            {{ $subscription->status?->value === 'replaced' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200' : '' }}">
                                            {{ ucfirst($subscription->status?->value ?? 'unknown') }}
                                        </span>
                                        {{-- todo fix misissing required route parameters --}}
                                        {{-- <a href="{{ route('token-subscriptions.show', $subscription) }}"
                                           class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300 bg-blue-50 hover:bg-blue-100 dark:bg-blue-900/20 dark:hover:bg-blue-900/40 rounded-lg transition-colors">
                                            View
                                            <svg class="w-3 h-3 ml-1" fill="none" stroke="currentColor"
                                                 viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M9 5l7 7-7 7"/>
                                            </svg>
                                        </a> --}}
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
</x-layouts.app>
