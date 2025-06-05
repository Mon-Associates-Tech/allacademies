    <div>
        @if($activeTab === 'dashboard')
            @livewire('students.overview')
        @elseif($activeTab === 'self-assessment')
            @livewire('students.self-assessment')
        @elseif($activeTab === 'my-books')
            @livewire('students.books')
        @elseif($activeTab === 'schedule')
            @livewire('students.student-schedule')
        @elseif($activeTab === 'performance')
            @livewire('students.performance-overview')
        @elseif($activeTab === 'profile')
            @livewire('students.student-profile')
        @elseif($activeTab === 'activity-log')
            @livewire('students.activity-logs')
        @endif
    </div>
