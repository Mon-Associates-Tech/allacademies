<div>
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100">General Exam Pricing Tiers</h2>
        <button wire:click="openCreate"
                class="px-4 py-2 text-sm font-medium text-white bg-violet-600 rounded-lg hover:bg-violet-700">
            Add Tier
        </button>
    </div>

    @if($showForm)
        <div class="mb-6 p-5 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm">
            <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-4">
                {{ $editingId ? 'Edit Tier' : 'New Tier' }}
            </h3>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Subject Count</label>
                    <input type="number" wire:model="subjectCount" min="1" max="20"
                           class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 text-sm" />
                    @error('subjectCount') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Price Per Student (GHS) — Online</label>
                    <input type="number" step="0.01" wire:model="pricePerStudent"
                           class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 text-sm" />
                    @error('pricePerStudent') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Print Flat Rate (GHS) — Per Subject</label>
                    <input type="number" step="0.01" wire:model="printFlatRate"
                           class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 text-sm" />
                    @error('printFlatRate') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>
            <div class="flex items-center gap-3 mt-4">
                <label class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400">
                    <input type="checkbox" wire:model="isActive" class="rounded border-gray-300 dark:border-gray-600" />
                    Active
                </label>
                <div class="flex gap-2 ml-auto">
                    <button wire:click="$set('showForm', false)"
                            class="px-4 py-2 text-sm text-gray-600 dark:text-gray-400 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700">
                        Cancel
                    </button>
                    <button wire:click="save"
                            class="px-4 py-2 text-sm font-medium text-white bg-violet-600 rounded-lg hover:bg-violet-700">
                        Save
                    </button>
                </div>
            </div>
        </div>
    @endif

    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 dark:bg-gray-700/50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Subjects</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Online (per student)</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Print (flat rate/subject)</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Status</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                @forelse($tiers as $tier)
                    <tr wire:key="tier-{{ $tier['id'] }}" class="hover:bg-gray-50 dark:hover:bg-gray-700/30">
                        <td class="px-4 py-3 font-medium text-gray-800 dark:text-gray-100">{{ $tier['subject_count'] }} subject(s)</td>
                        <td class="px-4 py-3 text-gray-600 dark:text-gray-300">GHS {{ number_format($tier['price_per_student'], 2) }}</td>
                        <td class="px-4 py-3 text-gray-600 dark:text-gray-300">GHS {{ number_format($tier['print_flat_rate'], 2) }}</td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-0.5 rounded-full text-xs font-medium {{ $tier['is_active'] ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400' : 'bg-gray-100 text-gray-500 dark:bg-gray-700 dark:text-gray-400' }}">
                                {{ $tier['is_active'] ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-2 justify-end">
                                <button wire:click="openEdit({{ $tier['id'] }})"
                                        class="text-xs text-violet-600 dark:text-violet-400 hover:underline">Edit</button>
                                <button wire:click="toggleActive({{ $tier['id'] }})"
                                        class="text-xs text-gray-500 dark:text-gray-400 hover:underline">
                                    {{ $tier['is_active'] ? 'Disable' : 'Enable' }}
                                </button>
                                <button wire:click="delete({{ $tier['id'] }})"
                                        wire:confirm="Delete this tier?"
                                        class="text-xs text-red-500 hover:underline">Delete</button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-8 text-center text-sm text-gray-400 dark:text-gray-500">
                            No pricing tiers configured yet.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
