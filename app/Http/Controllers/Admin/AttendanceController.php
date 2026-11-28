<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance\Attendance;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        abort_unless($user && ($user->hasRole('admin') || $user->isOwner() || $user->isOverseer() || $user->isSuperAdmin()), 403);

        $data = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'status' => ['nullable', 'in:present,absent,late,excused'],
        ]);
        $schoolId = getSchoolId();
        $canViewAllSchools = $user->isOwner() || $user->isSuperAdmin();
        abort_unless($schoolId || $canViewAllSchools, 403, 'Select a school before reviewing attendance.');

        $schoolAttendanceRecords = function ($query) use ($schoolId) {
            if ($schoolId) {
                $query->whereHas('student', fn ($studentQuery) => $studentQuery->where('school_id', $schoolId));
            }

            return $query;
        };

        $records = Attendance::query()
            ->when($schoolId, fn ($query) => $query->whereHas('teacher', fn ($teacherQuery) => $teacherQuery->where('school_id', $schoolId)))
            ->with([
                'teacher.user',
                'academicLevel',
                'academicSubject',
                'attendanceRecords' => fn ($query) => $schoolAttendanceRecords($query)->with('student.user'),
            ])
            ->withCount([
                'attendanceRecords as present_count' => fn ($query) => $schoolAttendanceRecords($query)->where('status', 'present'),
                'attendanceRecords as absent_count' => fn ($query) => $schoolAttendanceRecords($query)->where('status', 'absent'),
                'attendanceRecords as late_count' => fn ($query) => $schoolAttendanceRecords($query)->where('status', 'late'),
                'attendanceRecords as excused_count' => fn ($query) => $schoolAttendanceRecords($query)->where('status', 'excused'),
            ]);

        if (! empty($data['start_date'])) {
            $records->whereDate('date', '>=', $data['start_date']);
        }

        if (! empty($data['end_date'])) {
            $records->whereDate('date', '<=', $data['end_date']);
        }

        if (! empty($data['status'])) {
            $records->whereHas('attendanceRecords', function ($query) use ($data, $schoolId) {
                $query->where('status', $data['status'])
                    ->when($schoolId, fn ($query) => $query->whereHas('student', fn ($studentQuery) => $studentQuery->where('school_id', $schoolId)));
            });
        }

        if (! empty($data['search'])) {
            $search = $data['search'];
            $records->where(function ($query) use ($search, $schoolId) {
                $query->whereHas('teacher.user', fn ($userQuery) => $userQuery->where('name', 'like', '%'.$search.'%'))
                    ->orWhereHas('academicLevel', fn ($levelQuery) => $levelQuery->where('name', 'like', '%'.$search.'%'))
                    ->orWhereHas('attendanceRecords', function ($recordQuery) use ($search, $schoolId) {
                        $recordQuery->whereHas('student', fn ($studentQuery) => $studentQuery
                            ->when($schoolId, fn ($query) => $query->where('school_id', $schoolId))
                            ->whereHas('user', fn ($userQuery) => $userQuery->where('name', 'like', '%'.$search.'%')));
                    });
            });
        }

        $attendances = $records->orderByDesc('date')->orderByDesc('id')->paginate(20)->withQueryString();

        return view('admin.attendance.index', compact('attendances'));
    }
}
