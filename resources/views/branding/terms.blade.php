<x-layouts.guest pageName="Terms of Service">
    @section('title', 'Terms of Service - All Academies')

    <div x-data="termsPage()" x-init="init()" @scroll.window.throttle.100ms="onScroll()" class="bg-gray-50 dark:bg-gray-900">
        <!-- Page Title Section -->
        <div class="bg-white dark:bg-gray-800/50 border-b border-gray-200 dark:border-gray-700/50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Terms of Service</h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Last updated: {{ now()->format('F d, Y') }}</p>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 lg:py-12">
            <div class="lg:grid lg:grid-cols-12 lg:gap-12">
                <!-- Sidebar Navigation -->
                <aside class="hidden lg:block lg:col-span-3 xl:col-span-3">
                    <nav class="sticky top-24">
                        <h3 class="text-xs font-semibold text-gray-900 dark:text-white uppercase tracking-wider mb-4">On this page</h3>
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
                        <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 border border-gray-200 dark:border-gray-700 shadow-sm">
                            <p class="text-gray-700 dark:text-gray-300 leading-relaxed">
                                Welcome to All Academies. These Terms of Service ("Terms") govern your access to and use of our platform, including our website, mobile applications, and related services (collectively, the "Services"). By accessing or using our Services, you agree to be bound by these Terms and our Privacy Policy.
                            </p>
                        </div>

                        <!-- Section 1 -->
                        <section id="acceptance" class="scroll-mt-24">
                            <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">1. Acceptance of Terms</h2>
                            <p>By creating an account or using the Services, you affirm that you are at least 13 years of age (or have obtained parental/guardian consent if required by your local laws) and agree to comply with these Terms. If you are using the Services on behalf of an organization, you represent that you have the authority to bind that organization to these Terms.</p>
                        </section>

                        <!-- Section 2 -->
                        <section id="services" class="scroll-mt-24">
                            <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">2. Description of Services</h2>
                            <p>All Academies provides a comprehensive educational platform that includes, but is not limited to:</p>
                            <ul class="list-disc pl-6 space-y-2">
                                <li>Access to a digital library of books, quizzes, and learning materials.</li>
                                <li>A marketplace for authors to publish and monetize educational content.</li>
                                <li>Management tools for students, teachers, and academic administrators.</li>
                            </ul>
                            <p>We reserve the right to modify, suspend, or discontinue any part of our Services at any time, with or without notice.</p>
                        </section>

                        <!-- Section 3 -->
                        <section id="accounts" class="scroll-mt-24">
                            <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">3. User Accounts</h2>
                            <p>To access most features, you must register for an account. You agree to:</p>
                            <ul class="list-disc pl-6 space-y-2">
                                <li>Provide accurate, current, and complete information during registration.</li>
                                <li>Maintain the security of your password and accept all risks of unauthorized access to your account.</li>
                                <li>Promptly notify us if you discover or suspect any security breaches related to the Services.</li>
                            </ul>
                        </section>

                        <!-- Section 4 -->
                        <section id="content" class="scroll-mt-24">
                            <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">4. User Content and Responsibilities</h2>
                            <p>You are responsible for any content you post, upload, or share on the platform ("User Content"). By providing User Content, you grant All Academies a worldwide, non-exclusive, royalty-free license to use, host, store, reproduce, modify, and distribute it for the purpose of operating and improving our Services.</p>
                            <p>You represent and warrant that:</p>
                            <ul class="list-disc pl-6 space-y-2">
                                <li>You own your User Content or have the necessary rights to grant us this license.</li>
                                <li>Your User Content does not violate any third-party rights, including copyright, trademark, or privacy rights.</li>
                                <li>Your User Content complies with our Prohibited Activities policy.</li>
                            </ul>
                        </section>

                        <!-- Section 5 -->
                        <section id="prohibited" class="scroll-mt-24">
                            <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">5. Prohibited Activities</h2>
                            <p>You agree not to engage in any of the following prohibited activities:</p>
                            <ul class="list-disc pl-6 space-y-2">
                                <li>Using the service for any illegal purpose or in violation of any local, state, national, or international law.</li>
                                <li>Uploading or distributing any viruses, worms, or other malicious software.</li>
                                <li>Attempting to interfere with, compromise the system integrity or security, or decipher any transmissions to or from the servers running the Service.</li>
                                <li>Impersonating another person or otherwise misrepresenting your affiliation with a person or entity.</li>
                                <li>Collecting or harvesting any personally identifiable information, including account names, from the Service.</li>
                            </ul>
                        </section>

                        <!-- Section 6 -->
                        <section id="intellectual" class="scroll-mt-24">
                            <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">6. Intellectual Property</h2>
                            <p>Excluding User Content, all materials on the platform, including software, text, graphics, logos, and trademarks, are the exclusive property of All Academies and its licensors. You may not use, copy, or distribute any of this content without our prior written permission.</p>
                        </section>

                        <!-- Section 7 -->
                        <section id="payments" class="scroll-mt-24">
                            <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">7. Payments, Subscriptions, and Refunds</h2>
                            <p>Certain features of the Service may require payment. By making a purchase, you agree to our pricing and payment terms. All payments are handled by secure third-party processors. We do not store your credit card information. Subscription fees are billed in advance on a recurring basis. Refund policies, if any, will be specified at the time of purchase.</p>
                        </section>

                        <!-- Section 8 -->
                        <section id="liability" class="scroll-mt-24">
                            <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">8. Limitation of Liability</h2>
                            <div class="bg-red-50 dark:bg-red-900/20 border-l-4 border-red-500 p-4 rounded-r-lg not-prose">
                                <p class="text-red-800 dark:text-red-200 text-base">To the fullest extent permitted by law, All Academies shall not be liable for any indirect, incidental, special, consequential, or punitive damages, or any loss of profits or revenues, whether incurred directly or indirectly, or any loss of data, use, goodwill, or other intangible losses, resulting from your use of the Service.</p>
                            </div>
                        </section>

                        <!-- Section 9 -->
                        <section id="termination" class="scroll-mt-24">
                            <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">9. Termination</h2>
                            <p>We may terminate or suspend your account and bar access to the Service immediately, without prior notice or liability, for any reason whatsoever, including a breach of these Terms. You may terminate your account at any time by contacting our support team.</p>
                        </section>

                        <!-- Section 10 -->
                        <section id="changes" class="scroll-mt-24">
                            <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">10. Changes to Terms</h2>
                            <p>We reserve the right to modify these Terms at any time. We will provide notice of significant changes by posting the new Terms on our site or by sending you an email. Your continued use of the Service after such changes constitutes your acceptance of the new Terms.</p>
                        </section>

                        <!-- Section 11 -->
                        <section id="governing" class="scroll-mt-24">
                            <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">11. Governing Law</h2>
                            <p>These Terms shall be governed and construed in accordance with the laws of Ghana, without regard to its conflict of law provisions.</p>
                        </section>

                        <!-- Section 12 -->
                        <section id="contact" class="scroll-mt-24">
                            <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">12. Contact Us</h2>
                            <p>If you have any questions about these Terms, please contact us at:</p>
                            <div class="mt-4">
                                <a href="mailto:allacademies2023@gmail.com" class="inline-flex items-center px-6 py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition-colors no-underline">
                                    <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"/>
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

        document.addEventListener('alpine:init', () => {
            Alpine.store('theme', {
                isDark: localStorage.getItem('theme') === 'dark',
                toggle() {
                    this.isDark = !this.isDark;
                    localStorage.setItem('theme', this.isDark ? 'dark' : 'light');
                    if (this.isDark) {
                        document.documentElement.classList.add('dark');
                    } else {
                        document.documentElement.classList.remove('dark');
                    }
                }
            });
            // Apply initial theme
            if (Alpine.store('theme').isDark) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        });
    </script>
</x-layouts.guest>
