<?php

namespace App\Imports;

use App\Enums\UserRole;
use App\Models\Accountant;
use App\Models\School;
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

class AccountantsImporter implements ToCollection, WithBatchInserts, WithChunkReading, WithHeadingRow, WithValidation
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
                $this->processAccountantRow($row->toArray());
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
            Log::error('Accountant Import Errors:', $this->errors);
        }
    }

    protected function processAccountantRow(array $row): void
    {
        $accountantData = $this->cleanRowData($row);

        $user = null;
        if (! empty($accountantData['email'])) {
            $user = User::where('email', $accountantData['email'])->first();
        }

        if ($user) {
            $existingAccountant = Accountant::where('user_id', $user->id)->first();
            if ($existingAccountant) {
                $this->skippedCount++;

                return;
            }
        }

        if (! $user) {
            $user = User::create([
                'name' => trim($accountantData['first_name'].' '.($accountantData['other_names'] ? $accountantData['other_names'].' ' : '').$accountantData['last_name']),
                'first_name' => $accountantData['first_name'],
                'last_name' => $accountantData['last_name'],
                'other_names' => $accountantData['other_names'],
                'email' => $accountantData['email'],
                'password' => Hash::make($accountantData['password'] ?? $this->defaultPassword),
                'role' => UserRole::ACCOUNTANT,
                'phone' => $accountantData['phone'],
                'gender' => $accountantData['gender'],
                'school_id' => $this->defaultSchoolId,
                'address' => $accountantData['address'],
                'date_of_birth' => $accountantData['date_of_birth'],
            ]);
        }

        Accountant::create([
            'user_id' => $user->id,
            'school_id' => $this->defaultSchoolId,
            'employee_id' => $accountantData['employee_id'] ?? Accountant::generateEmployeeId($this->defaultSchoolId),
            'phone' => $accountantData['phone'],
            'address' => $accountantData['address'],
            'date_of_birth' => $accountantData['date_of_birth'],
            'hire_date' => $accountantData['hire_date'] ?? now(),
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
            'address' => $row['address'] ?? null,
            'date_of_birth' => $row['date_of_birth'] ?? $row['dob'] ?? null,
            'employee_id' => $row['employee_id'] ?? null,
            'hire_date' => $row['hire_date'] ?? null,
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
