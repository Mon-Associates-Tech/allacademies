<x-layouts.guest>

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

        @keyframes float {
            0%, 100% {
                transform: translateY(0px);
            }
            50% {
                transform: translateY(-10px);
            }
        }

        @keyframes slideInLeft {
            from {
                transform: translateX(-100px);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        @keyframes slideInRight {
            from {
                transform: translateX(100px);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        @keyframes scaleIn {
            from {
                transform: scale(0.8);
                opacity: 0;
            }
            to {
                transform: scale(1);
                opacity: 1;
            }
        }

        @keyframes fadeInUp {
            from {
                transform: translateY(30px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .animate-blob {
            animation: blob 7s infinite;
        }

        .animate-float {
            animation: float 3s ease-in-out infinite;
        }

        .animation-delay-2000 {
            animation-delay: 2s;
        }

        .animation-delay-4000 {
            animation-delay: 4s;
        }

        .animation-delay-1000 {
            animation-delay: 1s;
        }

        .group:hover .group-hover\:scale-110 {
            transform: scale(1.1);
        }

        .group:hover .group-hover\:rotate-6 {
            transform: rotate(6deg);
        }

        .group:hover .group-hover\:-rotate-6 {
            transform: rotate(-6deg);
        }

        /* Custom AOS animations */
        [data-aos="slide-up-stagger"] {
            transform: translateY(50px);
            opacity: 0;
            transition-property: transform, opacity;
        }

        [data-aos="slide-up-stagger"].aos-animate {
            transform: translateY(0);
            opacity: 1;
        }

        [data-aos="zoom-in-rotate"] {
            transform: scale(0.6) rotate(-30deg);
            opacity: 0;
            transition-property: transform, opacity;
        }

        [data-aos="zoom-in-rotate"].aos-animate {
            transform: scale(1) rotate(0deg);
            opacity: 1;
        }

        [data-aos="flip-left"] {
            transform: perspective(400px) rotate3d(0, 1, 0, -100deg);
            opacity: 0;
            transition-property: transform, opacity;
        }

        [data-aos="flip-left"].aos-animate {
            transform: perspective(400px) rotate3d(0, 1, 0, 0deg);
            opacity: 1;
        }

        [data-aos="slide-in-bounce"] {
            transform: translateX(-100px) scale(0.8);
            opacity: 0;
            transition-property: transform, opacity;
            transition-timing-function: cubic-bezier(0.68, -0.55, 0.265, 1.55);
        }

        [data-aos="slide-in-bounce"].aos-animate {
            transform: translateX(0) scale(1);
            opacity: 1;
        }

        /* Stagger animation delays */
        .aos-stagger-50 [data-aos] {
            transition-delay: 0s;
        }

        .aos-stagger-50 [data-aos]:nth-child(2) {
            transition-delay: 0.05s;
        }

        .aos-stagger-50 [data-aos]:nth-child(3) {
            transition-delay: 0.1s;
        }

        .aos-stagger-50 [data-aos]:nth-child(4) {
            transition-delay: 0.15s;
        }

        .aos-stagger-100 [data-aos] {
            transition-delay: 0s;
        }

        .aos-stagger-100 [data-aos]:nth-child(2) {
            transition-delay: 0.1s;
        }

        .aos-stagger-100 [data-aos]:nth-child(3) {
            transition-delay: 0.2s;
        }

        .aos-stagger-100 [data-aos]:nth-child(4) {
            transition-delay: 0.3s;
        }

        .aos-stagger-100 [data-aos]:nth-child(5) {
            transition-delay: 0.4s;
        }

        .aos-stagger-100 [data-aos]:nth-child(6) {
            transition-delay: 0.5s;
        }

        .aos-stagger-100 [data-aos]:nth-child(7) {
            transition-delay: 0.6s;
        }

        .aos-stagger-100 [data-aos]:nth-child(8) {
            transition-delay: 0.7s;
        }

        /* Hover effect enhancements */
        .card-hover {
            transition: all 0.4s cubic-bezier(0.25, 0.8, 0.25, 1);
        }

        .card-hover:hover {
            transform: translateY(-8px) scale(1.02);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        }

        .icon-float {
            transition: transform 0.3s ease;
        }

        .icon-float:hover {
            transform: translateY(-5px);
        }
    </style>

    <div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-100">
        {{-- Hero Section --}}
        <section class="relative overflow-hidden bg-gradient-to-r from-blue-600 via-purple-600 to-indigo-700 py-20">
            <div class="absolute inset-0 bg-black/20"></div>
            <div class="absolute inset-0">
                <div
                    class="absolute top-0 left-0 w-72 h-72 bg-white/10 rounded-full mix-blend-multiply filter blur-xl opacity-70 animate-blob"></div>
                <div
                    class="absolute top-0 right-0 w-72 h-72 bg-yellow-300/10 rounded-full mix-blend-multiply filter blur-xl opacity-70 animate-blob animation-delay-2000"></div>
                <div
                    class="absolute -bottom-8 left-20 w-72 h-72 bg-pink-300/10 rounded-full mix-blend-multiply filter blur-xl opacity-70 animate-blob animation-delay-4000"></div>
            </div>

            <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                <h1 class="text-5xl md:text-7xl font-bold text-white mb-6 leading-tight"
                    data-aos="fade-up"
                    data-aos-duration="1000">
                    Complete Educational
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-yellow-400 to-orange-400"
                          data-aos="fade-up"
                          data-aos-delay="200">
                    Ecosystem
                </span>
                </h1>
                <p class="text-xl md:text-2xl text-white/90 mb-8 max-w-3xl mx-auto leading-relaxed"
                   data-aos="fade-up"
                   data-aos-delay="400"
                   data-aos-duration="1000">
                    Discover the comprehensive modules and powerful features that make All Academies
                    the ultimate platform for modern education management and learning.
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center"
                     data-aos="fade-up"
                     data-aos-delay="600"
                     data-aos-duration="1000">
                    <a href="{{ route('sign-up') }}"
                       class="inline-flex items-center px-8 py-4 bg-white text-blue-600 rounded-full font-semibold text-lg hover:bg-gray-50 transform hover:scale-105 transition-all duration-300 shadow-2xl animate-float">
                        Start Free Trial
                        <svg class="ml-2 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                        </svg>
                    </a>
                    <a href="#modules"
                       class="inline-flex items-center px-8 py-4 border-2 border-white/30 text-white rounded-full font-semibold text-lg hover:bg-white/10 transform hover:scale-105 transition-all duration-300 animate-float animation-delay-1000">
                        Explore Features
                        <svg class="ml-2 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M19 14l-7 7m0 0l-7-7m7 7V3"/>
                        </svg>
                    </a>
                </div>
            </div>
        </section>

        {{-- Key Stats Section --}}
        <section class="py-16 bg-white/80 backdrop-blur-sm">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-8 aos-stagger-50">
                    <div class="text-center"
                         data-aos="zoom-in-rotate"
                         data-aos-duration="800">
                        <div class="text-4xl font-bold text-blue-600 mb-2 icon-float">5+</div>
                        <div class="text-gray-600 font-medium">Core Modules</div>
                    </div>
                    <div class="text-center"
                         data-aos="zoom-in-rotate"
                         data-aos-duration="800"
                         data-aos-delay="100">
                        <div class="text-4xl font-bold text-purple-600 mb-2 icon-float">8+</div>
                        <div class="text-gray-600 font-medium">User Types</div>
                    </div>
                    <div class="text-center"
                         data-aos="zoom-in-rotate"
                         data-aos-duration="800"
                         data-aos-delay="200">
                        <div class="text-4xl font-bold text-indigo-600 mb-2 icon-float">50+</div>
                        <div class="text-gray-600 font-medium">Features</div>
                    </div>
                    <div class="text-center"
                         data-aos="zoom-in-rotate"
                         data-aos-duration="800"
                         data-aos-delay="300">
                        <div class="text-4xl font-bold text-green-600 mb-2 icon-float">∞</div>
                        <div class="text-gray-600 font-medium">Possibilities</div>
                    </div>
                </div>
            </div>
        </section>

        {{-- Main Modules Section --}}
        <section id="modules" class="py-20">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-16">
                    <h2 class="text-4xl md:text-5xl font-bold text-gray-900 mb-6"
                        data-aos="fade-up"
                        data-aos-duration="1000">
                        Powerful Modules for
                        <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-purple-600"
                              data-aos="fade-up"
                              data-aos-delay="200">
                        Complete Learning
                    </span>
                    </h2>
                    <p class="text-xl text-gray-600 max-w-3xl mx-auto"
                       data-aos="fade-up"
                       data-aos-delay="400"
                       data-aos-duration="1000">
                        Five comprehensive modules designed to cover every aspect of educational management and learning
                    </p>
                </div>

                {{-- Module Cards --}}
                <div class="space-y-20">
                    {{-- Module 1: Records + Reports + ERP --}}
                    <div class="group card-hover"
                         data-aos="slide-in-bounce"
                         data-aos-duration="1200">
                        <div class="bg-gradient-to-br from-blue-50 to-indigo-100 rounded-3xl p-8 md:p-12 shadow-lg">
                            <div class="grid lg:grid-cols-2 gap-12 items-center">
                                <div data-aos="fade-right"
                                     data-aos-delay="200"
                                     data-aos-duration="1000">
                                    <div
                                        class="inline-flex items-center px-4 py-2 bg-blue-100 text-blue-800 rounded-full text-sm font-medium mb-6">
                                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"/>
                                            <path fill-rule="evenodd"
                                                  d="M4 5a2 2 0 012-2v1a1 1 0 102 0V3a2 2 0 012 2v6a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm2.5 7a1.5 1.5 0 100-3 1.5 1.5 0 000 3zm2.45.4a.5.5 0 01.1-.8l2-1a.5.5 0 11.4.9l-2 1a.5.5 0 01-.5-.1z"
                                                  clip-rule="evenodd"/>
                                        </svg>
                                        Module 1
                                    </div>
                                    <h3 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
                                        Records + Reports + ERP
                                    </h3>
                                    <p class="text-lg text-gray-600 mb-6">
                                        Comprehensive enterprise resource planning for complete institutional management
                                    </p>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3 aos-stagger-50">
                                        <div class="flex items-center text-gray-700"
                                             data-aos="fade-up"
                                             data-aos-delay="300">
                                            <svg class="w-5 h-5 text-green-500 mr-3" fill="currentColor"
                                                 viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                      d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                                      clip-rule="evenodd"/>
                                            </svg>
                                            Staff & Student Enrollment
                                        </div>
                                        <div class="flex items-center text-gray-700"
                                             data-aos="fade-up"
                                             data-aos-delay="350">
                                            <svg class="w-5 h-5 text-green-500 mr-3" fill="currentColor"
                                                 viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                      d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                                      clip-rule="evenodd"/>
                                            </svg>
                                            ID Card Generation
                                        </div>
                                        <div class="flex items-center text-gray-700"
                                             data-aos="fade-up"
                                             data-aos-delay="400">
                                            <svg class="w-5 h-5 text-green-500 mr-3" fill="currentColor"
                                                 viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                      d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                                      clip-rule="evenodd"/>
                                            </svg>
                                            Exam Records & Grades
                                        </div>
                                        <div class="flex items-center text-gray-700"
                                             data-aos="fade-up"
                                             data-aos-delay="450">
                                            <svg class="w-5 h-5 text-green-500 mr-3" fill="currentColor"
                                                 viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                      d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                                      clip-rule="evenodd"/>
                                            </svg>
                                            Terminal Reports
                                        </div>
                                        <div class="flex items-center text-gray-700"
                                             data-aos="fade-up"
                                             data-aos-delay="500">
                                            <svg class="w-5 h-5 text-green-500 mr-3" fill="currentColor"
                                                 viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                      d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                                      clip-rule="evenodd"/>
                                            </svg>
                                            Event Notifications
                                        </div>
                                        <div class="flex items-center text-gray-700"
                                             data-aos="fade-up"
                                             data-aos-delay="550">
                                            <svg class="w-5 h-5 text-green-500 mr-3" fill="currentColor"
                                                 viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                      d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                                      clip-rule="evenodd"/>
                                            </svg>
                                            Financial Management
                                        </div>
                                    </div>
                                </div>
                                <div class="flex justify-center"
                                     data-aos="flip-left"
                                     data-aos-delay="300"
                                     data-aos-duration="1200">
                                    <div class="relative animate-float">
                                        <div
                                            class="w-64 h-64 bg-gradient-to-br from-blue-400 to-blue-600 rounded-2xl shadow-2xl transform rotate-3 group-hover:rotate-6 transition-transform duration-500"></div>
                                        <div
                                            class="absolute inset-4 bg-white rounded-xl flex items-center justify-center">
                                            <svg class="w-24 h-24 text-blue-600" fill="none" stroke="currentColor"
                                                 viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                                                      d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                            </svg>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Module 2: Teaching and Assessment --}}
                    <div class="group card-hover"
                         data-aos="slide-in-bounce"
                         data-aos-anchor-placement="top-bottom"
                         data-aos-duration="1200">
                        <div class="bg-gradient-to-br from-purple-50 to-pink-100 rounded-3xl p-8 md:p-12 shadow-lg">
                            <div class="grid lg:grid-cols-2 gap-12 items-center">
                                <div class="order-2 lg:order-1 flex justify-center"
                                     data-aos="flip-left"
                                     data-aos-delay="300"
                                     data-aos-duration="1200">
                                    <div class="relative animate-float animation-delay-1000">
                                        <div
                                            class="w-64 h-64 bg-gradient-to-br from-purple-400 to-pink-600 rounded-2xl shadow-2xl transform -rotate-3 group-hover:-rotate-6 transition-transform duration-500"></div>
                                        <div
                                            class="absolute inset-4 bg-white rounded-xl flex items-center justify-center">
                                            <svg class="w-24 h-24 text-purple-600" fill="none" stroke="currentColor"
                                                 viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                                                      d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                            </svg>
                                        </div>
                                    </div>
                                </div>
                                <div class="order-1 lg:order-2"
                                     data-aos="fade-left"
                                     data-aos-delay="200"
                                     data-aos-duration="1000">
                                    <div
                                        class="inline-flex items-center px-4 py-2 bg-purple-100 text-purple-800 rounded-full text-sm font-medium mb-6">
                                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                            <path
                                                d="M10.394 2.08a1 1 0 00-.788 0l-7 3a1 1 0 000 1.84L5.25 8.051a.999.999 0 01.356-.257l4-1.714a1 1 0 11.788 1.838L7.667 9.088l1.94.831a1 1 0 00.787 0l7-3a1 1 0 000-1.838l-7-3zM3.31 9.397L5 10.12v4.102a8.969 8.969 0 00-1.05-.174 1 1 0 01-.89-.89 11.115 11.115 0 01.25-3.762zM9.3 16.573A9.026 9.026 0 007 14.935v-3.957l1.818.78a3 3 0 002.364 0l5.508-2.361a11.026 11.026 0 01.25 3.762 1 1 0 01-.89.89 8.968 8.968 0 00-5.35 2.524 1 1 0 01-1.4 0zM6 18a1 1 0 001-1v-2.065a8.935 8.935 0 00-2-.712V17a1 1 0 001 1z"/>
                                        </svg>
                                        Module 2
                                    </div>
                                    <h3 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
                                        Teaching & Assessment
                                    </h3>
                                    <p class="text-lg text-gray-600 mb-6">
                                        Advanced teaching tools with AI-powered assessment and analytics
                                    </p>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3 aos-stagger-50">
                                        <div class="flex items-center text-gray-700"
                                             data-aos="fade-up"
                                             data-aos-delay="300">
                                            <svg class="w-5 h-5 text-green-500 mr-3" fill="currentColor"
                                                 viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                      d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                                      clip-rule="evenodd"/>
                                            </svg>
                                            Question Database
                                        </div>
                                        <div class="flex items-center text-gray-700"
                                             data-aos="fade-up"
                                             data-aos-delay="350">
                                            <svg class="w-5 h-5 text-green-500 mr-3" fill="currentColor"
                                                 viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                      d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                                      clip-rule="evenodd"/>
                                            </svg>
                                            Video/Audio Teaching
                                        </div>
                                        <div class="flex items-center text-gray-700"
                                             data-aos="fade-up"
                                             data-aos-delay="400">
                                            <svg class="w-5 h-5 text-green-500 mr-3" fill="currentColor"
                                                 viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                      d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                                      clip-rule="evenodd"/>
                                            </svg>
                                            Online Quizzes
                                        </div>
                                        <div class="flex items-center text-gray-700"
                                             data-aos="fade-up"
                                             data-aos-delay="450">
                                            <svg class="w-5 h-5 text-green-500 mr-3" fill="currentColor"
                                                 viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                      d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                                      clip-rule="evenodd"/>
                                            </svg>
                                            Auto-Generated Exams
                                        </div>
                                        <div class="flex items-center text-gray-700"
                                             data-aos="fade-up"
                                             data-aos-delay="500">
                                            <svg class="w-5 h-5 text-green-500 mr-3" fill="currentColor"
                                                 viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                      d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                                      clip-rule="evenodd"/>
                                            </svg>
                                            AI Self-Assessment
                                        </div>
                                        <div class="flex items-center text-gray-700"
                                             data-aos="fade-up"
                                             data-aos-delay="550">
                                            <svg class="w-5 h-5 text-green-500 mr-3" fill="currentColor"
                                                 viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                      d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                                      clip-rule="evenodd"/>
                                            </svg>
                                            Performance Analytics
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Module 3: Library + Book --}}
                    <div class="group card-hover"
                         data-aos="slide-in-bounce"
                         data-aos-anchor-placement="top-bottom"
                         data-aos-duration="1200">
                        <div class="bg-gradient-to-br from-green-50 to-teal-100 rounded-3xl p-8 md:p-12 shadow-lg">
                            <div class="grid lg:grid-cols-2 gap-12 items-center">
                                <div data-aos="fade-right"
                                     data-aos-delay="200"
                                     data-aos-duration="1000">
                                    <div
                                        class="inline-flex items-center px-4 py-2 bg-green-100 text-green-800 rounded-full text-sm font-medium mb-6">
                                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                            <path
                                                d="M9 4.804A7.968 7.968 0 005.5 4c-1.255 0-2.443.29-3.5.804v10A7.969 7.969 0 015.5 14c1.669 0 3.218.51 4.5 1.385A7.962 7.962 0 0114.5 14c1.255 0 2.443.29 3.5.804v-10A7.968 7.968 0 0014.5 4c-1.255 0-2.443.29-3.5.804V12a1 1 0 11-2 0V4.804z"/>
                                        </svg>
                                        Module 3
                                    </div>
                                    <h3 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
                                        Library + Book Management
                                    </h3>
                                    <p class="text-lg text-gray-600 mb-6">
                                        Complete digital and physical library management system
                                    </p>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3 aos-stagger-50">
                                        <div class="flex items-center text-gray-700"
                                             data-aos="fade-up"
                                             data-aos-delay="300">
                                            <svg class="w-5 h-5 text-green-500 mr-3" fill="currentColor"
                                                 viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                      d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                                      clip-rule="evenodd"/>
                                            </svg>
                                            Digital & Physical Books
                                        </div>
                                        <div class="flex items-center text-gray-700"
                                             data-aos="fade-up"
                                             data-aos-delay="350">
                                            <svg class="w-5 h-5 text-green-500 mr-3" fill="currentColor"
                                                 viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                      d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                                      clip-rule="evenodd"/>
                                            </svg>
                                            Online Reading
                                        </div>
                                        <div class="flex items-center text-gray-700"
                                             data-aos="fade-up"
                                             data-aos-delay="400">
                                            <svg class="w-5 h-5 text-green-500 mr-3" fill="currentColor"
                                                 viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                      d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                                      clip-rule="evenodd"/>
                                            </svg>
                                            Borrowing System
                                        </div>
                                        <div class="flex items-center text-gray-700"
                                             data-aos="fade-up"
                                             data-aos-delay="450">
                                            <svg class="w-5 h-5 text-green-500 mr-3" fill="currentColor"
                                                 viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                      d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                                      clip-rule="evenodd"/>
                                            </svg>
                                            Approval Workflow
                                        </div>
                                        <div class="flex items-center text-gray-700"
                                             data-aos="fade-up"
                                             data-aos-delay="500">
                                            <svg class="w-5 h-5 text-green-500 mr-3" fill="currentColor"
                                                 viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                      d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                                      clip-rule="evenodd"/>
                                            </svg>
                                            Return Monitoring
                                        </div>
                                        <div class="flex items-center text-gray-700"
                                             data-aos="fade-up"
                                             data-aos-delay="550">
                                            <svg class="w-5 h-5 text-green-500 mr-3" fill="currentColor"
                                                 viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                      d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                                      clip-rule="evenodd"/>
                                            </svg>
                                            Smart Notifications
                                        </div>
                                    </div>
                                </div>
                                <div class="flex justify-center"
                                     data-aos="flip-left"
                                     data-aos-delay="300"
                                     data-aos-duration="1200">
                                    <div class="relative animate-float animation-delay-2000">
                                        <div
                                            class="w-64 h-64 bg-gradient-to-br from-green-400 to-teal-600 rounded-2xl shadow-2xl transform rotate-3 group-hover:rotate-6 transition-transform duration-500"></div>
                                        <div
                                            class="absolute inset-4 bg-white rounded-xl flex items-center justify-center">
                                            <svg class="w-24 h-24 text-green-600" fill="none" stroke="currentColor"
                                                 viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                                                      d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z"/>
                                            </svg>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Module 4: Authors Books + Subscription --}}
                    <div class="group card-hover"
                         data-aos="slide-in-bounce"
                         data-aos-anchor-placement="top-bottom"
                         data-aos-duration="1200">
                        <div class="bg-gradient-to-br from-yellow-50 to-orange-100 rounded-3xl p-8 md:p-12 shadow-lg">
                            <div class="grid lg:grid-cols-2 gap-12 items-center">
                                <div class="order-2 lg:order-1 flex justify-center"
                                     data-aos="flip-left"
                                     data-aos-delay="300"
                                     data-aos-duration="1200">
                                    <div class="relative animate-float animation-delay-4000">
                                        <div
                                            class="w-64 h-64 bg-gradient-to-br from-yellow-400 to-orange-600 rounded-2xl shadow-2xl transform -rotate-3 group-hover:-rotate-6 transition-transform duration-500"></div>
                                        <div
                                            class="absolute inset-4 bg-white rounded-xl flex items-center justify-center">
                                            <svg class="w-24 h-24 text-orange-600" fill="none" stroke="currentColor"
                                                 viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                                                      d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                            </svg>
                                        </div>
                                    </div>
                                </div>
                                <div class="order-1 lg:order-2"
                                     data-aos="fade-left"
                                     data-aos-delay="200"
                                     data-aos-duration="1000">
                                    <div
                                        class="inline-flex items-center px-4 py-2 bg-yellow-100 text-yellow-800 rounded-full text-sm font-medium mb-6">
                                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                  d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zm0 4a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1V8zm8 0a1 1 0 011-1h6a1 1 0 011 1v2a1 1 0 01-1 1h-6a1 1 0 01-1-1V8zm0 4a1 1 0 011-1h6a1 1 0 011 1v2a1 1 0 01-1 1h-6a1 1 0 01-1-1v-2z"
                                                  clip-rule="evenodd"/>
                                        </svg>
                                        Module 4
                                    </div>
                                    <h3 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
                                        Author Books + Subscriptions
                                    </h3>
                                    <p class="text-lg text-gray-600 mb-6">
                                        Marketplace for authors with flexible subscription models
                                    </p>
                                    <div class="grid grid-cols-1 gap-3">
                                        <div class="flex items-center text-gray-700"
                                             data-aos="fade-up"
                                             data-aos-delay="300">
                                            <svg class="w-5 h-5 text-green-500 mr-3" fill="currentColor"
                                                 viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                      d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                                      clip-rule="evenodd"/>
                                            </svg>
                                            Private Author Books for Subscription/Purchase
                                        </div>
                                        <div class="flex items-center text-gray-700"
                                             data-aos="fade-up"
                                             data-aos-delay="350">
                                            <svg class="w-5 h-5 text-green-500 mr-3" fill="currentColor"
                                                 viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                      d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                                      clip-rule="evenodd"/>
                                            </svg>
                                            Free Books (Government, NGOs, All Academies)
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Module 5: Performance Monitoring --}}
                    <div class="group card-hover"
                         data-aos="slide-in-bounce"
                         data-aos-anchor-placement="top-bottom"
                         data-aos-duration="1200">
                        <div class="bg-gradient-to-br from-indigo-50 to-purple-100 rounded-3xl p-8 md:p-12 shadow-lg">
                            <div class="grid lg:grid-cols-2 gap-12 items-center">
                                <div data-aos="fade-right"
                                     data-aos-delay="200"
                                     data-aos-duration="1000">
                                    <div
                                        class="inline-flex items-center px-4 py-2 bg-indigo-100 text-indigo-800 rounded-full text-sm font-medium mb-6">
                                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                            <path
                                                d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zM8 7a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zM14 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z"/>
                                        </svg>
                                        Module 5
                                    </div>
                                    <h3 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
                                        Performance Monitoring
                                    </h3>
                                    <p class="text-lg text-gray-600 mb-6">
                                        Advanced analytics and performance tracking for comprehensive insights
                                    </p>
                                    <div class="grid grid-cols-1 gap-3">
                                        <div class="flex items-center text-gray-700"
                                             data-aos="fade-up"
                                             data-aos-delay="300">
                                            <svg class="w-5 h-5 text-green-500 mr-3" fill="currentColor"
                                                 viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                      d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                                      clip-rule="evenodd"/>
                                            </svg>
                                            Student Performance Records (Quizzes & Assignments)
                                        </div>
                                        <div class="flex items-center text-gray-700"
                                             data-aos="fade-up"
                                             data-aos-delay="350">
                                            <svg class="w-5 h-5 text-green-500 mr-3" fill="currentColor"
                                                 viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                      d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                                      clip-rule="evenodd"/>
                                            </svg>
                                            Comprehensive Performance Analytics
                                        </div>
                                    </div>
                                </div>
                                <div class="flex justify-center"
                                     data-aos="flip-left"
                                     data-aos-delay="300"
                                     data-aos-duration="1200">
                                    <div class="relative animate-float animation-delay-1000">
                                        <div
                                            class="w-64 h-64 bg-gradient-to-br from-indigo-400 to-purple-600 rounded-2xl shadow-2xl transform rotate-3 group-hover:rotate-6 transition-transform duration-500"></div>
                                        <div
                                            class="absolute inset-4 bg-white rounded-xl flex items-center justify-center">
                                            <svg class="w-24 h-24 text-indigo-600" fill="none" stroke="currentColor"
                                                 viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                                                      d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                            </svg>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- User Types Section --}}
        <section class="py-20 bg-gradient-to-br from-gray-50 to-blue-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-16">
                    <h2 class="text-4xl md:text-5xl font-bold text-gray-900 mb-6"
                        data-aos="fade-up"
                        data-aos-duration="1000">
                        Built for
                        <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-purple-600"
                              data-aos="fade-up"
                              data-aos-delay="200">
                        Everyone
                    </span>
                    </h2>
                    <p class="text-xl text-gray-600 max-w-3xl mx-auto"
                       data-aos="fade-up"
                       data-aos-delay="400"
                       data-aos-duration="1000">
                        Eight distinct user types, each with tailored features and capabilities
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 aos-stagger-100">
                    {{-- School Administrators --}}
                    <div class="group bg-white rounded-2xl p-6 shadow-lg card-hover"
                         data-aos="slide-up-stagger"
                         data-aos-duration="800">
                        <div
                            class="w-16 h-16 bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform duration-300 icon-float">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-3">School Administrators</h3>
                        <p class="text-gray-600 text-sm mb-4">Complete institutional management and oversight</p>
                        <ul class="text-sm text-gray-600 space-y-1">
                            <li>• Enrollment management</li>
                            <li>• Teacher assignments</li>
                            <li>• Performance monitoring</li>
                            <li>• Financial oversight</li>
                        </ul>
                    </div>

                    {{-- Teachers --}}
                    <div class="group bg-white rounded-2xl p-6 shadow-lg card-hover"
                         data-aos="slide-up-stagger"
                         data-aos-duration="800">
                        <div
                            class="w-16 h-16 bg-gradient-to-br from-green-500 to-green-600 rounded-2xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform duration-300 icon-float">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-3">Teachers</h3>
                        <p class="text-gray-600 text-sm mb-4">Comprehensive teaching and assessment tools</p>
                        <ul class="text-sm text-gray-600 space-y-1">
                            <li>• Online teaching</li>
                            <li>• Quiz & exam creation</li>
                            <li>• Student performance tracking</li>
                            <li>• Lesson planning</li>
                        </ul>
                    </div>

                    {{-- Students --}}
                    <div class="group bg-white rounded-2xl p-6 shadow-lg card-hover"
                         data-aos="slide-up-stagger"
                         data-aos-duration="800">
                        <div
                            class="w-16 h-16 bg-gradient-to-br from-purple-500 to-purple-600 rounded-2xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform duration-300 icon-float">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-3">Students</h3>
                        <p class="text-gray-600 text-sm mb-4">Interactive learning and self-assessment</p>
                        <ul class="text-sm text-gray-600 space-y-1">
                            <li>• Online classes</li>
                            <li>• Self-assessment with AI</li>
                            <li>• Digital library access</li>
                            <li>• Discussion forums</li>
                        </ul>
                    </div>

                    {{-- Parents --}}
                    <div class="group bg-white rounded-2xl p-6 shadow-lg card-hover"
                         data-aos="slide-up-stagger"
                         data-aos-duration="800">
                        <div
                            class="w-16 h-16 bg-gradient-to-br from-pink-500 to-pink-600 rounded-2xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform duration-300 icon-float">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-3">Parents</h3>
                        <p class="text-gray-600 text-sm mb-4">Monitor and support child's education</p>
                        <ul class="text-sm text-gray-600 space-y-1">
                            <li>• Ward's performance</li>
                            <li>• Terminal reports</li>
                            <li>• Event notifications</li>
                            <li>• Book subscriptions</li>
                        </ul>
                    </div>

                    {{-- Librarians --}}
                    <div class="group bg-white rounded-2xl p-6 shadow-lg card-hover"
                         data-aos="slide-up-stagger"
                         data-aos-duration="800">
                        <div
                            class="w-16 h-16 bg-gradient-to-br from-teal-500 to-teal-600 rounded-2xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform duration-300 icon-float">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z"/>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-3">Librarians</h3>
                        <p class="text-gray-600 text-sm mb-4">Digital and physical library management</p>
                        <ul class="text-sm text-gray-600 space-y-1">
                            <li>• Inventory management</li>
                            <li>• Book approvals</li>
                            <li>• Borrowing monitoring</li>
                            <li>• Digital uploads</li>
                        </ul>
                    </div>

                    {{-- Book Authors --}}
                    <div class="group bg-white rounded-2xl p-6 shadow-lg card-hover"
                         data-aos="slide-up-stagger"
                         data-aos-duration="800">
                        <div
                            class="w-16 h-16 bg-gradient-to-br from-orange-500 to-orange-600 rounded-2xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform duration-300 icon-float">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-3">Book Authors</h3>
                        <p class="text-gray-600 text-sm mb-4">Publish and monetize educational content</p>
                        <ul class="text-sm text-gray-600 space-y-1">
                            <li>• Book submissions</li>
                            <li>• Sales analytics</li>
                            <li>• Revenue tracking</li>
                            <li>• Content management</li>
                        </ul>
                    </div>

                    {{-- Accounts Department --}}
                    <div class="group bg-white rounded-2xl p-6 shadow-lg card-hover"
                         data-aos="slide-up-stagger"
                         data-aos-duration="800">
                        <div
                            class="w-16 h-16 bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-2xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform duration-300 icon-float">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-3">Accounts Department</h3>
                        <p class="text-gray-600 text-sm mb-4">Financial and business transaction management</p>
                        <ul class="text-sm text-gray-600 space-y-1">
                            <li>• Financial tracking</li>
                            <li>• Transaction processing</li>
                            <li>• Revenue management</li>
                            <li>• Financial reporting</li>
                        </ul>
                    </div>

                    {{-- General Public --}}
                    <div class="group bg-white rounded-2xl p-6 shadow-lg card-hover"
                         data-aos="slide-up-stagger"
                         data-aos-duration="800">
                        <div
                            class="w-16 h-16 bg-gradient-to-br from-gray-500 to-gray-600 rounded-2xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform duration-300 icon-float">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-3">General Public</h3>
                        <p class="text-gray-600 text-sm mb-4">Access to free educational resources</p>
                        <ul class="text-sm text-gray-600 space-y-1">
                            <li>• Free book access</li>
                            <li>• Book subscriptions</li>
                            <li>• Educational content</li>
                            <li>• Community features</li>
                        </ul>
                    </div>
                </div>
            </div>
        </section>

        {{-- CTA Section --}}
        <section class="py-20 bg-gradient-to-r from-blue-600 via-purple-600 to-indigo-700 relative overflow-hidden">
            <div class="absolute inset-0 bg-black/20"></div>
            <div class="absolute inset-0">
                <div
                    class="absolute bottom-0 left-0 w-96 h-96 bg-white/5 rounded-full mix-blend-multiply filter blur-3xl opacity-70"></div>
                <div
                    class="absolute top-0 right-0 w-96 h-96 bg-yellow-300/5 rounded-full mix-blend-multiply filter blur-3xl opacity-70"></div>
            </div>

            <div class="relative max-w-4xl mx-auto text-center px-4 sm:px-6 lg:px-8">
                <h2 class="text-4xl md:text-5xl font-bold text-white mb-6"
                    data-aos="fade-up"
                    data-aos-duration="1000">
                    Ready to Transform Education?
                </h2>
                <p class="text-xl text-white/90 mb-8 max-w-2xl mx-auto"
                   data-aos="fade-up"
                   data-aos-delay="200"
                   data-aos-duration="1000">
                    Join thousands of institutions already using All Academies to revolutionize their educational
                    processes.
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center"
                     data-aos="fade-up"
                     data-aos-delay="400"
                     data-aos-duration="1000">
                    <a href="{{ route('sign-up') }}"
                       class="inline-flex items-center px-8 py-4 bg-white text-blue-600 rounded-full font-semibold text-lg hover:bg-gray-50 transform hover:scale-105 transition-all duration-300 shadow-2xl">
                        Get Started Free
                        <svg class="ml-2 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                        </svg>
                    </a>
                    <a href="{{ route('branding.contact') }}"
                       class="inline-flex items-center px-8 py-4 border-2 border-white/30 text-white rounded-full font-semibold text-lg hover:bg-white/10 transform hover:scale-105 transition-all duration-300">
                        Contact Sales
                        <svg class="ml-2 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                        </svg>
                    </a>
                </div>
            </div>
        </section>
    </div>


    <script>
        document.addEventListener('DOMContentLoaded', function () {

            window.AOS.init({
                duration: 800,
                easing: 'ease-in-out',
                once: true,
                mirror: false,
                anchorPlacement: 'top-bottom',
                offset: 100,
            });

            // Custom scroll-triggered animations
            window.addEventListener('scroll', function () {
                const scrolled = window.pageYOffset;
                const parallax = document.querySelectorAll('.animate-float');
                const speed = 0.5;

                parallax.forEach(element => {
                    const yPos = -(scrolled * speed);
                    element.style.transform = `translateY(${yPos}px)`;
                });
            });

            // Add intersection observer for custom animations
            const observerOptions = {
                threshold: 0.1,
                rootMargin: '0px 0px -50px 0px'
            };

            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('animate-in');
                    }
                });
            }, observerOptions);

            // Observe all module cards
            document.querySelectorAll('.card-hover').forEach(card => {
                observer.observe(card);
            });
        });

    </script>

</x-layouts.guest>
