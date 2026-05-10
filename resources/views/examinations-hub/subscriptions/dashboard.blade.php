<x-layouts.app>
    <x-examinations-hub.navigation active="subscriptions" />
    
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <div class="mb-4">
            <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">Examination Subscriptions</h1>
            <p class="text-sm text-gray-500">Hub subscription management for exam usage and participant limits.</p>
        </div>
        @livewire('teachers.general-exam-subscription-dashboard')
    </div>
</x-layouts.app>

