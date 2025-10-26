<div>
    @if($showBanner)
        <div class="relative bg-gradient-to-br {{ $isExpired ? 'from-red-50 to-orange-50 border-red-200' : 'from-amber-50 to-yellow-50 border-amber-200' }} border-2 rounded-xl shadow-lg overflow-hidden mb-6"
             x-data="{ show: true }"
             x-show="show"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 transform -translate-y-2"
             x-transition:enter-end="opacity-100 transform translate-y-0">

            <!-- Animated background pattern -->
            <div class="absolute inset-0 opacity-5">
                <div class="absolute inset-0" style="background-image: radial-gradient(circle at 2px 2px, currentColor 1px, transparent 0); background-size: 24px 24px;"></div>
            </div>

            <!-- Floating decorative elements -->
            @if(!$isExpired)
                <div class="absolute top-0 right-0 w-32 h-32 bg-amber-300/20 rounded-full blur-3xl animate-pulse"></div>
                <div class="absolute bottom-0 left-0 w-40 h-40 bg-yellow-300/20 rounded-full blur-3xl animate-pulse" style="animation-delay: 1s;"></div>
            @endif

            <div class="relative px-4 py-4 sm:px-6 sm:py-5">

                <!-- Top Section: Main message and CTA -->
                <div class="flex items-start gap-3 mb-4">
                    <!-- Icon -->
                    <div class="flex-shrink-0">
                        <div class="flex items-center justify-center w-12 h-12 rounded-xl {{ $isExpired ? 'bg-red-100' : 'bg-amber-100' }} shadow-sm">
                            @if($isExpired)
                                <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                </svg>
                            @else
                                <svg class="w-6 h-6 text-amber-600 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            @endif
                        </div>
                    </div>

                    <!-- Message content -->
                    <div class="flex-1 min-w-0">
                        <h3 class="text-lg font-bold {{ $isExpired ? 'text-red-900' : 'text-amber-900' }} mb-1">
                            @if($isExpired)
                                🎯 Your Free Trial Has Ended
                            @else
                                ⏰ Free Trial Expires {{ $daysRemaining === 0 ? 'Today' : "in {$daysRemaining} " . Str::plural('day', $daysRemaining) }}!
                            @endif
                        </h3>
                        <p class="text-sm {{ $isExpired ? 'text-red-700' : 'text-amber-700' }} mb-2">
                            @if($isExpired)
                                Continue enjoying AI-powered learning features with a subscription.
                            @else
                                You still have <strong>{{ number_format($tokensRemaining) }} tokens</strong> left. Don't lose access to premium features!
                            @endif
                        </p>

                        <!-- Token progress bar (compact) -->
                        @if(!$isExpired && $tokensUsed > 0)
                            <div class="mb-3">
                                <div class="flex items-center justify-between text-xs {{ $isExpired ? 'text-red-700' : 'text-amber-700' }} mb-1">
                                    <span class="font-medium">Token Usage</span>
                                    <span class="font-semibold">{{ round(($tokensUsed / ($tokensUsed + $tokensRemaining)) * 100) }}% used</span>
                                </div>
                                <div class="h-2 bg-amber-200 rounded-full overflow-hidden">
                                    <div class="h-full bg-gradient-to-r from-amber-500 to-amber-600 rounded-full transition-all duration-500"
                                         style="width: {{ ($tokensUsed / ($tokensUsed + $tokensRemaining)) * 100 }}%"></div>
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- CTA and Dismiss buttons -->
                    <div class="flex flex-col gap-2 flex-shrink-0">
                        <a href="{{ route('token-subscriptions.create') }}"
                           class="inline-flex items-center justify-center px-4 py-2.5 text-sm font-bold text-white {{ $isExpired ? 'bg-red-600 hover:bg-red-700' : 'bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-600 hover:to-orange-600' }} rounded-lg transition-all duration-200 shadow-md hover:shadow-lg hover:scale-105 group whitespace-nowrap">
                            @if($isExpired)
                                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"></path>
                                </svg>
                                Subscribe Now
                            @else
                                <svg class="w-4 h-4 mr-2 group-hover:animate-bounce" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                </svg>
                                Upgrade Now
                            @endif
                            <svg class="w-4 h-4 ml-1 group-hover:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                            </svg>
                        </a>

                        <button wire:click="dismissBanner"
                                @click="show = false"
                                class="inline-flex items-center justify-center px-3 py-1.5 text-xs font-medium {{ $isExpired ? 'text-red-600 hover:text-red-700 hover:bg-red-100' : 'text-amber-600 hover:text-amber-700 hover:bg-amber-100' }} rounded-lg transition-colors">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                            Dismiss
                        </button>
                    </div>
                </div>

                <!-- Bottom Section: Features showcase -->
                <div class="pt-3 border-t {{ $isExpired ? 'border-red-200' : 'border-amber-200' }}">
                    <p class="text-xs font-semibold {{ $isExpired ? 'text-red-800' : 'text-amber-800' }} mb-3 uppercase tracking-wide flex items-center">
                        @if($isExpired)
                            <svg class="w-4 h-4 mr-1.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"></path>
                            </svg>
                            Features You're Missing:
                        @else
                            <svg class="w-4 h-4 mr-1.5" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                            </svg>
                            ✨ Still Available on Your Trial:
                        @endif
                    </p>

                    <!-- Features Grid -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                        @foreach($features as $feature)
                            @if($isExpired)
                                <!-- Expired state: Locked features -->
                                <div class="relative bg-white/40 backdrop-blur-sm rounded-lg p-3 border border-red-200/50 opacity-75">
                                    <div class="flex items-center gap-3">
                                        <div class="flex-shrink-0 w-10 h-10 rounded-lg bg-gray-300 text-gray-500 flex items-center justify-center">
                                            {!! $feature['icon'] !!}
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <h4 class="text-sm font-semibold text-gray-700 truncate">
                                                {{ $feature['title'] }}
                                            </h4>
                                            <p class="text-xs text-gray-500 truncate">
                                                {{ $feature['description'] }}
                                            </p>
                                        </div>
                                    </div>
                                    <!-- Lock overlay -->
                                    <div class="absolute top-2 right-2">
                                        <svg class="w-5 h-5 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"></path>
                                        </svg>
                                    </div>
                                </div>
                            @else
                                <!-- Active trial: Clickable features -->
                                <a href="{{ $feature['link'] }}"
                                   class="group relative bg-white/70 backdrop-blur-sm rounded-lg p-3 hover:bg-white hover:shadow-md transition-all duration-200 border border-amber-200/50 hover:border-amber-300">
                                    <div class="flex items-center gap-3">
                                        <div class="flex-shrink-0 w-10 h-10 rounded-lg bg-gradient-to-br {{ $feature['gradient'] }} text-white flex items-center justify-center group-hover:scale-110 transition-transform shadow-sm">
                                            {!! $feature['icon'] !!}
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <h4 class="text-sm font-semibold text-gray-900 truncate group-hover:text-amber-700 transition-colors">
                                                {{ $feature['title'] }}
                                            </h4>
                                            <p class="text-xs text-gray-600 truncate">
                                                {{ $feature['description'] }}
                                            </p>
                                        </div>
                                        <svg class="w-4 h-4 text-amber-500 opacity-0 group-hover:opacity-100 transition-opacity flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                        </svg>
                                    </div>
                                    <!-- Hover effect border -->
                                    <div class="absolute inset-0 rounded-lg border-2 border-transparent group-hover:border-amber-400/40 transition-colors pointer-events-none"></div>
                                </a>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
