<div>
    <section>
        @if ($academicSubjects->count())
            <section class="mt-10 w-full mx-auto">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h3 class="font-semibold text-3xl text-gray-900">My Courses</h3>
                        <div class="text-sm text-gray-500 mt-1">
                            {{ $academicSubjects->count() }} {{ Str::plural('course', $academicSubjects->count()) }}
                            available
                        </div>

                        @if($student && $student->academicLevel)
                            <div class="mt-2">
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    {{ $student->academicLevel->academicGroup->name }} - {{ $student->academicLevel->name }}
                                </span>
                            </div>
                        @endif
                    </div>

                    <!-- View Toggle Controls -->
                    <div class="flex items-center space-x-4">
                        <div class="flex items-center bg-gray-100 rounded-lg p-1">
                            <button onclick="toggleView('grid')"
                                    id="grid-btn"
                                    class="flex items-center px-3 py-2 text-sm font-medium rounded-md transition-all duration-200 bg-white text-gray-700 shadow-sm">
                                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M5 3a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2V5a2 2 0 00-2-2H5zM5 11a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2v-2a2 2 0 00-2-2H5zM11 5a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V5zM11 13a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                                </svg>
                                Grid
                            </button>
                            <button onclick="toggleView('list')"
                                    id="list-btn"
                                    class="flex items-center px-3 py-2 text-sm font-medium rounded-md transition-all duration-200 text-gray-500 hover:text-gray-700">
                                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"/>
                                </svg>
                                List
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Grid View -->
                <div id="grid-view" class="grid hidden grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                    @foreach ($academicSubjects as $academicSubject)
                        <div class="bg-white rounded-lg border border-gray-200 hover:border-primary-300 transition-all duration-200 hover:shadow-lg group">
                            <!-- Course Header -->
                            <div class="p-6">
                                <div class="flex items-start justify-between mb-4">
                                    <div class="flex-1">
                                        <!-- Subject hierarchy -->
                                        <div class="text-sm text-gray-500 mb-2">
                                            <span class="bg-gray-100 px-2 py-1 rounded text-xs">
                                                {{ $academicSubject->academicLevel->academicGroup->name }}
                                            </span>
                                            <span class="mx-1">•</span>
                                            <span class="bg-blue-100 text-blue-700 px-2 py-1 rounded text-xs">
                                                {{ $academicSubject->academicLevel->name }}
                                            </span>
                                        </div>

                                        <!-- Subject name -->
                                        <h4 class="text-lg font-semibold text-gray-900 group-hover:text-primary-700 transition-colors">
                                            {{ $academicSubject->name }}
                                        </h4>

                                        @if($academicSubject->code)
                                            <p class="text-sm text-gray-500 mt-1">{{ $academicSubject->code }}</p>
                                        @endif
                                    </div>

                                    <!-- Subject icon -->
                                    <div class="ml-3">
                                        <div class="w-12 h-12 bg-gradient-to-br from-primary-500 to-primary-600 rounded-lg flex items-center justify-center">
                                            <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M10.394 2.08a1 1 0 00-.788 0l-7 3a1 1 0 000 1.84L5.25 8.051a.999.999 0 01.356-.257l4-1.714a1 1 0 11.788 1.838L7.667 9.088l1.94.831a1 1 0 00.787 0l7-3a1 1 0 000-1.838l-7-3zM3.31 9.397L5 10.12v4.102a8.969 8.969 0 00-1.05-.174 1 1 0 01-.89-.89 11.115 11.115 0 01.25-3.762zM9.3 16.573A9.026 9.026 0 007 14.935v-3.957l1.818.78a3 3 0 002.364 0l5.508-2.361a11.026 11.026 0 01.25 3.762 1 1 0 01-.89.89 8.968 8.968 0 00-5.35 2.524 1 1 0 01-1.4 0zM6 18a1 1 0 001-1v-2.065a8.935 8.935 0 00-2-.712V17a1 1 0 001 1z"/>
                                            </svg>
                                        </div>
                                    </div>
                                </div>

                                <!-- Quick stats -->
                                <div class="grid grid-cols-2 gap-4 mb-4 text-sm">
                                    <div class="flex items-center text-gray-600">
                                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"/>
                                        </svg>
                                        {{ $academicSubject->quizzes_count ?? $academicSubject->quizzes()->count() }} Quizzes
                                    </div>
                                    @can('privileged', $currentTeam)
                                        <div class="flex items-center text-gray-600">
                                            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                            </svg>
                                            {{ $academicSubject->examinations_count ?? $academicSubject->examinations()->count() }} Exams
                                        </div>
                                    @endcan
                                </div>

                                <!-- Action buttons -->
                                <div class="flex flex-col space-y-2">
                                    <button wire:click="showSubjectDetails({{ $academicSubject->id }})"
                                            class="inline-flex items-center justify-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-md transition-colors duration-200">
                                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"/>
                                            <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"/>
                                        </svg>
                                        View Details
                                    </button>

                                    <a href="{{ route('quizzes.index', ['academic_subject' => $academicSubject, 'academic_level' => $academicSubject->academicLevel, 'academic_group' => $academicSubject->academicLevel->academicGroup]) }}"
                                       class="inline-flex items-center justify-center px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium rounded-md transition-colors duration-200">
                                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"/>
                                        </svg>
                                        Practice Quizzes
                                    </a>

                                    @can('privileged', $currentTeam)
                                        <a href="{{ route('examinations.index', ['academic_subject' => $academicSubject, 'academic_level' => $academicSubject->academicLevel, 'academic_group' => $academicSubject->academicLevel->academicGroup]) }}"
                                           class="inline-flex items-center justify-center px-4 py-2 border border-primary-600 text-primary-600 hover:bg-primary-50 text-sm font-medium rounded-md transition-colors duration-200">
                                            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                            </svg>
                                            Take Examinations
                                        </a>
                                    @endcan
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- List View (similar structure with View Details buttons) -->
                <div id="list-view" class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden mb-8">
                    <!-- ... existing list view code with added View Details buttons ... -->
                </div>
            </section>
        @else
            <div class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No courses available</h3>
                <p class="mt-1 text-sm text-gray-500">You don't have access to any courses yet.</p>
            </div>
        @endif
    </section>

    <!-- Subject Details Modal -->
    @if($showSubjectModal && $selectedSubject)
        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
            <div class="relative top-20 mx-auto p-5 border w-11/12 max-w-4xl shadow-lg rounded-md bg-white">
                <div class="mt-3">
                    <!-- Modal Header -->
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <h3 class="text-2xl font-bold text-gray-900">{{ $selectedSubject->name }}</h3>
                            <p class="text-sm text-gray-500 mt-1">
                                {{ $selectedSubject->academicLevel->academicGroup->name }} - {{ $selectedSubject->academicLevel->name }}
                            </p>
                        </div>
                        <button wire:click="closeSubjectModal" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>

                    <!-- Modal Content -->
                    <div class="space-y-6">
                        <!-- Course Description -->
                        @if($selectedSubject->description)
                            <div>
                                <h4 class="text-lg font-semibold text-gray-900 mb-3">Course Description</h4>
                                <p class="text-gray-700 leading-relaxed">{{ $selectedSubject->description }}</p>
                            </div>
                        @endif

                        <!-- Course Outline -->
                        @if($selectedSubject->courseOutline)
                            <div>
                                <h4 class="text-lg font-semibold text-gray-900 mb-3">Course Outline</h4>
                                <div class="bg-gray-50 rounded-lg p-4">
                                    <p class="text-gray-700 leading-relaxed">{{ $selectedSubject->courseOutline->content }}</p>
                                </div>
                            </div>
                        @endif

                        <!-- Topics and Subtopics -->
                        @if($selectedSubject->academicTopics->count() > 0)
                            <div>
                                <h4 class="text-lg font-semibold text-gray-900 mb-3">Topics & Subtopics</h4>
                                <div class="space-y-4">
                                    @foreach($selectedSubject->academicTopics as $topic)
                                        <div class="border border-gray-200 rounded-lg p-4">
                                            <h5 class="font-medium text-gray-900 mb-2">{{ $topic->name }}</h5>
                                            @if($topic->description)
                                                <p class="text-sm text-gray-600 mb-3">{{ $topic->description }}</p>
                                            @endif

                                            @if($topic->subtopics->count() > 0)
                                                <div class="ml-4">
                                                    <h6 class="text-sm font-medium text-gray-700 mb-2">Subtopics:</h6>
                                                    <ul class="space-y-1">
                                                        @foreach($topic->subtopics as $subtopic)
                                                            <li class="text-sm text-gray-600 flex items-center">
                                                                <svg class="w-3 h-3 mr-2 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                                                </svg>
                                                                {{ $subtopic->name }}
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                </div>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <!-- Lessons -->
                        @if($selectedSubject->lessons->count() > 0)
                            <div>
                                <h4 class="text-lg font-semibold text-gray-900 mb-3">Lessons</h4>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    @foreach($selectedSubject->lessons as $lesson)
                                        <div class="border border-gray-200 rounded-lg p-4">
                                            <h5 class="font-medium text-gray-900">{{ $lesson->title }}</h5>
                                            @if($lesson->description)
                                                <p class="text-sm text-gray-600 mt-1">{{ $lesson->description }}</p>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <!-- Statistics -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="bg-blue-50 rounded-lg p-4">
                                <div class="flex items-center">
                                    <svg class="w-8 h-8 text-blue-600 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"/>
                                    </svg>
                                    <div>
                                        <p class="text-2xl font-bold text-blue-600">{{ $selectedSubject->quizzes->count() }}</p>
                                        <p class="text-sm text-blue-600">Available Quizzes</p>
                                    </div>
                                </div>
                            </div>

                            @can('privileged', $currentTeam)
                                <div class="bg-green-50 rounded-lg p-4">
                                    <div class="flex items-center">
                                        <svg class="w-8 h-8 text-green-600 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                        </svg>
                                        <div>
                                            <p class="text-2xl font-bold text-green-600">{{ $selectedSubject->examinations->count() }}</p>
                                            <p class="text-sm text-green-600">Available Examinations</p>
                                        </div>
                                    </div>
                                </div>
                            @endcan
                        </div>
                    </div>

                    <!-- Modal Actions -->
                    <div class="flex justify-end space-x-3 mt-6 pt-6 border-t">
                        <button wire:click="closeSubjectModal"
                                class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-md transition-colors">
                            Close
                        </button>
                        <a href="{{ route('quizzes.index', ['academic_subject' => $selectedSubject, 'academic_level' => $selectedSubject->academicLevel, 'academic_group' => $selectedSubject->academicLevel->academicGroup]) }}"
                           class="px-4 py-2 text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 rounded-md transition-colors">
                            Start Learning
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <script>
        function toggleView(view) {
            const gridView = document.getElementById('grid-view');
            const listView = document.getElementById('list-view');
            const gridBtn = document.getElementById('grid-btn');
            const listBtn = document.getElementById('list-btn');

            if (view === 'grid') {
                gridView.classList.remove('hidden');
                listView.classList.add('hidden');
                gridBtn.classList.add('bg-white', 'text-gray-700', 'shadow-sm');
                gridBtn.classList.remove('text-gray-500', 'hover:text-gray-700');
                listBtn.classList.remove('bg-white', 'text-gray-700', 'shadow-sm');
                listBtn.classList.add('text-gray-500', 'hover:text-gray-700');
            } else {
                gridView.classList.add('hidden');
                listView.classList.remove('hidden');
                listBtn.classList.add('bg-white', 'text-gray-700', 'shadow-sm');
                listBtn.classList.remove('text-gray-500', 'hover:text-gray-700');
                gridBtn.classList.remove('bg-white', 'text-gray-700', 'shadow-sm');
                gridBtn.classList.add('text-gray-500', 'hover:text-gray-700');
            }
        }

        // Initialize grid view by default
        document.addEventListener('DOMContentLoaded', function() {
            toggleView('grid');
        });
    </script>
</div>
