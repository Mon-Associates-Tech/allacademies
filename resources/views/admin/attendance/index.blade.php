<x-layouts.app pageName="Attendance Overview">
    <x-slot name="title">Attendance Overview</x-slot>

    <div class="space-y-6">
        <p class="text-sm text-gray-600 dark:text-gray-300">Read-only attendance records for your selected school. Owners and superadmins see all schools when no school is selected. Teachers remain responsible for recording and editing attendance.</p>

        <form method="GET" class="grid grid-cols-1 gap-4 rounded-lg bg-white p-4 shadow dark:bg-gray-800 md:grid-cols-4">
            <div>
                <label for="search" class="mb-1 block text-sm font-medium">Teacher, class, or student</label>
                <input id="search" name="search" value="{{ request('search') }}" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700">
            </div>
            <div>
                <label for="start_date" class="mb-1 block text-sm font-medium">From date</label>
                <input id="start_date" name="start_date" type="date" value="{{ request('start_date') }}" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700">
            </div>
            <div>
                <label for="end_date" class="mb-1 block text-sm font-medium">To date</label>
                <input id="end_date" name="end_date" type="date" value="{{ request('end_date') }}" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700">
            </div>
            <div>
                <label for="status" class="mb-1 block text-sm font-medium">Student status</label>
                <select id="status" name="status" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700">
                    <option value="">All statuses</option>
                    @foreach(['present', 'absent', 'late', 'excused'] as $status)
                        <option value="{{ $status }}" @selected(request('status') === $status)>{{ ucfirst($status) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-end gap-2 md:col-span-4">
                <button class="rounded-lg bg-violet-600 px-4 py-2 text-sm font-medium text-white hover:bg-violet-700">Apply filters</button>
                <a href="{{ route('admin.attendance.index') }}" class="rounded-lg bg-gray-100 px-4 py-2 text-sm dark:bg-gray-700">Reset</a>
            </div>
        </form>

        <div class="space-y-4">
            @forelse($attendances as $attendance)
                <section class="overflow-hidden rounded-lg bg-white shadow dark:bg-gray-800">
                    <div class="flex flex-wrap items-start justify-between gap-4 border-b border-gray-200 px-5 py-4 dark:border-gray-700">
                        <div>
                            <h2 class="font-semibold text-gray-900 dark:text-white">{{ $attendance->academicLevel?->name ?? 'Class' }}{{ $attendance->academicSubject ? ' · '.$attendance->academicSubject->name : '' }}</h2>
                            <p class="mt-1 text-sm text-gray-600 dark:text-gray-300">
                                {{ $attendance->date->format('M d, Y') }} · {{ ucfirst(str_replace('_', ' ', $attendance->session)) }} · {{ $attendance->teacher?->user?->name ?? 'Teacher unavailable' }}
                            </p>
                            @if($attendance->remarks)<p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ $attendance->remarks }}</p>@endif
                        </div>
                        <div class="flex flex-wrap gap-3 text-xs">
                            <span class="rounded-full bg-green-100 px-3 py-1 text-green-800 dark:bg-green-900/40 dark:text-green-200">Present {{ $attendance->present_count }}</span>
                            <span class="rounded-full bg-red-100 px-3 py-1 text-red-800 dark:bg-red-900/40 dark:text-red-200">Absent {{ $attendance->absent_count }}</span>
                            <span class="rounded-full bg-amber-100 px-3 py-1 text-amber-800 dark:bg-amber-900/40 dark:text-amber-200">Late {{ $attendance->late_count }}</span>
                            <span class="rounded-full bg-blue-100 px-3 py-1 text-blue-800 dark:bg-blue-900/40 dark:text-blue-200">Excused {{ $attendance->excused_count }}</span>
                        </div>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm">
                            <thead class="bg-gray-50 text-xs uppercase text-gray-500 dark:bg-gray-700 dark:text-gray-300">
                            <tr><th class="px-5 py-3">Student</th><th class="px-5 py-3">Student ID</th><th class="px-5 py-3">Status</th><th class="px-5 py-3">Remarks</th></tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse($attendance->attendanceRecords as $record)
                                <tr>
                                    <td class="px-5 py-3">{{ $record->student?->user?->name ?? 'Student unavailable' }}</td>
                                    <td class="px-5 py-3">{{ $record->student?->student_id ?? '—' }}</td>
                                    <td class="px-5 py-3 capitalize">{{ $record->status }}</td>
                                    <td class="px-5 py-3">{{ $record->remarks ?: '—' }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="px-5 py-5 text-center text-gray-500">No student records for this session.</td></tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </section>
            @empty
                <div class="rounded-lg bg-white p-8 text-center text-sm text-gray-500 shadow dark:bg-gray-800">No attendance records match these filters.</div>
            @endforelse
        </div>

        <div>{{ $attendances->links() }}</div>
    </div>
</x-layouts.app>