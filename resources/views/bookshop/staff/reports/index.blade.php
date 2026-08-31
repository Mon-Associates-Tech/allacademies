<x-bookshop::layouts.staff :title="'Reports - BookShop'">
    <div class="flex items-center justify-between flex-wrap gap-3">
        <h1 class="text-xl font-bold text-slate-900 dark:text-white" style="font-family: 'Georgia', serif;">
            {{ $staff->isSuperAdmin() ? 'Sales Reports — All Branches' : 'Sales Reports — ' . $staff->branch?->name }}
        </h1>
        <a href="{{ route('bookshop.staff.reports.export', request()->query()) }}"
           class="text-sm font-semibold px-4 py-2 text-white" style="border-radius: 2px; background: linear-gradient(135deg, #1e293b, #334155);">
            Export to Excel
        </a>
    </div>

    <form method="GET" class="flex flex-wrap items-end gap-3">
        <div>
            <label class="block text-xs text-slate-500 dark:text-slate-400 mb-1">From</label>
            <input type="date" name="from" value="{{ $from->format('Y-m-d') }}"
                   class="px-3 py-2 text-sm border border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-white" style="border-radius: 2px;">
        </div>
        <div>
            <label class="block text-xs text-slate-500 dark:text-slate-400 mb-1">To</label>
            <input type="date" name="to" value="{{ $to->format('Y-m-d') }}"
                   class="px-3 py-2 text-sm border border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-white" style="border-radius: 2px;">
        </div>
        <button type="submit" class="px-5 py-2.5 text-sm font-semibold text-white" style="border-radius: 2px; background: linear-gradient(135deg, #7c3aed, #a78bfa);">
            Apply
        </button>
        <p class="text-xs text-slate-500 dark:text-slate-400 pb-2.5">Based on when payment was confirmed, not when the order was placed.</p>
    </form>

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        @foreach([
            ['label' => 'Revenue', 'value' => 'GHS ' . number_format($total_revenue, 2)],
            ['label' => 'Paid Orders', 'value' => $total_orders],
            ['label' => 'Average Order Value', 'value' => 'GHS ' . number_format($average_order_value, 2)],
        ] as $metric)
            <div class="bg-white dark:bg-slate-900 px-4 py-4 flex flex-col items-center justify-center text-center"
                 style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 1px 6px rgba(0,0,0,0.04);">
                <p class="text-xs text-slate-500 dark:text-slate-400 uppercase tracking-widest mb-1 text-[10px]">{{ $metric['label'] }}</p>
                <p class="font-bold text-slate-900 dark:text-white tracking-tight text-2xl">{{ $metric['value'] }}</p>
            </div>
        @endforeach
    </div>

    @if($staff->isSuperAdmin() && $by_branch->isNotEmpty())
        <div class="bg-white dark:bg-slate-900 p-6" style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 1px 6px rgba(0,0,0,0.04);">
            <h2 class="font-bold text-slate-900 dark:text-white text-xs uppercase tracking-wider mb-4" style="letter-spacing: 0.1em;">Revenue by Branch</h2>
            <table class="w-full text-sm">
                <thead class="text-xs uppercase tracking-wider text-slate-500 dark:text-slate-400">
                    <tr><th class="text-left py-2">Branch</th><th class="text-left py-2">Orders</th><th class="text-left py-2">Revenue</th></tr>
                </thead>
                <tbody>
                    @foreach($by_branch as $row)
                        <tr class="border-t border-slate-100 dark:border-slate-800">
                            <td class="py-3 font-semibold text-slate-900 dark:text-white">{{ $row->branch_name }}</td>
                            <td class="py-3 text-slate-600 dark:text-slate-400">{{ $row->orders }}</td>
                            <td class="py-3 font-mono text-slate-600 dark:text-slate-400">GHS {{ number_format($row->revenue, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    <div class="bg-white dark:bg-slate-900 p-6" style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 1px 6px rgba(0,0,0,0.04);">
        <h2 class="font-bold text-slate-900 dark:text-white text-xs uppercase tracking-wider mb-4" style="letter-spacing: 0.1em;">Top Books</h2>
        @if($top_books->isEmpty())
            <p class="text-sm text-slate-500 dark:text-slate-400">No paid sales in this period yet.</p>
        @else
            <table class="w-full text-sm">
                <thead class="text-xs uppercase tracking-wider text-slate-500 dark:text-slate-400">
                    <tr><th class="text-left py-2">Book</th><th class="text-left py-2">Qty Sold</th><th class="text-left py-2">Revenue</th></tr>
                </thead>
                <tbody>
                    @foreach($top_books as $item)
                        <tr class="border-t border-slate-100 dark:border-slate-800">
                            <td class="py-3 font-semibold text-slate-900 dark:text-white">{{ $item->book?->title ?? 'Unknown' }}</td>
                            <td class="py-3 text-slate-600 dark:text-slate-400">{{ $item->qty_sold }}</td>
                            <td class="py-3 font-mono text-slate-600 dark:text-slate-400">GHS {{ number_format($item->revenue, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>

    <div class="bg-white dark:bg-slate-900 p-6" style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 1px 6px rgba(0,0,0,0.04);">
        <h2 class="font-bold text-slate-900 dark:text-white text-xs uppercase tracking-wider mb-4" style="letter-spacing: 0.1em;">Daily Revenue</h2>
        @if($daily->isEmpty())
            <p class="text-sm text-slate-500 dark:text-slate-400">No paid sales in this period yet.</p>
        @else
            <table class="w-full text-sm">
                <thead class="text-xs uppercase tracking-wider text-slate-500 dark:text-slate-400">
                    <tr><th class="text-left py-2">Date</th><th class="text-left py-2">Orders</th><th class="text-left py-2">Revenue</th></tr>
                </thead>
                <tbody>
                    @foreach($daily as $day)
                        <tr class="border-t border-slate-100 dark:border-slate-800">
                            <td class="py-3 text-slate-900 dark:text-white">{{ \Illuminate\Support\Carbon::parse($day->day)->format('M d, Y') }}</td>
                            <td class="py-3 text-slate-600 dark:text-slate-400">{{ $day->orders }}</td>
                            <td class="py-3 font-mono text-slate-600 dark:text-slate-400">GHS {{ number_format($day->revenue, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
</x-bookshop::layouts.staff>
