@props(['title', 'summary'])

<x-layout>
    <div class="w-64 fixed inset-y-0 py-20 bg-primary-600 space-y-10">
        <div class="text-center text-primary-100 space-y-1">
            <div class="grid place-content-center">
                <svg class="w-12 h-12" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round" class="feather feather-user"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
            </div>
            <p class="">{{ auth()->user()->name }}</p>
            <p class="text-xs">{{ auth()->user()->email }}</p>
        </div>
        <div class="px-10 text-primary-100 space-y-2">
            <div class="flex items-center group">
                <svg class="w-5 h-5 text-primary-200 group-hover:text-primary-100" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="feather feather-home"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>
                <a href="{{ route('dashboard') }}" class="ml-2 block group-hover:text-white">Dashboard</a>
            </div>
            <div class="flex items-center group">
                <svg class="w-5 h-5 text-primary-200 group-hover:text-primary-100" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="feather feather-layers"><polygon points="12 2 2 7 12 12 22 7 12 2"></polygon><polyline points="2 17 12 22 22 17"></polyline><polyline points="2 12 12 17 22 12"></polyline></svg>
                <a href="{{ route('academic-levels.index') }}" class="ml-2 block group-hover:text-white">Academic Levels</a>
            </div>
            <div class="flex items-center group">
                <svg class="w-5 h-5 text-primary-200 group-hover:text-primary-100" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="feather feather-book"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path></svg>
                <a href="{{ route('academic-subjects.index') }}" class="ml-2 block group-hover:text-white">Academic Subjects</a>
            </div>
            <div class="flex items-center group">
                <svg class="w-5 h-5 text-primary-200 group-hover:text-primary-100" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="feather feather-list"><line x1="8" y1="6" x2="21" y2="6"></line><line x1="8" y1="12" x2="21" y2="12"></line><line x1="8" y1="18" x2="21" y2="18"></line><line x1="3" y1="6" x2="3.01" y2="6"></line><line x1="3" y1="12" x2="3.01" y2="12"></line><line x1="3" y1="18" x2="3.01" y2="18"></line></svg>
                <a href="{{ route('academic-topics.index') }}" class="ml-2 block group-hover:text-white">Academic Topics</a>
            </div>
            <div class="flex items-center group">
                <svg class="w-5 h-5 text-primary-200 group-hover:text-primary-100" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="feather feather-sliders"><line x1="4" y1="21" x2="4" y2="14"></line><line x1="4" y1="10" x2="4" y2="3"></line><line x1="12" y1="21" x2="12" y2="12"></line><line x1="12" y1="8" x2="12" y2="3"></line><line x1="20" y1="21" x2="20" y2="16"></line><line x1="20" y1="12" x2="20" y2="3"></line><line x1="1" y1="14" x2="7" y2="14"></line><line x1="9" y1="8" x2="15" y2="8"></line><line x1="17" y1="16" x2="23" y2="16"></line></svg>
                <a href="{{ route('multiple-choice-questions.index') }}" class="ml-2 block group-hover:text-white">MC Questions</a>
            </div>
            <div class="flex items-center group">
                <svg class="w-5 h-5 text-primary-200 group-hover:text-primary-100" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="feather feather-file-text"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
                <a href="{{ route('essay-questions.index') }}" class="ml-2 block group-hover:text-white">Essay Questions</a>
            </div>
            <div class="flex items-center group">
                <svg class="w-5 h-5 text-primary-200 group-hover:text-primary-100" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="feather feather-check-circle"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
                <a href="{{ route('true-or-false-questions.index') }}" class="ml-2 block group-hover:text-white">T/F Questions</a>
            </div>
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