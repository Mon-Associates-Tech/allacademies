<x-app-layout :sidebarVariant="'v1'" :headerVariant="'v2'" title="{{ __('Student Dashboard') }}" pageName="Student Dashboard">
    <div>
        <!-- Tab content based on activeTab -->
        @if($activeTab === 'dashboard')
            @livewire('students.dashboard', [
                'recentBooks' => $recentBooks,
                'bookCount' => $bookCount,
                'recentAssessments' => $recentAssessments,
                'upcomingActivities' => $upcomingActivities,
                'upcomingActivitiesCount' => $upcomingActivitiesCount,
                'overallScore' => $overallScore,
                'subjectPerformance' => $subjectPerformance
            ])
        @elseif($activeTab === 'self-assessment')
            @livewire('students.self-assessment')
        @elseif($activeTab === 'my-books')
            @livewire('students.books')
        @elseif($activeTab === 'schedule')
            @livewire('students.schedule')
        @elseif($activeTab === 'performance')
            @livewire('students.performance')
        @elseif($activeTab === 'profile')
            @livewire('students.profile')
        @elseif($activeTab === 'activity-log')
            @livewire('students.activity-logs')
        @endif
    </div>
</x-app-layout>
