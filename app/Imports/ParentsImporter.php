<?php

namespace App\Imports;

use App\Enums\UserRole;
use App\Models\School;
use App\Models\Student;
use App\Models\StudentParent;
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

class ParentsImporter implements ToCollection, WithBatchInserts, WithChunkReading, WithHeadingRow, WithValidation
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
        $school = School::find($this->defaultSchoolId);
        if (! $school) {
            throw new Exception('School not found');
        }

        foreach ($collection as $row) {
            try {
                $this->processParentRow($row->toArray());
                $this->importedCount++;
            } catch (Exception $e) {
                $this->errorCount++;
                $this->errors[] = [
                    'row' => $row->toArray(),
                    'error' => $e->getMessage(),
                ];
            }
        }
        if (! empty($this->errors)) {
            Log::error('Parent Import Errors:', $this->errors);
        }
    }

    protected function processParentRow(array $row): void
    {
        $parentData = $this->cleanRowData($row);

        $user = null;
        if (! empty($parentData['email'])) {
            $user = User::where('email', $parentData['email'])->first();
        }

        if ($user) {
            $existingParent = StudentParent::where('user_id', $user->id)->first();
            if ($existingParent) {
                // If parent exists, maybe try to link to student if provided
                if (! empty($parentData['student_id'])) {
                    $this->linkToStudent($existingParent, $parentData['student_id'], $parentData['relationship']);
                }
                $this->skippedCount++;

                return;
            }
        }

        if (! $user) {
            $user = User::create([
                'name' => trim($parentData['first_name'].' '.($parentData['other_names'] ? $parentData['other_names'].' ' : '').$parentData['last_name']),
                'first_name' => $parentData['first_name'],
                'last_name' => $parentData['last_name'],
                'other_names' => $parentData['other_names'],
                'email' => $parentData['email'],
                'password' => Hash::make($parentData['password'] ?? $this->defaultPassword),
                'role' => UserRole::PARENT,
                'phone' => $parentData['phone'],
                'gender' => $parentData['gender'],
                'school_id' => $this->defaultSchoolId,
                'address' => $parentData['address'],
            ]);
        }

        $parent = StudentParent::create([
            'user_id' => $user->id,
            'school_id' => $this->defaultSchoolId,
            'relationship' => $parentData['relationship'],
            'parent_id' => $parentData['parent_id'] ?? StudentParent::generateParentCode($this->defaultSchoolId),
        ]);

        if (! empty($parentData['student_id'])) {
            $this->linkToStudent($parent, $parentData['student_id'], $parentData['relationship']);
        }
    }

    protected function linkToStudent(StudentParent $parent, $studentId, $relationship): void
    {
        $student = Student::where('student_id', $studentId)
            ->orWhere('id', $studentId)
            ->first();

        if ($student) {
            $parent->students()->syncWithoutDetaching([
                $student->id => ['relationship' => $relationship],
            ]);
        }
    }

    protected function cleanRowData(array $row): array
    {
        return [
            'first_name' => trim($row['first_name'] ?? ''),
            'last_name' => trim($row['last_name'] ?? ''),
            'other_names' => trim($row['other_names'] ?? $row['other_name'] ?? ''),
            'email' => strtolower(trim($row['email'] ?? '')),
            'password' => $row['password'] ?? null,
            'phone' => $row['phone'] ?? null,
            'gender' => $row['gender'] ?? null,
            'address' => $row['address'] ?? null,
            'relationship' => $row['relationship'] ?? 'Parent',
            'student_id' => $row['student_id'] ?? $row['student_identifier'] ?? null,
            'parent_id' => $row['parent_id'] ?? $row['parent_identifier'] ?? null,
        ];
    }

    public function rules(): array
    {
        return [
            '*.first_name' => 'required|string',
            '*.last_name' => 'required|string',
            '*.email' => 'required|email',
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
