<x-layouts.app pageName="Edit Financial Aid">
    <x-slot name="title">Edit Financial Aid Program</x-slot>

    <div class="max-w-3xl">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
            <form method="POST" action="{{ route('accountant.financial-aid.update', $financialAid) }}" class="p-6 space-y-6">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Program Name *</label>
                    <input type="text" name="name" value="{{ old('name', $financialAid->name) }}" required class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white @error('name') border-red-500 @enderror">
                    @error('name')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Description</label>
                    <textarea name="description" rows="4" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white @error('description') border-red-500 @enderror">{{ old('description', $financialAid->description) }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Target Amount (GH₵) *</label>
                    <input type="number" name="amount" value="{{ old('amount', $financialAid->amount) }}" step="0.01" min="0" required class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white @error('amount') border-red-500 @enderror">
                    @error('amount')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Payment Structure (Optional)</label>
                    <select name="school_payment_structure_id" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white @error('school_payment_structure_id') border-red-500 @enderror">
                        <option value="">-- None --</option>
                        @foreach($paymentStructures as $structure)
                            <option value="{{ $structure->id }}" {{ old('school_payment_structure_id', $financialAid->school_payment_structure_id) == $structure->id ? 'selected' : '' }}>
                                {{ $structure->name }} - {{ ucwords(str_replace('_', ' ', $structure->payment_type)) }} (GH₵ {{ number_format($structure->amount, 2) }})
                            </option>
                        @endforeach
                    </select>
                    @error('school_payment_structure_id')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Amount Raised</label>
                    <p class="text-lg font-bold text-green-600 dark:text-green-400">GH₵ {{ number_format($financialAid->amount_raised, 2) }}</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Status *</label>
                    <select name="status" required class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white @error('status') border-red-500 @enderror">
                        <option value="active" {{ old('status', $financialAid->status) === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ old('status', $financialAid->status) === 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                    @error('status')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex gap-4 pt-4">
                    <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        Update Program
                    </button>
                    <a href="{{ route('accountant.financial-aid.show', $financialAid) }}" class="px-6 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>
