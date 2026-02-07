<x-layouts.guest page-name="For Schools">
    <!-- Hero Section -->
    <section class="relative bg-gray-900 py-32 lg:py-40 overflow-hidden">
        <div class="absolute inset-0">
            <img src="https://images.unsplash.com/photo-1580582932707-520aed937b7b?w=1920&h=1080&fit=crop" 
                 alt="School building" 
                 class="w-full h-full object-cover">
            <div class="absolute inset-0 bg-gradient-to-r from-blue-900/70 to-purple-900/70"></div>
        </div>
        
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="max-w-3xl">
                <h1 class="text-4xl lg:text-6xl font-bold text-white mb-6">
                    Complete School Management <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-300 to-purple-300">Made Simple</span>
                </h1>
                <p class="text-xl text-gray-200 mb-8">
                    Streamline operations, enhance learning outcomes, and manage your entire institution from one powerful platform.
                </p>
                <div class="flex flex-wrap gap-4">
                    <a href="{{ route('register.school') }}" class="px-8 py-4 bg-white text-blue-600 font-semibold rounded-xl hover:shadow-xl transition-all">
                        Start Free Trial
                    </a>
                    <a href="{{ route('branding.contact') }}" class="px-8 py-4 border-2 border-white text-white font-semibold rounded-xl hover:bg-white/10 transition-all">
                        Schedule Demo
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Grid -->
    <section class="py-20 bg-white dark:bg-gray-900">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl lg:text-5xl font-bold text-gray-900 dark:text-white mb-4">Everything Your School Needs</h2>
                <p class="text-xl text-gray-600 dark:text-gray-400">Comprehensive tools for modern educational institutions</p>
            </div>
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                <div class="group p-8 bg-gradient-to-br from-blue-50 to-white dark:from-gray-800 dark:to-gray-800 rounded-2xl hover:shadow-2xl transition-all duration-300 border border-gray-100 dark:border-gray-700 hover:scale-105">
                    <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center text-4xl mb-6 group-hover:scale-110 transition-transform">📚</div>
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-3">Academic Management</h3>
                    <p class="text-gray-600 dark:text-gray-400 leading-relaxed">Manage academic years, periods, levels, groups, subjects, and topics with ease.</p>
                </div>
                <div class="group p-8 bg-gradient-to-br from-purple-50 to-white dark:from-gray-800 dark:to-gray-800 rounded-2xl hover:shadow-2xl transition-all duration-300 border border-gray-100 dark:border-gray-700 hover:scale-105">
                    <div class="w-16 h-16 bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl flex items-center justify-center text-4xl mb-6 group-hover:scale-110 transition-transform">👨🎓</div>
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-3">Student Information System</h3>
                    <p class="text-gray-600 dark:text-gray-400 leading-relaxed">Complete student records, enrollment, progression tracking, and report cards.</p>
                </div>
                <div class="group p-8 bg-gradient-to-br from-green-50 to-white dark:from-gray-800 dark:to-gray-800 rounded-2xl hover:shadow-2xl transition-all duration-300 border border-gray-100 dark:border-gray-700 hover:scale-105">
                    <div class="w-16 h-16 bg-gradient-to-br from-green-500 to-green-600 rounded-xl flex items-center justify-center text-4xl mb-6 group-hover:scale-110 transition-transform">💳</div>
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-3">Financial Management</h3>
                    <p class="text-gray-600 dark:text-gray-400 leading-relaxed">Fee structures, payment tracking, Paystack integration, and financial reporting.</p>
                </div>
                <div class="group p-8 bg-gradient-to-br from-yellow-50 to-white dark:from-gray-800 dark:to-gray-800 rounded-2xl hover:shadow-2xl transition-all duration-300 border border-gray-100 dark:border-gray-700 hover:scale-105">
                    <div class="w-16 h-16 bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-xl flex items-center justify-center text-4xl mb-6 group-hover:scale-110 transition-transform">📊</div>
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-3">Reports & Analytics</h3>
                    <p class="text-gray-600 dark:text-gray-400 leading-relaxed">Comprehensive insights into academic performance, attendance, and operations.</p>
                </div>
                <div class="group p-8 bg-gradient-to-br from-red-50 to-white dark:from-gray-800 dark:to-gray-800 rounded-2xl hover:shadow-2xl transition-all duration-300 border border-gray-100 dark:border-gray-700 hover:scale-105">
                    <div class="w-16 h-16 bg-gradient-to-br from-red-500 to-red-600 rounded-xl flex items-center justify-center text-4xl mb-6 group-hover:scale-110 transition-transform">✉️</div>
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-3">Communication Hub</h3>
                    <p class="text-gray-600 dark:text-gray-400 leading-relaxed">Internal messaging, notifications, and parent-teacher communication.</p>
                </div>
                <div class="group p-8 bg-gradient-to-br from-indigo-50 to-white dark:from-gray-800 dark:to-gray-800 rounded-2xl hover:shadow-2xl transition-all duration-300 border border-gray-100 dark:border-gray-700 hover:scale-105">
                    <div class="w-16 h-16 bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-xl flex items-center justify-center text-4xl mb-6 group-hover:scale-110 transition-transform">📖</div>
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-3">Digital Library</h3>
                    <p class="text-gray-600 dark:text-gray-400 leading-relaxed">Book catalog, borrowing system, and digital subscriptions for students.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-20 bg-gradient-to-r from-blue-600 to-purple-600">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-3xl lg:text-5xl font-bold text-white mb-6">Ready to Transform Your School?</h2>
            <p class="text-xl text-blue-100 mb-8">Join hundreds of schools already using {{ config('app.name') }}</p>
            <a href="{{ route('register.school') }}" class="inline-block px-8 py-4 bg-white text-blue-600 font-semibold rounded-xl hover:shadow-2xl transition-all">
                Get Started Today
            </a>
        </div>
    </section>
</x-layouts.guest>
