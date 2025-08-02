<x-layouts.guest pageName="Privacy Policy">

    <div x-data="termsPage()" x-init="init()" @scroll.window.throttle.100ms="onScroll()"
         class="bg-gray-50 dark:bg-gray-900">
        <!-- Page Title Section -->
        <div class="bg-white dark:bg-gray-800/50 border-b border-gray-200 dark:border-gray-700/50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Privacy Policy</h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Last updated: {{ now()->format('F d, Y') }}</p>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 lg:py-12">
            <div class="lg:grid lg:grid-cols-12 lg:gap-12">
                <!-- Sidebar Navigation -->
                <aside class="hidden lg:block lg:col-span-3 xl:col-span-3">
                    <nav class="sticky top-24">
                        <h3 class="text-xs font-semibold text-gray-900 dark:text-white uppercase tracking-wider mb-4">On
                            this page</h3>
                        <ul class="space-y-2">
                            <template x-for="section in sections" :key="section.id">
                                <li>
                                    <a :href="'#' + section.id"
                                       @click.prevent="scrollTo(section.id)"
                                       class="flex items-center text-sm font-medium transition-colors duration-200 group"
                                       :class="{
                                           'text-blue-600 dark:text-blue-400': activeSection === section.id,
                                           'text-gray-500 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white': activeSection !== section.id
                                       }">
                                        <span class="w-0.5 h-5 rounded-full mr-3 transition-colors duration-200"
                                              :class="{ 'bg-blue-600 dark:bg-blue-400': activeSection === section.id, 'bg-gray-200 dark:bg-gray-700 group-hover:bg-gray-400': activeSection !== section.id }"></span>
                                        <span x-text="section.title"></span>
                                    </a>
                                </li>
                            </template>
                        </ul>
                    </nav>
                </aside>

                <!-- Main Content -->
                <main class="lg:col-span-9 xl:col-span-9 prose prose-lg dark:prose-invert max-w-none">
                    <div class="space-y-16">
                        <!-- Introduction -->
                        <div
                            class="bg-white dark:bg-gray-800 rounded-2xl p-6 border border-gray-200 dark:border-gray-700 shadow-sm">
                            <p class="text-gray-700 dark:text-gray-300 leading-relaxed">
                                All Academies (“we,” “our,” or “us”) values your privacy. This Privacy Policy explains
                                how we collect, use, store, protect, and share your personal information when you access
                                or use the All Academies platform (website, mobile application, and related services).
                                By using our services, you agree to the terms of this Privacy Policy. If you do not
                                agree, please discontinue using our services.
                            </p>
                        </div>

                        <!-- Section 1 -->
                        <section id="information-collect" class="scroll-mt-24">
                            <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">1. Information We
                                Collect</h2>
                            <p>We collect the following types of information to provide and improve our services:</p>
                            <p>a. Personal Information:</p>
                            <ul class="list-disc pl-6 space-y-2">
                                <li>Name</li>
                                <li>Email address</li>
                                <li>Phone number</li>
                                <li>Profile information (e.g., educational background, role as teacher/student/author)
                                </li>
                            </ul>
                            <p>b. Non-Personal Information:</p>
                            <ul class="list-disc pl-6 space-y-2">
                                <li>Device information (browser type, operating system)</li>
                                <li>IP address and approximate location</li>
                                <li>Usage data (pages visited, features used, date and time of access)</li>
                            </ul>
                            <p>c. Uploaded Content:</p>
                            <p>If you upload books, quizzes, lessons, or other materials, we may store the content and
                                associated metadata (file name, upload date).</p>
                        </section>

                        <!-- Section 2 -->
                        <section id="use-of-information" class="scroll-mt-24">
                            <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">2. How We Use Your
                                Information</h2>
                            <p>We use the collected information to:</p>
                            <ol class="list-decimal pl-6 space-y-2">
                                <li>Create and manage your user account</li>
                                <li>Provide access to educational materials, quizzes, and book marketplaces</li>
                                <li>Personalize your user experience and recommendations</li>
                                <li>Improve and secure the All Academies platform</li>
                                <li>Communicate with you regarding updates, notifications, or support requests</li>
                                <li>Comply with legal obligations</li>
                            </ol>
                        </section>

                        <!-- Section 3 -->
                        <section id="sharing-of-information" class="scroll-mt-24">
                            <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">3. Sharing of
                                Information</h2>
                            <p>We do not sell your personal information. We may share information only in these
                                cases:</p>
                            <ul class="list-disc pl-6 space-y-2">
                                <li>With service providers: To operate hosting, payment, or analytics services under
                                    strict confidentiality
                                </li>
                                <li>For legal reasons: To comply with applicable laws, regulations, or lawful government
                                    requests
                                </li>
                                <li>For protection: To prevent fraud, security threats, or misuse of the platform</li>
                            </ul>
                        </section>

                        <!-- Section 4 -->
                        <section id="data-security" class="scroll-mt-24">
                            <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">4. Data Security and
                                Retention</h2>
                            <ul class="list-disc pl-6 space-y-2">
                                <li>We use encryption and secure servers to protect your information.</li>
                                <li>Your personal data is retained as long as your account is active or as required for
                                    legal purposes.
                                </li>
                                <li>You are responsible for keeping your login credentials confidential.</li>
                            </ul>
                        </section>

                        <!-- Section 5 -->
                        <section id="your-rights" class="scroll-mt-24">
                            <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">5. Your Rights</h2>
                            <p>You have the right to:</p>
                            <ol class="list-decimal pl-6 space-y-2">
                                <li>Access and update your personal data</li>
                                <li>Request deletion of your account and data</li>
                                <li>Opt-out of non-essential communications</li>
                                <li>Request clarification about how your data is used</li>
                            </ol>
                            <p>To exercise these rights, contact us at allacademies2023@gmail.com.</p>
                        </section>

                        <!-- Section 6 -->
                        <section id="cookies" class="scroll-mt-24">
                            <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">6. Cookies and Data
                                Storage Location</h2>
                            <ul class="list-disc pl-6 space-y-2">
                                <li>All Academies may use cookies and similar technologies to enhance your user
                                    experience, track usage patterns, and improve service quality.
                                </li>
                                <li>You can manage or disable cookies in your browser settings.</li>
                                <li>Your data may be stored on servers located in the United States, and we ensure
                                    compliance with the Ghana Data Protection Act and, where applicable, GDPR.
                                </li>
                            </ul>
                        </section>

                        <!-- Section 7 -->
                        <section id="third-party-links" class="scroll-mt-24">
                            <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">7. Third-Party Links and
                                Services</h2>
                            <p>All Academies may contain links to external websites or resources. We are not responsible
                                for the privacy practices of third parties. Please review their privacy policies before
                                providing information.</p>
                        </section>

                        <!-- Section 8 -->
                        <section id="updates" class="scroll-mt-24">
                            <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">8. Updates to this Privacy
                                Policy</h2>
                            <p>We may update this policy from time to time to reflect changes in law or our services.
                                Any major changes will be communicated via email or platform notification.</p>
                        </section>

                        <!-- Section 9 -->
                        <section id="contact" class="scroll-mt-24">
                            <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">9. Contact Us</h2>
                            <p>If you have questions or concerns about this Privacy Policy or your data, please contact
                                us at:</p>
                            <div class="mt-4">
                                <a href="mailto:allacademies2023@gmail.com"
                                   class="inline-flex items-center px-6 py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition-colors no-underline">
                                    <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                        <path
                                            d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"/>
                                        <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"/>
                                    </svg>
                                    allacademies2023@gmail.com
                                </a>
                            </div>
                        </section>
                    </div>
                </main>
            </div>
        </div>
    </div>

    <script>
        function termsPage() {
            return {
                activeSection: '',
                sections: [],
                init() {
                    this.sections = Array.from(document.querySelectorAll('main section[id]')).map(section => ({
                        id: section.id,
                        title: section.querySelector('h2').innerText.replace(/^\d+\.\s*/, '')
                    }));
                    this.setActiveSection();
                },
                onScroll() {
                    this.setActiveSection();
                },
                setActiveSection() {
                    let current = '';
                    const scrollY = window.scrollY;

                    this.sections.forEach(section => {
                        const element = document.getElementById(section.id);
                        if (element) {
                            const sectionTop = element.offsetTop - 120;
                            if (scrollY >= sectionTop) {
                                current = section.id;
                            }
                        }
                    });

                    this.activeSection = current || (this.sections.length > 0 ? this.sections[0].id : '');
                },
                scrollTo(id) {
                    const element = document.getElementById(id);
                    if (element) {
                        window.scrollTo({
                            top: element.offsetTop - 100,
                            behavior: 'smooth'
                        });
                    }
                }
            };
        }
    </script>
</x-layouts.guest>
