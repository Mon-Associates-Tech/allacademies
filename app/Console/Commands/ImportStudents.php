<?php

namespace App\Console\Commands;

use App\Services\StudentImportService;
use Illuminate\Console\Command;
use Illuminate\Http\UploadedFile;
use Symfony\Component\HttpFoundation\File\File;

class ImportStudents extends Command
{
    protected $signature = 'students:import 
                           {file : Path to the CSV file to import}
                           {--school-id= : Default school ID for students}
                           {--password=password123 : Default password for new users}
                           {--validate-only : Only validate the file structure without importing}';

    protected $description = 'Import students from a CSV file';

    protected StudentImportService $importService;

    public function __construct(StudentImportService $importService)
    {
        parent::__construct();
        $this->importService = $importService;
    }

    public function handle(): int
    {
        $filePath = $this->argument('file');
        
        if (!file_exists($filePath)) {
            $this->error("File not found: {$filePath}");
            return 1;
        }

        $file = new UploadedFile($filePath, basename($filePath), null, null, true);

        // Validate CSV structure first
        $this->info('Validating CSV structure...');
        $validation = $this->importService->validateCsvStructure($file);
        
        if (!$validation['valid']) {
            $this->error('CSV validation failed: ' . $validation['message']);
            return 1;
        }

        $this->info('✓ CSV structure is valid');
        $this->info("Found {$validation['total_rows']} rows to process");

        if ($this->option('validate-only')) {
            $this->info('Validation complete. Use without --validate-only to import.');
            return 0;
        }

        // Perform import
        $this->info('Starting import process...');
        
        $options = [
            'default_school_id' => $this->option('school-id'),
            'default_password' => $this->option('password'),
        ];

        $result = $this->importService->importFromFile($file, $options);

        if ($result['success']) {
            $stats = $result['stats'];
            $this->info('Import completed successfully!');
            $this->table(
                ['Metric', 'Count'],
                [
                    ['Imported', $stats['imported']],
                    ['Skipped', $stats['skipped']],
                    ['Errors', $stats['errors']],
                ]
            );

            if ($stats['errors'] > 0) {
                $this->warn('Some errors occurred during import:');
                foreach ($stats['error_details'] as $error) {
                    $this->error("Row error: {$error['error']}");
                }
            }
        } else {
            $this->error('Import failed: ' . $result['message']);
            return 1;
        }

        return 0;
    }
}
