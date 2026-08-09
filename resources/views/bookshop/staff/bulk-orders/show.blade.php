<x-bookshop::layouts.staff :title="$bulkOrderRequest->request_number . ' - BookShop'">
    <a href="{{ route('bookshop.staff.bulk-orders.index') }}" class="inline-flex items-center gap-1 text-sm text-slate-500 dark:text-slate-400">
        &larr; Bulk Orders
    </a>

    <div class="flex items-center justify-between">
        <h1 class="text-xl font-bold text-slate-900 dark:text-white" style="font-family: 'Georgia', serif;">{{ $bulkOrderRequest->request_number }}</h1>
        <span class="text-xs font-semibold px-3 py-1 border" style="border-radius: 2px;">{{ $bulkOrderRequest->status->label() }}</span>
    </div>

    <div class="bg-white dark:bg-slate-900 p-5" style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 1px 6px rgba(0,0,0,0.04);">
        <div class="grid sm:grid-cols-2 gap-4 text-sm">
            <div>
                <p class="text-xs text-slate-500 dark:text-slate-400 uppercase tracking-wider" style="letter-spacing: 0.08em;">Institution</p>
                <p class="text-slate-900 dark:text-white font-semibold">{{ $bulkOrderRequest->institution_name }}</p>
                <p class="text-slate-600 dark:text-slate-400">{{ ucfirst($bulkOrderRequest->institution_type) }}</p>
            </div>
            <div>
                <p class="text-xs text-slate-500 dark:text-slate-400 uppercase tracking-wider" style="letter-spacing: 0.08em;">Contact</p>
                <p class="text-slate-900 dark:text-white font-semibold">{{ $bulkOrderRequest->customer?->name }}</p>
                <p class="text-slate-600 dark:text-slate-400">{{ $bulkOrderRequest->customer?->email }} @if($bulkOrderRequest->contact_phone) &middot; {{ $bulkOrderRequest->contact_phone }} @endif</p>
            </div>
            @if($bulkOrderRequest->requested_delivery_date)
                <div>
                    <p class="text-xs text-slate-500 dark:text-slate-400 uppercase tracking-wider" style="letter-spacing: 0.08em;">Needed By</p>
                    <p class="text-slate-900 dark:text-white">{{ $bulkOrderRequest->requested_delivery_date->format('M d, Y') }}</p>
                </div>
            @endif
            @if($bulkOrderRequest->notes)
                <div>
                    <p class="text-xs text-slate-500 dark:text-slate-400 uppercase tracking-wider" style="letter-spacing: 0.08em;">Customer's Note</p>
                    <p class="text-slate-600 dark:text-slate-400">{{ $bulkOrderRequest->notes }}</p>
                </div>
            @endif
        </div>
    </div>

    @if($bulkOrderRequest->status->value === 'rejected' && $bulkOrderRequest->rejection_reason)
        <div class="px-4 py-3 text-sm text-red-700 bg-red-50 border border-red-200 dark:bg-red-900/20 dark:border-red-800 dark:text-red-300" style="border-radius: 2px;">
            Rejected: {{ $bulkOrderRequest->rejection_reason }}
        </div>
    @endif

    @if($bulkOrderRequest->status->value === 'converted' && $bulkOrderRequest->order)
        <div class="px-4 py-3 text-sm text-emerald-800 bg-emerald-50 border border-emerald-200 dark:text-emerald-200 dark:bg-emerald-900/30 dark:border-emerald-800" style="border-radius: 2px;">
            Converted to <a href="{{ route('bookshop.staff.orders.show', $bulkOrderRequest->order) }}" class="underline font-semibold">order {{ $bulkOrderRequest->order->order_number }}</a>.
        </div>
    @endif

    @if($bulkOrderRequest->isPending())
        <form method="POST" action="{{ route('bookshop.staff.bulk-orders.quote', $bulkOrderRequest) }}">
            @csrf @method('PATCH')
            <div class="bg-white dark:bg-slate-900 overflow-hidden" style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 1px 6px rgba(0,0,0,0.04);">
                <table class="w-full text-sm">
                    <thead class="bg-slate-50 dark:bg-slate-800/50 text-xs uppercase tracking-wider text-slate-500 dark:text-slate-400">
                        <tr>
                            <th class="text-left px-5 py-3">Book</th>
                            <th class="text-left px-5 py-3">Requested</th>
                            <th class="text-left px-5 py-3">Quote Quantity</th>
                            <th class="text-left px-5 py-3">Unit Price (GHS)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($bulkOrderRequest->items as $item)
                            <tr class="border-t border-slate-100 dark:border-slate-800">
                                <td class="px-5 py-3">
                                    <p class="font-semibold text-slate-900 dark:text-white">{{ $item->title_snapshot }}</p>
                                    @if($item->book)
                                        <p class="text-xs text-slate-500 dark:text-slate-400">Catalog price: GHS {{ number_format($item->book->price, 2) }}</p>
                                    @endif
                                </td>
                                <td class="px-5 py-3 text-slate-600 dark:text-slate-400">{{ $item->requested_quantity }}</td>
                                <td class="px-5 py-3">
                                    <input type="number" name="items[{{ $item->id }}][quantity]" min="1" max="{{ $item->requested_quantity }}" value="{{ $item->requested_quantity }}"
                                           class="w-24 px-2 py-1.5 text-sm border border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-white" style="border-radius: 2px;">
                                </td>
                                <td class="px-5 py-3">
                                    <input type="number" step="0.01" min="0" name="items[{{ $item->id }}][unit_price]" value="{{ old('items.'.$item->id.'.unit_price', $item->book?->price) }}"
                                           placeholder="Leave blank to decline this item"
                                           class="w-32 px-2 py-1.5 text-sm border border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-white" style="border-radius: 2px;">
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="bg-white dark:bg-slate-900 p-5 mt-4" style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 1px 6px rgba(0,0,0,0.04);">
                <label class="block text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2" style="letter-spacing: 0.08em;">Note to customer (optional)</label>
                <textarea name="staff_notes" rows="2" class="w-full px-4 py-2.5 text-sm border border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-white" style="border-radius: 2px;">{{ old('staff_notes') }}</textarea>

                <div class="flex gap-3 mt-4">
                    <button type="submit" class="px-6 py-2.5 text-sm font-semibold text-white" style="border-radius: 2px; background: linear-gradient(135deg, #7c3aed, #a78bfa);">
                        Send Quote
                    </button>
                </div>
                <p class="mt-2 text-xs text-slate-500 dark:text-slate-400">Leave an item's unit price blank to decline just that item — the rest of the quote still goes out.</p>
            </div>
        </form>

        <form method="POST" action="{{ route('bookshop.staff.bulk-orders.reject', $bulkOrderRequest) }}" onsubmit="return promptReject(this);">
            @csrf @method('PATCH')
            <input type="hidden" name="reason" class="reject-reason-input">
            <button type="submit" class="px-6 py-2.5 text-sm font-semibold text-red-600 border border-red-200 dark:border-red-800" style="border-radius: 2px;">
                Reject Entire Request
            </button>
        </form>

        <script>
            function promptReject(form) {
                const reason = prompt('Reason for rejecting this request:');
                if (! reason) return false;
                form.querySelector('.reject-reason-input').value = reason;
                return true;
            }
        </script>
    @else
        <div class="bg-white dark:bg-slate-900 overflow-hidden" style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 1px 6px rgba(0,0,0,0.04);">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 dark:bg-slate-800/50 text-xs uppercase tracking-wider text-slate-500 dark:text-slate-400">
                    <tr>
                        <th class="text-left px-5 py-3">Book</th>
                        <th class="text-left px-5 py-3">Requested</th>
                        <th class="text-left px-5 py-3">Quoted</th>
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
                                @else
                                    <span class="text-slate-400 dark:text-slate-500">Declined</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</x-bookshop::layouts.staff>
