<x-bookshop::layouts.staff :title="'New Restock Request - BookShop'">
    <h1 class="text-xl font-bold text-slate-900 dark:text-white" style="font-family: 'Georgia', serif;">Request Stock</h1>
    <p class="text-sm text-slate-500 dark:text-slate-400">Add up to 5 books in one request. Leave rows blank if you need fewer - blank rows are ignored.</p>

    <div class="bg-white dark:bg-slate-900 p-6 max-w-2xl" style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 1px 6px rgba(0,0,0,0.04);">
        <form method="POST" action="{{ route('bookshop.staff.restock-requests.store') }}" class="space-y-6">
            @csrf

            @for($i = 0; $i < 5; $i++)
                <div class="grid sm:grid-cols-5 gap-3 items-end pb-4 {{ $i < 4 ? 'border-b border-slate-100 dark:border-slate-800' : '' }}">
                    <div class="sm:col-span-3">
                        <x-bookshop::book-picker
                            id-prefix="restock-item-{{ $i }}"
                            name="items[{{ $i }}][book_id]"
                            :label="'Book #'.($i + 1)"
                            :value="old('items.'.$i.'.book_id')"
                            :show-hint="false"
                        />
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-xs text-slate-500 dark:text-slate-400 mb-1">Quantity</label>
                        <input type="number" name="items[{{ $i }}][quantity]" min="1" value="{{ old('items.'.$i.'.quantity') }}"
                               class="w-full px-3 py-2.5 text-sm border border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-white" style="border-radius: 2px;">
                    </div>
                </div>
            @endfor

            <div>
                <label class="block text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2" style="letter-spacing: 0.08em;">Notes (optional, applies to the whole request)</label>
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
