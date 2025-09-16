<x-center>
    <x-logo>Forgot Password</x-logo>

    <!-- Descriptive text -->
    <div class="text-center text-gray-600 text-sm mb-6 mt-3 max-w-md">
        <p>Enter your email address and we'll send you a link to reset your password.</p>
    </div>

    @if(session('status'))
        <div class="text-green-700 text-sm bg-green-50 p-4 border border-green-200 rounded-lg flex space-x-3 mb-6">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 flex-none text-green-600">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <div>
                <p class="font-medium">Email sent successfully!</p>
                <p class="text-green-600 text-xs mt-1">{{ session('status') }}</p>
            </div>
        </div>
    @endif

    @if($errors->any())
        <div class="text-red-700 text-sm bg-red-50 p-4 border border-red-200 rounded-lg flex space-x-3 mb-6">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 flex-none text-red-600">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
            </svg>
            <div>
                <p class="font-medium">Unable to send reset link</p>
                @foreach($errors->all() as $error)
                    <p class="text-red-600 text-xs mt-1">{{ $error }}</p>
                @endforeach
            </div>
        </div>
    @endif

    <form class="space-y-4 w-full max-w-sm" method="POST" action="{{ route('password.email') }}">
        @csrf

        <div class="space-y-2">

            <x-form.input
                name="email"
                type="email"
                placeholder="Enter your email address"
                class="w-full"
                required
                autofocus
            />
        </div>

        <x-button.primary class="w-full justify-center py-3" type="submit">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 mr-2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" />
            </svg>
            Send Reset Link
        </x-button.primary>
    </form>

    <!-- Back to login link -->
    <div class="mt-6 text-center">
        <a href="{{ route('sign-in') }}" class="text-sm text-primary-600 hover:text-primary-500 font-medium">
            ← Back to Sign In
        </a>
    </div>

    <!-- Additional help text -->
    <div class="mt-8 text-center text-xs text-gray-500 max-w-md">
        <p>Didn't receive an email? Check your spam folder or contact support if you continue to have issues.</p>
    </div>
</x-center>
