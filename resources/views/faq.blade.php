<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
    <!-- Header Section -->
    <div class="text-center mb-12 sm:mb-16">
        <div class="inline-flex items-center px-3 py-1.5 sm:px-4 sm:py-2 rounded-full bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-200 font-semibold text-xs sm:text-sm mb-3 sm:mb-4" data-aos="fade-down">
            <svg class="w-3 h-3 sm:w-4 sm:h-4 mr-1.5 sm:mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            FAQ
        </div>
        <h2 class="text-2xl sm:text-3xl md:text-4xl lg:text-5xl font-bold sm:font-black text-gray-900 dark:text-white mb-3 sm:mb-4" data-aos="fade-up" data-aos-delay="100">
            Frequently Asked <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-purple-600">Questions</span>
        </h2>
        <p class="text-base sm:text-lg md:text-xl text-gray-600 dark:text-gray-300 max-w-3xl mx-auto" data-aos="fade-up" data-aos-delay="200">
            Everything you need to know about All Academies. Can't find what you're looking for?
            <a href="{{route('branding.contact')}}" class="text-blue-600 dark:text-blue-400 hover:underline font-semibold">Contact us</a>.
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
        <div class="mb-8 sm:mb-12 max-w-2xl mx-auto" data-aos="fade-up" data-aos-delay="300">
            <div class="relative">
                <input type="text"
                       x-model="searchQuery"
                       placeholder="Search questions..."
                       class="w-full px-4 py-3 sm:px-6 sm:py-4 pl-12 sm:pl-14 text-sm sm:text-base text-gray-900 dark:text-white bg-white dark:bg-gray-800 border-2 border-gray-200 dark:border-gray-700 rounded-xl sm:rounded-2xl focus:ring-2 focus:ring-blue-500 focus:border-transparent shadow-lg transition-all">
                <svg class="absolute left-4 sm:left-5 top-1/2 transform -translate-y-1/2 w-5 h-5 sm:w-6 sm:h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </div>
        </div>

        <!-- Category Filter -->
        <div class="mb-8 sm:mb-12 flex flex-wrap justify-center gap-2 sm:gap-3" data-aos="fade-up" data-aos-delay="400">
            <button @click="selectedCategory = 'all'"
                    :class="selectedCategory === 'all' ? 'bg-blue-600 text-white shadow-lg' : 'bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300'"
                    class="px-4 py-2 sm:px-6 sm:py-3 rounded-lg sm:rounded-xl text-xs sm:text-sm font-semibold transition-all duration-200 hover:scale-105">
                All Questions
            </button>
            <button @click="selectedCategory = 'general'"
                    :class="selectedCategory === 'general' ? 'bg-blue-600 text-white shadow-lg' : 'bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300'"
                    class="px-4 py-2 sm:px-6 sm:py-3 rounded-lg sm:rounded-xl text-xs sm:text-sm font-semibold transition-all duration-200 hover:scale-105">
                General
            </button>
            <button @click="selectedCategory = 'subscription'"
                    :class="selectedCategory === 'subscription' ? 'bg-blue-600 text-white shadow-lg' : 'bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300'"
                    class="px-4 py-2 sm:px-6 sm:py-3 rounded-lg sm:rounded-xl text-xs sm:text-sm font-semibold transition-all duration-200 hover:scale-105">
                Subscription
            </button>
            <button @click="selectedCategory = 'technical'"
                    :class="selectedCategory === 'technical' ? 'bg-blue-600 text-white shadow-lg' : 'bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300'"
                    class="px-4 py-2 sm:px-6 sm:py-3 rounded-lg sm:rounded-xl text-xs sm:text-sm font-semibold transition-all duration-200 hover:scale-105">
                Technical
            </button>
        </div>

        <!-- No results message -->
        <div x-show="filteredFaqs.length === 0" class="text-center py-12 sm:py-16">
            <div class="w-16 h-16 sm:w-20 sm:h-20 bg-gray-100 dark:bg-gray-800 rounded-full flex items-center justify-center mx-auto mb-3 sm:mb-4">
                <svg class="w-8 h-8 sm:w-10 sm:h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 12h6m-6-4h6m2 5.291A7.962 7.962 0 0112 20.4a7.962 7.962 0 01-6-2.109M3 9.5A6.5 6.5 0 119.5 3 6.5 6.5 0 013 9.5z"/>
                </svg>
            </div>
            <h3 class="text-lg sm:text-xl font-bold text-gray-900 dark:text-white mb-2">No questions found</h3>
            <p class="text-sm sm:text-base text-gray-600 dark:text-gray-400 mb-4 sm:mb-6">Try adjusting your search or filters</p>
            <button @click="clearFilters()" class="px-5 py-2.5 sm:px-6 sm:py-3 bg-blue-600 text-white text-sm sm:text-base font-semibold rounded-lg sm:rounded-xl hover:bg-blue-700 transition-colors">
                Clear Filters
            </button>
        </div>

        <!-- FAQ Items -->
        <div class="space-y-2 sm:space-y-3">
            <template x-for="faq in filteredFaqs" :key="faq.id">
                <div class="bg-white dark:bg-gray-800 rounded-lg sm:rounded-xl border-2 border-gray-200 dark:border-gray-700 overflow-hidden transition-all duration-300 hover:border-blue-500 dark:hover:border-blue-400" data-aos="fade-up" data-aos-delay="100">
                    <button @click="openFaq = openFaq === faq.id ? null : faq.id"
                            class="w-full px-4 py-4 sm:px-6 sm:py-5 text-left flex justify-between items-center hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                        <span class="text-sm sm:text-base lg:text-lg font-bold text-gray-900 dark:text-white pr-4 sm:pr-8" x-text="faq.question"></span>
                        <div class="flex-shrink-0 w-8 h-8 sm:w-10 sm:h-10 rounded-full flex items-center justify-center transition-all duration-300"
                             :class="openFaq === faq.id ? 'bg-blue-100 dark:bg-blue-900/30' : 'bg-gray-100 dark:bg-gray-700'">
                            <svg :class="{ 'rotate-180': openFaq === faq.id }"
                                 class="w-4 h-4 sm:w-5 sm:h-5 transform transition-transform duration-300"
                                 :class="openFaq === faq.id ? 'text-blue-600 dark:text-blue-400' : 'text-gray-500 dark:text-gray-400'"
                                 fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </div>
                    </button>
                    <div x-show="openFaq === faq.id"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 -translate-y-2"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         x-transition:leave="transition ease-in duration-150"
                         x-transition:leave-start="opacity-100 translate-y-0"
                         x-transition:leave-end="opacity-0 -translate-y-2"
                         class="px-4 pb-4 sm:px-6 sm:pb-6 bg-gray-50 dark:bg-gray-900/50">
                        <div class="pt-3 sm:pt-4 pl-3 sm:pl-4 border-l-4 border-blue-500">
                            <p class="text-sm sm:text-base text-gray-700 dark:text-gray-300 leading-relaxed" x-text="faq.answer"></p>
                        </div>
                    </div>
                </div>
            </template>
        </div>

        <!-- Contact Support Section -->
        <div class="mt-12 sm:mt-16 lg:mt-20 bg-gradient-to-r from-blue-50 to-purple-50 dark:from-gray-800 dark:to-gray-900 rounded-2xl sm:rounded-3xl p-8 sm:p-12 text-center border border-gray-200 dark:border-gray-700" data-aos="zoom-in" data-aos-delay="200">
            <div class="w-12 h-12 sm:w-16 sm:h-16 bg-blue-600 rounded-xl sm:rounded-2xl flex items-center justify-center mx-auto mb-4 sm:mb-6">
                <svg class="w-6 h-6 sm:w-8 sm:h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                </svg>
            </div>
            <h3 class="text-xl sm:text-2xl lg:text-3xl font-bold sm:font-black text-gray-900 dark:text-white mb-2 sm:mb-3">Still have questions?</h3>
            <p class="text-sm sm:text-base lg:text-lg text-gray-600 dark:text-gray-300 mb-6 sm:mb-8 max-w-2xl mx-auto">Can't find the answer you're looking for? Our support team is ready to help.</p>
            <a href="{{route('branding.contact')}}" class="inline-flex items-center px-6 py-3 sm:px-8 sm:py-4 bg-blue-600 text-white font-bold text-base sm:text-lg rounded-xl sm:rounded-2xl hover:bg-blue-700 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-105">
                <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
                Contact Support
            </a>
        </div>
    </div>
</div>
