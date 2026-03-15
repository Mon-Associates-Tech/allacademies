<?php

namespace App\Imports;

use App\Enums\UserRole;
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

class AdministratorsImporter implements ToCollection, WithBatchInserts, WithChunkReading, WithHeadingRow, WithValidation
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
                $this->processAdministratorRow($row->toArray());
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
            Log::error('Administrator Import Errors:', $this->errors);
        }
    }

    protected function processAdministratorRow(array $row): void
    {
        $adminData = $this->cleanRowData($row);

        $user = null;
        if (! empty($adminData['email'])) {
            $user = User::where('email', $adminData['email'])->first();
        }

        if ($user) {
            // Check if user already has an admin-like role or specific admin record if exists
            // For now, if user exists, we skip to avoid changing roles or duplicates
            $this->skippedCount++;

            return;
        }

        User::create([
            'name' => trim($adminData['first_name'].' '.($adminData['other_names'] ? $adminData['other_names'].' ' : '').$adminData['last_name']),
            'first_name' => $adminData['first_name'],
            'last_name' => $adminData['last_name'],
            'other_names' => $adminData['other_names'],
            'email' => $adminData['email'],
            'password' => Hash::make($adminData['password'] ?? $this->defaultPassword),
            'role' => UserRole::ADMIN,
            'phone' => $adminData['phone'],
            'gender' => $adminData['gender'],
            'school_id' => $this->defaultSchoolId,
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
