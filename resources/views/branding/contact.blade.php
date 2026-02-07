<x-layouts.guest pageName="Contact Us">
    <div class="relative min-h-screen">
        <!-- Background decoration -->
        <div class="absolute inset-0 bg-gradient-to-br from-indigo-50 via-white to-purple-50 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900"></div>
        <div class="absolute inset-0 overflow-hidden">
            <div class="absolute -top-40 -right-40 w-96 h-96 bg-purple-300 dark:bg-purple-900/20 rounded-full mix-blend-multiply filter blur-3xl opacity-70 dark:opacity-20 animate-blob"></div>
            <div class="absolute -bottom-40 -left-40 w-96 h-96 bg-indigo-300 dark:bg-indigo-900/20 rounded-full mix-blend-multiply filter blur-3xl opacity-70 dark:opacity-20 animate-blob animation-delay-2000"></div>
            <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-96 h-96 bg-pink-300 dark:bg-pink-900/20 rounded-full mix-blend-multiply filter blur-3xl opacity-70 dark:opacity-20 animate-blob animation-delay-4000"></div>
        </div>

    <!-- Content -->
    <div class="relative pt-16 pb-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="text-center mb-16">
                <h1 class="text-4xl font-bold tracking-tight text-gray-900 dark:text-white sm:text-5xl md:text-6xl mb-6">
                    Get in <span class="text-transparent bg-clip-text bg-gradient-to-r from-indigo-600 to-purple-600">Touch</span>
                </h1>
                <p class="text-lg sm:text-xl text-gray-600 dark:text-gray-300 max-w-3xl mx-auto leading-relaxed">
                    Have questions about our platform? We'd love to hear from you. Send us a message and we'll respond as soon as possible.
                </p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">
                <!-- Contact Info -->
                <div class="lg:col-span-1 space-y-6">
                    <div class="bg-white dark:bg-gray-800 backdrop-blur-sm rounded-3xl shadow-xl border border-gray-200 dark:border-gray-700 p-8 hover:shadow-2xl transition-shadow duration-300">
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-8">Contact Information</h2>

                        <div class="space-y-6">
                            <!-- Email -->
                            <div class="group flex items-start space-x-4 p-4 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors duration-200">
                                <div class="flex-shrink-0">
                                    <div class="w-12 h-12 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-200">
                                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                        </svg>
                                    </div>
                                </div>
                                <div>
                                    <h3 class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-1">Email</h3>
                                    <p class="text-gray-900 dark:text-white font-medium">{{config('company.email')}}</p>
                                </div>
                            </div>

                            <!-- Phone -->
                            <div class="group flex items-start space-x-4 p-4 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors duration-200">
                                <div class="flex-shrink-0">
                                    <div class="w-12 h-12 bg-gradient-to-br from-green-500 to-emerald-600 rounded-xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-200">
                                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                        </svg>
                                    </div>
                                </div>
                                <div>
                                    <h3 class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-1">Phone</h3>
                                    <p class="text-gray-900 dark:text-white font-medium">{{config('company.phone')}}</p>
                                </div>
                            </div>

                            <!-- Office -->
                            <div class="group flex items-start space-x-4 p-4 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors duration-200">
                                <div class="flex-shrink-0">
                                    <div class="w-12 h-12 bg-gradient-to-br from-orange-500 to-red-600 rounded-xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-200">
                                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        </svg>
                                    </div>
                                </div>
                                <div>
                                    <h3 class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-1">Office</h3>
                                    <p class="text-gray-900 dark:text-white font-medium leading-relaxed">{{config('company.address')}}</p>
                                </div>
                            </div>

                            <!-- Hours -->
                            <div class="group flex items-start space-x-4 p-4 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors duration-200">
                                <div class="flex-shrink-0">
                                    <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-pink-600 rounded-xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-200">
                                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    </div>
                                </div>
                                <div>
                                    <h3 class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-1">Business Hours</h3>
                                    <p class="text-gray-900 dark:text-white font-medium">Monday - Friday<br>9:00 AM - 6:00 PM GMT</p>
                                </div>
                            </div>
                        </div>

                        <!-- Social Links -->
                        <div class="mt-8 pt-8 border-t border-gray-200 dark:border-gray-700">
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Follow Us</h3>
                            <div class="flex space-x-3">
                                <a href="#" class="group w-11 h-11 bg-gradient-to-br from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 rounded-xl flex items-center justify-center text-white transition-all duration-200 shadow-lg hover:shadow-xl hover:scale-110">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M20 10C20 4.477 15.523 0 10 0S0 4.477 0 10c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V10h2.54V7.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V10h2.773l-.443 2.89h-2.33v6.988C16.343 19.128 20 14.991 20 10z" clip-rule="evenodd"></path>
                                    </svg>
                                </a>
                                <a href="#" class="group w-11 h-11 bg-gradient-to-br from-sky-500 to-sky-600 hover:from-sky-600 hover:to-sky-700 rounded-xl flex items-center justify-center text-white transition-all duration-200 shadow-lg hover:shadow-xl hover:scale-110">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M6.29 18.251c7.547 0 11.675-6.253 11.675-11.675 0-.178 0-.355-.012-.53A8.348 8.348 0 0020 3.92a8.19 8.19 0 01-2.357.646 4.118 4.118 0 001.804-2.27 8.224 8.224 0 01-2.605.996 4.107 4.107 0 00-6.993 3.743 11.65 11.65 0 01-8.457-4.287 4.106 4.106 0 001.27 5.477A4.073 4.073 0 01.8 7.713v.052a4.105 4.105 0 003.292 4.022 4.095 4.095 0 01-1.853.07 4.108 4.108 0 003.834 2.85A8.233 8.233 0 010 16.407a11.616 11.616 0 006.29 1.84"></path>
                                    </svg>
                                </a>
                                <a href="#" class="group w-11 h-11 bg-gradient-to-br from-blue-700 to-blue-800 hover:from-blue-800 hover:to-blue-900 rounded-xl flex items-center justify-center text-white transition-all duration-200 shadow-lg hover:shadow-xl hover:scale-110">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.338 16.338H13.67V12.16c0-.995-.017-2.277-1.387-2.277-1.39 0-1.601 1.086-1.601 2.207v4.248H8.014v-8.59h2.559v1.174h.037c.356-.675 1.227-1.387 2.526-1.387 2.703 0 3.203 1.778 3.203 4.092v4.711zM5.005 6.575a1.548 1.548 0 11-.003-3.096 1.548 1.548 0 01.003 3.096zm-1.337 9.763H6.34v-8.59H3.667v8.59zM17.668 1H2.328C1.595 1 1 1.581 1 2.298v15.403C1 18.418 1.595 19 2.328 19h15.34c.734 0 1.332-.582 1.332-1.299V2.298C19 1.581 18.402 1 17.668 1z" clip-rule="evenodd"></path>
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Contact Form -->
                <div class="lg:col-span-2">
                    <div class="bg-white dark:bg-gray-800 backdrop-blur-sm rounded-3xl shadow-xl border border-gray-200 dark:border-gray-700 p-8 sm:p-10 hover:shadow-2xl transition-shadow duration-300">
                        <form
                            action="{{ route('contact.submit') }}"
                            method="POST"
                            x-data="contactForm()"
                            @submit.prevent="submitForm()"
                            class="space-y-6"
                        >
                            @csrf

                            <!-- Form Header -->
                            <div class="text-center mb-10">
                                <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-3">Send us a Message</h2>
                                <p class="text-gray-600 dark:text-gray-400">We'll get back to you within 24 hours</p>
                            </div>

                            <!-- Name Fields -->
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                                <div class="space-y-2">
                                    <label for="first_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        First Name <span class="text-red-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <input
                                            type="text"
                                            id="first_name"
                                            name="first_name"
                                            x-model="form.first_name"
                                            required
                                            class="block w-full px-4 py-3 text-gray-900 dark:text-white bg-white/50 dark:bg-gray-700/50 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent backdrop-blur-sm transition-all duration-200 placeholder-gray-500 dark:placeholder-gray-400"
                                            placeholder="John"
                                        >
                                        <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                                            <svg x-show="form.first_name" class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                            </svg>
                                        </div>
                                    </div>
                                </div>

                                <div class="space-y-2">
                                    <label for="last_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Last Name <span class="text-red-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <input
                                            type="text"
                                            id="last_name"
                                            name="last_name"
                                            x-model="form.last_name"
                                            required
                                            class="block w-full px-4 py-3 text-gray-900 dark:text-white bg-white/50 dark:bg-gray-700/50 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent backdrop-blur-sm transition-all duration-200 placeholder-gray-500 dark:placeholder-gray-400"
                                            placeholder="Doe"
                                        >
                                        <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                                            <svg x-show="form.last_name" class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                            </svg>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Email -->
                            <div class="space-y-2">
                                <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Email Address <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <input
                                        type="email"
                                        id="email"
                                        name="email"
                                        x-model="form.email"
                                        required
                                        class="block w-full px-4 py-3 text-gray-900 dark:text-white bg-white/50 dark:bg-gray-700/50 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent backdrop-blur-sm transition-all duration-200 placeholder-gray-500 dark:placeholder-gray-400"
                                        placeholder="john.doe@example.com"
                                    >
                                    <div class="absolute inset-y-0 left-0 flex items-center pl-3">
                                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                        </svg>
                                    </div>
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                                        <svg x-show="isValidEmail(form.email)" class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                    </div>
                                </div>
                            </div>

                            <!-- Subject -->
                            <div class="space-y-2">
                                <label for="subject" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Subject <span class="text-red-500">*</span>
                                </label>
                                <select
                                    id="subject"
                                    name="subject"
                                    x-model="form.subject"
                                    required
                                    class="block w-full px-4 py-3 text-gray-900 dark:text-white bg-white/50 dark:bg-gray-700/50 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent backdrop-blur-sm transition-all duration-200"
                                >
                                    <option value="">Select a subject</option>
                                    <option value="general">General Inquiry</option>
                                    <option value="support">Technical Support</option>
                                    <option value="billing">Billing Question</option>
                                    <option value="feedback">Feedback</option>
                                    <option value="partnership">Partnership Opportunity</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>

                            <!-- Message -->
                            <div class="space-y-2">
                                <label for="message" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Message <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                        <textarea
                                            id="message"
                                            name="message"
                                            rows="6"
                                            x-model="form.message"
                                            required
                                            class="block w-full px-4 py-3 text-gray-900 dark:text-white bg-white/50 dark:bg-gray-700/50 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent backdrop-blur-sm transition-all duration-200 placeholder-gray-500 dark:placeholder-gray-400 resize-none"
                                            placeholder="Tell us how we can help you..."
                                        ></textarea>
                                    <div class="absolute bottom-3 right-3">
                                            <span
                                                x-text="form.message.length + '/500'"
                                                :class="form.message.length > 500 ? 'text-red-500' : 'text-gray-400'"
                                                class="text-sm"
                                            ></span>
                                    </div>
                                </div>
                            </div>

                            <!-- Newsletter -->
                            <div class="flex items-start space-x-3">
                                <input
                                    type="checkbox"
                                    id="newsletter"
                                    name="newsletter"
                                    x-model.boolean="form.newsletter"
                                    class="mt-1 w-4 h-4 text-blue-600 bg-white dark:bg-gray-700 border-gray-300 dark:border-gray-600 rounded focus:ring-blue-500 focus:ring-2"
                                >
                                <label for="newsletter" class="text-sm text-gray-700 dark:text-gray-300">
                                    I'd like to receive updates about new features and educational content.
                                </label>
                            </div>

                            <!-- Submit Button -->
                            <div class="pt-6">
                                <button
                                    type="submit"
                                    :disabled="loading"
                                    :class="loading ? 'opacity-50 cursor-not-allowed' : 'hover:shadow-xl hover:shadow-indigo-500/30 hover:scale-[1.02]'"
                                    class="w-full bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white font-bold py-4 px-8 rounded-xl transition-all duration-300 transform focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800"
                                >
                                        <span x-show="!loading" class="flex items-center justify-center space-x-2">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                                            </svg>
                                            <span>Send Message</span>
                                        </span>
                                    <span x-show="loading" class="flex items-center justify-center space-x-2">
                                            <svg class="animate-spin w-5 h-5" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                            </svg>
                                            <span>Sending...</span>
                                        </span>
                                </button>
                            </div>

                            <!-- Success/Error Messages -->
                            <div x-show="showSuccess" x-transition class="p-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg">
                                <div class="flex items-center space-x-2">
                                    <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    <p class="text-green-800 dark:text-green-200">Thank you! Your message has been sent successfully.</p>
                                </div>
                            </div>

                            <div x-show="showError" x-transition class="p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg">
                                <div class="flex items-center space-x-2">
                                    <svg class="w-5 h-5 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <p class="text-red-800 dark:text-red-200" x-text="errorMessage"></p>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>

    @push('head')
        <script>
            function contactForm() {
                return {
                    form: {
                        first_name: '',
                        last_name: '',
                        email: '',
                        subject: '',
                        message: '',
                        newsletter: false
                    },
                    loading: false,
                    showSuccess: false,
                    showError: false,
                    errorMessage: '',

                    isValidEmail(email) {
                        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                        return email && emailRegex.test(email);
                    },

                    async submitForm() {
                        this.loading = true;
                        this.showSuccess = false;
                        this.showError = false;

                        try {
                            const formData = new FormData();
                            Object.keys(this.form).forEach(key => {
                                formData.append(key, this.form[key]);
                            });
                            formData.append('_token', document.querySelector('[name="_token"]').value);

                            const response = await window.axios.post('{{ route("contact.submit") }}', formData);

                            const data = await response.data;

                            if (response.status === 200) {
                                this.showSuccess = true;
                                this.resetForm();
                            } else {
                                this.showError = true;
                                this.errorMessage = data.message || 'An error occurred while sending your message.';
                            }
                        } catch (error) {
                            this.showError = true;
                            this.errorMessage = 'An error occurred while sending your message.';
                        } finally {
                            this.loading = false;
                        }
                    },

                    resetForm() {
                        this.form = {
                            first_name: '',
                            last_name: '',
                            email: '',
                            subject: '',
                            message: '',
                            newsletter: false
                        };
                    }
                }
            }
        </script>
    @endpush
</x-layouts.guest>
