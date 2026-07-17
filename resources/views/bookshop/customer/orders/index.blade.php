<x-bookshop::layouts.customer :title="'My Orders - BookShop'">
    <h1 class="text-xl font-bold text-slate-900 dark:text-white" style="font-family: 'Georgia', serif;">My Orders</h1>

    <div class="bg-white dark:bg-slate-900 overflow-hidden" style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 1px 6px rgba(0,0,0,0.04);">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 dark:bg-slate-800/50 text-xs uppercase tracking-wider text-slate-500 dark:text-slate-400">
                <tr>
                    <th class="text-left px-5 py-3">Order #</th>
                    <th class="text-left px-5 py-3">Branch</th>
                    <th class="text-left px-5 py-3">Items</th>
                    <th class="text-left px-5 py-3">Total</th>
                    <th class="text-left px-5 py-3">Status</th>
                    <th class="text-left px-5 py-3">Placed</th>
                </tr>
            </thead>
            <tbody>
                @forelse($orders as $order)
                    <tr class="border-t border-slate-100 dark:border-slate-800 hover:bg-slate-50 dark:hover:bg-slate-800/40 cursor-pointer"
                        onclick="window.location='{{ route('bookshop.shop.orders.show', $order) }}'">
                        <td class="px-5 py-3 font-mono text-slate-900 dark:text-white">{{ $order->order_number }}</td>
                        <td class="px-5 py-3 text-slate-600 dark:text-slate-400">{{ $order->branch?->name ?? '—' }}</td>
                        <td class="px-5 py-3 text-slate-600 dark:text-slate-400">{{ $order->items->count() }}</td>
                        <td class="px-5 py-3 font-mono text-slate-600 dark:text-slate-400">GHS {{ number_format($order->subtotal, 2) }}</td>
                        <td class="px-5 py-3">
                            <span class="text-xs font-semibold px-3 py-1 border" style="border-radius: 2px;">{{ $order->status->label() }}</span>
                        </td>
                        <td class="px-5 py-3 text-slate-600 dark:text-slate-400">{{ $order->created_at->format('M d, Y') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-5 py-8 text-center text-slate-500 dark:text-slate-400">
                        You haven't placed any orders yet.
                        <a href="{{ route('bookshop.shop.catalog') }}" class="text-purple-600 dark:text-purple-400">Browse the catalog &rarr;</a>
                    </td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($orders->hasPages())
        <div class="bg-white dark:bg-slate-900 px-5 py-4" style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06);">
            {{ $orders->links() }}
        </div>
    @endif
</x-bookshop::layouts.customer>
