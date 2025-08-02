<?php

namespace App\Services;

use App\Imports\StudentsImporter;
use Illuminate\Http\UploadedFile;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;

class StudentImportService
{
    public function importFromFile(UploadedFile $file, array $options = []): array
    {
        // Store the file temporarily
        $path = $file->store('imports/students', 'local');
        
        try {
            // Create importer instance with options
            $importer = new StudentsImporter(
                $options['default_school_id'] ?? null,
                $options['default_password'] ?? 'password123'
            );

            // Import the file
            Excel::import($importer, Storage::path($path));

            // Get import statistics
            $stats = $importer->getImportStats();

            return [
                'success' => true,
                'message' => "Import completed successfully",
                'stats' => $stats,
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => "Import failed: " . $e->getMessage(),
                'stats' => null,
            ];
        } finally {
            // Clean up temporary file
            Storage::delete($path);
        }
    }

    public function validateCsvStructure(UploadedFile $file): array
    {
        $requiredColumns = ['name', 'email'];
        $optionalColumns = [
            'first_name', 'last_name', 'academic_level', 'academic_group', 
            'school', 'student_group', 'student_id', 'date_of_birth', 
            'phone', 'address', 'password'
        ];

        try {
            $path = $file->store('temp', 'local');
            $data = Excel::toArray([], Storage::path($path))[0];
            
            if (empty($data)) {
                return [
                    'valid' => false,
                    'message' => 'CSV file is empty or invalid'
                ];
            }

            $headers = array_map('strtolower', array_keys($data[0]));
            $missingRequired = array_diff($requiredColumns, $headers);

            if (!empty($missingRequired)) {
                return [
                    'valid' => false,
                    'message' => 'Missing required columns: ' . implode(', ', $missingRequired),
                    'required_columns' => $requiredColumns,
                    'optional_columns' => $optionalColumns,
                    'found_columns' => $headers
                ];
            }

            return [
                'valid' => true,
                'message' => 'CSV structure is valid',
                'required_columns' => $requiredColumns,
                'optional_columns' => $optionalColumns,
                'found_columns' => $headers,
                'total_rows' => count($data)
            ];

        } catch (\Exception $e) {
            return [
                'valid' => false,
                'message' => 'Error reading CSV file: ' . $e->getMessage()
            ];
        } finally {
            if (isset($path)) {
                Storage::delete($path);
            }
        }
    }

    public function generateSampleCsv(): string
    {
        $sampleData = [
            [
                'name' => 'John Doe',
                'first_name' => 'John',
                'last_name' => 'Doe',
                'email' => 'john.doe@example.com',
                'academic_level' => 'Grade 10',
                'academic_group' => 'Science Group',
                'school' => 'Main Campus',
                'student_group' => 'Class A',
                'student_id' => 'STU001',
                'date_of_birth' => '2008-05-15',
                'phone' => '+1234567890',
                'address' => '123 Main St, City, State',
                'password' => 'student123'
            ],
            [
                'name' => 'Jane Smith',
                'first_name' => 'Jane',
                'last_name' => 'Smith',
                'email' => 'jane.smith@example.com',
                'academic_level' => 'Grade 11',
                'academic_group' => 'Arts Group',
                'school' => 'Main Campus',
                'student_group' => 'Class B',
                'student_id' => 'STU002',
                'date_of_birth' => '2007-09-22',
                'phone' => '+1234567891',
                'address' => '456 Oak Ave, City, State',
                'password' => 'student456'
            ]
        ];

        $filename = 'sample_students_import.csv';
        $filepath = storage_path('app/public/' . $filename);

        $handle = fopen($filepath, 'w');
        
        // Add header row
        fputcsv($handle, array_keys($sampleData[0]));
        
        // Add data rows
        foreach ($sampleData as $row) {
            fputcsv($handle, $row);
        }
        
        fclose($handle);

        return $filename;
    }
}
