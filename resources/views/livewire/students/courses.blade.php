<div>
    <section>
        @if ($academicSubjects->count())
            <section class="mt-10 w-full mx-auto">
                <!-- Header Section -->
                <div class="flex items-center justify-between mb-8">
                    <div>
                        <h3 class="font-bold text-4xl text-gray-900 mb-2">My Courses</h3>
                        <div class="text-lg text-gray-600 mb-3">
                            {{ $academicSubjects->count() }} {{ Str::plural('course', $academicSubjects->count()) }}
                            available
                        </div>

                        @if($student && $student->academicLevel)
                            <div class="mt-3">
                                <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-semibold bg-gradient-to-r from-blue-500 to-blue-600 text-white shadow-lg">
                                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M10.394 2.08a1 1 0 00-.788 0l-7 3a1 1 0 000 1.84L5.25 8.051a.999.999 0 01.356-.257l4-1.714a1 1 0 11.788 1.838L7.667 9.088l1.94.831a1 1 0 00.787 0l7-3a1 1 0 000-1.838l-7-3z"/>
                                        <path d="M3.31 9.397L5 10.12v4.102a8.969 8.969 0 00-1.05-.174 1 1 0 01-.89-.89 11.115 11.115 0 01.25-3.762z"/>
                                        <path d="M9.3 16.573A9.026 9.026 0 007 14.935v-3.957l1.818.78a3 3 0 002.364 0l5.508-2.361a11.026 11.026 0 01.25 3.762 1 1 0 01-.89.89 8.968 8.968 0 00-5.35 2.524 1 1 0 01-1.4 0z"/>
                                        <path d="M6 18a1 1 0 001-1v-2.065a8.935 8.935 0 00-2-.712V17a1 1 0 001 1z"/>
                                    </svg>
                                    {{ $student->academicLevel->academicGroup->name }} - {{ $student->academicLevel->name }}
                                </span>
                            </div>
                        @endif
                    </div>

                    <!-- View Toggle Controls -->
                    <div class="flex items-center space-x-4">
                        <div class="flex items-center bg-white rounded-xl p-1 shadow-lg border border-gray-200">
                            <button onclick="toggleView('grid')"
                                    id="grid-btn"
                                    class="flex items-center px-4 py-3 text-sm font-semibold rounded-lg transition-all duration-300 bg-gradient-to-r from-primary-500 to-primary-600 text-white shadow-md">
                                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M5 3a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2V5a2 2 0 00-2-2H5zM5 11a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2v-2a2 2 0 00-2-2H5zM11 5a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V5zM11 13a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                                </svg>
                                Grid
                            </button>
                            <button onclick="toggleView('list')"
                                    id="list-btn"
                                    class="flex items-center px-4 py-3 text-sm font-semibold rounded-lg transition-all duration-300 text-gray-500 hover:text-gray-700 hover:bg-gray-50">
                                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"/>
                                </svg>
                                List
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Grid View -->
                <div id="grid-view" class="grid hidden grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8 mb-8">
                    @foreach ($academicSubjects as $academicSubject)
                        <div class="course-card bg-white rounded-2xl border border-gray-100 hover:border-primary-200 transition-all duration-500 hover:shadow-2xl hover:-translate-y-2 group overflow-hidden">
                            <!-- Course Header with gradient -->
                            <div class="relative">
                                <div class="h-32 bg-gradient-to-br from-primary-400 via-primary-500 to-primary-600 relative overflow-hidden">
                                    <div class="absolute inset-0 bg-black bg-opacity-10"></div>
                                    <div class="absolute top-4 right-4">
                                        <div class="w-12 h-12 bg-white bg-opacity-20 backdrop-blur-sm rounded-xl flex items-center justify-center">
                                            <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M10.394 2.08a1 1 0 00-.788 0l-7 3a1 1 0 000 1.84L5.25 8.051a.999.999 0 01.356-.257l4-1.714a1 1 0 11.788 1.838L7.667 9.088l1.94.831a1 1 0 00.787 0l7-3a1 1 0 000-1.838l-7-3z"/>
                                                <path d="M3.31 9.397L5 10.12v4.102a8.969 8.969 0 00-1.05-.174 1 1 0 01-.89-.89 11.115 11.115 0 01.25-3.762z"/>
                                                <path d="M9.3 16.573A9.026 9.026 0 007 14.935v-3.957l1.818.78a3 3 0 002.364 0l5.508-2.361a11.026 11.026 0 01.25 3.762 1 1 0 01-.89.89 8.968 8.968 0 00-5.35 2.524 1 1 0 01-1.4 0z"/>
                                                <path d="M6 18a1 1 0 001-1v-2.065a8.935 8.935 0 00-2-.712V17a1 1 0 001 1z"/>
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="absolute bottom-4 left-4">
                                        <div class="flex space-x-2">
                                            <span class="bg-white bg-opacity-20 backdrop-blur-sm px-3 py-1 rounded-full text-xs font-medium text-white">
                                                {{ $academicSubject->academicLevel->academicGroup->name }}
                                            </span>
                                            <span class="bg-white bg-opacity-30 backdrop-blur-sm px-3 py-1 rounded-full text-xs font-medium text-white">
                                                {{ $academicSubject->academicLevel->name }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Course Content -->
                            <div class="p-6">
                                <!-- Subject name -->
                                <h4 class="text-xl font-bold text-gray-900 group-hover:text-primary-700 transition-colors duration-300 mb-2 line-clamp-2">
                                    {{ $academicSubject->name }}
                                </h4>

                                @if($academicSubject->code)
                                    <p class="text-sm font-medium text-primary-600 bg-primary-50 px-3 py-1 rounded-full inline-block mb-4">
                                        {{ $academicSubject->code }}
                                    </p>
                                @endif

                                <!-- Quick stats -->
                                <div class="flex justify-between items-center mb-6 text-sm">
                                    <div class="flex items-center text-gray-600 bg-gray-50 px-3 py-2 rounded-lg">
                                        <svg class="w-4 h-4 mr-2 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"/>
                                        </svg>
                                        <span class="font-semibold">{{ $academicSubject->quizzes_count ?? $academicSubject->quizzes()->count() }}</span>
                                        <span class="ml-1">Quizzes</span>
                                    </div>
                                    @can('privileged', $currentTeam)
                                        <div class="flex items-center text-gray-600 bg-gray-50 px-3 py-2 rounded-lg">
                                            <svg class="w-4 h-4 mr-2 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                            </svg>
                                            <span class="font-semibold">{{ $academicSubject->examinations_count ?? $academicSubject->examinations()->count() }}</span>
                                            <span class="ml-1">Exams</span>
                                        </div>
                                    @endcan
                                </div>

                                <!-- Action buttons -->
                                <div class="space-y-3">
                                    <a href="{{ route('student.course.details', $academicSubject->id) }}"
                                       class="w-full inline-flex items-center justify-center px-4 py-3 bg-gradient-to-r from-primary-600 to-primary-700 hover:from-primary-700 hover:to-primary-800 text-white text-sm font-semibold rounded-xl transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"/>
                                            <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"/>
                                        </svg>
                                        View Course Details
                                    </a>

                                    <a href="{{ route('quizzes.index', ['academic_subject' => $academicSubject, 'academic_level' => $academicSubject->academicLevel, 'academic_group' => $academicSubject->academicLevel->academicGroup]) }}"
                                       class="w-full inline-flex items-center justify-center px-4 py-3 bg-white border border-primary-200 text-primary-700 hover:bg-primary-50 text-sm font-semibold rounded-xl transition-all duration-300">
                                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"/>
                                        </svg>
                                        Practice Quizzes
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- List View -->
                <div id="list-view" class="bg-white rounded-2xl border border-gray-100 shadow-xl overflow-hidden mb-8">
                    <div class="divide-y divide-gray-100">
                        @foreach ($academicSubjects as $academicSubject)
                            <div class="p-6 hover:bg-gray-50 transition-all duration-300 group">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-4 flex-1">
                                        <!-- Course Icon -->
                                        <div class="w-16 h-16 bg-gradient-to-br from-primary-500 to-primary-600 rounded-xl flex items-center justify-center flex-shrink-0">
                                            <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M10.394 2.08a1 1 0 00-.788 0l-7 3a1 1 0 000 1.84L5.25 8.051a.999.999 0 01.356-.257l4-1.714a1 1 0 11.788 1.838L7.667 9.088l1.94.831a1 1 0 00.787 0l7-3a1 1 0 000-1.838l-7-3z"/>
                                                <path d="M3.31 9.397L5 10.12v4.102a8.969 8.969 0 00-1.05-.174 1 1 0 01-.89-.89 11.115 11.115 0 01.25-3.762z"/>
                                                <path d="M9.3 16.573A9.026 9.026 0 007 14.935v-3.957l1.818.78a3 3 0 002.364 0l5.508-2.361a11.026 11.026 0 01.25 3.762 1 1 0 01-.89.89 8.968 8.968 0 00-5.35 2.524 1 1 0 01-1.4 0z"/>
                                                <path d="M6 18a1 1 0 001-1v-2.065a8.935 8.935 0 00-2-.712V17a1 1 0 001 1z"/>
                                            </svg>
                                        </div>

                                        <!-- Course Info -->
                                        <div class="flex-1">
                                            <div class="flex items-center space-x-2 mb-2">
                                                <span class="bg-gray-100 px-3 py-1 rounded-full text-xs font-medium text-gray-600">
                                                    {{ $academicSubject->academicLevel->academicGroup->name }}
                                                </span>
                                                <span class="bg-blue-100 text-blue-700 px-3 py-1 rounded-full text-xs font-medium">
                                                    {{ $academicSubject->academicLevel->name }}
                                                </span>
                                            </div>

                                            <h4 class="text-xl font-bold text-gray-900 group-hover:text-primary-700 transition-colors duration-300 mb-1">
                                                {{ $academicSubject->name }}
                                            </h4>

                                            @if($academicSubject->code)
                                                <p class="text-sm font-medium text-primary-600">{{ $academicSubject->code }}</p>
                                            @endif

                                            <!-- Stats -->
                                            <div class="flex items-center space-x-4 mt-3 text-sm text-gray-600">
                                                <div class="flex items-center">
                                                    <svg class="w-4 h-4 mr-2 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"/>
                                                    </svg>
                                                    {{ $academicSubject->quizzes_count ?? $academicSubject->quizzes()->count() }} Quizzes
                                                </div>
                                                @can('privileged', $currentTeam)
                                                    <div class="flex items-center">
                                                        <svg class="w-4 h-4 mr-2 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                                        </svg>
                                                        {{ $academicSubject->examinations_count ?? $academicSubject->examinations()->count() }} Exams
                                                    </div>
                                                @endcan
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Action Buttons -->
                                    <div class="flex items-center space-x-3">
                                        <a href="{{ route('student.course.details', $academicSubject->id) }}"
                                           class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-primary-600 to-primary-700 hover:from-primary-700 hover:to-primary-800 text-white text-sm font-semibold rounded-lg transition-all duration-300 shadow-md hover:shadow-lg">
                                            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"/>
                                                <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"/>
                                            </svg>
                                            View Details
                                        </a>

                                        <a href="{{ route('quizzes.index', ['academic_subject' => $academicSubject, 'academic_level' => $academicSubject->academicLevel, 'academic_group' => $academicSubject->academicLevel->academicGroup]) }}"
                                           class="inline-flex items-center px-4 py-2 bg-white border border-primary-200 text-primary-700 hover:bg-primary-50 text-sm font-semibold rounded-lg transition-all duration-300">
                                            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"/>
                                            </svg>
                                            Practice
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>
        @else
            <!-- Empty State -->
            <div class="text-center py-20">
                <div class="max-w-md mx-auto">
                    <div class="w-24 h-24 bg-gradient-to-br from-gray-100 to-gray-200 rounded-full flex items-center justify-center mx-auto mb-6">
                        <svg class="w-12 h-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-2">No courses available</h3>
                    <p class="text-lg text-gray-500 mb-6">You don't have access to any courses yet. Contact your administrator to get started.</p>
                    <div class="bg-blue-50 border border-blue-200 rounded-xl p-4">
                        <p class="text-sm text-blue-700">
                            <svg class="w-5 h-5 inline mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                            </svg>
                            Once you're enrolled in courses, they'll appear here for you to explore and practice.
                        </p>
                    </div>
                </div>
            </div>
        @endif
    </section>

    <style>
        .course-card {
            animation: fadeInUp 0.6s ease-out;
        }

        .course-card:hover {
            transform: translateY(-8px) scale(1.02);
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
    </style>

    <script>
        function toggleView(view) {
            const gridView = document.getElementById('grid-view');
            const listView = document.getElementById('list-view');
            const gridBtn = document.getElementById('grid-btn');
            const listBtn = document.getElementById('list-btn');

            if (view === 'grid') {
                gridView.classList.remove('hidden');
                listView.classList.add('hidden');

                gridBtn.classList.add('bg-gradient-to-r', 'from-primary-500', 'to-primary-600', 'text-white', 'shadow-md');
                gridBtn.classList.remove('text-gray-500', 'hover:text-gray-700', 'hover:bg-gray-50');

                listBtn.classList.remove('bg-gradient-to-r', 'from-primary-500', 'to-primary-600', 'text-white', 'shadow-md');
                listBtn.classList.add('text-gray-500', 'hover:text-gray-700', 'hover:bg-gray-50');
            } else {
                gridView.classList.add('hidden');
                listView.classList.remove('hidden');

                listBtn.classList.add('bg-gradient-to-r', 'from-primary-500', 'to-primary-600', 'text-white', 'shadow-md');
                listBtn.classList.remove('text-gray-500', 'hover:text-gray-700', 'hover:bg-gray-50');

                gridBtn.classList.remove('bg-gradient-to-r', 'from-primary-500', 'to-primary-600', 'text-white', 'shadow-md');
                gridBtn.classList.add('text-gray-500', 'hover:text-gray-700', 'hover:bg-gray-50');
            }
        }

        // Initialize grid view by default
        document.addEventListener('DOMContentLoaded', function() {
            toggleView('grid');
        });
    </script>
</div>
