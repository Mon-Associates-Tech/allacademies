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
                        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Subscription Overview</h1>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-0.5">{{ $subscription->package->name }} Package</p>
                    </div>

                    {{-- Quick Action Button --}}
                    @if($subscription->status->value === 'pending')
                        <a href="{{ route('payment.token.initialize', $subscription->id) }}"
                           class="inline-flex items-center px-5 py-2.5 bg-orange-600 hover:bg-orange-700 text-white rounded-lg font-medium transition-colors shadow-lg">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                            </svg>
                            Complete Payment
                        </a>
                    @elseif($subscription->status->value === 'active' || $subscription->status->value === 'depleted')
                        <a href="{{ route('token-subscriptions.create') }}"
                           class="inline-flex items-center px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors shadow-lg">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            Top Up Tokens
                        </a>
                    @endif
                </div>

                {{-- Success Alert --}}
                @if(session('success'))
                    <div class="mb-6 bg-green-50 dark:bg-green-900/20 border-l-4 border-green-500 p-4 rounded-r-lg">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-green-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <span class="text-green-700 dark:text-green-300 font-medium">{{ session('success') }}</span>
                        </div>
                    </div>
                @endif

                {{-- Main Content Card --}}
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl overflow-hidden border border-gray-200 dark:border-gray-700">

                    {{-- Status Banner Section --}}
                    <div class="relative overflow-hidden {{ $subscription->status->value === 'active' ? 'bg-gradient-to-r from-green-500 to-emerald-600' : ($subscription->status === 'depleted' ? 'bg-gradient-to-r from-red-500 to-pink-600' : ($subscription->status === 'pending' ? 'bg-gradient-to-r from-orange-500 to-amber-600' : 'bg-gradient-to-r from-gray-500 to-gray-600')) }} p-8">
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
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                        </svg>
                                        {{ $subscription->package->name === 'Premium' ? 'Advanced' : 'Basic' }}
                                    </div>
                                    <h2 class="text-3xl font-bold text-white mb-1">{{ $subscription->package->name }}</h2>
                                    <p class="text-white/80">
                                        @if($subscription->status->value === 'active')
                                            Active and ready to use
                                        @elseif($subscription->status->value === 'depleted')
                                            All tokens have been used
                                        @elseif($subscription->status->value === 'pending')
                                            Awaiting payment confirmation
                                        @else
                                            {{ ucfirst($subscription->status->value) }}
                                        @endif
                                    </p>
                                </div>

                                <span class="inline-flex items-center px-4 py-2 bg-white/20 backdrop-blur-sm rounded-full text-sm font-semibold text-white border border-white/30">
                                    {{ ucfirst($subscription->status->value) }}
                                </span>
                            </div>

                            {{-- Token Progress --}}
                            <div class="space-y-4">
                                {{-- Stats Row --}}
                                <div class="grid grid-cols-3 gap-4">
                                    <div class="bg-white/10 backdrop-blur-sm rounded-xl p-4 border border-white/20">
                                        <p class="text-white/70 text-sm mb-1">Available</p>
                                        <p class="text-2xl font-bold text-white">{{ number_format($subscription->tokens_remaining) }}</p>
                                        <p class="text-white/60 text-xs mt-1">{{ number_format($subscription->remaining_percentage, 1) }}%</p>
                                    </div>
                                    <div class="bg-white/10 backdrop-blur-sm rounded-xl p-4 border border-white/20">
                                        <p class="text-white/70 text-sm mb-1">Used</p>
                                        <p class="text-2xl font-bold text-white">{{ number_format($subscription->tokens_used) }}</p>
                                        <p class="text-white/60 text-xs mt-1">{{ number_format($subscription->usage_percentage, 1) }}%</p>
                                    </div>
                                    <div class="bg-white/10 backdrop-blur-sm rounded-xl p-4 border border-white/20">
                                        <p class="text-white/70 text-sm mb-1">Total</p>
                                        <p class="text-2xl font-bold text-white">{{ number_format($subscription->tokens_purchased) }}</p>
                                        <p class="text-white/60 text-xs mt-1">
                                            @if($subscription->package->isFree())
                                                Free
                                            @else
                                                GH₵{{ number_format($subscription->package->price, 2) }}
                                            @endif
                                        </p>
                                    </div>
                                </div>

                                {{-- Progress Bar --}}
                                <div>
                                    <div class="relative w-full h-3 bg-white/20 rounded-full overflow-hidden">
                                        <div class="h-full bg-white rounded-full transition-all duration-500 shadow-lg"
                                             style="width: {{ $subscription->remaining_percentage }}%">
                                        </div>
                                    </div>
                                    <div class="flex justify-between text-xs text-white/70 mt-2">
                                        <span>0</span>
                                        <span>{{ number_format($subscription->tokens_purchased) }} tokens</span>
                                    </div>
                                </div>

                                {{-- Warning Messages --}}
                                @if($subscription->status->value === 'active' && $subscription->isNearingDepletion())
                                    <div class="bg-yellow-500/20 border border-yellow-400/30 rounded-lg p-4">
                                        <div class="flex items-start">
                                            <svg class="w-5 h-5 mr-3 flex-shrink-0 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                            </svg>
                                            <div>
                                                <h4 class="font-semibold text-white mb-1">Low Token Warning</h4>
                                                <p class="text-sm text-white/90">You have less than 10% of your tokens remaining. Consider topping up soon.</p>
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
                                {{-- Subscription Information --}}
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                                        <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        Subscription Details
                                    </h3>
                                    <div class="grid sm:grid-cols-2 gap-4">
                                        <div class="p-4 bg-gray-50 dark:bg-gray-700/30 rounded-lg">
                                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Package</p>
                                            <p class="font-semibold text-gray-900 dark:text-white">{{ $subscription->package->name }}</p>
                                        </div>
                                        <div class="p-4 bg-gray-50 dark:bg-gray-700/30 rounded-lg hidden">
                                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">AI Model</p>
                                            <p class="font-semibold text-gray-900 dark:text-white">{{ $subscription->package->model }}</p>
                                        </div>
                                        <div class="p-4 bg-gray-50 dark:bg-gray-700/30 rounded-lg">
                                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Purchase Date</p>
                                            <p class="font-semibold text-gray-900 dark:text-white">
                                                {{ $subscription->purchased_at ? $subscription->purchased_at->format('M d, Y') : 'Pending' }}
                                            </p>
                                        </div>
                                        <div class="p-4 bg-gray-50 dark:bg-gray-700/30 rounded-lg">
                                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Expiry Date</p>
                                            <p class="font-semibold text-gray-900 dark:text-white">
                                                {{ $subscription->expires_at ? $subscription->expires_at->format('M d, Y') : 'Never' }}
                                            </p>
                                        </div>
                                    </div>

                                    <div class="mt-4 p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800">
                                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Reference Number</p>
                                        <p class="font-mono text-sm text-gray-900 dark:text-white">{{ $subscription->reference }}</p>
                                    </div>
                                </div>

                                {{-- Payment Information --}}
                                @if($subscription->payment)
                                    <div class="pt-8 border-t border-gray-200 dark:border-gray-700">
                                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                                            <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                            Payment Details
                                        </h3>
                                        <div class="grid sm:grid-cols-3 gap-4">
                                            <div class="p-4 bg-gray-50 dark:bg-gray-700/30 rounded-lg">
                                                <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Amount Paid</p>
                                                <p class="font-semibold text-gray-900 dark:text-white">
                                                    {{ $subscription->payment->currency }} {{ number_format($subscription->payment->amount, 2) }}
                                                </p>
                                            </div>
                                            <div class="p-4 bg-gray-50 dark:bg-gray-700/30 rounded-lg">
                                                <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Payment Status</p>
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                    {{ $subscription->payment->status->value === 'succeeded' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200' }}">
                                                    {{ ucfirst($subscription->payment->status->value) }}
                                                </span>
                                            </div>
                                            <div class="p-4 bg-gray-50 dark:bg-gray-700/30 rounded-lg">
                                                <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Reference</p>
                                                <p class="font-mono text-xs text-gray-900 dark:text-white truncate">{{ $subscription->payment->reference }}</p>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                {{-- Usage Statistics --}}
                                @if($subscription->usageLogs->count() > 0)
                                    <div class="pt-8 border-t border-gray-200 dark:border-gray-700">
                                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                                            <svg class="w-5 h-5 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                            </svg>
                                            Usage Statistics
                                        </h3>
                                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                                            <div class="text-center p-4 bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900/20 dark:to-blue-800/20 rounded-lg">
                                                <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Requests</p>
                                                <p class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ $subscription->usageLogs->count() }}</p>
                                            </div>
                                            <div class="text-center p-4 bg-gradient-to-br from-green-50 to-green-100 dark:from-green-900/20 dark:to-green-800/20 rounded-lg">
                                                <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Avg/Request</p>
                                                <p class="text-2xl font-bold text-green-600 dark:text-green-400">{{ number_format($subscription->usageLogs->avg('total_tokens')) }}</p>
                                            </div>
                                            <div class="text-center p-4 bg-gradient-to-br from-purple-50 to-purple-100 dark:from-purple-900/20 dark:to-purple-800/20 rounded-lg">
                                                <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Total Used</p>
                                                <p class="text-2xl font-bold text-purple-600 dark:text-purple-400">{{ number_format($subscription->usageLogs->sum('total_tokens')) }}</p>
                                            </div>
                                            <div class="text-center p-4 bg-gradient-to-br from-orange-50 to-orange-100 dark:from-orange-900/20 dark:to-orange-800/20 rounded-lg">
                                                <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Last Used</p>
                                                <p class="text-sm font-bold text-orange-600 dark:text-orange-400">
                                                    {{ $subscription->usageLogs->first()?->created_at->diffForHumans(null, true) ?? 'Never' }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>

                            {{-- Right Column - Circular Progress & Actions --}}
                            <div class="space-y-6">
                                {{-- Circular Progress --}}
                                <div class="bg-gradient-to-br from-gray-50 to-blue-50 dark:from-gray-700/30 dark:to-blue-900/10 rounded-xl p-6 border border-gray-200 dark:border-gray-700">
                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-6 text-center">Token Status</h3>

                                    <div class="flex justify-center mb-6">
                                        <div class="relative inline-flex items-center justify-center">
                                            <svg class="transform -rotate-90" width="180" height="180">
                                                <circle cx="90" cy="90" r="75" stroke="currentColor" stroke-width="14" fill="transparent"
                                                        class="text-gray-200 dark:text-gray-700"/>
                                                <circle cx="90" cy="90" r="75"
                                                        stroke="{{ $subscription->usage_percentage <= 25 ? '#10b981' : ($subscription->usage_percentage <= 50 ? '#84cc16' : ($subscription->usage_percentage <= 75 ? '#eab308' : ($subscription->usage_percentage <= 90 ? '#f97316' : '#ef4444'))) }}"
                                                        stroke-width="14" fill="transparent"
                                                        stroke-dasharray="{{ 2 * 3.14159 * 75 }}"
                                                        stroke-dashoffset="{{ 2 * 3.14159 * 75 * (1 - $subscription->remaining_percentage / 100) }}"
                                                        stroke-linecap="round" class="transition-all duration-500"/>
                                            </svg>
                                            <div class="absolute inset-0 flex flex-col items-center justify-center">
                                                <span class="text-5xl font-bold text-gray-900 dark:text-white">
                                                    {{ number_format($subscription->remaining_percentage, 0) }}%
                                                </span>
                                                <span class="text-sm text-gray-500 dark:text-gray-400 mt-1">remaining</span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="space-y-2">
                                        <div class="flex justify-between items-center p-3 bg-white dark:bg-gray-800 rounded-lg">
                                            <span class="text-sm text-gray-600 dark:text-gray-400">Available</span>
                                            <span class="font-bold text-green-600 dark:text-green-400">
                                                {{ number_format($subscription->tokens_remaining) }}
                                            </span>
                                        </div>
                                        <div class="flex justify-between items-center p-3 bg-white dark:bg-gray-800 rounded-lg">
                                            <span class="text-sm text-gray-600 dark:text-gray-400">Used</span>
                                            <span class="font-bold text-red-600 dark:text-red-400">
                                                {{ number_format($subscription->tokens_used) }}
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                {{-- Quick Actions --}}
                                <div class="bg-gradient-to-br from-indigo-50 to-purple-50 dark:from-indigo-900/10 dark:to-purple-900/10 rounded-xl p-6 border border-gray-200 dark:border-gray-700">
                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Quick Actions</h3>
                                    <div class="space-y-3">
                                        @if($subscription->status->value === 'active' || $subscription->status->value === 'depleted')
                                            <a href="{{ route('token-subscriptions.create') }}"
                                               class="w-full flex items-center justify-center px-4 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white rounded-lg font-medium transition-all shadow-lg">
                                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                                </svg>
                                                Buy More Tokens
                                            </a>
                                        @endif

                                        @if($subscription->status->value === 'pending')
                                            <a href="{{ route('payment.token.initialize', $subscription->id) }}"
                                               class="w-full flex items-center justify-center px-4 py-3 bg-orange-600 hover:bg-orange-700 text-white rounded-lg font-medium transition-colors shadow-lg">
                                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                                                </svg>
                                                Complete Payment
                                            </a>
                                        @endif

                                        <a href="{{ route('token-subscriptions.index') }}"
                                           class="w-full flex items-center justify-center px-4 py-3 border-2 border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 font-medium transition-colors">
                                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                                            </svg>
                                            All Subscriptions
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Usage History Table --}}
                        <div class="mt-8 pt-8 border-t border-gray-200 dark:border-gray-700">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                Usage History
                                <span class="ml-2 text-sm font-normal text-gray-500 dark:text-gray-400">
                                    ({{ $subscription->usageLogs->count() }} {{ Str::plural('request', $subscription->usageLogs->count()) }})
                                </span>
                            </h3>

                            @if($subscription->usageLogs->count() > 0)
                                <div class="overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-700">
                                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                        <thead class="bg-gray-50 dark:bg-gray-700">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Date & Time</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Request Type</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Model</th>
                                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Prompt</th>
                                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Response</th>
                                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Total</th>
                                        </tr>
                                        </thead>
                                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                        @foreach($subscription->usageLogs as $log)
                                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                                    <div class="text-gray-900 dark:text-white font-medium">{{ $log->created_at->format('M d, Y') }}</div>
                                                    <div class="text-gray-500 dark:text-gray-400 text-xs">{{ $log->created_at->format('h:i A') }}</div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                        <span class="inline-flex items-center px-2.5 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                                            {{ ucfirst(str_replace('_', ' ', $log->request_type ?? 'Unknown')) }}
                                                        </span>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-400">
                                                    <div class="flex items-center">
                                                        <svg class="w-4 h-4 mr-1.5 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                                        </svg>
                                                        {{ $log->name === 'Premium' ? 'Advanced' : 'Basic' }}
                                                    </div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-center">
                                                    <span class="text-gray-900 dark:text-white font-medium">{{ number_format($log->prompt_tokens) }}</span>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-center">
                                                    <span class="text-gray-900 dark:text-white font-medium">{{ number_format($log->completion_tokens) }}</span>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-bold bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-200">
                                                            {{ number_format($log->total_tokens) }}
                                                        </span>
                                                </td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="bg-gray-50 dark:bg-gray-700/30 rounded-lg p-12 text-center border-2 border-dashed border-gray-300 dark:border-gray-600">
                                    <div class="inline-flex items-center justify-center w-16 h-16 bg-gray-200 dark:bg-gray-600 rounded-full mb-4">
                                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                    </div>
                                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">No Usage History Yet</h3>
                                    <p class="text-gray-500 dark:text-gray-400">
                                        Start using Messenger features to see your token usage appear here.
                                    </p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</x-layouts.app>
