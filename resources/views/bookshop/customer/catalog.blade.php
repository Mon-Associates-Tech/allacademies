<x-bookshop::layouts.customer :title="'Catalog - BookShop'">
    <h1 class="text-xl font-bold text-slate-900 dark:text-white" style="font-family: 'Georgia', serif;">Catalog</h1>

    @if(! $branch)
        <div class="px-4 py-3 text-sm text-amber-800 bg-amber-50 border border-amber-200 dark:text-amber-200 dark:bg-amber-900/30 dark:border-amber-800" style="border-radius: 2px;">
            @if($customer)
                No branch currently serves your region ({{ $customer->region ?: 'not set' }}). You can still browse, but you won't
                be able to place an order until a branch is added near you, or you
                <a href="{{ route('bookshop.shop.branches.index') }}" class="underline font-medium">choose a different branch</a>.
            @else
                You're browsing as a guest — <a href="{{ route('bookshop.shop.branches.index') }}" class="underline font-medium">choose a branch</a> to see stock and place an order.
            @endif
        </div>
    @else
        <p class="text-sm text-slate-500 dark:text-slate-400">
            Shopping at <strong>{{ $branch->name }}</strong> ({{ $branch->city }}).
            <a href="{{ route('bookshop.shop.branches.index') }}" class="text-purple-600 dark:text-purple-400 underline">Switch branch</a>
        </p>
    @endif

    {{-- Filters: stacked on mobile (search full-width, sort/category share a
         row), inline on larger screens - this is the primary browsing
         surface for the ~90% of customers on phones, so it needs to be
         usable one-thumb, not just "responsive" in the technical sense. --}}
    <form method="GET" class="space-y-3">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by title or author..."
               class="w-full px-4 py-3 text-sm border border-slate-200 dark:border-slate-700 focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500 dark:bg-slate-800 dark:text-white"
               style="border-radius: 2px;">

        <div class="grid grid-cols-2 gap-3">
            <select name="category_id" class="px-3 py-3 text-sm border border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-white" style="border-radius: 2px;">
                <option value="">All Categories</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}" {{ (int) request('category_id') === $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                @endforeach
            </select>
            <select name="sort" class="px-3 py-3 text-sm border border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-white" style="border-radius: 2px;">
                @foreach($sortOptions as $value => $label)
                    <option value="{{ $value }}" {{ $sortKey === $value ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
        </div>

        <button type="submit" class="w-full sm:w-auto px-6 py-3 text-sm font-semibold text-white" style="border-radius: 2px; background: linear-gradient(135deg, #1e293b, #334155);">
            Apply
        </button>
    </form>

    {{-- Mobile-first card grid: 2 columns on phones, scaling up on wider
         screens. Whole card is a tap target to the product detail page. --}}
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
        @forelse($books as $book)
            @php($available = $book->branch_stock ?? 0)
            <div class="bg-white dark:bg-slate-900 overflow-hidden" style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06);">
                <a href="{{ route('bookshop.shop.books.show', $book) }}" class="block hover:opacity-90 transition-opacity">
                    <div class="aspect-[2/3] bg-slate-100 dark:bg-slate-800 flex items-center justify-center overflow-hidden">
                        @if($book->hasCover())
                            <img src="{{ $book->coverUrl() }}" alt="{{ $book->title }}" class="w-full h-full object-cover" loading="lazy">
                        @else
                            <svg class="w-10 h-10 text-slate-300 dark:text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" />
                            </svg>
                        @endif
                    </div>
                    <div class="p-3 pb-1">
                        <p class="text-sm font-semibold text-slate-900 dark:text-white leading-snug" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                            {{ $book->title }}
                        </p>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5 truncate">{{ $book->author ?? ' ' }}</p>
                        <div class="flex items-center justify-between mt-2">
                            <span class="text-sm font-mono font-semibold text-slate-900 dark:text-white">GHS {{ number_format($book->price, 2) }}</span>
                            @if($branch)
                                @if($available > 0)
                                    <span class="text-[10px] font-semibold px-2 py-0.5 border text-emerald-800 bg-emerald-50 border-emerald-200 dark:text-emerald-200 dark:bg-emerald-900/30 dark:border-emerald-800" style="border-radius: 2px;">In Stock</span>
                                @else
                                    <span class="text-[10px] font-semibold px-2 py-0.5 border text-slate-500 bg-slate-50 border-slate-200 dark:text-slate-400 dark:bg-slate-800 dark:border-slate-700" style="border-radius: 2px;">Out of Stock</span>
                                @endif
                            @endif
                        </div>
                    </div>
                </a>
                @if($branch && $available > 0)
                    <form method="POST" action="{{ route('bookshop.shop.cart.add') }}" class="px-3 pb-3">
                        @csrf
                        <input type="hidden" name="book_id" value="{{ $book->id }}">
                        <input type="hidden" name="quantity" value="1">
                        <button type="submit" class="w-full text-xs font-semibold py-1.5 text-purple-700 dark:text-purple-300 border border-purple-200 dark:border-purple-800" style="border-radius: 2px;">
                            + Add to Cart
                        </button>
                    </form>
                @endif
            </div>
        @empty
            <div class="col-span-2 sm:col-span-3 lg:col-span-4 bg-white dark:bg-slate-900 text-center py-16"
                 style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06);">
                <p class="text-sm text-slate-500 dark:text-slate-400">No books match your search.</p>
            </div>
        @endforelse
    </div>

    @if($books->hasPages())
        <div class="bg-white dark:bg-slate-900 px-5 py-4" style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06);">
            {{ $books->links() }}
        </div>
    @endif
</x-bookshop::layouts.customer>
