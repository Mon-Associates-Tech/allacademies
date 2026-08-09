<x-bookshop::layouts.customer :title="'Review Bulk Request - BookShop'">
    <a href="{{ route('bookshop.shop.bulk-orders.catalog') }}" class="inline-flex items-center gap-1 text-sm text-slate-500 dark:text-slate-400">
        &larr; Continue Browsing
    </a>

    <h1 class="text-xl font-bold text-slate-900 dark:text-white" style="font-family: 'Georgia', serif;">Review Your Bulk Request</h1>

    @if($lines->isEmpty())
        <div class="bg-white dark:bg-slate-900 text-center py-16" style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06);">
            <p class="text-sm text-slate-500 dark:text-slate-400">
                Nothing added yet.
                <a href="{{ route('bookshop.shop.bulk-orders.catalog') }}" class="text-purple-600 dark:text-purple-400 underline">Browse the catalog &rarr;</a>
            </p>
        </div>
    @else
        <form method="POST" action="{{ route('bookshop.shop.bulk-orders.update') }}">
            @csrf @method('PUT')
            <div class="bg-white dark:bg-slate-900 overflow-hidden" style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 1px 6px rgba(0,0,0,0.04);">
                @foreach($lines as $line)
                    <div class="flex items-center gap-3 px-4 py-3 border-b border-slate-100 dark:border-slate-800 last:border-0">
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-slate-900 dark:text-white truncate">{{ $line['book']->title }}</p>
                            <p class="text-xs text-slate-500 dark:text-slate-400">Catalog price: GHS {{ number_format($line['book']->price, 2) }} each (final pricing comes with your quote)</p>
                        </div>
                        <input type="number" name="quantities[{{ $line['book']->id }}]" value="{{ $line['quantity'] }}" min="0"
                               class="w-24 px-2 py-1.5 text-sm border border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-white" style="border-radius: 2px;">
                    </div>
                @endforeach
            </div>

            <div class="flex items-center justify-between mt-4">
                <button type="submit" class="text-sm text-purple-600 dark:text-purple-400 font-medium underline">Update Quantities</button>
                <p class="text-sm text-slate-600 dark:text-slate-400">
                    Total: <span class="font-bold text-slate-900 dark:text-white">{{ $totalQuantity }}</span> copies
                    @if($totalQuantity < $minimumQuantity)
                        <span class="text-amber-600 dark:text-amber-400">(minimum {{ $minimumQuantity }})</span>
                    @endif
                </p>
            </div>
        </form>

        <div class="space-y-2">
            <p class="text-xs text-slate-500 dark:text-slate-400 uppercase tracking-wider" style="letter-spacing: 0.08em;">Remove an item</p>
            <div class="flex flex-wrap gap-2">
                @foreach($lines as $line)
                    <form method="POST" action="{{ route('bookshop.shop.bulk-orders.remove', $line['book']) }}">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-xs px-3 py-1.5 border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-400" style="border-radius: 2px;">
                            Remove "{{ \Illuminate\Support\Str::limit($line['book']->title, 25) }}"
                        </button>
                    </form>
                @endforeach
            </div>
        </div>

        @if($totalQuantity >= $minimumQuantity)
            <form method="POST" action="{{ route('bookshop.shop.bulk-orders.submit') }}" class="bg-white dark:bg-slate-900 p-6 space-y-4" style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 1px 6px rgba(0,0,0,0.04);">
                @csrf
                <h2 class="font-bold text-slate-900 dark:text-white text-xs uppercase tracking-wider" style="letter-spacing: 0.1em;">Who's this order for?</h2>

                <div class="grid sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2" style="letter-spacing: 0.08em;">Institution / Organization Name</label>
                        <input type="text" name="institution_name" value="{{ old('institution_name') }}" required
                               class="w-full px-4 py-2.5 text-sm border border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-white" style="border-radius: 2px;">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2" style="letter-spacing: 0.08em;">Type</label>
                        <select name="institution_type" class="w-full px-4 py-2.5 text-sm border border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-white" style="border-radius: 2px;">
                            <option value="school" {{ old('institution_type') === 'school' ? 'selected' : '' }}>School</option>
                            <option value="corporate" {{ old('institution_type') === 'corporate' ? 'selected' : '' }}>Corporate</option>
                            <option value="church" {{ old('institution_type') === 'church' ? 'selected' : '' }}>Church</option>
                            <option value="ngo" {{ old('institution_type') === 'ngo' ? 'selected' : '' }}>NGO</option>
                            <option value="other" {{ old('institution_type', 'other') === 'other' ? 'selected' : '' }}>Other</option>
                        </select>
                    </div>
                </div>

                <div class="grid sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2" style="letter-spacing: 0.08em;">Contact Phone (optional)</label>
                        <input type="text" name="contact_phone" value="{{ old('contact_phone') }}"
                               class="w-full px-4 py-2.5 text-sm border border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-white" style="border-radius: 2px;">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2" style="letter-spacing: 0.08em;">Needed By (optional)</label>
                        <input type="date" name="requested_delivery_date" value="{{ old('requested_delivery_date') }}" min="{{ now()->toDateString() }}"
                               class="w-full px-4 py-2.5 text-sm border border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-white" style="border-radius: 2px;">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2" style="letter-spacing: 0.08em;">Notes (optional)</label>
                    <textarea name="notes" rows="3" placeholder="Anything else the branch should know..."
                              class="w-full px-4 py-2.5 text-sm border border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-white" style="border-radius: 2px;">{{ old('notes') }}</textarea>
                </div>

                <button type="submit"
                        class="w-full inline-flex items-center justify-center gap-2 px-8 py-3 text-sm font-semibold text-white transition-all"
                        style="border-radius: 2px; background: linear-gradient(135deg, #7c3aed, #a78bfa); box-shadow: 0 2px 10px rgba(124,58,237,0.3);">
                    Submit Bulk Request
                </button>
                <p class="text-xs text-slate-500 dark:text-slate-400 text-center">
                    This isn't a payment yet — the branch will review and send you a quote first.
                </p>
            </form>
        @endif
    @endif
</x-bookshop::layouts.customer>
