<?php

namespace App\Services;

use App\Imports\StudentsImporter;
use App\Exports\StudentsExporter;
use App\Exports\TeachersExporter;
use App\Exports\BooksExporter;
use Illuminate\Http\UploadedFile;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImportExportService
{
    protected $availableImporters = [
        'students' => StudentsImporter::class,
        // Add more importers as they are created
    ];

    protected $availableExporters = [
        'students' => StudentsExporter::class,
        'teachers' => TeachersExporter::class,
        'books' => BooksExporter::class,
        // Add more exporters as they are created
    ];

    public function validateImportFile(UploadedFile $file, string $model): array
    {
        if (!isset($this->availableImporters[$model])) {
            return [
                'valid' => false,
                'message' => "Import not supported for model: {$model}"
            ];
        }

        $requiredColumns = $this->getRequiredColumns($model);
        $validator = new CsvValidator();

        return $validator->validateCsvStructure($file, $requiredColumns);
    }

    public function performImport(UploadedFile $file, string $model, array $options = []): array
    {
        if (!isset($this->availableImporters[$model])) {
            return [
                'success' => false,
                'message' => "Import not supported for model: {$model}"
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
                'stats' => $stats
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage(),
                'details' => $e->getTrace()
            ];
        }
    }

    public function performExport(string $model, string $format, array $filters = [], array $options = []): array
    {
        if (!isset($this->availableExporters[$model])) {
            return [
                'success' => false,
                'message' => "Export not supported for model: {$model}"
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
                'download_url' => Storage::disk('public')->url($path)
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    public function generateSampleFile(string $model): array
    {
        $sampleData = $this->getSampleData($model);

        if (empty($sampleData)) {
            return [
                'success' => false,
                'message' => 'No sample data available for this model'
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
                'download_url' => Storage::disk('public')->url($path)
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Error generating sample file: ' . $e->getMessage()
            ];
        }
    }

    protected function getRequiredColumns(string $model): array
    {
        $columns = [
            'students' => ['name', 'email'],
            'teachers' => ['name', 'email'],
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
                    'name' => 'John Doe',
                    'first_name' => 'John',
                    'last_name' => 'Doe',
                    'email' => 'john.doe@example.com',
                    'academic_level' => 'Grade 10',
                    'academic_group' => 'Science Group',
                    'school' => 'Main Campus',
                    'student_group' => 'Class A',
                    'student_id' => 'STU001'
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
                    'student_id' => 'STU002'
                ]
            ],
            'teachers' => [
                [
                    'name' => 'Dr. Alice Johnson',
                    'first_name' => 'Alice',
                    'last_name' => 'Johnson',
                    'email' => 'alice.johnson@school.edu',
                    'department' => 'Mathematics',
                    'school' => 'Main Campus',
                    'employee_id' => 'TCH001'
                ]
            ],
            'books' => [
                [
                    'title' => 'Introduction to Physics',
                    'author' => 'John Scientific',
                    'isbn' => '978-0123456789',
                    'category' => 'Science',
                    'pages' => '450',
                    'published_year' => '2023'
                ]
            ]
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
