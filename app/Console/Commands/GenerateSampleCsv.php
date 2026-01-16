<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class GenerateSampleCsv extends Command
{
    protected $signature = 'samples:generate {type=students : Type of sample to generate (students, teachers, books)}';

    protected $description = 'Generate sample CSV files for import testing';

    public function handle()
    {
        $type = $this->argument('type');

        switch ($type) {
            case 'students':
                $this->generateStudentsSample();
                break;
            case 'teachers':
                $this->generateTeachersSample();
                break;
            case 'books':
                $this->generateBooksSample();
                break;
            default:
                $this->error("Unknown sample type: {$type}");

                return 1;
        }

        return 0;
    }

    private function generateStudentsSample()
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
                'password' => 'student123',
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
                'password' => 'student456',
            ],
            [
                'name' => 'Michael Johnson',
                'first_name' => 'Michael',
                'last_name' => 'Johnson',
                'email' => 'michael.johnson@example.com',
                'academic_level' => 'Grade 9',
                'academic_group' => 'Science Group',
                'school' => 'West Campus',
                'student_group' => 'Class C',
                'student_id' => 'STU003',
                'date_of_birth' => '2009-08-10',
                'phone' => '+1234567892',
                'address' => '789 Pine Rd, City, State',
                'password' => 'student789',
            ],
            [
                'name' => 'Sarah Wilson',
                'first_name' => 'Sarah',
                'last_name' => 'Wilson',
                'email' => 'sarah.wilson@example.com',
                'academic_level' => 'Grade 12',
                'academic_group' => 'Arts Group',
                'school' => 'Main Campus',
                'student_group' => 'Class D',
                'student_id' => 'STU004',
                'date_of_birth' => '2006-12-03',
                'phone' => '+1234567893',
                'address' => '321 Elm St, City, State',
                'password' => 'student321',
            ],
            [
                'name' => 'David Brown',
                'first_name' => 'David',
                'last_name' => 'Brown',
                'email' => 'david.brown@example.com',
                'academic_level' => 'Grade 10',
                'academic_group' => 'Mathematics Group',
                'school' => 'East Campus',
                'student_group' => 'Class E',
                'student_id' => 'STU005',
                'date_of_birth' => '2008-03-28',
                'phone' => '+1234567894',
                'address' => '654 Maple Ave, City, State',
                'password' => 'student654',
            ],
        ];

        $filename = 'sample_students_import_'.date('Y-m-d_H-i-s').'.csv';
        $path = 'samples/'.$filename;

        $csvContent = $this->arrayToCsv($sampleData);
        Storage::disk('public')->put($path, $csvContent);

        $this->info("Sample students CSV generated: {$filename}");
        $this->info('Location: '.Storage::disk('public')->path($path));
        $this->info('Download URL: '.Storage::disk('public')->url($path));
    }

    private function generateTeachersSample()
    {
        $sampleData = [
            [
                'name' => 'Dr. Alice Johnson',
                'first_name' => 'Alice',
                'last_name' => 'Johnson',
                'email' => 'alice.johnson@school.edu',
                'department' => 'Mathematics',
                'school' => 'Main Campus',
                'employee_id' => 'TCH001',
                'phone' => '+1234567800',
                'specialization' => 'Algebra, Calculus',
                'password' => 'teacher123',
            ],
            [
                'name' => 'Prof. Bob Smith',
                'first_name' => 'Robert',
                'last_name' => 'Smith',
                'email' => 'bob.smith@school.edu',
                'department' => 'Science',
                'school' => 'West Campus',
                'employee_id' => 'TCH002',
                'phone' => '+1234567801',
                'specialization' => 'Physics, Chemistry',
                'password' => 'teacher456',
            ],
        ];

        $filename = 'sample_teachers_import_'.date('Y-m-d_H-i-s').'.csv';
        $path = 'samples/'.$filename;

        $csvContent = $this->arrayToCsv($sampleData);
        Storage::disk('public')->put($path, $csvContent);

        $this->info("Sample teachers CSV generated: {$filename}");
        $this->info('Location: '.Storage::disk('public')->path($path));
    }

    private function generateBooksSample()
    {
        $sampleData = [
            [
                'title' => 'Introduction to Physics',
                'author' => 'John Scientific',
                'isbn' => '978-0123456789',
                'category' => 'Science',
                'pages' => '450',
                'published_year' => '2023',
                'publisher' => 'Academic Press',
                'language' => 'English',
                'availability' => 'Available',
            ],
            [
                'title' => 'Advanced Mathematics',
                'author' => 'Jane Mathematical',
                'isbn' => '978-9876543210',
                'category' => 'Mathematics',
                'pages' => '650',
                'published_year' => '2022',
                'publisher' => 'Education Books',
                'language' => 'English',
                'availability' => 'Available',
            ],
        ];

        $filename = 'sample_books_import_'.date('Y-m-d_H-i-s').'.csv';
        $path = 'samples/'.$filename;

        $csvContent = $this->arrayToCsv($sampleData);
        Storage::disk('public')->put($path, $csvContent);

        $this->info("Sample books CSV generated: {$filename}");
        $this->info('Location: '.Storage::disk('public')->path($path));
    }

    private function arrayToCsv(array $data): string
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
}
