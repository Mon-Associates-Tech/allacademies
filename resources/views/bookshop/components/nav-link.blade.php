@props([
    'route',            // concrete route name to link to, e.g. 'bookshop.staff.books.index'
    'active' => null,   // pattern(s) to match for "current" styling; defaults to $route.
                         // Pass an array for links that should stay highlighted across
                         // several routes, e.g. :active="['bookshop.staff.books.*']"
                         // so editing a book still shows "Books" as active.
])

@php
    $patterns = $active ?? $route;
    $patterns = is_array($patterns) ? $patterns : [$patterns];
    $isActive = request()->routeIs(...$patterns);
@endphp

<a href="{{ route($route) }}"
   class="text-sm pb-1 border-b-2 transition-colors whitespace-nowrap {{ $isActive ? 'text-white border-purple-400 font-semibold' : 'text-slate-300 border-transparent hover:text-white' }}">
    {{ $slot }}
</a>
