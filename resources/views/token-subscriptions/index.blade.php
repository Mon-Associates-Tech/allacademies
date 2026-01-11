<x-layouts.app>
    <div class="min-h-screen bg-gradient-to-br from-gray-50 to-blue-50 dark:from-gray-900 dark:to-blue-900/20">
        <div class="container mx-auto px-4 py-8">
            <div class="max-w-7xl mx-auto">
                {{-- Header --}}
                <div class="mb-8">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                        <div>
                            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Messenger Subscriptions</h1>
                            <p class="text-gray-600 dark:text-gray-400 mt-1">Manage your messenger packages and
                                usage</p>
                        </div>

                        @if($activeSubscription)
                            <div class="flex gap-3">
                                <a href="{{ route('token-subscriptions.topup', $activeSubscription->id) }}"
                                   class="inline-flex items-center px-5 py-2.5 bg-gradient-to-r from-green-600 to-emerald-600 hover:from-green-700 hover:to-emerald-700 text-white rounded-lg font-medium transition-all shadow-lg">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                    </svg>
                                    Top Up Tokens
                                </a>
                                <a href="{{ route('token-subscriptions.create') }}"
                                   class="inline-flex items-center px-5 py-2.5 {{ $activeSubscription->package && $activeSubscription->package->isFree() ? 'bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700' : 'bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700' }} text-white rounded-lg font-medium transition-all shadow-lg">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                                    </svg>
                                    {{ $activeSubscription->package && $activeSubscription->package->isFree() ? 'Upgrade Package' : 'Change Package' }}
                                </a>
                            </div>
                        @else
                            <a href="{{ route('token-subscriptions.create') }}"
                               class="inline-flex items-center px-5 py-2.5 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white rounded-lg font-medium transition-all shadow-lg">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M12 4v16m8-8H4"/>
                                </svg>
                                Get Started
                            </a>
                        @endif
                    </div>
                </div>

                {{-- Stats Grid --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
                    <div
                        class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 border border-gray-200 dark:border-gray-700">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-600 dark:text-gray-400 text-sm font-medium">Subscriptions</p>
                                <p class="text-2xl font-bold text-gray-900 dark:text-white mt-2">{{ $stats['total_subscriptions'] }}</p>
                            </div>
                            <div
                                class="flex items-center justify-center w-12 h-12 bg-blue-100 dark:bg-blue-900/30 rounded-lg">
                                <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor"
                                     viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <div
                        class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 border border-gray-200 dark:border-gray-700">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-600 dark:text-gray-400 text-sm font-medium">Total Spent</p>
                                <p class="text-2xl font-bold text-gray-900 dark:text-white mt-2">
                                    GH₵{{ number_format($stats['total_spent'], 2) }}</p>
                            </div>
                            <div
                                class="flex items-center justify-center w-12 h-12 bg-green-100 dark:bg-green-900/30 rounded-lg">
                                <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none"
                                     stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <div
                        class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 border border-gray-200 dark:border-gray-700">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-600 dark:text-gray-400 text-sm font-medium">Lifetime Messengers</p>
                                <p class="text-2xl font-bold text-gray-900 dark:text-white mt-2">{{ number_format($stats['total_tokens_purchased']) }}</p>
                            </div>
                            <div
                                class="flex items-center justify-center w-12 h-12 bg-purple-100 dark:bg-purple-900/30 rounded-lg">
                                <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none"
                                     stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <div
                        class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 border border-gray-200 dark:border-gray-700">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-600 dark:text-gray-400 text-sm font-medium">Messengers Used</p>
                                <p class="text-2xl font-bold text-gray-900 dark:text-white mt-2">{{ number_format($stats['total_tokens_used']) }}</p>
                            </div>
                            <div
                                class="flex items-center justify-center w-12 h-12 bg-orange-100 dark:bg-orange-900/30 rounded-lg">
                                <svg class="w-6 h-6 text-orange-600 dark:text-orange-400" fill="none"
                                     stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Current Anniversary Cycle Card --}}
                @if($currentCycle)
                    <div class="mb-8">
                        <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                            <svg class="w-6 h-6 mr-2 text-blue-600" fill="none" stroke="currentColor"
                                 viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Current Subscription Cycle
                        </h2>

                        <div
                            class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl overflow-hidden border border-gray-200 dark:border-gray-700">
                            {{-- Header Section --}}
                            <div class="relative overflow-hidden bg-gradient-to-r from-blue-500 to-cyan-600 p-6">
                                <div class="absolute inset-0 opacity-10">
                                    <div class="absolute top-0 left-0 w-64 h-64 bg-white rounded-full blur-3xl"></div>
                                    <div
                                        class="absolute bottom-0 right-0 w-96 h-96 bg-white rounded-full blur-3xl"></div>
                                </div>

                                <div class="relative flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3">
                                    <div>
                                        <div class="flex flex-col sm:flex-row sm:items-center gap-2 sm:gap-3 mb-2">
                                            <h3 class="text-lg sm:text-2xl font-bold text-white">{{ $currentCycle->pricingTier->name }}
                                                Plan</h3>
                                            <span
                                                class="px-3 py-1 bg-white/20 backdrop-blur-sm rounded-full text-xs sm:text-sm font-semibold text-white border border-white/30 w-fit">
                                                    Month {{ $currentCycle->cycle_number }}
                                                </span>
                                        </div>
                                        <p class="text-xs sm:text-sm text-white/90">
                                            GH₵ {{ number_format($currentCycle->current_price, 2) }} total (cumulative)
                                            • {{ number_format($currentCycle->pricingTier->monthly_token_limit) }}
                                            tokens/month</p>
                                    </div>
                                    <span
                                        class="inline-flex items-center px-3 sm:px-4 py-2 bg-white/20 backdrop-blur-sm rounded-full text-xs sm:text-sm font-semibold text-white border border-white/30 w-fit whitespace-nowrap">
                                            ✓ {{ ucfirst($currentCycle->status) }}
                                        </span>
                                </div>
                            </div> {{-- Progress Section --}}
                            <div class="p-4 sm:p-6">
                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 sm:gap-4 mb-6">
                                    <div
                                        class="text-center p-3 sm:p-4 bg-green-50 dark:bg-green-900/20 rounded-lg border border-green-200 dark:border-green-800">
                                        <p class="text-xs sm:text-sm text-gray-600 dark:text-gray-400 mb-1">
                                            Available</p>
                                        <p class="text-xl sm:text-2xl font-bold text-green-600 dark:text-green-400">{{ number_format($currentCycle->tokens_allocated - $currentCycle->tokens_used) }}</p>
                                    </div>
                                    <div
                                        class="text-center p-3 sm:p-4 bg-orange-50 dark:bg-orange-900/20 rounded-lg border border-orange-200 dark:border-orange-800">
                                        <p class="text-xs sm:text-sm text-gray-600 dark:text-gray-400 mb-1">Used</p>
                                        <p class="text-xl sm:text-2xl font-bold text-orange-600 dark:text-orange-400">{{ number_format($currentCycle->tokens_used) }}</p>
                                    </div>
                                    <div
                                        class="text-center p-3 sm:p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800">
                                        <p class="text-xs sm:text-sm text-gray-600 dark:text-gray-400 mb-1">Topup</p>
                                        <p class="text-xl sm:text-2xl font-bold text-blue-600 dark:text-blue-400">{{ number_format(max(0, $currentCycle->topup_tokens_allocated)) }}</p>
                                    </div>
                                </div>

                                {{-- Progress Bar --}}
                                <div class="mb-4">
                                    @php
                                        $usagePercent = ($currentCycle->tokens_used / $currentCycle->tokens_allocated) * 100;
                                    @endphp
                                    <div class="flex justify-between text-sm text-gray-600 dark:text-gray-400 mb-2">
                                        <span>Token Usage Progress</span>
                                        <span class="font-semibold">{{ round($usagePercent, 1) }}%</span>
                                    </div>
                                    <div
                                        class="relative w-full h-4 bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden">
                                        <div
                                            class="h-full rounded-full transition-all duration-500 {{ $usagePercent >= 90 ? 'bg-gradient-to-r from-red-500 to-pink-500' : ($usagePercent >= 70 ? 'bg-gradient-to-r from-orange-500 to-yellow-500' : 'bg-gradient-to-r from-blue-500 to-cyan-500') }}"
                                            style="width: {{ $usagePercent }}%">
                                        </div>
                                    </div>
                                </div>

                                <div
                                    class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                                    <div class="text-xs sm:text-sm text-gray-600 dark:text-gray-400">
                                        <span
                                            class="font-semibold">Anniversary Cycle:</span> {{ $currentCycle->cycle_start_date->format('M d') }}
                                        - {{ $currentCycle->cycle_end_date->format('M d, Y') }}
                                        <br>
                                        <span
                                            class="text-xs">{{ $currentCycle->getRemainingDays() }} days remaining</span>
                                    </div>
                                    <a href="{{ route('token-subscriptions.show', $currentCycle->id) }}"
                                       class="inline-flex items-center justify-center sm:justify-start px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors text-sm w-full sm:w-auto">
                                        View Details
                                        <svg class="w-4 h-4 ml-1.5" fill="none" stroke="currentColor"
                                             viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M9 5l7 7-7 7"/>
                                        </svg>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    {{-- No Active Subscription --}}
                    <div
                        class="mb-8 bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-6 sm:p-12 text-center border border-gray-200 dark:border-gray-700">
                        <div
                            class="inline-flex items-center justify-center w-16 sm:w-20 h-16 sm:h-20 bg-blue-100 dark:bg-blue-900/30 rounded-full mb-4 sm:mb-6">
                            <svg class="w-8 sm:w-10 h-8 sm:h-10 text-blue-600 dark:text-blue-400" fill="none"
                                 stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                            </svg>
                        </div>
                        <h3 class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-white mb-2">No Active
                            Subscription</h3>
                        <p class="text-sm sm:text-base text-gray-600 dark:text-gray-400 mb-4 sm:mb-6 max-w-md mx-auto">
                            Get started with AI-powered learning by choosing a token package that fits your needs.
                        </p>
                        <a href="{{ route('token-subscriptions.create') }}"
                           class="inline-flex items-center justify-center px-6 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white rounded-lg font-medium transition-all shadow-lg w-full sm:w-auto text-sm">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M12 4v16m8-8H4"/>
                            </svg>
                            Browse Packages
                        </a>
                    </div>
                @endif

                {{-- Subscription History --}}
                @if($subscriptionHistory->count() > 0)
                    <div
                        class="bg-white mt-4 dark:bg-gray-800 rounded-2xl shadow-xl overflow-hidden border border-gray-200 dark:border-gray-700">
                        <div
                            class="bg-gradient-to-r from-indigo-50 to-purple-50 dark:from-indigo-900/20 dark:to-purple-900/20 px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                                <svg class="w-5 h-5 mr-2 text-indigo-600" fill="none" stroke="currentColor"
                                     viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                Subscription History
                            </h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Your past subscriptions and
                                upgrades</p>
                        </div>

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
                                @foreach($subscriptionHistory as $sub)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div
                                                class="text-sm font-medium text-gray-900 dark:text-white">{{ $sub->package?->name }}</div>
                                            <div
                                                class="text-xs text-gray-500 dark:text-gray-400">{{ $sub->package?->name === 'Premium' ? 'Advanced Messenger' : 'Basic Messenger' }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="inline-flex items-center px-2.5 py-1 text-xs font-medium rounded-full
                                                    {{ $sub->action_type === 'trial' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : '' }}
                                                    {{ $sub->action_type === 'upgrade' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200' : '' }}
                                                    {{ $sub->action_type === 'downgrade' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200' : '' }}
                                                    {{ $sub->action_type === 'purchase' ? 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200' : '' }}">
                                                    {{ ucfirst($sub->action_type) }}
                                                </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            <div
                                                class="text-gray-900 dark:text-white font-medium">{{ number_format($sub->tokens_used) }}</div>
                                            <div class="text-gray-500 dark:text-gray-400 text-xs">
                                                of {{ number_format($sub->tokens_purchased) }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-400">
                                            <div>{{ $sub->activated_at ? $sub->activated_at->format('M d, Y') : 'N/A' }}</div>
                                            <div class="text-xs">
                                                to {{ $sub->deactivated_at ? $sub->deactivated_at->format('M d, Y') : 'N/A' }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="inline-flex items-center px-2.5 py-1 text-xs font-semibold rounded-full
                                                    {{ $sub->status->value === 'expired' ? 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200' : '' }}
                                                    {{ $sub->status->value === 'depleted' ? 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' : '' }}
                                                    {{ $sub->status->value === 'replaced' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200' : '' }}">
                                                    {{ ucfirst($sub->status->value) }}
                                                </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            <a href="{{ route('token-subscriptions.show', $sub->id) }}"
                                               class="text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300 font-medium">
                                                View Details →
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>

                        {{-- Mobile Card View --}}
                        <div class="md:hidden space-y-4 p-4">
                            @foreach($subscriptionHistory as $sub)
                                <div
                                    class="bg-gray-50 dark:bg-gray-700/50 rounded-xl p-4 border border-gray-200 dark:border-gray-600">
                                    {{-- Header with Package and Action --}}
                                    <div class="flex items-start justify-between mb-3">
                                        <div>
                                            <h4 class="font-semibold text-gray-900 dark:text-white">{{ $sub->package?->name }}</h4>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ $sub->package?->name === 'Premium' ? 'Advanced Messenger' : 'Basic Messenger' }}</p>
                                        </div>
                                        <span class="inline-flex items-center px-2 py-1 text-xs font-medium rounded-full
                                            {{ $sub->action_type === 'trial' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : '' }}
                                            {{ $sub->action_type === 'upgrade' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200' : '' }}
                                            {{ $sub->action_type === 'downgrade' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200' : '' }}
                                            {{ $sub->action_type === 'purchase' ? 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200' : '' }}">
                                            {{ ucfirst($sub->action_type) }}
                                        </span>
                                    </div>

                                    {{-- Stats Grid --}}
                                    <div class="grid grid-cols-2 gap-3 mb-3">
                                        <div class="bg-white dark:bg-gray-800 rounded-lg p-3">
                                            <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Messengers</p>
                                            <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ number_format($sub->tokens_used) }}</p>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                                of {{ number_format($sub->tokens_purchased) }}</p>
                                        </div>
                                        <div class="bg-white dark:bg-gray-800 rounded-lg p-3">
                                            <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Period</p>
                                            <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ $sub->activated_at ? $sub->activated_at->format('M d') : 'N/A' }}</p>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                                to {{ $sub->deactivated_at ? $sub->deactivated_at->format('M d') : 'N/A' }}</p>
                                        </div>
                                    </div>

                                    {{-- Status and Action --}}
                                    <div class="flex items-center justify-between">
                                        <span class="inline-flex items-center px-2.5 py-1 text-xs font-semibold rounded-full
                                            {{ $sub->status->value === 'expired' ? 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200' : '' }}
                                            {{ $sub->status->value === 'depleted' ? 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' : '' }}
                                            {{ $sub->status->value === 'replaced' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200' : '' }}">
                                            {{ ucfirst($sub->status->value) }}
                                        </span>
                                        <a href="{{ route('token-subscriptions.show', $sub->id) }}"
                                           class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300 bg-blue-50 hover:bg-blue-100 dark:bg-blue-900/20 dark:hover:bg-blue-900/40 rounded-lg transition-colors">
                                            View
                                            <svg class="w-3 h-3 ml-1" fill="none" stroke="currentColor"
                                                 viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M9 5l7 7-7 7"/>
                                            </svg>
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-layouts.app>
