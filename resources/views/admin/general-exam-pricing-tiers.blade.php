<x-layouts.app page-name="Exam Pricing Tiers" :show-title-area="false">
    <div class="max-w-4xl mx-auto px-4 py-8">
        <div class="mb-6">
            <h1 class="text-xl font-bold text-gray-800 dark:text-gray-100">General Exam Pricing Tiers</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Configure per-subject pricing for online and print exam subscriptions.</p>
        </div>
        @livewire('owner.general-exam-pricing-tier-manager')
    </div>
</x-layouts.app>
