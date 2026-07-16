<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>BookShop Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-slate-100 dark:bg-slate-950" style="font-family: 'system-ui', -apple-system, sans-serif;">
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">

    <div class="overflow-hidden"
         style="border-radius: 2px; background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); box-shadow: 0 4px 24px rgba(0,0,0,0.15);">
        <div class="h-1 w-full" style="background: linear-gradient(90deg, #7c3aed, #a78bfa, #c4b5fd);"></div>
        <div class="px-7 py-6 flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-white leading-snug" style="letter-spacing: -0.02em; font-family: 'Georgia', serif;">
                    Welcome, {{ $staff->name }}
                </h1>
                <p class="text-slate-400 mt-2 text-sm">
                    {{ $staff->isSuperAdmin() ? 'Super Admin \u2014 all branches' : 'Branch Admin' }}
                </p>
            </div>
            <form method="POST" action="{{ route('bookshop.staff.logout') }}">
                @csrf
                <button type="submit" class="text-sm text-slate-300 hover:text-white transition-colors">Sign Out</button>
            </form>
        </div>
    </div>

    <div class="bg-white dark:bg-slate-900 p-6"
         style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 1px 6px rgba(0,0,0,0.04);">
        <h2 class="font-bold text-slate-900 dark:text-white text-xs uppercase tracking-wider mb-4" style="letter-spacing: 0.1em;">
            {{ $staff->isSuperAdmin() ? 'All Branches' : 'Your Branch' }}
        </h2>

        @forelse($branches as $branch)
            <div class="flex items-center justify-between py-3 border-b border-slate-100 dark:border-slate-800 last:border-0">
                <div>
                    <p class="font-semibold text-slate-900 dark:text-white">{{ $branch->name }}</p>
                    <p class="text-xs text-slate-500 dark:text-slate-400">{{ $branch->city }}, {{ $branch->region }} &middot; {{ $branch->code }}</p>
                </div>
                <span class="text-xs font-semibold px-3 py-1 border" style="border-radius: 2px;">
                    {{ $branch->is_active ? 'Active' : 'Inactive' }}
                </span>
            </div>
        @empty
            <p class="text-sm text-slate-500 dark:text-slate-400">No branches yet. Branch management, inventory, and orders land in the next phases.</p>
        @endforelse
    </div>

</div>
</body>
</html>
