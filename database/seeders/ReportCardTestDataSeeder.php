<?php

namespace Database\Seeders;

use App\Models\AcademicLevel;
use App\Models\AcademicPeriod;
use App\Models\AcademicSubject;
use App\Models\AcademicYear;
use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\GradeScale;
use App\Models\ReportCardConfiguration;
use App\Models\ReportCardTemplate;
use App\Models\School;
use App\Models\ScoreWeighting;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class ReportCardTestDataSeeder extends Seeder
{
    public function run()
    {
        // 1. Create a School
        $school = School::first() ?? School::create([
            'name' => 'Test Academy',
            'slug' => 'test-academy',
            'email' => 'admin@testacademy.com',
            'status' => 'active',
        ]);

        // 2. Create Admin User
        $adminUser = User::where('email', 'admin@testacademy.com')->first() ?? User::create([
            'first_name' => 'Admin',
            'last_name' => 'User',
            'email' => 'admin@testacademy.com',
            'password' => Hash::make('password'),
            'school_id' => $school->id,
            'email_verified_at' => now(),
        ]);
        $adminUser->assignRole('admin');

        // 3. Create Teacher
        $teacherUser = User::where('email', 'teacher@testacademy.com')->first() ?? User::create([
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'teacher@testacademy.com',
            'password' => Hash::make('password'),
            'school_id' => $school->id,
            'email_verified_at' => now(),
        ]);
        $teacherUser->assignRole('teacher');
        $teacher = Teacher::firstOrCreate(
            ['user_id' => $teacherUser->id],
            ['school_id' => $school->id, 'employee_id' => 'T101', 'status' => 'active']
        );

        // 4. Create Academic Year and Period
        $academicYear = AcademicYear::firstOrCreate(
            ['school_id' => $school->id, 'name' => '2025/2026'],
            ['start_date' => '2025-09-01', 'end_date' => '2026-06-30', 'status' => 'active']
        );

        $period = AcademicPeriod::firstOrCreate(
            ['school_id' => $school->id, 'name' => 'Term 1'],
            [
                'academic_year_id' => $academicYear->id,
                'start_date' => '2025-09-01',
                'end_date' => '2025-12-20',
                'status' => 'active',
                'is_current' => true,
            ]
        );

        // 5. Create Academic Level and Subject
        $level = AcademicLevel::firstOrCreate(
            ['name' => 'Grade 10', 'school_id' => $school->id],
            ['label' => 'G10']
        );

        // Connect school and level if using pivot
        if (\Schema::hasTable('school_academic_level')) {
            \DB::table('school_academic_level')->updateOrInsert(
                ['school_id' => $school->id, 'academic_level_id' => $level->id],
                ['is_active' => true]
            );
        }

        $subject = AcademicSubject::firstOrCreate(
            ['name' => 'Mathematics', 'academic_level_id' => $level->id],
            ['code' => 'MATH101']
        );

        // Assign teacher to level and subject
        if (\Schema::hasTable('academic_level_teacher')) {
            \DB::table('academic_level_teacher')->updateOrInsert(
                ['academic_level_id' => $level->id, 'teacher_id' => $teacher->id],
                ['is_primary' => true]
            );
        }
        if (\Schema::hasTable('subject_teacher')) {
            \DB::table('subject_teacher')->updateOrInsert(
                ['subject_id' => $subject->id, 'teacher_id' => $teacher->id],
                ['is_primary' => true]
            );
        }

        // 6. Create Students
        for ($i = 1; $i <= 3; $i++) {
            $studentUser = User::create([
                'first_name' => 'Student',
                'last_name' => "$i",
                'email' => "student$i@testacademy.com",
                'password' => Hash::make('password'),
                'school_id' => $school->id,
                'email_verified_at' => now(),
            ]);
            $studentUser->assignRole('student');
            Student::create([
                'user_id' => $studentUser->id,
                'school_id' => $school->id,
                'academic_level_id' => $level->id,
                'student_id' => "STU00$i",
                'status' => 'active',
            ]);
        }

        // 7. Create Grade Scales
        GradeScale::create(['school_id' => $school->id, 'academic_level_id' => $level->id, 'name' => 'A', 'min_score' => 80, 'max_score' => 100, 'letter_grade' => 'A', 'remarks' => 'Excellent']);
        GradeScale::create(['school_id' => $school->id, 'academic_level_id' => $level->id, 'name' => 'B', 'min_score' => 70, 'max_score' => 79.99, 'letter_grade' => 'B', 'remarks' => 'Very Good']);
        GradeScale::create(['school_id' => $school->id, 'academic_level_id' => $level->id, 'name' => 'C', 'min_score' => 50, 'max_score' => 69.99, 'letter_grade' => 'C', 'remarks' => 'Good']);
        GradeScale::create(['school_id' => $school->id, 'academic_level_id' => $level->id, 'name' => 'F', 'min_score' => 0, 'max_score' => 49.99, 'letter_grade' => 'F', 'remarks' => 'Fail']);

        // 8. Create Score Weightings
        ScoreWeighting::create(['school_id' => $school->id, 'academic_level_id' => $level->id, 'name' => 'Quiz', 'weight_percentage' => 40, 'sort_order' => 1]);
        ScoreWeighting::create(['school_id' => $school->id, 'academic_level_id' => $level->id, 'name' => 'Examination', 'weight_percentage' => 60, 'sort_order' => 2]);

        // 9. Create Template
        $template = ReportCardTemplate::create([
            'school_id' => $school->id,
            'academic_level_id' => $level->id,
            'name' => 'Standard Template',
            'is_default' => true,
        ]);

        // 10. Create Report Card Configuration
        ReportCardConfiguration::create([
            'school_id' => $school->id,
            'academic_period_id' => $period->id,
            'academic_level_id' => $level->id,
            'report_card_template_id' => $template->id,
            'requires_approval' => true,
            'is_published' => false,
            'preparation_mode' => 'hybrid',
        ]);

        // 11. Create Assignments and Submissions
        $students = Student::where('academic_level_id', $level->id)->get();

        $quiz = Assignment::create([
            'title' => 'Math Quiz 1',
            'type' => 'quiz',
            'academic_subject_id' => $subject->id,
            'user_id' => $teacherUser->id,
            'status' => 'published',
            'total_marks' => 50,
            'starts_at' => '2025-10-01 08:00:00',
            'ends_at' => '2025-10-01 09:00:00',
        ]);

        $exam = Assignment::create([
            'title' => 'Math Final Exam',
            'type' => 'examination',
            'academic_subject_id' => $subject->id,
            'user_id' => $teacherUser->id,
            'status' => 'published',
            'total_marks' => 100,
            'starts_at' => '2025-12-15 08:00:00',
            'ends_at' => '2025-12-15 10:00:00',
        ]);

        foreach ($students as $student) {
            // Quiz submission
            AssignmentSubmission::create([
                'assignment_id' => $quiz->id,
                'student_id' => $student->id,
                'submitted_at' => '2025-10-01 08:45:00',
                'score' => rand(30, 50),
                'total_marks' => 50,
                'status' => 'graded',
            ]);

            // Exam submission
            AssignmentSubmission::create([
                'assignment_id' => $exam->id,
                'student_id' => $student->id,
                'submitted_at' => '2025-12-15 09:50:00',
                'score' => rand(60, 100),
                'total_marks' => 100,
                'status' => 'graded',
            ]);
        }
    }
}
