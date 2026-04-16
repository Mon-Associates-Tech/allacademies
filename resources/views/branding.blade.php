<x-app>
    <div class="bg-white dark:bg-gray-900 transition-colors duration-300 overflow-x-hidden">
        <!-- Navigation -->
        @include('branding.partials.header')

        <!-- Hero Section -->
        <section id="home" class="relative min-h-screen flex items-center justify-center overflow-hidden">
            <div class="absolute inset-0 z-0">
                <video autoplay muted loop class="w-full h-full object-cover">
                    <source src="{{ asset('media/video/header-background-video.mp4') }}" type="video/mp4">
                </video>
                <div
                    class="absolute inset-0 bg-gradient-to-r from-blue-600/60 via-indigo-600/50 to-purple-600/60"></div>
            </div>

            <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 sm:py-32 text-center">
                <h1 class="text-3xl sm:text-4xl md:text-5xl lg:text-7xl font-extrabold sm:font-black text-white mb-4 sm:mb-6 leading-tight"
                    data-aos="fade-down" data-aos-delay="100">
                    Join <span class="text-yellow-300">{{ number_format($usersCount ?? 0) }}</span> Individuals<br>
                    <span
                        class="text-transparent bg-clip-text bg-gradient-to-r from-blue-200 via-purple-200 to-pink-500">Using All Academies</span>
                </h1>

                <p class="text-base sm:text-lg md:text-xl lg:text-2xl text-gray-100 mb-8 sm:mb-12 max-w-3xl mx-auto leading-relaxed"
                   data-aos="fade-up" data-aos-delay="300">
                    Access comprehensive digital resources, expert content, and cutting-edge learning tools
                </p>

                <div class="flex flex-col sm:flex-row gap-3 sm:gap-4 justify-center mb-6 sm:mb-8" data-aos="zoom-in"
                     data-aos-delay="500">
                    <a href="{{ route('register') }}"
                       class="px-6 py-3 sm:px-10 sm:py-5 bg-white text-blue-600 font-bold text-base sm:text-lg rounded-xl sm:rounded-2xl hover:shadow-2xl transform hover:scale-105 transition-all duration-300">
                        Start Learning Today
                    </a>
                    <a href="#features"
                       class="px-6 py-3 sm:px-10 sm:py-5 border-2 border-white text-white font-bold text-base sm:text-lg rounded-xl sm:rounded-2xl hover:bg-white/10 backdrop-blur-sm transition-all duration-300">
                        Explore Features
                    </a>
                </div>

                <div class="flex items-center justify-center space-x-2" data-aos="fade-up" data-aos-delay="700">
                    <div class="flex">
                        @for($i = 0; $i < 5; $i++)
                            <svg class="w-4 h-4 sm:w-6 sm:h-6 text-yellow-400 fill-current" viewBox="0 0 20 20">
                                <path
                                    d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/>
                            </svg>
                        @endfor
                    </div>
                    <span
                        class="text-white font-semibold text-sm sm:text-base lg:text-lg ml-2 sm:ml-3">4.9/5 Rating</span>
                </div>
            </div>
        </section>

        <!-- Video Demo Section -->
        <section id="demo"
                 class="relative py-20 bg-gradient-to-br from-gray-900 via-blue-900 to-indigo-900 overflow-hidden">
            <!-- Animated Background Pattern -->
            <div class="absolute inset-0 opacity-10">
                <div class="absolute inset-0"
                     style="background-image: radial-gradient(circle at 2px 2px, white 1px, transparent 0); background-size: 40px 40px;"></div>
            </div>

            <!-- Gradient Orbs -->
            <div
                class="absolute top-0 left-1/4 w-96 h-96 bg-blue-500 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-pulse"></div>
            <div
                class="absolute bottom-0 right-1/4 w-96 h-96 bg-indigo-500 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-pulse"
                style="animation-delay: 2s;"></div>

            <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Section Header -->
                <div class="text-center mb-12">
                    <div
                        class="inline-flex items-center px-6 py-3 rounded-full bg-white/10 backdrop-blur-md border border-white/20 text-white font-semibold text-sm mb-6"
                        data-aos="fade-down">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Platform Demo
                    </div>
                    <h2 class="text-4xl md:text-5xl font-extrabold text-white mb-6" data-aos="fade-up"
                        data-aos-delay="100">
                        Experience All Academies
                        <span
                            class="block mt-2 text-transparent bg-clip-text bg-gradient-to-r from-yellow-400 to-orange-400">In Action</span>
                    </h2>
                    <p class="max-w-3xl text-lg text-gray-200 mx-auto leading-relaxed" data-aos="fade-up"
                       data-aos-delay="200">
                        Watch how our platform transforms school management, enhances learning, and empowers educators
                        across Ghana.
                    </p>
                </div>

                <!-- Video Container -->
                <div class="relative max-w-5xl mx-auto" data-aos="zoom-in" data-aos-delay="300">
                    <!-- Main Video Card -->
                    <div class="relative group">
                        <!-- Glow Effect -->
                        <div
                            class="absolute -inset-1 bg-gradient-to-r from-blue-600 via-indigo-600 to-purple-600 rounded-2xl blur-xl opacity-75 group-hover:opacity-100 transition duration-1000"></div>

                        <!-- Video Wrapper -->
                        <div class="relative bg-gray-900 rounded-2xl overflow-hidden shadow-2xl">
                            <!-- Video Player -->
                            <div class="relative aspect-video bg-gradient-to-br from-gray-800 to-gray-900">
                                <video
                                    id="demo-video"
                                    class="w-full h-full object-cover"
                                    controls
                                    preload="metadata"
                                    poster="{{ asset('images/students-crowded-around-computer.jpeg') }}">
                                    <source src="{{ asset('media/video/platform-demo.mp4') }}" type="video/mp4">
                                    Your browser does not support the video tag.
                                </video>

                                <!-- Custom Play Button Overlay -->
                                <div id="play-overlay"
                                     class="absolute inset-0 flex items-center justify-center bg-black/40 backdrop-blur-sm cursor-pointer transition-opacity duration-300"
                                     onclick="document.getElementById('demo-video').play(); this.style.opacity='0'; setTimeout(() => this.style.display='none', 300);">
                                    <div class="relative">
                                        <div
                                            class="absolute inset-0 bg-blue-600 rounded-full blur-xl opacity-50 animate-pulse"></div>
                                        <div
                                            class="relative w-24 h-24 bg-gradient-to-r from-blue-600 to-indigo-600 rounded-full flex items-center justify-center shadow-2xl transform hover:scale-110 transition-transform duration-300">
                                            <svg class="w-10 h-10 text-white ml-1" fill="currentColor"
                                                 viewBox="0 0 20 20">
                                                <path
                                                    d="M6.3 2.841A1.5 1.5 0 004 4.11V15.89a1.5 1.5 0 002.3 1.269l9.344-5.89a1.5 1.5 0 000-2.538L6.3 2.84z"/>
                                            </svg>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Feature Highlights Below Video -->
                <div class="mt-12 grid grid-cols-1 md:grid-cols-3 gap-6" data-aos="fade-up" data-aos-delay="400">
                    <!-- Highlight 1 -->
                    <div
                        class="bg-white/5 backdrop-blur-md border border-white/10 rounded-2xl p-6 hover:bg-white/10 transition-all duration-300">
                        <div
                            class="w-12 h-12 bg-gradient-to-r from-blue-600 to-indigo-600 rounded-xl flex items-center justify-center mb-4">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-white mb-2">Quick Setup</h3>
                        <p class="text-gray-300 text-sm">Get your school up and running in less than 10 minutes with our
                            intuitive onboarding process.</p>
                    </div>

                    <!-- Highlight 2 -->
                    <div
                        class="bg-white/5 backdrop-blur-md border border-white/10 rounded-2xl p-6 hover:bg-white/10 transition-all duration-300">
                        <div
                            class="w-12 h-12 bg-gradient-to-r from-blue-600 to-indigo-600 rounded-xl flex items-center justify-center mb-4">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-white mb-2">Fully Customizable</h3>
                        <p class="text-gray-300 text-sm">Tailor every aspect of the platform to match your school's
                            unique needs and branding.</p>
                    </div>

                    <!-- Highlight 3 -->
                    <div
                        class="bg-white/5 backdrop-blur-md border border-white/10 rounded-2xl p-6 hover:bg-white/10 transition-all duration-300">
                        <div
                            class="w-12 h-12 bg-gradient-to-r from-blue-600 to-indigo-600 rounded-xl flex items-center justify-center mb-4">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192L5.636 18.364M12 2.25a9.75 9.75 0 11-9.75 9.75A9.75 9.75 0 0112 2.25z"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-white mb-2">24/7 Support</h3>
                        <p class="text-gray-300 text-sm">Our dedicated support team is always available to help you
                            succeed with the platform.</p>
                    </div>
                </div>

                <!-- CTA Button -->
                <div class="mt-10 text-center" data-aos="fade-up" data-aos-delay="500">
                    <a href="{{ route('register') }}"
                       class="inline-flex items-center px-8 py-4 bg-gradient-to-r from-yellow-400 to-orange-400 text-gray-900 font-bold text-lg rounded-xl hover:shadow-2xl transform hover:scale-105 transition-all duration-300">
                        Start Your Free Trial
                        <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                        </svg>
                    </a>
                    <p class="mt-4 text-sm text-gray-300">No credit card required • 30-day free trial • Cancel
                        anytime</p>
                </div>
            </div>
        </section>


        <!-- Public Payment Section -->
        <section class="">
            @include('branding.partials.donation-support')
        </section>

        <!-- Features Section -->
        <section id="features" class="py-20 bg-white dark:bg-gray-900 transition-colors duration-300">
            <div class="max-w-7xl mx-auto">
                <div class="text-center mb-20 px-4">
                    <div
                        class="inline-flex items-center px-4 py-2 rounded-full bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-200 font-semibold text-sm mb-4"
                        data-aos="fade-down">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                        Platform Features
                    </div>
                    <h2 class="text-3xl md:text-4xl font-extrabold text-gray-900 dark:text-white mb-6"
                        data-aos="fade-up" data-aos-delay="100">
                        Everything Your School Needs to <span
                            class="text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-indigo-600">Succeed</span>
                    </h2>
                    <p class="max-w-3xl text-base text-gray-600 dark:text-gray-300 mx-auto leading-relaxed"
                       data-aos="fade-up" data-aos-delay="200">
                        From student enrollment to graduation, manage every aspect of your educational institution with
                        our integrated platform.
                    </p>
                </div>

                <div class="space-y-24">
                    <!-- Feature 1: Comprehensive Library -->
                    <div class="flex flex-col lg:flex-row items-center gap-12 px-4 sm:px-6 lg:px-8">
                        <div class="lg:w-1/2 w-full" data-aos="fade-right" data-aos-delay="100">
                            <div class="relative group">
                                <div
                                    class="absolute -inset-2 sm:-inset-4 bg-gradient-to-r from-blue-500 to-green-500 rounded-2xl sm:rounded-3xl blur-lg opacity-30 group-hover:opacity-50 transition-opacity duration-500"></div>
                                <div class="relative">
                                    <img
                                        src="https://images.unsplash.com/photo-1481627834876-b7833e8f5570?w=600&h=400&fit=crop&crop=center"
                                        alt="Vast Digital Library with thousands of books"
                                        class="rounded-xl sm:rounded-2xl shadow-2xl w-full h-48 sm:h-64 lg:h-80 object-cover transform group-hover:scale-105 transition-transform duration-700">
                                    <div
                                        class="absolute inset-0 bg-gradient-to-tr from-blue-600/20 to-green-600/10 rounded-2xl"></div>

                                    <!-- Floating Elements -->
                                    <div
                                        class="absolute hidden sm:block top-4 left-4 bg-white/95 dark:bg-gray-800/95 backdrop-blur-sm rounded-lg px-3 py-2 shadow-lg">
                                        <div class="flexd hidden items-center space-x-2">
                                            <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
                                            <span class="text-sm font-semibold text-gray-900 dark:text-gray-100">15,000+ Books</span>
                                        </div>
                                    </div>

                                    <div
                                        class="absolute hidden sm:block bottom-4 right-4 bg-white/95 dark:bg-gray-800/95 backdrop-blur-sm rounded-lg px-3 py-2 shadow-lg">
                                        <span
                                            class="text-sm font-semibold text-gray-900 dark:text-gray-100">Updated Daily</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="lg:w-1/2 space-y-6 w-full" data-aos="fade-left" data-aos-delay="200">
                            <div class="flex items-center space-x-4">
                                <div
                                    class="flex items-center justify-center h-16 w-16 rounded-2xl bg-gradient-to-r from-blue-600 to-indigo-600 text-white shadow-lg flex-shrink-0">
                                    <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white">Comprehensive Digital
                                        Library</h3>
                                    <p class="text-base text-blue-600 dark:text-blue-400 font-semibold">Access
                                        Everything, Anytime</p>
                                </div>
                            </div>

                            <p class="text-base text-gray-600 dark:text-gray-300 leading-relaxed">
                                Dive into our ever-expanding collection of over 15,000 carefully curated educational
                                resources spanning multiple disciplines. From foundational textbooks to cutting-edge
                                research papers, discover everything you need in one unified platform.
                            </p>

                            <div class="grid grid-cols-2 gap-4">
                                <div
                                    class="bg-gray-50 dark:bg-gray-800 rounded-xl p-4 border border-gray-200 dark:border-gray-700">
                                    <div class="flex items-center space-x-3">
                                        <svg class="h-5 w-5 text-green-500 flex-shrink-0" fill="none"
                                             stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M5 13l4 4L19 7"></path>
                                        </svg>
                                        <span class="text-base text-gray-700 dark:text-gray-300 font-medium">AI-Powered Search</span>
                                    </div>
                                </div>
                                <div
                                    class="bg-gray-50 dark:bg-gray-800 rounded-xl p-4 border border-gray-200 dark:border-gray-700">
                                    <div class="flex items-center space-x-3">
                                        <svg class="h-5 w-5 text-green-500 flex-shrink-0" fill="none"
                                             stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M5 13l4 4L19 7"></path>
                                        </svg>
                                        <span class="text-base text-gray-700 dark:text-gray-300 font-medium">Multiple Formats</span>
                                    </div>
                                </div>
                                <div
                                    class="bg-gray-50 dark:bg-gray-800 rounded-xl p-4 border border-gray-200 dark:border-gray-700">
                                    <div class="flex items-center space-x-3">
                                        <svg class="h-5 w-5 text-green-500 flex-shrink-0" fill="none"
                                             stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M5 13l4 4L19 7"></path>
                                        </svg>
                                        <span class="text-base text-gray-700 dark:text-gray-300 font-medium">Offline Access</span>
                                    </div>
                                </div>
                                <div
                                    class="bg-gray-50 dark:bg-gray-800 rounded-xl p-4 border border-gray-200 dark:border-gray-700">
                                    <div class="flex items-center space-x-3">
                                        <svg class="h-5 w-5 text-green-500 flex-shrink-0" fill="none"
                                             stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M5 13l4 4L19 7"></path>
                                        </svg>
                                        <span class="text-base text-gray-700 dark:text-gray-300 font-medium">Daily Updates</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Feature 2: Expert Authors -->
                    <div class="flex flex-col lg:flex-row-reverse items-center gap-12 px-4 sm:px-6 lg:px-8">
                        <div class="lg:w-1/2 w-full" data-aos="fade-left" data-aos-delay="100">
                            <div class="relative group">
                                <div
                                    class="absolute -inset-2 sm:-inset-4 bg-gradient-to-r from-green-500 to-blue-500 rounded-2xl sm:rounded-3xl blur-lg opacity-30 group-hover:opacity-50 transition-opacity duration-500"></div>
                                <div class="relative">
                                    <img
                                        src="{{asset('images/professional-teacher.jpg')}}?w=full&h=400&fit=crop&crop=top"
                                        alt="Expert authors and educators"
                                        class="rounded-xl sm:rounded-2xl shadow-2xl w-full h-48 sm:h-64 lg:h-80 object-cover object-top transform group-hover:scale-105 transition-transform duration-700">
                                    <div
                                        class="absolute inset-0 bg-gradient-to-tl from-green-600/20 to-blue-600/10 rounded-2xl"></div>

                                    <!-- Author Cards Overlay -->
                                    <div class="absolute hidden sm:block top-4 right-4 space-y-2">
                                        <div
                                            class="bg-white/95 dark:bg-gray-800/95 backdrop-blur-sm rounded-lg px-3 py-2 shadow-lg">
                                            <div class="flex items-center space-x-2">
                                                <img class="w-6 h-6 rounded-full"
                                                     src="{{asset('images/professional-teacher.jpg')}}?w=24&h=24&fit=crop&crop=face"
                                                     alt="Expert">
                                                <span class="text-xs font-semibold text-gray-900 dark:text-gray-100">Dr. Smith - AI Expert</span>
                                            </div>
                                        </div>
                                        <div
                                            class="bg-white/95 dark:bg-gray-800/95 backdrop-blur-sm rounded-lg px-3 py-2 shadow-lg">
                                            <div class="flex items-center space-x-2">
                                                <img class="w-6 h-6 rounded-full"
                                                     src="{{asset('images/professional-teacher.jpg')}}?w=24&h=24&fit=crop&crop=face"
                                                     alt="Expert">
                                                <span class="text-xs font-semibold text-gray-900 dark:text-gray-100">Prof. Johnson - PhD</span>
                                            </div>
                                        </div>
                                    </div>

                                    <div
                                        class="absolute hidden sm:block bottom-4 left-4 bg-white/95 dark:bg-gray-800/95 backdrop-blur-sm rounded-lg px-3 py-2 shadow-lg">
                                        <span class="text-sm font-semibold text-gray-900 dark:text-gray-100">500+ Verified Experts</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="lg:w-1/2 space-y-6 w-full" data-aos="fade-right" data-aos-delay="200">
                            <div class="flex items-center space-x-4">
                                <div
                                    class="flex items-center justify-center h-16 w-16 rounded-2xl bg-gradient-to-r from-blue-600 to-indigo-600 text-white shadow-lg flex-shrink-0">
                                    <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white">Learn from Industry
                                        Leaders</h3>
                                    <p class="text-base text-blue-600 dark:text-blue-400 font-semibold">Verified
                                        Expertise</p>
                                </div>
                            </div>

                            <p class="text-base text-gray-600 dark:text-gray-300 leading-relaxed">
                                Connect with renowned academics, industry professionals, and thought leaders who
                                contribute high-quality, peer-reviewed content. Every author is verified and brings
                                real-world expertise to ensure you're learning from the best minds in each field.
                            </p>

                            <div class="space-y-4">
                                <div class="flex items-start space-x-4">
                                    <div class="flex-shrink-0">
                                        <div
                                            class="flex items-center justify-center h-10 w-10 rounded-lg bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400">
                                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                        </div>
                                    </div>
                                    <div>
                                        <h4 class="text-base font-semibold text-gray-900 dark:text-white">Verified
                                            Credentials</h4>
                                        <p class="text-sm text-gray-600 dark:text-gray-300">All authors undergo rigorous
                                            verification of their academic and professional credentials</p>
                                    </div>
                                </div>
                                <div class="flex items-start space-x-4">
                                    <div class="flex-shrink-0">
                                        <div
                                            class="flex items-center justify-center h-10 w-10 rounded-lg bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400">
                                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                            </svg>
                                        </div>
                                    </div>
                                    <div>
                                        <h4 class="text-base font-semibold text-gray-900 dark:text-white">Direct
                                            Communication</h4>
                                        <p class="text-sm text-gray-600 dark:text-gray-300">Engage directly with authors
                                            through Q&A sessions and discussion forums</p>
                                    </div>
                                </div>
                                <div class="flex items-start space-x-4">
                                    <div class="flex-shrink-0">
                                        <div
                                            class="flex items-center justify-center h-10 w-10 rounded-lg bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400">
                                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M4.871 4A17.926 17.926 0 003 12c0 2.874.673 5.59 1.871 8m14.13 0a17.926 17.926 0 001.87-8c0-2.874-.673-5.59-1.87-8M9 9h1.246a1 1 0 01.961.725l1.586 5.55a1 1 0 00.961.725H15m1-7h-.08a2 2 0 00-1.519.698L9.6 15.302A2 2 0 018.08 16H8"></path>
                                            </svg>
                                        </div>
                                    </div>
                                    <div>
                                        <h4 class="text-base font-semibold text-gray-900 dark:text-white">Latest
                                            Research</h4>
                                        <p class="text-sm text-gray-600 dark:text-gray-300">Content reflects the most
                                            current research and industry best practices</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Feature 3: Interactive Learning -->
                    <div class="flex flex-col lg:flex-row items-center gap-12 px-4 sm:px-6 lg:px-8">
                        <div class="lg:w-1/2 w-full" data-aos="fade-right" data-aos-delay="100">
                            <div class="relative group">
                                <div
                                    class="absolute -inset-2 sm:-inset-4 bg-gradient-to-r from-purple-500 to-pink-500 rounded-2xl sm:rounded-3xl blur-lg opacity-30 group-hover:opacity-50 transition-opacity duration-500"></div>
                                <div class="relative">
                                    <img
                                        src="{{asset('images/students-with-a-laptop.jpg')}}?w=600&h=400&fit=crop&crop=center"
                                        alt="Interactive learning platform with students collaborating"
                                        class="rounded-xl sm:rounded-2xl shadow-2xl w-full h-48 sm:h-64 lg:h-80 object-cover transform group-hover:scale-105 transition-transform duration-700">
                                    <div
                                        class="absolute inset-0 bg-gradient-to-br from-purple-600/20 to-pink-600/10 rounded-2xl"></div>

                                    <!-- Interactive Elements Overlay -->
                                    <div
                                        class="absolute hidden sm:block top-4 left-4 bg-white/95 dark:bg-gray-800/95 backdrop-blur-sm rounded-lg px-3 py-2 shadow-lg">
                                        <div class="flex items-center space-x-2">
                                            <div class="w-2 h-2 bg-purple-500 rounded-full animate-pulse"></div>
                                            <span class="text-sm font-semibold text-gray-900 dark:text-gray-100">Live Session</span>
                                        </div>
                                    </div>

                                    <div
                                        class="absolute hidden sm:block top-4 right-4 bg-white/95 dark:bg-gray-800/95 backdrop-blur-sm rounded-lg px-3 py-2 shadow-lg">
                                        <span
                                            class="text-sm font-semibold text-gray-900 dark:text-gray-100">24/7 Online</span>
                                    </div>

                                    <div
                                        class="absolute hidden sm:block bottom-4 left-4 bg-white/95 dark:bg-gray-800/95 backdrop-blur-sm rounded-lg px-3 py-2 shadow-lg">
                                        <span class="text-sm font-semibold text-gray-900 dark:text-gray-100">Interactive Tools</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="lg:w-1/2 space-y-6 w-full" data-aos="fade-left" data-aos-delay="200">
                            <div class="flex items-center space-x-4">
                                <div
                                    class="flex items-center justify-center h-16 w-16 rounded-2xl bg-gradient-to-r from-blue-600 to-indigo-600 text-white shadow-lg flex-shrink-0">
                                    <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white">Interactive Learning
                                        Experience</h3>
                                    <p class="text-base text-blue-600 dark:text-blue-400 font-semibold">Engage &
                                        Collaborate</p>
                                </div>
                            </div>

                            <p class="text-base text-gray-600 dark:text-gray-300 leading-relaxed">
                                Transform passive reading into active learning through interactive quizzes, multimedia
                                presentations, collaborative projects, and real-time discussions. Our platform adapts to
                                your learning style and pace.
                            </p>

                            <div class="grid grid-cols-2 gap-4">
                                <div
                                    class="bg-blue-50 dark:bg-blue-900/20 rounded-xl p-4 border border-blue-200 dark:border-blue-700">
                                    <div class="flex items-center space-x-3">
                                        <svg class="h-6 w-6 text-blue-600 flex-shrink-0" fill="none"
                                             stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                                        </svg>
                                        <div>
                                            <div class="text-base font-semibold text-gray-900 dark:text-gray-100">Smart
                                                Quizzes
                                            </div>
                                            <div class="text-sm text-gray-600 dark:text-gray-400">Adaptive assessment
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div
                                    class="bg-blue-50 dark:bg-blue-900/20 rounded-xl p-4 border border-blue-200 dark:border-blue-700">
                                    <div class="flex items-center space-x-3">
                                        <svg class="h-6 w-6 text-blue-600 flex-shrink-0" fill="none"
                                             stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                        </svg>
                                        <div>
                                            <div class="text-base font-semibold text-gray-900 dark:text-gray-100">Study
                                                Groups
                                            </div>
                                            <div class="text-sm text-gray-600 dark:text-gray-400">Collaborative
                                                learning
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div
                                    class="bg-blue-50 dark:bg-blue-900/20 rounded-xl p-4 border border-blue-200 dark:border-blue-700">
                                    <div class="flex items-center space-x-3">
                                        <svg class="h-6 w-6 text-blue-600 flex-shrink-0" fill="none"
                                             stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                        </svg>
                                        <div>
                                            <div class="text-base font-semibold text-gray-900 dark:text-gray-100">
                                                Progress Analytics
                                            </div>
                                            <div class="text-sm text-gray-600 dark:text-gray-400">Track your journey
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div
                                    class="bg-blue-50 dark:bg-blue-900/20 rounded-xl p-4 border border-blue-200 dark:border-blue-700">
                                    <div class="flex items-center space-x-3">
                                        <svg class="h-6 w-6 text-blue-600 flex-shrink-0" fill="none"
                                             stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                        </svg>
                                        <div>
                                            <div class="text-base font-semibold text-gray-900 dark:text-gray-100">Mobile
                                                Learning
                                            </div>
                                            <div class="text-sm text-gray-600 dark:text-gray-400">Learn anywhere</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Platform Modules Section -->
                    <section id="modules"
                             class="py-20 bg-gradient-to-br from-gray-50 to-blue-50 dark:from-gray-800 dark:to-gray-900 transition-colors duration-300">
                        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                            <div class="text-center mb-20 px-4">
                                <div
                                    class="inline-flex items-center px-4 py-2 rounded-full bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-200 font-semibold text-sm mb-4"
                                    data-aos="fade-down">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                    </svg>
                                    Platform Modules
                                </div>
                                <h2 class="text-3xl md:text-4xl font-extrabold text-gray-900 dark:text-white mb-6"
                                    data-aos="fade-up" data-aos-delay="100">
                                    Comprehensive Learning <span
                                        class="text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-indigo-600">Ecosystem</span>
                                </h2>
                                <p class="max-w-3xl text-base text-gray-600 dark:text-gray-300 mx-auto leading-relaxed"
                                   data-aos="fade-up" data-aos-delay="200">
                                    Discover our integrated modules designed to support every aspect of your educational
                                    journey, from administration to assessment and beyond.
                                </p>
                            </div>

                            <!-- Featured Modules -->
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 mb-16">
                                <!-- Administration Module -->
                                <div
                                    class="group bg-white dark:bg-gray-800 rounded-2xl shadow-xl hover:shadow-2xl transition-all duration-500 border border-gray-200 dark:border-gray-700 hover:border-blue-300 dark:hover:border-blue-600 p-8 transform hover:scale-105"
                                    data-aos="flip-left" data-aos-delay="100">
                                    <div
                                        class="flex items-center justify-center h-16 w-16 rounded-2xl bg-gradient-to-r from-blue-500 to-indigo-600 text-white mb-6 group-hover:scale-110 transition-transform duration-300 shadow-lg">
                                        <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                                        </svg>
                                    </div>
                                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-4">Administration</h3>
                                    <p class="text-base text-gray-600 dark:text-gray-300 mb-6 leading-relaxed">
                                        A smart dashboard for managing schools, teachers, students, and resources—all in
                                        one place. Track enrollments, assign roles, and monitor progress with ease.
                                    </p>
                                    <div class="space-y-3">
                                        <div class="flex items-center text-sm text-gray-600 dark:text-gray-300">
                                            <svg class="h-4 w-4 text-green-500 mr-3 flex-shrink-0" fill="none"
                                                 stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M5 13l4 4L19 7"></path>
                                            </svg>
                                            Smart Dashboard
                                        </div>
                                        <div class="flex items-center text-sm text-gray-600 dark:text-gray-300">
                                            <svg class="h-4 w-4 text-green-500 mr-3 flex-shrink-0" fill="none"
                                                 stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M5 13l4 4L19 7"></path>
                                            </svg>
                                            Role Management
                                        </div>
                                        <div class="flex items-center text-sm text-gray-600 dark:text-gray-300">
                                            <svg class="h-4 w-4 text-green-500 mr-3 flex-shrink-0" fill="none"
                                                 stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M5 13l4 4L19 7"></path>
                                            </svg>
                                            Progress Monitoring
                                        </div>
                                    </div>
                                </div>

                                <!-- Teaching Module -->
                                <div
                                    class="group bg-white dark:bg-gray-800 rounded-2xl sm:rounded-3xl shadow-xl hover:shadow-2xl transition-all duration-500 border border-gray-200 dark:border-gray-700 hover:border-green-300 dark:hover:border-green-600 p-6 sm:p-8 transform hover:scale-105"
                                    data-aos="flip-left" data-aos-delay="200">
                                    <div
                                        class="flex items-center justify-center h-14 w-14 sm:h-16 sm:w-16 lg:h-20 lg:w-20 rounded-2xl sm:rounded-3xl bg-gradient-to-r from-green-500 to-emerald-600 text-white mb-4 sm:mb-6 lg:mb-8 group-hover:scale-110 transition-transform duration-300 shadow-lg">
                                        <svg class="h-7 w-7 sm:h-8 sm:w-8 lg:h-10 lg:w-10" fill="none"
                                             stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                        </svg>
                                    </div>
                                    <h3 class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-white mb-3 sm:mb-4">
                                        Teaching</h3>
                                    <p class="text-sm sm:text-base text-gray-600 dark:text-gray-300 mb-4 sm:mb-6 leading-relaxed">
                                        Empower teachers to plan lessons, upload notes, record videos, and interact with
                                        students in
                                        real-time or asynchronously. Perfect for classroom or remote learning.
                                    </p>
                                    <div class="space-y-2 sm:space-y-3">
                                        <div
                                            class="flex items-center text-xs sm:text-sm text-gray-600 dark:text-gray-300">
                                            <svg class="h-3 w-3 sm:h-4 sm:w-4 text-green-500 mr-2 sm:mr-3 flex-shrink-0"
                                                 fill="none" stroke="currentColor"
                                                 viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M5 13l4 4L19 7"></path>
                                            </svg>
                                            Lesson Planning
                                        </div>
                                        <div
                                            class="flex items-center text-xs sm:text-sm text-gray-600 dark:text-gray-300">
                                            <svg class="h-3 w-3 sm:h-4 sm:w-4 text-green-500 mr-2 sm:mr-3 flex-shrink-0"
                                                 fill="none" stroke="currentColor"
                                                 viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M5 13l4 4L19 7"></path>
                                            </svg>
                                            Video Recording
                                        </div>
                                        <div
                                            class="flex items-center text-xs sm:text-sm text-gray-600 dark:text-gray-300">
                                            <svg class="h-3 w-3 sm:h-4 sm:w-4 text-green-500 mr-2 sm:mr-3 flex-shrink-0"
                                                 fill="none" stroke="currentColor"
                                                 viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M5 13l4 4L19 7"></path>
                                            </svg>
                                            Real-time Interaction
                                        </div>
                                    </div>
                                </div>

                                <!-- Quizzes Module -->
                                <div
                                    class="group bg-white dark:bg-gray-800 rounded-2xl sm:rounded-3xl shadow-xl hover:shadow-2xl transition-all duration-500 border border-gray-200 dark:border-gray-700 hover:border-purple-300 dark:hover:border-purple-600 p-6 sm:p-8 transform hover:scale-105"
                                    data-aos="flip-left" data-aos-delay="300">
                                    <div
                                        class="flex items-center justify-center h-14 w-14 sm:h-16 sm:w-16 lg:h-20 lg:w-20 rounded-2xl sm:rounded-3xl bg-gradient-to-r from-purple-500 to-violet-600 text-white mb-4 sm:mb-6 lg:mb-8 group-hover:scale-110 transition-transform duration-300 shadow-lg">
                                        <svg class="h-7 w-7 sm:h-8 sm:w-8 lg:h-10 lg:w-10" fill="none"
                                             stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                                        </svg>
                                    </div>
                                    <h3 class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-white mb-3 sm:mb-4">
                                        Quizzes</h3>
                                    <p class="text-sm sm:text-base text-gray-600 dark:text-gray-300 mb-4 sm:mb-6 leading-relaxed">
                                        Create and assign interactive quizzes automatically graded for instant feedback.
                                        Teachers
                                        can track scores and identify weak areas to support learners better.
                                    </p>
                                    <div class="space-y-2 sm:space-y-3">
                                        <div
                                            class="flex items-center text-xs sm:text-sm text-gray-600 dark:text-gray-300">
                                            <svg class="h-3 w-3 sm:h-4 sm:w-4 text-green-500 mr-2 sm:mr-3 flex-shrink-0"
                                                 fill="none" stroke="currentColor"
                                                 viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M5 13l4 4L19 7"></path>
                                            </svg>
                                            Auto-grading
                                        </div>
                                        <div
                                            class="flex items-center text-xs sm:text-sm text-gray-600 dark:text-gray-300">
                                            <svg class="h-3 w-3 sm:h-4 sm:w-4 text-green-500 mr-2 sm:mr-3 flex-shrink-0"
                                                 fill="none" stroke="currentColor"
                                                 viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M5 13l4 4L19 7"></path>
                                            </svg>
                                            Instant Feedback
                                        </div>
                                        <div
                                            class="flex items-center text-xs sm:text-sm text-gray-600 dark:text-gray-300">
                                            <svg class="h-3 w-3 sm:h-4 sm:w-4 text-green-500 mr-2 sm:mr-3 flex-shrink-0"
                                                 fill="none" stroke="currentColor"
                                                 viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M5 13l4 4L19 7"></path>
                                            </svg>
                                            Performance Tracking
                                        </div>
                                    </div>
                                </div>

                                <!-- Examinations Module -->
                                <div
                                    class="group bg-white dark:bg-gray-800 rounded-2xl sm:rounded-3xl shadow-xl hover:shadow-2xl transition-all duration-500 border border-gray-200 dark:border-gray-700 hover:border-orange-300 dark:hover:border-orange-600 p-6 sm:p-8 transform hover:scale-105"
                                    data-aos="flip-left" data-aos-delay="100">
                                    <div
                                        class="flex items-center justify-center h-14 w-14 sm:h-16 sm:w-16 lg:h-20 lg:w-20 rounded-2xl sm:rounded-3xl bg-gradient-to-r from-orange-500 to-red-600 text-white mb-4 sm:mb-6 lg:mb-8 group-hover:scale-110 transition-transform duration-300 shadow-lg">
                                        <svg class="h-7 w-7 sm:h-8 sm:w-8 lg:h-10 lg:w-10" fill="none"
                                             stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                    </div>
                                    <h3 class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-white mb-3 sm:mb-4">
                                        Examinations</h3>
                                    <p class="text-sm sm:text-base text-gray-600 dark:text-gray-300 mb-4 sm:mb-6 leading-relaxed">
                                        Securely set and manage exams with multiple formats—MCQs, essays, theory &
                                        practicals.
                                        Schedule exams, auto-mark scripts, and generate report cards instantly.
                                    </p>
                                    <div class="space-y-2 sm:space-y-3">
                                        <div
                                            class="flex items-center text-xs sm:text-sm text-gray-600 dark:text-gray-300">
                                            <svg class="h-3 w-3 sm:h-4 sm:w-4 text-green-500 mr-2 sm:mr-3 flex-shrink-0"
                                                 fill="none" stroke="currentColor"
                                                 viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M5 13l4 4L19 7"></path>
                                            </svg>
                                            Multiple Formats
                                        </div>
                                        <div
                                            class="flex items-center text-xs sm:text-sm text-gray-600 dark:text-gray-300">
                                            <svg class="h-3 w-3 sm:h-4 sm:w-4 text-green-500 mr-2 sm:mr-3 flex-shrink-0"
                                                 fill="none" stroke="currentColor"
                                                 viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M5 13l4 4L19 7"></path>
                                            </svg>
                                            Secure Environment
                                        </div>
                                        <div
                                            class="flex items-center text-xs sm:text-sm text-gray-600 dark:text-gray-300">
                                            <svg class="h-3 w-3 sm:h-4 sm:w-4 text-green-500 mr-2 sm:mr-3 flex-shrink-0"
                                                 fill="none" stroke="currentColor"
                                                 viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M5 13l4 4L19 7"></path>
                                            </svg>
                                            Auto Report Cards
                                        </div>
                                    </div>
                                </div>

                                <!-- Monitoring & Reporting Module -->
                                <div
                                    class="group bg-white dark:bg-gray-800 rounded-2xl sm:rounded-3xl shadow-xl hover:shadow-2xl transition-all duration-500 border border-gray-200 dark:border-gray-700 hover:border-indigo-300 dark:hover:border-indigo-600 p-6 sm:p-8 transform hover:scale-105"
                                    data-aos="flip-left" data-aos-delay="200">
                                    <div
                                        class="flex items-center justify-center h-14 w-14 sm:h-16 sm:w-16 lg:h-20 lg:w-20 rounded-2xl sm:rounded-3xl bg-gradient-to-r from-indigo-500 to-blue-600 text-white mb-4 sm:mb-6 lg:mb-8 group-hover:scale-110 transition-transform duration-300 shadow-lg">
                                        <svg class="h-7 w-7 sm:h-8 sm:w-8 lg:h-10 lg:w-10" fill="none"
                                             stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                        </svg>
                                    </div>
                                    <h3 class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-white mb-3 sm:mb-4">
                                        Monitoring &
                                        Reporting</h3>
                                    <p class="text-sm sm:text-base text-gray-600 dark:text-gray-300 mb-4 sm:mb-6 leading-relaxed">
                                        Real-time analytics to track student engagement, teacher activity, and
                                        school-wide
                                        performance. Instantly spot trends and get custom reports.
                                    </p>
                                    <div class="space-y-2 sm:space-y-3">
                                        <div
                                            class="flex items-center text-xs sm:text-sm text-gray-600 dark:text-gray-300">
                                            <svg class="h-3 w-3 sm:h-4 sm:w-4 text-green-500 mr-2 sm:mr-3 flex-shrink-0"
                                                 fill="none" stroke="currentColor"
                                                 viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M5 13l4 4L19 7"></path>
                                            </svg>
                                            Real-time Analytics
                                        </div>
                                        <div
                                            class="flex items-center text-xs sm:text-sm text-gray-600 dark:text-gray-300">
                                            <svg class="h-3 w-3 sm:h-4 sm:w-4 text-green-500 mr-2 sm:mr-3 flex-shrink-0"
                                                 fill="none" stroke="currentColor"
                                                 viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M5 13l4 4L19 7"></path>
                                            </svg>
                                            Performance Tracking
                                        </div>
                                        <div
                                            class="flex items-center text-xs sm:text-sm text-gray-600 dark:text-gray-300">
                                            <svg class="h-3 w-3 sm:h-4 sm:w-4 text-green-500 mr-2 sm:mr-3 flex-shrink-0"
                                                 fill="none" stroke="currentColor"
                                                 viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M5 13l4 4L19 7"></path>
                                            </svg>
                                            Custom Reports
                                        </div>
                                    </div>
                                </div>

                                <!-- Books & Marketplace Module -->
                                <div
                                    class="group bg-white dark:bg-gray-800 rounded-2xl sm:rounded-3xl shadow-xl hover:shadow-2xl transition-all duration-500 border border-gray-200 dark:border-gray-700 hover:border-emerald-300 dark:hover:border-emerald-600 p-6 sm:p-8 transform hover:scale-105"
                                    data-aos="flip-left" data-aos-delay="300">
                                    <div
                                        class="flex items-center justify-center h-16 w-16 sm:h-20 sm:w-20 rounded-2xl sm:rounded-3xl bg-gradient-to-r from-emerald-500 to-teal-600 text-white mb-6 sm:mb-8 group-hover:scale-110 transition-transform duration-300 shadow-lg">
                                        <svg class="h-8 w-8 sm:h-10 sm:w-10" fill="none" stroke="currentColor"
                                             viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                                        </svg>
                                    </div>
                                    <h3 class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-white mb-3 sm:mb-4">
                                        Books &
                                        Marketplace</h3>
                                    <p class="text-sm sm:text-base text-gray-600 dark:text-gray-300 mb-4 sm:mb-6 leading-relaxed">
                                        A digital bookstore where authors and publishers can sell or offer books
                                        (textbooks, novels,
                                        poems, short stories, etc). Schools and students can buy or download books
                                        easily.
                                    </p>
                                    <div class="space-y-2 sm:space-y-3">
                                        <div
                                            class="flex items-center text-xs sm:text-sm text-gray-600 dark:text-gray-300">
                                            <svg class="h-3 w-3 sm:h-4 sm:w-4 text-green-500 mr-2 sm:mr-3 flex-shrink-0"
                                                 fill="none" stroke="currentColor"
                                                 viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M5 13l4 4L19 7"></path>
                                            </svg>
                                            Digital Marketplace
                                        </div>
                                        <div
                                            class="flex items-center text-xs sm:text-sm text-gray-600 dark:text-gray-300">
                                            <svg class="h-3 w-3 sm:h-4 sm:w-4 text-green-500 mr-2 sm:mr-3 flex-shrink-0"
                                                 fill="none" stroke="currentColor"
                                                 viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M5 13l4 4L19 7"></path>
                                            </svg>
                                            Author Publishing
                                        </div>
                                        <div
                                            class="flex items-center text-xs sm:text-sm text-gray-600 dark:text-gray-300">
                                            <svg class="h-3 w-3 sm:h-4 sm:w-4 text-green-500 mr-2 sm:mr-3 flex-shrink-0"
                                                 fill="none" stroke="currentColor"
                                                 viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M5 13l4 4L19 7"></path>
                                            </svg>
                                            Easy Download
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Additional Modules Grid -->
                            <div
                                class="bg-white dark:bg-gray-800 rounded-2xl sm:rounded-3xl p-6 sm:p-8 border border-gray-200 dark:border-gray-700"
                                data-aos="fade-up" data-aos-delay="400">
                                <div class="text-center mb-8 sm:mb-12">
                                    <h3 class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-white">Additional
                                        Platform
                                        Features</h3>
                                    <p class="text-sm sm:text-base text-gray-600 dark:text-gray-300">Explore more tools
                                        and features that
                                        enhance your
                                        learning experience</p>
                                </div>

                                <div
                                    class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6 gap-4 sm:gap-6">
                                    <!-- Content Management -->
                                    <div
                                        class="group text-center p-3 sm:p-4 rounded-xl sm:rounded-2xl hover:bg-gray-50 dark:hover:bg-gray-700 transition-all duration-300 cursor-pointer">
                                        <div
                                            class="flex items-center justify-center h-12 w-12 sm:h-16 sm:w-16 rounded-xl sm:rounded-2xl bg-gradient-to-r from-cyan-500 to-blue-500 text-white mx-auto mb-3 sm:mb-4 group-hover:scale-110 transition-transform duration-300">
                                            <svg class="h-6 w-6 sm:h-8 sm:w-8" fill="none" stroke="currentColor"
                                                 viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                            </svg>
                                        </div>
                                        <h4 class="text-xs sm:text-sm font-semibold text-gray-900 dark:text-white mb-1 sm:mb-2">
                                            Content
                                            Management</h4>
                                        <p class="text-xs text-gray-600 dark:text-gray-400">Upload & organize
                                            materials</p>
                                    </div>

                                    <!-- Communication -->
                                    <div
                                        class="group text-center p-3 sm:p-4 rounded-xl sm:rounded-2xl hover:bg-gray-50 dark:hover:bg-gray-700 transition-all duration-300 cursor-pointer">
                                        <div
                                            class="flex items-center justify-center h-12 w-12 sm:h-16 sm:w-16 rounded-xl sm:rounded-2xl bg-gradient-to-r from-pink-500 to-rose-500 text-white mx-auto mb-3 sm:mb-4 group-hover:scale-110 transition-transform duration-300">
                                            <svg class="h-6 w-6 sm:h-8 sm:w-8" fill="none" stroke="currentColor"
                                                 viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                                            </svg>
                                        </div>
                                        <h4 class="text-xs sm:text-sm font-semibold text-gray-900 dark:text-white mb-1 sm:mb-2">
                                            Communication</h4>
                                        <p class="text-xs text-gray-600 dark:text-gray-400">Messaging & forums</p>
                                    </div>

                                    <!-- Student Portal -->
                                    <div
                                        class="group text-center p-3 sm:p-4 rounded-xl sm:rounded-2xl hover:bg-gray-50 dark:hover:bg-gray-700 transition-all duration-300 cursor-pointer">
                                        <div
                                            class="flex items-center justify-center h-12 w-12 sm:h-16 sm:w-16 rounded-xl sm:rounded-2xl bg-gradient-to-r from-lime-500 to-green-500 text-white mx-auto mb-3 sm:mb-4 group-hover:scale-110 transition-transform duration-300">
                                            <svg class="h-6 w-6 sm:h-8 sm:w-8" fill="none" stroke="currentColor"
                                                 viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                            </svg>
                                        </div>
                                        <h4 class="text-xs sm:text-sm font-semibold text-gray-900 dark:text-white mb-1 sm:mb-2">
                                            Student
                                            Portal</h4>
                                        <p class="text-xs text-gray-600 dark:text-gray-400">Personalized space</p>
                                    </div>

                                    <!-- Teacher CPD -->
                                    <div
                                        class="group text-center p-3 sm:p-4 rounded-xl sm:rounded-2xl hover:bg-gray-50 dark:hover:bg-gray-700 transition-all duration-300 cursor-pointer">
                                        <div
                                            class="flex items-center justify-center h-12 w-12 sm:h-16 sm:w-16 rounded-xl sm:rounded-2xl bg-gradient-to-r from-amber-500 to-orange-500 text-white mx-auto mb-3 sm:mb-4 group-hover:scale-110 transition-transform duration-300">
                                            <svg class="h-6 w-6 sm:h-8 sm:w-8" fill="none" stroke="currentColor"
                                                 viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M12 14l9-5-9-5-9 5 9 5z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M12 14l6.16-3.422A12.083 12.083 0 0112 8.624 12.083 12.083 0 015.84 10.578L12 14z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422A12.083 12.083 0 0112 8.624a12.083 12.083 0 00-6.16 1.954L12 14zm-4 6v-7.5l4-2.222"/>
                                            </svg>
                                        </div>
                                        <h4 class="text-xs sm:text-sm font-semibold text-gray-900 dark:text-white mb-1 sm:mb-2">
                                            Teacher
                                            CPD</h4>
                                        <p class="text-xs text-gray-600 dark:text-gray-400">Professional development</p>
                                    </div>

                                    <!-- Mock Exams -->
                                    <div
                                        class="group text-center p-3 sm:p-4 rounded-xl sm:rounded-2xl hover:bg-gray-50 dark:hover:bg-gray-700 transition-all duration-300 cursor-pointer">
                                        <div
                                            class="flex items-center justify-center h-12 w-12 sm:h-16 sm:w-16 rounded-xl sm:rounded-2xl bg-gradient-to-r from-violet-500 to-purple-500 text-white mx-auto mb-3 sm:mb-4 group-hover:scale-110 transition-transform duration-300">
                                            <svg class="h-6 w-6 sm:h-8 sm:w-8" fill="none" stroke="currentColor"
                                                 viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                            </svg>
                                        </div>
                                        <h4 class="text-xs sm:text-sm font-semibold text-gray-900 dark:text-white mb-1 sm:mb-2">
                                            Mock
                                            Exams</h4>
                                        <p class="text-xs text-gray-600 dark:text-gray-400">BECE/WASSCE practice</p>
                                    </div>

                                    <!-- National Curriculum -->
                                    <div
                                        class="group text-center p-3 sm:p-4 rounded-xl sm:rounded-2xl hover:bg-gray-50 dark:hover:bg-gray-700 transition-all duration-300 cursor-pointer">
                                        <div
                                            class="flex items-center justify-center h-12 w-12 sm:h-16 sm:w-16 rounded-xl sm:rounded-2xl bg-gradient-to-r from-teal-500 to-cyan-500 text-white mx-auto mb-3 sm:mb-4 group-hover:scale-110 transition-transform duration-300">
                                            <svg class="h-6 w-6 sm:h-8 sm:w-8" fill="none" stroke="currentColor"
                                                 viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                                            </svg>
                                        </div>
                                        <h4 class="text-xs sm:text-sm font-semibold text-gray-900 dark:text-white mb-1 sm:mb-2">
                                            National
                                            Curriculum</h4>
                                        <p class="text-xs text-gray-600 dark:text-gray-400">Aligned standards</p>
                                    </div>
                                </div>
                            </div>

                            <!-- CTA Section -->
                            <div class="py-16 sm:py-20 lg:py-24 bg-gradient-to-r" data-aos="zoom-in"
                                 data-aos-delay="200">
                                <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                                    <h2 class="text-2xl sm:text-3xl md:text-4xl lg:text-5xl font-extrabold sm:font-black mb-4 sm:mb-6">
                                        Ready to Transform Education?</h2>
                                    <p class="text-base sm:text-lg md:text-xl mb-6 sm:mb-8">Join thousands of learners
                                        and educators already on the
                                        platform</p>
                                    <div class="flex flex-col sm:flex-row gap-3 sm:gap-4 justify-center">
                                        <a href="{{ route('register') }}"
                                           class="px-6 py-3 sm:px-10 sm:py-5 bg-white text-blue-600 font-bold text-base sm:text-lg rounded-xl sm:rounded-2xl hover:shadow-2xl transform hover:scale-105 transition-all duration-300">
                                            Start Your Journey
                                        </a>
                                        <a href="{{ route('branding.features') }}"
                                           class="px-6 py-3 sm:px-10 sm:py-5 border-2 bg-blue-500 border-white text-white font-bold text-base sm:text-lg rounded-xl sm:rounded-2xl hover:bg-white/10 transition-all duration-300">
                                            Explore Features
                                        </a>
                                    </div>
                                    <div
                                        class="mt-6 sm:mt-8 flex flex-col sm:flex-row items-center justify-center sm:space-x-6 space-y-3 sm:space-y-0 text-xs sm:text-sm">
                                        <div class="flex items-center">
                                            <svg class="w-5 h-5 text-green-300 mr-2" fill="currentColor"
                                                 viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                      d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                      clip-rule="evenodd"/>
                                            </svg>
                                            Free 30-day trial
                                        </div>
                                        <div class="flex items-center">
                                            <svg class="w-5 h-5 text-green-300 mr-2" fill="currentColor"
                                                 viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                      d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                      clip-rule="evenodd"/>
                                            </svg>
                                            No credit card required
                                        </div>
                                        <div class="flex items-center">
                                            <svg class="w-5 h-5 text-green-300 mr-2" fill="currentColor"
                                                 viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                      d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                      clip-rule="evenodd"/>
                                            </svg>
                                            Cancel anytime
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>


                    <style>
                        .animation-delay-1000 {
                            animation-delay: 1s;
                        }

                        .animation-delay-2000 {
                            animation-delay: 2s;
                        }

                        @keyframes pulse-slow {

                            0%,
                            100% {
                                opacity: 1;
                                transform: scale(1);
                            }

                            50% {
                                opacity: 0.95;
                                transform: scale(1.02);
                            }
                        }


                        @keyframes blob {

                            0%,
                            100% {
                                transform: translate(0, 0) scale(1);
                            }

                            33% {
                                transform: translate(30px, -50px) scale(1.1);
                            }

                            66% {
                                transform: translate(-20px, 20px) scale(0.9);
                            }
                        }

                        .animate-blob {
                            animation: blob 7s infinite;
                        }

                        html {
                            scroll-behavior: smooth;
                        }
                    </style>
                </div>
            </div>
        </section>

        <!-- Testimonials Section -->
        <section class="py-20 bg-gradient-to-br from-blue-50 to-indigo-50 dark:from-gray-800 dark:to-gray-900">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-16">
                    <div
                        class="inline-flex items-center px-4 py-2 rounded-full bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-200 font-semibold text-sm mb-4"
                        data-aos="fade-down">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                        </svg>
                        Success Stories
                    </div>
                    <h2 class="text-3xl md:text-4xl font-extrabold text-gray-900 dark:text-white mb-6"
                        data-aos="fade-up">
                        Trusted by Schools <span
                            class="text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-indigo-600">Across Ghana</span>
                    </h2>
                    <p class="max-w-3xl text-base text-gray-600 dark:text-gray-300 mx-auto" data-aos="fade-up"
                       data-aos-delay="100">
                        See how schools are transforming education with All Academies
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <!-- Testimonial 1 -->
                    <div class="bg-white dark:bg-gray-800 rounded-2xl p-8 shadow-xl" data-aos="fade-up"
                         data-aos-delay="100">
                        <div class="flex items-center mb-4">
                            @for($i = 0; $i < 5; $i++)
                                <svg class="w-5 h-5 text-yellow-400 fill-current" viewBox="0 0 20 20">
                                    <path
                                        d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/>
                                </svg>
                            @endfor
                        </div>
                        <p class="text-gray-600 dark:text-gray-300 mb-6 italic">
                            "All Academies has revolutionized how we manage our school. Student engagement increased by
                            40% and administrative tasks are now 60% faster."
                        </p>
                        <div class="flex items-center">
                            <div
                                class="w-12 h-12 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center text-white font-bold mr-4">
                                KA
                            </div>
                            <div>
                                <div class="font-semibold text-gray-900 dark:text-white">Kwame Asante</div>
                                <div class="text-sm text-gray-500 dark:text-gray-400">Principal, Accra Senior High
                                    School
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Testimonial 2 -->
                    <div class="bg-white dark:bg-gray-800 rounded-2xl p-8 shadow-xl" data-aos="fade-up"
                         data-aos-delay="200">
                        <div class="flex items-center mb-4">
                            @for($i = 0; $i < 5; $i++)
                                <svg class="w-5 h-5 text-yellow-400 fill-current" viewBox="0 0 20 20">
                                    <path
                                        d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/>
                                </svg>
                            @endfor
                        </div>
                        <p class="text-gray-600 dark:text-gray-300 mb-6 italic">
                            "The digital library and AI-powered learning tools have transformed how our students learn.
                            Test scores improved by 25% in just one semester."
                        </p>
                        <div class="flex items-center">
                            <div
                                class="w-12 h-12 bg-gradient-to-br from-green-500 to-teal-600 rounded-full flex items-center justify-center text-white font-bold mr-4">
                                AM
                            </div>
                            <div>
                                <div class="font-semibold text-gray-900 dark:text-white">Ama Mensah</div>
                                <div class="text-sm text-gray-500 dark:text-gray-400">Head Teacher, Kumasi International
                                    School
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Testimonial 3 -->
                    <div class="bg-white dark:bg-gray-800 rounded-2xl p-8 shadow-xl" data-aos="fade-up"
                         data-aos-delay="300">
                        <div class="flex items-center mb-4">
                            @for($i = 0; $i < 5; $i++)
                                <svg class="w-5 h-5 text-yellow-400 fill-current" viewBox="0 0 20 20">
                                    <path
                                        d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/>
                                </svg>
                            @endfor
                        </div>
                        <p class="text-gray-600 dark:text-gray-300 mb-6 italic">
                            "As a parent, I can now track my child's progress in real-time. The communication tools keep
                            me connected with teachers effortlessly."
                        </p>
                        <div class="flex items-center">
                            <div
                                class="w-12 h-12 bg-gradient-to-br from-orange-500 to-red-600 rounded-full flex items-center justify-center text-white font-bold mr-4">
                                KO
                            </div>
                            <div>
                                <div class="font-semibold text-gray-900 dark:text-white">Kofi Owusu</div>
                                <div class="text-sm text-gray-500 dark:text-gray-400">Parent, Takoradi Methodist
                                    School
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Trust Badges -->
                <div class="mt-16 text-center" data-aos="fade-up" data-aos-delay="400">
                    <p class="text-gray-600 dark:text-gray-400 mb-6 font-semibold">Trusted by Leading Educational
                        Institutions</p>
                    <div class="flex flex-wrap justify-center items-center gap-8">
                        <div class="flex items-center space-x-2 text-gray-700 dark:text-gray-300">
                            <svg class="w-6 h-6 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                      d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                      clip-rule="evenodd"/>
                            </svg>
                            <span class="font-semibold">Ghana Education Service Approved</span>
                        </div>
                        <div class="flex items-center space-x-2 text-gray-700 dark:text-gray-300">
                            <svg class="w-6 h-6 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                      d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                      clip-rule="evenodd"/>
                            </svg>
                            <span class="font-semibold">ISO 27001 Certified</span>
                        </div>
                        <div class="flex items-center space-x-2 text-gray-700 dark:text-gray-300">
                            <svg class="w-6 h-6 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                      d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                      clip-rule="evenodd"/>
                            </svg>
                            <span class="font-semibold">GDPR Compliant</span>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- FAQ Section -->
        <section id="faq" class="bg-white dark:bg-gray-900">
            @include('faq')
        </section>
    </div>
    <x-layouts.footer/>
</x-app>
