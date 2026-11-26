<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Join {{ $session->title }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-gray-50 dark:bg-gray-900">
    <main class="flex min-h-screen items-center justify-center px-4 py-12">
        <div class="w-full max-w-lg rounded-lg bg-white p-8 shadow dark:bg-gray-800">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $session->title }}</h1>

            @if($session->description)
                <p class="mt-3 text-gray-600 dark:text-gray-300">{{ $session->description }}</p>
            @endif

            <p class="mt-4 text-sm text-gray-500 dark:text-gray-400">
                {{ $session->scheduled_start->format('l, F j, Y \a\t g:i A') }}
            </p>

            @error('session')
                <p class="mt-4 text-sm text-red-600">{{ $message }}</p>
            @enderror

            @if(! $session->allow_guest_login)
                <p class="mt-6 rounded-lg bg-red-50 p-4 text-sm text-red-700 dark:bg-red-900/20 dark:text-red-300">
                    Guest access is disabled for this session. Please contact the session host.
                </p>
            @elseif($session->isLive())
                <form method="POST" action="{{ route('classroom.join.submit', $joinToken) }}" class="mt-6 space-y-4">
                    @csrf
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Your name</label>
                        <input id="name" name="name" type="text" value="{{ old('name', auth()->user()?->name) }}" required maxlength="255" autofocus
                               class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-violet-500 focus:ring-violet-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <button type="submit" class="w-full rounded-lg bg-violet-600 px-4 py-2 font-medium text-white hover:bg-violet-700">
                        Join session
                    </button>
                </form>
            @else
                <p class="mt-6 rounded-lg bg-blue-50 p-4 text-sm text-blue-700 dark:bg-blue-900/20 dark:text-blue-300">
                    This session is not live yet. Please return to this link when the host starts the session.
                </p>
            @endif
        </div>
    </main>
</body>
</html>