@props([
    'name' => 'book_id',
    'idPrefix' => 'book-picker',
    'label' => 'Book',
    'required' => false,
    'value' => null,       // pre-selected book_id, e.g. when re-showing a form after a validation error
    'valueLabel' => null,  // pre-selected display text for the search box
    'showHint' => true,
])

<div data-book-picker="true" data-prefix="{{ $idPrefix }}" class="relative">
    @if($label)
        <label for="{{ $idPrefix }}-search" class="block text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2" style="letter-spacing: 0.08em;">
            {{ $label }}
        </label>
    @endif

    <input type="text" id="{{ $idPrefix }}-search" autocomplete="off"
           placeholder="Search by title, author, or ISBN..."
           value="{{ $valueLabel }}"
           class="w-full px-4 py-2.5 text-sm border border-slate-200 dark:border-slate-700 focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500 dark:bg-slate-800 dark:text-white"
           style="border-radius: 2px;">

    <input type="hidden" name="{{ $name }}" id="{{ $idPrefix }}-value" data-book-picker-value
           value="{{ $value }}" {{ $required ? 'required' : '' }}>

    <div id="{{ $idPrefix }}-results"
         class="absolute z-10 w-full mt-1 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 hidden max-h-60 overflow-y-auto"
         style="border-radius: 2px; box-shadow: 0 4px 12px rgba(0,0,0,0.1);"></div>

    @if($showHint)
    <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Type at least 2 characters, then pick a result — that's what sets the book, not the text itself.</p>
    @endif
</div>

@pushOnce('scripts')
    <script>
        (function initBookPickers() {
            const init = (wrapper) => {
                if (!wrapper || wrapper.dataset.ready) return;
                wrapper.dataset.ready = 'true';

                const prefix = wrapper.dataset.prefix;
                const search = document.getElementById(prefix + '-search');
                const hidden = document.getElementById(prefix + '-value');
                const results = document.getElementById(prefix + '-results');
                let debounceTimer = null;
                let currentItems = [];

                const hideResults = () => results.classList.add('hidden');

                const renderResults = (books) => {
                    currentItems = books;

                    if (!books.length) {
                        results.innerHTML = '<div class="px-4 py-3 text-sm text-slate-500 dark:text-slate-400">No matches found.</div>';
                        results.classList.remove('hidden');
                        return;
                    }

                    results.innerHTML = books.map((book, i) => `
                        <button type="button" data-index="${i}"
                                class="w-full text-left px-4 py-2.5 text-sm hover:bg-slate-50 dark:hover:bg-slate-700 border-b border-slate-100 dark:border-slate-700 last:border-0">
                            <span class="font-semibold text-slate-900 dark:text-white">${escapeHtml(book.title)}</span>
                            ${book.author ? '<span class="text-slate-500 dark:text-slate-400"> — ' + escapeHtml(book.author) + '</span>' : ''}
                        </button>
                    `).join('');
                    results.classList.remove('hidden');

                    results.querySelectorAll('button[data-index]').forEach((btn) => {
                        btn.addEventListener('click', () => {
                            const book = currentItems[parseInt(btn.dataset.index, 10)];
                            hidden.value = book.id;
                            search.value = book.title;
                            hideResults();
                        });
                    });
                };

                const escapeHtml = (str) => {
                    const div = document.createElement('div');
                    div.textContent = str;
                    return div.innerHTML;
                };

                search.addEventListener('input', () => {
                    // Any manual edit invalidates the previously selected
                    // book - force picking a fresh result rather than
                    // silently submitting a stale ID that no longer
                    // matches what's shown in the box.
                    hidden.value = '';
                    clearTimeout(debounceTimer);

                    const q = search.value.trim();
                    if (q.length < 2) {
                        hideResults();
                        return;
                    }

                    debounceTimer = setTimeout(() => {
                        fetch(`{{ route('bookshop.staff.books.search') }}?q=${encodeURIComponent(q)}`)
                            .then((r) => r.json())
                            .then(renderResults)
                            .catch((err) => console.error('book search error', err));
                    }, 250);
                });

                search.addEventListener('focus', () => {
                    if (currentItems.length && search.value.trim().length >= 2) {
                        results.classList.remove('hidden');
                    }
                });

                document.addEventListener('click', (e) => {
                    if (!wrapper.contains(e.target)) hideResults();
                });
            };

            const hydrate = () => document.querySelectorAll('[data-book-picker="true"]').forEach(init);
            document.addEventListener('DOMContentLoaded', hydrate);
            setTimeout(hydrate, 0);
        })();
    </script>
@endPushOnce
