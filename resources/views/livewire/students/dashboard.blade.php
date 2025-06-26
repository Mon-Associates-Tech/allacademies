    <div>
        @if($activeTab === 'dashboard')
            @livewire('students.overview')
        @elseif($activeTab === 'assessments')
            @livewire('students.self-assessment')
        @elseif($activeTab === 'books')
            @livewire('students.books')
        @elseif($activeTab === 'schedules')
            @livewire('students.student-schedule')
        @elseif($activeTab === 'performance')
            @livewire('students.performance-overview')
        @elseif($activeTab === 'profile')
            @livewire('students.student-profile')
        @elseif($activeTab === 'activities')
            @livewire('students.activity-logs')
        @endif
    </div>
