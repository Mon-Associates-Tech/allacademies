<x-layouts.exam>
    <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-indigo-50 to-blue-100 dark:from-gray-900 dark:to-gray-800 py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8">
            <div class="text-center">
                <h2 class="text-4xl font-extrabold text-gray-900 dark:text-white">
                    Join Examination
                </h2>
                <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                    Enter your access code to begin
                </p>
            </div>

            <div class="bg-white dark:bg-gray-800 shadow-xl rounded-lg p-8">
                <form method="POST" action="{{ route('examinations-hub.take.authenticate') }}" class="space-y-6">
                    @csrf

                    @if($errors->any())
                        <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4">
                            <p class="text-sm text-red-700 dark:text-red-400">{{ $errors->first() }}</p>
                        </div>
                    @endif

                    <div>
                        <label for="access_code" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Access Code
                        </label>
                        <input 
                            id="access_code" 
                            name="access_code" 
                            type="text" 
                            required 
                            value="{{ old('access_code') }}"
                            class="appearance-none relative block w-full px-4 py-3 border border-gray-300 dark:border-gray-600 placeholder-gray-500 dark:placeholder-gray-400 text-gray-900 dark:text-white rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 text-center text-lg font-mono uppercase tracking-widest"
                            placeholder="XXXXXXXX"
                            maxlength="8"
                        >
                    </div>

                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Your Name
                        </label>
                        <input 
                            id="name" 
                            name="name" 
                            type="text" 
                            value="{{ old('name') }}"
                            class="appearance-none relative block w-full px-4 py-3 border border-gray-300 dark:border-gray-600 placeholder-gray-500 dark:placeholder-gray-400 text-gray-900 dark:text-white rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700"
                            placeholder="Enter your full name"
                        >
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Email Address
                        </label>
                        <input 
                            id="email" 
                            name="email" 
                            type="email" 
                            value="{{ old('email') }}"
                            class="appearance-none relative block w-full px-4 py-3 border border-gray-300 dark:border-gray-600 placeholder-gray-500 dark:placeholder-gray-400 text-gray-900 dark:text-white rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700"
                            placeholder="your.email@example.com"
                        >
                    </div>

                    <div>
                        <label for="unique_code" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Unique Code (if provided)
                        </label>
                        <input 
                            id="unique_code" 
                            name="unique_code" 
                            type="text" 
                            value="{{ old('unique_code') }}"
                            class="appearance-none relative block w-full px-4 py-3 border border-gray-300 dark:border-gray-600 placeholder-gray-500 dark:placeholder-gray-400 text-gray-900 dark:text-white rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700"
                            placeholder="Your unique participant code"
                        >
                    </div>

                    <div>
                        <button 
                            type="submit" 
                            class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-lg text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors"
                        >
                            <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                                <svg class="h-5 w-5 text-indigo-500 group-hover:text-indigo-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
                                </svg>
                            </span>
                            Join Examination
                        </button>
                    </div>
                </form>
            </div>

            <div class="text-center">
                <p class="text-xs text-gray-500 dark:text-gray-400">
                    Make sure you have a stable internet connection before starting
                </p>
            </div>
        </div>
    </div>
</x-layouts.exam>
