<div class="container">
    <div class="bg-white rounded-lg shadow">
        @if($activeTab === 'overview')
            <livewire:administrators.overview/>
        @elseif($activeTab === 'users')
            <livewire:administrators.user-management/>
        @elseif($activeTab === 'students')
            <livewire:administrators.student-management/>
        @elseif($activeTab === 'groups')
            <livewire:administrators.group-management/>
        @elseif($activeTab === 'teachers')
            <livewire:administrators.teacher-management/>
        @elseif($activeTab === 'librarians')
            <livewire:administrators.librarian-management/>
        @elseif($activeTab === 'authors')
            <livewire:administrators.author-management/>
        @elseif($activeTab === 'books')
            <livewire:administrators.book-management/>
        @elseif($activeTab === 'book-approvals')
            <livewire:administrators.book-approval-management/>
        @elseif($activeTab === 'subjects')
            <livewire:administrators.subject-management/>
        @elseif($activeTab === 'reports')
            <livewire:administrators.reports-management/>
        @elseif($activeTab === 'academic-management')
            <livewire:academic-management.academic-group/>
        @elseif($activeTab === 'user-logins')
            <livewire:administrators.user-login-log/>
        @endif

    </div>
</div>

