@php use App\Enums\UserRole; @endphp

<div class="space-y-6" x-data="{ sidebarExpanded: $store.sidebar.expanded }">
    {{-- ═══════════════════════════════════════════════════════════
         SECTION: Dashboard & Overview
     ═══════════════════════════════════════════════════════════ --}}
    <x-app.nav-item
        href="{{ route('dashboard') }}"
        :active="Route::is('dashboard')"
        class="mt-4"
        icon="heroicon-o-home"
    >Dashboard
    </x-app.nav-item>

    @if(auth()->user()->role === UserRole::OWNER)
        <x-app.nav-item
            href="{{ route('admin.school-switcher') }}"
            :active="Route::is('admin.school-switcher')"
            icon="heroicon-o-building-office-2"
        >School Switcher
        </x-app.nav-item>
    @endif

    {{-- ═══════════════════════════════════════════════════════════
         SECTION: User & Role Management
     ═══════════════════════════════════════════════════════════ --}}
    <x-app.nav-section
        id="admin-nav-people"
        label="People"
        :open="true"
    >
        <x-app.nav-item
            href="{{ route('users.index') }}"
            :active="Route::is('users.index')"
            icon="heroicon-o-users"
        >User Management
        </x-app.nav-item>

        @if(auth()->user()->role === UserRole::OWNER)
            <x-app.nav-item
                href="{{ route('admin.student-management') }}"
                :active="Route::is('admin.student-management')"
                icon="heroicon-o-academic-cap"
            >Students
            </x-app.nav-item>

            <x-app.nav-item
                href="{{ route('admin.teacher-management') }}"
                :active="Route::is('admin.teacher-management')"
                icon="heroicon-o-user-group"
            >Teachers
            </x-app.nav-item>

            <x-app.nav-item
                href="{{ route('admin.parent-management') }}"
                :active="Route::is('admin.parent-management')"
                icon="heroicon-o-users"
            >Parents
            </x-app.nav-item>

            <x-app.nav-item
                href="{{ route('admin.librarian-management') }}"
                :active="Route::is('admin.librarian-management')"
                icon="heroicon-o-book-open"
            >Librarians
            </x-app.nav-item>

            <x-app.nav-item
                href="{{ route('admin.accountant-management') }}"
                :active="Route::is('admin.accountant-management')"
                icon="heroicon-o-calculator"
            >Accountants
            </x-app.nav-item>

            <x-app.nav-item
                href="{{ route('admin.author-management') }}"
                :active="Route::is('admin.author-management')"
                icon="heroicon-o-pencil-square"
            >Authors
            </x-app.nav-item>
        @endif

        {{-- Student Groups --}}
        <x-app.nav-item
            href="{{ route('admin.student-groups') }}"
            :active="Route::is('admin.student-groups')"
            icon="heroicon-o-users"
        >Student Groups
        </x-app.nav-item>

        {{-- Impersonation (hidden by default) --}}
        <x-app.nav-item
            href="{{ route('admin.users.impersonate') }}"
            :active="Route::is('admin.users.impersonate')"
            icon="heroicon-o-user-circle"
            class="hidden"
        >Impersonate User
        </x-app.nav-item>
    </x-app.nav-section>

    {{-- ═══════════════════════════════════════════════════════════
         SECTION: Academic & Content Management
     ═══════════════════════════════════════════════════════════ --}}
    <x-app.nav-section
        id="admin-nav-academics"
        label="Academics"
        :open="true"
    >
        @if(auth()->user()->role === UserRole::OWNER)
            <x-app.nav-item
                href="{{ route('academic-groups.index') }}"
                :active="Route::is('academic-groups.index')"
                icon="heroicon-o-book-open"
            >Academic Structure
            </x-app.nav-item>

            <x-app.nav-item
                href="{{ route('admin.subject-management') }}"
                :active="Route::is('admin.subject-management')"
                icon="heroicon-o-list-bullet"
            >Subjects
            </x-app.nav-item>

            <x-app.nav-item
                href="{{ route('admin.question-availability') }}"
                :active="Route::is('admin.question-availability')"
                icon="heroicon-o-magnifying-glass"
            >Question Bank
            </x-app.nav-item>
        @endif

            {{-- Timetable --}}
            <x-app.nav-item
                href="{{ route('timetable.index') }}"
                :active="Route::is('timetable.*')"
                icon="heroicon-o-calendar-days"
            >Timetable
            </x-app.nav-item>

        {{-- Report Cards --}}
        <x-app.nav-item
            href="{{ route('admin.report-cards') }}"
            :active="Route::is('admin.report-cards')"
            icon="heroicon-o-document-text"
        >Report Cards
        </x-app.nav-item>
    </x-app.nav-section>


    {{-- ═══════════════════════════════════════════════════════════
         SECTION: Settings & Configuration
     ═══════════════════════════════════════════════════════════ --}}
    <x-app.nav-section
        id="admin-nav-settings"
        label="Settings"
        :open="true"
    >
        <x-app.nav-item
            href="{{ route('school-settings.index') }}"
            :active="Route::is('school-settings*')"
            icon="heroicon-o-cog-6-tooth"
        >Academic Settings
        </x-app.nav-item>

        <x-app.nav-item
            href="{{ route('admin.settings.certificate-templates') }}"
            :active="Route::is('admin.settings.certificate-templates')"
            icon="heroicon-o-document-duplicate"
        >Certificate Templates
        </x-app.nav-item>

        {{-- Hidden: ID Card Templates --}}
        <x-app.nav-item
            href="{{ route('admin.settings.id-card-templates') }}"
            :active="Route::is('admin.settings.id-card-templates')"
            icon="heroicon-o-identification"
            class="hidden"
        >ID Card Templates
        </x-app.nav-item>

        <x-app.nav-item
            href="{{ route('onboarding.school-setup') }}"
            :active="Route::is('onboarding.*')"
            icon="heroicon-o-wrench-screwdriver"
        >School Setup
        </x-app.nav-item>
    </x-app.nav-section>


    {{-- ═══════════════════════════════════════════════════════════
         SECTION: Library & Books
     ═══════════════════════════════════════════════════════════ --}}
    @if(auth()->user()->role === UserRole::OWNER)
        <x-app.nav-section
            id="admin-nav-library"
            label="Library"
            :open="true"
        >
            <x-app.nav-item
                href="{{ route('admin.book-management') }}"
                :active="Route::is('admin.book-management')"
                icon="heroicon-c-bookmark"
            >Book Catalog
            </x-app.nav-item>

            <x-app.nav-item
                href="{{ route('admin.book-approvals') }}"
                :active="Route::is('admin.book-approvals')"
                icon="heroicon-o-check-circle"
            >Book Approvals
            </x-app.nav-item>
        </x-app.nav-section>
    @endif

    {{-- ═══════════════════════════════════════════════════════════
         SECTION: Examinations & Assessments
     ═══════════════════════════════════════════════════════════ --}}
    <x-app.nav-section
        id="admin-nav-examinations"
        label="Examinations"
        :open="true"
    >
        <x-app.nav-item
            href="{{ route('teachers.general-exams.index') }}"
            :active="Route::is('teachers.general-exams.index')"
            icon="heroicon-o-document"
        >Manage Exams
        </x-app.nav-item>

        <x-app.nav-item
            href="{{ route('teachers.general-exams.create') }}"
            :active="Route::is('teachers.general-exams.create')"
            icon="heroicon-o-plus-circle"
            class="hidden"
        >Create Exam
        </x-app.nav-item>

        <x-app.nav-item
            href="{{ route('general-exams.subscription.dashboard') }}"
            :active="Route::is('general-exams.subscription.dashboard')"
            icon="heroicon-o-ticket"
        >Exam Subscriptions
        </x-app.nav-item>


        <x-app.nav-item
            href="{{ route('mock-exams.index') }}"
            :active="Route::is('mock-exams.*')"
            icon="heroicon-o-clipboard-document-check"
        >Mock Exams
        </x-app.nav-item>

        <x-app.nav-item
            href="{{ route('mock-exams.templates.index') }}"
            :active="Route::is('mock-exams.templates.*')"
            icon="heroicon-o-document-duplicate"
        >Mock Exam Templates
        </x-app.nav-item>

        @if(auth()->user()->role === UserRole::OWNER)
            <x-app.nav-item
                href="{{ route('admin.generate-examination') }}"
                :active="Route::is('admin.generate-examination')"
                icon="heroicon-o-sparkles"
            >AI Exam Generator
            </x-app.nav-item>
        @endif
    </x-app.nav-section>

    {{-- ═══════════════════════════════════════════════════════════
         SECTION: Finance & Payments
     ═══════════════════════════════════════════════════════════ --}}
    <x-app.nav-section
        id="admin-nav-finance"
        label="Finance"
        :open="true"
    >
        <x-app.nav-item
            href="{{ route('admin.payments.index') }}"
            :active="Route::is('admin.payments*')"
            icon="heroicon-o-credit-card"
        >Payments
        </x-app.nav-item>

        <x-app.nav-item
            href="{{ route('admin.transactions.index') }}"
            :active="Route::is('admin.transactions*')"
            icon="heroicon-o-arrow-trending-up"
        >Transactions
        </x-app.nav-item>

        @if(auth()->user()->role === UserRole::OWNER)
            <x-app.nav-item
                href="{{ route('token-allocations.index') }}"
                :active="Route::is('token-allocations*')"
                icon="heroicon-o-ticket"
            >Messenger Allocations
            </x-app.nav-item>

            <x-app.nav-item
                href="{{ route('admin.messenger-transactions.index') }}"
                :active="Route::is('admin.messenger-transactions*')"
                icon="heroicon-o-arrows-right-left"
            >Messenger Transactions
            </x-app.nav-item>
        @endif
    </x-app.nav-section>

    {{-- ═══════════════════════════════════════════════════════════
         SECTION: Communication
     ═══════════════════════════════════════════════════════════ --}}
    <x-app.nav-section
        id="admin-nav-communication"
        label="Communication"
        :open="true"
    >
        <x-app.nav-item
            href="{{ route('notifications.index') }}"
            :active="Route::is('notifications*')"
            icon="heroicon-o-bell"
        >Notifications
        </x-app.nav-item>

        <x-app.nav-item
            href="{{ route('chat') }}"
            :active="Route::is('chat*')"
            icon="heroicon-o-chat-bubble-left-right"
        >Group Chat
        </x-app.nav-item>

        <x-app.nav-item
            href="{{ route('research-assistant.index') }}"
            :active="Route::is('research-assistant*')"
            icon="heroicon-o-sparkles"
        >Research Assistant
        </x-app.nav-item>

        <x-app.nav-item
            href="{{ route('forums') }}"
            :active="Route::is('forums*')"
            icon="heroicon-o-chat-bubble-left-ellipsis"
        >Discussion Forums
        </x-app.nav-item>

        {{-- Hidden: Message Center --}}
        <x-app.nav-item
            href="{{ route('admin.messages.index') }}"
            :active="Route::is('admin.messages*')"
            icon="heroicon-o-envelope"
            class="hidden"
        >Message Center
        </x-app.nav-item>
    </x-app.nav-section>

    {{-- ═══════════════════════════════════════════════════════════
         SECTION: Owner Tools (Admin Only)
     ═══════════════════════════════════════════════════════════ --}}
    @if(auth()->user()->role === UserRole::OWNER)
        <x-app.nav-section
            id="admin-nav-owner-tools"
            label="Owner Tools"
            :open="true"
        >
            <x-app.nav-item
                href="{{ route('admin.pricing-settings.edit') }}"
                :active="Route::is('admin.pricing-settings.*')"
                icon="heroicon-o-currency-dollar"
            >Pricing Settings
            </x-app.nav-item>

            <x-app.nav-item
                href="{{ route('admin.general-exams.subscriptions') }}"
                :active="Route::is('admin.general-exams.subscriptions')"
                icon="heroicon-o-ticket"
            >Exam Subscriptions
            </x-app.nav-item>

            <x-app.nav-item
                href="{{ route('admin.general-exams.pricing-tiers') }}"
                :active="Route::is('admin.general-exams.pricing-tiers')"
                icon="heroicon-o-tag"
            >Exam Pricing Tiers
            </x-app.nav-item>

            <x-app.nav-item
                href="{{ route('admin.pricing-settings.audits') }}"
                :active="Route::is('admin.pricing-settings.audits')"
                icon="heroicon-o-clipboard-document-list"
            >Pricing Audit Log
            </x-app.nav-item>

            <x-app.nav-item
                href="{{ route('admin.artisan-commands') }}"
                :active="Route::is('admin.artisan-commands')"
                icon="heroicon-o-command-line"
            >System Commands
            </x-app.nav-item>

            <x-app.nav-item
                href="{{ route('admin.logins') }}"
                :active="Route::is('admin.logins')"
                icon="heroicon-o-key"
            >Login Activity
            </x-app.nav-item>

            <x-app.nav-item
                href="{{ route('admin.activity-trail.index') }}"
                :active="Route::is('admin.activity-trail*')"
                icon="heroicon-o-list-bullet"
            >User Actions
            </x-app.nav-item>

            <x-app.nav-item
                href="{{ route('admin.academic-activities') }}"
                :active="Route::is('admin.academic-activities*')"
                icon="heroicon-o-shield-check"
            >Moderator Activities
            </x-app.nav-item>

            {{-- Hidden: Media & ChangeLog --}}
            <x-app.nav-item
                href="{{ route('media.index') }}"
                :active="Route::is('media*')"
                icon="heroicon-o-photo"
                class="hidden"
            >Media Management
            </x-app.nav-item>

            <x-app.nav-item
                href="{{ route('admin.change-log.index') }}"
                :active="Route::is('admin.change-log*')"
                icon="heroicon-o-clock"
                class="hidden"
            >Change Log
            </x-app.nav-item>
        </x-app.nav-section>
    @endif

    {{-- ═══════════════════════════════════════════════════════════
         SECTION: LMS & Courses
     ═══════════════════════════════════════════════════════════ --}}
    @can('create', App\Models\Lms\Course::class)
        <x-app.nav-section
            id="admin-nav-lms"
            label="LMS"
            :open="true"
            :bordered="true"
        >
            <x-app.nav-item
                href="{{ route('course-management.index') }}"
                :active="Route::is('course-management.*')"
                icon="heroicon-o-book-open"
            >Course Management
            </x-app.nav-item>
        </x-app.nav-section>
    @endcan

</div>
