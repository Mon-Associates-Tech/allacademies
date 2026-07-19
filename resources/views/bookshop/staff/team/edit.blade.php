<x-bookshop::layouts.staff :title="'Edit Staff Member - BookShop'">
    <h1 class="text-xl font-bold text-slate-900 dark:text-white" style="font-family: 'Georgia', serif;">Edit — {{ $staffMember->name }}</h1>

    <div class="bg-white dark:bg-slate-900 p-6" style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 1px 6px rgba(0,0,0,0.04);">
        <form method="POST" action="{{ route('bookshop.staff.team.update', $staffMember) }}">
            @csrf @method('PUT')
            <x-bookshop::staff.team.form :staff-member="$staffMember" :branches="$branches" />
        </form>
    </div>
</x-bookshop::layouts.staff>
