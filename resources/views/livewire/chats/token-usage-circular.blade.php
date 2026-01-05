<div class="w-full">
    @if($subscription)
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
            <div class="text-center">
                <h4 class="text-sm font-semibold text-gray-800 dark:text-gray-200 mb-1">AI Tokens</h4>
                <p class="text-xs text-gray-500 dark:text-gray-400 mb-4">{{ $subscription->package?->name }}</p>

                {{-- Circular Progress --}}
                <div class="relative inline-flex items-center justify-center">
                    <svg class="transform -rotate-90" width="120" height="120">
                        {{-- Background circle --}}
                        <circle
                            cx="60"
                            cy="60"
                            r="52"
                            stroke="currentColor"
                            stroke-width="8"
                            fill="transparent"
                            class="text-gray-200 dark:text-gray-700"
                        />
                        {{-- Progress circle --}}
                        <circle
                            cx="60"
                            cy="60"
                            r="52"
                            stroke="{{ $this->getProgressColor() }}"
                            stroke-width="8"
                            fill="transparent"
                            stroke-dasharray="{{ 2 * 3.14159 * 52 }}"
                            stroke-dashoffset="{{ 2 * 3.14159 * 52 * (1 - $subscription->remaining_percentage / 100) }}"
                            stroke-linecap="round"
                            class="transition-all duration-500"
                        />
                    </svg>
                    <div class="absolute inset-0 flex flex-col items-center justify-center">
                        <span class="text-2xl font-bold text-gray-900 dark:text-white">
                            {{ number_format($subscription->remaining_percentage, 0) }}%
                        </span>
                        <span class="text-xs text-gray-500 dark:text-gray-400">remaining</span>
                    </div>
                </div>

                <div class="mt-4 space-y-1">
                    <div class="flex justify-between text-xs">
                        <span class="text-gray-600 dark:text-gray-400">Available:</span>
                        <span class="font-semibold text-gray-900 dark:text-white">
                            {{ number_format($subscription->tokens_remaining) }}
                        </span>
                    </div>
                    <div class="flex justify-between text-xs">
                        <span class="text-gray-600 dark:text-gray-400">Used:</span>
                        <span class="font-semibold text-gray-900 dark:text-white">
                            {{ number_format($subscription->tokens_used) }}
                        </span>
                    </div>
                </div>

                @if($showAlert && $subscription->isNearingDepletion())
                    <div class="mt-4 p-2 bg-red-50 dark:bg-red-900/20 rounded">
                        <p class="text-xs text-red-600 dark:text-red-400 font-medium mb-1">
                            ⚠️ Running Low
                        </p>
                        <a href="{{ route('token-subscriptions.index') }}" class="text-xs text-blue-600 dark:text-blue-400 underline">
                            Top up now
                        </a>
                    </div>
                @endif
            </div>
        </div>
    @else
        <div class="bg-gray-100 dark:bg-gray-800 rounded-lg shadow p-4">
            <div class="text-center">
                <div class="inline-flex items-center justify-center w-24 h-24 bg-gray-200 dark:bg-gray-700 rounded-full mb-3">
                    <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                </div>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-3">No active tokens</p>
                <a href="{{ route('token-subscriptions.create') }}" class="inline-block px-4 py-2 bg-blue-600 text-white text-xs rounded-lg hover:bg-blue-700">
                    Start Free
                </a>
            </div>
        </div>
    @endif
</div>
