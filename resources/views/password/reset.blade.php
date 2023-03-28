<x-app>
    <div class="w-full mx-auto max-w-screen-sm py-10">
        @if(session('status'))
            <div class="bg-green-400 py-3 px-5 rounded">{{ session('status') }}</div>
        @endif
        <form class="space-y-4" method="POST" action="{{ route('password.update') }}">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">
            <div>
                <label class="block">Email</label>
                <input class="w-full" type="email" name="email" value="{{ old('email', $email) }}">
                @error('email')
                <div class="text-xs text-red-500">{{ $message }}</div>
                @enderror
            </div>
            <div>
                <label class="block">Password</label>
                <input class="w-full" type="password" name="password">
                @error('password')
                <div class="text-xs text-red-500">{{ $message }}</div>
                @enderror
            </div>
            <div class="flex justify-between items-center">
                <button class="bg-gray-800 text-white py-3 px-4">Submit</button>
            </div>
        </form>
    </div>
</x-app>