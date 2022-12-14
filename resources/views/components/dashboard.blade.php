@props(['title', 'summary'])

<x-layout>
    <div class="w-64 fixed inset-y-0 py-20 bg-primary-600 space-y-10">
        <div class="text-center text-primary-100 space-y-1">
            <div class="grid place-content-center">
                <svg class="w-12 h-12" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round" class="feather feather-user"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
            </div>
            <p class="">{{ auth()->user()->name }}</p>
            <p class="text-xs">{{ auth()->user()->email }}</p>
            <form method="POST" action="{{ route('sign-out') }}">
                @csrf
                <button class="text-xs text-primary-300" type="submit">Sign Out</button>
            </form>
        </div>
        <div class="px-10 text-primary-100 space-y-2">
            <div class="flex items-center group">
                <svg class="w-5 h-5 text-primary-200 group-hover:text-primary-100" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="feather feather-home"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>
                <a href="{{ route('dashboard') }}" class="ml-2 block group-hover:text-white">Dashboard</a>
            </div>
            @can('administrate')
            <div class="flex items-center group">
                <svg class="w-5 h-5 text-primary-200 group-hover:text-primary-100" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="feather feather-hard-drive"><line x1="22" y1="12" x2="2" y2="12"></line><path d="M5.45 5.11L2 12v6a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-6l-3.45-6.89A2 2 0 0 0 16.76 4H7.24a2 2 0 0 0-1.79 1.11z"></path><line x1="6" y1="16" x2="6.01" y2="16"></line><line x1="10" y1="16" x2="10.01" y2="16"></line></svg>
                <a href="{{ route('academic-groups.index') }}" class="ml-2 block group-hover:text-white">Academic Groups</a>
            </div>
            @endcan
            @can('moderate')
            <div class="flex items-center group">
                <svg class="w-5 h-5 text-primary-200 group-hover:text-primary-100" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="feather feather-layers"><polygon points="12 2 2 7 12 12 22 7 12 2"></polygon><polyline points="2 17 12 22 22 17"></polyline><polyline points="2 12 12 17 22 12"></polyline></svg>
                <a href="{{ route('academic-levels.index') }}" class="ml-2 block group-hover:text-white">Academic Levels</a>
            </div>
            @endcan
            <div class="flex items-center group">
                <svg class="w-5 h-5 text-primary-200 group-hover:text-primary-100" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="feather feather-book"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path></svg>
                <a href="{{ route('academic-subjects.index') }}" class="ml-2 block group-hover:text-white">Academic Subjects</a>
            </div>
            @can('moderate')
            <div class="flex items-center group">
                <svg class="w-5 h-5 text-primary-200 group-hover:text-primary-100" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="feather feather-list"><line x1="8" y1="6" x2="21" y2="6"></line><line x1="8" y1="12" x2="21" y2="12"></line><line x1="8" y1="18" x2="21" y2="18"></line><line x1="3" y1="6" x2="3.01" y2="6"></line><line x1="3" y1="12" x2="3.01" y2="12"></line><line x1="3" y1="18" x2="3.01" y2="18"></line></svg>
                <a href="{{ route('academic-topics.index') }}" class="ml-2 block group-hover:text-white">Academic Topics</a>
            </div>
            @endcan
            @can('moderate')
            <div class="flex items-center group">
                <svg class="w-5 h-5 text-primary-200 group-hover:text-primary-100" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="feather feather-sliders"><line x1="4" y1="21" x2="4" y2="14"></line><line x1="4" y1="10" x2="4" y2="3"></line><line x1="12" y1="21" x2="12" y2="12"></line><line x1="12" y1="8" x2="12" y2="3"></line><line x1="20" y1="21" x2="20" y2="16"></line><line x1="20" y1="12" x2="20" y2="3"></line><line x1="1" y1="14" x2="7" y2="14"></line><line x1="9" y1="8" x2="15" y2="8"></line><line x1="17" y1="16" x2="23" y2="16"></line></svg>
                <a href="{{ route('multiple-choice-questions.index') }}" class="ml-2 block group-hover:text-white">MC Questions</a>
            </div>
            @endcan
            @can('moderate')
            <div class="flex items-center group">
                <svg class="w-5 h-5 text-primary-200 group-hover:text-primary-100" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="feather feather-file-text"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
                <a href="{{ route('essay-questions.index') }}" class="ml-2 block group-hover:text-white">Essay Questions</a>
            </div>
            @endcan
            @can('moderate')
            <div class="flex items-center group">
                <svg class="w-5 h-5 text-primary-200 group-hover:text-primary-100" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="feather feather-check-circle"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
                <a href="{{ route('true-or-false-questions.index') }}" class="ml-2 block group-hover:text-white">T/F Questions</a>
            </div>
            @endcan
            <div class="flex items-center group">
                <svg class="w-5 h-5 text-primary-200 group-hover:text-primary-100" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="feather feather-archive"><polyline points="21 8 21 21 3 21 3 8"></polyline><rect x="1" y="3" width="22" height="5"></rect><line x1="10" y1="12" x2="14" y2="12"></line></svg>
                <a href="{{ route('examinations.index') }}" class="ml-2 block group-hover:text-white">Examinations</a>
            </div>
            <div class="flex items-center group">
                <svg class="w-5 h-5 text-primary-200 group-hover:text-primary-100" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="feather feather-rss"><path d="M4 11a9 9 0 0 1 9 9"></path><path d="M4 4a16 16 0 0 1 16 16"></path><circle cx="5" cy="19" r="1"></circle></svg>
                <a href="{{ route('subscriptions.index') }}" class="ml-2 block group-hover:text-white">Subscriptions</a>
            </div>
            @can('administrate')
            <div class="flex items-center group">
                <svg class="w-5 h-5 text-primary-200 group-hover:text-primary-100" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="feather feather-dollar-sign"><line x1="12" y1="1" x2="12" y2="23"></line><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path></svg>
                <a href="{{ route('payments.index') }}" class="ml-2 block group-hover:text-white">Payments</a>
            </div>
            @endcan
            <div class="flex items-center group">
                <svg class="w-5 h-5 text-primary-200 group-hover:text-primary-100" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="feather feather-users"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                <a href="{{ route('teams.index') }}" class="ml-2 block group-hover:text-white">Team Members</a>
            </div>
            @can('preside')
            <div class="flex items-center group">
                <svg class="w-5 h-5 text-primary-200 group-hover:text-primary-100" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="feather feather-settings"><circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path></svg>
                <a href="{{ route('settings.index') }}" class="ml-2 block group-hover:text-white">Settings</a>
            </div>
            @endcan
        </div>
    </div>
    <div class="pl-64">
        <div class="p-8">
            <div class="py-4">
                <div class="text-xl font-semibold tracking-wider text-gray-900">{{ $title }}</div>
                <div class="text-sm font-light text-gray-500">{{ $summary }}</div>
            </div>
            {{ $slot }}
        </div>
    </div>
</x-layout>