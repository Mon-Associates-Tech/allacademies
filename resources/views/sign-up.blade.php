<x-center>
    <x-logo>Sign Up</x-logo>
    <form method="POST" action="{{ route('sign-up') }}" class="space-y-3">
        @csrf
        <x-form.input name="name" type="text" />
        <x-form.input name="email" type="email" />
        <x-form.password name="password" />
        <x-form.password name="password_confirmation" label="Confirm Password" />
        <x-button.primary class="w-full justify-center">Sign Up</x-button.primary>
    </form>
    <a href="{{ route('sign-in') }}" class="block w-full mt-5 text-center text-primary-600 text-sm">Already have an account? Sign In</a>
</x-center>