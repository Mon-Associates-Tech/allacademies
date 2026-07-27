@php
    /** @var \App\BookShop\Models\Staff $staff */
    $staff = auth('bookshop_staff')->user();
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'BookShop' }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-slate-100 dark:bg-slate-950" style="font-family: 'system-ui', -apple-system, sans-serif;">

<div style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); box-shadow: 0 4px 24px rgba(0,0,0,0.15);">
    <div class="h-1 w-full" style="background: linear-gradient(90deg, #7c3aed, #a78bfa, #c4b5fd);"></div>
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex items-center justify-between">
        <div class="flex items-center gap-8">
            <span class="text-white font-bold" style="font-family: 'Georgia', serif; letter-spacing: -0.02em;">BookShop</span>
            <nav class="hidden md:flex items-center gap-5 text-sm">
                <x-bookshop::nav-link route="bookshop.staff.dashboard">Dashboard</x-bookshop::nav-link>
                <x-bookshop::nav-link route="bookshop.staff.orders.index" :active="['bookshop.staff.orders.*']">Orders</x-bookshop::nav-link>
                <x-bookshop::nav-link route="bookshop.staff.restock-requests.index" :active="['bookshop.staff.restock-requests.*']">Restock</x-bookshop::nav-link>

                <x-bookshop::nav-dropdown label="Catalog" :active="['bookshop.staff.books.*', 'bookshop.staff.stock.*', 'bookshop.staff.warehouse.*', 'bookshop.staff.categories.*']">
                    <x-bookshop::nav-link variant="dropdown" route="bookshop.staff.books.index" :active="['bookshop.staff.books.*']">Books</x-bookshop::nav-link>
                    <x-bookshop::nav-link variant="dropdown" route="bookshop.staff.stock.index" :active="['bookshop.staff.stock.*']">Stock</x-bookshop::nav-link>
                    @if($staff?->isSuperAdmin())
                        <x-bookshop::nav-link variant="dropdown" route="bookshop.staff.warehouse.index" :active="['bookshop.staff.warehouse.*']">Warehouse</x-bookshop::nav-link>
                        <x-bookshop::nav-link variant="dropdown" route="bookshop.staff.categories.index" :active="['bookshop.staff.categories.*']">Categories</x-bookshop::nav-link>
                    @endif
                </x-bookshop::nav-dropdown>

                <x-bookshop::nav-dropdown label="Manage" :active="['bookshop.staff.customers.*', 'bookshop.staff.reports.*', 'bookshop.staff.branches.*', 'bookshop.staff.team.*']">
                    <x-bookshop::nav-link variant="dropdown" route="bookshop.staff.customers.index" :active="['bookshop.staff.customers.*']">Customers</x-bookshop::nav-link>
                    <x-bookshop::nav-link variant="dropdown" route="bookshop.staff.reports.index" :active="['bookshop.staff.reports.*']">Reports</x-bookshop::nav-link>
                    @if($staff?->isSuperAdmin())
                        <x-bookshop::nav-link variant="dropdown" route="bookshop.staff.branches.index" :active="['bookshop.staff.branches.*']">Branches</x-bookshop::nav-link>
                        <x-bookshop::nav-link variant="dropdown" route="bookshop.staff.team.index" :active="['bookshop.staff.team.*']">Team</x-bookshop::nav-link>
                    @endif
                </x-bookshop::nav-dropdown>
            </nav>
        </div>
        <div class="flex items-center gap-3">
            <x-bookshop::notification-bell guard="bookshop_staff" route-prefix="bookshop.staff." />
            <form method="POST" action="{{ route('bookshop.staff.logout') }}" class="hidden md:block">
                @csrf
                <button type="submit" class="text-sm text-slate-300 hover:text-white transition-colors">Sign Out ({{ $staff?->name }})</button>
            </form>
            <button type="button" id="mobile-nav-toggle" class="md:hidden text-slate-300 hover:text-white" aria-label="Menu">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>
        </div>
    </div>

    <div id="mobile-nav-panel" class="hidden md:hidden border-t border-white/10 px-4 py-3 flex flex-col gap-2">
        <x-bookshop::nav-link route="bookshop.staff.dashboard">Dashboard</x-bookshop::nav-link>
        <x-bookshop::nav-link route="bookshop.staff.orders.index" :active="['bookshop.staff.orders.*']">Orders</x-bookshop::nav-link>
        <x-bookshop::nav-link route="bookshop.staff.books.index" :active="['bookshop.staff.books.*']">Books</x-bookshop::nav-link>
        <x-bookshop::nav-link route="bookshop.staff.stock.index" :active="['bookshop.staff.stock.*']">Stock</x-bookshop::nav-link>
        <x-bookshop::nav-link route="bookshop.staff.restock-requests.index" :active="['bookshop.staff.restock-requests.*']">Restock</x-bookshop::nav-link>
        <x-bookshop::nav-link route="bookshop.staff.customers.index" :active="['bookshop.staff.customers.*']">Customers</x-bookshop::nav-link>
        <x-bookshop::nav-link route="bookshop.staff.reports.index" :active="['bookshop.staff.reports.*']">Reports</x-bookshop::nav-link>
        @if($staff?->isSuperAdmin())
            <x-bookshop::nav-link route="bookshop.staff.warehouse.index" :active="['bookshop.staff.warehouse.*']">Warehouse</x-bookshop::nav-link>
            <x-bookshop::nav-link route="bookshop.staff.branches.index" :active="['bookshop.staff.branches.*']">Branches</x-bookshop::nav-link>
            <x-bookshop::nav-link route="bookshop.staff.team.index" :active="['bookshop.staff.team.*']">Team</x-bookshop::nav-link>
            <x-bookshop::nav-link route="bookshop.staff.categories.index" :active="['bookshop.staff.categories.*']">Categories</x-bookshop::nav-link>
        @endif
        <form method="POST" action="{{ route('bookshop.staff.logout') }}" class="pt-2 border-t border-white/10 mt-2">
            @csrf
            <button type="submit" class="text-sm text-slate-300 hover:text-white transition-colors py-1">Sign Out ({{ $staff?->name }})</button>
        </form>
    </div>
</div>

<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">
    @if(session('status'))
        <div class="px-4 py-3 text-sm text-emerald-800 bg-emerald-50 border border-emerald-200 dark:text-emerald-200 dark:bg-emerald-900/30 dark:border-emerald-800" style="border-radius: 2px;">
            {{ session('status') }}
        </div>
    @endif
    @if(session('warning'))
        <div class="px-4 py-3 text-sm text-amber-800 bg-amber-50 border border-amber-200 dark:text-amber-200 dark:bg-amber-900/30 dark:border-amber-800" style="border-radius: 2px;">
            {{ session('warning') }}
        </div>
    @endif
    @if($errors->any())
        <div class="px-4 py-3 text-sm text-red-700 bg-red-50 border border-red-200 dark:bg-red-900/20 dark:border-red-800 dark:text-red-300" style="border-radius: 2px;">
            <ul class="list-disc list-inside space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{ $slot }}
</div>

@stack('scripts')
<script>
    (function () {
        const toggle = document.getElementById('mobile-nav-toggle');
        const panel = document.getElementById('mobile-nav-panel');
        if (!toggle || !panel) return;
        toggle.addEventListener('click', () => panel.classList.toggle('hidden'));
    })();
</script>
</body>
</html>
