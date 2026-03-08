<x-layouts.guest page-name="For Students & Learners">
    <!-- Hero Section -->
    <section class="relative bg-gray-900 py-32 lg:py-40 overflow-hidden">
        <div class="absolute inset-0">
            <img src="https://images.unsplash.com/photo-1503676260728-1c00da094a0b?w=1920&h=1080&fit=crop" 
                 alt="Students learning" 
                 class="w-full h-full object-cover">
            <div class="absolute inset-0 bg-gradient-to-r from-green-900/70 to-blue-900/70"></div>
        </div>
        
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="max-w-3xl">
                <h1 class="text-4xl lg:text-6xl font-bold text-white mb-6">
                    Learn Smarter with <span class="text-transparent bg-clip-text bg-gradient-to-r from-green-300 to-blue-300">AI-Powered Education</span>
                </h1>
                <p class="text-xl text-gray-200 mb-8">
                    Access interactive lessons, AI tutoring, digital books, and personalized learning experiences.
                </p>
                <div class="flex flex-wrap gap-4">
                    <a href="{{ route('register.guest') }}" class="px-8 py-4 bg-white text-green-600 font-semibold rounded-xl hover:shadow-xl transition-all">
                        Start Learning Free
                    </a>
                    <a href="{{ route('branding.features') }}" class="px-8 py-4 border-2 border-white text-white font-semibold rounded-xl hover:bg-white/10 transition-all">
                        Explore Platform
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Grid -->
    <section class="py-20 bg-white dark:bg-gray-900">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl lg:text-5xl font-bold text-gray-900 dark:text-white mb-4">Everything You Need to Excel</h2>
                <p class="text-xl text-gray-600 dark:text-gray-400">Powerful learning tools designed for students</p>
            </div>
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                <div class="group p-8 bg-gradient-to-br from-green-50 to-white dark:from-gray-800 dark:to-gray-800 rounded-2xl hover:shadow-2xl transition-all duration-300 border border-gray-100 dark:border-gray-700 hover:scale-105">
                    <div class="w-16 h-16 bg-gradient-to-br from-green-500 to-green-600 rounded-xl flex items-center justify-center text-4xl mb-6 group-hover:scale-110 transition-transform">🤖</div>
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-3">AI Academic Chat</h3>
                    <p class="text-gray-600 dark:text-gray-400 leading-relaxed">Get instant help with homework, explanations, and study guidance from AI.</p>
                </div>
                <div class="group p-8 bg-gradient-to-br from-blue-50 to-white dark:from-gray-800 dark:to-gray-800 rounded-2xl hover:shadow-2xl transition-all duration-300 border border-gray-100 dark:border-gray-700 hover:scale-105">
                    <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center text-4xl mb-6 group-hover:scale-110 transition-transform">📖</div>
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-3">Digital Books</h3>
                    <p class="text-gray-600 dark:text-gray-400 leading-relaxed">Access thousands of textbooks, novels, and educational materials.</p>
                </div>
                <div class="group p-8 bg-gradient-to-br from-purple-50 to-white dark:from-gray-800 dark:to-gray-800 rounded-2xl hover:shadow-2xl transition-all duration-300 border border-gray-100 dark:border-gray-700 hover:scale-105">
                    <div class="w-16 h-16 bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl flex items-center justify-center text-4xl mb-6 group-hover:scale-110 transition-transform">✍️</div>
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-3">Assignments & Quizzes</h3>
                    <p class="text-gray-600 dark:text-gray-400 leading-relaxed">Complete assignments, take quizzes, and track your progress.</p>
                </div>
                <div class="group p-8 bg-gradient-to-br from-yellow-50 to-white dark:from-gray-800 dark:to-gray-800 rounded-2xl hover:shadow-2xl transition-all duration-300 border border-gray-100 dark:border-gray-700 hover:scale-105">
                    <div class="w-16 h-16 bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-xl flex items-center justify-center text-4xl mb-6 group-hover:scale-110 transition-transform">📊</div>
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-3">Performance Analytics</h3>
                    <p class="text-gray-600 dark:text-gray-400 leading-relaxed">Monitor your grades, attendance, and academic progress.</p>
                </div>
                <div class="group p-8 bg-gradient-to-br from-red-50 to-white dark:from-gray-800 dark:to-gray-800 rounded-2xl hover:shadow-2xl transition-all duration-300 border border-gray-100 dark:border-gray-700 hover:scale-105">
                    <div class="w-16 h-16 bg-gradient-to-br from-red-500 to-red-600 rounded-xl flex items-center justify-center text-4xl mb-6 group-hover:scale-110 transition-transform">💬</div>
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-3">Collaboration Tools</h3>
                    <p class="text-gray-600 dark:text-gray-400 leading-relaxed">Chat with classmates, join forums, and share notes.</p>
                </div>
                <div class="group p-8 bg-gradient-to-br from-indigo-50 to-white dark:from-gray-800 dark:to-gray-800 rounded-2xl hover:shadow-2xl transition-all duration-300 border border-gray-100 dark:border-gray-700 hover:scale-105">
                    <div class="w-16 h-16 bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-xl flex items-center justify-center text-4xl mb-6 group-hover:scale-110 transition-transform">🎥</div>
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-3">Virtual Classes</h3>
                    <p class="text-gray-600 dark:text-gray-400 leading-relaxed">Attend live sessions, watch recordings, and engage with teachers.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Guest Access -->
    <section class="py-20 bg-gray-50 dark:bg-gray-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl lg:text-5xl font-bold text-gray-900 dark:text-white mb-4">Open to Everyone</h2>
                <p class="text-xl text-gray-600 dark:text-gray-400">Not enrolled in a school? Learn as a guest!</p>
            </div>
            <div class="grid md:grid-cols-2 gap-8">
                <div class="bg-white dark:bg-gray-900 p-10 rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 border border-gray-100 dark:border-gray-700">
                    <h3 class="text-3xl font-bold text-gray-900 dark:text-white mb-6">🌍 Guest Learning</h3>
                    <ul class="space-y-4 text-gray-600 dark:text-gray-400">
                        <li class="flex items-start gap-4"><span class="text-green-600 text-2xl font-bold">✓</span><span class="text-lg">Access free educational content</span></li>
                        <li class="flex items-start gap-4"><span class="text-green-600 text-2xl font-bold">✓</span><span class="text-lg">Subscribe to premium courses and books</span></li>
                        <li class="flex items-start gap-4"><span class="text-green-600 text-2xl font-bold">✓</span><span class="text-lg">Use AI research assistant</span></li>
                        <li class="flex items-start gap-4"><span class="text-green-600 text-2xl font-bold">✓</span><span class="text-lg">Learn at your own pace</span></li>
                    </ul>
                </div>
                <div class="bg-white dark:bg-gray-900 p-10 rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 border border-gray-100 dark:border-gray-700">
                    <h3 class="text-3xl font-bold text-gray-900 dark:text-white mb-6">🎓 Enrolled Students</h3>
                    <ul class="space-y-4 text-gray-600 dark:text-gray-400">
                        <li class="flex items-start gap-4"><span class="text-green-600 text-2xl font-bold">✓</span><span class="text-lg">Full access to school curriculum</span></li>
                        <li class="flex items-start gap-4"><span class="text-green-600 text-2xl font-bold">✓</span><span class="text-lg">Submit assignments and take exams</span></li>
                        <li class="flex items-start gap-4"><span class="text-green-600 text-2xl font-bold">✓</span><span class="text-lg">Receive grades and report cards</span></li>
                        <li class="flex items-start gap-4"><span class="text-green-600 text-2xl font-bold">✓</span><span class="text-lg">Connect with teachers and classmates</span></li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-20 bg-gradient-to-r from-green-600 to-blue-600">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-3xl lg:text-5xl font-bold text-white mb-6">Begin Your Learning Journey</h2>
            <p class="text-xl text-green-100 mb-8">Join millions of students learning on {{ config('app.name') }}</p>
            <a href="{{ route('register.guest') }}" class="inline-block px-8 py-4 bg-white text-green-600 font-semibold rounded-xl hover:shadow-2xl transition-all">
                Sign Up Free
            </a>
        </div>
    </section>
</x-layouts.guest>
