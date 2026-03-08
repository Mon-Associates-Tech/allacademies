<div class="bg-white p-8 max-w-4xl mx-auto">
    <!-- Header -->
    <div class="text-center border-b-2 border-gray-800 pb-4 mb-6">
        <h1 class="text-2xl font-bold text-gray-900">{{ $previewData['student']->school->name ?? 'School Name' }}</h1>
        <h2 class="text-xl font-semibold text-gray-700 mt-2">STUDENT REPORT CARD</h2>
        <p class="text-sm text-gray-600 mt-2">
            Academic Year: {{ $previewData['academic_year']->name ?? 'N/A' }} |
            Term: {{ $previewData['term'] }}
        </p>
    </div>

    <!-- Student Information Grid -->
    <div class="grid grid-cols-2 gap-4 mb-6 bg-gray-50 p-4 rounded-lg">
        <div class="border-r border-gray-300 pr-4">
            <h3 class="text-sm font-bold text-gray-700 mb-3">Student Information</h3>
            <div class="space-y-2 text-sm">
                <div class="flex">
                    <span class="font-semibold w-32">Name:</span>
                    <span>{{ $previewData['student']->user->name }}</span>
                </div>
                <div class="flex">
                    <span class="font-semibold w-32">Student ID:</span>
                    <span>{{ $previewData['student']->student_id ?? 'N/A' }}</span>
                </div>
                <div class="flex">
                    <span class="font-semibold w-32">Academic Level:</span>
                    <span>{{ $previewData['student']->academicLevel->name ?? 'N/A' }}</span>
                </div>
            </div>
        </div>

        <div class="pl-4">
            <h3 class="text-sm font-bold text-gray-700 mb-3">Class Information</h3>
            <div class="space-y-2 text-sm">
                <div class="flex">
                    <span class="font-semibold w-32">Class:</span>
                    <span>{{ $previewData['student']->studentGroup->name ?? 'N/A' }}</span>
                </div>
                <div class="flex">
                    <span class="font-semibold w-32">Class Teacher:</span>
                    <span>{{ $previewData['student']->primary_teacher?->user->name ?? 'N/A' }}</span>
                </div>
                <div class="flex">
                    <span class="font-semibold w-32">Report Date:</span>
                    <span>{{ now()->format('F d, Y') }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Grades Table -->
    <div class="mb-6 overflow-x-auto">
        <h3 class="text-sm font-bold text-gray-700 mb-3">Academic Performance</h3>
        <table class="min-w-full border-collapse border border-gray-800">
            <thead class="bg-gray-200">
            <tr>
                <th class="border border-gray-800 px-3 py-2 text-left text-xs font-bold text-gray-900">Subject</th>
                <th class="border border-gray-800 px-3 py-2 text-center text-xs font-bold text-gray-900">Assignments<br>(40%)</th>
                <th class="border border-gray-800 px-3 py-2 text-center text-xs font-bold text-gray-900">Quizzes<br>(10%)</th>
                <th class="border border-gray-800 px-3 py-2 text-center text-xs font-bold text-gray-900">Final Exam<br>(50%)</th>
                <th class="border border-gray-800 px-3 py-2 text-center text-xs font-bold text-gray-900">Total Score</th>
                <th class="border border-gray-800 px-3 py-2 text-center text-xs font-bold text-gray-900">Grade</th>
                <th class="border border-gray-800 px-3 py-2 text-left text-xs font-bold text-gray-900">Remarks</th>
            </tr>
            </thead>
            <tbody>
            @forelse($previewData['grades'] as $subjectId => $grade)
                <tr class="hover:bg-gray-50">
                    <td class="border border-gray-800 px-3 py-2 text-sm font-medium text-gray-900">
                        {{ $grade['subject_name'] }}
                    </td>
                    <td class="border border-gray-800 px-3 py-2 text-center text-sm">
                        {{ $grade['assessments_score'] ?: '-' }}
                    </td>
                    <td class="border border-gray-800 px-3 py-2 text-center text-sm">
                        {{ $grade['quizzes_score'] ?: '-' }}
                    </td>
                    <td class="border border-gray-800 px-3 py-2 text-center text-sm">
                        {{ $grade['final_exam_score'] ?: '-' }}
                    </td>
                    <td class="border border-gray-800 px-3 py-2 text-center text-sm font-semibold">
                        {{ number_format($grade['total_score'], 1) }}
                    </td>
                    <td class="border border-gray-800 px-3 py-2 text-center text-sm font-bold
                            {{ $grade['grade_label'] === 'F' ? 'text-red-600' : ($grade['grade_label'] === 'A+' || $grade['grade_label'] === 'A' ? 'text-green-600' : 'text-gray-900') }}">
                        {{ $grade['grade_label'] ?: '-' }}
                    </td>
                    <td class="border border-gray-800 px-3 py-2 text-sm italic text-gray-600">
                        {{ $grade['remarks'] ?: '-' }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="border border-gray-800 px-3 py-4 text-center text-sm text-gray-500">
                        No grades available
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <!-- Attendance Summary -->
    <div class="mb-6 bg-blue-50 p-4 rounded-lg border border-blue-200">
        <h3 class="text-sm font-bold text-gray-700 mb-3">Attendance Summary</h3>
        <div class="grid grid-cols-4 gap-4 text-center">
            <div>
                <div class="text-2xl font-bold text-gray-900">{{ $previewData['attendance']['total'] }}</div>
                <div class="text-xs text-gray-600">Total Sessions</div>
            </div>
            <div>
                <div class="text-2xl font-bold text-green-600">{{ $previewData['attendance']['present'] }}</div>
                <div class="text-xs text-gray-600">Present</div>
            </div>
            <div>
                <div class="text-2xl font-bold text-red-600">{{ $previewData['attendance']['absent'] }}</div>
                <div class="text-xs text-gray-600">Absent</div>
            </div>
            <div>
                <div class="text-2xl font-bold text-indigo-600">{{ $previewData['attendance']['rate'] }}%</div>
                <div class="text-xs text-gray-600">Attendance Rate</div>
            </div>
        </div>
    </div>

    <!-- Signatures -->
    <div class="grid grid-cols-3 gap-8 mt-12 pt-8 border-t border-gray-300">
        <div class="text-center">
            <div class="border-t-2 border-gray-400 pt-2 mt-12">
                <p class="text-xs font-semibold text-gray-700">Class Teacher</p>
                <p class="text-xs text-gray-600">{{ $previewData['student']->primary_teacher?->user->name ?? 'N/A' }}</p>
            </div>
        </div>
        <div class="text-center">
            <div class="border-t-2 border-gray-400 pt-2 mt-12">
                <p class="text-xs font-semibold text-gray-700">Principal</p>
                <p class="text-xs text-gray-600">________________________</p>
            </div>
        </div>
        <div class="text-center">
            <div class="border-t-2 border-gray-400 pt-2 mt-12">
                <p class="text-xs font-semibold text-gray-700">Parent/Guardian</p>
                <p class="text-xs text-gray-600">________________________</p>
            </div>
        </div>
    </div>
</div>
