<x-bookshop::layouts.staff :title="'New Restock Request - BookShop'">
    <h1 class="text-xl font-bold text-slate-900 dark:text-white" style="font-family: 'Georgia', serif;">Request Stock</h1>

    <div class="bg-white dark:bg-slate-900 p-6 max-w-lg" style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 1px 6px rgba(0,0,0,0.04);">
        <form method="POST" action="{{ route('bookshop.staff.restock-requests.store') }}" class="space-y-5">
            @csrf

            <x-bookshop::book-picker id-prefix="restock-book-picker" name="book_id" label="Book" :required="true"
                :value="old('book_id')" :value-label="$prefillBook?->title" />

            <div>
                <label class="block text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2" style="letter-spacing: 0.08em;">Quantity Needed</label>
                <input type="number" name="requested_quantity" min="1" value="{{ old('requested_quantity') }}" required
                       class="w-full px-4 py-2.5 text-sm border border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-white" style="border-radius: 2px;">
            </div>

            <div>
                <label class="block text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2" style="letter-spacing: 0.08em;">Notes (optional)</label>
                <textarea name="notes" rows="3" class="w-full px-4 py-2.5 text-sm border border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-white" style="border-radius: 2px;">{{ old('notes') }}</textarea>
            </div>

            <button type="submit"
                    class="inline-flex items-center gap-2 px-6 py-2.5 text-sm font-semibold text-white transition-all"
                    style="border-radius: 2px; background: linear-gradient(135deg, #7c3aed, #a78bfa); box-shadow: 0 2px 10px rgba(124,58,237,0.3);">
                Submit Request
            </button>
        </form>
    </div>
</x-bookshop::layouts.staff>
