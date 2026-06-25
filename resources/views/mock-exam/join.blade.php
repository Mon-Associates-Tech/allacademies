<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Join Examination</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full bg-slate-50 dark:bg-slate-950 flex items-center justify-center p-4">

<div class="w-full max-w-md" x-data="joinForm()">

    {{-- Logo / brand mark --}}
    <div class="text-center mb-8">
        <div class="inline-flex items-center justify-center w-14 h-14 bg-gradient-to-br from-violet-600 to-violet-400 rounded-[2px] shadow-lg shadow-violet-500/30 mb-4">
            <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
        </div>
        <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Join Examination</h1>
        <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Enter the access code provided by your instructor</p>
    </div>

    {{-- Errors --}}
    @if($errors->any())
        <div class="mb-4 px-4 py-3 rounded-[2px] bg-red-50 border border-red-200 text-red-700 text-sm dark:bg-red-900/20 dark:border-red-800 dark:text-red-400">
            @foreach($errors->all() as $e) <p>{{ $e }}</p> @endforeach
        </div>
    @endif

    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-[2px] shadow-sm overflow-hidden">
        <form method="POST" action="{{ route('mock-exams.take.authenticate') }}" class="p-6 space-y-4">
            @csrf

            {{-- Access code --}}
            <div class="space-y-2">
                <label class="block text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                    Access Code <span class="text-red-500">*</span>
                </label>
                <input type="text" name="access_code"
                       value="{{ old('access_code') }}"
                       x-model="accessCode"
                       @input="accessCode = $event.target.value.toUpperCase()"
                       maxlength="8" required autofocus autocomplete="off"
                       class="w-full px-4 py-3 text-center text-2xl font-mono font-bold tracking-[0.5em] border border-slate-200 dark:border-slate-700 rounded-none focus:outline-none focus:ring-2 focus:ring-violet-500/20 focus:border-violet-500 dark:bg-slate-800 dark:text-white uppercase"
                       placeholder="XXXXXX">
            </div>

            {{-- Name --}}
            <div class="space-y-2">
                <label class="block text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                    Your Full Name <span class="text-red-500">*</span>
                </label>
                <input type="text" name="name" value="{{ old('name') }}" required
                       class="w-full px-4 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-none focus:outline-none focus:ring-2 focus:ring-violet-500/20 focus:border-violet-500 dark:bg-slate-800 dark:text-white"
                       placeholder="e.g. Kofi Mensah">
            </div>

            {{-- Email --}}
            <div class="space-y-2">
                <label class="block text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                    Email Address
                </label>
                <input type="email" name="email" value="{{ old('email') }}"
                       class="w-full px-4 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-none focus:outline-none focus:ring-2 focus:ring-violet-500/20 focus:border-violet-500 dark:bg-slate-800 dark:text-white"
                       placeholder="your@email.com">
            </div>

            {{-- Unique code (shown when instructor uses configured mode) --}}
            <div class="space-y-2">
                <label class="block text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                    Unique Candidate Code
                    <span class="text-slate-400 normal-case font-normal">(if provided by instructor)</span>
                </label>
                <input type="text" name="unique_code" value="{{ old('unique_code') }}"
                       class="w-full px-4 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-none focus:outline-none focus:ring-2 focus:ring-violet-500/20 focus:border-violet-500 dark:bg-slate-800 dark:text-white font-mono"
                       placeholder="e.g. STU-001">
            </div>

            <button type="submit"
                    class="w-full px-5 py-3 bg-gradient-to-r from-violet-600 to-violet-500 hover:from-violet-700 hover:to-violet-600 text-white font-semibold rounded-[2px] text-sm shadow-[0_2px_10px_rgba(124,58,237,0.3)] transition-all">
                Enter Examination →
            </button>
        </form>
    </div>

    <p class="text-center text-xs text-slate-400 mt-6">
        Ensure you have a stable internet connection before starting.
    </p>
</div>

<script>
    function joinForm() {
        return { accessCode: '{{ old('access_code', '') }}' };
    }
</script>
</body>
</html>
