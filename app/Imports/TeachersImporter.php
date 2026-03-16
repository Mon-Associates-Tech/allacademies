<?php

namespace App\Imports;

use App\Enums\UserRole;
use App\Models\School;
use App\Models\Teacher;
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

class TeachersImporter implements ToCollection, WithBatchInserts, WithChunkReading, WithHeadingRow, WithValidation
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
                $this->processTeacherRow($row->toArray());
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
            Log::error('Teacher Import Errors:', $this->errors);
        }
    }

    protected function processTeacherRow(array $row): void
    {
        $teacherData = $this->cleanRowData($row);

        $user = null;
        if (! empty($teacherData['email'])) {
            $user = User::where('email', $teacherData['email'])->first();
        }

        if ($user) {
            $existingTeacher = Teacher::where('user_id', $user->id)->first();
            if ($existingTeacher) {
                $this->skippedCount++;

                return;
            }
        }

        if (! $user) {
            $user = User::create([
                'name' => trim($teacherData['first_name'].' '.($teacherData['other_names'] ? $teacherData['other_names'].' ' : '').$teacherData['last_name']),
                'first_name' => $teacherData['first_name'],
                'last_name' => $teacherData['last_name'],
                'other_names' => $teacherData['other_names'],
                'email' => $teacherData['email'],
                'password' => Hash::make($teacherData['password'] ?? $this->defaultPassword),
                'role' => UserRole::TEACHER,
                'phone' => $teacherData['phone'],
                'gender' => $teacherData['gender'],
                'school_id' => $this->defaultSchoolId,
            ]);
        }

        Teacher::create([
            'user_id' => $user->id,
            'school_id' => $this->defaultSchoolId,
            'employee_id' => $teacherData['employee_id'] ?? Teacher::generateEmployeeId($this->defaultSchoolId),
            'department' => $teacherData['department'],
            'hire_date' => $teacherData['hire_date'] ?? now(),
            'qualifications' => $teacherData['qualifications'] ? explode(',', $teacherData['qualifications']) : [],
            'specialization' => $teacherData['specialization'],
            'status' => 'active',
        ]);
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
            'employee_id' => $row['employee_id'] ?? null,
            'department' => $row['department'] ?? null,
            'hire_date' => $row['hire_date'] ?? null,
            'qualifications' => $row['qualifications'] ?? $row['qualification'] ?? null,
            'specialization' => $row['specialization'] ?? null,
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
