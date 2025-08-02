<?php

namespace App\Imports;

use App\Models\User;
use App\Models\Student;
use App\Models\AcademicLevel;
use App\Models\AcademicGroup;
use App\Models\School;
use App\Models\StudentGroup;
use App\Enums\UserRole;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Log;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class StudentsImporter implements ToCollection, WithHeadingRow, WithValidation, WithBatchInserts, WithChunkReading
{
    protected $importedCount = 0;
    protected $skippedCount = 0;
    protected $errorCount = 0;
    protected $errors = [];
    protected $defaultSchoolId;
    protected $defaultPassword;

    public function __construct($defaultSchoolId = null, $defaultPassword = 'password123')
    {
        $this->defaultSchoolId = $defaultSchoolId;
        $this->defaultPassword = $defaultPassword;
    }

    /**
     * @param Collection $collection
     */
    public function collection(Collection $collection)
    {
        foreach ($collection as $row) {
            try {
                $this->processStudentRow($row->toArray());
                $this->importedCount++;
            } catch (\Exception $e) {
                $this->errorCount++;
                $this->errors[] = [
                    'row' => $row->toArray(),
                    'error' => $e->getMessage()
                ];
            }
        }
        Log::error($this->errors);
    }

    protected function processStudentRow(array $row)
    {
        // Clean and validate data
        $studentData = $this->cleanRowData($row);

        // Check if user already exists
        $user = User::where('email', $studentData['email'])->first();

        if (!$user) {
            // Create new user
            $user = User::create([
                'name' => $studentData['name'],
                'first_name' => $studentData['first_name'],
                'last_name' => $studentData['last_name'],
                'email' => $studentData['email'],
                'password' => Hash::make($studentData['password'] ?? $this->defaultPassword),
                'email_verified_at' => now(),
                'role' => UserRole::STUDENT,
            ]);
        }

        // Check if student record already exists
        $existingStudent = Student::where('user_id', $user->id)->first();

        if ($existingStudent) {
            $this->skippedCount++;
            return;
        }

        // Find or create related entities
        $academicLevel = $this->findOrCreateAcademicLevel($studentData);
        $academicGroup = $this->findOrCreateAcademicGroup($studentData);
        $school = $this->findOrCreateSchool($studentData);
        $studentGroup = $this->findOrCreateStudentGroup($studentData);

        // Create student record
        $student = Student::create([
            'user_id' => $user->id,
            'academic_level_id' => $academicLevel?->id,
            'academic_group_id' => $academicGroup?->id,
            'school_id' => $school?->id,
            'student_group_id' => $studentGroup?->id,
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
            'academic_level_name' => trim($row['academic_level'] ?? $row['level'] ?? ''),
            'academic_group_name' => trim($row['academic_group'] ?? $row['group'] ?? ''),
            'school_name' => trim($row['school'] ?? $row['school_name'] ?? ''),
            'student_group_name' => trim($row['student_group'] ?? $row['class'] ?? ''),
            'student_id' => $row['student_id'] ?? null,
            'date_of_birth' => $row['date_of_birth'] ?? $row['dob'] ?? null,
            'phone' => $row['phone'] ?? $row['mobile'] ?? null,
            'address' => $row['address'] ?? null,
        ];
    }

    protected function findOrCreateAcademicLevel(array $studentData): ?AcademicLevel
    {
        if (empty($studentData['academic_level_name'])) {
            return null;
        }

        return AcademicLevel::firstOrCreate(
            ['name' => $studentData['academic_level_name']],
            [
                'description' => "Imported academic level: {$studentData['academic_level_name']}",
                'is_active' => true,
            ]
        );
    }

    protected function findOrCreateAcademicGroup(array $studentData): ?AcademicGroup
    {
        if (empty($studentData['academic_group_name'])) {
            return null;
        }

        return AcademicGroup::firstOrCreate(
            ['name' => $studentData['academic_group_name']],
            [
                'description' => "Imported academic group: {$studentData['academic_group_name']}",
                'is_active' => true,
            ]
        );
    }

    protected function findOrCreateSchool(array $studentData): ?School
    {
        if (empty($studentData['school_name']) && !$this->defaultSchoolId) {
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
        // Add any additional relationship handling here
        // For example, assigning to specific teachers, subjects, etc.
    }

    public function rules(): array
    {
        return [
            '*.email' => 'required|email',
            '*.name' => 'required|string|min:2',
        ];
    }

    public function customValidationMessages(): array
    {
        return [
            '*.email.required' => 'Email is required for each student',
            '*.email.email' => 'Email must be a valid email address',
            '*.name.required' => 'Name is required for each student',
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
