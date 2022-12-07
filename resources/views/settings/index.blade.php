<x-dashboard title="Settings" summary="System Settings">
    <table class="w-full divide-y divide-gray-300">
        <caption>
            <div class="flex items-center justify-between px-2 py-3">
                <div class="font-medium text-gray-500 tracking-wide">
                    System settings
                </div>
                <div>
                </div>
            </div>
        </caption>
        <thead>
            <tr>
                <x-table.th>Name</x-table.th>
                <x-table.th><span class="sr-only">Actions</span></x-table.th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
            <tr>
                <td class="p-2 text-sm text-gray-900 font-medium">Role</td>
                <td class="p-2 text-sm text-primary-600 space-x-3">
                    <a href="{{ route('settings.role') }}">Change</a>
                </td>
            </tr>
        </tbody>
    </table>
</x-dashboard>