<x-layout>
    <form method="POST" action="{{ route('sign-in') }}" class="w-screen h-screen grid place-items-center">
        @csrf
        <div class="space-y-3 w-full max-w-sm">
            <h4 class="text-center text-lg text-primary-800">🔥 {{ config('app.name') }} 🔥</h4>
            <x-form.input full name="email" />
            <x-form.input full name="password" type="password" />
            <x-button full>Sign In</x-button>
            <a class="block text-center text-sm text-primary-800 hover:text-primary-700">Forgot password?</a>
        </div>
    </form>
</x-layout>