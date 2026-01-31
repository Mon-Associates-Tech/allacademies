<x-layouts.app>
    <div class="min-h-screen bg-gradient-to-br from-gray-50 to-blue-50 dark:from-gray-900 dark:to-blue-900/20">
        <div class="container mx-auto px-4 py-8">
            <div class="max-w-5xl mx-auto">
                {{-- Header --}}
                <div class="mb-8">
                    <a href="{{ route('token-subscriptions.show', $cycle->id) }}" class="inline-flex items-center text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 mb-4">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                        </svg>
                        Back to Subscription
                    </a>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Topup Tokens</h1>
                    <p class="text-gray-600 dark:text-gray-400 mt-2">Add extra tokens to your current subscription cycle</p>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    {{-- Left: Topup Form --}}
                    <div class="lg:col-span-2">
                        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-8 border border-gray-200 dark:border-gray-700">
                            {{-- Current Cycle Info --}}
                            <div class="mb-8 p-6 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Current Cycle Information</h3>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">Cycle Number</p>
                                        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $cycle->cycle_number }}</p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">Status</p>
                                        <p class="text-2xl font-bold text-green-600 dark:text-green-400">{{ ucfirst($cycle->status) }}</p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">Base Tokens Remaining</p>
                                        <p class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ number_format($cycle->getBaseTokensRemaining()) }}</p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">Days Remaining</p>
                                        <p class="text-2xl font-bold text-purple-600 dark:text-purple-400">{{ $cycle->getRemainingDays() }}</p>
                                    </div>
                                </div>
                            </div>

                            {{-- Topup Form --}}
                            <form action="{{ route('token-subscriptions.process-topup') }}" method="POST" class="space-y-6"
                                x-data="{
                                    amount: '',
                                    tokensPerCurrency: {{ ($pricingTier->monthly_token_limit / (float) $pricingTier->base_price) }},
                                    get calculatedTokens() {
                                        const amt = parseFloat(this.amount) || 0;
                                        return Math.floor(amt * this.tokensPerCurrency);
                                    }
                                }">
                                @csrf

                                <input type="hidden" name="cycle_id" value="{{ $cycle->id }}">

                                {{-- Amount Selection --}}
                                <div>
                                    <label for="amount" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
                                        Topup Amount <span class="text-red-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <span class="absolute left-4 top-3 text-gray-500 dark:text-gray-400 text-lg">GH₵</span>
                                        <input 
                                            type="number" 
                                            id="amount"
                                            name="amount" 
                                            step="0.01"
                                            min="10"
                                            x-model.number="amount"
                                            class="w-full pl-10 pr-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                            placeholder="Enter amount (minimum GH₵10)"
                                            required
                                            @error('amount') aria-invalid="true" @enderror
                                        >
                                    </div>
                                    @error('amount')
                                        <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">Minimum topup: GH₵10</p>

                                    {{-- Token Preview --}}
                                    <div x-show="amount >= 10" class="mt-4 p-4 bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-900/30 dark:to-indigo-900/30 rounded-lg border border-blue-200 dark:border-blue-800 transition-all">
                                        <div class="flex items-center justify-between">
                                            <div>
                                                <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">You will receive</p>
                                                <p class="text-3xl font-bold text-blue-600 dark:text-blue-400" x-text="calculatedTokens.toLocaleString()"></p>
                                               
                                            </div>
                                            <div class="text-right">
                                                <svg class="w-12 h-12 text-blue-300 dark:text-blue-700" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M10.5 1.5H5.75A2.25 2.25 0 003.5 3.75v12.5A2.25 2.25 0 005.75 18.5h8.5a2.25 2.25 0 002.25-2.25V6.5m-11-5v5h5m-2-5l6.5 6.5"/>
                                                </svg>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Tokens Info --}}
                                <div class="p-4 bg-amber-50 dark:bg-amber-900/20 rounded-lg border border-amber-200 dark:border-amber-800">
                                    <h4 class="font-semibold text-amber-900 dark:text-amber-100 mb-3">How Topup Works</h4>
                                    <ul class="space-y-2 text-sm text-amber-800 dark:text-amber-200">
                                        <li class="flex items-start">
                                            <span class="mr-2">•</span>
                                            <span>Topup tokens are added to your current cycle immediately</span>
                                        </li>
                                        <li class="flex items-start">
                                            <span class="mr-2">•</span>
                                            <span>Base tokens are used first, then topup tokens</span>
                                        </li>
                                        <li class="flex items-start">
                                            <span class="mr-2">•</span>
                                            <span>Unused topup tokens carry over to your next cycle</span>
                                        </li>
                                        <li class="flex items-start">
                                            <span class="mr-2">•</span>
                                            <span>Base allocation always resets each cycle</span>
                                        </li>
                                    </ul>
                                </div>

                                {{-- Submit Button --}}
                                <button 
                                    type="submit"
                                    class="w-full bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-semibold py-3 px-6 rounded-lg transition-all shadow-lg"
                                >
                                    <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                    </svg>
                                    Proceed to Checkout
                                </button>
                            </form>
                        </div>
                    </div>

                    {{-- Right: Sidebar Info --}}
                    <div class="space-y-6">
                        {{-- Pricing Tier Info --}}
                        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 border border-gray-200 dark:border-gray-700">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Your Tier</h3>
                            <p class="text-2xl font-bold text-blue-600 dark:text-blue-400 mb-2">{{ $pricingTier->name }}</p>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">{{ $pricingTier->description }}</p>

                            <div class="space-y-3 border-t border-gray-200 dark:border-gray-700 pt-4 mt-4">
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600 dark:text-gray-400">Monthly Base</span>
                                    <span class="font-semibold text-gray-900 dark:text-white">{{ number_format($pricingTier->monthly_token_limit) }}</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600 dark:text-gray-400">Initial Price (6mo)</span>
                                    <span class="font-semibold text-gray-900 dark:text-white">GH₵{{ number_format($pricingTier->initial_price, 2) }}</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600 dark:text-gray-400">Subsequent Price</span>
                                    <span class="font-semibold text-gray-900 dark:text-white">GH₵{{ number_format($pricingTier->subsequent_price, 2) }}</span>
                                </div>
                            </div>
                        </div>

                        {{-- Usage Summary --}}
                        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 border border-gray-200 dark:border-gray-700">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Token Usage</h3>

                            {{-- Progress Bar --}}
                            <div class="mb-4">
                                <div class="flex justify-between items-center mb-2">
                                    <span class="text-sm font-medium text-gray-600 dark:text-gray-400">Overall Usage</span>
                                    <span class="text-sm font-semibold text-gray-900 dark:text-white">{{ $cycle->usage_percentage }}%</span>
                                </div>
                                <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                    <div 
                                        class="bg-gradient-to-r from-green-500 to-blue-500 h-2 rounded-full transition-all"
                                        style="width: {{ $cycle->usage_percentage }}%"
                                    ></div>
                                </div>
                            </div>

                            <div class="space-y-3">
                                <div class="flex justify-between items-center text-sm">
                                    <span class="text-gray-600 dark:text-gray-400">Total Allocated</span>
                                    <span class="font-semibold text-gray-900 dark:text-white">{{ number_format($cycle->tokens_allocated) }}</span>
                                </div>
                                <div class="flex justify-between items-center text-sm">
                                    <span class="text-gray-600 dark:text-gray-400">Used</span>
                                    <span class="font-semibold text-gray-900 dark:text-white">{{ number_format($cycle->tokens_used) }}</span>
                                </div>
                                <div class="flex justify-between items-center text-sm border-t border-gray-200 dark:border-gray-700 pt-3">
                                    <span class="text-gray-600 dark:text-gray-400">Remaining</span>
                                    <span class="font-semibold text-green-600 dark:text-green-400">{{ number_format($cycle->getTokensRemainingAttribute()) }}</span>
                                </div>
                            </div>
                        </div>

                        {{-- Topup Tokens Summary --}}
                        @if($cycle->topup_tokens_allocated > 0)
                            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 border border-green-200 dark:border-green-800 bg-gradient-to-br from-green-50 to-transparent dark:from-green-900/20 dark:to-transparent">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Topup Tokens</h3>

                                <div class="space-y-3">
                                    <div class="flex justify-between items-center text-sm">
                                        <span class="text-gray-600 dark:text-gray-400">Available</span>
                                        <span class="font-semibold text-green-600 dark:text-green-400">{{ number_format($cycle->getTopupTokensRemaining()) }}</span>
                                    </div>
                                    <div class="flex justify-between items-center text-sm">
                                        <span class="text-gray-600 dark:text-gray-400">Used</span>
                                        <span class="font-semibold text-gray-900 dark:text-white">{{ number_format(max(0, $cycle->tokens_used - $cycle->getBaseTokensAllocated())) }}</span>
                                    </div>
                                    <div class="flex justify-between items-center text-sm">
                                        <span class="text-gray-600 dark:text-gray-400">Total Topup</span>
                                        <span class="font-semibold text-gray-900 dark:text-white">{{ number_format($cycle->topup_tokens_allocated) }}</span>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
