@php
    /** @var \App\BookShop\Models\Customer|null $customer */
    $customer = auth('bookshop_customer')->user();
    $cartCount = app(\App\BookShop\Services\CartService::class)->count();
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
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex items-center justify-between">
        <div class="flex items-center gap-8">
            <span class="text-white font-bold" style="font-family: 'Georgia', serif; letter-spacing: -0.02em;">BookShop</span>
            <nav class="hidden md:flex items-center gap-5 text-sm">
                <x-bookshop::nav-link route="bookshop.shop.catalog" :active="['bookshop.shop.catalog', 'bookshop.shop.books.show']">Catalog</x-bookshop::nav-link>
                @auth('bookshop_customer')
                    <x-bookshop::nav-link route="bookshop.shop.orders.index" :active="['bookshop.shop.orders.*']">My Orders</x-bookshop::nav-link>
                    <x-bookshop::nav-link route="bookshop.shop.bulk-orders.index" :active="['bookshop.shop.bulk-orders.*']">Bulk Order</x-bookshop::nav-link>
                @endauth
            </nav>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('bookshop.shop.cart.show') }}" class="relative text-slate-300 hover:text-white transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 00-3 3h15.75m-12.75-3h11.218c1.121-2.3 1.994-4.708 2.602-7.202.078-.324-.183-.622-.516-.622H4.756m-1.373 4.16L3.383 4.235M4.756 14.25l-3.5-14.25M4.756 14.25L4.756 14.25" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20.25a.75.75 0 11-1.5 0 .75.75 0 011.5 0zM18.75 20.25a.75.75 0 11-1.5 0 .75.75 0 011.5 0z" />
                </svg>
                @if($cartCount > 0)
                    <span class="absolute -top-1 -right-1 flex items-center justify-center w-4 h-4 text-[10px] font-bold text-white bg-purple-500" style="border-radius: 2px;">
                        {{ $cartCount > 9 ? '9+' : $cartCount }}
                    </span>
                @endif
            </a>

            <div class="hidden md:flex items-center gap-3">
                @auth('bookshop_customer')
                    <x-bookshop::notification-bell guard="bookshop_customer" route-prefix="bookshop.shop." />
                    <form method="POST" action="{{ route('bookshop.shop.logout') }}">
                        @csrf
                        <button type="submit" class="text-sm text-slate-300 hover:text-white transition-colors">Sign Out ({{ $customer?->name }})</button>
                    </form>
                @else
                    <a href="{{ route('bookshop.shop.login') }}" class="text-sm text-slate-300 hover:text-white transition-colors">Log In</a>
                    <a href="{{ route('bookshop.shop.register') }}" class="text-sm font-semibold px-4 py-2 text-white transition-all" style="border-radius: 2px; background: linear-gradient(135deg, #7c3aed, #a78bfa);">
                        Sign Up
                    </a>
                @endauth
            </div>

            <button type="button" id="mobile-nav-toggle" class="md:hidden text-slate-300 hover:text-white" aria-label="Menu">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>
        </div>
    </div>

    <div id="mobile-nav-panel" class="hidden md:hidden border-t border-white/10 px-4 py-3 flex flex-col gap-2">
        <x-bookshop::nav-link route="bookshop.shop.catalog" :active="['bookshop.shop.catalog', 'bookshop.shop.books.show']">Catalog</x-bookshop::nav-link>
        @auth('bookshop_customer')
            <x-bookshop::nav-link route="bookshop.shop.orders.index" :active="['bookshop.shop.orders.*']">My Orders</x-bookshop::nav-link>
            <x-bookshop::nav-link route="bookshop.shop.bulk-orders.index" :active="['bookshop.shop.bulk-orders.*']">Bulk Order</x-bookshop::nav-link>
            <div class="pt-2 border-t border-white/10 mt-1">
                <x-bookshop::notification-bell guard="bookshop_customer" route-prefix="bookshop.shop." />
            </div>
            <form method="POST" action="{{ route('bookshop.shop.logout') }}">
                @csrf
                <button type="submit" class="text-sm text-slate-300 hover:text-white transition-colors py-1">Sign Out ({{ $customer?->name }})</button>
            </form>
        @else
            <a href="{{ route('bookshop.shop.login') }}" class="text-sm text-slate-300 hover:text-white transition-colors py-1">Log In</a>
            <a href="{{ route('bookshop.shop.register') }}" class="text-sm font-semibold text-white py-1">Sign Up</a>
        @endauth
    </div>
</div>

<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">
    @if(session('status'))
        <div class="px-4 py-3 text-sm text-emerald-800 bg-emerald-50 border border-emerald-200 dark:text-emerald-200 dark:bg-emerald-900/30 dark:border-emerald-800" style="border-radius: 2px;">
            {{ session('status') }}
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
