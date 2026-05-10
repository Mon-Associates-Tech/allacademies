<x-layouts.app>
    <x-examinations-hub.navigation active="admin" />
    
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">Examinations Admin</h1>
            <p class="text-sm text-gray-500">Dedicated administration for pricing tiers and subscription allocations.</p>
        </div>

        <div class="grid lg:grid-cols-2 gap-6">
            <section class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                <h2 class="font-semibold mb-3">Subscription Allocations</h2>
                @livewire('owner.general-exam-subscription-manager')
            </section>
            <section class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                <h2 class="font-semibold mb-3">Pricing Tiers</h2>
                @livewire('owner.general-exam-pricing-tier-manager')
            </section>
        </div>
    </div>
</x-layouts.app>

