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

    <form method="GET" class="flex gap-3">
        <select name="status" onchange="this.form.submit()" class="px-3 py-2 text-sm border border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-white" style="border-radius: 2px;">
            <option value="">Any Status</option>
            @foreach(\App\BookShop\Enums\RestockRequestStatus::cases() as $status)
                <option value="{{ $status->value }}" {{ request('status') === $status->value ? 'selected' : '' }}>{{ $status->label() }}</option>
            @endforeach
        </select>
        <p class="self-center text-xs text-slate-500 dark:text-slate-400">Filters to requests with at least one item in that status.</p>
    </form>

    <div class="bg-white dark:bg-slate-900 overflow-hidden" style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 1px 6px rgba(0,0,0,0.04);">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 dark:bg-slate-800/50 text-xs uppercase tracking-wider text-slate-500 dark:text-slate-400">
                <tr>
                    @if($staff->isSuperAdmin())
                        <th class="text-left px-5 py-3">Branch</th>
                    @endif
                    <th class="text-left px-5 py-3">Requested By</th>
                    <th class="text-left px-5 py-3">Items</th>
                    <th class="text-left px-5 py-3">Status</th>
                    <th class="text-left px-5 py-3">Submitted</th>
                    <th class="text-right px-5 py-3">&nbsp;</th>
                </tr>
            </thead>
            <tbody>
                @php($statusColors = [
                    'pending' => 'text-amber-800 bg-amber-50 border-amber-200 dark:text-amber-200 dark:bg-amber-900/30 dark:border-amber-800',
                    'approved' => 'text-blue-800 bg-blue-50 border-blue-200 dark:text-blue-200 dark:bg-blue-900/30 dark:border-blue-800',
                    'dispatched' => 'text-purple-800 bg-purple-50 border-purple-200 dark:text-purple-200 dark:bg-purple-900/30 dark:border-purple-800',
                    'delivered' => 'text-indigo-800 bg-indigo-50 border-indigo-200 dark:text-indigo-200 dark:bg-indigo-900/30 dark:border-indigo-800',
                    'confirmed' => 'text-emerald-800 bg-emerald-50 border-emerald-200 dark:text-emerald-200 dark:bg-emerald-900/30 dark:border-emerald-800',
                    'rejected' => 'text-red-700 bg-red-50 border-red-200 dark:text-red-300 dark:bg-red-900/20 dark:border-red-800',
                ])
                @forelse($batches as $batch)
                    @php($batchItems = $itemsByBatch->get($batch->batch_id, collect()))
                    @php($statusCounts = $batchItems->countBy(fn ($item) => $item->status->value))
                    <tr class="border-t border-slate-100 dark:border-slate-800 hover:bg-slate-50 dark:hover:bg-slate-800/40 cursor-pointer"
                        onclick="window.location='{{ route('bookshop.staff.restock-requests.show', $batch->batch_id) }}'">
                        @if($staff->isSuperAdmin())
                            <td class="px-5 py-3 text-slate-600 dark:text-slate-400">{{ $batchItems->first()?->branch?->name }}</td>
                        @endif
                        <td class="px-5 py-3 text-slate-600 dark:text-slate-400">{{ $batchItems->first()?->requestedBy?->name }}</td>
                        <td class="px-5 py-3 text-slate-900 dark:text-white font-semibold">{{ $batch->item_count }} book{{ $batch->item_count > 1 ? 's' : '' }}</td>
                        <td class="px-5 py-3 space-x-1">
                            @foreach(\App\BookShop\Enums\RestockRequestStatus::cases() as $status)
                                @if(($statusCounts[$status->value] ?? 0) > 0)
                                    <span class="text-xs font-semibold px-2 py-1 border {{ $statusColors[$status->value] }}" style="border-radius: 2px;">
                                        {{ $statusCounts[$status->value] }} {{ $status->label() }}
                                    </span>
                                @endif
                            @endforeach
                        </td>
                        <td class="px-5 py-3 text-slate-600 dark:text-slate-400">{{ $batchItems->first()?->created_at?->diffForHumans() }}</td>
                        <td class="px-5 py-3 text-right text-purple-600 dark:text-purple-400 font-medium">View &rarr;</td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-5 py-8 text-center text-slate-500 dark:text-slate-400">No restock requests yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($batches->hasPages())
        <div class="bg-white dark:bg-slate-900 px-5 py-4" style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06);">
            {{ $batches->links() }}
        </div>
    @endif
</x-bookshop::layouts.staff>
