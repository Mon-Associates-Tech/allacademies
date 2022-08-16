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