<?php

namespace App\Imports;

use App\Enums\UserRole;
use App\Models\AcademicGroup;
use App\Models\AcademicLevel;
use App\Models\School;
use App\Models\Student;
use App\Models\StudentGroup;
use App\Models\User;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Log;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class StudentsImporter implements ToCollection, WithBatchInserts, WithChunkReading, WithHeadingRow, WithValidation
{
    protected int $importedCount = 0;

    protected int $skippedCount = 0;

    protected int $errorCount = 0;

    protected array $errors = [];

    protected mixed $defaultSchoolId;

    protected mixed $defaultPassword;

    public function __construct($defaultSchoolId = null, $defaultPassword = 'password123')
    {
        if (is_array($defaultSchoolId)) {
            $options = $defaultSchoolId;
            $this->defaultSchoolId = $options['default_school_id'] ?? null;
            $this->defaultPassword = $options['default_password'] ?? 'password123';
        } else {
            $this->defaultSchoolId = $defaultSchoolId;
            $this->defaultPassword = $defaultPassword;
        }
    }

    public function collection(Collection $collection): void
    {
        // NEW: Check if school has active subscription
        $school = School::find($this->defaultSchoolId);

        if (! $school) {
            throw new Exception('School not found');
        }

        if (! $school->hasActiveContentSubscription()) {
            throw new Exception(
                'Your school does not have an active content subscription. '
                .'Please purchase a subscription before importing students.'
            );
        }

        // Check if enough capacity for all students in file
        $totalToImport = count($collection);
        $remainingCapacity = $school->getRemainingStudentCapacity();

        if ($totalToImport > $remainingCapacity) {
            throw new Exception(
                "Cannot import {$totalToImport} students. "
                ."Remaining capacity: {$remainingCapacity}. "
                ."Your subscription allows {$school->getActiveSubscription()->beneficiaries} students total."
            );
        }

        logInfo('data: '.json_encode($collection));
        foreach ($collection as $row) {

            try {
                $this->processStudentRow($row->toArray());
                $this->importedCount++;
            } catch (Exception $e) {
                $this->errorCount++;
                $this->errors[] = [
                    'row' => $row->toArray(),
                    'error' => $e->getMessage(),
                ];
            }
        }
        Log::error($this->errors);
    }

    protected function processStudentRow(array $row): void
    {

        // Clean and validate data
        $studentData = $this->cleanRowData($row);

        // Check if user already exists
        $user = User::where('email', $studentData['email'])->first();

        if (! $user) {
            // Create new user
            $user = User::create([
                'name' => trim($studentData['first_name'].' '.$studentData['last_name']),
                'email' => $studentData['email'],
                'password' => Hash::make($studentData['password'] ?? $this->defaultPassword),
                'role' => UserRole::STUDENT,
                'phone' => $studentData['phone'] ?? null,
            ]);
        }

        // Check if student record already exists
        $existingStudent = Student::where('user_id', $user->id)->first();

        if ($existingStudent) {
            $this->skippedCount++;

            return;
        }

        // Validate academic level and group IDs if provided
        $academicLevel = null;
        $academicGroup = null;

        if (! empty($studentData['academic_level_id'])) {
            $academicLevel = AcademicLevel::find($studentData['academic_level_id']);
            if (! $academicLevel) {
                throw new Exception("Academic level with ID {$studentData['academic_level_id']} not found");
            }
        }

        if (! empty($studentData['academic_group_id'])) {
            $academicGroup = AcademicGroup::find($studentData['academic_group_id']);
            if (! $academicGroup) {
                throw new Exception("Academic group with ID {$studentData['academic_group_id']} not found");
            }
        }

        // Find or create school
        $school = $this->findOrCreateSchool($studentData);

        // Find or create student group
        //        $studentGroup = $this->findOrCreateStudentGroup($studentData);

        // Create student record
        $student = Student::create([
            'user_id' => $user->id,
            'academic_level_id' => $academicLevel?->id,
            'academic_group_id' => $academicGroup?->id,
            'school_id' => $school?->id ?? $this->defaultSchoolId,
            'student_group_id' => null,
            // Include additional fields
            'date_of_birth' => $studentData['date_of_birth'] ?? null,
            'phone' => $studentData['phone'] ?? null,
            'address' => $studentData['address'] ?? null,
            'gender' => $studentData['gender'] ?? null,
            'parent_name' => $studentData['parent_name'] ?? null,
            'parent_phone' => $studentData['parent_phone'] ?? null,
            'parent_email' => $studentData['parent_email'] ?? null,
            'emergency_contact' => $studentData['emergency_contact'] ?? null,
            'blood_group' => $studentData['blood_group'] ?? null,
            'admission_date' => $studentData['admission_date'] ?? now(),
            'student_id' => $studentData['student_id'] ?? Student::generateStudentId(),
        ]);

        // Handle additional relationships if needed
        $this->handleAdditionalRelationships($student, $studentData);
    }

    protected function cleanRowData(array $row): array
    {
        return [
            'name' => trim($row['name'] ?? $row['full_name'] ?? ''),
            'first_name' => trim($row['first_name'] ?? explode(' ', $row['name'] ?? '')[0] ?? ''),
            'last_name' => trim($row['last_name'] ?? implode(' ', array_slice(explode(' ', $row['name'] ?? ''), 1)) ?? ''),
            'email' => strtolower(trim($row['email'] ?? '')),
            'password' => $row['password'] ?? null,
            'academic_level_id' => $row['academic_level_id'] ?? null,
            'academic_group_id' => $row['academic_group_id'] ?? null,
            'school_name' => trim($row['school'] ?? $row['school_name'] ?? ''),
            'student_group_name' => trim($row['student_group'] ?? $row['class'] ?? ''),
            'student_id' => $row['student_id'] ?? null,
            'date_of_birth' => $row['date_of_birth'] ?? $row['dob'] ?? null,
            'phone' => $row['phone'] ?? $row['mobile'] ?? null,
            'address' => $row['address'] ?? null,
            // Additional fields
            'gender' => $row['gender'] ?? null,
            'parent_name' => $row['parent_name'] ?? null,
            'parent_phone' => $row['parent_phone'] ?? null,
            'parent_email' => $row['parent_email'] ?? null,
            'emergency_contact' => $row['emergency_contact'] ?? null,
            'blood_group' => $row['blood_group'] ?? null,
            'admission_date' => $row['admission_date'] ?? now(),
        ];
    }

    protected function findOrCreateSchool(array $studentData): ?School
    {
        if (empty($studentData['school_name']) && ! $this->defaultSchoolId) {
            return null;
        }

        if ($this->defaultSchoolId) {
            return School::find($this->defaultSchoolId);
        }

        return School::firstOrCreate(
            ['name' => $studentData['school_name']],
            [
                'address' => 'Imported school',
                'is_active' => true,
            ]
        );
    }

    protected function findOrCreateStudentGroup(array $studentData): ?StudentGroup
    {
        if (empty($studentData['student_group_name'])) {
            return null;
        }

        return StudentGroup::firstOrCreate(
            ['name' => $studentData['student_group_name']],
            [
                'description' => "Imported student group: {$studentData['student_group_name']}",
                'is_active' => true,
            ]
        );
    }

    protected function handleAdditionalRelationships(Student $student, array $studentData): void
    {
        // 1. Auto-assign all subjects from the academic level to the student
        if ($student->academicLevel) {
            $this->assignLevelSubjectsToStudent($student);
        }

        // 2. Track academic progression/history
        if ($student->academic_level_id || $student->academic_group_id) {
            $this->createAcademicProgression($student, $studentData);
        }

        // 3. Assign to academic level's teachers (if you want automatic teacher-student relationships)
        // $this->assignLevelTeachersToStudent($student);
    }

    /**
     * Assign all subjects from the student's academic level
     */
    protected function assignLevelSubjectsToStudent(Student $student): void
    {
        $subjects = $student->academicLevel->academicSubjects;

        if ($subjects->isEmpty()) {
            return;
        }

        // Attach subjects with pivot data
        $subjectData = [];
        foreach ($subjects as $subject) {
            $subjectData[$subject->id] = [
                'is_active' => true,
                'assigned_by' => auth()->id() ?? null,
                'assigned_at' => now(),
                'notes' => 'Auto-assigned from academic level during import',
            ];
        }

        // Sync subjects (won't duplicate if already exists)
        $student->academicSubjects()->syncWithoutDetaching($subjectData);

        \Log::info("Assigned {$subjects->count()} subjects to student {$student->student_id}");
    }

    /**
     * Create academic progression record
     */
    protected function createAcademicProgression(Student $student, array $studentData): void
    {
        try {
            $student->academicProgression()->create([
                'academic_level_id' => $student->academic_level_id,
                'academic_group_id' => $student->academic_group_id,
                'start_date' => $studentData['admission_date'] ?? now(),
                'end_date' => null, // Will be set when they graduate or move levels
                'status' => 'current',
                'remarks' => 'Initial assignment during import',
            ]);

            \Log::info("Created academic progression for student {$student->student_id}");
        } catch (\Exception $e) {
            \Log::warning('Could not create academic progression: '.$e->getMessage());
        }
    }

    /**
     * Optional: Assign teachers from the academic level/group to the student
     */
    protected function assignLevelTeachersToStudent(Student $student): void
    {
        if (! $student->academicLevel) {
            return;
        }

        // Get teachers from the academic level
        $teachers = $student->academicLevel->teachers;

        if ($teachers->isEmpty()) {
            return;
        }

        // Attach teachers with pivot data
        $teacherData = [];
        foreach ($teachers as $teacher) {
            $teacherData[$teacher->id] = [
                'is_primary' => false, // You can set logic for primary teacher
                'notes' => 'Auto-assigned during import',
            ];
        }

        // Only assign the first teacher as primary
        if ($teachers->isNotEmpty()) {
            $teacherData[$teachers->first()->id]['is_primary'] = true;
        }

        $student->teachers()->syncWithoutDetaching($teacherData);

        \Log::info("Assigned {$teachers->count()} teachers to student {$student->student_id}");
    }

    public function rules(): array
    {
        return [
            '*.email' => 'required|email',
            '*.first_name' => 'required|string|min:2',
            '*.last_name' => 'required|string|min:2',
            '*.academic_level_id' => 'nullable|exists:academic_levels,id',
            '*.academic_group_id' => 'nullable|exists:academic_groups,id',
        ];
    }

    public function prepareForValidation($data, $index)
    {
        //   \Log::info("Row {$index} before validation:", $data);
        return $data;
    }

    public function customValidationMessages(): array
    {
        return [
            '*.email.required' => 'Email is required for each student',
            '*.email.email' => 'Email must be a valid email address',
            '*.first_name.required' => 'First Name is required for each student',
            '*.last_name.required' => 'Last Name is required for each student',
            '*.academic_level_id.exists' => 'The specified academic level does not exist',
            '*.academic_group_id.exists' => 'The specified academic group does not exist',
        ];
    }

    public function batchSize(): int
    {
        return 100;
    }

    public function chunkSize(): int
    {
        return 100;
    }

    public function getImportStats(): array
    {
        return [
            'imported' => $this->importedCount,
            'skipped' => $this->skippedCount,
            'errors' => $this->errorCount,
            'error_details' => $this->errors,
        ];
    }
}
