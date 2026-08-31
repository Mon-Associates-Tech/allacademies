<x-bookshop::layouts.customer :title="'Cart - BookShop'">
    <h1 class="text-xl font-bold text-slate-900 dark:text-white" style="font-family: 'Georgia', serif;">Your Cart</h1>

    @if(! $branch)
        <div class="px-4 py-3 text-sm text-amber-800 bg-amber-50 border border-amber-200 dark:text-amber-200 dark:bg-amber-900/30 dark:border-amber-800" style="border-radius: 2px;">
            No branch currently serves your region yet.
            <a href="{{ route('bookshop.shop.branches.index') }}" class="underline font-medium">Choose a branch &rarr;</a>
        </div>
    @else
        <p class="text-sm text-slate-500 dark:text-slate-400">
            Shopping at <strong>{{ $branch->name }}</strong>.
            <a href="{{ route('bookshop.shop.branches.index') }}" class="text-purple-600 dark:text-purple-400 underline">Switch branch</a>
        </p>
    @endif

    @if($lines->isEmpty())
        <div class="bg-white dark:bg-slate-900 text-center py-16" style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06);">
            <p class="text-sm text-slate-500 dark:text-slate-400">
                Your cart is empty.
                <a href="{{ route('bookshop.shop.catalog') }}" class="text-purple-600 dark:text-purple-400 underline">Browse the catalog &rarr;</a>
            </p>
        </div>
    @else
        <form method="POST" action="{{ route('bookshop.shop.cart.update') }}">
            @csrf @method('PUT')

            <div class="bg-white dark:bg-slate-900 overflow-hidden" style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 1px 6px rgba(0,0,0,0.04);">
                @foreach($lines as $line)
                    <div class="flex items-center gap-3 px-4 py-3 border-b border-slate-100 dark:border-slate-800 last:border-0">
                        <div class="w-12 h-16 flex-shrink-0 bg-slate-100 dark:bg-slate-800 flex items-center justify-center overflow-hidden" style="border-radius: 2px;">
                            @if($line['book']->hasCover())
                                <img src="{{ $line['book']->coverUrl() }}" alt="" class="w-full h-full object-cover">
                            @else
                                <svg class="w-5 h-5 text-slate-300 dark:text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" />
                                </svg>
                            @endif
                        </div>
                        <div class="flex-1 min-w-0">
                            <a href="{{ route('bookshop.shop.books.show', $line['book']) }}" class="text-sm font-semibold text-slate-900 dark:text-white truncate block">{{ $line['book']->title }}</a>
                            <p class="text-xs text-slate-500 dark:text-slate-400">GHS {{ number_format($line['book']->price, 2) }} each &middot; {{ $line['available'] }} available</p>
                        </div>
                        <input type="number" name="quantities[{{ $line['book']->id }}]" value="{{ $line['quantity'] }}" min="0" max="{{ $line['available'] }}"
                               class="w-16 px-2 py-1.5 text-sm border border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-white" style="border-radius: 2px;">
                    </div>
                @endforeach
            </div>

            <div class="flex items-center justify-between mt-4">
                <button type="submit" class="text-sm text-purple-600 dark:text-purple-400 font-medium underline">Update Quantities</button>
                <p class="text-lg font-mono font-bold text-slate-900 dark:text-white">GHS {{ number_format($subtotal, 2) }}</p>
            </div>
        </form>

        @if($branch)
            <form method="POST" action="{{ route('bookshop.shop.cart.checkout') }}" class="bg-white dark:bg-slate-900 p-6" style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 1px 6px rgba(0,0,0,0.04);">
                @csrf
                @if(! $isGuest)
                    <div class="mb-4">
                        <label class="block text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2" style="letter-spacing: 0.08em;">How would you like this order?</label>
                        <div class="flex gap-3" id="fulfillment-toggle">
                            <label class="flex-1 flex items-center gap-2 px-4 py-3 border border-slate-200 dark:border-slate-700 cursor-pointer" style="border-radius: 2px;">
                                <input type="radio" name="fulfillment_method" value="pickup" checked data-fulfillment-option>
                                <span class="text-sm text-slate-700 dark:text-slate-200">Pickup at {{ $branch->name }}</span>
                            </label>
                            <label class="flex-1 flex items-center gap-2 px-4 py-3 border border-slate-200 dark:border-slate-700 cursor-pointer" style="border-radius: 2px;">
                                <input type="radio" name="fulfillment_method" value="delivery" data-fulfillment-option>
                                <span class="text-sm text-slate-700 dark:text-slate-200">Delivery</span>
                            </label>
                        </div>
                    </div>

                    <div id="delivery-address-field" class="mb-4 hidden">
                        <label class="block text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2" style="letter-spacing: 0.08em;">Delivery Address</label>
                        <textarea name="delivery_address" rows="2" placeholder="Where should this be delivered?"
                                  class="w-full px-3 py-2 text-sm border border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-white" style="border-radius: 2px;">{{ old('delivery_address') }}</textarea>
                    </div>

                    <label class="block text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2" style="letter-spacing: 0.08em;">Notes (optional)</label>
                    <textarea name="notes" rows="2" placeholder="Any special instructions for this order..."
                              class="w-full px-3 py-2 text-sm border border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-white" style="border-radius: 2px;">{{ old('notes') }}</textarea>
                @else
                    <p class="text-sm text-slate-500 dark:text-slate-400 mb-2">
                        You'll create a free account on the next step — your cart stays exactly as it is now.
                    </p>
                @endif

                <button type="submit"
                        class="mt-4 w-full inline-flex items-center justify-center gap-2 px-8 py-3 text-sm font-semibold text-white transition-all"
                        style="border-radius: 2px; background: linear-gradient(135deg, #7c3aed, #a78bfa); box-shadow: 0 2px 10px rgba(124,58,237,0.3);">
                    @if($isGuest)
                        Continue to Order &mdash; GHS {{ number_format($subtotal, 2) }}
                    @else
                        Place Order &mdash; GHS {{ number_format($subtotal, 2) }}
                    @endif
                </button>
            </form>

            @if(! $isGuest)
                <script>
                    (function () {
                        const options = document.querySelectorAll('[data-fulfillment-option]');
                        const addressField = document.getElementById('delivery-address-field');
                        if (!options.length || !addressField) return;

                        const sync = () => {
                            const selected = document.querySelector('[data-fulfillment-option]:checked');
                            addressField.classList.toggle('hidden', !selected || selected.value !== 'delivery');
                        };

                        options.forEach((el) => el.addEventListener('change', sync));
                        sync();
                    })();
                </script>
            @endif
        @endif

        <div class="space-y-2">
            <p class="text-xs text-slate-500 dark:text-slate-400 uppercase tracking-wider" style="letter-spacing: 0.08em;">Remove an item</p>
            <div class="flex flex-wrap gap-2">
                @foreach($lines as $line)
                    <form method="POST" action="{{ route('bookshop.shop.cart.remove', $line['book']) }}">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-xs px-3 py-1.5 border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-400" style="border-radius: 2px;">
                            Remove "{{ \Illuminate\Support\Str::limit($line['book']->title, 25) }}"
                        </button>
                    </form>
                @endforeach
            </div>
        </div>
    @endif
</x-bookshop::layouts.customer>
