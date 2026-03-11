<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between md:hidden items-center">
            <h1 class="text-2xl font-semibold text-gray-900">Student Dashboard</h1>

            <!-- Student Info -->
            <div class="flex items-center">
                <div class="mr-4 text-right">
                    <p class="text-sm font-medium text-gray-900">{{ $student->name }}</p>
                    <p class="text-xs text-gray-500">{{ $studentProfile?->studentGroup?->name }}</p>
                </div>
                <img class="h-12 w-12 rounded-full" src="{{ $student->avatar ? asset($student->avatar) : 'https://ui-avatars.com/api/?name='.urlencode($student->name).'&color=7F9CF5&background=EBF4FF' }}" alt="{{ $student->name }}">
            </div>
        </div>

        <!-- Tabs Navigation -->
        <div class="mt-6 border-b border-gray-200">
            <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                <button
                    wire:click="setActiveTab('overview')"
                    class="{{ $activeTab === 'overview' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-500' }} whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm"
                >
                    Overview
                </button>
                <button
                    wire:click="setActiveTab('subjects')"
                    class="{{ $activeTab === 'subjects' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-500' }} whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm"
                >
                    Subjects
                </button>
                <button
                    wire:click="setActiveTab('books')"
                    class="{{ $activeTab === 'books' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-500' }} whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm"
                >
                    Books
                </button>
                <button
                    wire:click="setActiveTab('lessons')"
                    class="{{ $activeTab === 'lessons' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-500' }} whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm"
                >
                    Lessons
                </button>
                <button
                    wire:click="setActiveTab('assessments')"
                    class="{{ $activeTab === 'assessments' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-500' }} whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm"
                >
                    Assessments
                </button>
            </nav>
        </div>

        <!-- Overview Tab -->
        <div x-data="{ showSubjects: true, showBooks: true, showLessons: true, showAssessments: true }"
             x-cloak
             class="{{ $activeTab === 'overview' ? 'block' : 'hidden' }} mt-6 grid grid-cols-1 gap-5 lg:grid-cols-2">

            <!-- Subjects Section -->
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="px-4 py-5 sm:px-6 flex justify-between items-center">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">
                        My Subjects
                    </h3>
                    <button @click="showSubjects = !showSubjects" class="text-gray-400 hover:text-gray-500">
                        <svg x-show="!showSubjects" class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                        <svg x-show="showSubjects" class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                        </svg>
                    </button>
                </div>
                <div x-show="showSubjects" class="border-t border-gray-200">
                    <ul class="divide-y divide-gray-200">
                        @forelse($subjects as $subject)
                            <li class="px-4 py-3">
                                <div class="flex justify-between">
                                    <p class="text-sm font-medium text-indigo-600">{{ $subject->name }}</p>
                                    <p class="text-sm text-gray-500">{{ $subject->lessons_count }} lessons</p>
                                </div>
                                <p class="text-xs text-gray-500 mt-1">{{ Str::limit($subject->description, 50) }}</p>
                            </li>
                        @empty
                            <li class="px-4 py-3 text-sm text-gray-500">No subjects found.</li>
                        @endforelse
                    </ul>
                    @if(count($subjects) > 0)
                        <div class="bg-gray-50 px-4 py-3 text-right">
                            <button wire:click="setActiveTab('subjects')" class="text-sm font-medium text-indigo-600 hover:text-indigo-500">
                                View all subjects
                            </button>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Books Section -->
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="px-4 py-5 sm:px-6 flex justify-between items-center">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">
                        My Books
                    </h3>
                    <button @click="showBooks = !showBooks" class="text-gray-400 hover:text-gray-500">
                        <svg x-show="!showBooks" class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                        <svg x-show="showBooks" class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                        </svg>
                    </button>
                </div>
                <div x-show="showBooks" class="border-t border-gray-200">
                    <div class="px-4 py-3">
                        <h4 class="text-sm font-medium text-gray-500">Borrowed Books</h4>
                        <ul class="mt-2 divide-y divide-gray-200">
                            @forelse($borrowedBooks as $borrowing)
                                <li class="py-2">
                                    <div class="flex justify-between">
                                        <p class="text-sm font-medium text-indigo-600">{{ $borrowing->book->title }}</p>
                                        <p class="text-xs px-2 inline-flex leading-5 font-semibold rounded-full {{ $borrowing->status === 'borrowed' ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800' }}">
                                            {{ ucfirst($borrowing->status) }}
                                        </p>
                                    </div>
                                    <p class="text-xs text-gray-500">Due: {{ $borrowing->due_date->format('M d, Y') }}</p>
                                </li>
                            @empty
                                <li class="py-2 text-sm text-gray-500">No borrowed books.</li>
                            @endforelse
                        </ul>
                    </div>
                    <div class="px-4 py-3 border-t border-gray-200">
                        <h4 class="text-sm font-medium text-gray-500">Subscribed Books</h4>
                        <ul class="mt-2 divide-y divide-gray-200">
                            @forelse($subscribedBooks as $subscription)
                                <li class="py-2">
                                    <p class="text-sm font-medium text-indigo-600">{{ $subscription->book->title }}</p>
                                    <p class="text-xs text-gray-500">By: {{ $subscription->book->author->user->name }}</p>
                                </li>
                            @empty
                                <li class="py-2 text-sm text-gray-500">No subscribed books.</li>
                            @endforelse
                        </ul>
                    </div>
                    @if(count($borrowedBooks) > 0 || count($subscribedBooks) > 0)
                        <div class="bg-gray-50 px-4 py-3 text-right">
                            <button wire:click="setActiveTab('books')" class="text-sm font-medium text-indigo-600 hover:text-indigo-500">
                                View all books
                            </button>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Upcoming Lessons -->
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="px-4 py-5 sm:px-6 flex justify-between items-center">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">
                        Upcoming Lessons
                    </h3>
                    <button @click="showLessons = !showLessons" class="text-gray-400 hover:text-gray-500">
                        <svg x-show="!showLessons" class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                        <svg x-show="showLessons" class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                        </svg>
                    </button>
                </div>
                <div x-show="showLessons" class="border-t border-gray-200">
                    <ul class="divide-y divide-gray-200">
                        @forelse($upcomingLessons as $lesson)
                            <li class="px-4 py-3">
                                <div class="flex justify-between">
                                    <p class="text-sm font-medium text-indigo-600">{{ $lesson->title }}</p>
                                    <p class="text-xs px-2 inline-flex leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                        {{ $lesson->date->format('M d, Y') }}
                                    </p>
                                </div>
                                <p class="text-xs text-gray-500">Subject: {{ $lesson->subject->name }}</p>
                                <p class="text-xs text-gray-500">Teacher: {{ $lesson->teacher->user->name }}</p>
                            </li>
                        @empty
                            <li class="px-4 py-3 text-sm text-gray-500">No upcoming lessons.</li>
                        @endforelse
                    </ul>
                    @if(count($upcomingLessons) > 0)
                        <div class="bg-gray-50 px-4 py-3 text-right">
                            <button wire:click="setActiveTab('lessons')" class="text-sm font-medium text-indigo-600 hover:text-indigo-500">
                                View all lessons
                            </button>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Recent Assessments -->
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="px-4 py-5 sm:px-6 flex justify-between items-center">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">
                        Recent Assessments
                    </h3>
                    <button @click="showAssessments = !showAssessments" class="text-gray-400 hover:text-gray-500">
                        <svg x-show="!showAssessments" class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                        <svg x-show="showAssessments" class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                        </svg>
                    </button>
                </div>
                <div x-show="showAssessments" class="border-t border-gray-200">
                    <ul class="divide-y divide-gray-200">
                        @forelse($recentAssessments as $assessment)
                            <li class="px-4 py-3">
                                <div class="flex justify-between">
                                    <p class="text-sm font-medium text-indigo-600">{{ $assessment->book->title }}</p>
                                    <p class="text-xs px-2 inline-flex leading-5 font-semibold rounded-full
                                        {{ $assessment->score >= 80 ? 'bg-green-100 text-green-800' :
                                           ($assessment->score >= 60 ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                        Score: {{ $assessment->score }}%
                                    </p>
                                </div>
                                <p class="text-xs text-gray-500">By: {{ $assessment->book->author->user->name }}</p>
                                @if($assessment->comments)
                                    <p class="text-xs text-gray-500 mt-1">{{ Str::limit($assessment->comments, 50) }}</p>
                                @endif
                            </li>
                        @empty
                            <li class="px-4 py-3 text-sm text-gray-500">No recent assessments.</li>
                        @endforelse
                    </ul>
                    @if(count($recentAssessments) > 0)
                        <div class="bg-gray-50 px-4 py-3 text-right">
                            <button wire:click="setActiveTab('assessments')" class="text-sm font-medium text-indigo-600 hover:text-indigo-500">
                                View all assessments
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Subjects Tab -->
        <div class="{{ $activeTab === 'subjects' ? 'block' : 'hidden' }} mt-6">
            <div class="bg-white shadow overflow-hidden sm:rounded-md">
                <ul class="divide-y divide-gray-200">
                    @forelse($subjects as $subject)
                        <li>
                            <a href="{{ route('student.subjects.show', $subject->slug) }}" class="block hover:bg-gray-50">
                                <div class="px-4 py-4 sm:px-6">
                                    <div class="flex items-center justify-between">
                                        <p class="text-sm font-medium text-indigo-600 truncate">{{ $subject->name }}</p>
                                        <div class="ml-2 flex-shrink-0 flex">
                                            <p class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                {{ $subject->lessons_count }} lessons
                                            </p>
                                        </div>
                                    </div>
                                    <div class="mt-2 sm:flex sm:justify-between">
                                        <div class="sm:flex">
                                            <p class="flex items-center text-sm text-gray-500">
                                                {{ Str::limit($subject->description, 100) }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </li>
                    @empty
                        <li class="px-4 py-5 sm:px-6 text-center text-gray-500">
                            No subjects found for your student group.
                        </li>
                    @endforelse
                </ul>
            </div>
        </div>

        <!-- Books Tab -->
        <div class="{{ $activeTab === 'books' ? 'block' : 'hidden' }} mt-6">
            <!-- Borrowed Books -->
            <h3 class="text-lg font-medium text-gray-900 mb-3">Borrowed Books</h3>
            <div class="bg-white shadow overflow-hidden sm:rounded-md mb-6">
                <ul class="divide-y divide-gray-200">
                    @forelse($borrowedBooks as $borrowing)
                        <li>
                            <div class="px-4 py-4 sm:px-6">
                                <div class="flex items-center justify-between">
                                    <p class="text-sm font-medium text-indigo-600 truncate">{{ $borrowing->book->title }}</p>
                                    <div class="ml-2 flex-shrink-0 flex">
                                        <p class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $borrowing->status === 'borrowed' ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800' }}">
                                            {{ ucfirst($borrowing->status) }}
                                        </p>
                                    </div>
                                </div>
                                <div class="mt-2 sm:flex sm:justify-between">
                                    <div class="sm:flex">
                                        <p class="flex items-center text-sm text-gray-500">
                                            By: {{ $borrowing->book->author->user->name }}
                                        </p>
                                        <p class="mt-2 flex items-center text-sm text-gray-500 sm:mt-0 sm:ml-6">
                                            Category: {{ $borrowing->book->bookCategory->name }}
                                        </p>
                                    </div>
                                    <div class="mt-2 flex items-center text-sm text-gray-500 sm:mt-0">
                                        <p>
                                            Borrowed: {{ $borrowing->borrow_date->format('M d, Y') }}
                                        </p>
                                        <p class="ml-6">
                                            Due: {{ $borrowing->due_date->format('M d, Y') }}
                                        </p>
                                        @if($borrowing->return_date)
                                            <p class="ml-6">
                                                Returned: {{ $borrowing->return_date->format('M d, Y') }}
                                            </p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </li>
                    @empty
                        <li class="px-4 py-5 sm:px-6 text-center text-gray-500">
                            You don't have any borrowed books.
                        </li>
                    @endforelse
                </ul>
            </div>

            <!-- Subscribed Books -->
            <h3 class="text-lg font-medium text-gray-900 mb-3">Subscribed Books</h3>
            <div class="bg-white shadow overflow-hidden sm:rounded-md">
                <ul class="divide-y divide-gray-200">
                    @forelse($subscribedBooks as $subscription)
                        <li>
                            <div class="px-4 py-4 sm:px-6">
                                <div class="flex items-center justify-between">
                                    <p class="text-sm font-medium text-indigo-600 truncate">{{ $subscription->book->title }}</p>
                                    <div class="ml-2 flex-shrink-0 flex">
                                        <p class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                            {{ $subscription instanceof \App\Models\GroupBookSubscription ? 'Group Subscription' : 'Individual Subscription' }}
                                        </p>
                                    </div>
                                </div>
                                <div class="mt-2 sm:flex sm:justify-between">
                                    <div class="sm:flex">
                                        <p class="flex items-center text-sm text-gray-500">
                                            By: {{ $subscription->book->author->user->name }}
                                        </p>
                                        <p class="mt-2 flex items-center text-sm text-gray-500 sm:mt-0 sm:ml-6">
                                            Category: {{ $subscription->book->bookCategory->name }}
                                        </p>
                                    </div>
                                    <div class="mt-2 flex items-center text-sm text-gray-500 sm:mt-0">
                                        <p>
                                            Start: {{ $subscription->start_date->format('M d, Y') }}
                                        </p>
                                        <p class="ml-6">
                                            End: {{ $subscription->end_date->format('M d, Y') }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </li>
                    @empty
                        <li class="px-4 py-5 sm:px-6 text-center text-gray-500">
                            You don't have any subscribed books.
                        </li>
                    @endforelse
                </ul>
            </div>
        </div>

        <!-- Lessons Tab -->
        <div class="{{ $activeTab === 'lessons' ? 'block' : 'hidden' }} mt-6">
            <div class="bg-white shadow overflow-hidden sm:rounded-md">
                <ul class="divide-y divide-gray-200">
                    @forelse($upcomingLessons as $lesson)
                        <li>
                            <a href="{{ route('student.lessons.show', $lesson->slug) }}" class="block hover:bg-gray-50">
                                <div class="px-4 py-4 sm:px-6">
                                    <div class="flex items-center justify-between">
                                        <p class="text-sm font-medium text-indigo-600 truncate">{{ $lesson->title }}</p>
                                        <div class="ml-2 flex-shrink-0 flex">
                                            <p class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                                {{ $lesson->date->format('M d, Y') }}
                                            </p>
                                        </div>
                                    </div>
                                    <div class="mt-2 sm:flex sm:justify-between">
                                        <div class="sm:flex">
                                            <p class="flex items-center text-sm text-gray-500">
                                                Subject: {{ $lesson->subject->name }}
                                            </p>
                                            <p class="mt-2 flex items-center text-sm text-gray-500 sm:mt-0 sm:ml-6">
                                                Teacher: {{ $lesson->teacher->user->name }}
                                            </p>
                                        </div>
                                        <div class="mt-2 flex items-center text-sm text-gray-500 sm:mt-0">
                                            <p>
                                                {{ Str::limit($lesson->description, 50) }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </li>
                    @empty
                        <li class="px-4 py-5 sm:px-6 text-center text-gray-500">
                            No upcoming lessons found.
                        </li>
                    @endforelse
                </ul>
            </div>
        </div>

        <!-- Assessments Tab -->
        <div class="{{ $activeTab === 'assessments' ? 'block' : 'hidden' }} mt-6">
            <div class="bg-white shadow overflow-hidden sm:rounded-md">
                <ul class="divide-y divide-gray-200">
                    @forelse($recentAssessments as $assessment)
                        <li>
                            <div class="px-4 py-4 sm:px-6">
                                <div class="flex items-center justify-between">
                                    <p class="text-sm font-medium text-indigo-600 truncate">{{ $assessment->book->title }}</p>
                                    <div class="ml-2 flex-shrink-0 flex">
                                        <p class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                            {{ $assessment->score >= 80 ? 'bg-green-100 text-green-800' :
                                            ($assessment->score >= 60 ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                            Score: {{ $assessment->score }}%
                                        </p>
                                    </div>
                                </div>
                                <div class="mt-2 sm:flex sm:justify-between">
                                    <div class="sm:flex">
                                        <p class="flex items-center text-sm text-gray-500">
                                            Book by: {{ $assessment->book->author->user->name }}
                                        </p>
                                        <p class="mt-2 flex items-center text-sm text-gray-500 sm:mt-0 sm:ml-6">
                                            Category: {{ $assessment->book->bookCategory->name }}
                                        </p>
                                    </div>
                                </div>
                                @if($assessment->comments)
                                    <div class="mt-2">
                                        <p class="text-sm text-gray-500">Comments: {{ $assessment->comments }}</p>
                                    </div>
                                @endif
                            </div>
                        </li>
                    @empty
                        <li class="px-4 py-5 sm:px-6 text-center text-gray-500">
                            No assessment records found.
                        </li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>
</div>
