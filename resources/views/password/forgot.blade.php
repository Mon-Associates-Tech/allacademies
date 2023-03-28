<x-center>
    @if(session('status'))
        <div class="bg-green-400 py-3 px-5 rounded">{{ session('status') }}</div>
    @endif
    <x-logo>Forgot Password</x-logo>
    <form class="space-y-3" method="POST" action="{{ route('password.email') }}">
        @csrf
        <x-form.input name="email" type="email" />
        <x-button.primary class="w-full justify-center">Request Reset Link</x-button.primary>
    </form>
</x-center>