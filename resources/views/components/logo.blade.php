<div class="flex items-center justify-center space-x-3">
    <a href="{{ route('dashboard') }}">
        <img src="{{ asset('img/logo.png') }}" alt="{{ config('app.name') }} Logo" class="w-8 h-8">
    </a>
    <div class="h-3 border-l border-gray-300"></div>
    <div class="font-semibold text-xl text-gray-800 tracking-wide">{{ $slot }}</div>
</div>