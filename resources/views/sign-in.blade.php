<x-center>
    <x-logo>Login</x-logo>
    <form method="POST" action="{{ route('sign-in') }}" class="space-y-3">
        @csrf
        <x-form.input name="email" type="email" />
        <x-form.password name="password" />
        <div class="flex items-center justify-between">
            <x-form.checkbox name="remember" description="Remember Me" inline />
            <a class="text-sm text-primary-600 hover:text-primary-500" href="{{ route('password.request') }}">Forgot Password?</a>
        </div>
        <x-button.primary class="w-full justify-center">Log In</x-button.primary>
    </form>
    <a href="{{ route('sign-up') }}" class="block w-full mt-5 text-center text-primary-600 hover:text-primary-500 text-sm">No Account Yet? Sign Up</a>
</x-center>