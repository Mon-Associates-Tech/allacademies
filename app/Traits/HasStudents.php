<?php

namespace App\Traits;

use App\Models\Student;
use Illuminate\Database\Eloquent\Collection;

trait HasStudents
{

    // ... existing relationships ...

    /**
     * Get all students associated with this teacher
     * Includes students from:
     * - Direct assignments (teacher_student pivot table)
     * - Academic levels the teacher belongs to
     * - Academic groups the teacher belongs to
     * - Student groups managed by the teacher
     *
     * @param bool $withSource - Whether to include source information
     * @return Collection
     */
    public function getAllStudents($withSource = false)
    {
        $studentIds = collect();
        $studentsWithSource = collect();

        // 1. Get directly assigned students
        $directlyAssigned = $this->assignedStudents()
            ->with(['user', 'academicLevel.academicGroup'])
            ->get();

        foreach ($directlyAssigned as $student) {
            $studentIds->push($student->id);
            if ($withSource) {
                $student->association_source = $student->pivot->is_primary ? 'Primary Assignment' : 'Secondary Assignment';
                $student->association_type = 'direct';
                $studentsWithSource->push($student);
            }
        }

        // 2. Get students from academic levels
        foreach ($this->academicLevels as $level) {
            $levelStudents = Student::where('academic_level_id', $level->id)
                ->with(['user', 'academicLevel.academicGroup'])
                ->get();

            foreach ($levelStudents as $student) {
                if (!$studentIds->contains($student->id)) {
                    $studentIds->push($student->id);
                    if ($withSource) {
                        $student->association_source = 'Academic Level: ' . $level->name;
                        $student->association_type = 'level';
                        $student->is_primary_level = $level->pivot->is_primary ?? false;
                        $studentsWithSource->push($student);
                    }
                }
            }
        }

        // 3. Get students from academic groups
        foreach ($this->academicGroups as $group) {
            $groupStudents = Student::whereHas('academicLevel', function ($query) use ($group) {
                $query->where('academic_group_id', $group->id);
            })
                ->with(['user', 'academicLevel.academicGroup'])
                ->get();

            foreach ($groupStudents as $student) {
                if (!$studentIds->contains($student->id)) {
                    $studentIds->push($student->id);
                    if ($withSource) {
                        $student->association_source = 'Academic Group: ' . $group->name;
                        $student->association_type = 'group';
                        $student->is_primary_group = $group->pivot->is_primary ?? false;
                        $studentsWithSource->push($student);
                    }
                }
            }
        }

        // 4. Get students from student groups managed by this teacher
        foreach ($this->studentGroups as $studentGroup) {
            $studentGroupStudents = $studentGroup->students()
                ->with(['user', 'academicLevel.academicGroup'])
                ->get();

            foreach ($studentGroupStudents as $student) {
                if (!$studentIds->contains($student->id)) {
                    $studentIds->push($student->id);
                    if ($withSource) {
                        $student->association_source = 'Student Group: ' . $studentGroup->name;
                        $student->association_type = 'student_group';
                        $studentsWithSource->push($student);
                    }
                }
            }
        }

        if ($withSource) {
            return $studentsWithSource;
        }

        // Return all unique students without source info
        return Student::whereIn('id', $studentIds->unique())
            ->with(['user', 'academicLevel.academicGroup'])
            ->get();
    }

    /**
     * Get students with detailed association information
     *
     * @return Collection
     */
    public function getStudentsWithDetails()
    {
        return $this->getAllStudents(true);
    }

    /**
     * Get count of all associated students
     *
     * @return int
     */
    public function getStudentsCount()
    {
        return $this->getAllStudents()->count();
    }

    /**
     * Get students grouped by their association type
     *
     * @return array
     */
    public function getStudentsGroupedBySource()
    {
        $students = $this->getAllStudents(true);

        return $students->groupBy('association_type')->map(function ($group) {
            return $group->map(function ($student) {
                return [
                    'id' => $student->id,
                    'name' => $student->user->name,
                    'email' => $student->user->email,
                    'academic_level' => $student->academicLevel->name ?? 'N/A',
                    'academic_group' => $student->academicLevel->academicGroup->name ?? 'N/A',
                    'source' => $student->association_source,
                    'is_primary' => $student->is_primary_level ?? $student->is_primary_group ?? false,
                ];
            });
        })->toArray();
    }

    /**
     * Check if teacher has access to a specific student
     *
     * @param int|Student $student
     * @return bool
     */
    public function hasStudent($student)
    {
        $studentId = is_object($student) ? $student->id : $student;
        return $this->getAllStudents()->contains('id', $studentId);
    }

    /**
     * Get students by academic level
     *
     * @param int $academicLevelId
     * @return Collection
     */
    public function getStudentsByLevel($academicLevelId)
    {
        return $this->getAllStudents()->filter(function ($student) use ($academicLevelId) {
            return $student->academic_level_id == $academicLevelId;
        });
    }

    /**
     * Get students by academic group
     *
     * @param int $academicGroupId
     * @return Collection
     */
    public function getStudentsByGroup($academicGroupId)
    {
        return $this->getAllStudents()->filter(function ($student) use ($academicGroupId) {
            return $student->academicLevel &&
                $student->academicLevel->academic_group_id == $academicGroupId;
        });
    }

    /**
     * Get only directly assigned students (via pivot table)
     *
     * @return Collection
     */
    public function getDirectlyAssignedStudents()
    {
        return $this->assignedStudents()
            ->with(['user', 'academicLevel.academicGroup'])
            ->get();
    }

    /**
     * Get students automatically associated through academic levels/groups
     *
     * @return Collection
     */
    public function getAutomaticallyAssociatedStudents()
    {
        $allStudents = $this->getAllStudents();
        $directlyAssigned = $this->getDirectlyAssignedStudents();
        $directIds = $directlyAssigned->pluck('id');

        return $allStudents->reject(function ($student) use ($directIds) {
            return $directIds->contains($student->id);
        });
    }

    /**
     * Get students summary statistics
     *
     * @return array
     */
    public function getStudentsSummary()
    {
        $studentsWithDetails = $this->getAllStudents(true);

        return [
            'total_students' => $studentsWithDetails->count(),
            'directly_assigned' => $studentsWithDetails->where('association_type', 'direct')->count(),
            'from_levels' => $studentsWithDetails->where('association_type', 'level')->count(),
            'from_groups' => $studentsWithDetails->where('association_type', 'group')->count(),
            'from_student_groups' => $studentsWithDetails->where('association_type', 'student_group')->count(),
            'by_association_type' => $studentsWithDetails->groupBy('association_type')->map->count()->toArray(),
        ];
    }

}
