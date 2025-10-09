<div class="w-full">
    @if($subscription)
        @php
            $remaining = $subscription->remaining_percentage;

            if ($remaining >= 75) {
                $colorStart = '#10b981';
                $colorEnd = '#22c55e';
            } elseif ($remaining >= 50) {
                $colorStart = '#84cc16';
                $colorEnd = '#a3e635';
            } elseif ($remaining >= 25) {
                $colorStart = '#eab308';
                $colorEnd = '#fbbf24';
            } elseif ($remaining > 0) {
                $colorStart = '#f97316';
                $colorEnd = '#fb923c';
            } else {
                $colorStart = '#ef4444';
                $colorEnd = '#dc2626';
            }
        @endphp

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
            <div class="flex items-center justify-between mb-2">
                <div class="flex items-center space-x-2">
                    <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">
                        AI Tokens
                    </span>
                </div>
                <span class="text-xs text-gray-500 dark:text-gray-400">
                    {{ $subscription->package->name }}
                </span>
            </div>

            {{-- Horizontal Progress Bar --}}
            <div class="relative w-full h-6 bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden">
                <div
                    class="h-full transition-all duration-500 ease-in-out"
                    style="width: {{ $remaining }}%; background: linear-gradient(to right, {{ $colorStart }}, {{ $colorEnd }});">
                </div>
                @if($showText)
                    <div class="absolute inset-0 flex items-center justify-center">
                        <span class="text-xs font-bold text-gray-700 dark:text-gray-200">
                            {{ number_format($subscription->tokens_remaining) }} / {{ number_format($subscription->tokens_purchased) }}
                        </span>
                    </div>
                @endif
            </div>

            @if($showAlert && $subscription->isNearingDepletion())
                <div class="mt-2 flex items-center text-xs text-red-600 dark:text-red-400">
                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                    <span>Low tokens - <a href="{{ route('token-subscriptions.index') }}" class="underline">Top up</a></span>
                </div>
            @endif
        </div>
    @else
        <div class="bg-gray-100 dark:bg-gray-800 rounded-lg shadow p-4">
            <div class="text-center">
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">No active token subscription</p>
                <a href="{{ route('token-subscriptions.create') }}" class="text-xs text-blue-600 hover:text-blue-700 font-medium">
                    Get Started →
                </a>
            </div>
        </div>
    @endif
</div>
