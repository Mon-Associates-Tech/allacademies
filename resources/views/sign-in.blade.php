<x-center>
    <x-logo>Login</x-logo>
    <form method="POST" action="{{ route('sign-in') }}" class="space-y-3">
        @csrf
        <x-form.input name="email" type="email" />
        <x-form.password name="password" />
        <x-button.primary class="w-full justify-center">Log In</x-button.primary>
        <div class="flex justify-center">
                <a class="text-gray-600 text-sm text-center hover:text-gray-700" href="{{ route('password.request') }}">Forgot Password?</a>
        </div>
    </form>
    <a href="{{ route('sign-up') }}" class="block w-full mt-5 text-center text-primary-600 text-sm">No Account Yet? Sign Up</a>
</x-center>