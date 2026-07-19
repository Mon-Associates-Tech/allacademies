<x-bookshop::layouts.staff :title="'Warehouse - BookShop'">
    <h1 class="text-xl font-bold text-slate-900 dark:text-white" style="font-family: 'Georgia', serif;">Warehouse Stock</h1>
    <p class="text-sm text-slate-500 dark:text-slate-400">The central pool branches draw from when a restock request is approved.</p>

    <div class="bg-white dark:bg-slate-900 p-6" style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 1px 6px rgba(0,0,0,0.04);">
        <h2 class="font-bold text-slate-900 dark:text-white text-xs uppercase tracking-wider mb-4" style="letter-spacing: 0.1em;">Set / Adjust Warehouse Stock</h2>
        <form method="POST" action="{{ route('bookshop.staff.warehouse.store') }}" class="grid sm:grid-cols-4 gap-3 items-end">
            @csrf
            <div class="sm:col-span-2">
                <x-bookshop::book-picker id-prefix="warehouse-book-picker" name="book_id" label="Book" :required="true" :show-hint="false"
                    :value="old('book_id')" :value-label="$prefillBook?->title" />
            </div>
            <div>
                <label class="block text-xs text-slate-500 dark:text-slate-400 mb-1">Quantity</label>
                <input type="number" name="quantity" min="0" required
                       class="w-full px-3 py-2 text-sm border border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-white" style="border-radius: 2px;">
            </div>
            <button type="submit" class="px-5 py-2.5 text-sm font-semibold text-white" style="border-radius: 2px; background: linear-gradient(135deg, #7c3aed, #a78bfa);">
                Save
            </button>
        </form>
    </div>

    <form method="GET" class="flex gap-3">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by title or author..."
               class="flex-1 px-4 py-2.5 text-sm border border-slate-200 dark:border-slate-700 focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500 dark:bg-slate-800 dark:text-white"
               style="border-radius: 2px;">
        <button type="submit" class="px-5 py-2.5 text-sm font-semibold text-white" style="border-radius: 2px; background: linear-gradient(135deg, #1e293b, #334155);">
            Filter
        </button>
    </form>

    <div class="bg-white dark:bg-slate-900 overflow-hidden" style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 1px 6px rgba(0,0,0,0.04);">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 dark:bg-slate-800/50 text-xs uppercase tracking-wider text-slate-500 dark:text-slate-400">
                <tr>
                    <th class="text-left px-5 py-3">Book</th>
                    <th class="text-left px-5 py-3">Quantity</th>
                    <th class="text-left px-5 py-3">Last Updated</th>
                </tr>
            </thead>
            <tbody>
                @forelse($stock as $row)
                    <tr class="border-t border-slate-100 dark:border-slate-800">
                        <td class="px-5 py-3 font-semibold text-slate-900 dark:text-white">{{ $row->book?->title }}</td>
                        <td class="px-5 py-3 font-mono text-slate-600 dark:text-slate-400">{{ $row->quantity }}</td>
                        <td class="px-5 py-3 text-slate-600 dark:text-slate-400">{{ $row->updated_at->diffForHumans() }}</td>
                    </tr>
                @empty
                    <tr><td colspan="3" class="px-5 py-8 text-center text-slate-500 dark:text-slate-400">No warehouse stock found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($stock->hasPages())
        <div class="bg-white dark:bg-slate-900 px-5 py-4" style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06);">
            {{ $stock->links() }}
        </div>
    @endif
</x-bookshop::layouts.staff>
