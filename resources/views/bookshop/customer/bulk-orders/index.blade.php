<x-bookshop::layouts.customer :title="'Bulk Requests - BookShop'">
    <div class="flex items-center justify-between">
        <h1 class="text-xl font-bold text-slate-900 dark:text-white" style="font-family: 'Georgia', serif;">My Bulk Requests</h1>
        <a href="{{ route('bookshop.shop.bulk-orders.catalog') }}"
           class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-semibold text-white transition-all"
           style="border-radius: 2px; background: linear-gradient(135deg, #7c3aed, #a78bfa); box-shadow: 0 2px 10px rgba(124,58,237,0.3);">
            + New Bulk Request
        </a>
    </div>

    <div class="bg-white dark:bg-slate-900 overflow-hidden" style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 1px 6px rgba(0,0,0,0.04);">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 dark:bg-slate-800/50 text-xs uppercase tracking-wider text-slate-500 dark:text-slate-400">
                <tr>
                    <th class="text-left px-5 py-3">Request #</th>
                    <th class="text-left px-5 py-3">Institution</th>
                    <th class="text-left px-5 py-3">Items</th>
                    <th class="text-left px-5 py-3">Status</th>
                    <th class="text-left px-5 py-3">Submitted</th>
                </tr>
            </thead>
            <tbody>
                @forelse($requests as $request)
                    <tr class="border-t border-slate-100 dark:border-slate-800 hover:bg-slate-50 dark:hover:bg-slate-800/40 cursor-pointer"
                        onclick="window.location='{{ route('bookshop.shop.bulk-orders.show', $request) }}'">
                        <td class="px-5 py-3 font-mono text-slate-900 dark:text-white">{{ $request->request_number }}</td>
                        <td class="px-5 py-3 text-slate-600 dark:text-slate-400">{{ $request->institution_name }}</td>
                        <td class="px-5 py-3 text-slate-600 dark:text-slate-400">{{ $request->items->count() }} book(s), {{ $request->items->sum('requested_quantity') }} copies</td>
                        <td class="px-5 py-3">
                            <span class="text-xs font-semibold px-3 py-1 border" style="border-radius: 2px;">{{ $request->status->label() }}</span>
                        </td>
                        <td class="px-5 py-3 text-slate-600 dark:text-slate-400">{{ $request->created_at->format('M d, Y') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-5 py-8 text-center text-slate-500 dark:text-slate-400">
                        No bulk requests yet.
                        <a href="{{ route('bookshop.shop.bulk-orders.catalog') }}" class="text-purple-600 dark:text-purple-400">Start one &rarr;</a>
                    </td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($requests->hasPages())
        <div class="bg-white dark:bg-slate-900 px-5 py-4" style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06);">
            {{ $requests->links() }}
        </div>
    @endif
</x-bookshop::layouts.customer>
