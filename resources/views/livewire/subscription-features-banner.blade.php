<div>
    @if($showBanner)
        <div
            class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-indigo-600 via-purple-600 to-pink-600 shadow-2xl"
            x-data="{ currentSlide: 0, autoplay: true }"
            x-init="
                 setInterval(() => {
                     if (autoplay) {
                         currentSlide = (currentSlide + 1) % {{ count($features) }}
                     }
                 }, 5000)
             ">

            <!-- Background Pattern -->
            <div class="absolute inset-0 opacity-10">
                <div class="absolute inset-0"
                     style="background-image: radial-gradient(circle at 2px 2px, white 1px, transparent 0); background-size: 32px 32px;"></div>
            </div>

            <!-- Floating Elements -->
            <div class="absolute inset-0 overflow-hidden pointer-events-none">
                <div class="absolute -top-4 -right-4 w-40 h-40 bg-white/10 rounded-full blur-3xl animate-pulse"></div>
                <div class="absolute -bottom-8 -left-8 w-64 h-64 bg-purple-300/10 rounded-full blur-3xl animate-pulse"
                     style="animation-delay: 1s;"></div>
                <div class="absolute top-1/2 left-1/2 w-32 h-32 bg-pink-300/10 rounded-full blur-2xl animate-pulse"
                     style="animation-delay: 2s;"></div>
            </div>

            <div class="relative px-6 py-8 sm:px-8 sm:py-10 lg:px-12 lg:py-12">
                <!-- Close Button -->
                @if($placement === 'dashboard')
                    <button wire:click="dismissBanner"
                            class="absolute top-4 right-4 text-white/70 hover:text-white transition-colors z-10">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                @endif

                <div class="max-w-7xl mx-auto">
                    <!-- Header -->
                    <div class="text-center mb-8 lg:mb-12">
                        <div
                            class="inline-flex items-center justify-center space-x-2 bg-white/20 backdrop-blur-sm rounded-full px-4 py-2 mb-4">
                            <svg class="w-5 h-5 text-yellow-300" fill="currentColor" viewBox="0 0 20 20">
                                <path
                                    d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                            </svg>
                            <span class="text-sm font-semibold text-white">Premium Features</span>
                        </div>

                        <h2 class="text-3xl sm:text-4xl lg:text-5xl font-bold text-white mb-3">
                            Supercharge Your Learning
                        </h2>
                        <p class="text-lg sm:text-xl text-white/90 max-w-2xl mx-auto">
                            Unlock AI-powered tools, unlimited resources, and personalized learning experiences
                        </p>
                    </div>

                    <!-- Features Grid -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 lg:gap-6 mb-8">
                        @foreach($features as $index => $feature)
                            <div
                                class="group relative bg-white/10 backdrop-blur-md rounded-xl p-6 hover:bg-white/20 transition-all duration-300 hover:scale-105 hover:shadow-xl"
                                x-data="{ hovered: false }"
                                @mouseenter="hovered = true; autoplay = false"
                                @mouseleave="hovered = false; autoplay = true">

                                <!-- Icon -->
                                <div
                                    class="inline-flex items-center justify-center w-12 h-12 rounded-lg bg-gradient-to-br {{ $feature['gradient'] }} text-white mb-4 group-hover:scale-110 transition-transform">
                                    {!! $feature['icon'] !!}
                                </div>

                                <!-- Content -->
                                <h3 class="text-lg font-bold text-white mb-2">
                                    {{ $feature['title'] }}
                                </h3>
                                <p class="text-sm text-white/80 leading-relaxed">
                                    {{ $feature['description'] }}
                                </p>

                                <!-- Hover Effect Border -->
                                <div
                                    class="absolute inset-0 rounded-xl border-2 border-white/0 group-hover:border-white/30 transition-colors pointer-events-none"></div>
                            </div>
                        @endforeach
                    </div>

                    <!-- CTA Section -->
                    <div class="flex flex-col sm:flex-row items-center justify-center gap-4 pt-4">
                        <a href="{{ route('token-subscriptions.create') }}"
                           class="inline-flex items-center justify-center px-8 py-4 bg-white text-purple-600 font-bold rounded-xl hover:bg-gray-100 transition-all duration-300 shadow-xl hover:shadow-2xl hover:scale-105 group">
                            <span>Get Started Now</span>
                            <svg class="w-5 h-5 ml-2 group-hover:translate-x-1 transition-transform" fill="none"
                                 stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                            </svg>
                        </a>

                        @auth
                            @if(auth()->user()->activeSubscriptionCycle)
                                <a href="{{ route('token-subscriptions.show', auth()->user()->activeSubscriptionCycle) }}"
                                   class="inline-flex items-center justify-center px-8 py-4 bg-white/10 backdrop-blur-sm text-white font-semibold rounded-xl hover:bg-white/20 transition-all duration-300 border-2 border-white/30">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <span>View My Subscription</span>
                                </a>
                            @else
                                <a href="{{ route('token-subscriptions.index') }}"
                                   class="inline-flex items-center justify-center px-8 py-4 bg-white/10 backdrop-blur-sm text-white font-semibold rounded-xl hover:bg-white/20 transition-all duration-300 border-2 border-white/30">
                                    <span>See All Plans</span>
                                </a>
                            @endif
                        @endauth
                    </div>

                    <!-- Trust Indicators -->
                    <div class="flex flex-wrap items-center justify-center gap-6 mt-8 pt-8 border-t border-white/20">
                        <div class="flex items-center text-white/80">
                            <svg class="w-5 h-5 mr-2 text-green-300" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                      d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                      clip-rule="evenodd"></path>
                            </svg>
                            <span class="text-sm font-medium">7-Day Free Trial</span>
                        </div>
                        <div class="flex items-center text-white/80">
                            <svg class="w-5 h-5 mr-2 text-green-300" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                      d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                      clip-rule="evenodd"></path>
                            </svg>
                            <span class="text-sm font-medium">Cancel Anytime</span>
                        </div>
                        <div class="flex items-center text-white/80">
                            <svg class="w-5 h-5 mr-2 text-green-300" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                      d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                      clip-rule="evenodd"></path>
                            </svg>
                            <span class="text-sm font-medium">24/7 Support</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
