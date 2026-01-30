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
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Assign Tokens to Users</h1>
            </div>

            <form action="{{ route('token-allocations.store-assignment') }}" method="POST" class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 p-6" x-data="{ assignmentType: 'new_cycle' }">
                @csrf

                <div class="space-y-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Select Users *</label>
                        <livewire:common.searchable-multi-select
                            :lazy-load="true"
                            model-class="App\Models\User"
                            :search-column="['name', 'email']"
                            name="user_ids"
                            placeholder="Search and select users..."
                            :multiple="true"
                            :required="true"
                            label-key="name"
                            value-key="id"
                        />
                        @error('user_ids')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Assignment Type *</label>
                        <div class="flex gap-4">
                            <label class="flex items-center gap-2">
                                <input 
                                    type="radio" 
                                    name="assignment_type" 
                                    value="new_cycle" 
                                    x-model="assignmentType"
                                    checked 
                                    class="w-4 h-4 text-blue-600 border-gray-300 focus:ring-blue-500"
                                />
                                <span class="text-sm text-gray-700 dark:text-gray-300">New Subscription Cycle</span>
                            </label>
                            <label class="flex items-center gap-2">
                                <input 
                                    type="radio" 
                                    name="assignment_type" 
                                    value="topup" 
                                    x-model="assignmentType"
                                    class="w-4 h-4 text-blue-600 border-gray-300 focus:ring-blue-500"
                                />
                                <span class="text-sm text-gray-700 dark:text-gray-300">Topup to Existing Cycle</span>
                            </label>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Pricing Tier *</label>
                        <select 
                            name="pricing_tier_id" 
                            required 
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="">Select a tier</option>
                            @foreach($pricingTiers as $tier)
                                <option value="{{ $tier->id }}">{{ $tier->name }} - {{ number_format($tier->monthly_token_limit) }} tokens/month</option>
                            @endforeach
                        </select>
                    </div>

                    <x-form.input 
                        name="tokens" 
                        label="Number of Tokens" 
                        type="number" 
                        min="1" 
                        required 
                    />

                    <div x-show="assignmentType === 'new_cycle'" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <x-form.input 
                            name="cycle_start_date" 
                            label="Start Date" 
                            type="date" 
                            :value="now()->format('Y-m-d')" 
                            required 
                        />

                        <x-form.input 
                            name="cycle_end_date" 
                            label="End Date" 
                            type="date" 
                            :value="now()->addDays(30)->format('Y-m-d')" 
                            required 
                        />
                    </div>

                    <div x-show="assignmentType === 'new_cycle'" class="flex items-center gap-3">
                        <input 
                            type="checkbox" 
                            name="is_trial" 
                            id="is_trial" 
                            value="1" 
                            class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                        />
                        <label for="is_trial" class="text-sm font-medium text-gray-700 dark:text-gray-300">Mark as Trial</label>
                    </div>

                    <div x-show="assignmentType === 'new_cycle'">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Status *</label>
                        <select 
                            name="status" 
                            required 
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                </div>

                <div class="mt-8 flex gap-4">
                    <button type="submit" class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors">
                        Assign Tokens
                    </button>
                    <a href="{{ route('token-allocations.index') }}" class="px-6 py-3 bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-900 dark:text-white rounded-lg font-medium transition-colors">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('livewire:init', () => {
            Livewire.on('selection-changed', (event) => {
                if (event.name === 'user_ids') {
                    const form = document.querySelector('form');
                    form.querySelectorAll('input[name="user_ids[]"]').forEach(el => el.remove());
                    
                    event.selected.forEach(userId => {
                        const input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = 'user_ids[]';
                        input.value = userId;
                        form.appendChild(input);
                    });
                }
            });
        });
    </script>
    @endpush
</x-layouts.app>
