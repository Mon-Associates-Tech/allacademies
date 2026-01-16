<?php

namespace App\Services;

use App\Models\Attendance\Attendance;
use App\Models\Attendance\AttendanceRecord;
use App\Models\Teacher;
use Carbon\Carbon;

class AttendanceService
{
    public function createAttendanceSession($teacherId, $academicLevelId, $data)
    {
        $teacher = Teacher::findOrFail($teacherId);

        // Verify teacher can take attendance for this level
        if (! $teacher->canTakeAttendanceForLevel($academicLevelId)) {
            throw new \Exception('Teacher is not assigned to this academic level');
        }

        // Create the attendance session
        return Attendance::create([
            'teacher_id' => $teacherId,
            'academic_level_id' => $academicLevelId,
            'academic_subject_id' => $data['academic_subject_id'] ?? null,
            'date' => $data['date'] ?? Carbon::today(),
            'session' => $data['session'] ?? 'morning',
            'remarks' => $data['remarks'] ?? null,
        ]);

    }

    public function recordStudentAttendance($attendanceId, $studentId, $status, $remarks = null)
    {
        return AttendanceRecord::updateOrCreate(
            [
                'attendance_id' => $attendanceId,
                'student_id' => $studentId,
            ],
            [
                'status' => $status,
                'remarks' => $remarks,
            ]
        );

    }

    public function getAttendanceReport($academicLevelId, $startDate, $endDate)
    {
        return Attendance::where('academic_level_id', $academicLevelId)
            ->whereBetween('date', [$startDate, $endDate])
            ->with(['attendanceRecords.student.user', 'academicSubject'])
            ->get();
    }
}
