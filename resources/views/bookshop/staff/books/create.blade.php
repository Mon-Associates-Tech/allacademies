<x-bookshop::layouts.staff :title="'New Book - BookShop'">
    <h1 class="text-xl font-bold text-slate-900 dark:text-white" style="font-family: 'Georgia', serif;">New Book</h1>

    <div class="bg-white dark:bg-slate-900 p-6" style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 1px 6px rgba(0,0,0,0.04);">
        <form method="POST" action="{{ route('bookshop.staff.books.store') }}">
            @csrf
            <x-bookshop::staff.books.form :categories="$categories" />
        </form>
    </div>
</x-bookshop::layouts.staff>
