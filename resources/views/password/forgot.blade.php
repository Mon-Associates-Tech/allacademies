<x-center>
    @if(session('status'))
        <div class="text-gray-600 text-sm bg-gray-200 p-3 border border-gray-300 rounded-lg flex space-x-3 mb-5">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 flex-none">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" />
            </svg>
            <span>
                {{ session('status') }}
            </span>
        </div>
    @endif

    <x-logo>Forgot Password</x-logo>

    <form class="space-y-3" method="POST" action="{{ route('password.email') }}">
        @csrf
        <x-form.input name="email" type="email" />
        <x-button.primary class="w-full justify-center">Request Reset Link</x-button.primary>
    </form>
</x-center>