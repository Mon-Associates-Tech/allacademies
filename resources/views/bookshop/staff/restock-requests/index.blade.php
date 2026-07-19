<x-bookshop::layouts.staff :title="'Restock Requests - BookShop'">
    <div class="flex items-center justify-between">
        <h1 class="text-xl font-bold text-slate-900 dark:text-white" style="font-family: 'Georgia', serif;">
            {{ $staff->isSuperAdmin() ? 'Restock Requests — All Branches' : 'Restock Requests — ' . $staff->branch?->name }}
        </h1>
        @if(! $staff->isSuperAdmin())
            <a href="{{ route('bookshop.staff.restock-requests.create') }}"
               class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-semibold text-white transition-all"
               style="border-radius: 2px; background: linear-gradient(135deg, #7c3aed, #a78bfa); box-shadow: 0 2px 10px rgba(124,58,237,0.3);">
                + New Request
            </a>
        @endif
    </div>

    <div class="bg-white dark:bg-slate-900 overflow-hidden" style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 1px 6px rgba(0,0,0,0.04);">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 dark:bg-slate-800/50 text-xs uppercase tracking-wider text-slate-500 dark:text-slate-400">
                <tr>
                    @if($staff->isSuperAdmin())
                        <th class="text-left px-5 py-3">Branch</th>
                    @endif
                    <th class="text-left px-5 py-3">Book</th>
                    <th class="text-left px-5 py-3">Qty Requested</th>
                    <th class="text-left px-5 py-3">Requested By</th>
                    <th class="text-left px-5 py-3">Status</th>
                    @if($staff->isSuperAdmin())
                        <th class="text-right px-5 py-3">Actions</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @forelse($requests as $request)
                    <tr class="border-t border-slate-100 dark:border-slate-800">
                        @if($staff->isSuperAdmin())
                            <td class="px-5 py-3 text-slate-600 dark:text-slate-400">{{ $request->branch?->name }}</td>
                        @endif
                        <td class="px-5 py-3 font-semibold text-slate-900 dark:text-white">{{ $request->book?->title }}</td>
                        <td class="px-5 py-3 font-mono text-slate-600 dark:text-slate-400">{{ $request->requested_quantity }}</td>
                        <td class="px-5 py-3 text-slate-600 dark:text-slate-400">{{ $request->requestedBy?->name }}</td>
                        <td class="px-5 py-3">
                            <span class="text-xs font-semibold px-3 py-1 border" style="border-radius: 2px;">{{ $request->status->label() }}</span>
                            @if($request->status->value === 'rejected' && $request->reason)
                                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">{{ $request->reason }}</p>
                            @endif
                        </td>
                        @if($staff->isSuperAdmin())
                            <td class="px-5 py-3 text-right space-x-3">
                                @if($request->isPending())
                                    <form method="POST" action="{{ route('bookshop.staff.restock-requests.approve', $request) }}" class="inline">
                                        @csrf @method('PATCH')
                                        <button type="submit" class="text-emerald-600 dark:text-emerald-400 font-medium">Approve</button>
                                    </form>
                                    <form method="POST" action="{{ route('bookshop.staff.restock-requests.reject', $request) }}" class="inline"
                                          onsubmit="return promptReject(this);">
                                        @csrf @method('PATCH')
                                        <input type="hidden" name="reason" class="reject-reason-input">
                                        <button type="submit" class="text-red-500 font-medium">Reject</button>
                                    </form>
                                @endif
                            </td>
                        @endif
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-5 py-8 text-center text-slate-500 dark:text-slate-400">No restock requests yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($requests->hasPages())
        <div class="bg-white dark:bg-slate-900 px-5 py-4" style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06);">
            {{ $requests->links() }}
        </div>
    @endif

    <script>
        function promptReject(form) {
            const reason = prompt('Reason for rejecting this request:');
            if (! reason) return false;
            form.querySelector('.reject-reason-input').value = reason;
            return true;
        }
    </script>
</x-bookshop::layouts.staff>
