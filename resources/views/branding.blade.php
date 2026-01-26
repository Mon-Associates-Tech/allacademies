<x-app>
    <!-- Navigation -->
    <div class="bg-white dark:bg-gray-900 transition-colors  duration-300">
        <!-- Navigation -->
        @include('branding.partials.header')

        <!-- Hero Section -->
        <div id="home"
             class="relative h-screen overflow-hidden pb-5 flex flex-col flex-1 mt-auto items-center justify-center">
            <!-- Video Background -->
            <div class="absolute inset-0 z-0">
                <video autoplay muted loop class="w-full h-full object-cover">
                    <!-- Local video file -->
                    <source src="{{ asset('media/video/header-background-video.mp4') }}" type="video/mp4">
                    Your browser does not support the video tag.
                </video>
                <div
                    class="absolute inset-0 bg-gradient-to-r from-blue-400/50 via-indigo-400/50 to-green-400/40 dark:from-blue-400/55 dark:via-indigo-400/50 dark:to-green-400/50"></div>
            </div>

            <!-- Animated Background Elements (kept for visual enhancement) -->
            <div class="absolute inset-0 hidden">
                <div
                    class="absolute top-10 left-10 w-72 h-72 bg-blue-300/20 dark:bg-blue-500/10 rounded-full mix-blend-multiply filter blur-xl animate-blob"></div>
                <div
                    class="absolute top-10 right-10 w-72 h-72 bg-green-300/20 dark:bg-green-500/10 rounded-full mix-blend-multiply filter blur-xl animate-blob animation-delay-2000"></div>
                <div
                    class="absolute -bottom-8 left-20 w-72 h-72 bg-purple-300/20 dark:bg-purple-500/10 rounded-full mix-blend-multiply filter blur-xl animate-blob animation-delay-4000"></div>
            </div>

            <!-- Content -->
            <div class="z-10 w-full mt-24 max-h-[1044px] flex items-center">
                <div class="flex flex-col w-full h-full  mt-auto items-center justify-center">
                    <!-- Text Content -->
                    <div class="text-center space-y-8">
                        <div class="text-white">
                            <div
                                class="inline-flex hidden items-center px-3 py-1 rounded-full bg-white/10 dark:bg-white/5 backdrop-blur-sm border border-white/20 dark:border-white/10 text-white">
                                <svg class="w-4 h-4 mr-2 text-green-400" fill="none" stroke="currentColor"
                                     viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span class="text-xs font-medium">Trusted by Thousands of Students Worldwide</span>
                            </div>

                            <h1 class="text-4xl font-extrabold pt-2 tracking-tight text-white sm:text-5xl md:text-6xl">
                                <span class="block">Transform Your Learning Journey</span>
                                <span
                                    class="hidden text-transparent bg-clip-text bg-gradient-to-r from-blue-300 via-purple-300 to-green-300">Learning Journey</span>
                                <span
                                    class="block mt-2 text-transparent bg-clip-text bg-gradient-to-r from-blue-300 via-purple-300 to-green-300">With All Academies</span>
                            </h1>
                        </div>

                        <p class="max-w-xl text-xl hidden lg:block text-gray-200 leading-relaxed mx-auto">
                            Access a comprehensive digital ecosystem of educational resources,
                            expert-authored content, and cutting-edge learning tools designed for academic and
                            professional excellence.
                        </p>

                        <div class="flex flex-col sm:flex-row gap-4 justify-center">
                            <a href="#features"
                               class="group inline-flex items-center justify-center px-8 py-4 text-base font-semibold rounded-xl text-white bg-gradient-to-r from-blue-600 to-green-600 hover:from-blue-700 hover:to-green-700 shadow-xl hover:shadow-2xl transform hover:scale-105 transition-all duration-300">
                                <svg class="w-5 h-5 mr-2 group-hover:animate-pulse" fill="none"
                                     stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                </svg>
                                Start Learning Today
                            </a>
                            <a href="#modules"
                               class="inline-flex items-center justify-center px-8 py-4 border-2 border-white/70 text-base font-semibold rounded-xl text-white bg-white/10 backdrop-blur-sm hover:bg-white hover:text-gray-900 shadow-lg hover:shadow-xl transition-all duration-300">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                                Explore Modules
                            </a>
                        </div>

                        <!-- Trust Indicators -->
                        <div
                            class="flex flex-col sm:flex-row items-center justify-center space-y-4 sm:space-y-0 sm:space-x-6 pt-4">
                            <div class="flex items-center space-x-1">
                                <div class="flex -space-x-1">
                                    <img class="w-8 h-8 rounded-full border-2 border-white"
                                         src="https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=32&h=32&fit=crop&crop=face"
                                         alt="User">
                                    <img class="w-8 h-8 rounded-full border-2 border-white"
                                         src="https://images.unsplash.com/photo-1494790108755-2616b612b786?w=32&h=32&fit=crop&crop=face"
                                         alt="User">
                                    <img class="w-8 h-8 rounded-full border-2 border-white"
                                         src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=32&h=32&fit=crop&crop=face"
                                         alt="User">
                                </div>
                                <span class="text-white text-sm ml-2">Join thousands of learners</span>
                            </div>
                            <div class="flex items-center space-x-1">
                                <div class="flex space-x-1">
                                    <svg class="w-4 h-4 text-yellow-400 fill-current" viewBox="0 0 20 20">
                                        <path
                                            d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/>
                                    </svg>
                                    <svg class="w-4 h-4 text-yellow-400 fill-current" viewBox="0 0 20 20">
                                        <path
                                            d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/>
                                    </svg>
                                    <svg class="w-4 h-4 text-yellow-400 fill-current" viewBox="0 0 20 20">
                                        <path
                                            d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/>
                                    </svg>
                                    <svg class="w-4 h-4 text-yellow-400 fill-current" viewBox="0 0 20 20">
                                        <path
                                            d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/>
                                    </svg>
                                    <svg class="w-4 h-4 text-yellow-400 fill-current" viewBox="0 0 20 20">
                                        <path
                                            d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/>
                                    </svg>
                                </div>
                                <span class="text-white text-sm">4.9/5 Rating</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <section>
            <!-- Video Demo Section -->
            <div id="demo" class="py-24 bg-gray-50 dark:bg-gray-800 transition-colors duration-300">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="text-center mb-16">
                        <div
                            class="inline-flex items-center px-4 py-2 rounded-full bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-200 font-semibold text-sm mb-4">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Watch Demo
                        </div>
                        <h2 class="text-4xl font-extrabold text-gray-900 dark:text-white sm:text-5xl mb-6">
                            See the Platform <span
                                class="text-transparent bg-clip-text bg-gradient-to-r from-red-600 to-orange-600">In Action</span>
                        </h2>
                        <p class="max-w-3xl text-xl text-gray-600 dark:text-gray-300 mx-auto leading-relaxed">
                            Take a guided tour through our features and see how we transform the learning
                            experience for students and educators.
                        </p>
                    </div>

                    <div class="relative max-w-5xl mx-auto">
                        <!-- Decorative Elements -->
                        <div
                            class="absolute -top-10 -left-10 w-40 h-40 bg-red-300 dark:bg-red-600/20 rounded-full mix-blend-multiply filter blur-xl opacity-70 animate-blob"></div>
                        <div
                            class="absolute -bottom-10 -right-10 w-40 h-40 bg-orange-300 dark:bg-orange-600/20 rounded-full mix-blend-multiply filter blur-xl opacity-70 animate-blob animation-delay-2000"></div>

                        <div
                            class="relative rounded-3xl overflow-hidden shadow-2xl border border-gray-200 dark:border-gray-700 bg-gray-900 group">
                            <!-- Video Player -->
                            <div class="aspect-w-16 aspect-h-9">
                                <video class="w-full h-full object-cover" controls preload="none"
                                       poster="https://images.unsplash.com/photo-1516321318423-f06f85e504b3?w=1200&h=675&fit=crop">
                                    <!-- Replace with your actual demo video path -->
                                    <source src="{{ asset('media/video/platform-demo.mp4') }}" type="video/mp4">
                                    Your browser does not support the video tag.
                                </video>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Features Section -->
        <div id="features" class="py-24 bg-white dark:bg-gray-900 transition-colors duration-300">
            <div class="max-w-7xl mx-auto">
                <div class="text-center mb-20">
                    <div
                        class="inline-flex items-center px-4 py-2 rounded-full bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-200 font-semibold text-sm mb-4">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                        Platform Features
                    </div>
                    <h2 class="text-4xl font-extrabold text-gray-900 dark:text-white sm:text-5xl mb-6">
                        Everything You Need to <span
                            class="text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-green-600">Excel</span>
                    </h2>
                    <p class="max-w-3xl text-xl text-gray-600 dark:text-gray-300 mx-auto leading-relaxed">
                        Our comprehensive platform combines cutting-edge technology with educational expertise to
                        deliver an unparalleled learning experience that adapts to your unique goals.
                    </p>
                </div>

                <div class="space-y-32">
                    <!-- Feature 1: Comprehensive Library -->
                    <div class="flex flex-col lg:flex-row items-center gap-16  px-4 sm:px-6 lg:px-8 ">
                        <div class="lg:w-1/2">
                            <div class="relative group">
                                <div
                                    class="absolute -inset-4 bg-gradient-to-r from-blue-500 to-green-500 rounded-3xl blur-lg opacity-30 group-hover:opacity-50 transition-opacity duration-500"></div>
                                <div class="relative">
                                    <img
                                        src="https://images.unsplash.com/photo-1481627834876-b7833e8f5570?w=600&h=400&fit=crop&crop=center"
                                        alt="Vast Digital Library with thousands of books"
                                        class="rounded-2xl shadow-2xl w-full h-80 object-cover transform group-hover:scale-105 transition-transform duration-700">
                                    <div
                                        class="absolute inset-0 bg-gradient-to-tr from-blue-600/20 to-green-600/10 rounded-2xl"></div>

                                    <!-- Floating Elements -->
                                    <div
                                        class="absolute top-4 left-4 bg-white/95 dark:bg-gray-800/95 backdrop-blur-sm rounded-lg px-3 py-2 shadow-lg">
                                        <div class="flex items-center space-x-2">
                                            <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
                                            <span class="text-sm font-semibold text-gray-900 dark:text-gray-100">15,000+ Books</span>
                                        </div>
                                    </div>

                                    <div
                                        class="absolute bottom-4 right-4 bg-white/95 dark:bg-gray-800/95 backdrop-blur-sm rounded-lg px-3 py-2 shadow-lg">
                                    <span
                                        class="text-sm font-semibold text-gray-900 dark:text-gray-100">Updated Daily</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="lg:w-1/2 space-y-6">
                            <div class="flex items-center space-x-4">
                                <div
                                    class="flex items-center justify-center h-16 w-16 rounded-2xl bg-gradient-to-r from-blue-500 to-blue-600 text-white shadow-lg">
                                    <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-3xl font-bold text-gray-900 dark:text-white">Comprehensive Digital
                                        Library</h3>
                                    <p class="text-blue-600 dark:text-blue-400 font-semibold">Access Everything,
                                        Anytime</p>
                                </div>
                            </div>

                            <p class="text-lg text-gray-600 dark:text-gray-300 leading-relaxed">
                                Dive into our ever-expanding collection of over 15,000 carefully curated educational
                                resources spanning multiple disciplines. From foundational textbooks to cutting-edge
                                research papers, discover everything you need in one unified platform.
                            </p>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div
                                    class="bg-gray-50 dark:bg-gray-800 rounded-xl p-4 border border-gray-200 dark:border-gray-700">
                                    <div class="flex items-center space-x-3">
                                        <svg class="h-5 w-5 text-green-500" fill="none" stroke="currentColor"
                                             viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M5 13l4 4L19 7"></path>
                                        </svg>
                                        <span
                                            class="text-gray-700 dark:text-gray-300 font-medium">AI-Powered Search</span>
                                    </div>
                                </div>
                                <div
                                    class="bg-gray-50 dark:bg-gray-800 rounded-xl p-4 border border-gray-200 dark:border-gray-700">
                                    <div class="flex items-center space-x-3">
                                        <svg class="h-5 w-5 text-green-500" fill="none" stroke="currentColor"
                                             viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M5 13l4 4L19 7"></path>
                                        </svg>
                                        <span
                                            class="text-gray-700 dark:text-gray-300 font-medium">Multiple Formats</span>
                                    </div>
                                </div>
                                <div
                                    class="bg-gray-50 dark:bg-gray-800 rounded-xl p-4 border border-gray-200 dark:border-gray-700">
                                    <div class="flex items-center space-x-3">
                                        <svg class="h-5 w-5 text-green-500" fill="none" stroke="currentColor"
                                             viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M5 13l4 4L19 7"></path>
                                        </svg>
                                        <span class="text-gray-700 dark:text-gray-300 font-medium">Offline Access</span>
                                    </div>
                                </div>
                                <div
                                    class="bg-gray-50 dark:bg-gray-800 rounded-xl p-4 border border-gray-200 dark:border-gray-700">
                                    <div class="flex items-center space-x-3">
                                        <svg class="h-5 w-5 text-green-500" fill="none" stroke="currentColor"
                                             viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M5 13l4 4L19 7"></path>
                                        </svg>
                                        <span class="text-gray-700 dark:text-gray-300 font-medium">Daily Updates</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Feature 2: Expert Authors -->
                    <div class="flex flex-col lg:flex-row-reverse items-center gap-16  px-4 sm:px-6 lg:px-8">
                        <div class="lg:w-1/2">
                            <div class="relative group">
                                <div
                                    class="absolute -inset-4 bg-gradient-to-r from-green-500 to-blue-500 rounded-3xl blur-lg opacity-30 group-hover:opacity-50 transition-opacity duration-500"></div>
                                <div class="relative">
                                    <img
                                        src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=600&h=400&fit=crop&crop=center"
                                        alt="Expert authors and educators"
                                        class="rounded-2xl shadow-2xl w-full h-80 object-cover transform group-hover:scale-105 transition-transform duration-700">
                                    <div
                                        class="absolute inset-0 bg-gradient-to-tl from-green-600/20 to-blue-600/10 rounded-2xl"></div>

                                    <!-- Author Cards Overlay -->
                                    <div class="absolute top-4 right-4 space-y-2">
                                        <div
                                            class="bg-white/95 dark:bg-gray-800/95 backdrop-blur-sm rounded-lg px-3 py-2 shadow-lg">
                                            <div class="flex items-center space-x-2">
                                                <img class="w-6 h-6 rounded-full"
                                                     src="https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=24&h=24&fit=crop&crop=face"
                                                     alt="Expert">
                                                <span class="text-xs font-semibold text-gray-900 dark:text-gray-100">Dr. Smith - AI Expert</span>
                                            </div>
                                        </div>
                                        <div
                                            class="bg-white/95 dark:bg-gray-800/95 backdrop-blur-sm rounded-lg px-3 py-2 shadow-lg">
                                            <div class="flex items-center space-x-2">
                                                <img class="w-6 h-6 rounded-full"
                                                     src="https://images.unsplash.com/photo-1494790108755-2616b612b786?w=24&h=24&fit=crop&crop=face"
                                                     alt="Expert">
                                                <span class="text-xs font-semibold text-gray-900 dark:text-gray-100">Prof. Johnson - PhD</span>
                                            </div>
                                        </div>
                                    </div>

                                    <div
                                        class="absolute bottom-4 left-4 bg-white/95 dark:bg-gray-800/95 backdrop-blur-sm rounded-lg px-3 py-2 shadow-lg">
                                        <span class="text-sm font-semibold text-gray-900 dark:text-gray-100">500+ Verified Experts</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="lg:w-1/2 space-y-6">
                            <div class="flex items-center space-x-4">
                                <div
                                    class="flex items-center justify-center h-16 w-16 rounded-2xl bg-gradient-to-r from-green-500 to-green-600 text-white shadow-lg">
                                    <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-3xl font-bold text-gray-900 dark:text-white">Learn from Industry
                                        Leaders</h3>
                                    <p class="text-green-600 dark:text-green-400 font-semibold">Verified Expertise</p>
                                </div>
                            </div>

                            <p class="text-lg text-gray-600 dark:text-gray-300 leading-relaxed">
                                Connect with renowned academics, industry professionals, and thought leaders who
                                contribute high-quality, peer-reviewed content. Every author is verified and brings
                                real-world expertise to ensure you're learning from the best minds in each field.
                            </p>

                            <div class="space-y-4">
                                <div class="flex items-start space-x-4">
                                    <div class="flex-shrink-0">
                                        <div
                                            class="flex items-center justify-center h-10 w-10 rounded-lg bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400">
                                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                        </div>
                                    </div>
                                    <div>
                                        <h4 class="text-lg font-semibold text-gray-900 dark:text-white">Verified
                                            Credentials</h4>
                                        <p class="text-gray-600 dark:text-gray-300">All authors undergo rigorous
                                            verification of their academic and professional credentials</p>
                                    </div>
                                </div>
                                <div class="flex items-start space-x-4">
                                    <div class="flex-shrink-0">
                                        <div
                                            class="flex items-center justify-center h-10 w-10 rounded-lg bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400">
                                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                            </svg>
                                        </div>
                                    </div>
                                    <div>
                                        <h4 class="text-lg font-semibold text-gray-900 dark:text-white">Direct
                                            Communication</h4>
                                        <p class="text-gray-600 dark:text-gray-300">Engage directly with authors through
                                            Q&A sessions and discussion forums</p>
                                    </div>
                                </div>
                                <div class="flex items-start space-x-4">
                                    <div class="flex-shrink-0">
                                        <div
                                            class="flex items-center justify-center h-10 w-10 rounded-lg bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400">
                                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M4.871 4A17.926 17.926 0 003 12c0 2.874.673 5.59 1.871 8m14.13 0a17.926 17.926 0 001.87-8c0-2.874-.673-5.59-1.87-8M9 9h1.246a1 1 0 01.961.725l1.586 5.55a1 1 0 00.961.725H15m1-7h-.08a2 2 0 00-1.519.698L9.6 15.302A2 2 0 018.08 16H8"></path>
                                            </svg>
                                        </div>
                                    </div>
                                    <div>
                                        <h4 class="text-lg font-semibold text-gray-900 dark:text-white">Latest
                                            Research</h4>
                                        <p class="text-gray-600 dark:text-gray-300">Content reflects the most current
                                            research and industry best practices</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Feature 3: Interactive Learning -->
                    <div class="flex flex-col lg:flex-row items-center gap-16  px-4 sm:px-6 lg:px-8">
                        <div class="lg:w-1/2">
                            <div class="relative group">
                                <div
                                    class="absolute -inset-4 bg-gradient-to-r from-purple-500 to-pink-500 rounded-3xl blur-lg opacity-30 group-hover:opacity-50 transition-opacity duration-500"></div>
                                <div class="relative">
                                    <img
                                        src="https://images.unsplash.com/photo-1522202176988-66273c2fd55f?w=600&h=400&fit=crop&crop=center"
                                        alt="Interactive learning platform with students collaborating"
                                        class="rounded-2xl shadow-2xl w-full h-80 object-cover transform group-hover:scale-105 transition-transform duration-700">
                                    <div
                                        class="absolute inset-0 bg-gradient-to-br from-purple-600/20 to-pink-600/10 rounded-2xl"></div>

                                    <!-- Interactive Elements Overlay -->
                                    <div
                                        class="absolute top-4 left-4 bg-white/95 dark:bg-gray-800/95 backdrop-blur-sm rounded-lg px-3 py-2 shadow-lg">
                                        <div class="flex items-center space-x-2">
                                            <div class="w-2 h-2 bg-purple-500 rounded-full animate-pulse"></div>
                                            <span class="text-sm font-semibold text-gray-900 dark:text-gray-100">Live Session</span>
                                        </div>
                                    </div>

                                    <div
                                        class="absolute top-4 right-4 bg-white/95 dark:bg-gray-800/95 backdrop-blur-sm rounded-lg px-3 py-2 shadow-lg">
                                        <span
                                            class="text-sm font-semibold text-gray-900 dark:text-gray-100">24 Online</span>
                                    </div>

                                    <div
                                        class="absolute bottom-4 left-4 bg-white/95 dark:bg-gray-800/95 backdrop-blur-sm rounded-lg px-3 py-2 shadow-lg">
                                        <span class="text-sm font-semibold text-gray-900 dark:text-gray-100">Interactive Tools</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="lg:w-1/2 space-y-6">
                            <div class="flex items-center space-x-4">
                                <div
                                    class="flex items-center justify-center h-16 w-16 rounded-2xl bg-gradient-to-r from-purple-500 to-pink-500 text-white shadow-lg">
                                    <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-3xl font-bold text-gray-900 dark:text-white">Interactive Learning
                                        Experience</h3>
                                    <p class="text-purple-600 dark:text-purple-400 font-semibold">Engage &
                                        Collaborate</p>
                                </div>
                            </div>

                            <p class="text-lg text-gray-600 dark:text-gray-300 leading-relaxed">
                                Transform passive reading into active learning through interactive quizzes, multimedia
                                presentations, collaborative projects, and real-time discussions. Our platform adapts to
                                your learning style and pace.
                            </p>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div
                                    class="bg-gradient-to-r from-purple-50 to-pink-50 dark:from-purple-900/20 dark:to-pink-900/20 rounded-xl p-4 border border-purple-200 dark:border-purple-700">
                                    <div class="flex items-center space-x-3">
                                        <svg class="h-6 w-6 text-purple-500" fill="none" stroke="currentColor"
                                             viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                                        </svg>
                                        <div>
                                            <div class="font-semibold text-gray-900 dark:text-gray-100">Smart Quizzes
                                            </div>
                                            <div class="text-sm text-gray-600 dark:text-gray-400">Adaptive assessment
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div
                                    class="bg-gradient-to-r from-purple-50 to-pink-50 dark:from-purple-900/20 dark:to-pink-900/20 rounded-xl p-4 border border-purple-200 dark:border-purple-700">
                                    <div class="flex items-center space-x-3">
                                        <svg class="h-6 w-6 text-purple-500" fill="none" stroke="currentColor"
                                             viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                        </svg>
                                        <div>
                                            <div class="font-semibold text-gray-900 dark:text-gray-100">Study Groups
                                            </div>
                                            <div class="text-sm text-gray-600 dark:text-gray-400">Collaborative
                                                learning
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div
                                    class="bg-gradient-to-r from-purple-50 to-pink-50 dark:from-purple-900/20 dark:to-pink-900/20 rounded-xl p-4 border border-purple-200 dark:border-purple-700">
                                    <div class="flex items-center space-x-3">
                                        <svg class="h-6 w-6 text-purple-500" fill="none" stroke="currentColor"
                                             viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                        </svg>
                                        <div>
                                            <div class="font-semibold text-gray-900 dark:text-gray-100">Progress
                                                Analytics
                                            </div>
                                            <div class="text-sm text-gray-600 dark:text-gray-400">Track your journey
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div
                                    class="bg-gradient-to-r from-purple-50 to-pink-50 dark:from-purple-900/20 dark:to-pink-900/20 rounded-xl p-4 border border-purple-200 dark:border-purple-700">
                                    <div class="flex items-center space-x-3">
                                        <svg class="h-6 w-6 text-purple-500" fill="none" stroke="currentColor"
                                             viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                        </svg>
                                        <div>
                                            <div class="font-semibold text-gray-900 dark:text-gray-100">Mobile
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
                    <div id="modules"
                         class="py-24 bg-gradient-to-br from-gray-50 to-blue-50 dark:from-gray-800 dark:to-gray-900 transition-colors duration-300">
                        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                            <div class="text-center mb-20">
                                <div
                                    class="inline-flex items-center px-4 py-2 rounded-full bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-200 font-semibold text-sm mb-4">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                    </svg>
                                    Platform Modules
                                </div>
                                <h2 class="text-4xl font-extrabold text-gray-900 dark:text-white sm:text-5xl mb-6">
                                    Comprehensive Learning <span
                                        class="text-transparent bg-clip-text bg-gradient-to-r from-green-600 to-blue-600">Ecosystem</span>
                                </h2>
                                <p class="max-w-3xl text-xl text-gray-600 dark:text-gray-300 mx-auto leading-relaxed">
                                    Discover our integrated modules designed to support every aspect of your educational
                                    journey,
                                    from administration to assessment and beyond.
                                </p>
                            </div>

                            <!-- Featured Modules -->
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 mb-16">
                                <!-- Administration Module -->
                                <div
                                    class="group bg-white dark:bg-gray-800 rounded-3xl shadow-xl hover:shadow-2xl transition-all duration-500 border border-gray-200 dark:border-gray-700 hover:border-blue-300 dark:hover:border-blue-600 p-8 transform hover:scale-105">
                                    <div
                                        class="flex items-center justify-center h-20 w-20 rounded-3xl bg-gradient-to-r from-blue-500 to-indigo-600 text-white mb-8 group-hover:scale-110 transition-transform duration-300 shadow-lg">
                                        <svg class="h-10 w-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                                        </svg>
                                    </div>
                                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">
                                        Administration</h3>
                                    <p class="text-gray-600 dark:text-gray-300 mb-6 leading-relaxed">
                                        A smart dashboard for managing schools, teachers, students, and resources—all in
                                        one place.
                                        Track enrollments, assign roles, and monitor progress with ease.
                                    </p>
                                    <div class="space-y-3">
                                        <div class="flex items-center text-sm text-gray-600 dark:text-gray-300">
                                            <svg class="h-4 w-4 text-green-500 mr-3" fill="none" stroke="currentColor"
                                                 viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M5 13l4 4L19 7"></path>
                                            </svg>
                                            Smart Dashboard
                                        </div>
                                        <div class="flex items-center text-sm text-gray-600 dark:text-gray-300">
                                            <svg class="h-4 w-4 text-green-500 mr-3" fill="none" stroke="currentColor"
                                                 viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M5 13l4 4L19 7"></path>
                                            </svg>
                                            Role Management
                                        </div>
                                        <div class="flex items-center text-sm text-gray-600 dark:text-gray-300">
                                            <svg class="h-4 w-4 text-green-500 mr-3" fill="none" stroke="currentColor"
                                                 viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M5 13l4 4L19 7"></path>
                                            </svg>
                                            Progress Monitoring
                                        </div>
                                    </div>
                                </div>

                                <!-- Teaching Module -->
                                <div
                                    class="group bg-white dark:bg-gray-800 rounded-3xl shadow-xl hover:shadow-2xl transition-all duration-500 border border-gray-200 dark:border-gray-700 hover:border-green-300 dark:hover:border-green-600 p-8 transform hover:scale-105">
                                    <div
                                        class="flex items-center justify-center h-20 w-20 rounded-3xl bg-gradient-to-r from-green-500 to-emerald-600 text-white mb-8 group-hover:scale-110 transition-transform duration-300 shadow-lg">
                                        <svg class="h-10 w-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                        </svg>
                                    </div>
                                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">Teaching</h3>
                                    <p class="text-gray-600 dark:text-gray-300 mb-6 leading-relaxed">
                                        Empower teachers to plan lessons, upload notes, record videos, and interact with
                                        students in
                                        real-time or asynchronously. Perfect for classroom or remote learning.
                                    </p>
                                    <div class="space-y-3">
                                        <div class="flex items-center text-sm text-gray-600 dark:text-gray-300">
                                            <svg class="h-4 w-4 text-green-500 mr-3" fill="none" stroke="currentColor"
                                                 viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M5 13l4 4L19 7"></path>
                                            </svg>
                                            Lesson Planning
                                        </div>
                                        <div class="flex items-center text-sm text-gray-600 dark:text-gray-300">
                                            <svg class="h-4 w-4 text-green-500 mr-3" fill="none" stroke="currentColor"
                                                 viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M5 13l4 4L19 7"></path>
                                            </svg>
                                            Video Recording
                                        </div>
                                        <div class="flex items-center text-sm text-gray-600 dark:text-gray-300">
                                            <svg class="h-4 w-4 text-green-500 mr-3" fill="none" stroke="currentColor"
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
                                    class="group bg-white dark:bg-gray-800 rounded-3xl shadow-xl hover:shadow-2xl transition-all duration-500 border border-gray-200 dark:border-gray-700 hover:border-purple-300 dark:hover:border-purple-600 p-8 transform hover:scale-105">
                                    <div
                                        class="flex items-center justify-center h-20 w-20 rounded-3xl bg-gradient-to-r from-purple-500 to-violet-600 text-white mb-8 group-hover:scale-110 transition-transform duration-300 shadow-lg">
                                        <svg class="h-10 w-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                                        </svg>
                                    </div>
                                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">Quizzes</h3>
                                    <p class="text-gray-600 dark:text-gray-300 mb-6 leading-relaxed">
                                        Create and assign interactive quizzes automatically graded for instant feedback.
                                        Teachers
                                        can track scores and identify weak areas to support learners better.
                                    </p>
                                    <div class="space-y-3">
                                        <div class="flex items-center text-sm text-gray-600 dark:text-gray-300">
                                            <svg class="h-4 w-4 text-green-500 mr-3" fill="none" stroke="currentColor"
                                                 viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M5 13l4 4L19 7"></path>
                                            </svg>
                                            Auto-grading
                                        </div>
                                        <div class="flex items-center text-sm text-gray-600 dark:text-gray-300">
                                            <svg class="h-4 w-4 text-green-500 mr-3" fill="none" stroke="currentColor"
                                                 viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M5 13l4 4L19 7"></path>
                                            </svg>
                                            Instant Feedback
                                        </div>
                                        <div class="flex items-center text-sm text-gray-600 dark:text-gray-300">
                                            <svg class="h-4 w-4 text-green-500 mr-3" fill="none" stroke="currentColor"
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
                                    class="group bg-white dark:bg-gray-800 rounded-3xl shadow-xl hover:shadow-2xl transition-all duration-500 border border-gray-200 dark:border-gray-700 hover:border-orange-300 dark:hover:border-orange-600 p-8 transform hover:scale-105">
                                    <div
                                        class="flex items-center justify-center h-20 w-20 rounded-3xl bg-gradient-to-r from-orange-500 to-red-600 text-white mb-8 group-hover:scale-110 transition-transform duration-300 shadow-lg">
                                        <svg class="h-10 w-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                    </div>
                                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">Examinations</h3>
                                    <p class="text-gray-600 dark:text-gray-300 mb-6 leading-relaxed">
                                        Securely set and manage exams with multiple formats—MCQs, essays, theory &
                                        practicals.
                                        Schedule exams, auto-mark scripts, and generate report cards instantly.
                                    </p>
                                    <div class="space-y-3">
                                        <div class="flex items-center text-sm text-gray-600 dark:text-gray-300">
                                            <svg class="h-4 w-4 text-green-500 mr-3" fill="none" stroke="currentColor"
                                                 viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M5 13l4 4L19 7"></path>
                                            </svg>
                                            Multiple Formats
                                        </div>
                                        <div class="flex items-center text-sm text-gray-600 dark:text-gray-300">
                                            <svg class="h-4 w-4 text-green-500 mr-3" fill="none" stroke="currentColor"
                                                 viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M5 13l4 4L19 7"></path>
                                            </svg>
                                            Secure Environment
                                        </div>
                                        <div class="flex items-center text-sm text-gray-600 dark:text-gray-300">
                                            <svg class="h-4 w-4 text-green-500 mr-3" fill="none" stroke="currentColor"
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
                                    class="group bg-white dark:bg-gray-800 rounded-3xl shadow-xl hover:shadow-2xl transition-all duration-500 border border-gray-200 dark:border-gray-700 hover:border-indigo-300 dark:hover:border-indigo-600 p-8 transform hover:scale-105">
                                    <div
                                        class="flex items-center justify-center h-20 w-20 rounded-3xl bg-gradient-to-r from-indigo-500 to-blue-600 text-white mb-8 group-hover:scale-110 transition-transform duration-300 shadow-lg">
                                        <svg class="h-10 w-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                        </svg>
                                    </div>
                                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">Monitoring &
                                        Reporting</h3>
                                    <p class="text-gray-600 dark:text-gray-300 mb-6 leading-relaxed">
                                        Real-time analytics to track student engagement, teacher activity, and
                                        school-wide
                                        performance. Instantly spot trends and get custom reports.
                                    </p>
                                    <div class="space-y-3">
                                        <div class="flex items-center text-sm text-gray-600 dark:text-gray-300">
                                            <svg class="h-4 w-4 text-green-500 mr-3" fill="none" stroke="currentColor"
                                                 viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M5 13l4 4L19 7"></path>
                                            </svg>
                                            Real-time Analytics
                                        </div>
                                        <div class="flex items-center text-sm text-gray-600 dark:text-gray-300">
                                            <svg class="h-4 w-4 text-green-500 mr-3" fill="none" stroke="currentColor"
                                                 viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M5 13l4 4L19 7"></path>
                                            </svg>
                                            Performance Tracking
                                        </div>
                                        <div class="flex items-center text-sm text-gray-600 dark:text-gray-300">
                                            <svg class="h-4 w-4 text-green-500 mr-3" fill="none" stroke="currentColor"
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
                                    class="group bg-white dark:bg-gray-800 rounded-3xl shadow-xl hover:shadow-2xl transition-all duration-500 border border-gray-200 dark:border-gray-700 hover:border-emerald-300 dark:hover:border-emerald-600 p-8 transform hover:scale-105">
                                    <div
                                        class="flex items-center justify-center h-20 w-20 rounded-3xl bg-gradient-to-r from-emerald-500 to-teal-600 text-white mb-8 group-hover:scale-110 transition-transform duration-300 shadow-lg">
                                        <svg class="h-10 w-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                                        </svg>
                                    </div>
                                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">Books &
                                        Marketplace</h3>
                                    <p class="text-gray-600 dark:text-gray-300 mb-6 leading-relaxed">
                                        A digital bookstore where authors and publishers can sell or offer books
                                        (textbooks, novels,
                                        poems, short stories, etc). Schools and students can buy or download books
                                        easily.
                                    </p>
                                    <div class="space-y-3">
                                        <div class="flex items-center text-sm text-gray-600 dark:text-gray-300">
                                            <svg class="h-4 w-4 text-green-500 mr-3" fill="none" stroke="currentColor"
                                                 viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M5 13l4 4L19 7"></path>
                                            </svg>
                                            Digital Marketplace
                                        </div>
                                        <div class="flex items-center text-sm text-gray-600 dark:text-gray-300">
                                            <svg class="h-4 w-4 text-green-500 mr-3" fill="none" stroke="currentColor"
                                                 viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M5 13l4 4L19 7"></path>
                                            </svg>
                                            Author Publishing
                                        </div>
                                        <div class="flex items-center text-sm text-gray-600 dark:text-gray-300">
                                            <svg class="h-4 w-4 text-green-500 mr-3" fill="none" stroke="currentColor"
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
                                class="bg-white dark:bg-gray-800 rounded-3xl  p-8 border border-gray-200 dark:border-gray-700">
                                <div class="text-center mb-12">
                                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white">Additional Platform
                                        Features</h3>
                                    <p class="text-gray-600 dark:text-gray-300">Explore more tools and features that
                                        enhance your
                                        learning experience</p>
                                </div>

                                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6 gap-6">
                                    <!-- Content Management -->
                                    <div
                                        class="group text-center p-4 rounded-2xl hover:bg-gray-50 dark:hover:bg-gray-700 transition-all duration-300 cursor-pointer">
                                        <div
                                            class="flex items-center justify-center h-16 w-16 rounded-2xl bg-gradient-to-r from-cyan-500 to-blue-500 text-white mx-auto mb-4 group-hover:scale-110 transition-transform duration-300">
                                            <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                            </svg>
                                        </div>
                                        <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-2">Content
                                            Management</h4>
                                        <p class="text-xs text-gray-600 dark:text-gray-400">Upload & organize
                                            materials</p>
                                    </div>

                                    <!-- Communication -->
                                    <div
                                        class="group text-center p-4 rounded-2xl hover:bg-gray-50 dark:hover:bg-gray-700 transition-all duration-300 cursor-pointer">
                                        <div
                                            class="flex items-center justify-center h-16 w-16 rounded-2xl bg-gradient-to-r from-pink-500 to-rose-500 text-white mx-auto mb-4 group-hover:scale-110 transition-transform duration-300">
                                            <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                                            </svg>
                                        </div>
                                        <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-2">
                                            Communication</h4>
                                        <p class="text-xs text-gray-600 dark:text-gray-400">Messaging & forums</p>
                                    </div>

                                    <!-- Student Portal -->
                                    <div
                                        class="group text-center p-4 rounded-2xl hover:bg-gray-50 dark:hover:bg-gray-700 transition-all duration-300 cursor-pointer">
                                        <div
                                            class="flex items-center justify-center h-16 w-16 rounded-2xl bg-gradient-to-r from-lime-500 to-green-500 text-white mx-auto mb-4 group-hover:scale-110 transition-transform duration-300">
                                            <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                            </svg>
                                        </div>
                                        <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-2">Student
                                            Portal</h4>
                                        <p class="text-xs text-gray-600 dark:text-gray-400">Personalized space</p>
                                    </div>

                                    <!-- Teacher CPD -->
                                    <div
                                        class="group text-center p-4 rounded-2xl hover:bg-gray-50 dark:hover:bg-gray-700 transition-all duration-300 cursor-pointer">
                                        <div
                                            class="flex items-center justify-center h-16 w-16 rounded-2xl bg-gradient-to-r from-amber-500 to-orange-500 text-white mx-auto mb-4 group-hover:scale-110 transition-transform duration-300">
                                            <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M12 14l9-5-9-5-9 5 9 5z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M12 14l6.16-3.422A12.083 12.083 0 0112 8.624 12.083 12.083 0 015.84 10.578L12 14z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422A12.083 12.083 0 0112 8.624a12.083 12.083 0 00-6.16 1.954L12 14zm-4 6v-7.5l4-2.222"/>
                                            </svg>
                                        </div>
                                        <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-2">Teacher
                                            CPD</h4>
                                        <p class="text-xs text-gray-600 dark:text-gray-400">Professional development</p>
                                    </div>

                                    <!-- Mock Exams -->
                                    <div
                                        class="group text-center p-4 rounded-2xl hover:bg-gray-50 dark:hover:bg-gray-700 transition-all duration-300 cursor-pointer">
                                        <div
                                            class="flex items-center justify-center h-16 w-16 rounded-2xl bg-gradient-to-r from-violet-500 to-purple-500 text-white mx-auto mb-4 group-hover:scale-110 transition-transform duration-300">
                                            <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                            </svg>
                                        </div>
                                        <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-2">Mock
                                            Exams</h4>
                                        <p class="text-xs text-gray-600 dark:text-gray-400">BECE/WASSCE practice</p>
                                    </div>

                                    <!-- National Curriculum -->
                                    <div
                                        class="group text-center p-4 rounded-2xl hover:bg-gray-50 dark:hover:bg-gray-700 transition-all duration-300 cursor-pointer">
                                        <div
                                            class="flex items-center justify-center h-16 w-16 rounded-2xl bg-gradient-to-r from-teal-500 to-cyan-500 text-white mx-auto mb-4 group-hover:scale-110 transition-transform duration-300">
                                            <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                                            </svg>
                                        </div>
                                        <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-2">National
                                            Curriculum</h4>
                                        <p class="text-xs text-gray-600 dark:text-gray-400">Aligned standards</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Call to Action for Modules -->
                            <div class="mt-20 relative">
                                <!-- Subtle Background Elements -->
                                <div class="absolute inset-0 overflow-hidden pointer-events-none">
                                    <div
                                        class="absolute -top-40 -right-40 w-80 h-80 bg-blue-50/30 dark:bg-blue-900/10 rounded-full blur-3xl"></div>
                                    <div
                                        class="absolute -bottom-40 -left-40 w-80 h-80 bg-gray-50/40 dark:bg-gray-800/20 rounded-full blur-3xl"></div>
                                </div>

                                <div class="relative max-w-6xl mx-auto text-center">
                                    <!-- Clean CTA Card -->
                                    <div
                                        class="group relative overflow-hidden bg-white dark:bg-gray-800 rounded-3xl shadow-xl border border-gray-100 dark:border-gray-700 transform hover:scale-105 transition-all duration-500">
                                        <!-- Subtle Pattern Overlay -->
                                        <div class="absolute inset-0 opacity-[0.02] dark:opacity-[0.05]">
                                            <div
                                                class="absolute inset-0 bg-gradient-to-br from-gray-900 to-gray-600"></div>
                                        </div>

                                        <!-- Minimal Border Effect -->
                                        <div
                                            class="absolute inset-0 rounded-3xl ring-1 ring-gray-200/50 dark:ring-gray-700/50 group-hover:ring-blue-200/50 dark:group-hover:ring-blue-700/50 transition-all duration-500"></div>

                                        <!-- Subtle Shine Effect -->
                                        <div class="absolute inset-0 rounded-3xl overflow-hidden">
                                            <div
                                                class="absolute inset-0 -skew-x-12 -translate-x-full group-hover:translate-x-full transition-transform duration-1000 bg-gradient-to-r from-transparent via-white/10 dark:via-white/5 to-transparent"></div>
                                        </div>

                                        <div class="relative p-12 lg:p-16 text-gray-900 dark:text-white">
                                            <!-- Simple Icon -->
                                            <div class="mb-8 flex justify-center">
                                                <div class="relative">
                                                    <div
                                                        class="w-20 h-20 bg-blue-50 dark:bg-blue-900/20 rounded-2xl flex items-center justify-center shadow-sm border border-blue-100 dark:border-blue-800/50">
                                                        <svg class="w-10 h-10 text-blue-600 dark:text-blue-400"
                                                             fill="none"
                                                             stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                  stroke-width="2"
                                                                  d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                                        </svg>
                                                    </div>
                                                    <!-- Single accent dot -->
                                                    <div
                                                        class="absolute -top-1 -right-1 w-3 h-3 bg-blue-500 rounded-full"></div>
                                                </div>
                                            </div>

                                            <!-- Clean Heading -->
                                            <h3 class="text-4xl lg:text-5xl font-bold mb-6 leading-tight">
                                                Ready to
                                                <span class="relative inline-block text-blue-600 dark:text-blue-400">
                        Transform
                        <svg class="absolute -bottom-2 left-0 w-full h-2 text-blue-200 dark:text-blue-800/50"
                             viewBox="0 0 100 8" fill="none">
                            <path d="M2 6C20 2 40 2 60 6C70 2 80 2 98 6" stroke="currentColor" stroke-width="2"
                                  stroke-linecap="round"/>
                        </svg>
                    </span>
                                                <br>Education?
                                            </h3>

                                            <!-- Simple Description -->
                                            <div class="max-w-3xl mx-auto mb-10">
                                                <p class="text-xl lg:text-2xl mb-6 text-gray-600 dark:text-gray-300 leading-relaxed">
                                                    Join <span class="font-bold text-blue-600 dark:text-blue-400">thousands</span>
                                                    of educators and learners who are already experiencing the power of
                                                    our
                                                    comprehensive platform.
                                                </p>

                                                <!-- Clean Stats Row -->
                                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 mb-8">
                                                    <div
                                                        class="text-center p-4 bg-gray-50 dark:bg-gray-700/30 rounded-xl">
                                                        <div
                                                            class="text-2xl font-bold text-blue-600 dark:text-blue-400">
                                                            Thousands
                                                        </div>
                                                        <div class="text-sm text-gray-600 dark:text-gray-400">Resources
                                                            Available
                                                        </div>
                                                    </div>
                                                    <div
                                                        class="text-center p-4 bg-gray-50 dark:bg-gray-700/30 rounded-xl">
                                                        <div
                                                            class="text-2xl font-bold text-blue-600 dark:text-blue-400">
                                                            500+
                                                        </div>
                                                        <div class="text-sm text-gray-600 dark:text-gray-400">Expert
                                                            Authors
                                                        </div>
                                                    </div>
                                                    <div
                                                        class="text-center p-4 bg-gray-50 dark:bg-gray-700/30 rounded-xl">
                                                        <div
                                                            class="text-2xl font-bold text-blue-600 dark:text-blue-400">
                                                            4.9★
                                                        </div>
                                                        <div class="text-sm text-gray-600 dark:text-gray-400">User
                                                            Rating
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Clean Action Buttons -->
                                            <div class="flex flex-col sm:flex-row gap-6 justify-center items-center">
                                                <!-- Primary CTA -->
                                                <a href="{{ route('register') }}"
                                                   class="group relative inline-flex items-center justify-center px-10 py-5 text-lg font-bold rounded-2xl bg-blue-600 text-white shadow-lg hover:bg-blue-700 hover:shadow-xl transform hover:scale-105 hover:-translate-y-1 transition-all duration-300 min-w-[200px]">
                                                    <svg
                                                        class="w-6 h-6 mr-3 transform group-hover:translate-x-1 transition-transform duration-300"
                                                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                              stroke-width="2"
                                                              d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                                    </svg>
                                                    <span>Start Your Journey</span>
                                                </a>

                                                <!-- Secondary CTA -->
                                                <a href="{{route('branding.features')}}"
                                                   class="group inline-flex items-center justify-center px-10 py-5 text-lg font-semibold rounded-2xl border-2 border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 bg-transparent hover:bg-gray-50 dark:hover:bg-gray-700/30 hover:border-blue-300 dark:hover:border-blue-600 shadow-md hover:shadow-lg transition-all duration-300 min-w-[200px]">
                                                    <svg
                                                        class="w-6 h-6 mr-3 transform group-hover:rotate-12 transition-transform duration-300"
                                                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                              stroke-width="2"
                                                              d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                              stroke-width="2"
                                                              d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                    </svg>
                                                    Explore Features
                                                </a>
                                            </div>

                                            <!-- Simple Trust Indicators -->
                                            <div class="mt-12 pt-8 border-t border-gray-200 dark:border-gray-700">
                                                <div
                                                    class="flex items-center justify-center space-x-8 text-sm text-gray-500 dark:text-gray-400">
                                                    <div class="flex items-center space-x-2">
                                                        <svg class="w-4 h-4 text-green-500" fill="currentColor"
                                                             viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd"
                                                                  d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                                  clip-rule="evenodd"/>
                                                        </svg>
                                                        <span>Free 30-day trial</span>
                                                    </div>
                                                    <div class="flex items-center space-x-2">
                                                        <svg class="w-4 h-4 text-green-500" fill="currentColor"
                                                             viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd"
                                                                  d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                                  clip-rule="evenodd"/>
                                                        </svg>
                                                        <span>No credit card required</span>
                                                    </div>
                                                    <div class="flex items-center space-x-2">
                                                        <svg class="w-4 h-4 text-green-500" fill="currentColor"
                                                             viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd"
                                                                  d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                                  clip-rule="evenodd"/>
                                                        </svg>
                                                        <span>Cancel anytime</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Minimal Social Proof Section -->
                                    <div
                                        class="mt-12 flex items-center justify-center space-x-6 text-gray-500 dark:text-gray-400">
                                        <div class="flex items-center space-x-2">
                                            <span class="text-sm font-medium">Trusted by leading institutions:</span>
                                        </div>
                                        <div class="flex -space-x-2">
                                            <div
                                                class="w-8 h-8 bg-blue-50 dark:bg-blue-900/30 border-2 border-blue-200 dark:border-blue-800 rounded-full flex items-center justify-center text-xs font-bold text-blue-600 dark:text-blue-400">
                                                U1
                                            </div>
                                            <div
                                                class="w-8 h-8 bg-gray-50 dark:bg-gray-700 border-2 border-gray-200 dark:border-gray-600 rounded-full flex items-center justify-center text-xs font-bold text-gray-600 dark:text-gray-400">
                                                S2
                                            </div>
                                            <div
                                                class="w-8 h-8 bg-blue-50 dark:bg-blue-900/30 border-2 border-blue-200 dark:border-blue-800 rounded-full flex items-center justify-center text-xs font-bold text-blue-600 dark:text-blue-400">
                                                C3
                                            </div>
                                            <div
                                                class="w-8 h-8 bg-gray-100 dark:bg-gray-600 border-2 border-gray-300 dark:border-gray-500 rounded-full flex items-center justify-center text-xs font-bold text-gray-600 dark:text-gray-300">
                                                +50
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <style>
                            .animation-delay-1000 {
                                animation-delay: 1s;
                            }

                            .animation-delay-2000 {
                                animation-delay: 2s;
                            }

                            @keyframes float {
                                0%, 100% {
                                    transform: translateY(0px) rotate(0deg);
                                }
                                50% {
                                    transform: translateY(-10px) rotate(2deg);
                                }
                            }

                            .animate-float {
                                animation: float 6s ease-in-out infinite;
                            }
                        </style>
                    </div>

                </div>
                <!-- Pricing Section -->
                <div id="pricing" class="py-24 bg-white dark:bg-gray-900 transition-colors duration-300">
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                        <div class="text-center mb-20">
                            <div
                                class="inline-flex items-center px-4 py-2 rounded-full bg-purple-100 dark:bg-purple-900/30 text-purple-800 dark:text-purple-200 font-semibold text-sm mb-4">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Pricing Plans
                            </div>
                            <h2 class="text-4xl font-extrabold text-gray-900 dark:text-white sm:text-5xl mb-6">
                                Simple, Transparent <span
                                    class="text-transparent bg-clip-text bg-gradient-to-r from-purple-600 to-blue-600">Pricing</span>
                            </h2>
                            <p class="max-w-3xl text-xl text-gray-600 dark:text-gray-300 mx-auto leading-relaxed">
                                Choose the plan that fits your learning needs. Full access, no hidden fees, cancel
                                anytime.
                                Start with our 30-day money-back guarantee.
                            </p>
                        </div>
                        <section>
                            @include('branding.partials.pricing')
                        </section>

                        <div class="mt-16 text-center">
                            <div
                                class="inline-flex items-center space-x-3 bg-green-50 dark:bg-green-900/20 text-green-800 dark:text-green-200 px-6 py-3 rounded-full">
                                <svg class="w-6 h-6 text-green-500" fill="none" stroke="currentColor"
                                     viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span class="font-semibold">Cancellation follows our <a
                                        href="{{route('branding.terms')}}"
                                        class="text-blue-600 dark:text-blue-400">Terms & Conditions</a></span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Testimonials Section -->
                <div
                    class="py-24 bg-gradient-to-br from-blue-50 to-green-50 dark:from-gray-800 dark:to-gray-900 transition-colors duration-300">
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                        <div class="text-center mb-20">
                            <div
                                class="inline-flex items-center px-4 py-2 rounded-full bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-200 font-semibold text-sm mb-4">
                                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path
                                        d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                                What Our Users Say
                            </div>
                            <h2 class="text-4xl font-extrabold text-gray-900 dark:text-white sm:text-5xl mb-6">
                                Trusted by <span
                                    class="text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-green-600">1000+ Learners</span>
                            </h2>
                            <p class="max-w-3xl text-xl text-gray-600 dark:text-gray-300 mx-auto leading-relaxed">
                                Don't just take our word for it. See what our community of learners, educators, and
                                professionals have to say about their experience.
                            </p>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                            <!-- Testimonial 1 -->
                            <div
                                class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-8 border border-gray-200 dark:border-gray-700">
                                <div class="flex items-center space-x-1 mb-4">
                                    <svg class="w-5 h-5 text-yellow-400 fill-current" viewBox="0 0 20 20">
                                        <path
                                            d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/>
                                    </svg>
                                    <svg class="w-5 h-5 text-yellow-400 fill-current" viewBox="0 0 20 20">
                                        <path
                                            d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/>
                                    </svg>
                                    <svg class="w-5 h-5 text-yellow-400 fill-current" viewBox="0 0 20 20">
                                        <path
                                            d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/>
                                    </svg>
                                    <svg class="w-5 h-5 text-yellow-400 fill-current" viewBox="0 0 20 20">
                                        <path
                                            d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/>
                                    </svg>
                                    <svg class="w-5 h-5 text-yellow-400 fill-current" viewBox="0 0 20 20">
                                        <path
                                            d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/>
                                    </svg>
                                </div>
                                <p class="text-gray-600 dark:text-gray-300 mb-6 leading-relaxed">
                                    "All Academies has revolutionized my learning experience. The vast library and
                                    expert
                                    authors have helped me advance my career in data science significantly."
                                </p>
                                <div class="flex items-center">
                                    <img class="w-12 h-12 rounded-full mr-4"
                                         src="https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=48&h=48&fit=crop&crop=face"
                                         alt="Sarah Johnson">
                                    <div>
                                        <div class="font-semibold text-gray-900 dark:text-white">Sarah Johnson</div>
                                        <div class="text-sm text-gray-600 dark:text-gray-400">Data Scientist</div>
                                    </div>
                                </div>
                            </div>

                            <!-- Testimonial 2 -->
                            <div
                                class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-8 border border-gray-200 dark:border-gray-700">
                                <div class="flex items-center space-x-1 mb-4">
                                    <svg class="w-5 h-5 text-yellow-400 fill-current" viewBox="0 0 20 20">
                                        <path
                                            d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/>
                                    </svg>
                                    <svg class="w-5 h-5 text-yellow-400 fill-current" viewBox="0 0 20 20">
                                        <path
                                            d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/>
                                    </svg>
                                    <svg class="w-5 h-5 text-yellow-400 fill-current" viewBox="0 0 20 20">
                                        <path
                                            d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/>
                                    </svg>
                                    <svg class="w-5 h-5 text-yellow-400 fill-current" viewBox="0 0 20 20">
                                        <path
                                            d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/>
                                    </svg>
                                    <svg class="w-5 h-5 text-yellow-400 fill-current" viewBox="0 0 20 20">
                                        <path
                                            d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/>
                                    </svg>
                                </div>
                                <p class="text-gray-600 dark:text-gray-300 mb-6 leading-relaxed">
                                    "As a university professor, I appreciate the quality and depth of content available.
                                    The
                                    interactive features have transformed how I teach my courses."
                                </p>
                                <div class="flex items-center">
                                    <img class="w-12 h-12 rounded-full mr-4"
                                         src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=48&h=48&fit=crop&crop=face"
                                         alt="Dr. Michael Chen">
                                    <div>
                                        <div class="font-semibold text-gray-900 dark:text-white">Dr. Michael Chen</div>
                                        <div class="text-sm text-gray-600 dark:text-gray-400">University Professor</div>
                                    </div>
                                </div>
                            </div>

                            <!-- Testimonial 3 -->
                            <div
                                class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-8 border border-gray-200 dark:border-gray-700">
                                <div class="flex items-center space-x-1 mb-4">
                                    <svg class="w-5 h-5 text-yellow-400 fill-current" viewBox="0 0 20 20">
                                        <path
                                            d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/>
                                    </svg>
                                    <svg class="w-5 h-5 text-yellow-400 fill-current" viewBox="0 0 20 20">
                                        <path
                                            d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/>
                                    </svg>
                                    <svg class="w-5 h-5 text-yellow-400 fill-current" viewBox="0 0 20 20">
                                        <path
                                            d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/>
                                    </svg>
                                    <svg class="w-5 h-5 text-yellow-400 fill-current" viewBox="0 0 20 20">
                                        <path
                                            d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/>
                                    </svg>
                                    <svg class="w-5 h-5 text-yellow-400 fill-current" viewBox="0 0 20 20">
                                        <path
                                            d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/>
                                    </svg>
                                </div>
                                <p class="text-gray-600 dark:text-gray-300 mb-6 leading-relaxed">
                                    "The mobile app makes learning so convenient. I can study during my commute and the
                                    offline
                                    feature is a game-changer for my busy schedule."
                                </p>
                                <div class="flex items-center">
                                    <img class="w-12 h-12 rounded-full mr-4"
                                         src="https://images.unsplash.com/photo-1494790108755-2616b612b786?w=48&h=48&fit=crop&crop=face"
                                         alt="Emily Rodriguez">
                                    <div>
                                        <div class="font-semibold text-gray-900 dark:text-white">Emily Rodriguez</div>
                                        <div class="text-sm text-gray-600 dark:text-gray-400">MBA Student</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Public Payment Section -->
                <section>
                    @include('branding.partials.donation-support')
                </section>


                <!-- FAQ Section -->
                <div id="faq" class="py-24 bg-white dark:bg-gray-900 transition-colors duration-300">
                    @include('faq')
                </div>

                <!-- Custom Styles and Animations -->
                <style>
                    @keyframes blob {
                        0% {
                            transform: translate(0px, 0px) scale(1);
                        }
                        33% {
                            transform: translate(30px, -50px) scale(1.1);
                        }
                        66% {
                            transform: translate(-20px, 20px) scale(0.9);
                        }
                        100% {
                            transform: translate(0px, 0px) scale(1);
                        }
                    }

                    .animate-blob {
                        animation: blob 7s infinite;
                    }

                    .animation-delay-2000 {
                        animation-delay: 2s;
                    }

                    .animation-delay-4000 {
                        animation-delay: 4s;
                    }

                    /* Smooth scrolling */
                    html {
                        scroll-behavior: smooth;
                    }

                    /* Custom gradient text animation */
                    @keyframes gradient {
                        0%, 100% {
                            background-position: 0% 50%;
                        }
                        50% {
                            background-position: 100% 50%;
                        }
                    }

                    .bg-gradient-animate {
                        background: linear-gradient(-45deg, #ee7752, #e73c7e, #23a6d5, #23d5ab);
                        background-size: 400% 400%;
                        animation: gradient 10s ease infinite;
                    }

                    /* Dark mode improvements */
                    @media (prefers-color-scheme: dark) {
                        .animate-blob {
                            opacity: 0.6;
                        }
                    }

                    /* Responsive improvements */
                    @media (max-width: 640px) {
                        .animate-blob {
                            width: 200px;
                            height: 200px;
                        }
                    }
                </style>
            </div>
        </div>
        <!-- Footer -->
        <x-layouts.footer/>
    </div>
</x-app>
