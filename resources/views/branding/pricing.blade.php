<x-layouts.guest>
    <div class="bg-white dark:bg-gray-900 transition-colors duration-300 min-h-screen">
        
        <div class="py-24 bg-white dark:bg-gray-900 transition-colors duration-300">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-16">
                    <h1 class="text-4xl md:text-5xl font-extrabold text-gray-900 dark:text-white mb-4">
                        Pricing Plans
                    </h1>
                    <p class="text-xl text-gray-600 dark:text-gray-300 max-w-3xl mx-auto">
                        Choose the perfect plan for your educational journey. Flexible pricing for individuals and institutions.
                    </p>
                </div>
                
                @include('branding.partials.pricing')
            </div>
        </div>
    </div>
</x-app>
