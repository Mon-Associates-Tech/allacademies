@props([
    'label',           // trigger button text, e.g. 'Catalog'
    'active' => [],    // route pattern(s) that should keep the TRIGGER highlighted
                        // even while the dropdown itself is closed, e.g. when the
                        // current page is one of the items inside it
])

@php
    $patterns = is_array($active) ? $active : [$active];
    $isActive = ! empty($patterns) && request()->routeIs(...$patterns);
@endphp

<div class="relative" data-nav-dropdown="true">
    <button type="button" data-nav-dropdown-toggle
            class="flex items-center gap-1 text-sm pb-1 border-b-2 transition-colors whitespace-nowrap {{ $isActive ? 'text-white border-purple-400 font-semibold' : 'text-slate-300 border-transparent hover:text-white' }}">
        {{ $label }}
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
        </svg>
    </button>

    <div data-nav-dropdown-panel
         class="hidden absolute left-0 mt-2 w-48 bg-white dark:bg-slate-900 z-20 py-1"
         style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 4px 16px rgba(0,0,0,0.15);">
        {{ $slot }}
    </div>
</div>

@once
    <script>
        (function initNavDropdowns() {
            const init = (wrapper) => {
                if (!wrapper || wrapper.dataset.ready) return;
                wrapper.dataset.ready = 'true';

                const toggle = wrapper.querySelector('[data-nav-dropdown-toggle]');
                const panel = wrapper.querySelector('[data-nav-dropdown-panel]');

                toggle.addEventListener('click', (e) => {
                    e.stopPropagation();
                    // Close any other open nav dropdown before opening this one,
                    // same "only one thing open at a time" behavior as the
                    // notification bell.
                    document.querySelectorAll('[data-nav-dropdown-panel]').forEach((p) => {
                        if (p !== panel) p.classList.add('hidden');
                    });
                    panel.classList.toggle('hidden');
                });

                document.addEventListener('click', (e) => {
                    if (!wrapper.contains(e.target)) panel.classList.add('hidden');
                });
            };

            const hydrate = () => document.querySelectorAll('[data-nav-dropdown="true"]').forEach(init);
            document.addEventListener('DOMContentLoaded', hydrate);
            setTimeout(hydrate, 0);
        })();
    </script>
@endonce
