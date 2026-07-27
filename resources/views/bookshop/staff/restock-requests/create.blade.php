<x-bookshop::layouts.staff :title="'New Restock Request - BookShop'">
    <h1 class="text-xl font-bold text-slate-900 dark:text-white" style="font-family: 'Georgia', serif;">Request Stock</h1>
    <p class="text-sm text-slate-500 dark:text-slate-400 mb-6">
        Add books to your request. Click "Add Book" to include more items. Each result shows how much the warehouse
        currently has — you can still request items that show as out of stock; they'll be matched automatically once
        the warehouse is restocked.
    </p>

    <div class="bg-white dark:bg-slate-900 p-6 max-w-2xl" style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 1px 6px rgba(0,0,0,0.04);">
        <form method="POST" action="{{ route('bookshop.staff.restock-requests.store') }}" class="space-y-6" id="restock-form">
            @csrf

            <div id="book-rows-container" class="space-y-4">
                @php
                    $oldItems = old('items', []);
                    // Ensure we always render at least 1 row, or the number of rows from a failed validation
                    $initialRows = is_array($oldItems) ? max(1, count($oldItems)) : 1;
                    // Prefill support for the "Request Stock" quick-action from the
                    // browsable catalog (?book_id=&book_title=&quantity=) - only
                    // applied via JS below, and only when there's no old() data
                    // taking precedence (a failed-validation redisplay always wins).
                    $prefillBookId = ! old('items') ? request()->integer('book_id') : null;
                    $prefillBookTitle = request()->string('book_title');
                    $prefillQuantity = request()->integer('quantity');
                @endphp

                @for($i = 0; $i < $initialRows; $i++)
                    <div class="grid sm:grid-cols-5 gap-3 items-end pb-4 border-b border-slate-100 dark:border-slate-800 book-row">
                        <div class="sm:col-span-3">
                            <x-bookshop::book-picker
                                id-prefix="restock-item-{{ $i }}"
                                name="items[{{ $i }}][book_id]"
                                :label="'Book #'.($i + 1)"
                                :value="old('items.'.$i.'.book_id', $i === 0 ? $prefillBookId : null)"
                                :value-label="$i === 0 ? $prefillBookTitle : null"
                                :show-hint="false"
                            />
                        </div>
                        <div class="sm:col-span-2 relative">
                            <label class="block text-xs text-slate-500 dark:text-slate-400 mb-1">Quantity</label>
                            <input type="number" name="items[{{ $i }}][quantity]" min="1"
                                   value="{{ old('items.'.$i.'.quantity', $i === 0 && $prefillQuantity ? $prefillQuantity : null) }}"
                                   class="w-full px-3 py-2.5 text-sm border border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-white" style="border-radius: 2px;">
                            
                            <button type="button" class="remove-row absolute -top-2 -right-2 text-slate-400 hover:text-red-500 p-1 transition-colors" title="Remove book" style="display: {{ $initialRows === 1 ? 'none' : 'block' }}">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </button>
                        </div>
                    </div>
                @endfor
            </div>

            <button type="button" id="add-book-btn"
                    class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-purple-700 dark:text-purple-300 bg-purple-50 dark:bg-purple-900/30 hover:bg-purple-100 dark:hover:bg-purple-900/50 transition-colors"
                    style="border-radius: 2px;">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                </svg>
                Add Book
            </button>

            <a href="{{ route('bookshop.staff.books.index') }}" class="inline-block ml-2 text-sm text-slate-500 dark:text-slate-400 underline">
                Browse the full catalog instead &rarr;
            </a>

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

    <!-- Template for dynamically added rows. Mirrors the markup rendered by
         <x-bookshop::book-picker> (including the -stock-info placeholder) so
         window.initBookPicker - exposed by that component's own script,
         loaded once on this page via the row(s) above - works identically
         on rows added here at runtime. -->
    <template id="book-row-template">
        <div class="grid sm:grid-cols-5 gap-3 items-end pb-4 border-b border-slate-100 dark:border-slate-800 book-row">
            <div class="sm:col-span-3">
                <label for="restock-item-__INDEX__-search" class="block text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2" style="letter-spacing: 0.08em;">
                    Book #<span class="row-number"></span>
                </label>
                <div data-book-picker="true" data-prefix="restock-item-__INDEX__" class="relative">
                    <input type="text" id="restock-item-__INDEX__-search" autocomplete="off"
                           placeholder="Search by title, author, or ISBN..."
                           class="w-full px-4 py-2.5 text-sm border border-slate-200 dark:border-slate-700 focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500 dark:bg-slate-800 dark:text-white"
                           style="border-radius: 2px;">
                    <input type="hidden" name="items[__INDEX__][book_id]" id="restock-item-__INDEX__-value" data-book-picker-value>
                    <p id="restock-item-__INDEX__-stock-info" class="mt-1 text-xs"></p>
                    <div id="restock-item-__INDEX__-results"
                         class="absolute z-10 w-full mt-1 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 hidden max-h-60 overflow-y-auto"
                         style="border-radius: 2px; box-shadow: 0 4px 12px rgba(0,0,0,0.1);"></div>
                </div>
            </div>
            <div class="sm:col-span-2 relative">
                <label class="block text-xs text-slate-500 dark:text-slate-400 mb-1">Quantity</label>
                <input type="number" name="items[__INDEX__][quantity]" min="1"
                       class="w-full px-3 py-2.5 text-sm border border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-white" style="border-radius: 2px;">
                <button type="button" class="remove-row absolute -top-2 -right-2 text-slate-400 hover:text-red-500 p-1 transition-colors" title="Remove book">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                </button>
            </div>
        </div>
    </template>

    @pushOnce('scripts')
    <script>
        // Dynamic row add/remove management. Book-picker search/render logic
        // itself lives in the <x-bookshop::book-picker> component's own
        // pushed script (which also exposes window.initBookPicker for us to
        // call on newly-cloned rows) - not duplicated here, so any future
        // improvement to the picker (like the warehouse-stock display) only
        // needs to be made in one place.
        document.addEventListener('DOMContentLoaded', () => {
            const container = document.getElementById('book-rows-container');
            if (!container) return;

            const template = document.getElementById('book-row-template');
            const addBtn = document.getElementById('add-book-btn');

            // Start indexing after the initially rendered rows
            let rowIndex = {{ $initialRows }};

            const updateRowNumbers = () => {
                const rows = container.querySelectorAll('.book-row');
                rows.forEach((row, index) => {
                    // Update visual "Book #N" label
                    const numSpan = row.querySelector('.row-number');
                    if (numSpan) {
                        numSpan.textContent = index + 1;
                    }
                    // Ensure at least one row always remains
                    const removeBtn = row.querySelector('.remove-row');
                    if (removeBtn) {
                        removeBtn.style.display = rows.length === 1 ? 'none' : 'block';
                    }
                });
            };

            addBtn.addEventListener('click', () => {
                const clone = template.content.cloneNode(true);
                const newRow = clone.querySelector('.book-row');

                // Replace the __INDEX__ placeholder with the unique rowIndex
                newRow.innerHTML = newRow.innerHTML.replace(/__INDEX__/g, rowIndex);

                container.appendChild(newRow);
                rowIndex++; // Increment for the next addition

                // Initialize the book picker functionality for this new row -
                // window.initBookPicker comes from the book-picker component's
                // own script, already loaded on this page.
                const newPicker = newRow.querySelector('[data-book-picker="true"]');
                if (newPicker && window.initBookPicker) {
                    window.initBookPicker(newPicker);
                }

                updateRowNumbers();
            });

            // Event delegation for remove buttons (works for dynamically added rows)
            container.addEventListener('click', (e) => {
                const removeBtn = e.target.closest('.remove-row');
                if (removeBtn) {
                    const row = removeBtn.closest('.book-row');
                    if (container.querySelectorAll('.book-row').length > 1) {
                        row.remove();
                        updateRowNumbers();
                    }
                }
            });

            // Initial setup to ensure correct state on page load (e.g., after validation errors)
            updateRowNumbers();
        });
    </script>
    @endPushOnce
</x-bookshop::layouts.staff>
