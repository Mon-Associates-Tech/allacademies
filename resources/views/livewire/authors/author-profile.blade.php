<div class="author-profile-container">
    <!-- Profile Header -->
    <div class="bg-white dark:bg-gray-900 shadow-sm border-b border-gray-200 dark:border-gray-700 mb-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="lg:flex lg:items-center lg:justify-between">
                <div class="flex-1 min-w-0">
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Author Profile</h1>
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                        Manage your author profile, books, and account settings
                    </p>
                </div>
                <div class="mt-5 flex lg:mt-0 lg:ml-4 space-x-3">
                    <button wire:click="showPasswordModal"
                            class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                        </svg>
                        Change Password
                    </button>
                    <button wire:click="refreshProfile"
                            class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                        Refresh
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Flash Messages -->
    @if (session()->has('message'))
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-6">
            <div class="bg-green-50 dark:bg-green-900 border border-green-200 dark:border-green-700 rounded-md p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-green-800 dark:text-green-200">{{ session('message') }}</p>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-6">
            <div class="bg-red-50 dark:bg-red-900 border border-red-200 dark:border-red-700 rounded-md p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-red-800 dark:text-red-200">{{ session('error') }}</p>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Profile Content Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Profile Information Card -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Basic Information -->
                <div class="bg-white dark:bg-gray-900 shadow-sm border border-gray-200 dark:border-gray-700 rounded-lg">
                    <div class="px-6 py-5 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">Basic Information</h3>
                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                            Update your basic profile information and contact details.
                        </p>
                    </div>

                    <div class="px-6 py-5">
                        <form wire:submit.prevent="updateProfile" class="space-y-6">
                            <!-- Profile Picture -->
                            <div class="flex items-center space-x-6">
                                <div class="shrink-0">
                                    @if(auth()->user()->avatar)
                                        <img class="h-20 w-20 rounded-full object-cover border-2 border-gray-200 dark:border-gray-600"
                                             src="{{ Storage::url(auth()->user()->avatar) }}"
                                             alt="{{ auth()->user()->name }}">
                                    @else
                                        <div class="h-20 w-20 rounded-full bg-violet-100 dark:bg-violet-900 flex items-center justify-center">
                                            <svg class="h-10 w-10 text-violet-600 dark:text-violet-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                            </svg>
                                        </div>
                                    @endif
                                </div>
                                <div>
                                    <label for="avatar" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Profile Picture
                                    </label>
                                    <input type="file" id="avatar" wire:model="avatar"
                                           class="mt-1 block w-full text-sm text-gray-500 dark:text-gray-400
                                                  file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0
                                                  file:text-sm file:font-semibold file:bg-violet-50 file:text-violet-700
                                                  hover:file:bg-violet-100 dark:file:bg-gray-700 dark:file:text-gray-300">
                                    @error('avatar')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Name -->
                                <div>
                                    <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Full Name *
                                    </label>
                                    <input type="text" id="name" wire:model="name"
                                           class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm
                                                  focus:border-violet-500 focus:ring-violet-500 dark:bg-gray-800 dark:text-white
                                                  sm:text-sm">
                                    @error('name')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Pen Name -->
                                <div>
                                    <label for="pen_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Pen Name
                                    </label>
                                    <input type="text" id="pen_name" wire:model="pen_name"
                                           class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm
                                                  focus:border-violet-500 focus:ring-violet-500 dark:bg-gray-800 dark:text-white
                                                  sm:text-sm">
                                    @error('pen_name')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Email -->
                                <div>
                                    <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Email Address *
                                    </label>
                                    <input type="email" id="email" wire:model="email"
                                           class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm
                                                  focus:border-violet-500 focus:ring-violet-500 dark:bg-gray-800 dark:text-white
                                                  sm:text-sm">
                                    @error('email')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Phone -->
                                <div>
                                    <label for="phone" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Phone Number
                                    </label>
                                    <input type="tel" id="phone" wire:model="phone"
                                           class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm
                                                  focus:border-violet-500 focus:ring-violet-500 dark:bg-gray-800 dark:text-white
                                                  sm:text-sm">
                                    @error('phone')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <!-- Website -->
                            <div>
                                <label for="website" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Website
                                </label>
                                <input type="url" id="website" wire:model="website"
                                       placeholder="https://your-website.com"
                                       class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm
                                              focus:border-violet-500 focus:ring-violet-500 dark:bg-gray-800 dark:text-white
                                              sm:text-sm">
                                @error('website')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Bio -->
                            <div>
                                <label for="bio" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Bio
                                </label>
                                <textarea id="bio" wire:model="bio" rows="4"
                                          class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm
                                                 focus:border-violet-500 focus:ring-violet-500 dark:bg-gray-800 dark:text-white
                                                 sm:text-sm"
                                          placeholder="Tell readers about yourself..."></textarea>
                                @error('bio')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Author Statement -->
                            <div>
                                <label for="author_statement" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Author Statement
                                </label>
                                <textarea id="author_statement" wire:model="author_statement" rows="4"
                                          class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm
                                                 focus:border-violet-500 focus:ring-violet-500 dark:bg-gray-800 dark:text-white
                                                 sm:text-sm"
                                          placeholder="Your writing philosophy and mission..."></textarea>
                                @error('author_statement')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Writing Experience -->
                                <div>
                                    <label for="writing_experience" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Writing Experience
                                    </label>
                                    <textarea id="writing_experience" wire:model="writing_experience" rows="3"
                                              class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm
                                                     focus:border-violet-500 focus:ring-violet-500 dark:bg-gray-800 dark:text-white
                                                     sm:text-sm"
                                              placeholder="Years of experience, previous publications..."></textarea>
                                    @error('writing_experience')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Education -->
                                <div>
                                    <label for="education" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Education
                                    </label>
                                    <textarea id="education" wire:model="education" rows="3"
                                              class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm
                                                     focus:border-violet-500 focus:ring-violet-500 dark:bg-gray-800 dark:text-white
                                                     sm:text-sm"
                                              placeholder="Educational background..."></textarea>
                                    @error('education')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <!-- Awards -->
                            <div>
                                <label for="awards" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Awards & Recognition
                                </label>
                                <textarea id="awards" wire:model="awards" rows="3"
                                          class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm
                                                 focus:border-violet-500 focus:ring-violet-500 dark:bg-gray-800 dark:text-white
                                                 sm:text-sm"
                                          placeholder="Literary awards, recognition, honors..."></textarea>
                                @error('awards')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Submit Button -->
                            <div class="flex justify-end">
                                <button type="submit"
                                        class="inline-flex items-center px-6 py-3 border border-transparent rounded-md
                                               shadow-sm text-sm font-medium text-white bg-violet-600 hover:bg-violet-700
                                               focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-violet-500
                                               disabled:opacity-50 disabled:cursor-not-allowed">
                                    <svg wire:loading wire:target="updateProfile" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    Save Changes
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Social Media Links -->
                <div class="bg-white dark:bg-gray-900 shadow-sm border border-gray-200 dark:border-gray-700 rounded-lg">
                    <div class="px-6 py-5 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">Social Media Links</h3>
                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                            Connect your social media profiles to increase visibility.
                        </p>
                    </div>

                    <div class="px-6 py-5">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Twitter -->
                            <div>
                                <label for="twitter" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    <svg class="inline w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/>
                                    </svg>
                                    Twitter
                                </label>
                                <input type="url" id="twitter" wire:model="social_links.twitter"
                                       placeholder="https://twitter.com/username"
                                       class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm
                                              focus:border-violet-500 focus:ring-violet-500 dark:bg-gray-800 dark:text-white
                                              sm:text-sm">
                            </div>

                            <!-- Facebook -->
                            <div>
                                <label for="facebook" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    <svg class="inline w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                                    </svg>
                                    Facebook
                                </label>
                                <input type="url" id="facebook" wire:model="social_links.facebook"
                                       placeholder="https://facebook.com/username"
                                       class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm
                                              focus:border-violet-500 focus:ring-violet-500 dark:bg-gray-800 dark:text-white
                                              sm:text-sm">
                            </div>

                            <!-- Instagram -->
                            <div>
                                <label for="instagram" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    <svg class="inline w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12.017 0C5.396 0 .029 5.367.029 11.987c0 6.62 5.367 11.987 11.988 11.987 6.62 0 11.987-5.367 11.987-11.987C24.014 5.367 18.637.001 12.017.001zM8.449 16.988c-1.297 0-2.448-.735-3.016-1.816-.568-1.081-.464-2.427.272-3.402L9.18 7.73c1.297-1.72 3.706-2.068 5.426-.77 1.72 1.297 2.068 3.706.77 5.426l-3.475 4.04c-.736.975-2.082 1.079-3.163.511-1.081-.568-1.816-1.719-1.816-3.016 0-.309.044-.616.128-.911.168-.588.504-1.127.973-1.561.469-.434 1.043-.75 1.664-.915.621-.165 1.277-.177 1.905-.035.628.142 1.211.435 1.692.85.481.415.846.937 1.061 1.516.215.579.277 1.201.18 1.808-.097.607-.361 1.178-.767 1.66-.406.482-.925.861-1.508 1.102-.583.241-1.213.336-1.834.277-.621-.059-1.22-.286-1.741-.659-.521-.373-.949-.879-1.245-1.471-.296-.592-.451-1.251-.451-1.92 0-.669.155-1.328.451-1.92.296-.592.724-1.098 1.245-1.471.521-.373 1.12-.6 1.741-.659.621-.059 1.251.036 1.834.277.583.241 1.102.62 1.508 1.102.406.482.67 1.053.767 1.66.097.607.035 1.229-.18 1.808-.215.579-.58 1.101-1.061 1.516-.481.415-1.064.708-1.692.85-.628.142-1.284.13-1.905-.035-.621-.165-1.195-.481-1.664-.915-.469-.434-.805-.973-.973-1.561-.084-.295-.128-.602-.128-.911z"/>
                                    </svg>
                                    Instagram
                                </label>
                                <input type="url" id="instagram" wire:model="social_links.instagram"
                                       placeholder="https://instagram.com/username"
                                       class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm
                                              focus:border-violet-500 focus:ring-violet-500 dark:bg-gray-800 dark:text-white
                                              sm:text-sm">
                            </div>

                            <!-- LinkedIn -->
                            <div>
                                <label for="linkedin" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    <svg class="inline w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/>
                                    </svg>
                                    LinkedIn
                                </label>
                                <input type="url" id="linkedin" wire:model="social_links.linkedin"
                                       placeholder="https://linkedin.com/in/username"
                                       class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm
                                              focus:border-violet-500 focus:ring-violet-500 dark:bg-gray-800 dark:text-white
                                              sm:text-sm">
                            </div>

                            <!-- Goodreads -->
                            <div class="md:col-span-2">
                                <label for="goodreads" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    <svg class="inline w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12 0C5.373 0 0 5.373 0 12s5.373 12 12 12 12-5.373 12-12S18.627 0 12 0zm5.568 8.16c-.169 1.858-.896 3.391-2.182 4.598-1.286 1.207-2.859 1.81-4.72 1.81-1.876 0-3.457-.603-4.744-1.81C4.636 11.551 3.91 10.018 3.741 8.16c-.169-1.858.096-3.391.795-4.598C5.234 2.355 6.38 1.752 7.741 1.752c1.361 0 2.507.603 3.206 1.81.699 1.207.964 2.74.795 4.598z"/>
                                    </svg>
                                    Goodreads
                                </label>
                                <input type="url" id="goodreads" wire:model="social_links.goodreads"
                                       placeholder="https://goodreads.com/author/show/username"
                                       class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm
                                              focus:border-violet-500 focus:ring-violet-500 dark:bg-gray-800 dark:text-white
                                              sm:text-sm">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Profile Stats & Quick Actions -->
            <div class="space-y-6">
                <!-- Profile Stats -->
                <div class="bg-white dark:bg-gray-900 shadow-sm border border-gray-200 dark:border-gray-700 rounded-lg">
                    <div class="px-6 py-5 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">Author Statistics</h3>
                    </div>
                    <div class="px-6 py-5">
                        <div class="space-y-6">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <div class="w-8 h-8 bg-violet-100 dark:bg-violet-900 rounded-lg flex items-center justify-center mr-3">
                                        <svg class="w-4 h-4 text-violet-600 dark:text-violet-400" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M21 5c-1.11-.35-2.33-.5-3.5-.5-1.95 0-4.05.4-5.5 1.5-1.45-1.1-3.55-1.5-5.5-1.5S2.45 4.9 1 6v14.65c0 .25.25.5.5.5.1 0 .15-.05.25-.05C3.1 20.45 5.05 20 6.5 20c1.95 0 4.05.4 5.5 1.5 1.35-.85 3.8-1.5 5.5-1.5 1.65 0 3.35.3 4.75 1.05.1.05.15.05.25.05.25 0 .5-.25.5-.5V6c-.6-.45-1.25-.75-2-1z"/>
                                        </svg>
                                    </div>
                                    <span class="text-sm font-medium text-gray-900 dark:text-white">Books Published</span>
                                </div>
                                <span class="text-2xl font-bold text-gray-900 dark:text-white">{{ $totalBooks ?? 0 }}</span>
                            </div>

                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <div class="w-8 h-8 bg-green-100 dark:bg-green-900 rounded-lg flex items-center justify-center mr-3">
                                        <svg class="w-4 h-4 text-green-600 dark:text-green-400" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M16 7c0-2.21-1.79-4-4-4s-4 1.79-4 4 1.79 4 4 4 4-1.79 4-4zm4 7v4h-2v-4c0-1.1-.9-2-2-2H8c-1.1 0-2 .9-2 2v4H4v-4c0-2.21 1.79-4 4-4h8c2.21 0 4 1.79 4 4z"/>
                                        </svg>
                                    </div>
                                    <span class="text-sm font-medium text-gray-900 dark:text-white">Subscriptions</span>
                                </div>
                                <span class="text-2xl font-bold text-gray-900 dark:text-white">{{ $totalSubscriptions ?? 0 }}</span>
                            </div>

                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <div class="w-8 h-8 bg-blue-100 dark:bg-blue-900 rounded-lg flex items-center justify-center mr-3">
                                        <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M17 3H7c-1.1 0-2 .9-2 2v16l7-3 7 3V5c0-1.1-.9-2-2-2z"/>
                                        </svg>
                                    </div>
                                    <span class="text-sm font-medium text-gray-900 dark:text-white">Borrowings</span>
                                </div>
                                <span class="text-2xl font-bold text-gray-900 dark:text-white">{{ $totalBorrowings ?? 0 }}</span>
                            </div>

                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <div class="w-8 h-8 bg-yellow-100 dark:bg-yellow-900 rounded-lg flex items-center justify-center mr-3">
                                        <svg class="w-4 h-4 text-yellow-600 dark:text-yellow-400" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm.31-8.86c-1.77-.45-2.34-.94-2.34-1.67 0-.84.79-1.43 2.1-1.43 1.38 0 1.9.66 1.94 1.64h1.71c-.05-1.34-.87-2.57-2.49-2.97V5H10.9v1.69c-1.51.32-2.72 1.3-2.72 2.81 0 1.79 1.49 2.69 3.66 3.21 1.95.46 2.34 1.15 2.34 1.87 0 .53-.39 1.39-2.1 1.39-1.6 0-2.23-.72-2.32-1.64H8.04c.1 1.7 1.36 2.66 2.86 2.97V19h2.34v-1.67c1.52-.29 2.72-1.16 2.73-2.77-.01-2.2-1.9-2.96-3.66-3.42z"/>
                                        </svg>
                                    </div>
                                    <span class="text-sm font-medium text-gray-900 dark:text-white">Revenue</span>
                                </div>
                                <span class="text-xl font-bold text-gray-900 dark:text-white">GHS {{ number_format($totalRevenue ?? 0, 2) }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="bg-white dark:bg-gray-900 shadow-sm border border-gray-200 dark:border-gray-700 rounded-lg">
                    <div class="px-6 py-5 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">Quick Actions</h3>
                    </div>
                    <div class="px-6 py-5">
                        <div class="space-y-3">
                            <a href="{{ route('author.books.create') }}"
                               class="w-full inline-flex items-center justify-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-violet-600 hover:bg-violet-700">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                </svg>
                                Create New Book
                            </a>

                            <a href="{{ route('author.books.index') }}"
                               class="w-full inline-flex items-center justify-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                </svg>
                                Manage Books
                            </a>

                            <a href="{{ route('author.analytics.index') }}"
                               class="w-full inline-flex items-center justify-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                </svg>
                                View Analytics
                            </a>

                            <a href="{{ route('author.revenue.index') }}"
                               class="w-full inline-flex items-center justify-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                                </svg>
                                Revenue Report
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Profile Completion -->
                <div class="bg-white dark:bg-gray-900 shadow-sm border border-gray-200 dark:border-gray-700 rounded-lg">
                    <div class="px-6 py-5">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Profile Completion</h3>

                        @php
                            $completionFields = [
                                'avatar' => auth()->user()->avatar ? 1 : 0,
                                'bio' => auth()->user()->bio ? 1 : 0,
                                'phone' => auth()->user()->phone ? 1 : 0,
                                'website' => auth()->user()->website ? 1 : 0,
                                'author_statement' => auth()->user()->author_statement ? 1 : 0,
                                'social_links' => (auth()->user()->social_links && count(array_filter(auth()->user()->social_links ?? []))) ? 1 : 0,
                            ];
                            $completionPercentage = (array_sum($completionFields) / count($completionFields)) * 100;
                        @endphp

                        <div class="mb-4">
                            <div class="flex justify-between text-sm mb-1">
                                <span class="text-gray-600 dark:text-gray-400">Profile Completion</span>
                                <span class="text-gray-900 dark:text-white font-medium">{{ number_format($completionPercentage) }}%</span>
                            </div>
                            <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                <div class="bg-violet-600 h-2 rounded-full transition-all duration-300" style="width: {{ $completionPercentage }}%"></div>
                            </div>
                        </div>

                        <div class="space-y-2 text-sm">
                            @if(!auth()->user()->avatar)
                                <div class="flex items-center text-gray-600 dark:text-gray-400">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9"/>
                                    </svg>
                                    Add profile picture
                                </div>
                            @endif

                            @if(!auth()->user()->bio)
                                <div class="flex items-center text-gray-600 dark:text-gray-400">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9"/>
                                    </svg>
                                    Add bio
                                </div>
                            @endif

                            @if(!auth()->user()->website)
                                <div class="flex items-center text-gray-600 dark:text-gray-400">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9"/>
                                    </svg>
                                    Add website
                                </div>
                            @endif

                            @if(!auth()->user()->social_links || !count(array_filter(auth()->user()->social_links ?? [])))
                                <div class="flex items-center text-gray-600 dark:text-gray-400">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9"/>
                                    </svg>
                                    Add social media links
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Password Modal -->
    @if($showPasswordModal)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white dark:bg-gray-800 rounded-lg p-6 max-w-md w-full mx-4">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">Change Password</h3>
                    <button wire:click="hidePasswordModal" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <form wire:submit.prevent="updatePassword" class="space-y-4">
                    <div>
                        <label for="current_password" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Current Password
                        </label>
                        <input type="password" id="current_password" wire:model="current_password"
                               class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm
                                      focus:border-violet-500 focus:ring-violet-500 dark:bg-gray-700 dark:text-white
                                      sm:text-sm">
                        @error('current_password')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="new_password" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            New Password
                        </label>
                        <input type="password" id="new_password" wire:model="new_password"
                               class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm
                                      focus:border-violet-500 focus:ring-violet-500 dark:bg-gray-700 dark:text-white
                                      sm:text-sm">
                        @error('new_password')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="new_password_confirmation" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Confirm New Password
                        </label>
                        <input type="password" id="new_password_confirmation" wire:model="new_password_confirmation"
                               class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm
                                      focus:border-violet-500 focus:ring-violet-500 dark:bg-gray-700 dark:text-white
                                      sm:text-sm">
                        @error('new_password_confirmation')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex justify-end space-x-4 pt-4">
                        <button type="button" wire:click="hidePasswordModal"
                                class="px-4 py-2 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-md transition-colors">
                            Cancel
                        </button>
                        <button type="submit"
                                class="px-4 py-2 bg-violet-500 hover:bg-violet-600 text-white rounded-md transition-colors">
                            Update Password
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
