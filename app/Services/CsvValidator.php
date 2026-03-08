<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class CsvValidator
{
    public function validateCsvStructure(UploadedFile $file, array $requiredColumns): array
    {
        try {
            // Store file temporarily
            $path = $file->store('temp/validation', 'local');
            $fullPath = Storage::path($path);

            // Read CSV manually
            $handle = fopen($fullPath, 'r');
            if (! $handle) {
                throw new \Exception('Could not open CSV file');
            }

            // Read header row
            $headers = fgetcsv($handle);
            if (! $headers) {
                throw new \Exception('Could not read header row');
            }

            // Clean and normalize headers
            $cleanHeaders = array_map(function ($header) {
                return strtolower(trim($header, " \t\n\r\0\x0B\""));
            }, $headers);

            // Normalize required columns
            $normalizedRequired = array_map('strtolower', $requiredColumns);

            // Check for missing columns
            $missingColumns = array_diff($normalizedRequired, $cleanHeaders);

            if (! empty($missingColumns)) {
                fclose($handle);

                return [
                    'valid' => false,
                    'message' => 'Missing required columns: '.implode(', ', $missingColumns),
                    'required_columns' => $requiredColumns,
                    'found_columns' => $cleanHeaders,
                    'raw_headers' => $headers,
                ];
            }

            // Count data rows
            $dataRowCount = 0;
            $sampleRow = null;

            while (($row = fgetcsv($handle)) !== false) {
                if ($dataRowCount === 0) {
                    $sampleRow = array_combine($cleanHeaders, $row);
                }
                $dataRowCount++;

                // Only read first few rows for validation
                if ($dataRowCount > 5) {
                    break;
                }
            }

            fclose($handle);

            if ($dataRowCount === 0) {
                return [
                    'valid' => false,
                    'message' => 'No data rows found in CSV',
                ];
            }

            // Validate sample row has required data
            $emptyRequired = [];
            foreach ($normalizedRequired as $required) {
                if (empty(trim($sampleRow[$required] ?? ''))) {
                    $emptyRequired[] = $required;
                }
            }

            if (! empty($emptyRequired)) {
                return [
                    'valid' => false,
                    'message' => 'Sample row has empty required fields: '.implode(', ', $emptyRequired),
                    'sample_row' => $sampleRow,
                ];
            }

            return [
                'valid' => true,
                'message' => 'CSV structure is valid',
                'total_rows' => $dataRowCount,
                'required_columns' => $requiredColumns,
                'found_columns' => $cleanHeaders,
                'sample_row' => $sampleRow,
            ];

        } catch (\Exception $e) {
            return [
                'valid' => false,
                'message' => 'Error reading CSV: '.$e->getMessage(),
            ];
        } finally {
            if (isset($path)) {
                Storage::delete($path);
            }
        }
    }
}
