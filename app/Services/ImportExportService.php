<?php

namespace App\Services;

use App\Exports\BooksExporter;
use App\Exports\StudentsExporter;
use App\Exports\TeachersExporter;
use App\Imports\AccountantsImporter;
use App\Imports\AdministratorsImporter;
use App\Imports\LibrariansImporter;
use App\Imports\ParentsImporter;
use App\Imports\StudentsImporter;
use App\Imports\TeachersImporter;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class ImportExportService
{
    protected $availableImporters = [
        'students' => StudentsImporter::class,
        'teachers' => TeachersImporter::class,
        'librarians' => LibrariansImporter::class,
        'administrators' => AdministratorsImporter::class,
        'parents' => ParentsImporter::class,
        'accountants' => AccountantsImporter::class,
    ];

    protected $availableExporters = [
        'students' => StudentsExporter::class,
        'teachers' => TeachersExporter::class,
        'books' => BooksExporter::class,
        // Add more exporters as they are created
    ];

    public function validateImportFile(UploadedFile $file, string $model): array
    {
        if (! isset($this->availableImporters[$model])) {
            return [
                'valid' => false,
                'message' => "Import not supported for model: {$model}",
            ];
        }

        $requiredColumns = $this->getRequiredColumns($model);
        $validator = new CsvValidator;

        return $validator->validateCsvStructure($file, $requiredColumns);
    }

    public function performImport(UploadedFile $file, string $model, array $options = []): array
    {
        if (! isset($this->availableImporters[$model])) {
            return [
                'success' => false,
                'message' => "Import not supported for model: {$model}",
            ];
        }

        try {
            $importerClass = $this->availableImporters[$model];
            $importer = new $importerClass($options);

            Excel::import($importer, $file);

            $stats = method_exists($importer, 'getImportStats')
                ? $importer->getImportStats()
                : ['imported' => 'unknown', 'skipped' => 0, 'errors' => 0];

            return [
                'success' => true,
                'message' => 'Import completed successfully',
                'stats' => $stats,
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage(),
                'details' => $e->getTrace(),
            ];
        }
    }

    public function performExport(string $model, string $format, array $filters = [], array $options = []): array
    {
        if (! isset($this->availableExporters[$model])) {
            return [
                'success' => false,
                'message' => "Export not supported for model: {$model}",
            ];
        }

        try {
            $exporterClass = $this->availableExporters[$model];
            $exporter = new $exporterClass($filters, $options);

            $filename = $this->generateExportFilename($model, $format);
            $path = "exports/{$filename}";

            // Store in public disk for download
            Excel::store($exporter, $path, 'public');

            return [
                'success' => true,
                'message' => 'Export completed successfully',
                'filename' => $filename,
                'download_url' => Storage::disk('public')->url($path),
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    public function generateSampleFile(string $model): array
    {
        $sampleData = $this->getSampleData($model);

        if (empty($sampleData)) {
            return [
                'success' => false,
                'message' => 'No sample data available for this model',
            ];
        }

        try {
            $filename = "sample_{$model}_import.csv";
            $path = "samples/{$filename}";

            // Create CSV content
            $csvContent = $this->generateCsvContent($sampleData);
            Storage::disk('public')->put($path, $csvContent);

            return [
                'success' => true,
                'filename' => $filename,
                'download_url' => Storage::disk('public')->url($path),
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Error generating sample file: '.$e->getMessage(),
            ];
        }
    }

    protected function getRequiredColumns(string $model): array
    {
        $columns = [
            'students' => [
                'first_name', 'last_name', 'email', 'academic_level_id', 'academic_group_id',
            ],
            'teachers' => ['first_name', 'last_name', 'email'],
            'librarians' => ['first_name', 'last_name', 'email'],
            'administrators' => ['first_name', 'last_name', 'email'],
            'parents' => ['first_name', 'last_name', 'email'],
            'accountants' => ['first_name', 'last_name', 'email'],
            'books' => ['title', 'author'],
            'academic_subjects' => ['name'],
            'academic_levels' => ['name'],
            'schools' => ['name'],
        ];

        return $columns[$model] ?? [];
    }

    protected function getSampleData(string $model): array
    {
        $samples = [
            'students' => [
                [
                    'first_name' => 'John',
                    'last_name' => 'Doe',
                    'other_name' => 'Junior',
                    'email' => 'john.doe@example.com',
                    'phone' => '+233201234567',
                    'date_of_birth' => '2010-05-15',
                    'gender' => 'Male',
                    'academic_group_id' => '1',
                    'academic_level_id' => '1',
                    'student_id' => 'STD2024001',
                    'admission_date' => '2024-01-15',
                    'blood_group' => 'O+',
                    'address' => '123 Main Street',
                    'city' => 'Accra',
                    'region' => 'Greater Accra',
                    'country' => 'Ghana',
                    'parent_name' => 'Jane Doe',
                    'parent_phone' => '+233207654321',
                    'parent_email' => 'jane.doe@example.com',
                    'emergency_contact' => '+233501234567',
                    'bio' => 'Interested in science and art.',
                    'favorite_subjects' => 'Science, Mathematics',
                    'learning_goals' => 'Improve coding skills.',
                ],
            ],
            'teachers' => [
                [
                    'first_name' => 'Alice',
                    'last_name' => 'Johnson',
                    'other_names' => '',
                    'email' => 'alice.johnson@school.edu',
                    'phone' => '+233301234567',
                    'gender' => 'Female',
                    'qualification' => 'Masters in Mathematics',
                    'specialization' => 'Mathematics',
                    'employee_id' => 'TCH2024001',
                    'hire_date' => '2024-01-01',
                    'address' => '456 Teacher Lane',
                    'city' => 'Accra',
                    'region' => 'Greater Accra',
                    'country' => 'Ghana',
                ],
            ],
            'librarians' => [
                [
                    'first_name' => 'Robert',
                    'last_name' => 'Johnson',
                    'email' => 'robert.johnson@school.edu',
                    'phone' => '+233401234567',
                    'gender' => 'Male',
                    'qualification' => 'Bachelors in Library Science',
                    'employee_id' => 'LIB2024001',
                    'hire_date' => '2024-01-01',
                    'address' => '789 Library Road',
                ],
            ],
            'administrators' => [
                [
                    'first_name' => 'Mary',
                    'last_name' => 'Williams',
                    'email' => 'mary.williams@school.edu',
                    'phone' => '+233501234567',
                    'gender' => 'Female',
                    'position' => 'Principal',
                    'department' => 'Administration',
                    'employee_id' => 'ADM2024001',
                    'hire_date' => '2024-01-01',
                ],
            ],
            'parents' => [
                [
                    'first_name' => 'David',
                    'last_name' => 'Brown',
                    'email' => 'david.brown@example.com',
                    'phone' => '+233601234567',
                    'address' => '654 Parent Avenue',
                    'relationship' => 'Father',
                    'occupation' => 'Engineer',
                    'student_id' => 'STD2024001',
                ],
            ],
            'accountants' => [
                [
                    'first_name' => 'Sarah',
                    'last_name' => 'Miller',
                    'email' => 'sarah.miller@school.edu',
                    'phone' => '+233701234567',
                    'gender' => 'Female',
                    'employee_id' => 'ACC2024001',
                    'hire_date' => '2024-01-01',
                    'address' => '321 Finance Blvd',
                    'date_of_birth' => '1985-06-20',
                ],
            ],
            'books' => [
                [
                    'title' => 'Introduction to Physics',
                    'author' => 'John Scientific',
                    'isbn' => '978-0123456789',
                    'category' => 'Science',
                    'pages' => '450',
                    'published_year' => '2023',
                ],
            ],
        ];

        return $samples[$model] ?? [];
    }

    protected function generateCsvContent(array $data): string
    {
        if (empty($data)) {
            return '';
        }

        $output = fopen('php://temp', 'r+');

        // Add header
        fputcsv($output, array_keys($data[0]));

        // Add data rows
        foreach ($data as $row) {
            fputcsv($output, $row);
        }

        rewind($output);
        $csvContent = stream_get_contents($output);
        fclose($output);

        return $csvContent;
    }

    protected function generateExportFilename(string $model, string $format): string
    {
        $timestamp = now()->format('Y-m-d_H-i-s');

        return "{$model}_export_{$timestamp}.{$format}";
    }
}
