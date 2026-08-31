@props([
    'route',            // concrete route name to link to, e.g. 'bookshop.staff.books.index'
    'active' => null,   // pattern(s) to match for "current" styling; defaults to $route.
                         // Pass an array for links that should stay highlighted across
                         // several routes, e.g. :active="['bookshop.staff.books.*']"
                         // so editing a book still shows "Books" as active.
    'variant' => 'pill', // 'pill' (horizontal top-nav underline style, default) or 'dropdown'
])

@php
    $patterns = $active ?? $route;
    $patterns = is_array($patterns) ? $patterns : [$patterns];
    $isActive = request()->routeIs(...$patterns);

    $pillClasses = 'text-sm pb-1 border-b-2 transition-colors whitespace-nowrap '
        .($isActive ? 'text-white border-purple-400 font-semibold' : 'text-slate-300 border-transparent hover:text-white');

    $dropdownClasses = 'block px-4 py-2.5 text-sm transition-colors '
        .($isActive
            ? 'text-purple-700 dark:text-purple-300 bg-purple-50 dark:bg-purple-900/30 font-semibold'
            : 'text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-800');
@endphp

<a href="{{ route($route) }}" class="{{ $variant === 'dropdown' ? $dropdownClasses : $pillClasses }}">
    {{ $slot }}
</a>
