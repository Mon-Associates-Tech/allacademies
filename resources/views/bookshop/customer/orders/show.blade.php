<x-bookshop::layouts.customer :title="$order->order_number . ' - BookShop'">
    <div class="flex items-center justify-between">
        <h1 class="text-xl font-bold text-slate-900 dark:text-white" style="font-family: 'Georgia', serif;">{{ $order->order_number }}</h1>
        <div class="flex gap-2">
            <span class="text-xs font-semibold px-3 py-1 border" style="border-radius: 2px;">{{ $order->status->label() }}</span>
            <span class="text-xs font-semibold px-3 py-1 border {{ $order->isPaid() ? 'text-emerald-800 bg-emerald-50 border-emerald-200 dark:text-emerald-200 dark:bg-emerald-900/30 dark:border-emerald-800' : 'text-amber-800 bg-amber-50 border-amber-200 dark:text-amber-200 dark:bg-amber-900/30 dark:border-amber-800' }}" style="border-radius: 2px;">
                {{ $order->payment_status->label() }}
            </span>
        </div>
    </div>

    <p class="text-sm text-slate-500 dark:text-slate-400">
        Placed {{ $order->created_at->format('M d, Y \a\t h:i A') }} &middot;
        Served by {{ $order->branch?->name ?? 'Unassigned' }} &middot;
        {{ $order->fulfillment_method->label() }}
        @if($order->isPaid())
            &middot; <a href="{{ route('bookshop.shop.orders.receipt', $order) }}" target="_blank" class="text-purple-600 dark:text-purple-400 underline">Download Receipt</a>
        @endif
    </p>

    @if($order->isDelivery() && $order->delivery_address)
        <div class="px-4 py-3 text-sm text-slate-700 dark:text-slate-300 bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700" style="border-radius: 2px;">
            <strong>Delivering to:</strong> {{ $order->delivery_address }}
        </div>
    @endif

    @if(! $order->isPaid() && $order->status->value !== 'cancelled')
        <div class="px-4 py-3 text-sm text-amber-800 bg-amber-50 border border-amber-200 dark:text-amber-200 dark:bg-amber-900/30 dark:border-amber-800 flex items-center justify-between gap-3" style="border-radius: 2px;">
            <span>
                @if($order->payment_status->value === 'failed')
                    Your last payment attempt didn't go through. Your items are still reserved — you can retry.
                @else
                    This order isn't paid for yet. Complete payment to have it fulfilled.
                @endif
            </span>
            <a href="{{ route('bookshop.shop.payments.initialize', $order) }}"
               class="flex-shrink-0 text-xs font-semibold px-4 py-2 text-white" style="border-radius: 2px; background: linear-gradient(135deg, #7c3aed, #a78bfa);">
                {{ $order->payment_status->value === 'failed' ? 'Retry Payment' : 'Complete Payment' }}
            </a>
        </div>
    @endif

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
