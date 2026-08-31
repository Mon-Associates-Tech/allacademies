<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\ExaminationHub\Models\GeneralExamParticipantGroup;
use App\ExaminationHub\Models\GeneralExamParticipantGroupMember;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ImportParticipantsCommand extends Command
{
    protected $signature = 'participants:import {file : The path to the CSV file}';
    protected $description = 'Import participants into a Course -> Programme -> Member hierarchy';

    public function handle()
    {
        $filePath = $this->argument('file');

        if (!file_exists($filePath)) {
            $this->error("File not found: {$filePath}");
            return Command::FAILURE;
        }

        if (Str::endsWith(strtolower($filePath), ['.xlsx', '.xls'])) {
            $this->error("This command expects a CSV file. Please convert your Excel file to CSV first.");
            return Command::FAILURE;
        }

        $this->info("Reading file: {$filePath}");
        $rows = $this->parseCsv($filePath);

        if (empty($rows)) {
            $this->error("No valid data found in the file.");
            return Command::FAILURE;
        }

        // 1. Group data hierarchically: Course -> Programme -> Students
        $hierarchy = [];
        foreach ($rows as $row) {
            $course = trim($row['course'] ?? '');
            $programme = trim($row['programme'] ?? '');
            $name = trim($row['name'] ?? '');
            $indexNumber = trim($row['index_number'] ?? '');
            $email = trim($row['email'] ?? '');

            if (empty($course) || empty($programme) || empty($name) || empty($email)) {
                continue; 
            }

            // Initialize arrays if they don't exist
            if (!isset($hierarchy[$course])) {
                $hierarchy[$course] = [];
            }
            if (!isset($hierarchy[$course][$programme])) {
                $hierarchy[$course][$programme] = [];
            }

            $hierarchy[$course][$programme][] = [
                'name' => $name,
                'index_number' => $indexNumber,
                'email' => $email,
            ];
        }

        // 2. Sort the hierarchy (Courses alphabetically, then Programmes, then Students)
        ksort($hierarchy); // Sort Courses
        foreach ($hierarchy as $course => &$programmes) {
            ksort($programmes); // Sort Programmes
            foreach ($programmes as $programme => &$members) {
                usort($members, fn($a, $b) => strcmp($a['name'], $b['name'])); // Sort Students
            }
        }

        $totalCourses = count($hierarchy);
        $bar = $this->output->createProgressBar($totalCourses);
        $bar->start();

        // 3. Save to Database
        DB::transaction(function () use ($hierarchy, $bar) {
            foreach ($hierarchy as $course => $programmes) {
                
                // Create/Find the COURSE group (Level 1: parent_id = null)
                $courseGroup = GeneralExamParticipantGroup::firstOrCreate(
                    ['name' => $course, 'parent_id' => null],
                    ['description' => "Course: {$course}"]
                );

                foreach ($programmes as $programme => $members) {
                    
                    // Create/Find the PROGRAMME group (Level 2: parent_id = courseGroup->id)
                    $programmeGroup = GeneralExamParticipantGroup::firstOrCreate(
                        ['name' => $programme, 'parent_id' => $courseGroup->id],
                        ['description' => "Programme: {$programme} under {$course}"]
                    );

                    // Insert/Update MEMBERS into the Programme group
                    foreach ($members as $memberData) {
                        GeneralExamParticipantGroupMember::updateOrCreate(
                            [
                                'group_id' => $programmeGroup->id,
                                'email'    => $memberData['email'],
                            ],
                            [
                                'name'        => $memberData['name'], // Clean name, no programme prefix
                                'unique_code' => $memberData['index_number'],
                            ]
                        );
                    }
                }
                $bar->advance();
            }
        });

        $bar->finish();
        $this->newLine();
        $this->info('✅ Participants imported successfully into Course -> Programme -> Member hierarchy!');

        return Command::SUCCESS;
    }

    protected function parseCsv($filePath)
    {
        $rows = [];
        $handle = fopen($filePath, 'r');

        if ($handle !== false) {
            $headers = fgetcsv($handle);
            if ($headers === false) {
                fclose($handle);
                return [];
            }

            $headers = array_map('trim', $headers);
            $headers = array_map('strtolower', $headers);

            while (($data = fgetcsv($handle)) !== false) {
                if (count($data) === count($headers)) {
                    $rows[] = array_combine($headers, $data);
                }
            }
            fclose($handle);
        }

        return $rows;
    }
}
