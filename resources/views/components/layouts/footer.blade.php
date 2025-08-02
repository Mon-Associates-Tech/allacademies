<footer class="bg-gray-50 dark:bg-gray-900 border-t border-gray-200 dark:border-gray-700">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
            <!-- Brand -->
            <div class="col-span-1 md:col-span-1">
                <div class="flex items-center space-x-2 mb-4">
                    <x-logo class="h-8 w-auto"/>
                    <span class="text-xl font-bold text-gray-900 dark:text-white">
                        {{ config('app.name') }}
                    </span>
                </div>
                <p class="text-gray-600 dark:text-gray-400 max-w-md">
                    Empowering education through innovative digital learning solutions.
                    Join thousands of students and educators in transforming the way we learn.
                </p>
            </div>

            <!-- Links -->
            <div>
                <h3 class="text-sm font-semibold text-gray-900 dark:text-white uppercase tracking-wider mb-4">
                    Product
                </h3>
                <ul class="space-y-3">
                    <li><a href="{{route('home')}}"
                           class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition-colors duration-200">Features</a>
                    </li>
                    <li><a href="{{route('home')}}"
                           class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition-colors duration-200">Pricing</a>
                    </li>
                    <li><a href="{{route('home')}}"
                           class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition-colors duration-200">Modules</a>
                    </li>
                </ul>
            </div>

            <div>
                <h3 class="text-sm font-semibold text-gray-900 dark:text-white uppercase tracking-wider mb-4">
                    Support
                </h3>
                <ul class="space-y-3">
                    <li><a href="{{route('branding.contact')}}"
                           class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition-colors duration-200">Help Center</a></li>
                    <li><a href="{{route('branding.contact')}}"
                           class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition-colors duration-200">Contact Us</a></li>
                    <li><a href="{{route('branding.privacy')}}"
                           class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition-colors duration-200">Privacy Policy</a></li>
                    <li><a href="{{route('branding.terms')}}"
                           class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition-colors duration-200">Terms of Service</a></li>
                </ul>
            </div>

            <!-- Newsletter & Contact -->
            <div>
                <h3 class="text-sm font-semibold text-gray-900 dark:text-white uppercase tracking-wider mb-4">
                    Stay Connected
                </h3>

                <!-- Newsletter Subscription -->
                <x-newsletter.subscription-form
                    theme="light"
                    size="compact"
                    button-text="Subscribe"
                />
            </div>
        </div>

        <div class="my-auto mt-2 pb-3">
            <!-- Contact Information -->
            <div class="space-x-4  flex">
                <div class="flex items-center my-auto text-sm text-gray-600 dark:text-gray-400">
                    <svg class="w-4 h-4 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                    <a href="mailto:{{config('company.email')}}"
                       class="hover:text-gray-900 my-auto dark:hover:text-white transition-colors">
                        {{config('company.email')}}
                    </a>
                </div>

                <div class="flex items-center my-auto text-sm text-gray-600 dark:text-gray-400">
                    <svg class="w-4 h-4 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                    </svg>
                    <span>{{config('company.phone')}}</span>
                </div>

                <div class="flex items-start text-sm my-auto text-gray-600 dark:text-gray-400">
                    <svg class="w-4 h-4 mr-2 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    <span>{{config('company.address')}}</span>
                </div>
            </div>
        </div>

        <div class="pt-2 border-t my-aut border-gray-200 dark:border-gray-700">
            <p class="text-center text-gray-600 dark:text-gray-400">
                &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
            </p>
        </div>
    </div>
</footer>
