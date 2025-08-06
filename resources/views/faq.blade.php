<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="text-center mb-20">
        <div
            class="inline-flex items-center px-4 py-2 rounded-full bg-orange-100 dark:bg-orange-900/30 text-orange-800 dark:text-orange-200 font-semibold text-sm mb-4">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            Support
        </div>
        <h2 class="text-4xl font-extrabold text-gray-900 dark:text-white sm:text-5xl mb-6">
            Frequently Asked <span class="text-transparent bg-clip-text bg-gradient-to-r from-orange-600 to-red-600">Questions</span>
        </h2>
        <p class="text-xl text-gray-600 dark:text-gray-300 leading-relaxed">
            Everything you need to know about All Academies platform. Can't find what you're looking for?
            <a href="{{route('branding.contact')}}" class="text-blue-600 dark:text-blue-400 hover:underline">Contact our support team</a>.
        </p>
    </div>

    <div class="space-y-6" x-data="{ openFaq: null }">
        <!-- FAQ Item 1 -->
        <div
            class="bg-gray-50 dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <button @click="openFaq = openFaq === 1 ? null : 1"
                    class="w-full px-8 py-6 text-left flex justify-between items-center hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none focus:bg-gray-100 dark:focus:bg-gray-700 transition-colors">
                <span class="text-lg font-semibold text-gray-900 dark:text-white">How do I access books after subscribing?</span>
                <svg :class="{ 'rotate-180': openFaq === 1 }"
                     class="w-6 h-6 text-gray-400 dark:text-gray-500 transform transition-transform duration-200"
                     fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>
            <div x-show="openFaq === 1" x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 transform scale-y-95"
                 x-transition:enter-end="opacity-100 transform scale-y-100" class="px-8 pb-6">
                <p class="text-gray-600 dark:text-gray-300 leading-relaxed">Once you subscribe, you'll have instant
                    access to our entire library through your personal dashboard. Simply log in to your account, browse
                    or search for books, and start reading immediately. You can access books on any device with an
                    internet connection, and Premium subscribers can download books for offline reading.</p>
            </div>
        </div>

        <!-- FAQ Item 2 -->
        <div
            class="bg-gray-50 dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <button @click="openFaq = openFaq === 2 ? null : 2"
                    class="w-full px-8 py-6 text-left flex justify-between items-center hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none focus:bg-gray-100 dark:focus:bg-gray-700 transition-colors">
                <span class="text-lg font-semibold text-gray-900 dark:text-white">What's the difference between the pricing tiers?</span>
                <svg :class="{ 'rotate-180': openFaq === 2 }"
                     class="w-6 h-6 text-gray-400 dark:text-gray-500 transform transition-transform duration-200"
                     fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>
            <div x-show="openFaq === 2" x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 transform scale-y-95"
                 x-transition:enter-end="opacity-100 transform scale-y-100" class="px-8 pb-6">
                <p class="text-gray-600 dark:text-gray-300 leading-relaxed">Our Basic plan (GHS 15/3 months) gives you
                    access to 5,000+ books with basic features. The Biannual plan (GHS 20/6 months) includes unlimited
                    access to our entire library, offline downloads, advanced search, and priority support. The Annual
                    plan (GHS 25/12 months) provides all the benefits of the biannual plan plus additional features and
                     offer custom solutions for institutions with unlimited users, advanced analytics, and
                    dedicated support.</p>
            </div>
        </div>

        <!-- FAQ Item 3 -->
        <div
            class="bg-gray-50 dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <button @click="openFaq = openFaq === 3 ? null : 3"
                    class="w-full px-8 py-6 text-left flex justify-between items-center hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none focus:bg-gray-100 dark:focus:bg-gray-700 transition-colors">
                <span class="text-lg font-semibold text-gray-900 dark:text-white">Can I download books for offline reading?</span>
                <svg :class="{ 'rotate-180': openFaq === 3 }"
                     class="w-6 h-6 text-gray-400 dark:text-gray-500 transform transition-transform duration-200"
                     fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>
            <div x-show="openFaq === 3" x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 transform scale-y-95"
                 x-transition:enter-end="opacity-100 transform scale-y-100" class="px-8 pb-6">
                <p class="text-gray-600 dark:text-gray-300 leading-relaxed">Yes! Premium and Enterprise subscribers can
                    download books in PDF and EPUB formats for offline reading. This is perfect for studying when you
                    don't have internet access. Downloaded books remain accessible for the duration of your subscription
                    and sync across all your devices.</p>
            </div>
        </div>

        <!-- FAQ Item 4 -->
        <div
            class="bg-gray-50 dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <button @click="openFaq = openFaq === 4 ? null : 4"
                    class="w-full px-8 py-6 text-left flex justify-between items-center hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none focus:bg-gray-100 dark:focus:bg-gray-700 transition-colors">
                <span
                    class="text-lg font-semibold text-gray-900 dark:text-white">What subjects and topics are covered?</span>
                <svg :class="{ 'rotate-180': openFaq === 4 }"
                     class="w-6 h-6 text-gray-400 dark:text-gray-500 transform transition-transform duration-200"
                     fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>
            <div x-show="openFaq === 4" x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 transform scale-y-95"
                 x-transition:enter-end="opacity-100 transform scale-y-100" class="px-8 pb-6">
                <p class="text-gray-600 dark:text-gray-300 leading-relaxed">Our library spans over 150 subject areas
                    including Science, Technology, Engineering, Mathematics, Business, Humanities, Arts, Social
                    Sciences, Medicine, Law, and many more. We continuously add new books across diverse academic and
                    professional fields to meet our users' evolving learning needs. You can browse by category or use
                    our AI-powered search to find exactly what you need.</p>
            </div>
        </div>

        <!-- FAQ Item 5 -->
        <div
            class="bg-gray-50 dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <button @click="openFaq = openFaq === 5 ? null : 5"
                    class="w-full px-8 py-6 text-left flex justify-between items-center hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none focus:bg-gray-100 dark:focus:bg-gray-700 transition-colors">
                <span
                    class="text-lg font-semibold text-gray-900 dark:text-white">How do I cancel my subscription?</span>
                <svg :class="{ 'rotate-180': openFaq === 5 }"
                     class="w-6 h-6 text-gray-400 dark:text-gray-500 transform transition-transform duration-200"
                     fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>
            <div x-show="openFaq === 5" x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 transform scale-y-95"
                 x-transition:enter-end="opacity-100 transform scale-y-100" class="px-8 pb-6">
                <p class="text-gray-600 dark:text-gray-300 leading-relaxed">You can cancel your subscription at any time
                    from your account settings under "Subscription Management." There are no cancellation fees, and
                    you'll continue to have full access to all books until the end of your current billing period. We
                    also offer a 30-day money-back guarantee for new subscribers if you're not completely satisfied.</p>
            </div>
        </div>

        <!-- FAQ Item 6 -->
        <div
            class="bg-gray-50 dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <button @click="openFaq = openFaq === 6 ? null : 6"
                    class="w-full px-8 py-6 text-left flex justify-between items-center hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none focus:bg-gray-100 dark:focus:bg-gray-700 transition-colors">
                <span class="text-lg font-semibold text-gray-900 dark:text-white">Is customer support available?</span>
                <svg :class="{ 'rotate-180': openFaq === 6 }"
                     class="w-6 h-6 text-gray-400 dark:text-gray-500 transform transition-transform duration-200"
                     fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>
            <div x-show="openFaq === 6" x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 transform scale-y-95"
                 x-transition:enter-end="opacity-100 transform scale-y-100" class="px-8 pb-6">
                <p class="text-gray-600 dark:text-gray-300 leading-relaxed">Absolutely! We provide comprehensive
                    customer support through multiple channels. Basic subscribers get email support, Premium subscribers
                    receive priority support via email and live chat, and Enterprise customers have access to 24/7 phone
                    support with a dedicated account manager. Our support team is available Monday through Friday from 9
                    AM to 6 PM GMT.</p>
            </div>
        </div>
    </div>
</div>
