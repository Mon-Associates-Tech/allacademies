<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\AcademicGroup;
use App\Models\AcademicLevel;
use App\Models\School;
use App\Models\Student;
use App\Models\StudentParent;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class SchoolRelationshipSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::beginTransaction();

        try {
            // Create a school
            $school = School::create([
                'name' => 'Greenwood Academy',
                'code' => 'GWA0001',
                'email' => 'info@greenwoodacademy.edu',
                'phone' => '+1234567890',
                'website' => 'https://greenwoodacademy.edu',
                'address' => '123 Education Street',
                'city' => 'Springfield',
                'state' => 'State',
                'country' => 'Country',
                'postal_code' => '12345',
                'type' => 'secondary',
                'description' => 'A comprehensive educational institution',
                'status' => 'active',
                'subscription_plan' => 'premium',
                'subscription_ends_at' => now()->addYear(),
                'timezone' => 'UTC',
                'currency' => 'USD',
                'academic_year_start' => now()->startOfYear(),
                'academic_year_end' => now()->endOfYear(),
            ]);

            $this->command->info("✓ School created: {$school->name}");

            // Get the first available academic group
            $academicGroup = AcademicGroup::first();

            if (! $academicGroup) {
                $this->command->error('✗ No academic groups found. Please seed academic groups first.');
                DB::rollBack();

                return;
            }

            $this->command->info("✓ Using Academic Group: {$academicGroup->name} (ID: {$academicGroup->id})");

            // Attach academic group to school
            $school->academicGroups()->syncWithoutDetaching([
                $academicGroup->id => ['is_active' => true],
            ]);

            // Get available academic levels for this group
            $academicLevels = AcademicLevel::where('academic_group_id', $academicGroup->id)
                ->take(3)
                ->get();

            if ($academicLevels->isEmpty()) {
                $this->command->error("✗ No academic levels found for group ID {$academicGroup->id}. Please seed academic levels first.");
                DB::rollBack();

                return;
            }

            $this->command->info("✓ Found {$academicLevels->count()} academic levels");

            // Attach academic levels to school
            foreach ($academicLevels as $index => $level) {
                $school->academicLevels()->syncWithoutDetaching([
                    $level->id => [
                        'is_active' => true,
                        'sort_order' => $index + 1,
                        'academic_group_id' => $academicGroup->id,
                    ],
                ]);
                $this->command->info("  - {$level->name} (ID: {$level->id})");
            }

            // Create Teachers (3 teachers)
            $teachers = [];
            $teacherData = [
                [
                    'name' => 'John Smith',
                    'email' => 'john.smith@greenwoodacademy.edu',
                    'department' => 'Mathematics',
                ],
                [
                    'name' => 'Sarah Johnson',
                    'email' => 'sarah.johnson@greenwoodacademy.edu',
                    'department' => 'English',
                ],
                [
                    'name' => 'Michael Brown',
                    'email' => 'michael.brown@greenwoodacademy.edu',
                    'department' => 'Science',
                ],
            ];

            foreach ($teacherData as $data) {
                $user = User::create([
                    'school_id' => $school->id,
                    'name' => $data['name'],
                    'email' => $data['email'],
                    'password' => Hash::make('password'),
                    'role' => UserRole::TEACHER,
                    'status' => 'active',
                    'is_active' => true,
                    'email_verified_at' => now(),
                ]);

                $teacher = Teacher::where('user_id', $user->id)->first();

                if (! $teacher) {
                    $teacher = Teacher::create([
                        'school_id' => $school->id,
                        'user_id' => $user->id,
                        'employee_id' => Teacher::generateEmployeeId($school->id),
                        'department' => $data['department'],
                        'hire_date' => now()->subYears(rand(1, 5)),
                        'employment_type' => 'full_time',
                        'status' => 'active',
                        'salary' => rand(40000, 80000),
                    ]);
                }

                // Assign teachers to academic levels using IDs
                $levelAssignments = [];
                foreach ($academicLevels as $index => $level) {
                    $levelAssignments[$level->id] = [
                        'is_primary' => $index === 0, // First level is primary for each teacher
                    ];
                }
                $teacher->academicLevels()->attach($levelAssignments);

                // Assign teacher to academic group
                $teacher->academicGroups()->attach($academicGroup->id, [
                    'is_primary' => count($teachers) === 0, // First teacher is primary
                ]);

                $teachers[] = $teacher;
                $this->command->info("✓ Teacher created: {$user->name}");
            }

            // Create Parents (5 parents)
            $parents = [];
            $parentNames = [
                ['Robert Davis', 'robert.davis@example.com'],
                ['Jennifer Wilson', 'jennifer.wilson@example.com'],
                ['David Martinez', 'david.martinez@example.com'],
                ['Lisa Anderson', 'lisa.anderson@example.com'],
                ['James Taylor', 'james.taylor@example.com'],
            ];

            foreach ($parentNames as [$name, $email]) {
                $user = User::create([
                    'school_id' => $school->id,
                    'name' => $name,
                    'email' => $email,
                    'password' => Hash::make('password'),
                    'role' => UserRole::PARENT,
                    'status' => 'active',
                    'is_active' => true,
                    'email_verified_at' => now(),
                ]);

                $parent = StudentParent::where('user_id', $user->id)->first();

                if (! $parent) {
                    $parent = StudentParent::create([
                        'school_id' => $school->id,
                        'user_id' => $user->id,
                    ]);
                }

                $parents[] = $parent;
                $this->command->info("✓ Parent created: {$user->name}");
            }

            // Create Students (10 students) - distribute across available levels
            $students = [];
            $studentData = [
                ['Emma Davis', 'emma.davis@student.greenwoodacademy.edu', 0, 0],
                ['Oliver Wilson', 'oliver.wilson@student.greenwoodacademy.edu', 0, 1],
                ['Sophia Martinez', 'sophia.martinez@student.greenwoodacademy.edu', 1, 2],
                ['Liam Anderson', 'liam.anderson@student.greenwoodacademy.edu', 1, 3],
                ['Ava Taylor', 'ava.taylor@student.greenwoodacademy.edu', 0, 4],
                ['Noah Davis', 'noah.davis@student.greenwoodacademy.edu', 2, 0],
                ['Isabella Brown', 'isabella.brown@student.greenwoodacademy.edu', 2, 1],
                ['Ethan White', 'ethan.white@student.greenwoodacademy.edu', 1, 2],
                ['Mia Johnson', 'mia.johnson@student.greenwoodacademy.edu', 0, 3],
                ['Lucas Green', 'lucas.green@student.greenwoodacademy.edu', 2, 4],
            ];

            foreach ($studentData as $index => [$name, $email, $levelIndex, $parentIndex]) {
                // Use modulo to handle cases where we have fewer levels
                $levelIndex = $levelIndex % $academicLevels->count();
                $level = $academicLevels[$levelIndex];

                $user = User::create([
                    'school_id' => $school->id,
                    'name' => $name,
                    'email' => $email,
                    'password' => Hash::make('password'),
                    'role' => UserRole::STUDENT,
                    'status' => 'active',
                    'is_active' => true,
                    'email_verified_at' => now(),
                ]);

                $student = Student::where('user_id', $user->id)->first();

                if (! $student) {
                    $student = Student::create([
                        'school_id' => $school->id,
                        'user_id' => $user->id,
                        'student_id' => Student::generateStudentId($school->id),
                        'academic_level_id' => $level->id,
                        'academic_group_id' => $academicGroup->id,
                        'admission_date' => now()->subYears(rand(1, 3)),
                        'status' => 'active',
                    ]);
                }

                // Attach parent to student via pivot table (parent_student)
                $parent = $parents[$parentIndex % count($parents)];
                $parent->students()->attach($student->id, [
                    'relationship' => $parentIndex % 2 === 0 ? 'Father' : 'Mother',
                ]);

                // Attach teachers to students via pivot table (teacher_student)
                // Each student gets 2-3 teachers
                $teacherCount = rand(2, 3);
                $selectedTeachers = collect($teachers)->random(min($teacherCount, count($teachers)));

                foreach ($selectedTeachers as $teacherIndex => $teacher) {
                    $teacher->assignedStudents()->attach($student->id, [
                        'is_primary' => $teacherIndex === 0, // First teacher is primary
                        'notes' => $teacherIndex === 0 ? 'Primary teacher' : 'Secondary teacher',
                    ]);
                }

                $students[] = $student;
                $this->command->info("✓ Student created: {$user->name}");
            }

            // Display summary
            $this->command->newLine();
            $this->command->info('=== Seeding Summary ===');
            $this->command->info("School: {$school->name} (ID: {$school->id})");
            $this->command->info("Academic Group: {$academicGroup->name} (ID: {$academicGroup->id})");
            $this->command->info("Academic Levels: {$academicLevels->count()}");
            $this->command->info('Teachers: '.count($teachers));
            $this->command->info('Parents: '.count($parents));
            $this->command->info('Students: '.count($students));
            $this->command->newLine();

            // Display relationships - reload students with relationships to avoid null issues
            $this->command->info('=== Relationships Created ===');

            // Reload students with all necessary relationships
            $studentsWithRelations = Student::with(['user', 'academicLevel', 'parents.user', 'teachers.user', 'primaryTeacher.user'])
                ->whereIn('id', collect($students)->pluck('id'))
                ->get();

            foreach ($studentsWithRelations as $student) {
                $studentUser = $student->user;
                $studentParents = $student->parents;
                $studentTeachers = $student->teachers;
                $academicLevel = $student->academicLevel;

                $this->command->line("Student: {$studentUser->name}");

                if ($academicLevel) {
                    $this->command->line("  - Level: {$academicLevel->name} (ID: {$academicLevel->id})");
                } else {
                    $this->command->line('  - Level: Not assigned');
                }

                $this->command->line("  - Group: {$academicGroup->name} (ID: {$academicGroup->id})");
                $this->command->line('  - Parents: '.$studentParents->pluck('user.name')->implode(', '));
                $this->command->line('  - Teachers: '.$studentTeachers->pluck('user.name')->implode(', '));

                $primaryTeacher = $student->primaryTeacher()->first();
                $this->command->line('  - Primary Teacher: '.($primaryTeacher?->user->name ?? 'None'));
            }

            DB::commit();
            $this->command->newLine();
            $this->command->info('✓ Seeding completed successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error('✗ Seeding failed: '.$e->getMessage());
            $this->command->error('Stack trace: '.$e->getTraceAsString());
            throw $e;
        }
    }
}
