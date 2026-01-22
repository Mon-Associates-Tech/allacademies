<x-layouts.app>
    <section class="min-h-screen bg-gradient-to-br from-gray-50 to-blue-50 dark:from-gray-900 dark:to-blue-900/20">
        <div class="container mx-auto px-4 py-8">
            <div class="max-w-7xl mx-auto">
                {{-- Header Navigation --}}
                <div class="flex items-center gap-4 mb-6">
                    <a href="{{ route('token-subscriptions.index') }}"
                       class="flex items-center justify-center w-10 h-10 rounded-lg bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors shadow-sm">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                    </a>
                    <div class="flex-1">
                        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Token Subscription Details</h1>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-0.5">
                            Reference: {{ $subscription->reference }}</p>
                    </div>

                    {{-- Status Badge --}}
                    <span class="inline-flex items-center px-4 py-2 bg-gray-100 dark:bg-gray-700 rounded-full text-sm font-semibold
                        @if($subscription->status->value === 'active') text-green-700 dark:text-green-400
                        @elseif($subscription->status->value === 'expired') text-red-700 dark:text-red-400
                        @elseif($subscription->status->value === 'pending') text-yellow-700 dark:text-yellow-400
                        @else text-gray-700 dark:text-gray-400 @endif">
                        {{ ucfirst($subscription->status->value) }}
                    </span>
                </div>

                {{-- Main Content Card --}}
                <div
                    class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl overflow-hidden border border-gray-200 dark:border-gray-700">

                    {{-- Status Banner Section --}}
                    <div class="relative overflow-hidden
                        @if($subscription->status->value === 'active') bg-gradient-to-r from-green-500 to-emerald-600
                        @elseif($subscription->status->value === 'expired') bg-gradient-to-r from-red-500 to-rose-600
                        @elseif($subscription->status->value === 'pending') bg-gradient-to-r from-yellow-500 to-amber-600
                        @else bg-gradient-to-r from-gray-500 to-gray-600 @endif p-8">

                        {{-- Background Pattern --}}
                        <div class="absolute inset-0 opacity-10">
                            <div class="absolute top-0 left-0 w-64 h-64 bg-white rounded-full blur-3xl"></div>
                            <div class="absolute bottom-0 right-0 w-96 h-96 bg-white rounded-full blur-3xl"></div>
                        </div>

                        <div class="relative">
                            <div class="flex items-start justify-between mb-6">
                                <div>
                                    <div
                                        class="inline-flex items-center px-3 py-1 bg-white/20 backdrop-blur-sm rounded-full text-sm font-semibold text-white mb-3">
                                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor"
                                             viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        One-Time Purchase
                                    </div>
                                    <h2 class="text-3xl font-bold text-white mb-1">Token Subscription</h2>
                                    <p class="text-white/80">
                                        @if($subscription->status->value === 'active')
                                            Your tokens are active and ready to use
                                        @elseif($subscription->status->value === 'expired')
                                            This subscription has expired
                                        @elseif($subscription->status->value === 'pending')
                                            Awaiting payment confirmation
                                        @elseif($subscription->status->value === 'depleted')
                                            All tokens have been used
                                        @else
                                            {{ ucfirst($subscription->status->value) }} status
                                        @endif
                                    </p>
                                </div>
                            </div>

                            {{-- Token Progress --}}
                            <div class="space-y-4">
                                {{-- Stats Row --}}
                                @php
                                    $usagePercent = ($subscription->tokens_used / $subscription->tokens_purchased) * 100;
                                @endphp
                                <div class="grid grid-cols-3 gap-4">
                                    <div class="bg-white/10 backdrop-blur-sm rounded-xl p-4 border border-white/20">
                                        <p class="text-white/70 text-sm mb-1">Available</p>
                                        <p class="text-2xl font-bold text-white">{{ number_format($subscription->tokens_remaining) }}</p>
                                        <p class="text-white/60 text-xs mt-1">{{ round(100 - $usagePercent, 1) }}%</p>
                                    </div>
                                    <div class="bg-white/10 backdrop-blur-sm rounded-xl p-4 border border-white/20">
                                        <p class="text-white/70 text-sm mb-1">Used</p>
                                        <p class="text-2xl font-bold text-white">{{ number_format($subscription->tokens_used) }}</p>
                                        <p class="text-white/60 text-xs mt-1">{{ round($usagePercent, 1) }}%</p>
                                    </div>
                                    <div class="bg-white/10 backdrop-blur-sm rounded-xl p-4 border border-white/20">
                                        <p class="text-white/70 text-sm mb-1">Total Purchased</p>
                                        <p class="text-2xl font-bold text-white">{{ number_format($subscription->tokens_purchased) }}</p>
                                        <p class="text-white/60 text-xs mt-1">tokens</p>
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
                                        <span>{{ number_format($subscription->tokens_purchased) }} tokens</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Content Section --}}
                    <div class="p-8">
                        {{-- Information Grid --}}
                        <div class="grid md:grid-cols-2 gap-8 mb-8">
                            {{-- Subscription Information --}}
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                                    <svg class="w-5 h-5 mr-2 text-blue-600 dark:text-blue-400" fill="none"
                                         stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    Subscription Information
                                </h3>
                                <div class="space-y-3">
                                    <div class="flex justify-between items-start">
                                        <span class="text-gray-600 dark:text-gray-400">Purchase Date</span>
                                        <span
                                            class="font-semibold text-gray-900 dark:text-white">{{ $subscription->purchased_at?->format('M d, Y') ?? 'N/A' }}</span>
                                    </div>
                                    <div class="flex justify-between items-start">
                                        <span class="text-gray-600 dark:text-gray-400">Activation Date</span>
                                        <span
                                            class="font-semibold text-gray-900 dark:text-white">{{ $subscription->activated_at?->format('M d, Y') ?? 'Not activated' }}</span>
                                    </div>
                                    <div class="flex justify-between items-start">
                                        <span class="text-gray-600 dark:text-gray-400">Expires</span>
                                        <span class="font-semibold
                                            @if($subscription->expires_at && $subscription->expires_at->isFuture()) text-green-600 dark:text-green-400
                                            @else text-red-600 dark:text-red-400 @endif">
                                            {{ $subscription->expires_at?->format('M d, Y') ?? 'Never' }}
                                        </span>
                                    </div>
                                    <div class="flex justify-between items-start">
                                        <span class="text-gray-600 dark:text-gray-400">Reference</span>
                                        <code
                                            class="bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded text-xs text-gray-900 dark:text-white">{{ $subscription->reference }}</code>
                                    </div>
                                </div>
                            </div>

                            {{-- Token Breakdown --}}
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                                    <svg class="w-5 h-5 mr-2 text-blue-600 dark:text-blue-400" fill="none"
                                         stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                    </svg>
                                    Token Breakdown
                                </h3>
                                <div class="space-y-3">
                                    <div
                                        class="flex justify-between items-center p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                                        <span class="text-gray-600 dark:text-gray-400">Total Tokens</span>
                                        <span
                                            class="font-bold text-xl text-gray-900 dark:text-white">{{ number_format($subscription->tokens_purchased) }}</span>
                                    </div>
                                    <div
                                        class="flex justify-between items-center p-3 bg-red-50 dark:bg-red-900/20 rounded-lg">
                                        <span class="text-red-600 dark:text-red-400">Used</span>
                                        <span
                                            class="font-bold text-xl text-red-600 dark:text-red-400">{{ number_format($subscription->tokens_used) }}</span>
                                    </div>
                                    <div
                                        class="flex justify-between items-center p-3 bg-green-50 dark:bg-green-900/20 rounded-lg">
                                        <span class="text-green-600 dark:text-green-400">Remaining</span>
                                        <span
                                            class="font-bold text-xl text-green-600 dark:text-green-400">{{ number_format($subscription->tokens_remaining) }}</span>
                                    </div>
                                    <div
                                        class="text-sm text-gray-600 dark:text-gray-400 pt-2 border-t border-gray-200 dark:border-gray-700">
                                        Usage: <span class="font-semibold">{{ $subscription->usage_percentage }}%</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Action Buttons --}}
                        <div class="border-t border-gray-200 dark:border-gray-700 pt-8">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Actions</h3>
                            <div class="grid sm:grid-cols-2 gap-4">
                                <a href="{{ route('token-subscriptions.topup', $subscription) }}"
                                   class="inline-fl hidden items-center justify-center px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors shadow-lg">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M12 4v16m8-8H4"/>
                                    </svg>
                                    Purchase More Tokens
                                </a>
                                <a href="{{ route('token-subscriptions.create') }}"
                                   class="inline-flex items-center justify-center px-6 py-3 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg font-medium transition-colors shadow-lg">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                    </svg>
                                    Upgrade to Monthly Tier
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</x-layouts.app>
