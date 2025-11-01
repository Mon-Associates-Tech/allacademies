<div
    class="min-h-screen bg-gradient-to-br from-indigo-50 via-purple-50 to-pink-50 dark:from-gray-900 dark:via-indigo-900/10 dark:to-purple-900/10">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        <!-- Enhanced Header Section -->
        <div class="relative mb-8 overflow-hidden rounded-3xl">
            <div class="absolute inset-0 bg-gradient-to-r from-indigo-600 via-purple-600 to-pink-600 opacity-90"></div>
            <div class="absolute inset-0 bg-black opacity-20"></div>
            <div class="absolute inset-0"
                 style="background-image: url('data:image/svg+xml;charset=utf-8,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'80\' height=\'80\' viewBox=\'0 0 80 80\'%3E%3Cg fill-rule=\'evenodd\'%3E%3Cg fill=\'%23ffffff\' fill-opacity=\'0.05\'%3E%3Cpath d=\'M0 0h80v80H0V0zm20 20v40h40V20H20zm20 35a15 15 0 1 1 0-30 15 15 0 0 1 0 30z\'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E')"></div>

            <div class="relative px-8 py-12">
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
                    <div class="mb-6 lg:mb-0">
                        <div class="flex items-center mb-4">
                            <div class="p-3 bg-white/20 rounded-full backdrop-blur-sm mr-4">
                                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                            </div>
                            <div>
                                <h1 class="text-4xl lg:text-5xl font-bold text-white mb-2">Account Settings</h1>
                                <p class="text-purple-100 text-lg">Manage your profile, security, and preferences</p>
                            </div>
                        </div>
                    </div>

                    <!-- User Info Card -->
                    <div class="bg-white/20 backdrop-blur-sm rounded-2xl p-6 border border-white/30">
                        <div class="flex items-center space-x-4">
                            <div class="relative">
                                @if($avatar)
                                    <img src="{{ Storage::url($avatar) }}" alt="Avatar"
                                         class="w-16 h-16 rounded-full object-cover border-4 border-white/50">
                                @else
                                    <div
                                        class="w-16 h-16 rounded-full bg-gradient-to-br from-purple-500 to-pink-500 flex items-center justify-center text-white font-bold text-2xl border-4 border-white/50">
                                        {{ strtoupper(substr($name, 0, 1)) }}
                                    </div>
                                @endif
                                <div
                                    class="absolute -top-1 -right-1 w-5 h-5 bg-green-500 rounded-full border-2 border-white"></div>
                            </div>
                            <div class="text-white">
                                <h3 class="font-semibold text-lg">{{ $name }}</h3>
                                <p class="text-purple-100 text-sm">{{ $email }}</p>
                                <p class="text-purple-100 text-xs">{{ ucfirst($subscription_tier) }} Plan</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Flash Messages -->
        @if (session()->has('profile-updated'))
            <div
                class="mb-6 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-700 text-green-800 dark:text-green-200 px-4 py-3 rounded-xl">
                {{ session('profile-updated') }}
            </div>
        @endif

        @if (session()->has('password-updated'))
            <div
                class="mb-6 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-700 text-green-800 dark:text-green-200 px-4 py-3 rounded-xl">
                {{ session('password-updated') }}
            </div>
        @endif

        <!-- Settings Navigation -->
        <div
            class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700 mb-8 overflow-hidden">
            <div class="flex flex-wrap border-b border-gray-200 dark:border-gray-700 overflow-x-auto">
                @php
                    $tabs = [
                        'profile' => ['icon' => 'user', 'label' => 'Profile'],
                        'security' => ['icon' => 'shield-check', 'label' => 'Security'],
                        'notifications' => ['icon' => 'bell', 'label' => 'Notifications'],
                        'billing' => ['icon' => 'credit-card', 'label' => 'Billing'],
                        'account' => ['icon' => 'cog', 'label' => 'Account']
                    ];
                @endphp

                @foreach($tabs as $tab => $config)
                    <button wire:click="setActiveTab('{{ $tab }}')"
                            class="flex-shrink-0 px-6 py-4 text-sm font-medium transition-all duration-200 {{ $activeTab === $tab ? 'bg-purple-50 dark:bg-purple-900/20 text-purple-700 dark:text-purple-300 border-b-2 border-purple-500' : 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700' }}">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                @if($config['icon'] === 'user')
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                @elseif($config['icon'] === 'shield-check')
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.031 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                @elseif($config['icon'] === 'bell')
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                                @elseif($config['icon'] === 'eye-off')
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21"/>
                                @elseif($config['icon'] === 'book-open')
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                @elseif($config['icon'] === 'credit-card')
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                                @elseif($config['icon'] === 'link')
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                                @else
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                @endif
                            </svg>
                            {{ $config['label'] }}
                        </div>
                    </button>
                @endforeach
            </div>
        </div>

        <!-- Profile Tab -->
        @if($activeTab === 'profile')
            <div class="space-y-8">
                <!-- Profile Information -->
                <div
                    class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <div
                        class="bg-gradient-to-r from-purple-50 to-pink-50 dark:from-purple-900/20 dark:to-pink-900/20 px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                            <svg class="w-5 h-5 mr-2 text-purple-600" fill="none" stroke="currentColor"
                                 viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            Profile Information
                        </h3>
                    </div>

                    <form wire:submit.prevent="updateProfile" class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Avatar Upload -->
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-4">Profile
                                    Picture</label>
                                <div class="flex items-center space-x-6">
                                    @if($avatar)
                                        <img src="{{ Storage::url($avatar) }}" alt="Avatar"
                                             class="w-20 h-20 rounded-full object-cover border-4 border-purple-100">
                                    @else
                                        <div
                                            class="w-20 h-20 rounded-full bg-gradient-to-br from-purple-500 to-pink-500 flex items-center justify-center text-white font-bold text-2xl border-4 border-purple-100">
                                            {{ strtoupper(substr($name, 0, 1)) }}
                                        </div>
                                    @endif

                                    <div class="space-y-2">
                                        <div class="flex space-x-3">
                                            <label
                                                class="cursor-pointer inline-flex items-center px-4 py-2 border border-purple-300 rounded-lg text-sm font-medium text-purple-700 bg-white hover:bg-purple-50 focus:outline-none focus:ring-2 focus:ring-purple-500">
                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor"
                                                     viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                          stroke-width="2"
                                                          d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                                </svg>
                                                Upload new
                                                <input type="file" wire:model="new_avatar" class="hidden"
                                                       accept="image/*">
                                            </label>

                                            @if($avatar)
                                                <button type="button" wire:click="removeAvatar"
                                                        class="inline-flex items-center px-4 py-2 border border-red-300 rounded-lg text-sm font-medium text-red-700 bg-white hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-red-500">
                                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor"
                                                         viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                              stroke-width="2"
                                                              d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                    </svg>
                                                    Remove
                                                </button>
                                            @endif
                                        </div>

                                        @if($new_avatar)
                                            <button type="button" wire:click="uploadAvatar"
                                                    class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg text-sm font-medium hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500">
                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor"
                                                     viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                          stroke-width="2" d="M5 13l4 4L19 7"/>
                                                </svg>
                                                Save Avatar
                                            </button>
                                        @endif

                                        <p class="text-xs text-gray-500 dark:text-gray-400">JPG, PNG or GIF. Max size
                                            2MB.</p>
                                        @error('new_avatar') <span
                                            class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Basic Info -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Full
                                    Name</label>
                                <input wire:model="name" type="text"
                                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 dark:bg-gray-700 dark:text-white"
                                       placeholder="Enter your full name">
                                @error('name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Email
                                    Address</label>
                                <input wire:model="email" type="email"
                                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 dark:bg-gray-700 dark:text-white"
                                       placeholder="Enter your email">
                                @error('email') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Pen
                                    Name</label>
                                <input wire:model="pen_name" type="text"
                                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 dark:bg-gray-700 dark:text-white"
                                       placeholder="Enter your pen name (optional)">
                                @error('pen_name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Website</label>
                                <input wire:model="website" type="url"
                                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 dark:bg-gray-700 dark:text-white"
                                       placeholder="https://your-website.com">
                                @error('website') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <!-- Biography -->
                            <div class="md:col-span-2">
                                <label
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Biography</label>
                                <textarea wire:model="biography" rows="4"
                                          class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 dark:bg-gray-700 dark:text-white"
                                          placeholder="Tell readers about yourself..."></textarea>
                                @error('biography') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <!-- Writing Experience -->
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Writing
                                    Experience</label>
                                <textarea wire:model="writing_experience" rows="3"
                                          class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 dark:bg-gray-700 dark:text-white"
                                          placeholder="Describe your writing experience..."></textarea>
                                @error('writing_experience') <span
                                    class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <!-- Education -->
                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Education</label>
                                <textarea wire:model="education" rows="3"
                                          class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 dark:bg-gray-700 dark:text-white"
                                          placeholder="Your educational background..."></textarea>
                                @error('education') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <!-- Awards -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Awards &
                                    Recognition</label>
                                <textarea wire:model="awards" rows="3"
                                          class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 dark:bg-gray-700 dark:text-white"
                                          placeholder="Any awards or recognition..."></textarea>
                                @error('awards') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <!-- Author Statement -->
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Author
                                    Statement</label>
                                <textarea wire:model="author_statement" rows="4"
                                          class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 dark:bg-gray-700 dark:text-white"
                                          placeholder="Your personal statement as an author..."></textarea>
                                @error('author_statement') <span
                                    class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="mt-6 flex justify-end">
                            <button type="submit"
                                    class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-lg text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M5 13l4 4L19 7"/>
                                </svg>
                                Update Profile
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Social Media Links -->
                <div
                    class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <div
                        class="bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                            <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor"
                                 viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                            </svg>
                            Social Media Links
                        </h3>
                    </div>

                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            @foreach($social_links as $platform => $url)
                                <div>
                                    <label
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2 capitalize">
                                        {{ $platform }}
                                    </label>
                                    <input wire:model="social_links.{{ $platform }}" type="url"
                                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 dark:bg-gray-700 dark:text-white"
                                           placeholder="https://{{ $platform }}.com/yourprofile">
                                </div>
                            @endforeach
                        </div>

                        <div class="mt-6 flex justify-end">
                            <button wire:click="updateProfile"
                                    class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-lg text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M5 13l4 4L19 7"/>
                                </svg>
                                Update Social Links
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Genres & Languages -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Genres -->
                    <div
                        class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
                        <div
                            class="bg-gradient-to-r from-green-50 to-teal-50 dark:from-green-900/20 dark:to-teal-900/20 px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                                <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor"
                                     viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                                </svg>
                                Writing Genres
                            </h3>
                        </div>

                        <div class="p-6">
                            <div class="flex flex-wrap gap-2 mb-4">
                                @foreach($genres as $genre)
                                    <span
                                        class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800 dark:bg-green-900/50 dark:text-green-300">
                                        {{ $genre }}
                                        <button wire:click="removeGenre('{{ $genre }}')"
                                                class="ml-2 text-green-600 hover:text-green-800 dark:text-green-400 dark:hover:text-green-200">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M6 18L18 6M6 6l12 12"/>
                                            </svg>
                                        </button>
                                    </span>
                                @endforeach
                            </div>

                            <div class="space-y-2">
                                <select
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 dark:bg-gray-700 dark:text-white"
                                    onchange="@this.addGenre(this.value); this.value = '';">
                                    <option value="">Select a genre to add...</option>
                                    @foreach($availableGenres as $genre)
                                        @if(!in_array($genre, $genres))
                                            <option value="{{ $genre }}">{{ $genre }}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Languages -->
                    <div
                        class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
                        <div
                            class="bg-gradient-to-r from-orange-50 to-red-50 dark:from-orange-900/20 dark:to-red-900/20 px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                                <svg class="w-5 h-5 mr-2 text-orange-600" fill="none" stroke="currentColor"
                                     viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129"/>
                                </svg>
                                Languages
                            </h3>
                        </div>

                        <div class="p-6">
                            <div class="flex flex-wrap gap-2 mb-4">
                                @foreach($languages as $language)
                                    <span
                                        class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-orange-100 text-orange-800 dark:bg-orange-900/50 dark:text-orange-300">
                                        {{ $language }}
                                        <button wire:click="removeLanguage('{{ $language }}')"
                                                class="ml-2 text-orange-600 hover:text-orange-800 dark:text-orange-400 dark:hover:text-orange-200">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M6 18L18 6M6 6l12 12"/>
                                            </svg>
                                        </button>
                                    </span>
                                @endforeach
                            </div>

                            <div class="space-y-2">
                                <select
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 dark:bg-gray-700 dark:text-white"
                                    onchange="@this.addLanguage(this.value); this.value = '';">
                                    <option value="">Select a language to add...</option>
                                    @foreach($availableLanguages as $language)
                                        @if(!in_array($language, $languages))
                                            <option value="{{ $language }}">{{ $language }}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Security Tab -->
        @if($activeTab === 'security')
            <div class="space-y-8">
                <!-- Change Password -->
                <div
                    class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <div
                        class="bg-gradient-to-r from-red-50 to-pink-50 dark:from-red-900/20 dark:to-pink-900/20 px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                            <svg class="w-5 h-5 mr-2 text-red-600" fill="none" stroke="currentColor"
                                 viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                            Change Password
                        </h3>
                    </div>

                    <form wire:submit.prevent="updatePassword" class="p-6">
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Current
                                    Password</label>
                                <input wire:model="current_password" type="password"
                                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 dark:bg-gray-700 dark:text-white"
                                       placeholder="Enter current password">
                                @error('current_password') <span
                                    class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">New
                                    Password</label>
                                <input wire:model="new_password" type="password"
                                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 dark:bg-gray-700 dark:text-white"
                                       placeholder="Enter new password">
                                @error('new_password') <span
                                    class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Confirm
                                    New Password</label>
                                <input wire:model="new_password_confirmation" type="password"
                                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 dark:bg-gray-700 dark:text-white"
                                       placeholder="Confirm new password">
                                @error('new_password_confirmation') <span
                                    class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="mt-6 flex justify-end">
                            <button type="submit"
                                    class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-lg text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M5 13l4 4L19 7"/>
                                </svg>
                                Update Password
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Two-Factor Authentication -->
                <div
                    class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <div
                        class="bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                            <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor"
                                 viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.031 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                            </svg>
                            Two-Factor Authentication
                        </h3>
                    </div>

                    <div class="p-6">
                        <div class="flex items-center justify-between mb-6">
                            <div>
                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                    Add an extra layer of security to your account by enabling two-factor
                                    authentication.
                                </p>
                            </div>
                            <div class="flex items-center">
                                <button wire:click="{{ $two_factor_enabled ? 'disable2FA' : 'enable2FA' }}"
                                        class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors {{ $two_factor_enabled ? 'bg-blue-600' : 'bg-gray-200' }}">
                                    <span
                                        class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform {{ $two_factor_enabled ? 'translate-x-6' : 'translate-x-1' }}"></span>
                                </button>
                            </div>
                        </div>

                        @if($two_factor_enabled)
                            <div
                                class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-700 rounded-lg p-4 mb-4">
                                <div class="flex items-center">
                                    <svg class="w-5 h-5 text-green-600 mr-2" fill="none" stroke="currentColor"
                                         viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M5 13l4 4L19 7"/>
                                    </svg>
                                    <span class="text-green-800 dark:text-green-200 font-medium">Two-factor authentication is enabled</span>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Active Sessions -->
                <div
                    class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <div
                        class="bg-gradient-to-r from-yellow-50 to-orange-50 dark:from-yellow-900/20 dark:to-orange-900/20 px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                            <svg class="w-5 h-5 mr-2 text-yellow-600" fill="none" stroke="currentColor"
                                 viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                            Active Sessions
                        </h3>
                    </div>

                    <div class="p-6">
                        <div class="space-y-4">
                            <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                <div class="flex items-center space-x-4">
                                    <div class="p-2 bg-green-100 dark:bg-green-900/50 rounded-lg">
                                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor"
                                             viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-900 dark:text-white">Chrome on Windows</p>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">Current session • New York,
                                            NY</p>
                                    </div>
                                </div>
                                <span
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/50 dark:text-green-300">
                                    Active
                                </span>
                            </div>

                            <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                <div class="flex items-center space-x-4">
                                    <div class="p-2 bg-blue-100 dark:bg-blue-900/50 rounded-lg">
                                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor"
                                             viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-900 dark:text-white">Safari on iPhone</p>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">2 hours ago • New York,
                                            NY</p>
                                    </div>
                                </div>
                                <button wire:click="revokeSession('session_id')"
                                        class="text-red-600 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300 font-medium text-sm">
                                    Revoke
                                </button>
                            </div>
                        </div>

                        <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-600">
                            <button wire:click="revokeAllSessions"
                                    class="text-red-600 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300 font-medium text-sm">
                                Revoke all other sessions
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Security Log -->
                <div
                    class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <div
                        class="bg-gradient-to-r from-gray-50 to-gray-100 dark:from-gray-700 dark:to-gray-600 px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                            <svg class="w-5 h-5 mr-2 text-gray-600" fill="none" stroke="currentColor"
                                 viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            Security Log
                        </h3>
                    </div>

                    <div class="p-6">
                        <div class="space-y-4">
                            @foreach($securityLogs as $log)
                                <div
                                    class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                    <div class="flex items-center space-x-4">
                                        <div
                                            class="p-2 bg-{{ $log['status'] === 'success' ? 'green' : 'red' }}-100 dark:bg-{{ $log['status'] === 'success' ? 'green' : 'red' }}-900/50 rounded-lg">
                                            <svg
                                                class="w-5 h-5 text-{{ $log['status'] === 'success' ? 'green' : 'red' }}-600"
                                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                @if($log['status'] === 'success')
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                          stroke-width="2" d="M5 13l4 4L19 7"/>
                                                @else
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                          stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                                @endif
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="font-medium text-gray-900 dark:text-white">{{ $log['action'] }}</p>
                                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ $log['ip_address'] }}
                                                • {{ $log['device'] }}</p>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-sm text-gray-900 dark:text-white">{{ $log['timestamp']->diffForHumans() }}</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ $log['location'] }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Account Tab -->
        @if($activeTab === 'account')
            <div class="space-y-8">
                <!-- Account Overview -->
                <div
                    class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <div
                        class="bg-gradient-to-r from-purple-50 to-pink-50 dark:from-purple-900/20 dark:to-pink-900/20 px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                            <svg class="w-5 h-5 mr-2 text-purple-600" fill="none" stroke="currentColor"
                                 viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            Account Overview
                        </h3>
                    </div>

                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                            <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-4">
                                <div class="flex items-center">
                                    <div class="p-2 bg-blue-100 dark:bg-blue-900/50 rounded-lg mr-3">
                                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor"
                                             viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $accountStats['total_books'] }}</p>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">Total Books</p>
                                    </div>
                                </div>
                            </div>

                            <div class="bg-green-50 dark:bg-green-900/20 rounded-lg p-4">
                                <div class="flex items-center">
                                    <div class="p-2 bg-green-100 dark:bg-green-900/50 rounded-lg mr-3">
                                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor"
                                             viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-2xl font-bold text-gray-900 dark:text-white">
                                            ${{ number_format($accountStats['total_revenue'], 2) }}</p>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">Total Revenue</p>
                                    </div>
                                </div>
                            </div>

                            <div class="bg-yellow-50 dark:bg-yellow-900/20 rounded-lg p-4">
                                <div class="flex items-center">
                                    <div class="p-2 bg-yellow-100 dark:bg-yellow-900/50 rounded-lg mr-3">
                                        <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor"
                                             viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $accountStats['average_rating'] }}</p>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">Average Rating</p>
                                    </div>
                                </div>
                            </div>

                            <div class="bg-purple-50 dark:bg-purple-900/20 rounded-lg p-4">
                                <div class="flex items-center">
                                    <div class="p-2 bg-purple-100 dark:bg-purple-900/50 rounded-lg mr-3">
                                        <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor"
                                             viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($accountStats['followers']) }}</p>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">Followers</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Storage Usage -->
                <div
                    class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <div
                        class="bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                            <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor"
                                 viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s8-1.79 8-4"/>
                            </svg>
                            Storage Usage
                        </h3>
                    </div>

                    <div class="p-6">
                        <div class="mb-6">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                    {{ $storageBreakdown['total_used'] }} MB of {{ $storageBreakdown['total_limit'] }} MB used
                                </span>
                                <span class="text-sm text-gray-500 dark:text-gray-400">
                                    {{ number_format(($storageBreakdown['total_used'] / $storageBreakdown['total_limit']) * 100, 1) }}%
                                </span>
                            </div>
                            <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                <div class="bg-blue-600 h-2 rounded-full"
                                     style="width: {{ ($storageBreakdown['total_used'] / $storageBreakdown['total_limit']) * 100 }}%"></div>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                            @foreach($storageBreakdown['breakdown'] as $type => $data)
                                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                                    <div class="text-center">
                                        <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ $data['size'] }}
                                            MB</p>
                                        <p class="text-sm text-gray-600 dark:text-gray-400 capitalize">{{ $type }}</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ $data['count'] }}
                                            items</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- API Access -->
                <div
                    class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <div
                        class="bg-gradient-to-r from-green-50 to-teal-50 dark:from-green-900/20 dark:to-teal-900/20 px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                            <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor"
                                 viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M8 9l3 3-3 3m5 0h3M5 20h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            API Access
                        </h3>
                    </div>

                    <div class="p-6">
                        <div class="mb-6">
                            <div class="flex items-center justify-between mb-4">
                                <div>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">
                                        Generate an API key to access your data programmatically.
                                    </p>
                                </div>
                                <div class="flex items-center">
                                    <button wire:click="{{ $api_access ? 'revokeApiKey' : 'generateApiKey' }}"
                                            class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors {{ $api_access ? 'bg-green-600' : 'bg-gray-200' }}">
                                        <span
                                            class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform {{ $api_access ? 'translate-x-6' : 'translate-x-1' }}"></span>
                                    </button>
                                </div>
                            </div>

                            @if($api_key)
                                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                                    <div class="flex items-center justify-between">
                                        <div class="flex-1">
                                            <p class="text-sm font-medium text-gray-900 dark:text-white mb-1">API
                                                Key</p>
                                            <p class="text-xs text-gray-500 dark:text-gray-400 font-mono">{{ $api_key }}</p>
                                        </div>
                                        <button
                                            class="ml-4 p-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300"
                                            onclick="navigator.clipboard.writeText('{{ $api_key }}')">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            @endif
                        </div>

                        @if($api_access)
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-4">
                                    <div class="text-center">
                                        <p class="text-lg font-semibold text-blue-900 dark:text-blue-100">{{ number_format($apiUsage['requests_this_month']) }}</p>
                                        <p class="text-sm text-blue-600 dark:text-blue-400">This Month</p>
                                    </div>
                                </div>
                                <div class="bg-green-50 dark:bg-green-900/20 rounded-lg p-4">
                                    <div class="text-center">
                                        <p class="text-lg font-semibold text-green-900 dark:text-green-100">{{ number_format($apiUsage['remaining']) }}</p>
                                        <p class="text-sm text-green-600 dark:text-green-400">Remaining</p>
                                    </div>
                                </div>
                                <div class="bg-purple-50 dark:bg-purple-900/20 rounded-lg p-4">
                                    <div class="text-center">
                                        <p class="text-lg font-semibold text-purple-900 dark:text-purple-100">{{ number_format($apiUsage['rate_limit']) }}</p>
                                        <p class="text-sm text-purple-600 dark:text-purple-400">Rate Limit</p>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Data Export -->
                <div
                    class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <div
                        class="bg-gradient-to-r from-yellow-50 to-orange-50 dark:from-yellow-900/20 dark:to-orange-900/20 px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                            <svg class="w-5 h-5 mr-2 text-yellow-600" fill="none" stroke="currentColor"
                                 viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M12 10v6m0 0l-3-3m3 3l3-3M3 17V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z"/>
                            </svg>
                            Data Export
                        </h3>
                    </div>

                    <div class="p-6">
                        <div class="space-y-4">
                            @foreach($exportOptions as $type => $description)
                                <div
                                    class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                    <div>
                                        <p class="font-medium text-gray-900 dark:text-white">{{ ucfirst(str_replace('_', ' ', $type)) }}</p>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">{{ $description }}</p>
                                    </div>
                                    <button wire:click="exportData('{{ $type }}')"
                                            class="inline-flex items-center px-4 py-2 border border-yellow-300 rounded-lg text-sm font-medium text-yellow-700 bg-white hover:bg-yellow-50 focus:outline-none focus:ring-2 focus:ring-yellow-500 dark:bg-gray-800 dark:border-yellow-600 dark:text-yellow-400 dark:hover:bg-gray-700">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                        Export
                                    </button>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Account Actions -->
                <div
                    class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <div
                        class="bg-gradient-to-r from-red-50 to-pink-50 dark:from-red-900/20 dark:to-pink-900/20 px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                            <svg class="w-5 h-5 mr-2 text-red-600" fill="none" stroke="currentColor"
                                 viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                            Danger Zone
                        </h3>
                    </div>

                    <div class="p-6">
                        <div
                            class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-700 rounded-lg p-4">
                            <div class="flex items-start">
                                <div class="flex-shrink-0">
                                    <svg class="w-5 h-5 text-red-600 mt-1" fill="none" stroke="currentColor"
                                         viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                    </svg>
                                </div>
                                <div class="ml-3 flex-1">
                                    <h4 class="text-sm font-medium text-red-800 dark:text-red-200">Delete Account</h4>
                                    <p class="text-sm text-red-700 dark:text-red-300 mt-1">
                                        Once you delete your account, all of your data will be permanently removed. This
                                        action cannot be undone.
                                    </p>
                                    <div class="mt-4">
                                        <button wire:click="deleteAccount"
                                                class="inline-flex items-center px-4 py-2 border border-red-600 rounded-lg text-sm font-medium text-red-600 bg-white hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-red-500 dark:bg-gray-800 dark:hover:bg-red-900/20">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor"
                                                 viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                            Delete Account
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        @if($activeTab === 'notifications')
            <div
                class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700 p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Notification Settings</h3>
                <p class="text-gray-600 dark:text-gray-400">Notification preferences will be implemented here.</p>

                <livewire:common.mock-data-banner variant="warning" />

            </div>
        @endif

        @if($activeTab === 'privacy')
            <div
                class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700 p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Privacy Settings</h3>
                <p class="text-gray-600 dark:text-gray-400">Privacy preferences will be implemented here.</p>
            </div>
        @endif

        @if($activeTab === 'billing')
            <div class="space-y-8">
                <!-- Payment Account Section -->
                <div
                    class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <div
                        class="bg-gradient-to-r from-green-50 to-teal-50 dark:from-green-900/20 dark:to-teal-900/20 px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                            <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor"
                                 viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                            </svg>
                            Payment Account
                        </h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Set up your bank account to receive
                            payments from book sales (90% of revenue)</p>
                    </div>

                    <div class="p-6">
                        <livewire:common.payment-account-manager :model="$author" model-type="author"/>
                    </div>
                </div>

                <!-- Revenue Information -->
                <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
                    <div class="flex">
                        <svg class="w-5 h-5 text-blue-600 dark:text-blue-400 flex-shrink-0" fill="currentColor"
                             viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                  d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                  clip-rule="evenodd"/>
                        </svg>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-blue-800 dark:text-blue-200">Revenue Sharing
                                Information</h3>
                            <div class="mt-2 text-sm text-blue-700 dark:text-blue-300">
                                <ul class="list-disc list-inside space-y-1">
                                    <li><strong>You receive 90%</strong> of all book subscription payments</li>
                                    <li>Platform fee of 10% helps us maintain and improve the platform</li>
                                    <li>Payments are automatically split and sent to your account</li>
                                    <li>Track detailed earnings in the <a href="{{ route('author.revenue.index') }}"
                                                                          class="underline font-medium">Revenue tab</a>
                                    </li>
                                    <li>Free books do not generate revenue</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Revenue Summary Cards -->
                @if($revenueStats['has_subaccount'])
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <!-- This Month Revenue -->
                        <div
                            class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 hover:shadow-md transition-shadow">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-sm font-medium text-gray-600 dark:text-gray-400">This Month</span>
                                <div class="p-2 bg-green-100 dark:bg-green-900/20 rounded-lg">
                                    <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor"
                                         viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                                    </svg>
                                </div>
                            </div>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">
                                GHS {{ number_format($revenueStats['this_month_revenue'], 2) }}
                            </p>
                            <p class="text-xs text-gray-500 mt-1">
                                {{ $revenueStats['this_month_payments'] }} {{ $revenueStats['this_month_payments'] === 1 ? 'payment' : 'payments' }}
                            </p>
                            <div class="mt-3 pt-3 border-t border-gray-200 dark:border-gray-700">
                                <p class="text-xs text-gray-600 dark:text-gray-400">
                                    Your share (90%)
                                </p>
                            </div>
                        </div>

                        <!-- Total Revenue -->
                        <div
                            class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 hover:shadow-md transition-shadow">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Revenue</span>
                                <div class="p-2 bg-blue-100 dark:bg-blue-900/20 rounded-lg">
                                    <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor"
                                         viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                                    </svg>
                                </div>
                            </div>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">
                                GHS {{ number_format($revenueStats['total_revenue'], 2) }}
                            </p>
                            <p class="text-xs text-gray-500 mt-1">All time earnings</p>
                            <div class="mt-3 pt-3 border-t border-gray-200 dark:border-gray-700">
                                <p class="text-xs text-gray-600 dark:text-gray-400">
                                    {{ $revenueStats['total_payments'] }}
                                    total {{ $revenueStats['total_payments'] === 1 ? 'sale' : 'sales' }}
                                </p>
                            </div>
                        </div>

                        <!-- Average Per Sale -->
                        <div
                            class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 hover:shadow-md transition-shadow">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-sm font-medium text-gray-600 dark:text-gray-400">Avg Per Sale</span>
                                <div class="p-2 bg-purple-100 dark:bg-purple-900/20 rounded-lg">
                                    <svg class="w-5 h-5 text-purple-500" fill="none" stroke="currentColor"
                                         viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                    </svg>
                                </div>
                            </div>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">
                                GHS {{ number_format($revenueStats['average_per_sale'], 2) }}
                            </p>
                            <p class="text-xs text-gray-500 mt-1">Average per book sale</p>
                            <div class="mt-3 pt-3 border-t border-gray-200 dark:border-gray-700">
                                <p class="text-xs text-gray-600 dark:text-gray-400">
                                    After 10% platform fee
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Action to Full Revenue Dashboard -->
                    <div
                        class="bg-gradient-to-r from-indigo-50 to-purple-50 dark:from-indigo-900/20 dark:to-purple-900/20 rounded-xl p-6 border border-indigo-200 dark:border-indigo-800">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="p-3 bg-indigo-600 rounded-lg mr-4">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor"
                                         viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="text-lg font-semibold text-gray-900 dark:text-white">View Detailed
                                        Revenue Analytics</h4>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">Track earnings, view trends, and
                                        analyze performance</p>
                                </div>
                            </div>
                            <a href="{{ route('author.revenue.index') }}"
                               class="inline-flex items-center px-6 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors shadow-lg hover:shadow-xl">
                                View Revenue
                                <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M9 5l7 7-7 7"/>
                                </svg>
                            </a>
                        </div>
                    </div>
                @elseif(!$author->subaccount)
                    <!-- No Revenue Stats - Show Setup Prompt -->
                    <div
                        class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-8 text-center">
                        <div
                            class="mx-auto w-16 h-16 bg-yellow-100 dark:bg-yellow-900/20 rounded-full flex items-center justify-center mb-4">
                            <svg class="w-8 h-8 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor"
                                 viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Set Up Your Payment
                            Account</h3>
                        <p class="text-gray-600 dark:text-gray-400 mb-4 max-w-md mx-auto">
                            Add your bank account above to start receiving payments from book sales. Once set up, your
                            revenue statistics will appear here.
                        </p>
                    </div>
                @endif
            </div>
        @endif

        @if($activeTab === 'integrations')
            <div
                class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700 p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Integrations</h3>
                <p class="text-gray-600 dark:text-gray-400">Third-party integrations will be implemented here.</p>
            </div>
        @endif
    </div>
</div>
