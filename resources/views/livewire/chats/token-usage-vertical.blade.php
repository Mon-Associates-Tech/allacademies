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
            <div class="text-center mb-3">
                <div class="inline-flex items-center justify-center w-10 h-10 bg-blue-100 dark:bg-blue-900 rounded-full mb-2">
                    <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                </div>
                <h4 class="text-sm font-semibold text-gray-800 dark:text-gray-200">AI Tokens</h4>
                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $subscription->package->name }}</p>
            </div>

            {{-- Vertical Progress Bar --}}
            <div class="relative w-full h-32 bg-gray-200 dark:bg-gray-700 rounded-lg overflow-hidden flex flex-col-reverse">
                <div
                    class="w-full transition-all duration-500 ease-in-out"
                    style="height: {{ $remaining }}%; background: linear-gradient(to top, {{ $colorStart }}, {{ $colorEnd }});">
                </div>
            </div>

            <div class="mt-3 text-center">
                <p class="text-lg font-bold text-gray-900 dark:text-white">
                    {{ number_format($subscription->tokens_remaining) }}
                </p>
                <p class="text-xs text-gray-500 dark:text-gray-400">
                    of {{ number_format($subscription->tokens_purchased) }} remaining
                </p>
            </div>

            @if($showAlert && $subscription->isNearingDepletion())
                <div class="mt-3 p-2 bg-red-50 dark:bg-red-900/20 rounded text-center">
                    <p class="text-xs text-red-600 dark:text-red-400 font-medium">
                        Running low
                    </p>
                    <a href="{{ route('token-subscriptions.index') }}" class="text-xs text-red-700 dark:text-red-300 underline">
                        Top up now
                    </a>
                </div>
            @endif
        </div>
    @else
        <div class="bg-gray-100 dark:bg-gray-800 rounded-lg shadow p-4 text-center">
            <svg class="w-12 h-12 mx-auto text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
            </svg>
            <p class="text-sm text-gray-600 dark:text-gray-400 mb-3">No tokens yet</p>
            <a href="{{ route('token-subscriptions.create') }}" class="inline-block px-4 py-2 bg-blue-600 text-white text-xs rounded-lg hover:bg-blue-700">
                Get Free Tokens
            </a>
        </div>
    @endif
</div>
