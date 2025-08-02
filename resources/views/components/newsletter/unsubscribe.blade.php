<x-layouts.guest>
    <div class="min-h-screen bg-gray-50 flex flex-col justify-center py-12 sm:px-6 lg:px-8">
        <div class="sm:mx-auto sm:w-full sm:max-w-md">
            <div class="text-center">
                <x-logo class="mx-auto h-12 w-auto" />
                <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                    Newsletter Unsubscription
                </h2>
            </div>
        </div>

        <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
            <div class="bg-white py-8 px-4 shadow sm:rounded-lg sm:px-10">
                @if($success)
                    <div class="text-center">
                        <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100">
                            <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                        <h3 class="mt-4 text-lg font-medium text-gray-900">Successfully Unsubscribed</h3>
                        <p class="mt-2 text-sm text-gray-500">
                            You have been successfully unsubscribed from our newsletter. We're sorry to see you go!
                        </p>
                        <p class="mt-4 text-sm text-gray-500">
                            If you change your mind, you can always subscribe again from our website.
                        </p>
                    </div>
                @else
                    <div class="text-center">
                        <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                            <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </div>
                        <h3 class="mt-4 text-lg font-medium text-gray-900">Unsubscribe Failed</h3>
                        <p class="mt-2 text-sm text-gray-500">
                            We couldn't find a subscription with this token, or it may have already been unsubscribed.
                        </p>
                    </div>
                @endif

                <div class="mt-6">
                    <a href="{{ route('home') }}"
                       class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                        Return to Homepage
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-layouts.guest>
