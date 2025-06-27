@php use App\Enums\UserRole; @endphp
<x-layouts.app :has-action="false" page-name="Dashboard">


    <div class="grid-cols-12 gap-6 hidden">

        <!-- Doughnut chart (Top Countries) -->
        <x-dashboard.dashboard-card-06/>

        <!-- Table (Top Channels) -->
        <x-dashboard.dashboard-card-07/>

        <!-- Card (Customers) -->
        <x-dashboard.dashboard-card-10/>

        <!-- Card (Recent Activity) -->
        <x-dashboard.dashboard-card-12/>

        <!-- Card (Income/Expenses) -->
        <x-dashboard.dashboard-card-13/>

    </div>

    @if(Auth::user()->role === 'student')
        @livewire('students.dashboard')
    @endif()
    @if(Auth::user()->role === 'teacher')
        @livewire('teachers.dashboard')
    @endif
    @if(Auth::user()->role === 'librarian')
        @livewire('librarians.dashboard')
    @endif
    @if(Auth::user()->hasAnyRole(['admin', 'owner', 'moderator', 'subscriber']))
        @livewire('administrators.dashboard')
    @endif

    @if ($academicSubjects->count())
        <section class="mt-10 w-full mx-auto">

            <h3 class="font-semibold text-2xl pb-4">My Courses</h3>
            <x-table>
                <x-slot name="head">
                    <tr>
                        <x-table.th>Available Subjects</x-table.th>
                        <x-table.th><span>Actions</span></x-table.th>
                    </tr>
                </x-slot>

                @foreach ($academicSubjects as $academicSubject)
                    <tr>
                        <x-table.td bold>
                            <span span
                                  class="text-gray-500">{{ $academicSubject->academicLevel->academicGroup->name }}</span>
                            <span class="text-gray-500">/</span>
                            <span class="text-gray-500">{{ $academicSubject->academicLevel->name }}</span>
                            <span class="text-gray-500">/</span>
                            <span>{{ $academicSubject->name }}</span>
                        </x-table.td>
                        <x-table.td action>
                            <a class="text-primary-600 hover:text-primary-900"
                               href="{{ route('quizzes.index', ['academic_subject' => $academicSubject, 'academic_level' => $academicSubject->academicLevel, 'academic_group' => $academicSubject->academicLevel->academicGroup]) }}">Quizzes</a>
                            @can('privileged', $currentTeam)
                                <a class="text-primary-600 hover:text-primary-900"
                                   href="{{ route('examinations.index', ['academic_subject' => $academicSubject, 'academic_level' => $academicSubject->academicLevel, 'academic_group' => $academicSubject->academicLevel->academicGroup]) }}">Examinations</a>
                            @endcan
                        </x-table.td>
                    </tr>
                @endforeach
            </x-table>

            <div class="mt-3">
                {{ $academicSubjects->links() }}
            </div>
            @else
                <div class="max-w-md mx-auto my-10 p-8 text-center">
                    <!-- Animated Icon -->
                    <div class="relative mb-6">
                        <div class="w-24 h-24 mx-auto relative">
                            <div class="absolute inset-0 animate-pulse">
                                <svg class="w-full h-full text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M10.394 2.08a1 1 0 00-.788 0l-7 3a1 1 0 000 1.84L5.25 8.051a.999.999 0 01.356-.257l4-1.714a1 1 0 11.788 1.838L7.667 9.088l1.94.831a1 1 0 00.787 0l7-3a1 1 0 000-1.838l-7-3zM3.31 9.397L5 10.12v4.102a8.969 8.969 0 00-1.05-.174 1 1 0 01-.89-.89 11.115 11.115 0 01.25-3.762zM9.3 16.573A9.026 9.026 0 007 14.935v-3.957l1.818.78a3 3 0 002.364 0l5.508-2.361a11.026 11.026 0 01.25 3.762 1 1 0 01-.89.89 8.968 8.968 0 00-5.35 2.524 1 1 0 01-1.4 0zM6 18a1 1 0 001-1v-2.065a8.935 8.935 0 00-2-.712V17a1 1 0 001 1z"/>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- Dynamic Heading -->
                    <h2 class="text-3xl font-bold text-gray-900 mb-2">
                        Ready to Learn?
                    </h2>
                    <p class="text-lg text-gray-600 mb-8">
                        Your academic journey awaits
                    </p>

                    <!-- Feature Cards -->
                    <div class="grid grid-cols-2 gap-4 mb-8">
                        <div class="p-4 text-center">
                            <div class="w-12 h-12 mx-auto mb-2 text-blue-600">
                                <svg fill="currentColor" viewBox="0 0 20 20" class="w-full h-full">
                                    <path d="M9 4.804A7.968 7.968 0 005.5 4c-1.255 0-2.443.29-3.5.804v10A7.969 7.969 0 015.5 14c1.669 0 3.218.51 4.5 1.385A7.962 7.962 0 0114.5 14c1.255 0 2.443.29 3.5.804v-10A7.968 7.968 0 0014.5 4c-1.255 0-2.443.29-3.5.804V12a1 1 0 11-2 0V4.804z"/>
                                </svg>
                            </div>
                            <h3 class="font-semibold text-sm text-gray-800">Premium Content</h3>
                            <p class="text-xs text-gray-600">Access all courses</p>
                        </div>

                        <div class="p-4 text-center">
                            <div class="w-12 h-12 mx-auto mb-2 text-green-600">
                                <svg fill="currentColor" viewBox="0 0 20 20" class="w-full h-full">
                                    <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <h3 class="font-semibold text-sm text-gray-800">Certificates</h3>
                            <p class="text-xs text-gray-600">Earn credentials</p>
                        </div>

                        <div class="p-4 text-center">
                            <div class="w-12 h-12 mx-auto mb-2 text-purple-600">
                                <svg fill="currentColor" viewBox="0 0 20 20" class="w-full h-full">
                                    <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <h3 class="font-semibold text-sm text-gray-800">Progress Tracking</h3>
                            <p class="text-xs text-gray-600">Monitor growth</p>
                        </div>

                        <div class="p-4 text-center">
                            <div class="w-12 h-12 mx-auto mb-2 text-orange-600">
                                <svg fill="currentColor" viewBox="0 0 20 20" class="w-full h-full">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-6-3a2 2 0 11-4 0 2 2 0 014 0zm-2 4a5 5 0 00-4.546 2.916A5.986 5.986 0 0010 16a5.986 5.986 0 004.546-2.084A5 5 0 0010 11z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <h3 class="font-semibold text-sm text-gray-800">Expert Support</h3>
                            <p class="text-xs text-gray-600">Get guidance</p>
                        </div>
                    </div>

                    <!-- Urgency Message -->
                    <div class="mb-6 p-3 text-center">
                        <p class="text-sm text-gray-700 font-medium">
                            🚀 Limited time: Start learning today
                        </p>
                        <p class="text-xs text-gray-500">
                            Join 10,000+ active learners
                        </p>
                    </div>

                    <!-- CTA Buttons -->
                    <div class="space-y-3">
                        <a href="{{ route('subscriptions.create') }}"
                           class="block w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-4 px-6 text-lg transition-all duration-200 transform hover:scale-105">
                            Subscribe Now
                        </a>

                        <button class="w-full text-gray-600 hover:text-gray-800 text-sm underline transition-colors"
                                onclick="toggleDetails()">
                            Learn more about benefits
                        </button>
                    </div>

                    <!-- Expandable Details -->
                    <div id="details" class="hidden mt-6 p-4 text-left text-sm text-gray-600 space-y-2">
                        <p>✓ Access to all premium courses and materials</p>
                        <p>✓ Interactive quizzes and assessments</p>
                        <p>✓ Downloadable resources and study guides</p>
                        <p>✓ Community forum access</p>
                        <p>✓ Mobile app compatibility</p>
                        <p>✓ 30-day money-back guarantee</p>
                    </div>
                </div>

                <script>
                    function toggleDetails() {
                        const details = document.getElementById('details');
                        details.classList.toggle('hidden');
                    }
                </script>
            @endif

        </section>
</x-layouts.app>
