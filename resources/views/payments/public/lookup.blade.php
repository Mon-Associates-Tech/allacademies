<x-layouts.guest page-name="Pay School Fees">
    <!DOCTYPE html>
    <html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <body class="antialiased bg-gray-50 dark:bg-gray-900">
    <div class="min-h-screen flex flex-col justify-center py-12 sm:px-6 lg:px-8">
        <div class="sm:mx-auto sm:w-full sm:max-w-md">
            <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900 dark:text-white">
                Pay School Fees
            </h2>
            <p class="mt-2 text-center text-sm text-gray-600 dark:text-gray-400">
                Enter student information to continue
            </p>
        </div>

        <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
            <div class="bg-white dark:bg-gray-800 py-8 px-4 shadow sm:rounded-lg sm:px-10"
                 x-data="{
                    mode: 'individual',
                    inputs: [''],
                    addInput() {
                        this.inputs.push('');
                    },
                    checkAndAdd(index) {
                        // If typing in the last input, add a new one
                        if (index === this.inputs.length - 1 && this.inputs[index].length > 0) {
                            this.addInput();
                        }
                    },
                    removeInput(index) {
                        if (this.inputs.length > 1) {
                            this.inputs.splice(index, 1);
                        } else {
                             this.inputs[0] = '';
                        }
                    }
                 }">

                @if ($errors->any())
                    <div class="mb-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-600 dark:text-red-400 px-4 py-3 rounded">
                        @if($errors->has('lookup'))
                            <p class="text-sm">{{ $errors->first('lookup') }}</p>
                        @else
                            <ul class="list-disc list-inside text-sm">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                @endif

                <!-- Toggle Tabs -->
                <div class="flex border-b border-gray-200 dark:border-gray-700 mb-6">
                    <button type="button"
                            @click="mode = 'individual'"
                            :class="{ 'border-violet-500 text-violet-600 dark:text-violet-400': mode === 'individual', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300': mode !== 'individual' }"
                            class="w-1/2 py-4 px-1 text-center border-b-2 font-medium text-sm focus:outline-none transition-colors duration-200">
                        Student ID / Email
                    </button>
                    <button type="button"
                            @click="mode = 'code'"
                            :class="{ 'border-violet-500 text-violet-600 dark:text-violet-400': mode === 'code', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300': mode !== 'code' }"
                            class="w-1/2 py-4 px-1 text-center border-b-2 font-medium text-sm focus:outline-none transition-colors duration-200">
                        Payment Code
                    </button>
                </div>

                <form method="POST" action="{{ route('payments.public.lookup.post') }}" class="space-y-6">
                    @csrf

                    <!-- Individual/Multiple Student Search -->
                    <div x-show="mode === 'individual'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0">
                        <div class="space-y-4">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Student IDs or Emails
                            </label>

                            <template x-for="(input, index) in inputs" :key="index">
                                <div class="relative flex items-center">
                                    <input type="text"
                                           name="identifiers[]"
                                           x-model="inputs[index]"
                                           @input="checkAndAdd(index)"
                                           placeholder="Enter Student ID or Email"
                                           class="appearance-none block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-violet-500 focus:border-violet-500 dark:bg-gray-700 dark:text-white sm:text-sm pr-10">

                                    <!-- Clear/Remove Button -->
                                    <button type="button"
                                            @click="removeInput(index)"
                                            class="absolute right-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 focus:outline-none"
                                            x-show="inputs.length > 1 || inputs[0].length > 0">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    </button>
                                </div>
                            </template>

                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                Start typing to add another student.
                            </p>
                        </div>
                    </div>

                    <!-- Payment Code Search -->
                    <div x-show="mode === 'code'" style="display: none;" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0">
                        <div>
                            <label for="payment_code" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Group / Payment Code
                            </label>
                            <div class="mt-1">
                                <input id="payment_code"
                                       name="payment_code"
                                       type="text"
                                       placeholder="Enter the code provided to you"
                                       class="appearance-none block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-violet-500 focus:border-violet-500 dark:bg-gray-700 dark:text-white sm:text-sm">
                            </div>
                        </div>
                    </div>

                    <!-- Info Box -->
                    <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-md p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-blue-700 dark:text-blue-300">
                                    <span x-show="mode === 'individual'">
                                        You can pay for multiple students at once. Provide their Student IDs or registered email addresses.
                                    </span>
                                    <span x-show="mode === 'code'" style="display: none;">
                                        Use the code provided by the school to pay for a specific group or event.
                                    </span>
                                </p>
                            </div>
                        </div>
                    </div>

                    <div>
                        <button type="submit"
                                class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-violet-600 hover:bg-violet-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-violet-500">
                            Continue to Payment
                        </button>
                    </div>
                </form>
            </div>

            <div class="mt-6">
                <div class="relative">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-gray-300 dark:border-gray-600"></div>
                    </div>
                    <div class="relative flex justify-center text-sm">
                        <span class="px-2 bg-gray-50 dark:bg-gray-900 text-gray-500 dark:text-gray-400">
                            Need help?
                        </span>
                    </div>
                </div>

                <div class="mt-6 text-center space-y-2">
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        Contact your school administration if you need assistance
                    </p>
                </div>
            </div>
        </div>
    </div>
    </body>
    </html>
</x-layouts.guest>
