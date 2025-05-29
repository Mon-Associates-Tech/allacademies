<div class="admin-dashboard">
    <div class="flex">
        <!-- Main Content Area -->
        <div class="flex-1 bg-gray-100 min-h-screen mx-auto">
            <div class="bg-white rounded-lg shadow-md p-6">
                @if($activeTab === 'overview')
                    <livewire:administrators.overview />
                @elseif($activeTab === 'users')
                    <livewire:administrators.user-management />
                @elseif($activeTab === 'students')
                    <livewire:administrators.student-management />
                @elseif($activeTab === 'groups')
                    <livewire:administrators.group-management />
                @elseif($activeTab === 'teachers')
                    <livewire:administrators.teacher-management />
                @elseif($activeTab === 'librarians')
                    <livewire:administrators.librarian-management />
                @elseif($activeTab === 'authors')
                    <livewire:administrators.author-management />
                @elseif($activeTab === 'books')
                    <livewire:administrators.book-management />
                @elseif($activeTab === 'book-approvals')
                    <livewire:administrators.book-approval-management />
                @elseif($activeTab === 'subjects')
                    <livewire:administrators.subject-management />
                @elseif($activeTab === 'reports')
                    <livewire:administrators.reports-management />
                @endif
            </div>
        </div>
    </div>
</div>
