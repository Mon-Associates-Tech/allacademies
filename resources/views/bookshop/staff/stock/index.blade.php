<x-bookshop::layouts.staff :title="'Stock - BookShop'">
    <h1 class="text-xl font-bold text-slate-900 dark:text-white" style="font-family: 'Georgia', serif;">
        {{ $staff->isSuperAdmin() ? 'Stock — All Branches' : 'Stock — ' . $staff->branch?->name }}
    </h1>

    @if($staff->isSuperAdmin())
        <div class="bg-white dark:bg-slate-900 p-6" style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 1px 6px rgba(0,0,0,0.04);">
            <h2 class="font-bold text-slate-900 dark:text-white text-xs uppercase tracking-wider mb-4" style="letter-spacing: 0.1em;">Set / Adjust Stock</h2>
            <form method="POST" action="{{ route('bookshop.staff.stock.store') }}" class="grid sm:grid-cols-5 gap-3 items-end">
                @csrf
                <div class="sm:col-span-2">
                    <label class="block text-xs text-slate-500 dark:text-slate-400 mb-1">Branch</label>
                    <select name="branch_id" required class="w-full px-3 py-2 text-sm border border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-white" style="border-radius: 2px;">
                        @foreach($branches as $branch)
                            <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="sm:col-span-2">
                    <x-bookshop::book-picker id-prefix="stock-book-picker" name="book_id" label="Book" :required="true" :show-hint="false"
                        :value="old('book_id')" :value-label="$prefillBook?->title" />
                </div>
                <div>
                    <label class="block text-xs text-slate-500 dark:text-slate-400 mb-1">Quantity</label>
                    <input type="number" name="quantity" min="0" required
                           class="w-full px-3 py-2 text-sm border border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-white" style="border-radius: 2px;">
                </div>
                <button type="submit" class="sm:col-span-5 px-5 py-2.5 text-sm font-semibold text-white" style="border-radius: 2px; background: linear-gradient(135deg, #7c3aed, #a78bfa);">
                    Save
                </button>
            </form>
        </div>
    @endif

    <form method="GET" class="flex gap-3">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by title or author..."
               class="flex-1 px-4 py-2.5 text-sm border border-slate-200 dark:border-slate-700 focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500 dark:bg-slate-800 dark:text-white"
               style="border-radius: 2px;">
        @if($staff->isSuperAdmin())
            <select name="branch_id" class="px-4 py-2.5 text-sm border border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-white" style="border-radius: 2px;">
                <option value="">All Branches</option>
                @foreach($branches as $branch)
                    <option value="{{ $branch->id }}" {{ (int) request('branch_id') === $branch->id ? 'selected' : '' }}>{{ $branch->name }}</option>
                @endforeach
            </select>
        @endif
        <label class="inline-flex items-center gap-2 px-4 text-sm text-slate-600 dark:text-slate-400 whitespace-nowrap">
            <input type="checkbox" name="low_only" value="1" {{ request('low_only') ? 'checked' : '' }} style="border-radius: 2px;">
            Low stock only
        </label>
        <button type="submit" class="px-5 py-2.5 text-sm font-semibold text-white" style="border-radius: 2px; background: linear-gradient(135deg, #1e293b, #334155);">
            Filter
        </button>
    </form>

    <div class="bg-white dark:bg-slate-900 overflow-hidden" style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 1px 6px rgba(0,0,0,0.04);">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 dark:bg-slate-800/50 text-xs uppercase tracking-wider text-slate-500 dark:text-slate-400">
                <tr>
                    @if($staff->isSuperAdmin())
                        <th class="text-left px-5 py-3">Branch</th>
                    @endif
                    <th class="text-left px-5 py-3">Book</th>
                    <th class="text-left px-5 py-3">Quantity</th>
                    <th class="text-left px-5 py-3">Threshold</th>
                    <th class="text-left px-5 py-3">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($stockLevels as $level)
                    <tr class="border-t border-slate-100 dark:border-slate-800">
                        @if($staff->isSuperAdmin())
                            <td class="px-5 py-3 text-slate-600 dark:text-slate-400">{{ $level->branch?->name }}</td>
                        @endif
                        <td class="px-5 py-3 font-semibold text-slate-900 dark:text-white">{{ $level->book?->title }}</td>
                        <td class="px-5 py-3 font-mono text-slate-600 dark:text-slate-400">{{ $level->quantity }}</td>
                        <td class="px-5 py-3 text-slate-600 dark:text-slate-400">{{ $level->low_stock_threshold }}</td>
                        <td class="px-5 py-3">
                            @if($level->isLowStock())
                                <span class="text-xs font-semibold px-3 py-1 border text-amber-800 bg-amber-50 border-amber-200 dark:text-amber-200 dark:bg-amber-900/30 dark:border-amber-800" style="border-radius: 2px;">Low Stock</span>
                            @else
                                <span class="text-xs font-semibold px-3 py-1 border text-emerald-800 bg-emerald-50 border-emerald-200 dark:text-emerald-200 dark:bg-emerald-900/30 dark:border-emerald-800" style="border-radius: 2px;">OK</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-5 py-8 text-center text-slate-500 dark:text-slate-400">No stock records found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($stockLevels->hasPages())
        <div class="bg-white dark:bg-slate-900 px-5 py-4" style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06);">
            {{ $stockLevels->links() }}
        </div>
    @endif
</x-bookshop::layouts.staff>
