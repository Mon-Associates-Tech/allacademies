<x-layouts.app>
    <div class="max-w-xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <h1 class="text-2xl font-semibold mb-2">{{ $exam?->title ?? 'Join Examination' }}</h1>
        <p class="text-sm text-gray-500 mb-6">{{ $exam ? ('Access code: '.$exam->access_code) : 'Enter your examination code to continue.' }}</p>

        @if($errors->any())
            <div class="mb-4 p-3 rounded-lg bg-red-50 border border-red-200 text-red-700">
                {{ $errors->first() }}
            </div>
        @endif

        @if(!$exam)
            <form method="POST" action="{{ route('examination-hub.join.lookup') }}" class="space-y-3">
                @csrf
                <input name="code" placeholder="Exam code" class="w-full px-3 py-2 border rounded-lg uppercase" value="{{ old('code', $code ?? '') }}">
                <button class="w-full px-4 py-2 bg-indigo-600 text-white rounded-lg">Find Exam</button>
            </form>
        @else
            <form method="POST" action="{{ route('examination-hub.join.attempt', $exam->access_code) }}" class="space-y-3">
                @csrf
                <input name="name" placeholder="Full name" class="w-full px-3 py-2 border rounded-lg" value="{{ old('name') }}">
                <input type="email" name="email" placeholder="Email" class="w-full px-3 py-2 border rounded-lg" value="{{ old('email') }}">
                <input name="unique_code" placeholder="Unique code (if configured)" class="w-full px-3 py-2 border rounded-lg" value="{{ old('unique_code') }}">
                <button class="w-full px-4 py-2 bg-indigo-600 text-white rounded-lg">Continue to Exam</button>
            </form>
        @endif
    </div>
</x-layouts.app>
