<x-bookshop::layouts.customer :title="$bulkOrderRequest->request_number . ' - BookShop'">
    <a href="{{ route('bookshop.shop.bulk-orders.index') }}" class="inline-flex items-center gap-1 text-sm text-slate-500 dark:text-slate-400">
        &larr; My Bulk Requests
    </a>

    <div class="flex items-center justify-between">
        <h1 class="text-xl font-bold text-slate-900 dark:text-white" style="font-family: 'Georgia', serif;">{{ $bulkOrderRequest->request_number }}</h1>
        <span class="text-xs font-semibold px-3 py-1 border" style="border-radius: 2px;">{{ $bulkOrderRequest->status->label() }}</span>
    </div>

    <p class="text-sm text-slate-500 dark:text-slate-400">
        For {{ $bulkOrderRequest->institution_name }} ({{ ucfirst($bulkOrderRequest->institution_type) }}) &middot;
        Submitted {{ $bulkOrderRequest->created_at->format('M d, Y') }} &middot;
        Served by {{ $bulkOrderRequest->branch?->name }}
    </p>

    @if($bulkOrderRequest->status->value === 'rejected' && $bulkOrderRequest->rejection_reason)
        <div class="px-4 py-3 text-sm text-red-700 bg-red-50 border border-red-200 dark:bg-red-900/20 dark:border-red-800 dark:text-red-300" style="border-radius: 2px;">
            <strong>Not fulfilled:</strong> {{ $bulkOrderRequest->rejection_reason }}
        </div>
    @endif

    @if($bulkOrderRequest->status->value === 'converted' && $bulkOrderRequest->order)
        <div class="px-4 py-3 text-sm text-emerald-800 bg-emerald-50 border border-emerald-200 dark:text-emerald-200 dark:bg-emerald-900/30 dark:border-emerald-800" style="border-radius: 2px;">
            This request became <a href="{{ route('bookshop.shop.orders.show', $bulkOrderRequest->order) }}" class="underline font-semibold">order {{ $bulkOrderRequest->order->order_number }}</a>.
        </div>
    @endif

    @if($bulkOrderRequest->status->value === 'quoted' && $bulkOrderRequest->staff_notes)
        <div class="px-4 py-3 text-sm text-slate-700 dark:text-slate-300 bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700" style="border-radius: 2px;">
            <strong>Note from the branch:</strong> {{ $bulkOrderRequest->staff_notes }}
        </div>
    @endif

    <div class="bg-white dark:bg-slate-900 overflow-hidden" style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 1px 6px rgba(0,0,0,0.04);">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 dark:bg-slate-800/50 text-xs uppercase tracking-wider text-slate-500 dark:text-slate-400">
                <tr>
                    <th class="text-left px-5 py-3">Book</th>
                    <th class="text-left px-5 py-3">Requested</th>
                    <th class="text-left px-5 py-3">Quote</th>
                </tr>
            </thead>
            <tbody>
                @foreach($bulkOrderRequest->items as $item)
                    <tr class="border-t border-slate-100 dark:border-slate-800">
                        <td class="px-5 py-3 font-semibold text-slate-900 dark:text-white">{{ $item->title_snapshot }}</td>
                        <td class="px-5 py-3 text-slate-600 dark:text-slate-400">{{ $item->requested_quantity }}</td>
                        <td class="px-5 py-3 font-mono text-slate-600 dark:text-slate-400">
                            @if($item->isQuoted())
                                {{ $item->quoted_quantity ?? $item->requested_quantity }} &times; GHS {{ number_format($item->quoted_unit_price, 2) }}
                                = GHS {{ number_format($item->quotedLineTotal(), 2) }}
                            @else
                                <span class="text-slate-400 dark:text-slate-500">Not quoted</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
            @if($bulkOrderRequest->isQuoted())
                <tfoot>
                    <tr class="border-t border-slate-200 dark:border-slate-700">
                        <td colspan="2" class="px-5 py-3 text-right font-semibold text-slate-900 dark:text-white">Total</td>
                        <td class="px-5 py-3 font-mono font-semibold text-slate-900 dark:text-white">
                            GHS {{ number_format($bulkOrderRequest->items->whereNotNull('quoted_unit_price')->sum(fn($i) => $i->quotedLineTotal()), 2) }}
                        </td>
                    </tr>
                </tfoot>
            @endif
        </table>
    </div>

    <div class="flex gap-3">
        @if($bulkOrderRequest->isQuoted())
            <form method="POST" action="{{ route('bookshop.shop.bulk-orders.accept', $bulkOrderRequest) }}">
                @csrf
                <button type="submit"
                        class="inline-flex items-center gap-2 px-6 py-2.5 text-sm font-semibold text-white transition-all"
                        style="border-radius: 2px; background: linear-gradient(135deg, #7c3aed, #a78bfa); box-shadow: 0 2px 10px rgba(124,58,237,0.3);">
                    Accept Quote &amp; Pay
                </button>
            </form>
        @endif
        @if(! $bulkOrderRequest->status->isTerminal())
            <form method="POST" action="{{ route('bookshop.shop.bulk-orders.cancel', $bulkOrderRequest) }}" onsubmit="return confirm('Cancel this bulk request?');">
                @csrf
                <button type="submit" class="px-6 py-2.5 text-sm font-semibold text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-700" style="border-radius: 2px;">
                    Cancel Request
                </button>
            </form>
        @endif
    </div>
</x-bookshop::layouts.customer>
