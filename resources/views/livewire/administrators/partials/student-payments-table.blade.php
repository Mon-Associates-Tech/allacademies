<table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
    <thead class="bg-gray-50 dark:bg-gray-900">
    <tr>
        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Student</th>
        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Academic Info</th>
        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Total Expected</th>
        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Amount Paid</th>
        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Outstanding</th>
        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Payment Records</th>
        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Actions</th>
    </tr>
    </thead>
    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
    @foreach($studentPayments as $student)
        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 {{ $student->payment_status === 'overdue' ? 'bg-red-50 dark:bg-red-900/10' : '' }}">
            <td class="px-6 py-4 whitespace-nowrap">
                <div class="flex items-center">
                    <div>
                        <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                            {{ $student->user->name ?? 'N/A' }}
                        </div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">
                            {{ $student->student_id ?? 'N/A' }}
                        </div>
                    </div>
                </div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-sm text-gray-900 dark:text-gray-100">
                    {{ $student->academicGroup->name ?? 'N/A' }}
                </div>
                <div class="text-xs text-gray-500 dark:text-gray-400">
                    {{ $student->academicLevel->name ?? 'N/A' }}
                </div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                GHS {{ number_format($student->payment_summary['total_expected'], 2) }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-green-600 dark:text-green-400">
                GHS {{ number_format($student->payment_summary['total_paid'], 2) }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium {{ $student->payment_summary['total_outstanding'] > 0 ? 'text-red-600 dark:text-red-400' : 'text-green-600 dark:text-green-400' }}">
                GHS {{ number_format($student->payment_summary['total_outstanding'], 2) }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                {{ $student->payment_summary['records_count'] }} record(s)
                @if($student->payment_summary['overdue_count'] > 0)
                    <div class="text-xs text-red-600 dark:text-red-400 font-semibold">
                        {{ $student->payment_summary['overdue_count'] }} overdue
                    </div>
                @endif
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
                @if($student->payment_status === 'no_obligations')
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200">
                        No Obligations
                    </span>
                @elseif($student->payment_status === 'paid')
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200">
                        Fully Paid
                    </span>
                @elseif($student->payment_status === 'partial')
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-200">
                        Partial Payment
                    </span>
                @elseif($student->payment_status === 'overdue')
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200">
                        Overdue
                    </span>
                @else
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-orange-100 dark:bg-orange-900 text-orange-800 dark:text-orange-200">
                        Unpaid
                    </span>
                @endif
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                <a href="{{ route('students.show', $student->id) }}"
                   class="text-blue-600 dark:text-blue-400 hover:text-blue-900 dark:hover:text-blue-300">
                    View Details
                </a>
            </td>
        </tr>
    @endforeach
    </tbody>
</table>
