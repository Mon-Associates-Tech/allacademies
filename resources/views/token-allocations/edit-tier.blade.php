<x-layouts.app>
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-8">
        <div class="container mx-auto px-4 max-w-4xl">
            <div class="mb-8">
                <a href="{{ route('token-allocations.index') }}" class="text-blue-600 hover:text-blue-700 dark:text-blue-400 text-sm flex items-center gap-2 mb-4">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Back to Token Allocations
                </a>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Edit Pricing Tier</h1>
            </div>

            <form action="{{ route('token-allocations.update-tier', $tier) }}" method="POST" class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 p-6">
                @csrf
                @method('PUT')

                <div class="space-y-6">
                    <x-form.input 
                        name="name" 
                        label="Tier Name" 
                        :value="$tier->name" 
                        required 
                    />

                    <x-form.input 
                        name="model" 
                        label="AI Model" 
                        :value="$tier->model" 
                        required 
                    />

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <x-form.input 
                            name="initial_price" 
                            label="Initial Price (First 6 Months)" 
                            type="number" 
                            step="0.01" 
                            min="0" 
                            :value="$tier->initial_price" 
                            required 
                        />

                        <x-form.input 
                            name="subsequent_price" 
                            label="Subsequent Price (After 6 Months)" 
                            type="number" 
                            step="0.01" 
                            min="0" 
                            :value="$tier->subsequent_price" 
                            required 
                        />
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <x-form.input 
                            name="monthly_token_limit" 
                            label="Monthly Token Limit" 
                            type="number" 
                            min="1" 
                            :value="$tier->monthly_token_limit" 
                            required 
                        />

                        <x-form.input 
                            name="initial_period_months" 
                            label="Initial Period (Months)" 
                            type="number" 
                            min="1" 
                            :value="$tier->initial_period_months" 
                            required 
                        />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Description</label>
                        <textarea 
                            name="description" 
                            rows="4" 
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">{{ $tier->description }}</textarea>
                    </div>

                    <div class="flex items-center gap-3">
                        <input 
                            type="checkbox" 
                            name="is_active" 
                            id="is_active" 
                            value="1" 
                            {{ $tier->is_active ? 'checked' : '' }} 
                            class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                        />
                        <label for="is_active" class="text-sm font-medium text-gray-700 dark:text-gray-300">Active</label>
                    </div>
                </div>

                <div class="mt-8 flex gap-4">
                    <button type="submit" class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors">
                        Update Tier
                    </button>
                    <a href="{{ route('token-allocations.index') }}" class="px-6 py-3 bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-900 dark:text-white rounded-lg font-medium transition-colors">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>
