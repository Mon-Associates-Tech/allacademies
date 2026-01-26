<div class="w-full">
    @if($subscription)
        {{-- Token Usage Progress Bar --}}
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 mb-4">
            <div class="flex justify-between items-center mb-2">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                    OpenAI Token Usage
                </h3>
                <span class="text-sm text-gray-600 dark:text-gray-400">
                    {{ $subscription->package?->name }} Package ({{ $subscription->package?->model }})
                </span>
            </div>

            {{-- Progress Bar with Gradient --}}
            <div class="relative w-full h-8 bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden mb-3">
                <div
                    class="h-full transition-all duration-500 ease-in-out flex items-center justify-center text-white text-xs font-bold"
                    style="width: {{ 100 - $subscription->usage_percentage }}%;
                           background: linear-gradient(to right,
                               @if($subscription->usage_percentage <= 25) #10b981, #22c55e
                               @elseif($subscription->usage_percentage <= 50) #84cc16, #a3e635
                               @elseif($subscription->usage_percentage <= 75) #eab308, #fbbf24
                               @elseif($subscription->usage_percentage <= 90) #f97316, #fb923c
                               @else #ef4444, #dc2626
                               @endif
                           );">
                    {{ number_format(100 - $subscription->usage_percentage, 1) }}% Remaining
                </div>
            </div>

            {{-- Token Stats --}}
            <div class="grid grid-cols-3 gap-4 text-center">
                <div>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">
                        {{ number_format($subscription->tokens_remaining) }}
                    </p>
                    <p class="text-xs text-gray-600 dark:text-gray-400">Remaining</p>
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">
                        {{ number_format($subscription->tokens_used) }}
                    </p>
                    <p class="text-xs text-gray-600 dark:text-gray-400">Used</p>
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">
                        {{ number_format($subscription->tokens_purchased) }}
                    </p>
                    <p class="text-xs text-gray-600 dark:text-gray-400">Total</p>
                </div>
            </div>

            {{-- Low Token Warning --}}
            @if($showAlert && $subscription->isNearingDepletion())
                <div class="mt-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                    <strong class="font-bold">Low Token Warning!</strong>
                    <span class="block sm:inline">
                        You have less than {{ number_format($subscription->tokens_remaining) }} tokens remaining.
                        Please top up soon to continue using AI features.
                    </span>
                    <span class="absolute top-0 bottom-0 right-0 px-4 py-3" wire:click="dismissAlert">
                        <svg class="fill-current h-6 w-6 text-red-500 cursor-pointer" role="button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                            <path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z"/>
                        </svg>
                    </span>
                </div>
            @endif

            {{-- Depleted State --}}
            @if($subscription->status === 'depleted')
                <div class="mt-4 bg-red-50 dark:bg-red-900/20 border-l-4 border-red-500 p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-red-800 dark:text-red-200">
                                Tokens Exhausted
                            </h3>
                            <p class="mt-2 text-sm text-red-700 dark:text-red-300">
                                Your token balance is depleted. Please purchase a new package to continue using AI features.
                            </p>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    @else
        {{-- No Active Subscription --}}
        <div class="bg-yellow-50 dark:bg-yellow-900/20 border-l-4 border-yellow-400 p-4 mb-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-yellow-800 dark:text-yellow-200">
                        No Active Subscription
                    </h3>
                    <p class="mt-2 text-sm text-yellow-700 dark:text-yellow-300">
                        You don't have an active token subscription. Please choose a package below to start using AI features.
                    </p>
                </div>
            </div>
        </div>
    @endif

    {{-- Available Packages --}}
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
            Available Token Packages
        </h3>

        <div class="grid md:grid-cols-2 gap-4">
            @foreach($packages as $package)
                <div class="border-2 rounded-lg p-4 transition-all duration-200 cursor-pointer
                    {{ $selectedPackage && $selectedPackage->id === $package->id
                        ? 'border-blue-500 bg-blue-50 dark:bg-blue-900/20'
                        : 'border-gray-200 dark:border-gray-700 hover:border-blue-300' }}"
                     wire:click="selectPackage({{ $package->id }})">

                    <div class="flex justify-between items-start mb-2">
                        <div>
                            <h4 class="text-xl font-bold text-gray-900 dark:text-white">
                                {{ $package->name }}
                            </h4>
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                {{ $package->model }}
                            </p>
                        </div>
                        <span class="text-2xl font-bold text-blue-600 dark:text-blue-400">
                            ${{ number_format($package->price, 2) }}
                        </span>
                    </div>

                    <p class="text-gray-700 dark:text-gray-300 text-sm mb-3">
                        {{ $package->description }}
                    </p>

                    <div class="flex items-center justify-between text-sm">
                        <span class="text-gray-600 dark:text-gray-400">
                            {{ number_format($package->token_limit) }} tokens
                        </span>
                        @if($selectedPackage && $selectedPackage->id === $package->id)
                            <svg class="w-5 h-5 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        @if($selectedPackage)
            <div class="mt-6 flex justify-end">
                <button
                    wire:click="subscribe"
                    class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg transition-colors duration-200">
                    Subscribe to {{ $selectedPackage->name }} Package
                </button>
            </div>
        @endif

        @if(session()->has('success'))
            <div class="mt-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                {{ session('success') }}
            </div>
        @endif

        @if(session()->has('error'))
            <div class="mt-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                {{ session('error') }}
            </div>
        @endif
    </div>
</div>
