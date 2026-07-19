<x-bookshop::layouts.customer :title="$book->title . ' - BookShop'">
    <a href="{{ route('bookshop.shop.catalog') }}" class="inline-flex items-center gap-1 text-sm text-slate-500 dark:text-slate-400">
        &larr; Back to Catalog
    </a>

    {{-- Mobile-first: single column, cover stacked above details. Two
         columns only once there's room for it to actually help, not just
         because desktop "should" look different. --}}
    <div class="grid sm:grid-cols-2 gap-6">
        <div class="aspect-[2/3] sm:aspect-auto sm:h-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center overflow-hidden"
             style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06);">
            @if($book->hasCover())
                <img src="{{ $book->coverUrl() }}" alt="{{ $book->title }}" class="w-full h-full object-cover">
            @else
                <svg class="w-16 h-16 text-slate-300 dark:text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" />
                </svg>
            @endif
        </div>

        <div class="space-y-4">
            <div>
                @if($book->category)
                    <span class="text-[10px] font-semibold uppercase tracking-wider px-2 py-1 border text-purple-700 bg-purple-50 border-purple-200 dark:text-purple-300 dark:bg-purple-900/30 dark:border-purple-800" style="border-radius: 2px;">
                        {{ $book->category->name }}
                    </span>
                @endif
                <h1 class="text-2xl font-bold text-slate-900 dark:text-white mt-2" style="font-family: 'Georgia', serif;">{{ $book->title }}</h1>
                @if($book->author)
                    <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">by {{ $book->author }}</p>
                @endif
            </div>

            <p class="text-2xl font-mono font-bold text-slate-900 dark:text-white">GHS {{ number_format($book->price, 2) }}</p>

            @if(! $branch)
                <div class="px-4 py-3 text-sm text-amber-800 bg-amber-50 border border-amber-200 dark:text-amber-200 dark:bg-amber-900/30 dark:border-amber-800" style="border-radius: 2px;">
                    No branch currently serves your region yet, so this can't be ordered right now.
                </div>
            @elseif($availableQuantity < 1)
                <div class="px-4 py-3 text-sm text-slate-600 bg-slate-50 border border-slate-200 dark:text-slate-400 dark:bg-slate-800 dark:border-slate-700" style="border-radius: 2px;">
                    Out of stock at {{ $branch->name }} right now.
                </div>
            @else
                <p class="text-sm text-emerald-700 dark:text-emerald-400 font-medium">{{ $availableQuantity }} available at {{ $branch->name }}</p>
            @endif

            @if($book->hasPreview())
                <a href="{{ $book->previewUrl() }}" target="_blank" rel="noopener"
                   class="inline-flex items-center gap-2 px-5 py-3 text-sm font-semibold border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-200 transition-all"
                   style="border-radius: 2px;">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    Read Sample Preview
                </a>
            @endif

            @if($branch && $availableQuantity > 0)
                <form method="POST" action="{{ route('bookshop.shop.orders.store') }}" class="pt-2">
                    @csrf
                    <div class="flex items-center gap-3">
                        <label class="text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider" style="letter-spacing: 0.08em;">Quantity</label>
                        <input type="number" name="quantities[{{ $book->id }}]" min="1" max="{{ $availableQuantity }}" value="1"
                               class="w-20 px-3 py-2 text-sm border border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-white"
                               style="border-radius: 2px;">
                    </div>
                    <div class="mt-3">
                        <label class="text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider" style="letter-spacing: 0.08em;">Notes (optional)</label>
                        <textarea name="notes" rows="2" placeholder="Any special instructions for this order..."
                                  class="mt-1 w-full px-3 py-2 text-sm border border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-white"
                                  style="border-radius: 2px;"></textarea>
                    </div>
                    <button type="submit"
                            class="mt-4 w-full sm:w-auto inline-flex items-center justify-center gap-2 px-8 py-3 text-sm font-semibold text-white transition-all"
                            style="border-radius: 2px; background: linear-gradient(135deg, #7c3aed, #a78bfa); box-shadow: 0 2px 10px rgba(124,58,237,0.3);">
                        Place Order
                    </button>
                </form>
            @endif

            @if($book->description)
                <div class="pt-4 border-t border-slate-100 dark:border-slate-800">
                    <h2 class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2" style="letter-spacing: 0.08em;">Description</h2>
                    <p class="text-sm text-slate-700 dark:text-slate-300 leading-relaxed whitespace-pre-line">{{ $book->description }}</p>
                </div>
            @endif
        </div>
    </div>

    @if($relatedBooks->isNotEmpty())
        <div class="pt-6 border-t border-slate-100 dark:border-slate-800">
            <h2 class="text-sm font-bold text-slate-900 dark:text-white mb-4">More in {{ $book->category?->name }}</h2>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                @foreach($relatedBooks as $related)
                    <a href="{{ route('bookshop.shop.books.show', $related) }}"
                       class="bg-white dark:bg-slate-900 overflow-hidden block hover:shadow-md transition-shadow"
                       style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06);">
                        <div class="aspect-[2/3] bg-slate-100 dark:bg-slate-800 flex items-center justify-center overflow-hidden">
                            @if($related->hasCover())
                                <img src="{{ $related->coverUrl() }}" alt="{{ $related->title }}" class="w-full h-full object-cover" loading="lazy">
                            @else
                                <svg class="w-8 h-8 text-slate-300 dark:text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" />
                                </svg>
                            @endif
                        </div>
                        <div class="p-2">
                            <p class="text-xs font-semibold text-slate-900 dark:text-white leading-snug" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                {{ $related->title }}
                            </p>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    @endif
</x-bookshop::layouts.customer>
