<x-layouts.app pageName="Student Details">
    <x-slot name="title">Student Details</x-slot>

    <div class="space-y-6">
        <!-- Student Info -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Student Information</h3>
            </div>
            <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Student ID</label>
                    <p class="mt-1 text-sm font-mono text-gray-900 dark:text-white">{{ $student->student_id }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Name</label>
                    <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $student->user->name ?? 'N/A' }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Email</label>
                    <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $student->user->email ?? 'N/A' }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Phone</label>
                    <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $student->phone ?? 'N/A' }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Academic Group</label>
                    <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $student->academicGroup->name ?? 'N/A' }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Academic Level</label>
                    <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $student->academicLevel->name ?? 'N/A' }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Status</label>
                    <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ ucfirst($student->status) }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Admission Date</label>
                    <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $student->admission_date?->format('M d, Y') ?? 'N/A' }}</p>
                </div>
            </div>
        </div>

        <!-- Parent Info -->
        @if($student->parent_name || $student->parent_email)
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Parent/Guardian Information</h3>
            </div>
            <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Name</label>
                    <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $student->parent_name ?? 'N/A' }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Email</label>
                    <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $student->parent_email ?? 'N/A' }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Phone</label>
                    <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $student->parent_phone ?? 'N/A' }}</p>
                </div>
            </div>
        </div>
        @endif

        <!-- Actions -->
        <div class="flex gap-4">
            <a href="{{ route('accountant.students.index') }}" class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600">
                Back to Students
            </a>
            <a href="{{ route('accountant.students.payments', $student) }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                View Payments
            </a>
        </div>
    </div>
</x-layouts.app>
