<x-layouts.app page-name="Exam Subscriptions" :show-title-area="false">
    <div class="max-w-7xl mx-auto px-4 py-8">
        <div class="mb-6">
            <h1 class="text-xl font-bold text-gray-800 dark:text-gray-100">General Exam Subscriptions</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Manage your exam subscriptions, view results, and track participant performance.</p>
        </div>
        @livewire('teachers.general-exam-subscription-dashboard')
    </div>
</x-layouts.app>
