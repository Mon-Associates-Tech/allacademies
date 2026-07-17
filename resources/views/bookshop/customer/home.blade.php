<x-bookshop::layouts.customer :title="'BookShop'">
    <div class="overflow-hidden" style="border-radius: 2px; background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); box-shadow: 0 4px 24px rgba(0,0,0,0.15);">
        <div class="h-1 w-full" style="background: linear-gradient(90deg, #7c3aed, #a78bfa, #c4b5fd);"></div>
        <div class="px-7 py-6">
            <h1 class="text-2xl font-bold text-white leading-snug" style="letter-spacing: -0.02em; font-family: 'Georgia', serif;">
                Hi, {{ $customer->name }}
            </h1>
            <p class="text-slate-400 mt-2 text-sm">{{ $customer->city }}, {{ $customer->region }}</p>
        </div>
    </div>

    <div class="grid sm:grid-cols-2 gap-6">
        <a href="{{ route('bookshop.shop.catalog') }}" class="bg-white dark:bg-slate-900 p-6 block hover:shadow-md transition-shadow"
           style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06);">
            <h2 class="font-bold text-slate-900 dark:text-white">Browse Catalog</h2>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Order books from your nearest branch.</p>
        </a>
        <a href="{{ route('bookshop.shop.orders.index') }}" class="bg-white dark:bg-slate-900 p-6 block hover:shadow-md transition-shadow"
           style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06);">
            <h2 class="font-bold text-slate-900 dark:text-white">My Orders</h2>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Track the status of what you've ordered.</p>
        </a>
    </div>
</x-bookshop::layouts.customer>
