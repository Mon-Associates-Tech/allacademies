<x-layouts.app>
    <section class="min-h-screen bg-gradient-to-br from-gray-50 to-blue-50 dark:from-gray-900 dark:to-blue-900/20">
        <div class="container mx-auto px-4 py-8">
            <div class="max-w-7xl mx-auto">
                {{-- Header Navigation --}}
                <div class="flex items-center gap-4 mb-6">
                    <a href="{{ route('token-subscriptions.index') }}"
                       class="flex items-center justify-center w-10 h-10 rounded-lg bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors shadow-sm">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                    </a>
                    <div class="flex-1">
                        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Subscription Cycle Details</h1>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-0.5">{{ $subscriptionCycle->pricingTier->name }} Plan • Month {{ $subscriptionCycle->cycle_number }}</p>
                    </div>

                    {{-- Quick Action Button --}}
                    @if($subscriptionCycle->status === 'active')
                        <a href="{{ route('token-subscriptions.create') }}"
                           class="inline-flex items-center px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors shadow-lg">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            Upgrade Tier
                        </a>
                    @endif
                </div>

                {{-- Main Content Card --}}
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl overflow-hidden border border-gray-200 dark:border-gray-700">

                    {{-- Status Banner Section --}}
                    <div class="relative overflow-hidden {{ $subscriptionCycle->status === 'active' ? 'bg-gradient-to-r from-blue-500 to-cyan-600' : 'bg-gradient-to-r from-gray-500 to-gray-600' }} p-8">
                        {{-- Background Pattern --}}
                        <div class="absolute inset-0 opacity-10">
                            <div class="absolute top-0 left-0 w-64 h-64 bg-white rounded-full blur-3xl"></div>
                            <div class="absolute bottom-0 right-0 w-96 h-96 bg-white rounded-full blur-3xl"></div>
                        </div>

                        <div class="relative">
                            <div class="flex items-start justify-between mb-6">
                                <div>
                                    <div class="inline-flex items-center px-3 py-1 bg-white/20 backdrop-blur-sm rounded-full text-sm font-semibold text-white mb-3">
                                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        {{ $subscriptionCycle->pricingTier->name }}
                                    </div>
                                    <h2 class="text-3xl font-bold text-white mb-1">{{ $subscriptionCycle->pricingTier->name }} Plan</h2>
                                    <p class="text-white/80">
                                        @if($subscriptionCycle->status === 'active')
                                            Active monthly cycle
                                        @elseif($subscriptionCycle->status === 'expired')
                                            Cycle has ended
                                        @else
                                            {{ ucfirst($subscriptionCycle->status) }} cycle
                                        @endif
                                    </p>
                                </div>

                                <span class="inline-flex items-center px-4 py-2 bg-white/20 backdrop-blur-sm rounded-full text-sm font-semibold text-white border border-white/30">
                                    {{ ucfirst($subscriptionCycle->status) }}
                                </span>
                            </div>

                            {{-- Token Progress --}}
                            <div class="space-y-4">
                                {{-- Stats Row --}}
                                @php
                                    $usagePercent = ($subscriptionCycle->tokens_used / $subscriptionCycle->tokens_allocated) * 100;
                                @endphp
                                <div class="grid grid-cols-3 gap-4">
                                    <div class="bg-white/10 backdrop-blur-sm rounded-xl p-4 border border-white/20">
                                        <p class="text-white/70 text-sm mb-1">Available</p>
                                        <p class="text-2xl font-bold text-white">{{ number_format($subscriptionCycle->tokens_allocated - $subscriptionCycle->tokens_used) }}</p>
                                        <p class="text-white/60 text-xs mt-1">{{ round(100 - $usagePercent, 1) }}%</p>
                                    </div>
                                    <div class="bg-white/10 backdrop-blur-sm rounded-xl p-4 border border-white/20">
                                        <p class="text-white/70 text-sm mb-1">Used</p>
                                        <p class="text-2xl font-bold text-white">{{ number_format($subscriptionCycle->tokens_used) }}</p>
                                        <p class="text-white/60 text-xs mt-1">{{ round($usagePercent, 1) }}%</p>
                                    </div>
                                    <div class="bg-white/10 backdrop-blur-sm rounded-xl p-4 border border-white/20">
                                        <p class="text-white/70 text-sm mb-1">Total Allocated</p>
                                        <p class="text-2xl font-bold text-white">{{ number_format($subscriptionCycle->tokens_allocated) }}</p>
                                        <p class="text-white/60 text-xs mt-1">per month</p>
                                    </div>
                                </div>

                                {{-- Progress Bar --}}
                                <div>
                                    <div class="relative w-full h-3 bg-white/20 rounded-full overflow-hidden">
                                        <div class="h-full bg-white rounded-full transition-all duration-500 shadow-lg"
                                             style="width: {{ $usagePercent }}%"></div>
                                    </div>
                                    <div class="flex justify-between text-xs text-white/70 mt-2">
                                        <span>0</span>
                                        <span>{{ number_format($subscriptionCycle->tokens_allocated) }} tokens</span>
                                    </div>
                                </div>

                                {{-- Warning Messages --}}
                                @if($subscriptionCycle->status === 'active' && $usagePercent >= 80)
                                    <div class="bg-yellow-500/20 border border-yellow-400/30 rounded-lg p-4">
                                        <div class="flex items-start">
                                            <svg class="w-5 h-5 text-yellow-400 mr-3 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                            </svg>
                                            <div>
                                                <p class="text-yellow-300 font-semibold">Running Low on Tokens</p>
                                                <p class="text-yellow-200 text-sm mt-1">You're using {{ round($usagePercent, 0) }}% of your monthly tokens. Consider upgrading your tier.</p>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Details Grid --}}
                    <div class="p-8">
                        <div class="grid lg:grid-cols-3 gap-8">
                            {{-- Left Column - Details --}}
                            <div class="lg:col-span-2 space-y-8">
                                {{-- Cycle Information --}}
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                                        <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        Cycle Information
                                    </h3>
                                    <div class="grid sm:grid-cols-2 gap-4">
                                        <div class="p-4 bg-gray-50 dark:bg-gray-700/30 rounded-lg">
                                            <p class="text-sm text-gray-600 dark:text-gray-400">Cycle Number</p>
                                            <p class="text-xl font-bold text-gray-900 dark:text-white mt-1">#{{ $subscriptionCycle->cycle_number }}</p>
                                        </div>
                                        <div class="p-4 bg-gray-50 dark:bg-gray-700/30 rounded-lg">
                                            <p class="text-sm text-gray-600 dark:text-gray-400">Days Remaining</p>
                                            <p class="text-xl font-bold text-gray-900 dark:text-white mt-1">{{ $subscriptionCycle->getRemainingDays() }} days</p>
                                            @if($subscriptionCycle->isEndingSoon())
                                                <p class="text-xs text-orange-600 dark:text-orange-400 mt-1">Ending soon</p>
                                            @endif
                                        </div>
                                        <div class="p-4 bg-gray-50 dark:bg-gray-700/30 rounded-lg">
                                            <p class="text-sm text-gray-600 dark:text-gray-400">Monthly Price</p>
                                            <p class="text-xl font-bold text-gray-900 dark:text-white mt-1">GH₵ {{ number_format($subscriptionCycle->current_price, 2) }}</p>
                                        </div>
                                        <div class="p-4 bg-gray-50 dark:bg-gray-700/30 rounded-lg">
                                            <p class="text-sm text-gray-600 dark:text-gray-400">Tier</p>
                                            <p class="text-xl font-bold text-gray-900 dark:text-white mt-1">{{ $subscriptionCycle->pricingTier->name }}</p>
                                        </div>
                                        <div class="p-4 bg-gray-50 dark:bg-gray-700/30 rounded-lg">
                                            <p class="text-sm text-gray-600 dark:text-gray-400">Start Date (Anniversary)</p>
                                            <p class="text-xl font-bold text-gray-900 dark:text-white mt-1">{{ $subscriptionCycle->cycle_start_date->format('M d, Y') }}</p>
                                        </div>
                                        <div class="p-4 bg-gray-50 dark:bg-gray-700/30 rounded-lg">
                                            <p class="text-sm text-gray-600 dark:text-gray-400">End Date</p>
                                            <p class="text-xl font-bold text-gray-900 dark:text-white mt-1">{{ $subscriptionCycle->cycle_end_date->format('M d, Y') }}</p>
                                        </div>
                                    </div>
                                        </div>
                                        <div class="p-4 bg-gray-50 dark:bg-gray-700/30 rounded-lg">
                                            <p class="text-sm text-gray-600 dark:text-gray-400">Pricing Period</p>
                                            <p class="text-xl font-bold text-gray-900 dark:text-white mt-1">
                                                @if($subscriptionCycle->cycle_number <= $subscriptionCycle->pricingTier->initial_period_months)
                                                    Introductory
                                                @else
                                                    Regular
                                                @endif
                                            </p>
                                        </div>
                                        <div class="p-4 bg-gray-50 dark:bg-gray-700/30 rounded-lg">
                                            <p class="text-sm text-gray-600 dark:text-gray-400">Tier</p>
                                            <p class="text-xl font-bold text-gray-900 dark:text-white mt-1">{{ $subscriptionCycle->pricingTier->name }}</p>
                                        </div>
                                    </div>

                                {{-- Pricing Breakdown --}}
                                <div class="pt-8 border-t border-gray-200 dark:border-gray-700">
                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                                        <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        Pricing Information
                                    </h3>
                                    <div class="grid sm:grid-cols-2 gap-4">
                                        <div class="p-4 bg-gradient-to-br from-blue-50 to-cyan-50 dark:from-blue-900/20 dark:to-cyan-900/20 rounded-lg border border-blue-200 dark:border-blue-800">
                                            <p class="text-sm text-gray-600 dark:text-gray-400">Monthly Increment (Cycle {{ $subscriptionCycle->cycle_number }})</p>
                                            <p class="text-2xl font-bold text-blue-600 dark:text-blue-400 mt-1">GH₵ {{ number_format($subscriptionCycle->pricingTier->getMonthlyPriceIncrement($subscriptionCycle->cycle_number), 2) }}</p>
                                            @if($subscriptionCycle->cycle_number <= $subscriptionCycle->pricingTier->initial_period_months)
                                                <p class="text-xs text-blue-700 dark:text-blue-300 mt-2">Initial rate (first {{ $subscriptionCycle->pricingTier->initial_period_months }} months)</p>
                                            @else
                                                <p class="text-xs text-blue-700 dark:text-blue-300 mt-2">Reduced rate (after month {{ $subscriptionCycle->pricingTier->initial_period_months }})</p>
                                            @endif
                                        </div>
                                        <div class="p-4 bg-gradient-to-br from-purple-50 to-pink-50 dark:from-purple-900/20 dark:to-pink-900/20 rounded-lg border border-purple-200 dark:border-purple-800">
                                            <p class="text-sm text-gray-600 dark:text-gray-400">Cumulative Total to Date</p>
                                            <p class="text-2xl font-bold text-purple-600 dark:text-purple-400 mt-1">GH₵ {{ number_format($subscriptionCycle->current_price, 2) }}</p>
                                            <p class="text-xs text-purple-700 dark:text-purple-300 mt-2">Total amount paid through this cycle</p>
                                        </div>
                                    </div>

                                    {{-- Pricing Structure Explanation --}}
                                    <div class="mt-4 p-4 bg-amber-50 dark:bg-amber-900/20 rounded-lg border border-amber-200 dark:border-amber-800">
                                        <p class="text-sm text-amber-900 dark:text-amber-100">
                                            <span class="font-semibold">📋 Pricing Structure:</span> Month 1-{{ $subscriptionCycle->pricingTier->initial_period_months }}: <strong>GH₵{{ number_format($subscriptionCycle->pricingTier->initial_price, 2) }}/month</strong> | Month {{ $subscriptionCycle->pricingTier->initial_period_months + 1 }}+: <strong>GH₵{{ number_format($subscriptionCycle->pricingTier->subsequent_price, 2) }}/month</strong>
                                        </p>
                                    </div>
                                </div>
                            </div>

                            {{-- Right Column - Circular Progress & Actions --}}
                            <div class="space-y-6">
                                {{-- Circular Progress --}}
                                <div class="bg-gradient-to-br from-gray-50 to-blue-50 dark:from-gray-700/30 dark:to-blue-900/10 rounded-xl p-6 border border-gray-200 dark:border-gray-700">
                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-6 text-center">Token Status</h3>

                                    <div class="flex justify-center mb-6">
                                        <div class="relative w-32 h-32">
                                            <svg class="w-32 h-32 transform -rotate-90" viewBox="0 0 120 120">
                                                <circle cx="60" cy="60" r="54" fill="none" stroke="currentColor" stroke-width="8" class="text-gray-200 dark:text-gray-700"/>
                                                <circle cx="60" cy="60" r="54" fill="none" stroke="currentColor" stroke-width="8" stroke-dasharray="339.29" :stroke-dashoffset="`{{ 339.29 * (1 - ($usagePercent / 100)) }}`" class="text-blue-500 transition-all duration-500" stroke-linecap="round"/>
                                            </svg>
                                            <div class="absolute inset-0 flex flex-col items-center justify-center">
                                                <span class="text-3xl font-bold text-gray-900 dark:text-white">{{ round($usagePercent, 0) }}%</span>
                                                <span class="text-xs text-gray-600 dark:text-gray-400">Used</span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="space-y-2">
                                        <div class="flex justify-between text-sm">
                                            <span class="text-gray-600 dark:text-gray-400">Base Allocation</span>
                                            <span class="font-semibold text-gray-900 dark:text-white">{{ number_format($subscriptionCycle->pricingTier->monthly_token_limit) }}</span>
                                        </div>
                                        <div class="flex justify-between text-sm">
                                            <span class="text-gray-600 dark:text-gray-400">Topup Tokens</span>
                                            <span class="font-semibold text-blue-600 dark:text-blue-400">{{ number_format(max(0, $subscriptionCycle->tokens_allocated - $subscriptionCycle->pricingTier->monthly_token_limit)) }}</span>
                                        </div>
                                        <div class="flex justify-between text-sm">
                                            <span class="text-gray-600 dark:text-gray-400">Used</span>
                                            <span class="font-semibold text-gray-900 dark:text-white">{{ number_format($subscriptionCycle->tokens_used) }}</span>
                                        </div>
                                        <div class="flex justify-between text-sm">
                                            <span class="text-gray-600 dark:text-gray-400">Available</span>
                                            <span class="font-semibold text-gray-900 dark:text-white">{{ number_format($subscriptionCycle->tokens_allocated - $subscriptionCycle->tokens_used) }}</span>
                                        </div>
                                        <div class="flex justify-between text-sm border-t border-gray-200 dark:border-gray-700 pt-2 mt-2">
                                            <span class="text-gray-600 dark:text-gray-400">Total Allocated</span>
                                            <span class="font-semibold text-gray-900 dark:text-white">{{ number_format($subscriptionCycle->tokens_allocated) }}</span>
                                        </div>
                                    </div>
                                </div>

                                {{-- Quick Actions --}}
                                <div class="bg-gradient-to-br from-indigo-50 to-purple-50 dark:from-indigo-900/10 dark:to-purple-900/10 rounded-xl p-6 border border-gray-200 dark:border-gray-700">
                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Cycle & Topup Information</h3>
                                    <div class="space-y-3 mb-4">
                                        <div class="p-3 bg-white dark:bg-gray-800 rounded-lg">
                                            <p class="text-xs text-gray-600 dark:text-gray-400 uppercase tracking-wide mb-1">Anniversary Cycle</p>
                                            <p class="text-sm font-semibold text-gray-900 dark:text-white">Resets every 30 days on {{ $subscriptionCycle->cycle_start_date->format('M d') }}</p>
                                        </div>
                                        <div class="p-3 bg-white dark:bg-gray-800 rounded-lg">
                                            <p class="text-xs text-gray-600 dark:text-gray-400 uppercase tracking-wide mb-1">Base Allocation</p>
                                            <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ number_format($subscriptionCycle->pricingTier->monthly_token_limit) }} tokens/month</p>
                                        </div>
                                        <div class="p-3 bg-white dark:bg-gray-800 rounded-lg">
                                            <p class="text-xs text-gray-600 dark:text-gray-400 uppercase tracking-wide mb-1">Topup Tokens</p>
                                            <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ number_format(max(0, $subscriptionCycle->tokens_allocated - $subscriptionCycle->pricingTier->monthly_token_limit)) }} carried forward</p>
                                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Unused topups carry over automatically</p>
                                        </div>
                                    </div>
                                    <div class="space-y-3">
                                        <a href="{{ route('token-subscriptions.index') }}"
                                           class="block w-full text-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors">
                                            View Subscriptions
                                        </a>
                                        @if($subscriptionCycle->status === 'active')
                                            <a href="{{ route('token-subscriptions.create') }}"
                                               class="block w-full text-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-medium transition-colors">
                                                Upgrade Tier
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</x-layouts.app>
