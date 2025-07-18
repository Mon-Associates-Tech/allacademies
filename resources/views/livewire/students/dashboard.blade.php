<div>
@if(Auth::user()->role === 'student')
    @if(Auth::user()->student)
        @if($activeTab === 'dashboard')
            @livewire('students.overview')
        @elseif($activeTab === 'assessments')
            @livewire('students.self-assessment')
        @elseif($activeTab === 'books')
            @livewire('students.books')
            @elseif($activeTab === 'courses')
                @livewire('students.courses')
        @elseif($activeTab === 'schedules')
            @livewire('students.student-schedule')
        @elseif($activeTab === 'performance')
            @livewire('students.performance-overview')
        @elseif($activeTab === 'profile')
            @livewire('students.student-profile')
        @elseif($activeTab === 'activities')
            @livewire('students.activity-logs')
        @endif
    @else
        <div class="alert alert-warning">
            Your student profile is being set up. Please contact an administrator if this message persists.
        </div>
    @endif
@else
    <div>Student Account not active</div>
    @endif
</div>

