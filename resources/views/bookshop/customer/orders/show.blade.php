<x-bookshop::layouts.customer :title="$order->order_number . ' - BookShop'">
    <div class="flex items-center justify-between">
        <h1 class="text-xl font-bold text-slate-900 dark:text-white" style="font-family: 'Georgia', serif;">{{ $order->order_number }}</h1>
        <span class="text-xs font-semibold px-3 py-1 border" style="border-radius: 2px;">{{ $order->status->label() }}</span>
    </div>

    <p class="text-sm text-slate-500 dark:text-slate-400">
        Placed {{ $order->created_at->format('M d, Y \a\t h:i A') }} &middot;
        Served by {{ $order->branch?->name ?? 'Unassigned' }}
    </p>

    @if($order->status->value === 'cancelled' && $order->cancelled_reason)
        <div class="px-4 py-3 text-sm text-red-700 bg-red-50 border border-red-200 dark:bg-red-900/20 dark:border-red-800 dark:text-red-300" style="border-radius: 2px;">
            Cancelled: {{ $order->cancelled_reason }}
        </div>
    @endif

    <div class="bg-white dark:bg-slate-900 overflow-hidden" style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 1px 6px rgba(0,0,0,0.04);">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 dark:bg-slate-800/50 text-xs uppercase tracking-wider text-slate-500 dark:text-slate-400">
                <tr>
                    <th class="text-left px-5 py-3">Book</th>
                    <th class="text-left px-5 py-3">Unit Price</th>
                    <th class="text-left px-5 py-3">Qty</th>
                    <th class="text-left px-5 py-3">Line Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->items as $item)
                    <tr class="border-t border-slate-100 dark:border-slate-800">
                        <td class="px-5 py-3">
                            <p class="font-semibold text-slate-900 dark:text-white">{{ $item->title_snapshot }}</p>
                            <p class="text-xs text-slate-500 dark:text-slate-400">{{ $item->author_snapshot }}</p>
                        </td>
                        <td class="px-5 py-3 font-mono text-slate-600 dark:text-slate-400">GHS {{ number_format($item->unit_price, 2) }}</td>
                        <td class="px-5 py-3 text-slate-600 dark:text-slate-400">{{ $item->quantity }}</td>
                        <td class="px-5 py-3 font-mono text-slate-600 dark:text-slate-400">GHS {{ number_format($item->line_total, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="border-t border-slate-200 dark:border-slate-700">
                    <td colspan="3" class="px-5 py-3 text-right font-semibold text-slate-900 dark:text-white">Total</td>
                    <td class="px-5 py-3 font-mono font-semibold text-slate-900 dark:text-white">GHS {{ number_format($order->subtotal, 2) }}</td>
                </tr>
            </tfoot>
        </table>
    </div>
</x-bookshop::layouts.customer>
