<?php

namespace App\Http\Controllers;

class ImportTemplateController extends Controller
{
    public function download($type)
    {
        $templates = [
            'students' => $this->getStudentTemplate(),
            'teachers' => $this->getTeacherTemplate(),
            'librarians' => $this->getLibrarianTemplate(),
            'administrators' => $this->getAdministratorTemplate(),
            'parents' => $this->getParentTemplate(),
            'accountants' => $this->getAccountantTemplate(),
        ];

        if (! isset($templates[$type])) {
            abort(404);
        }

        $template = $templates[$type];
        $filename = ucfirst($type).'_Import_Template_'.date('Y-m-d').'.csv';

        return response()->streamDownload(function () use ($template) {
            echo $template;
        }, $filename, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ]);
    }

    public function viewFormats()
    {
        $formats = [
            'students' => [
                'title' => 'Students Import Format',
                'description' => 'Upload CSV file with student information. Student records will be linked to a user account.',
                'required_columns' => [
                    'first_name' => 'Student\'s first name (required)',
                    'last_name' => 'Student\'s last name (required)',
                    'email' => 'Student\'s email address (required, must be unique across the system)',
                    'academic_group_id' => 'ID of academic group (must exist in system)',
                    'academic_level_id' => 'ID of academic level (must exist in system)',
                ],
                'optional_columns' => [
                    'other_name', 'username', 'password', 'phone', 'date_of_birth', 'gender', 'student_id',
                    'admission_date', 'blood_group', 'address', 'city', 'region', 'country',
                    'parent_name', 'parent_phone', 'parent_email', 'emergency_contact',
                    'bio', 'favorite_subjects', 'learning_goals', 'school_name', 'student_group_name',
                    // New: control whether a login/user is created for the imported student. Defaults to 1 when missing.
                    'create_login',
                ],
            ],
            'teachers' => [
                'title' => 'Teachers Import Format',
                'description' => 'Upload CSV file with teacher information',
                'required_columns' => [
                    'first_name' => 'Teacher\'s first name (required)',
                    'last_name' => 'Teacher\'s last name (required)',
                    'email' => 'Teacher\'s email address (required, must be unique)',
                ],
                'optional_columns' => [
                    'other_names', 'phone', 'date_of_birth', 'gender', 'qualification', 'specialization',
                    'employee_id', 'hire_date', 'address', 'city', 'region', 'country',
                    'emergency_contact', 'department',
                ],
            ],
            'librarians' => [
                'title' => 'Librarians Import Format',
                'description' => 'Upload CSV file with librarian information',
                'required_columns' => [
                    'first_name' => 'Librarian\'s first name (required)',
                    'last_name' => 'Librarian\'s last name (required)',
                    'email' => 'Librarian\'s email address (required, must be unique)',
                ],
                'optional_columns' => [
                    'phone', 'date_of_birth', 'gender', 'qualification',
                    'employee_id', 'hire_date', 'address', 'city', 'region', 'country',
                    'emergency_contact',
                ],
            ],
            'administrators' => [
                'title' => 'Administrators Import Format',
                'description' => 'Upload CSV file with administrator information',
                'required_columns' => [
                    'first_name' => 'Administrator\'s first name (required)',
                    'last_name' => 'Administrator\'s last name (required)',
                    'email' => 'Administrator\'s email address (required, must be unique)',
                ],
                'optional_columns' => [
                    'phone', 'date_of_birth', 'gender', 'position', 'department',
                    'employee_id', 'hire_date', 'address', 'city', 'region', 'country',
                    'emergency_contact', 'qualification',
                ],
            ],
            'parents' => [
                'title' => 'Parents Import Format',
                'description' => 'Upload CSV file with parent/guardian information',
                'required_columns' => [
                    'first_name' => 'Parent\'s first name (required)',
                    'last_name' => 'Parent\'s last name (required)',
                    'email' => 'Parent\'s email address (required, must be unique)',
                    'student_id' => 'Student ID to link parent to (must exist)',
                ],
                'optional_columns' => [
                    'phone', 'address', 'city', 'region', 'country',
                    'relationship', 'occupation', 'emergency_contact',
                ],
            ],
            'accountants' => [
                'title' => 'Accountants Import Format',
                'description' => 'Upload CSV file with accountant information',
                'required_columns' => [
                    'first_name' => 'Accountant\'s first name (required)',
                    'last_name' => 'Accountant\'s last name (required)',
                    'email' => 'Accountant\'s email address (required, must be unique)',
                ],
                'optional_columns' => [
                    'phone', 'date_of_birth', 'gender',
                    'employee_id', 'hire_date', 'address', 'city', 'region', 'country',
                ],
            ],
        ];

        return view('livewire.school.import-formats', compact('formats'));
    }

    private function getStudentTemplate()
    {
        $headers = [
            'first_name',
            'last_name',
            'other_name',
            'email',
            'username',
            'password',
            'phone',
            'date_of_birth',
            'gender',
            'academic_group_id',
            'academic_level_id',
            'student_id',
            'admission_date',
            'blood_group',
            'address',
            'city',
            'region',
            'country',
            'parent_name',
            'parent_phone',
            'parent_email',
            'emergency_contact',
            'bio',
            'favorite_subjects',
            'learning_goals',
            'school_name',
            'student_group_name',
            // When set to 0 or false, import will not create a user account for the student. Defaults to 1 when missing.
            'create_login',
        ];

        $example = [
            'John',
            'Doe',
            'Junior',
            'john.doe@example.com',
            'johndoe24',
            'password123',
            '+233201234567',
            '2010-05-15',
            'Male',
            '1',
            '1',
            'STD2024001',
            '2024-01-15',
            'O+',
            '123 Main Street',
            'Accra',
            'Greater Accra',
            'Ghana',
            'Jane Doe',
            '+233207654321',
            'jane.doe@example.com',
            '+233501234567',
            'Interested in science and art.',
            'Science, Mathematics',
            'Improve coding skills.',
            'Springfield Academy',
            'Year 10A',
            // create_login example: 1 = create user (default), 0 = skip user creation
            '1',
        ];

        return $this->generateCsv($headers, [$example]);
    }

    private function getTeacherTemplate()
    {
        $headers = [
            'first_name',
            'last_name',
            'other_names',
            'email',
            'phone',
            'date_of_birth',
            'gender',
            'qualification',
            'specialization',
            'employee_id',
            'hire_date',
            'address',
            'city',
            'region',
            'country',
            'emergency_contact',
            'department',
        ];

        $example = [
            'Alice',
            'Smith',
            '',
            'alice.smith@school.com',
            '+233301234567',
            '1985-03-20',
            'Female',
            'Masters in Mathematics',
            'Mathematics',
            'TCH2024001',
            '2024-01-01',
            '456 Teacher Lane',
            'Accra',
            'Greater Accra',
            'Ghana',
            '+233507654321',
            'Mathematics Department',
        ];

        return $this->generateCsv($headers, [$example]);
    }

    private function getLibrarianTemplate()
    {
        $headers = [
            'first_name',
            'last_name',
            'email',
            'phone',
            'date_of_birth',
            'gender',
            'qualification',
            'employee_id',
            'hire_date',
            'address',
            'city',
            'state',
            'country',
            'emergency_contact',
            'certification',
        ];

        $example = [
            'Robert',
            'Johnson',
            'robert.johnson@school.com',
            '+233401234567',
            '1990-07-10',
            'Male',
            'Bachelors in Library Science',
            'LIB2024001',
            '2024-01-01',
            '789 Library Road',
            'Accra',
            'Greater Accra',
            'Ghana',
            '+233607654321',
            'Certified Librarian',
        ];

        return $this->generateCsv($headers, [$example]);
    }

    private function getAdministratorTemplate()
    {
        $headers = [
            'first_name',
            'last_name',
            'email',
            'phone',
            'date_of_birth',
            'gender',
            'position',
            'department',
            'employee_id',
            'hire_date',
            'address',
            'city',
            'state',
            'country',
            'emergency_contact',
            'qualification',
        ];

        $example = [
            'Mary',
            'Williams',
            'mary.williams@school.com',
            '+233501234567',
            '1980-11-25',
            'Female',
            'Principal',
            'Administration',
            'ADM2024001',
            '2024-01-01',
            '321 Admin Street',
            'Accra',
            'Greater Accra',
            'Ghana',
            '+233707654321',
            'Masters in Educational Leadership',
        ];

        return $this->generateCsv($headers, [$example]);
    }

    private function getParentTemplate()
    {
        $headers = [
            'first_name',
            'last_name',
            'email',
            'phone',
            'address',
            'city',
            'state',
            'country',
            'postal_code',
            'relationship',
            'occupation',
            'student_id',
            'emergency_contact',
            'secondary_phone',
        ];

        $example = [
            'David',
            'Brown',
            'david.brown@example.com',
            '+233601234567',
            '654 Parent Avenue',
            'Accra',
            'Greater Accra',
            'Ghana',
            '00233',
            'Father',
            'Engineer',
            'STD2024001',
            '+233807654321',
            '+233901234567',
        ];

        return $this->generateCsv($headers, [$example]);
    }

    private function getAccountantTemplate()
    {
        $headers = [
            'first_name',
            'last_name',
            'other_names',
            'email',
            'phone',
            'date_of_birth',
            'gender',
            'employee_id',
            'hire_date',
            'address',
            'city',
            'region',
            'country',
        ];

        $example = [
            'Sarah',
            'Miller',
            '',
            'sarah.miller@school.com',
            '+233701234567',
            '1985-06-20',
            'Female',
            'ACC2024001',
            '2024-01-01',
            '321 Finance Blvd',
            'Accra',
            'Greater Accra',
            'Ghana',
        ];

        return $this->generateCsv($headers, [$example]);
    }

    private function generateCsv(array $headers, array $rows)
    {
        $output = fopen('php://temp', 'r+');

        // Write headers
        fputcsv($output, $headers);

        // Write example rows
        foreach ($rows as $row) {
            fputcsv($output, $row);
        }

        rewind($output);
        $csv = stream_get_contents($output);
        fclose($output);

        return $csv;
    }
}
