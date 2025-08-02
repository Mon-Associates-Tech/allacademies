<section :title="$course->name" page-name="Course Details">
    <x-slot name="breadcrumb">
        <nav class="flex mb-4" aria-label="Breadcrumb">
            <ol class="flex items-center space-x-4">
                <li>
                    <a href="{{ route('student.courses') }}" class="text-gray-400 hover:text-gray-500 flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        My Courses
                    </a>
                </li>
                <li>
                    <div class="flex items-center">
                        <svg class="w-4 h-4 text-gray-300 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                        </svg>
                        <span class="text-sm text-gray-500">{{ $course->name }}</span>
                    </div>
                </li>
            </ol>
        </nav>
    </x-slot>

    <!-- Course Header Card -->
    <div class="bg-gradient-to-r from-blue-600 to-blue-800 rounded-xl shadow-lg mb-6 overflow-hidden">
        <div class="px-6 py-8 relative">
            <!-- Background Pattern -->
            <div class="absolute inset-0 bg-black opacity-10">
                <svg class="w-full h-full" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
                    <defs>
                        <pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse">
                            <circle cx="25" cy="25" r="1" fill="white" opacity="0.1"/>
                            <circle cx="75" cy="75" r="1" fill="white" opacity="0.1"/>
                            <circle cx="25" cy="75" r="1" fill="white" opacity="0.1"/>
                            <circle cx="75" cy="25" r="1" fill="white" opacity="0.1"/>
                        </pattern>
                    </defs>
                    <rect width="100%" height="100%" fill="url(#grain)"/>
                </svg>
            </div>

            <div class="relative flex flex-col lg:flex-row lg:items-center lg:justify-between">
                <div class="flex-1">
                    <!-- Course Badges -->
                    <div class="flex flex-wrap items-center gap-2 mb-4">
                        <span class="px-3 py-1 bg-white/20 text-white/90 rounded-full text-xs font-medium">
                            {{ $course->academicLevel->academicGroup->name }}
                        </span>
                        <span class="px-3 py-1 bg-white/30 text-white rounded-full text-xs font-medium">
                            {{ $course->academicLevel->name }}
                        </span>
                        @if($course->code)
                            <span class="px-3 py-1 bg-yellow-400/20 text-yellow-100 rounded-full text-xs font-medium">
                                {{ $course->code }}
                            </span>
                        @endif
                    </div>

                    <!-- Course Title -->
                    <h1 class="text-3xl lg:text-4xl font-bold text-white mb-3">{{ $course->name }}</h1>

                    <!-- Teachers -->
                    @if($course->teachers && $course->teachers->count() > 0)
                        <div class="flex items-center text-white/80">
                            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"/>
                            </svg>
                            <span class="text-sm">
                                @foreach($course->teachers as $teacher)
                                    {{ $teacher->user->name }}@if(!$loop->last), @endif
                                @endforeach
                            </span>
                        </div>
                    @endif
                </div>

                <!-- Quick Actions -->
                <div class="mt-6 lg:mt-0 lg:ml-8 flex flex-col sm:flex-row gap-3">
                    <a href="{{ route('quizzes.index', ['academic_subject' => $course, 'academic_level' => $course->academicLevel, 'academic_group' => $course->academicLevel->academicGroup]) }}"
                       class="inline-flex items-center justify-center px-6 py-3 bg-white text-blue-600 rounded-lg font-medium hover:bg-gray-100 transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Practice Quizzes
                    </a>
                    @can('privileged', auth()->user()->currentTeam)
                        <a href="{{ route('examinations.index', ['academic_subject' => $course, 'academic_level' => $course->academicLevel, 'academic_group' => $course->academicLevel->academicGroup]) }}"
                           class="inline-flex items-center justify-center px-6 py-3 bg-white/10 border border-white/20 text-white rounded-lg font-medium hover:bg-white/20 transition-colors">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Take Exams
                        </a>
                    @endcan
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        @php
            $stats = [
                ['title' => 'Topics', 'count' => $course->academicTopics->count(), 'color' => 'blue', 'icon' => 'M4 6h16M4 10h16M4 14h16M4 18h16'],
                ['title' => 'Lessons', 'count' => $course->lessons->count(), 'color' => 'green', 'icon' => 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253'],
                ['title' => 'Quizzes', 'count' => $course->quizzes->count(), 'color' => 'purple', 'icon' => 'M9 5H7a2 2 0 00-2 2v6a2 2 0 002 2h2m0-10V3a2 2 0 012-2h4a2 2 0 012 2v2M9 5V3a2 2 0 012-2h4a2 2 0 012 2v2m-6 9l2 2 4-4'],
                ['title' => 'Exams', 'count' => $course->examinations->count(), 'color' => 'orange', 'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z']
            ];
        @endphp

        @foreach($stats as $stat)
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 mb-1">{{ $stat['title'] }}</p>
                        <p class="text-3xl font-bold text-gray-900">{{ $stat['count'] }}</p>
                    </div>
                    <div class="w-12 h-12 bg-{{ $stat['color'] }}-100 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-{{ $stat['color'] }}-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $stat['icon'] }}"/>
                        </svg>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Content Tabs -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <!-- Tab Navigation -->
        <div class="border-b border-gray-200 bg-gray-50">
            <nav class="flex px-6">
                @php
                    $tabs = [
                        'overview' => ['name' => 'Overview', 'icon' => 'M4 6h16M4 10h16M4 14h16M4 18h16'],
                        'topics' => ['name' => 'Topics', 'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
                        'lessons' => ['name' => 'Lessons', 'icon' => 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253'],
                        'practice' => ['name' => 'Practice', 'icon' => 'M9 3a1 1 0 012 0v5.5a.5.5 0 001 0V4a1 1 0 112 0v4.5a.5.5 0 001 0V6a1 1 0 112 0v6a7 7 0 11-14 0V9a1 1 0 012 0v2.5a.5.5 0 001 0V4a1 1 0 012 0v4.5a.5.5 0 001 0V3z']
                    ];
                @endphp

                @foreach($tabs as $tabKey => $tab)
                    <button wire:click="setActiveTab('{{ $tabKey }}')"
                            class="flex items-center px-6 py-4 text-sm font-medium border-b-2 transition-colors {{ $activeTab === $tabKey ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $tab['icon'] }}"/>
                        </svg>
                        {{ $tab['name'] }}
                    </button>
                @endforeach
            </nav>
        </div>

        <!-- Tab Content -->
        <div class="p-6">
            @if($activeTab === 'overview')
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <!-- Main Content -->
                    <div class="lg:col-span-2 space-y-6">
                        @if($course->description)
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 mb-3">About This Course</h3>
                                <div class="prose prose-gray max-w-none">
                                    <p class="text-gray-700 leading-relaxed">{{ $course->description }}</p>
                                </div>
                            </div>
                        @endif

                        @if($course->courseOutline)
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 mb-3">Course Outline</h3>
                                <div class="bg-gray-50 rounded-lg p-6">
                                    <p class="text-gray-700 leading-relaxed">{{ $course->courseOutline->content }}</p>
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Sidebar -->
                    <div class="space-y-6">
                        <!-- Progress Card -->
                        <div class="bg-gradient-to-br from-green-50 to-emerald-50 rounded-lg p-6 border border-green-200">
                            <h4 class="font-semibold text-green-900 mb-4">Course Progress</h4>
                            <div class="space-y-3">
                                <div class="flex justify-between text-sm">
                                    <span class="text-green-700">Completion</span>
                                    <span class="text-green-900 font-medium">0%</span>
                                </div>
                                <div class="w-full bg-green-200 rounded-full h-2">
                                    <div class="bg-green-500 h-2 rounded-full" style="width: 0%"></div>
                                </div>
                            </div>
                        </div>

                        <!-- Quick Actions -->
                        <div class="bg-gray-50 rounded-lg p-6">
                            <h4 class="font-semibold text-gray-900 mb-4">Quick Actions</h4>
                            <div class="space-y-3">
                                <a href="{{ route('quizzes.index', ['academic_subject' => $course, 'academic_level' => $course->academicLevel, 'academic_group' => $course->academicLevel->academicGroup]) }}"
                                   class="block w-full text-center px-4 py-2 bg-purple-100 text-purple-700 rounded-lg hover:bg-purple-200 transition-colors">
                                    Start Practice Quiz
                                </a>
                                <button wire:click="setActiveTab('topics')"
                                        class="block w-full text-center px-4 py-2 bg-blue-100 text-blue-700 rounded-lg hover:bg-blue-200 transition-colors">
                                    Browse Topics
                                </button>
                                <button wire:click="setActiveTab('lessons')"
                                        class="block w-full text-center px-4 py-2 bg-green-100 text-green-700 rounded-lg hover:bg-green-200 transition-colors">
                                    View Lessons
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            @if($activeTab === 'topics')
                @if($course->academicTopics->count() > 0)
                    <div class="space-y-6">
                        @foreach($course->academicTopics as $index => $topic)
                            <div class="border border-gray-200 rounded-lg p-6 hover:shadow-sm transition-shadow">
                                <div class="flex items-start justify-between mb-4">
                                    <div class="flex-1">
                                        <div class="flex items-center mb-2">
                                            <span class="w-8 h-8 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center text-sm font-medium mr-3">
                                                {{ $index + 1 }}
                                            </span>
                                            <h3 class="text-xl font-semibold text-gray-900">{{ $topic->name }}</h3>
                                        </div>
                                        @if($topic->description)
                                            <p class="text-gray-600 ml-11">{{ $topic->description }}</p>
                                        @endif
                                    </div>
                                    <span class="ml-4 px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm font-medium">
                                        {{ $topic->subtopics->count() }} subtopics
                                    </span>
                                </div>

                                @if($topic->subtopics->count() > 0)
                                    <div class="ml-11 mt-4">
                                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                                            @foreach($topic->subtopics as $subtopic)
                                                <div class="flex items-center p-3 bg-gray-50 rounded-lg">
                                                    <svg class="w-4 h-4 text-blue-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                                    </svg>
                                                    <span class="text-sm text-gray-700">{{ $subtopic->name }}</span>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-12">
                        <div class="w-24 h-24 mx-auto mb-6 bg-gray-100 rounded-full flex items-center justify-center">
                            <svg class="w-12 h-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">No topics available</h3>
                        <p class="text-gray-500">Topics for this course haven't been added yet.</p>
                    </div>
                @endif
            @endif

            @if($activeTab === 'lessons')
                @if($course->lessons->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($course->lessons as $lesson)
                            <div class="border border-gray-200 rounded-lg p-6 hover:shadow-md transition-shadow">
                                <div class="flex items-center justify-between mb-4">
                                    <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                                        <svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                    </div>
                                    <span class="px-2 py-1 bg-green-100 text-green-800 rounded text-xs font-medium">Available</span>
                                </div>
                                <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ $lesson->title }}</h3>
                                @if($lesson->description)
                                    <p class="text-gray-600 text-sm">{{ $lesson->description }}</p>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-12">
                        <div class="w-24 h-24 mx-auto mb-6 bg-gray-100 rounded-full flex items-center justify-center">
                            <svg class="w-12 h-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                            </svg>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">No lessons available</h3>
                        <p class="text-gray-500">Lessons for this course haven't been added yet.</p>
                    </div>
                @endif
            @endif

            @if($activeTab === 'practice')
                <div class="space-y-8">
                    <!-- Quizzes -->
                    <div>
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-xl font-semibold text-gray-900">Practice Quizzes</h3>
                            <span class="px-3 py-1 bg-purple-100 text-purple-800 rounded-full text-sm font-medium">
                                {{ $course->quizzes->count() }} available
                            </span>
                        </div>

                        @if($course->quizzes->count() > 0)
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                                @foreach($course->quizzes as $quiz)
                                    <div class="border border-gray-200 rounded-lg p-6 hover:shadow-md transition-shadow">
                                        <div class="flex items-start justify-between mb-4">
                                            <div class="flex-1">
                                                <h4 class="text-lg font-semibold text-gray-900 mb-2">{{ $quiz->title }}</h4>
                                                @if($quiz->description)
                                                    <p class="text-gray-600 text-sm mb-4">{{ $quiz->description }}</p>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="flex items-center justify-between">
                                            <span class="text-sm text-gray-500">{{ $quiz->questions->count() ?? 0 }} questions</span>
                                            <a href="{{ route('quizzes.show', $quiz) }}"
                                               class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors">
                                                Start Quiz
                                            </a>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <div class="text-center">
                                <a href="{{ route('quizzes.index', ['academic_subject' => $course, 'academic_level' => $course->academicLevel, 'academic_group' => $course->academicLevel->academicGroup]) }}"
                                   class="inline-flex items-center px-6 py-3 bg-purple-100 text-purple-700 rounded-lg hover:bg-purple-200 transition-colors">
                                    View All Quizzes
                                </a>
                            </div>
                        @else
                            <div class="text-center py-8 bg-gray-50 rounded-lg">
                                <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                <h4 class="text-lg font-medium text-gray-900 mb-2">No quizzes available</h4>
                                <p class="text-gray-500">Practice quizzes for this course haven't been added yet.</p>
                            </div>
                        @endif
                    </div>

                    <!-- Examinations -->
                    @can('privileged', auth()->user()->currentTeam)
                        <div>
                            <div class="flex items-center justify-between mb-6">
                                <h3 class="text-xl font-semibold text-gray-900">Examinations</h3>
                                <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm font-medium">
                                    {{ $course->examinations->count() }} available
                                </span>
                            </div>

                            @if($course->examinations->count() > 0)
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                                    @foreach($course->examinations as $exam)
                                        <div class="border border-gray-200 rounded-lg p-6 hover:shadow-md transition-shadow">
                                            <div class="flex items-start justify-between mb-4">
                                                <div class="flex-1">
                                                    <h4 class="text-lg font-semibold text-gray-900 mb-2">{{ $exam->title }}</h4>
                                                    @if($exam->description)
                                                        <p class="text-gray-600 text-sm mb-4">{{ $exam->description }}</p>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="flex items-center justify-between">
                                                <span class="text-sm text-gray-500">{{ $exam->questions->count() ?? 0 }} questions</span>
                                                <a href="{{ route('examinations.show', $exam) }}"
                                                   class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                                                    Take Exam
                                                </a>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                <div class="text-center">
                                    <a href="{{ route('examinations.index', ['academic_subject' => $course, 'academic_level' => $course->academicLevel, 'academic_group' => $course->academicLevel->academicGroup]) }}"
                                       class="inline-flex items-center px-6 py-3 bg-green-100 text-green-700 rounded-lg hover:bg-green-200 transition-colors">
                                        View All Examinations
                                    </a>
                                </div>
                            @else
                                <div class="text-center py-8 bg-gray-50 rounded-lg">
                                    <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    <h4 class="text-lg font-medium text-gray-900 mb-2">No examinations available</h4>
                                    <p class="text-gray-500">Examinations for this course haven't been added yet.</p>
                                </div>
                            @endif
                        </div>
                    @endcan
                </div>
            @endif
        </div>
    </div>
</section>
