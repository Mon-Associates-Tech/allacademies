<x-layouts.guest page-name="For Teachers & Authors">
    <!-- Hero Section -->
    <section class="relative bg-gray-900 py-32 lg:py-40 overflow-hidden">
        <div class="absolute inset-0">
            <img src="https://images.unsplash.com/photo-1524178232363-1fb2b075b655?w=1920&h=1080&fit=crop" 
                 alt="Teacher with students" 
                 class="w-full h-full object-cover">
            <div class="absolute inset-0 bg-gradient-to-r from-purple-900/70 to-blue-900/70"></div>
        </div>
        
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="max-w-3xl">
                <h1 class="text-4xl lg:text-6xl font-bold text-white mb-6">
                    Empower Your Teaching <span class="text-transparent bg-clip-text bg-gradient-to-r from-purple-300 to-blue-300">& Monetize Content</span>
                </h1>
                <p class="text-xl text-gray-200 mb-8">
                    Create engaging lessons, assess students with AI, and publish educational content for free or for a fee.
                </p>
                <div class="flex flex-wrap gap-4">
                    <a href="{{ route('register.author') }}" class="px-8 py-4 bg-white text-purple-600 font-semibold rounded-xl hover:shadow-xl transition-all">
                        Start Creating
                    </a>
                    <a href="{{ route('branding.features') }}" class="px-8 py-4 border-2 border-white text-white font-semibold rounded-xl hover:bg-white/10 transition-all">
                        Explore Features
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Grid -->
    <section class="py-20 bg-white dark:bg-gray-900">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl lg:text-5xl font-bold text-gray-900 dark:text-white mb-4">Tools for Modern Educators</h2>
                <p class="text-xl text-gray-600 dark:text-gray-400">Everything you need to teach effectively and earn from your expertise</p>
            </div>
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                <div class="group p-8 bg-gradient-to-br from-purple-50 to-white dark:from-gray-800 dark:to-gray-800 rounded-2xl hover:shadow-2xl transition-all duration-300 border border-gray-100 dark:border-gray-700 hover:scale-105">
                    <div class="w-16 h-16 bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl flex items-center justify-center text-4xl mb-6 group-hover:scale-110 transition-transform">📝</div>
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-3">Lesson Planning</h3>
                    <p class="text-gray-600 dark:text-gray-400 leading-relaxed">Create structured lessons with topics, subtopics, and learning objectives.</p>
                </div>
                <div class="group p-8 bg-gradient-to-br from-blue-50 to-white dark:from-gray-800 dark:to-gray-800 rounded-2xl hover:shadow-2xl transition-all duration-300 border border-gray-100 dark:border-gray-700 hover:scale-105">
                    <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center text-4xl mb-6 group-hover:scale-110 transition-transform">📋</div>
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-3">Assessment Creation</h3>
                    <p class="text-gray-600 dark:text-gray-400 leading-relaxed">Build quizzes, exams, and assignments with multiple question types.</p>
                </div>
                <div class="group p-8 bg-gradient-to-br from-green-50 to-white dark:from-gray-800 dark:to-gray-800 rounded-2xl hover:shadow-2xl transition-all duration-300 border border-gray-100 dark:border-gray-700 hover:scale-105">
                    <div class="w-16 h-16 bg-gradient-to-br from-green-500 to-green-600 rounded-xl flex items-center justify-center text-4xl mb-6 group-hover:scale-110 transition-transform">🤖</div>
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-3">AI Generation</h3>
                    <p class="text-gray-600 dark:text-gray-400 leading-relaxed">Auto-generate questions, quizzes, and worksheets using AI.</p>
                </div>
                <div class="group p-8 bg-gradient-to-br from-yellow-50 to-white dark:from-gray-800 dark:to-gray-800 rounded-2xl hover:shadow-2xl transition-all duration-300 border border-gray-100 dark:border-gray-700 hover:scale-105">
                    <div class="w-16 h-16 bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-xl flex items-center justify-center text-4xl mb-6 group-hover:scale-110 transition-transform">📊</div>
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-3">Grade Management</h3>
                    <p class="text-gray-600 dark:text-gray-400 leading-relaxed">Automated grading for objective questions, manual review for essays.</p>
                </div>
                <div class="group p-8 bg-gradient-to-br from-red-50 to-white dark:from-gray-800 dark:to-gray-800 rounded-2xl hover:shadow-2xl transition-all duration-300 border border-gray-100 dark:border-gray-700 hover:scale-105">
                    <div class="w-16 h-16 bg-gradient-to-br from-red-500 to-red-600 rounded-xl flex items-center justify-center text-4xl mb-6 group-hover:scale-110 transition-transform">📚</div>
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-3">Content Publishing</h3>
                    <p class="text-gray-600 dark:text-gray-400 leading-relaxed">Publish books, courses, and materials for students and guests.</p>
                </div>
                <div class="group p-8 bg-gradient-to-br from-indigo-50 to-white dark:from-gray-800 dark:to-gray-800 rounded-2xl hover:shadow-2xl transition-all duration-300 border border-gray-100 dark:border-gray-700 hover:scale-105">
                    <div class="w-16 h-16 bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-xl flex items-center justify-center text-4xl mb-6 group-hover:scale-110 transition-transform">💵</div>
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-3">Revenue Sharing</h3>
                    <p class="text-gray-600 dark:text-gray-400 leading-relaxed">Monetize your content with flexible pricing - free or paid subscriptions.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Author Benefits -->
    <section class="py-20 bg-gray-50 dark:bg-gray-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl lg:text-5xl font-bold text-gray-900 dark:text-white mb-4">For Authors & Content Creators</h2>
                <p class="text-xl text-gray-600 dark:text-gray-400">Turn your expertise into income</p>
            </div>
            <div class="grid md:grid-cols-2 gap-8">
                <div class="bg-white dark:bg-gray-900 p-10 rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 border border-gray-100 dark:border-gray-700">
                    <h3 class="text-3xl font-bold text-gray-900 dark:text-white mb-6">📖 Publish Digital Books</h3>
                    <ul class="space-y-4 text-gray-600 dark:text-gray-400">
                        <li class="flex items-start gap-4"><span class="text-green-600 text-2xl font-bold">✓</span><span class="text-lg">Upload PDFs, ePubs, or create content directly</span></li>
                        <li class="flex items-start gap-4"><span class="text-green-600 text-2xl font-bold">✓</span><span class="text-lg">Set your own pricing or offer for free</span></li>
                        <li class="flex items-start gap-4"><span class="text-green-600 text-2xl font-bold">✓</span><span class="text-lg">Reach students and guests across schools</span></li>
                        <li class="flex items-start gap-4"><span class="text-green-600 text-2xl font-bold">✓</span><span class="text-lg">Track reading progress and engagement</span></li>
                    </ul>
                </div>
                <div class="bg-white dark:bg-gray-900 p-10 rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 border border-gray-100 dark:border-gray-700">
                    <h3 class="text-3xl font-bold text-gray-900 dark:text-white mb-6">🎓 Create Courses</h3>
                    <ul class="space-y-4 text-gray-600 dark:text-gray-400">
                        <li class="flex items-start gap-4"><span class="text-green-600 text-2xl font-bold">✓</span><span class="text-lg">Build comprehensive course outlines</span></li>
                        <li class="flex items-start gap-4"><span class="text-green-600 text-2xl font-bold">✓</span><span class="text-lg">Include lessons, assessments, and resources</span></li>
                        <li class="flex items-start gap-4"><span class="text-green-600 text-2xl font-bold">✓</span><span class="text-lg">Offer individual or group subscriptions</span></li>
                        <li class="flex items-start gap-4"><span class="text-green-600 text-2xl font-bold">✓</span><span class="text-lg">Earn from every enrollment</span></li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-20 bg-gradient-to-r from-purple-600 to-blue-600">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-3xl lg:text-5xl font-bold text-white mb-6">Start Teaching & Earning Today</h2>
            <p class="text-xl text-purple-100 mb-8">Join thousands of educators already using {{ config('app.name') }}</p>
            <a href="{{ route('register.author') }}" class="inline-block px-8 py-4 bg-white text-purple-600 font-semibold rounded-xl hover:shadow-2xl transition-all">
                Create Your Account
            </a>
        </div>
    </section>
</x-layouts.guest>
