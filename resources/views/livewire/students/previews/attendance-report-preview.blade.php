<div class="bg-white p-8 max-w-4xl mx-auto">
    <!-- Header -->
    <div class="text-center border-b-2 border-gray-800 pb-4 mb-6">
        <h1 class="text-2xl font-bold text-gray-900">{{ $student->school->name ?? 'School Name' }}</h1>
        <h2 class="text-xl font-semibold text-gray-700 mt-2">STUDENT ATTENDANCE REPORT</h2>
        @if($selectedAcademicYearId)
            <p class="text-sm text-gray-600 mt-2">
                Academic Year: {{ \App\Models\AcademicYear::find($selectedAcademicYearId)?->name ?? 'N/A' }}
            </p>
        @endif
        <p class="text-sm text-gray-500 mt-1">Generated: {{ now()->format('F d, Y') }}</p>
    </div>

    <!-- Student Info -->
    <div class="grid grid-cols-2 gap-4 mb-6 bg-gray-50 p-4 rounded-lg">
        <div>
            <div class="space-y-2 text-sm">
                <div class="flex">
                    <span class="font-semibold w-32">Student Name:</span>
                    <span>{{ $student->user->name }}</span>
                </div>
                <div class="flex">
                    <span class="font-semibold w-32">Student ID:</span>
                    <span>{{ $student->student_id ?? 'N/A' }}</span>
                </div>
            </div>
        </div>
        <div>
            <div class="space-y-2 text-sm">
                <div class="flex">
                    <span class="font-semibold w-32">Academic Level:</span>
                    <span>{{ $student->academicLevel->name ?? 'N/A' }}</span>
                </div>
                <div class="flex">
                    <span class="font-semibold w-32">Class:</span>
                    <span>{{ $student->studentGroup->name ?? 'N/A' }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-4 gap-4 mb-6">
        <div class="bg-gray-100 border border-gray-300 rounded-lg p-4 text-center">
            <div class="text-3xl font-bold text-gray-900">{{ $previewData['summary']['total'] }}</div>
            <div class="text-xs text-gray-600 mt-2 uppercase font-semibold">Total Sessions</div>
        </div>
        <div class="bg-green-50 border border-green-300 rounded-lg p-4 text-center">
            <div class="text-3xl font-bold text-green-600">{{ $previewData['summary']['present'] }}</div>
            <div class="text-xs text-gray-600 mt-2 uppercase font-semibold">Present</div>
        </div>
        <div class="bg-red-50 border border-red-300 rounded-lg p-4 text-center">
            <div class="text-3xl font-bold text-red-600">{{ $previewData['summary']['absent'] }}</div>
            <div class="text-xs text-gray-600 mt-2 uppercase font-semibold">Absent</div>
        </div>
        <div class="bg-indigo-50 border border-indigo-300 rounded-lg p-4 text-center">
            <div class="text-3xl font-bold text-indigo-600">{{ $previewData['summary']['rate'] }}%</div>
            <div class="text-xs text-gray-600 mt-2 uppercase font-semibold">Attendance Rate</div>
        </div>
    </div>

    <!-- Visual Progress Bar -->
    <div class="mb-6 bg-gray-50 p-4 rounded-lg">
        <h3 class="text-sm font-bold text-gray-700 mb-3">Attendance Overview</h3>
        <div class="flex h-8 rounded-lg overflow-hidden">
            @php
                $totalSessions = $previewData['summary']['total'] ?: 1;
                $presentWidth = ($previewData['summary']['present'] / $totalSessions) * 100;
                $absentWidth = ($previewData['summary']['absent'] / $totalSessions) * 100;
                $lateWidth = (($previewData['summary']['late'] ?? 0) / $totalSessions) * 100;
            @endphp

            @if($previewData['summary']['present'] > 0)
                <div class="bg-green-500 flex items-center justify-center text-white text-xs font-semibold"
                     style="width: {{ $presentWidth }}%">
                    {{ $previewData['summary']['present'] }}
                </div>
            @endif

            @if($previewData['summary']['absent'] > 0)
                <div class="bg-red-500 flex items-center justify-center text-white text-xs font-semibold"
                     style="width: {{ $absentWidth }}%">
                    {{ $previewData['summary']['absent'] }}
                </div>
            @endif

            @if(($previewData['summary']['late'] ?? 0) > 0)
                <div class="bg-yellow-500 flex items-center justify-center text-white text-xs font-semibold"
                     style="width: {{ $lateWidth }}%">
                    {{ $previewData['summary']['late'] }}
                </div>
            @endif
        </div>
        <div class="flex justify-between mt-2 text-xs text-gray-600">
            <span>🟢 Present: {{ number_format($presentWidth, 1) }}%</span>
            <span>🔴 Absent: {{ number_format($absentWidth, 1) }}%</span>
            @if(($previewData['summary']['late'] ?? 0) > 0)
                <span>🟡 Late: {{ number_format($lateWidth, 1) }}%</span>
            @endif
        </div>
    </div>

    <!-- Detailed Records -->
    <div class="mb-6">
        <h3 class="text-sm font-bold text-gray-700 mb-3">Detailed Attendance Records (Last 10)</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full border-collapse border border-gray-800">
                <thead class="bg-gray-200">
                <tr>
                    <th class="border border-gray-800 px-3 py-2 text-left text-xs font-bold text-gray-900">Date</th>
                    <th class="border border-gray-800 px-3 py-2 text-left text-xs font-bold text-gray-900">Session</th>
                    <th class="border border-gray-800 px-3 py-2 text-left text-xs font-bold text-gray-900">Level</th>
                    <th class="border border-gray-800 px-3 py-2 text-left text-xs font-bold text-gray-900">Subject</th>
                    <th class="border border-gray-800 px-3 py-2 text-center text-xs font-bold text-gray-900">Status</th>
                    <th class="border border-gray-800 px-3 py-2 text-left text-xs font-bold text-gray-900">Remarks</th>
                </tr>
                </thead>
                <tbody>
                @forelse($previewData['records']->take(10) as $record)
                    <tr class="hover:bg-gray-50">
                        <td class="border border-gray-800 px-3 py-2 text-sm">
                            {{ $record->attendance->date->format('M d, Y') }}
                        </td>
                        <td class="border border-gray-800 px-3 py-2 text-sm capitalize">
                            {{ $record->attendance->session }}
                        </td>
                        <td class="border border-gray-800 px-3 py-2 text-sm">
                            {{ $record->attendance->academicLevel->name ?? 'N/A' }}
                        </td>
                        <td class="border border-gray-800 px-3 py-2 text-sm">
                            {{ $record->attendance->academicSubject->name ?? 'All Subjects' }}
                        </td>
                        <td class="border border-gray-800 px-3 py-2 text-center">
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                    {{ $record->status === 'present' ? 'bg-green-100 text-green-800' :
                                       ($record->status === 'absent' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                                    {{ ucfirst($record->status) }}
                                </span>
                        </td>
                        <td class="border border-gray-800 px-3 py-2 text-sm italic text-gray-600">
                            {{ $record->remarks ?: '-' }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="border border-gray-800 px-3 py-4 text-center text-sm text-gray-500">
                            No attendance records found for the selected period
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        @if($previewData['records']->count() > 10)
            <p class="text-xs text-gray-500 mt-2 italic text-center">
                Showing 10 of {{ $previewData['records']->count() }} records. Full report will include all records.
            </p>
        @endif
    </div>

    <!-- Footer -->
    <div class="mt-8 pt-4 border-t border-gray-300 text-center text-xs text-gray-500">
        <p>This is a computer-generated document. No signature is required.</p>
        <p class="mt-1">© {{ now()->year }} {{ $student->school->name ?? 'School Name' }}. All rights reserved.</p>
    </div>
</div>
