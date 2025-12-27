<div>
    @if($isVisible)
        <div
            x-data="{ show: true }"
            x-show="show"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 transform -translate-y-2"
            x-transition:enter-end="opacity-100 transform translate-y-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 transform translate-y-0"
            x-transition:leave-end="opacity-0 transform -translate-y-2"
            class="mb-6 rounded-xl border
            @if($variant === 'warning') bg-yellow-50 dark:bg-yellow-900/20 border-yellow-200 dark:border-yellow-800
             @elseif($variant === 'notice') bg-amber-50 dark:bg-amber-900/20 border-amber-200 dark:border-amber-800
             @elseif($variant === 'success') bg-green-50 dark:bg-green-900/20 border-green-200 dark:border-green-800
             @elseif($variant === 'error') bg-red-50 dark:bg-red-900/20 border-red-200 dark:border-red-800
              @else bg-blue-50 dark:bg-blue-900/20 border-blue-200 dark:border-blue-800
               @endif shadow-sm"
        >
            <div class="p-4">
                <div class="flex items-start">
                    <!-- Icon -->
                    @if($showIcon)
                        <div class="flex-shrink-0">
                            @if($variant === 'warning')
                                <svg class="w-6 h-6 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                </svg>
                            @elseif($variant === 'notice')
                                <svg class="w-6 h-6 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            @else
                                <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            @endif
                        </div>
                    @endif

                    <!-- Content -->
                    <div class="@if($showIcon) ml-3 @endif flex-1">
                        <h3 class="text-sm font-semibold @if($variant === 'warning') text-yellow-900 dark:text-yellow-200 @elseif($variant === 'notice') text-amber-900 dark:text-amber-200 @else text-blue-900 dark:text-blue-200 @endif">
                            {{ $heading }}
                        </h3>
                        <div class="mt-1 text-sm @if($variant === 'warning') text-yellow-800 dark:text-yellow-300 @elseif($variant === 'notice') text-amber-800 dark:text-amber-300 @else text-blue-800 dark:text-blue-300 @endif">
                            <p>{{ $message }}</p>
                        </div>

                        <!-- Action Button -->
                        @if($actionText && $actionUrl)
                            <div class="mt-3">
                                <a href="{{ $actionUrl }}"
                                   class="inline-flex items-center text-sm font-medium @if($variant === 'warning') text-yellow-700 hover:text-yellow-800 dark:text-yellow-400 dark:hover:text-yellow-300 @elseif($variant === 'notice') text-amber-700 hover:text-amber-800 dark:text-amber-400 dark:hover:text-amber-300 @else text-blue-700 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 @endif">
                                    {{ $actionText }}
                                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                    </svg>
                                </a>
                            </div>
                        @endif
                    </div>

                    <!-- Dismiss Button -->
                    @if($dismissible)
                        <div class="ml-3 flex-shrink-0">
                            <button
                                wire:click="dismiss"
                                @click="show = false"
                                type="button"
                                class="inline-flex rounded-md p-1.5 @if($variant === 'warning') text-yellow-500 hover:bg-yellow-100 dark:hover:bg-yellow-900/50 @elseif($variant === 'notice') text-amber-500 hover:bg-amber-100 dark:hover:bg-amber-900/50 @else text-blue-500 hover:bg-blue-100 dark:hover:bg-blue-900/50 @endif focus:outline-none focus:ring-2 focus:ring-offset-2 @if($variant === 'warning') focus:ring-yellow-500 @elseif($variant === 'notice') focus:ring-amber-500 @else focus:ring-blue-500 @endif transition-colors"
                            >
                                <span class="sr-only">Dismiss</span>
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif
</div>
