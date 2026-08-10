<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'BestBrain' }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    {{-- Standalone module: uses the Tailwind CDN build rather than the host
         app's compiled asset pipeline, so this page renders correctly even
         if BookShop is extracted into its own application. Swap for your
         own @vite directive if you'd rather bundle it with the host app. --}}
</head>
<body class="min-h-screen bg-slate-100 dark:bg-slate-950" style="font-family: 'system-ui', -apple-system, sans-serif;">
    <div class="min-h-screen flex items-center justify-center px-4 py-12">
        <div class="w-full max-w-md">
            <div class="overflow-hidden mb-6"
                 style="border-radius: 2px; background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); box-shadow: 0 4px 24px rgba(0,0,0,0.15);">
                <div class="h-1 w-full" style="background: linear-gradient(90deg, #7c3aed, #a78bfa, #c4b5fd);"></div>
                <div class="px-7 py-6">
                    <h1 class="text-xl font-bold text-white leading-snug" style="letter-spacing: -0.02em; font-family: 'Georgia', serif;">
                        {{ $heading ?? 'BestBrain' }}
                    </h1>
                    @if(isset($subheading))
                        <p class="text-slate-400 mt-2 text-sm">{{ $subheading }}</p>
                    @endif
                </div>
            </div>

            <div class="bg-white dark:bg-slate-900 overflow-hidden"
                 style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 1px 6px rgba(0,0,0,0.04);">
                <div class="p-7">
                    @if(session('status'))
                        <div class="mb-5 px-4 py-3 text-sm text-purple-800 bg-purple-50 border border-purple-200 dark:text-purple-200 dark:bg-purple-900/30 dark:border-purple-800" style="border-radius: 2px;">
                            {{ session('status') }}
                        </div>
                    @endif
                    {{ $slot }}
                </div>
            </div>
        </div>
    </div>
    @stack('scripts')
</body>
</html>
