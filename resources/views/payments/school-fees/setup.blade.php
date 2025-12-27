<x-layouts.app page-name="Setup Fee Structure">
    <div class="max-w-3xl mx-auto bg-white rounded-2xl shadow-lg p-8 mt-10">
        <h2 class="text-2xl font-bold text-gray-800 mb-6">Setup School Fee Structure</h2>

        @if(session('success'))
            <div class="mb-4 p-3 bg-green-100 text-green-700 rounded-lg">
                {{ session('success') }}
            </div>
        @endif

        <form method="POST" action="{{ route('school.fee-setup.store') }}" class="space-y-6">
            @csrf

            <div>
                <label class="block text-sm font-medium text-gray-700">Academic Group</label>
                <select name="academic_group_id" required class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">Select Group</option>
                    @foreach($academicGroups as $group)
                        <option value="{{ $group->id }}">{{ $group->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Academic Level</label>
                <select name="academic_level_id" required class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">Select Level</option>
                    @foreach($academicLevels as $level)
                        <option value="{{ $level->id }}">{{ $level->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Term</label>
                <select name="current_term_id" required class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">Select Term</option>
                    @foreach($academicTerms as $term)
                        <option value="{{ $term->id }}">{{ $term->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Amount (₵)</label>
                <input type="number" name="amount" step="0.01" required class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500" />
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Due Date</label>
                <input type="date" name="due_date" required class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500" />
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Payment Method</label>
                <select name="payment_method" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="Momo">Momo</option>
                    <option value="Paystack">Paystack</option>
                    <option value="Cash">Cash</option>
                </select>
            </div>

            <div class="pt-4">
                <button type="submit" class="px-6 py-3 bg-indigo-600 text-white rounded-xl shadow hover:bg-indigo-700 transition">
                    Save Fee Structure
                </button>
            </div>
        </form>
    </div>
</x-layouts.app>
