<x-bookshop::layouts.staff :title="'Bulk Orders - BookShop'">
    <div class="flex items-center justify-between">
        <h1 class="text-xl font-bold text-slate-900 dark:text-white" style="font-family: 'Georgia', serif;">
            {{ $staff->isSuperAdmin() ? 'Bulk Order Requests — All Branches' : 'Bulk Order Requests — ' . $staff->branch?->name }}
        </h1>
        <form method="GET">
            <select name="status" onchange="this.form.submit()" class="px-3 py-2 text-sm border border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-white" style="border-radius: 2px;">
                <option value="">All Statuses</option>
                @foreach(\App\BookShop\Enums\BulkOrderRequestStatus::cases() as $status)
                    <option value="{{ $status->value }}" {{ request('status') === $status->value ? 'selected' : '' }}>{{ $status->label() }}</option>
                @endforeach
            </select>
        </form>
    </div>

    <div class="bg-white dark:bg-slate-900 overflow-hidden" style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 1px 6px rgba(0,0,0,0.04);">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 dark:bg-slate-800/50 text-xs uppercase tracking-wider text-slate-500 dark:text-slate-400">
                <tr>
                    <th class="text-left px-5 py-3">Request #</th>
                    <th class="text-left px-5 py-3">Institution</th>
                    <th class="text-left px-5 py-3">Customer</th>
                    @if($staff->isSuperAdmin())
                        <th class="text-left px-5 py-3">Branch</th>
                    @endif
                    <th class="text-left px-5 py-3">Items</th>
                    <th class="text-left px-5 py-3">Status</th>
                    <th class="text-left px-5 py-3">Submitted</th>
                </tr>
            </thead>
            <tbody>
                @forelse($requests as $request)
                    <tr class="border-t border-slate-100 dark:border-slate-800 hover:bg-slate-50 dark:hover:bg-slate-800/40 cursor-pointer"
                        onclick="window.location='{{ route('bookshop.staff.bulk-orders.show', $request) }}'">
                        <td class="px-5 py-3 font-mono text-slate-900 dark:text-white">{{ $request->request_number }}</td>
                        <td class="px-5 py-3 text-slate-600 dark:text-slate-400">{{ $request->institution_name }}</td>
                        <td class="px-5 py-3 text-slate-600 dark:text-slate-400">{{ $request->customer?->name }}</td>
                        @if($staff->isSuperAdmin())
                            <td class="px-5 py-3 text-slate-600 dark:text-slate-400">{{ $request->branch?->name }}</td>
                        @endif
                        <td class="px-5 py-3 text-slate-600 dark:text-slate-400">{{ $request->items->count() }} book(s), {{ $request->items->sum('requested_quantity') }} copies</td>
                        <td class="px-5 py-3">
                            <span class="text-xs font-semibold px-3 py-1 border" style="border-radius: 2px;">{{ $request->status->label() }}</span>
                        </td>
                        <td class="px-5 py-3 text-slate-600 dark:text-slate-400">{{ $request->created_at->diffForHumans() }}</td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="px-5 py-8 text-center text-slate-500 dark:text-slate-400">No bulk order requests yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($requests->hasPages())
        <div class="bg-white dark:bg-slate-900 px-5 py-4" style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06);">
            {{ $requests->links() }}
        </div>
    @endif
</x-bookshop::layouts.staff>
