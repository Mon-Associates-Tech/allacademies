@props(['variant' => 'minimal'])

@if(auth()->check() && !$has_token_subscription ?? false)
    <div class="absolute inset-0 z-40" 
         x-data="{ init() { document.body.style.overflow = 'hidden'; }, destroy() { document.body.style.overflow = 'auto'; } }" 
         x-init="init()"
         x-show="true">
        <!-- Overlay Background - absolute to cover slot area only -->
        <div class="absolute inset-0 bg-black/70 dark:bg-black/80 z-40 pointer-events-auto" role="presentation"></div>
        
        @if($variant === 'full-page')
            <!-- Full Page Variant -->
            <div class="absolute inset-0 flex items-center justify-center z-50 p-4 pointer-events-auto" role="alert">
                <div class="max-w-md w-full bg-white dark:bg-slate-800 rounded-lg shadow-2xl p-8">
                    <div class="flex justify-center mb-6">
                        <div class="flex-shrink-0">
                            <svg class="h-12 w-12 text-amber-400 dark:text-amber-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                            </svg>
                        </div>
                    </div>
                    <h2 class="text-center text-xl font-bold text-slate-900 dark:text-white mb-2">
                        Messenger Subscription Required
                    </h2>
                    <p class="text-center text-slate-600 dark:text-slate-400 mb-6">
                        You need an active messenger subscription to access this feature. Please subscribe to continue using this service.
                    </p>
                    <a href="{{ route('token-subscriptions.create') }}" class="w-full inline-flex items-center justify-center px-4 py-3 border border-transparent text-base leading-6 font-medium rounded-md shadow-sm text-white bg-amber-600 hover:bg-amber-700 dark:bg-amber-500 dark:hover:bg-amber-600 focus:outline-none focus:ring-2 focus:ring-offset-2 dark:focus:ring-offset-slate-800 focus:ring-amber-500 transition-colors">
                        <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Subscribe Now
                    </a>
                </div>
            </div>
        @else
            <!-- Minimal Variant (Default) -->
            <div class="fixed top-0 left-0 right-0 z-50 bg-amber-50  ml-auto dark:bg-amber-950 border-b-4 border-amber-400 dark:border-amber-500 p-4 shadow-lg pointer-events-auto" role="alert">
                <div class="flex items-start max-w-7xl mx-auto">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-amber-400 dark:text-amber-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3 flex-1">
                        <p class="text-sm font-medium text-amber-800 dark:text-amber-200">
                            Messenger Subscription Required
                        </p>
                        <p class="mt-2 text-sm text-amber-700 dark:text-amber-300">
                            You need an active messenger subscription to access this feature. Please subscribe to continue using this service.
                        </p>
                    </div>
                    <div class="mt-4 ml-4">
                        <a href="{{ route('token-subscriptions.create') }}" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md shadow-sm text-white bg-amber-600 hover:bg-amber-700 dark:bg-amber-500 dark:hover:bg-amber-600 focus:outline-none focus:ring-2 focus:ring-offset-2 dark:focus:ring-offset-amber-950 focus:ring-amber-500 transition-colors whitespace-nowrap">
                            <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            Subscribe Now
                        </a>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endif
