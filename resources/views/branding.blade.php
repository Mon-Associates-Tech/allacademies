<x-app>
    <div class="bg-white dark:bg-gray-900 transition-colors duration-300 overflow-x-hidden">
        @include('branding.partials.header')

        <!-- Hero Section -->
        <section id="home" class="relative min-h-screen flex items-center justify-center overflow-hidden">
            <div class="absolute inset-0 z-0">
                <video autoplay muted loop class="w-full h-full object-cover">
                    <source src="{{ asset('media/video/header-background-video.mp4') }}" type="video/mp4">
                </video>
                <div class="absolute inset-0 bg-gradient-to-r from-blue-600/60 via-indigo-600/50 to-purple-600/60"></div>
            </div>

            <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-32 text-center">
                <h1 class="text-5xl md:text-7xl font-black text-white mb-6 leading-tight">
                    Join <span class="text-yellow-300">{{ number_format($usersCount ?? 0) }}</span> Users<br>
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-200 via-purple-200 to-pink-200">Transforming Education</span>
                </h1>
                
                <p class="text-xl md:text-2xl text-gray-100 mb-12 max-w-3xl mx-auto">
                    Access comprehensive digital resources, expert content, and cutting-edge learning tools
                </p>

                <div class="flex flex-col sm:flex-row gap-4 justify-center mb-8">
                    <a href="{{ route('register') }}" class="px-10 py-5 bg-white text-blue-600 font-bold text-lg rounded-2xl hover:shadow-2xl transform hover:scale-105 transition-all duration-300">
                        Start Learning Today
                    </a>
                    <a href="#features" class="px-10 py-5 border-2 border-white text-white font-bold text-lg rounded-2xl hover:bg-white/10 backdrop-blur-sm transition-all duration-300">
                        Explore Features
                    </a>
                </div>

                <div class="flex items-center justify-center space-x-2">
                    <div class="flex">
                        @for($i = 0; $i < 5; $i++)
                            <svg class="w-6 h-6 text-yellow-400 fill-current" viewBox="0 0 20 20">
                                <path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/>
                            </svg>
                        @endfor
                    </div>
                    <span class="text-white font-semibold text-lg ml-3">4.9/5 Rating</span>
                </div>
            </div>
        </section>

        <!-- Stats Section -->
        <section class="py-16 hidden bg-gradient-to-r from-blue-600 to-purple-600">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center text-white">
                    <div>
                        <div class="text-4xl md:text-5xl font-black mb-2">15K+</div>
                        <div class="text-blue-100">Books Available</div>
                    </div>
                    <div>
                        <div class="text-4xl md:text-5xl font-black mb-2">500+</div>
                        <div class="text-blue-100">Expert Authors</div>
                    </div>
                    <div>
                        <div class="text-4xl md:text-5xl font-black mb-2">50+</div>
                        <div class="text-blue-100">Institutions</div>
                    </div>
                    <div>
                        <div class="text-4xl md:text-5xl font-black mb-2">24/7</div>
                        <div class="text-blue-100">AI Support</div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Video Demo Section -->
        <section id="demo" class="py-24 bg-white dark:bg-gray-900">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-16">
                    <div class="inline-flex items-center px-4 py-2 rounded-full bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-200 font-semibold text-sm mb-4">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Watch Demo
                    </div>
                    <h2 class="text-4xl md:text-5xl font-black text-gray-900 dark:text-white mb-4">
                        See the Platform <span class="text-transparent bg-clip-text bg-gradient-to-r from-red-600 to-orange-600">In Action</span>
                    </h2>
                    <p class="text-xl text-gray-600 dark:text-gray-300 max-w-3xl mx-auto">
                        Take a guided tour through our features and see how we transform the learning experience
                    </p>
                </div>

                <div class="max-w-5xl mx-auto">
                    <div class="relative rounded-3xl overflow-hidden shadow-2xl border border-gray-200 dark:border-gray-700 bg-gray-900">
                        <div class="aspect-w-16 aspect-h-9">
                            <video class="w-full h-full object-cover" controls preload="none" poster="{{ asset('images/students-crowded-around-computer.jpeg') }}?w=1280&h=720&fit=crop">
                                <source src="{{ asset('media/video/platform-demo.mp4') }}" type="video/mp4">
                                Your browser does not support the video tag.
                            </video>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Features Section -->
        <section id="features" class="py-24 bg-gray-50 dark:bg-gray-800">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-16">
                    <h2 class="text-4xl md:text-5xl font-black text-gray-900 dark:text-white mb-4">
                        Everything You Need to <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-purple-600">Excel</span>
                    </h2>
                    <p class="text-xl text-gray-600 dark:text-gray-300 max-w-3xl mx-auto">
                        Comprehensive platform combining technology with educational expertise
                    </p>
                </div>

                <div class="grid md:grid-cols-3 gap-8">
                    <div class="bg-white dark:bg-gray-900 rounded-3xl p-8 shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2">
                        <div class="w-16 h-16 bg-gradient-to-r from-blue-500 to-blue-600 rounded-2xl flex items-center justify-center mb-6">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                            </svg>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">Digital Library</h3>
                        <p class="text-gray-600 dark:text-gray-300 mb-6">Access 15,000+ curated educational resources across multiple disciplines</p>
                        <ul class="space-y-2">
                            <li class="flex items-center text-gray-600 dark:text-gray-300">
                                <svg class="w-5 h-5 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                AI-Powered Search
                            </li>
                            <li class="flex items-center text-gray-600 dark:text-gray-300">
                                <svg class="w-5 h-5 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                Offline Access
                            </li>
                            <li class="flex items-center text-gray-600 dark:text-gray-300">
                                <svg class="w-5 h-5 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                Daily Updates
                            </li>
                        </ul>
                    </div>

                    <div class="bg-white dark:bg-gray-900 rounded-3xl p-8 shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2">
                        <div class="w-16 h-16 bg-gradient-to-r from-green-500 to-green-600 rounded-2xl flex items-center justify-center mb-6">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                            </svg>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">Expert Authors</h3>
                        <p class="text-gray-600 dark:text-gray-300 mb-6">Learn from 500+ verified academics and industry professionals</p>
                        <ul class="space-y-2">
                            <li class="flex items-center text-gray-600 dark:text-gray-300">
                                <svg class="w-5 h-5 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                Verified Credentials
                            </li>
                            <li class="flex items-center text-gray-600 dark:text-gray-300">
                                <svg class="w-5 h-5 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                Direct Communication
                            </li>
                            <li class="flex items-center text-gray-600 dark:text-gray-300">
                                <svg class="w-5 h-5 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                Latest Research
                            </li>
                        </ul>
                    </div>

                    <div class="bg-white dark:bg-gray-900 rounded-3xl p-8 shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2">
                        <div class="w-16 h-16 bg-gradient-to-r from-purple-500 to-purple-600 rounded-2xl flex items-center justify-center mb-6">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                            </svg>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">Interactive Learning</h3>
                        <p class="text-gray-600 dark:text-gray-300 mb-6">Engage with quizzes, projects, and real-time discussions</p>
                        <ul class="space-y-2">
                            <li class="flex items-center text-gray-600 dark:text-gray-300">
                                <svg class="w-5 h-5 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                Smart Quizzes
                            </li>
                            <li class="flex items-center text-gray-600 dark:text-gray-300">
                                <svg class="w-5 h-5 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                Study Groups
                            </li>
                            <li class="flex items-center text-gray-600 dark:text-gray-300">
                                <svg class="w-5 h-5 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                Progress Analytics
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </section>

        <!-- Modules Section -->
        <section id="modules" class="py-24 bg-white dark:bg-gray-900">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-16">
                    <h2 class="text-4xl md:text-5xl font-black text-gray-900 dark:text-white mb-4">
                        Comprehensive Learning <span class="text-transparent bg-clip-text bg-gradient-to-r from-green-600 to-blue-600">Ecosystem</span>
                    </h2>
                    <p class="text-xl text-gray-600 dark:text-gray-300 max-w-3xl mx-auto">
                        Integrated modules supporting every aspect of your educational journey
                    </p>
                </div>

                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                    @php
                        $modules = [
                            ['name' => 'Administration', 'icon' => 'M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01', 'color' => 'from-blue-500 to-indigo-600', 'desc' => 'Smart dashboard for managing schools, teachers, students, and resources'],
                            ['name' => 'Teaching', 'icon' => 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253', 'color' => 'from-green-500 to-emerald-600', 'desc' => 'Plan lessons, upload notes, record videos, and interact with students'],
                            ['name' => 'Quizzes', 'icon' => 'M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z', 'color' => 'from-purple-500 to-violet-600', 'desc' => 'Create interactive quizzes with automatic grading and instant feedback'],
                            ['name' => 'Examinations', 'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z', 'color' => 'from-orange-500 to-red-600', 'desc' => 'Secure exam management with multiple formats and auto-marking'],
                            ['name' => 'Monitoring', 'icon' => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z', 'color' => 'from-indigo-500 to-blue-600', 'desc' => 'Real-time analytics to track engagement and performance'],
                            ['name' => 'Marketplace', 'icon' => 'M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z', 'color' => 'from-emerald-500 to-teal-600', 'desc' => 'Digital bookstore for authors, publishers, schools, and students'],
                        ];
                    @endphp

                    @foreach($modules as $module)
                        <div class="bg-gradient-to-br from-gray-50 to-white dark:from-gray-800 dark:to-gray-900 rounded-3xl p-8 shadow-lg hover:shadow-2xl transition-all duration-300 border border-gray-200 dark:border-gray-700 transform hover:scale-105">
                            <div class="w-16 h-16 bg-gradient-to-r {{ $module['color'] }} rounded-2xl flex items-center justify-center mb-6">
                                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $module['icon'] }}"/>
                                </svg>
                            </div>
                            <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-3">{{ $module['name'] }}</h3>
                            <p class="text-gray-600 dark:text-gray-300">{{ $module['desc'] }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        <!-- Financial Aid Section -->
        @include('branding.partials.donation-support')

        <!-- Pricing Section -->
        <section id="pricing" class="py-24 bg-white dark:bg-gray-900">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-16">
                    <h2 class="text-4xl md:text-5xl font-black text-gray-900 dark:text-white mb-4">
                        Simple, Transparent <span class="text-transparent bg-clip-text bg-gradient-to-r from-purple-600 to-blue-600">Pricing</span>
                    </h2>
                    <p class="text-xl text-gray-600 dark:text-gray-300 max-w-3xl mx-auto">
                        Choose the plan that fits your needs. Full access, no hidden fees, cancel anytime
                    </p>
                </div>
                @include('branding.partials.pricing')
            </div>
        </section>

        <!-- Testimonials Section -->
        <section class="py-24 bg-gradient-to-br from-blue-50 to-purple-50 dark:from-gray-800 dark:to-gray-900">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-16">
                    <h2 class="text-4xl md:text-5xl font-black text-gray-900 dark:text-white mb-4">
                        Trusted by <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-purple-600">Thousands</span>
                    </h2>
                    <p class="text-xl text-gray-600 dark:text-gray-300">See what our community says</p>
                </div>

                <div class="grid md:grid-cols-3 gap-8">
                    @php
                        $testimonials = [
                            ['name' => 'Sarah Johnson', 'role' => 'Data Scientist', 'image' => 'photo-1472099645785-5658abf4ff4e', 'text' => 'All Academies has revolutionized my learning experience. The vast library and expert authors have helped me advance my career significantly.'],
                            ['name' => 'Dr. Michael Chen', 'role' => 'University Professor', 'image' => 'photo-1507003211169-0a1dd7228f2d', 'text' => 'The quality and depth of content available is impressive. The interactive features have transformed how I teach my courses.'],
                            ['name' => 'Emily Rodriguez', 'role' => 'MBA Student', 'image' => 'photo-1494790108755-2616b612b786', 'text' => 'The mobile app makes learning so convenient. I can study during my commute and the offline feature is a game-changer.'],
                        ];
                    @endphp

                    @foreach($testimonials as $testimonial)
                        <div class="bg-white dark:bg-gray-800 rounded-3xl p-8 shadow-lg">
                            <div class="flex space-x-1 mb-4">
                                @for($i = 0; $i < 5; $i++)
                                    <svg class="w-5 h-5 text-yellow-400 fill-current" viewBox="0 0 20 20">
                                        <path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/>
                                    </svg>
                                @endfor
                            </div>
                            <p class="text-gray-600 dark:text-gray-300 mb-6">{{ $testimonial['text'] }}</p>
                            <div class="flex items-center">
                                <img class="w-12 h-12 rounded-full mr-4" src="https://images.unsplash.com/{{ $testimonial['image'] }}?w=48&h=48&fit=crop&crop=face" alt="{{ $testimonial['name'] }}">
                                <div>
                                    <div class="font-bold text-gray-900 dark:text-white">{{ $testimonial['name'] }}</div>
                                    <div class="text-sm text-gray-600 dark:text-gray-400">{{ $testimonial['role'] }}</div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        <!-- CTA Section -->
        <section class="py-24 bg-gradient-to-r from-blue-600 to-purple-600">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                <h2 class="text-4xl md:text-5xl font-black text-white mb-6">Ready to Transform Education?</h2>
                <p class="text-xl text-blue-100 mb-8">Join thousands of learners and educators already on the platform</p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="{{ route('register') }}" class="px-10 py-5 bg-white text-blue-600 font-bold text-lg rounded-2xl hover:shadow-2xl transform hover:scale-105 transition-all duration-300">
                        Start Your Journey
                    </a>
                    <a href="{{ route('branding.features') }}" class="px-10 py-5 border-2 border-white text-white font-bold text-lg rounded-2xl hover:bg-white/10 transition-all duration-300">
                        Explore Features
                    </a>
                </div>
                <div class="mt-8 flex items-center justify-center space-x-6 text-sm text-blue-100">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-green-300 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        Free 30-day trial
                    </div>
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-green-300 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        No credit card required
                    </div>
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-green-300 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        Cancel anytime
                    </div>
                </div>
            </div>
        </section>

        <!-- FAQ Section -->
        <section id="faq" class="py-24 bg-white dark:bg-gray-900">
            @include('faq')
        </section>

        <x-layouts.footer/>
    </div>

    <style>
        @keyframes blob {
            0%, 100% { transform: translate(0, 0) scale(1); }
            33% { transform: translate(30px, -50px) scale(1.1); }
            66% { transform: translate(-20px, 20px) scale(0.9); }
        }
        .animate-blob { animation: blob 7s infinite; }
        html { scroll-behavior: smooth; }
    </style>
</x-app>
