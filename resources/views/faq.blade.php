<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
    <!-- Enhanced Header Section -->
    <div class="text-center mb-20">
        <div class="inline-flex items-center px-4 py-2 rounded-full bg-gradient-to-r from-orange-100 to-amber-100 dark:from-orange-900/30 dark:to-amber-900/30 text-orange-800 dark:text-orange-200 font-semibold text-sm mb-6 shadow-lg">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            Support Center
        </div>
        <h2 class="text-5xl font-extrabold text-gray-900 dark:text-white sm:text-6xl mb-6 leading-tight">
            Frequently Asked
            <span class="text-transparent bg-clip-text bg-gradient-to-r from-orange-600 via-red-600 to-pink-600 animate-pulse">
                Questions
            </span>
        </h2>
        <p class="text-xl text-gray-600 dark:text-gray-300 leading-relaxed max-w-3xl mx-auto">
            Everything you need to know about All Academies platform. Can't find what you're looking for?
            <a href="{{route('branding.contact')}}" class="text-blue-600 dark:text-blue-400 hover:underline font-semibold hover:text-blue-700 dark:hover:text-blue-300 transition-colors duration-200">Contact our support team</a>.
        </p>
    </div>

    <!-- FAQ Items with Enhanced Alpine.js -->
    <div x-data="{
        openFaq: null,
        searchQuery: '',
        selectedCategory: 'all',
        faqs: [
            { id: 1, category: 'general', question: 'How do I access books after subscribing?', answer: 'Once you subscribe, you will have instant access to our entire library through your personal dashboard. Simply log in to your account, browse or search for books, and start reading immediately. You can access books on any device with an internet connection, and Premium subscribers can download books for offline reading.' },
            { id: 2, category: 'subscription', question: 'What is the difference between the pricing tiers?', answer: 'Our Basic plan (GHS 15/3 months) gives you access to 5,000+ books with basic features. The Biannual plan (GHS 20/6 months) includes unlimited access to our entire library, offline downloads, advanced search, and priority support. The Annual plan (GHS 25/12 months) provides all the benefits of the biannual plan plus additional features and we offer custom solutions for institutions with unlimited users, advanced analytics, and dedicated support.' },
            { id: 3, category: 'technical', question: 'Can I download books for offline reading?', answer: 'Yes! Premium and Enterprise subscribers can download books in PDF and EPUB formats for offline reading. This is perfect for studying when you do not have internet access. Downloaded books remain accessible for the duration of your subscription and sync across all your devices.' },
            { id: 4, category: 'general', question: 'What subjects and topics are covered?', answer: 'Our library spans over 150 subject areas including Science, Technology, Engineering, Mathematics, Business, Humanities, Arts, Social Sciences, Medicine, Law, and many more. We continuously add new books across diverse academic and professional fields to meet our users evolving learning needs. You can browse by category or use our AI-powered search to find exactly what you need.' },
            { id: 5, category: 'subscription', question: 'How do I cancel my subscription?', answer: 'You can cancel your subscription at any time from your account settings under Subscription Management. There are no cancellation fees, and you will continue to have full access to all books until the end of your current billing period. We also offer a 30-day money-back guarantee for new subscribers if you are not completely satisfied.' },
            { id: 6, category: 'general', question: 'Is customer support available?', answer: 'Absolutely! We provide comprehensive customer support through multiple channels. Basic subscribers get email support, Premium subscribers receive priority support via email and live chat, and Enterprise customers have access to 24/7 phone support with a dedicated account manager. Our support team is available Monday through Friday from 9 AM to 6 PM GMT.' },
            { id: 7, category: 'technical', question: 'What devices and browsers are supported?', answer: 'All Academies works on all modern devices and browsers. You can access your account on desktop computers (Windows, Mac, Linux), tablets (iPad, Android), and smartphones (iOS, Android). We support Chrome, Firefox, Safari, Edge, and other modern browsers. Our mobile app is available for iOS and Android devices.' },
            { id: 8, category: 'subscription', question: 'Do you offer student or institutional discounts?', answer: 'Yes! We offer special pricing for students with valid student IDs (20% discount) and bulk institutional licenses with significant savings for schools, universities, and libraries. Contact our sales team for custom institutional pricing that can include unlimited users, advanced analytics, and dedicated support.' }
        ],
        filteredFaqs: [],
        init() {
            this.updateFilteredFaqs();
            this.$watch('searchQuery', () => this.updateFilteredFaqs());
            this.$watch('selectedCategory', () => this.updateFilteredFaqs());
        },
        updateFilteredFaqs() {
            let filtered = this.faqs;

            if (this.selectedCategory !== 'all') {
                filtered = filtered.filter(faq => faq.category === this.selectedCategory);
            }

            if (this.searchQuery.trim() !== '') {
                const query = this.searchQuery.toLowerCase();
                filtered = filtered.filter(faq =>
                    faq.question.toLowerCase().includes(query) ||
                    faq.answer.toLowerCase().includes(query)
                );
            }

            this.filteredFaqs = filtered;
        },
        clearFilters() {
            this.searchQuery = '';
            this.selectedCategory = 'all';
        }
    }">

        <!-- Search Bar -->
        <div class="mb-8 max-w-md mx-auto">
            <div class="relative">
                <input type="text"
                       x-model="searchQuery"
                       placeholder="Search FAQs..."
                       class="w-full px-4 py-3 pl-12 pr-4 text-gray-900 dark:text-white bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-transparent shadow-lg">
                <svg class="absolute left-4 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
            </div>
        </div>

        <!-- Category Filter -->
        <div class="mb-8 flex flex-wrap justify-center gap-3">
            <button @click="selectedCategory = 'all'"
                    :class="selectedCategory === 'all' ? 'bg-orange-500 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300'"
                    class="px-4 py-2 rounded-full text-sm font-medium transition-all duration-200 hover:scale-105">
                All
            </button>
            <button @click="selectedCategory = 'general'"
                    :class="selectedCategory === 'general' ? 'bg-orange-500 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300'"
                    class="px-4 py-2 rounded-full text-sm font-medium transition-all duration-200 hover:scale-105">
                General
            </button>
            <button @click="selectedCategory = 'subscription'"
                    :class="selectedCategory === 'subscription' ? 'bg-orange-500 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300'"
                    class="px-4 py-2 rounded-full text-sm font-medium transition-all duration-200 hover:scale-105">
                Subscription
            </button>
            <button @click="selectedCategory = 'technical'"
                    :class="selectedCategory === 'technical' ? 'bg-orange-500 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300'"
                    class="px-4 py-2 rounded-full text-sm font-medium transition-all duration-200 hover:scale-105">
                Technical
            </button>
        </div>

        <!-- Results counter -->
        <div x-show="searchQuery !== '' || selectedCategory !== 'all'" class="text-center py-4">
            <p class="text-gray-600 dark:text-gray-400">
                Showing <span x-text="filteredFaqs.length"></span> of <span x-text="faqs.length"></span> results
                <span x-show="searchQuery !== ''" class="ml-2">
                    for "<span x-text="searchQuery" class="font-semibold"></span>"
                </span>
            </p>
        </div>

        <!-- No results message -->
        <div x-show="filteredFaqs.length === 0" class="text-center py-12">
            <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 12h6m-6-4h6m2 5.291A7.962 7.962 0 0112 20.4a7.962 7.962 0 01-6-2.109M3 9.5A6.5 6.5 0 119.5 3 6.5 6.5 0 013 9.5z"></path>
            </svg>
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">No FAQs found</h3>
            <p class="text-gray-600 dark:text-gray-400 mb-4">Try adjusting your search or contact support for help.</p>
            <button @click="clearFilters()" class="px-4 py-2 bg-orange-500 text-white rounded-lg hover:bg-orange-600 transition-colors duration-200">
                Clear filters
            </button>
        </div>

        <!-- FAQ Items -->
        <div class="space-y-6">
            <template x-for="faq in filteredFaqs" :key="faq.id">
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg hover:shadow-xl border border-gray-200 dark:border-gray-700 overflow-hidden transform transition-all duration-300 hover:scale-[1.02]">
                    <button @click="openFaq = openFaq === faq.id ? null : faq.id"
                            class="w-full px-8 py-6 text-left flex justify-between items-center hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:bg-gray-50 dark:focus:bg-gray-700 transition-colors">
                        <div class="flex items-start">
                            <!-- Category Badge -->
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium mr-4 mt-1"
                                  :class="{
                                      'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300': faq.category === 'general',
                                      'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300': faq.category === 'subscription',
                                      'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-300': faq.category === 'technical'
                                  }"
                                  x-text="faq.category.charAt(0).toUpperCase() + faq.category.slice(1)">
                            </span>
                            <span class="text-lg font-semibold text-gray-900 dark:text-white" x-text="faq.question"></span>
                        </div>
                        <svg :class="{ 'rotate-180': openFaq === faq.id }"
                             class="w-6 h-6 text-gray-400 dark:text-gray-500 transform transition-transform duration-200 flex-shrink-0 ml-4"
                             fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    <div x-show="openFaq === faq.id"
                         x-transition:enter="transition ease-out duration-300"
                         x-transition:enter-start="opacity-0 transform scale-y-95"
                         x-transition:enter-end="opacity-100 transform scale-y-100"
                         x-transition:leave="transition ease-in duration-200"
                         x-transition:leave-start="opacity-100 transform scale-y-100"
                         x-transition:leave-end="opacity-0 transform scale-y-95"
                         class="px-8 pb-6">
                        <div class="border-l-4 border-orange-500 pl-4">
                            <p class="text-gray-600 dark:text-gray-300 leading-relaxed" x-text="faq.answer"></p>
                        </div>

                        <!-- Helpful feedback -->
                        <div class="mt-6 pt-4 border-t border-gray-200 dark:border-gray-600 flex items-center justify-between">
                            <p class="text-sm text-gray-600 dark:text-gray-400">Was this helpful?</p>
                            <div class="flex space-x-2">
                                <button class="flex items-center px-3 py-1 text-sm text-green-600 dark:text-green-400 hover:bg-green-50 dark:hover:bg-green-900/20 rounded-lg transition-colors duration-200">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 10h4.764a2 2 0 011.789 2.894l-3.5 7A2 2 0 0115.263 21h-4.017c-.163 0-.326-.02-.485-.06L7 20m7-10V5a2 2 0 00-2-2h-.095c-.5 0-.905.405-.905.905 0 .714-.211 1.412-.608 2.006L7 11v9m7-10h-2M7 20H2v-9a2 2 0 012-2h1m2 0h6.5c.542 0 1.041.238 1.37.62l2.764 3.102a2 2 0 010 2.556L14.37 18.38c-.329.382-.828.62-1.37.62H7"></path>
                                    </svg>
                                    Yes
                                </button>
                                <button class="flex items-center px-3 py-1 text-sm text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors duration-200">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14H5.236a2 2 0 01-1.789-2.894l3.5-7A2 2 0 018.736 3h4.018c.163 0 .326.02.485.06L17 4m-7 10v2a2 2 0 002 2h.095c.5 0 .905-.405.905-.905 0-.714.211-1.412.608-2.006L17 13V4m-7 10h2m5-10h4.764a2 2 0 011.789 2.894l-3.5 7A2 2 0 0115.263 21h-4.017c-.163 0-.326-.02-.485-.06L7 20"></path>
                                    </svg>
                                    No
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </template>
        </div>

        <!-- Contact Support Section -->
        <div class="mt-16 bg-gradient-to-r from-orange-50 to-amber-50 dark:from-orange-900/20 dark:to-amber-900/20 rounded-2xl p-8 text-center border border-orange-200 dark:border-orange-800">
            <svg class="mx-auto h-12 w-12 text-orange-500 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192L5.636 18.364M12 2.25a9.75 9.75 0 11-7.5 13.5A9.75 9.75 0 0112 2.25z"></path>
            </svg>
            <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">Still have questions?</h3>
            <p class="text-gray-600 dark:text-gray-300 mb-6">Our support team is here to help you get the most out of All Academies.</p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{route('branding.contact')}}" class="inline-flex items-center px-6 py-3 bg-orange-500 text-white font-semibold rounded-lg hover:bg-orange-600 transition-colors duration-200 shadow-lg hover:shadow-xl">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                    </svg>
                    Contact Support
                </a>
                <a href="#" class="inline-flex items-center px-6 py-3 border-2 border-orange-500 text-orange-500 font-semibold rounded-lg hover:bg-orange-500 hover:text-white transition-colors duration-200">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                    </svg>
                    Live Chat
                </a>
            </div>
        </div>
    </div>
</div>
