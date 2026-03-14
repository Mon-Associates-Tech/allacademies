<x-layouts.app pageName="Financial Aid Details">
    <x-slot name="title">Financial Aid Details</x-slot>

    <div class="space-y-6">
        <!-- Aid Info -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ $financialAid->name }}</h3>
                    @if($financialAid->status === 'active')
                        <span class="px-3 py-1 text-sm font-medium rounded-full bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400">Active</span>
                    @else
                        <span class="px-3 py-1 text-sm font-medium rounded-full bg-gray-100 text-gray-800 dark:bg-gray-900/30 dark:text-gray-400">Inactive</span>
                    @endif
                </div>
            </div>
            <div class="p-6 space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Code</label>
                        <p class="mt-1 text-sm font-mono text-gray-900 dark:text-white">{{ $financialAid->code }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Target Amount</label>
                        <p class="mt-1 text-lg font-bold text-gray-900 dark:text-white">GH₵ {{ number_format($financialAid->amount, 2) }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Amount Raised</label>
                        <p class="mt-1 text-lg font-bold text-green-600 dark:text-green-400">GH₵ {{ number_format($financialAid->amount_raised, 2) }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Amount Remaining</label>
                        <p class="mt-1 text-lg font-bold text-gray-900 dark:text-white">GH₵ {{ number_format($financialAid->amount_left, 2) }}</p>
                    </div>
                </div>

                @if($financialAid->description)
                <div>
                    <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Description</label>
                    <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $financialAid->description }}</p>
                </div>
                @endif

                <!-- Progress Bar -->
                <div>
                    <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Progress</label>
                    <div class="flex items-center gap-4">
                        <div class="flex-1 bg-gray-200 dark:bg-gray-700 rounded-full h-4">
                            <div class="bg-blue-600 h-4 rounded-full transition-all duration-300" style="width: {{ $financialAid->progress_percentage }}%"></div>
                        </div>
                        <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $financialAid->progress_percentage }}%</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Beneficiaries -->
        @if($financialAid->beneficiaries->count() > 0)
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Beneficiaries ({{ $financialAid->beneficiaries->count() }})</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 dark:bg-gray-700/50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Student ID</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Name</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Group/Level</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($financialAid->beneficiaries as $student)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-mono text-gray-900 dark:text-gray-100">
                                    {{ $student->student_id }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                    {{ $student->user->name ?? 'N/A' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                    {{ $student->academicGroup->name ?? 'N/A' }} / {{ $student->academicLevel->name ?? 'N/A' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <a href="{{ route('accountant.students.show', $student) }}" class="text-blue-600 hover:text-blue-700 dark:text-blue-400">View</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

        <!-- Actions -->
        <div class="flex gap-4">
            <a href="{{ route('accountant.financial-aid.index') }}" class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600">
                Back to Financial Aid
            </a>
            <a href="{{ route('accountant.financial-aid.edit', $financialAid) }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                Edit Program
            </a>
            <a href="{{ route('accountant.financial-aid.beneficiaries', $financialAid) }}" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                Manage Beneficiaries
            </a>
        </div>
    </div>
</x-layouts.app>
