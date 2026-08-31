<x-bookshop::layouts.staff :title="'Restock Request - BookShop'">
    <a href="{{ route('bookshop.staff.restock-requests.index') }}" class="inline-flex items-center gap-1 text-sm text-slate-500 dark:text-slate-400">
        &larr; Back to Requests
    </a>

    @php($first = $items->first())
    @php($pending = $items->where('status', \App\BookShop\Enums\RestockRequestStatus::PENDING))

    <div class="flex items-center justify-between">
        <h1 class="text-xl font-bold text-slate-900 dark:text-white" style="font-family: 'Georgia', serif;">
            Restock Request &mdash; {{ $first->branch?->name }}
        </h1>
    </div>

    <p class="text-sm text-slate-500 dark:text-slate-400">
        Requested by {{ $first->requestedBy?->name }} &middot;
        {{ $items->count() }} book{{ $items->count() > 1 ? 's' : '' }} &middot;
        Submitted {{ $first->created_at->format('M d, Y \a\t h:i A') }}
    </p>

    @if($first->notes)
        <div class="px-4 py-3 text-sm text-slate-700 dark:text-slate-300 bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700" style="border-radius: 2px;">
            <strong>Note from requester:</strong> {{ $first->notes }}
        </div>
    @endif

    <div class="bg-white dark:bg-slate-900 overflow-hidden" style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 1px 6px rgba(0,0,0,0.04);">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 dark:bg-slate-800/50 text-xs uppercase tracking-wider text-slate-500 dark:text-slate-400">
                <tr>
                    <th class="text-left px-5 py-3">Book</th>
                    <th class="text-left px-5 py-3">Quantity</th>
                    <th class="text-left px-5 py-3">Status</th>
                    <th class="text-right px-5 py-3">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($items as $item)
                    <tr class="border-t border-slate-100 dark:border-slate-800">
                        <td class="px-5 py-3 font-semibold text-slate-900 dark:text-white">{{ $item->book?->title }}</td>
                        <td class="px-5 py-3 font-mono text-slate-600 dark:text-slate-400">
                            {{ $item->requested_quantity }}
                            @if($item->status->value === 'confirmed' && $item->confirmed_quantity !== $item->requested_quantity)
                                <span class="text-xs text-amber-600 dark:text-amber-400">({{ $item->confirmed_quantity }} confirmed)</span>
                            @endif
                        </td>
                        <td class="px-5 py-3">
                            <span class="text-xs font-semibold px-3 py-1 border" style="border-radius: 2px;">{{ $item->status->label() }}</span>
                            @if($item->status->value === 'rejected' && $item->reason)
                                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">{{ $item->reason }}</p>
                            @endif
                            @if($item->status->value === 'dispatched' && $item->dispatchedBy)
                                <p class="text-xs text-slate-400 dark:text-slate-500 mt-1">by {{ $item->dispatchedBy->name }} &middot; {{ $item->dispatched_at?->diffForHumans() }}</p>
                            @elseif($item->status->value === 'delivered' && $item->deliveredBy)
                                <p class="text-xs text-slate-400 dark:text-slate-500 mt-1">by {{ $item->deliveredBy->name }} &middot; {{ $item->delivered_at?->diffForHumans() }}</p>
                            @elseif($item->status->value === 'confirmed' && $item->confirmedBy)
                                <p class="text-xs text-slate-400 dark:text-slate-500 mt-1">by {{ $item->confirmedBy->name }} &middot; {{ $item->confirmed_at?->diffForHumans() }}</p>
                            @elseif($item->reviewedBy)
                                <p class="text-xs text-slate-400 dark:text-slate-500 mt-1">by {{ $item->reviewedBy->name }} &middot; {{ $item->reviewed_at?->diffForHumans() }}</p>
                            @endif
                        </td>
                        <td class="px-5 py-3 text-right space-x-3">
                            @if($item->status->value === 'pending' && $staff->isSuperAdmin())
                                <form method="POST" action="{{ route('bookshop.staff.restock-requests.approve', $item) }}" class="inline">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="text-emerald-600 dark:text-emerald-400 font-medium">Approve</button>
                                </form>
                                <form method="POST" action="{{ route('bookshop.staff.restock-requests.reject', $item) }}" class="inline" onsubmit="return promptReject(this);">
                                    @csrf @method('PATCH')
                                    <input type="hidden" name="reason" class="reject-reason-input">
                                    <button type="submit" class="text-red-500 font-medium">Reject</button>
                                </form>
                            @elseif($item->status->value === 'approved' && $staff->isSuperAdmin())
                                <form method="POST" action="{{ route('bookshop.staff.restock-requests.dispatch', $item) }}" class="inline">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="text-purple-600 dark:text-purple-400 font-medium">Mark Dispatched</button>
                                </form>
                                <form method="POST" action="{{ route('bookshop.staff.restock-requests.reject', $item) }}" class="inline" onsubmit="return promptReject(this);">
                                    @csrf @method('PATCH')
                                    <input type="hidden" name="reason" class="reject-reason-input">
                                    <button type="submit" class="text-red-500 font-medium">Reject</button>
                                </form>
                            @elseif($item->status->value === 'dispatched')
                                <form method="POST" action="{{ route('bookshop.staff.restock-requests.deliver', $item) }}" class="inline">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="text-purple-600 dark:text-purple-400 font-medium">Mark Delivered</button>
                                </form>
                            @elseif($item->status->value === 'delivered')
                                <form method="POST" action="{{ route('bookshop.staff.restock-requests.confirm', $item) }}" class="inline flex items-center justify-end gap-2">
                                    @csrf @method('PATCH')
                                    <input type="number" name="confirmed_quantity" min="0" max="{{ $item->requested_quantity }}" value="{{ $item->requested_quantity }}"
                                           class="w-16 px-2 py-1 text-xs border border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-white" style="border-radius: 2px;">
                                    <button type="submit" class="text-emerald-600 dark:text-emerald-400 font-medium">Confirm Received</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @if($staff->isSuperAdmin() && $pending->count() > 1)
        <div class="bg-white dark:bg-slate-900 p-6" style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 1px 6px rgba(0,0,0,0.04);">
            <h2 class="font-bold text-slate-900 dark:text-white text-xs uppercase tracking-wider mb-3" style="letter-spacing: 0.1em;">Bulk Actions</h2>
            <p class="text-sm text-slate-500 dark:text-slate-400 mb-4">Applies to all {{ $pending->count() }} items still in review in this request.</p>
            <div class="flex gap-3">
                <form method="POST" action="{{ route('bookshop.staff.restock-requests.approve-all', $batchId) }}">
                    @csrf @method('PATCH')
                    <button type="submit" class="px-5 py-2.5 text-sm font-semibold text-white" style="border-radius: 2px; background: linear-gradient(135deg, #7c3aed, #a78bfa);">
                        Approve All Pending
                    </button>
                </form>
                <form method="POST" action="{{ route('bookshop.staff.restock-requests.reject-all', $batchId) }}" onsubmit="return promptReject(this);">
                    @csrf @method('PATCH')
                    <input type="hidden" name="reason" class="reject-reason-input">
                    <button type="submit" class="px-5 py-2.5 text-sm font-semibold text-red-600 border border-red-200 dark:border-red-800" style="border-radius: 2px;">
                        Reject All Pending
                    </button>
                </form>
            </div>
        </div>
    @endif

    <script>
        function promptReject(form) {
            const reason = prompt('Reason for rejecting:');
            if (! reason) return false;
            form.querySelector('.reject-reason-input').value = reason;
            return true;
        }
    </script>
</x-bookshop::layouts.staff>
