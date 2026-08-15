<?php
// app/Timetable/Services/ConflictDetectionService.php

namespace App\Timetable\Services;

use App\Timetable\Models\TimetableEntry;

class ConflictDetectionService
{
    /**
     * @return array<string, bool> e.g. ['teacher' => true, 'room' => false, 'class' => false]
     */
    public function check(
        int $schoolId,
        int $academicPeriodId,
        int $teacherId,
        int $roomId,
        int $academicLevelId,
        int $timeSlotId,
        string $dayOfWeek,
        ?int $excludeEntryId = null,
    ): array {
        return [
            'teacher' => $this->hasTeacherConflict($schoolId, $academicPeriodId, $teacherId, $timeSlotId, $dayOfWeek, $excludeEntryId),
            'room' => $this->hasRoomConflict($schoolId, $academicPeriodId, $roomId, $timeSlotId, $dayOfWeek, $excludeEntryId),
            'class' => $this->hasClassConflict($schoolId, $academicPeriodId, $academicLevelId, $timeSlotId, $dayOfWeek, $excludeEntryId),
        ];
    }

    public function hasTeacherConflict(
        int $schoolId, int $academicPeriodId, int $teacherId, int $timeSlotId, string $dayOfWeek, ?int $excludeEntryId = null,
    ): bool {
        return $this->baseQuery($schoolId, $academicPeriodId, $timeSlotId, $dayOfWeek, $excludeEntryId)
            ->where('teacher_id', $teacherId)
            ->exists();
    }

    public function hasRoomConflict(
        int $schoolId, int $academicPeriodId, int $roomId, int $timeSlotId, string $dayOfWeek, ?int $excludeEntryId = null,
    ): bool {
        return $this->baseQuery($schoolId, $academicPeriodId, $timeSlotId, $dayOfWeek, $excludeEntryId)
            ->where('room_id', $roomId)
            ->exists();
    }

    public function hasClassConflict(
        int $schoolId, int $academicPeriodId, int $academicLevelId, int $timeSlotId, string $dayOfWeek, ?int $excludeEntryId = null,
    ): bool {
        return $this->baseQuery($schoolId, $academicPeriodId, $timeSlotId, $dayOfWeek, $excludeEntryId)
            ->where('academic_level_id', $academicLevelId)
            ->exists();
    }

    /**
     * Reusable by Examinations Hub: is this room/slot already committed to a
     * regular class on this day, regardless of period (exams may span outside
     * the normal period boundary, so period isn't checked here).
     */
    public function hasRegularClassConflict(int $schoolId, int $roomId, int $timeSlotId, string $dayOfWeek): bool
    {
        return TimetableEntry::query()
            ->forSchool($schoolId)
            ->where('room_id', $roomId)
            ->where('time_slot_id', $timeSlotId)
            ->where('day_of_week', $dayOfWeek)
            ->exists();
    }

    protected function baseQuery(int $schoolId, int $academicPeriodId, int $timeSlotId, string $dayOfWeek, ?int $excludeEntryId)
    {
        $query = TimetableEntry::query()
            ->forSchool($schoolId)
            ->forPeriod($academicPeriodId)
            ->where('time_slot_id', $timeSlotId)
            ->where('day_of_week', $dayOfWeek);

        if ($excludeEntryId) {
            $query->where('id', '!=', $excludeEntryId);
        }

        return $query;
    }
}
