<x-center>
    <div class="space-y-3">
        <x-alert.success />
        <div class="flex justify-center">
            <div class="bg-primary-200 rounded-full p-3">
                <svg class="w-8 h-8 text-primary-700" fill="none" viewBox="0 0 24 24">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4.75 7.75C4.75 6.64543 5.64543 5.75 6.75 5.75H17.25C18.3546 5.75 19.25 6.64543 19.25 7.75V16.25C19.25 17.3546 18.3546 18.25 17.25 18.25H6.75C5.64543 18.25 4.75 17.3546 4.75 16.25V7.75Z"></path>
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5.5 6.5L12 12.25L18.5 6.5"></path>
                </svg>
            </div>
        </div>
        <div class="text-center text-gray-700">
            <p class="font-medium">Verify your email</p>
            <p class="mt-2 text-sm">We have sent you an email, check and verify to continue.</p>
        </div>
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <x-button.primary class="w-full justify-center">Resend Email</x-button.primary>
        </form>
        <div class="flex justify-center">
            <a class="text-primary-600 text-sm text-center hover:text-gray-700" href="{{ route('dashboard') }}">Already verified?</a>
        </div>
    </div>
</x-center>