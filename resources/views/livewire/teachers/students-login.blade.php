<div class="p-6">
    <div class="mb-4 flex justify-between items-center">
        <h2 class="text-2xl font-bold">Student Activities</h2>
        <div class="flex space-x-4">
            <input type="text"
                   wire:model.debounce.300ms="searchTerm"
                   class="rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                   placeholder="Search student activities...">
        </div>
    </div>

    <x-user-logins :activities="$activities" />

    <div class="mt-4">
        {{ $activities->links() }}
    </div>
</div>
