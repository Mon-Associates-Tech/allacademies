<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>BookShop</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-slate-100 dark:bg-slate-950" style="font-family: 'system-ui', -apple-system, sans-serif;">
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">

    <div class="overflow-hidden"
         style="border-radius: 2px; background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); box-shadow: 0 4px 24px rgba(0,0,0,0.15);">
        <div class="h-1 w-full" style="background: linear-gradient(90deg, #7c3aed, #a78bfa, #c4b5fd);"></div>
        <div class="px-7 py-6 flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-white leading-snug" style="letter-spacing: -0.02em; font-family: 'Georgia', serif;">
                    Hi, {{ $customer->name }}
                </h1>
                <p class="text-slate-400 mt-2 text-sm">{{ $customer->city }}, {{ $customer->region }}</p>
            </div>
            <form method="POST" action="{{ route('bookshop.shop.logout') }}">
                @csrf
                <button type="submit" class="text-sm text-slate-300 hover:text-white transition-colors">Sign Out</button>
            </form>
        </div>
    </div>

    <div class="bg-white dark:bg-slate-900 p-6 text-center"
         style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 1px 6px rgba(0,0,0,0.04);">
        <p class="text-sm text-slate-500 dark:text-slate-400">
            The catalog and ordering flow land in the next phase. Your account is
            ready — you'll be able to browse and order books here shortly.
        </p>
    </div>

</div>
</body>
</html>
