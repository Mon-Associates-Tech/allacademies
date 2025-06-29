<div class="">
    Teacher {{$teacher->name}}
</div>
{{--<div class="space-y-6 hidden">--}}
{{--    <!-- Header Section -->--}}
{{--    <div class="bg-white shadow rounded-lg p-6">--}}
{{--        <div class="flex items-center justify-between">--}}
{{--            <div>--}}
{{--                <h1 class="text-2xl font-bold text-gray-900">Teacher Dashboard</h1>--}}
{{--                <p class="text-gray-600">Welcome back, {{ auth()->user()->name }}</p>--}}
{{--            </div>--}}
{{--            <div class="flex space-x-3">--}}
{{--                <a href="{{ route('teachers.assignments.create') }}"--}}
{{--                   class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition duration-200">--}}
{{--                    Create Assignment--}}
{{--                </a>--}}
{{--                <a href="{{ route('teachers.quizzes.create') }}"--}}
{{--                   class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition duration-200">--}}
{{--                    Create Quiz--}}
{{--                </a>--}}
{{--            </div>--}}
{{--        </div>--}}
{{--    </div>--}}

{{--    <!-- Statistics Cards -->--}}
{{--    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">--}}
{{--        <div class="bg-white p-6 rounded-lg shadow">--}}
{{--            <div class="flex items-center">--}}
{{--                <div class="p-2 bg-blue-100 rounded-lg">--}}
{{--                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">--}}
{{--                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>--}}
{{--                    </svg>--}}
{{--                </div>--}}
{{--                <div class="ml-4">--}}
{{--                    <p class="text-sm font-medium text-gray-600">Total Assignments</p>--}}
{{--                    <p class="text-2xl font-semibold text-gray-900">{{ $stats['total_assignments'] }}</p>--}}
{{--                </div>--}}
{{--            </div>--}}
{{--        </div>--}}

{{--        <div class="bg-white p-6 rounded-lg shadow">--}}
{{--            <div class="flex items-center">--}}
{{--                <div class="p-2 bg-green-100 rounded-lg">--}}
{{--                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">--}}
{{--                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>--}}
{{--                    </svg>--}}
{{--                </div>--}}
{{--                <div class="ml-4">--}}
{{--                    <p class="text-sm font-medium text-gray-600">Active Assignments</p>--}}
{{--                    <p class="text-2xl font-semibold text-gray-900">{{ $stats['active_assignments'] }}</p>--}}
{{--                </div>--}}
{{--            </div>--}}
{{--        </div>--}}

{{--        <div class="bg-white p-6 rounded-lg shadow">--}}
{{--            <div class="flex items-center">--}}
{{--                <div class="p-2 bg-yellow-100 rounded-lg">--}}
{{--                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">--}}
{{--                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>--}}
{{--                    </svg>--}}
{{--                </div>--}}
{{--                <div class="ml-4">--}}
{{--                    <p class="text-sm font-medium text-gray-600">Pending Grades</p>--}}
{{--                    <p class="text-2xl font-semibold text-gray-900">{{ $stats['pending_grades'] }}</p>--}}
{{--                </div>--}}
{{--            </div>--}}
{{--        </div>--}}

{{--        <div class="bg-white p-6 rounded-lg shadow">--}}
{{--            <div class="flex items-center">--}}
{{--                <div class="p-2 bg-purple-100 rounded-lg">--}}
{{--                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">--}}
{{--                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>--}}
{{--                    </svg>--}}
{{--                </div>--}}
{{--                <div class="ml-4">--}}
{{--                    <p class="text-sm font-medium text-gray-600">Total Students</p>--}}
{{--                    <p class="text-2xl font-semibold text-gray-900">{{ $stats['total_students'] }}</p>--}}
{{--                </div>--}}
{{--            </div>--}}
{{--        </div>--}}
{{--    </div>--}}

{{--    <!-- Navigation Tabs -->--}}
{{--    <div class="bg-white shadow rounded-lg">--}}
{{--        <div class="border-b border-gray-200">--}}
{{--            <nav class="-mb-px flex space-x-8" aria-label="Tabs">--}}
{{--                <button wire:click="setTab('overview')"--}}
{{--                        class="@if($selectedTab === 'overview') border-blue-500 text-blue-600 @else border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 @endif whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm">--}}
{{--                    Overview--}}
{{--                </button>--}}
{{--                <button wire:click="setTab('academic-structure')"--}}
{{--                        class="@if($selectedTab === 'academic-structure') border-blue-500 text-blue-600 @else border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 @endif whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm">--}}
{{--                    Academic Structure--}}
{{--                </button>--}}
{{--                <button wire:click="setTab('assignments')"--}}
{{--                        class="@if($selectedTab === 'assignments') border-blue-500 text-blue-600 @else border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 @endif whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm">--}}
{{--                    Assignments & Quizzes--}}
{{--                </button>--}}
{{--                <button wire:click="setTab('students')"--}}
{{--                        class="@if($selectedTab === 'students') border-blue-500 text-blue-600 @else border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 @endif whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm">--}}
{{--                    Students--}}
{{--                </button>--}}
{{--                <button wire:click="setTab('grading')"--}}
{{--                        class="@if($selectedTab === 'grading') border-blue-500 text-blue-600 @else border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 @endif whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm">--}}
{{--                    Grading--}}
{{--                </button>--}}
{{--            </nav>--}}
{{--        </div>--}}

{{--        <div class="p-6">--}}
{{--            @if($selectedTab === 'overview')--}}
{{--                @include('livewire.teachers.dashboard.overview')--}}
{{--            @elseif($selectedTab === 'academic-structure')--}}
{{--                @include('livewire.teachers.dashboard.academic-structure')--}}
{{--            @elseif($selectedTab === 'assignments')--}}
{{--                @include('livewire.teachers.dashboard.assignments')--}}
{{--            @elseif($selectedTab === 'students')--}}
{{--                @include('livewire.teachers.dashboard.students')--}}
{{--            @elseif($selectedTab === 'grading')--}}
{{--                @include('livewire.teachers.dashboard.grading')--}}
{{--            @endif--}}
{{--        </div>--}}
{{--    </div>--}}
{{--</div>--}}
