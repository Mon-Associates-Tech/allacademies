<?php

namespace Database\Seeders;

use App\Models\AcademicPeriod;
use App\Models\School;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class AcademicPeriodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $schools = School::all();

        foreach ($schools as $school) {
            $this->createPeriodsForSchool($school);
        }
    }

    private function createPeriodsForSchool(School $school): void
    {
        // Different schools can have different academic structures
        switch ($school->type) {
            case 'secondary':
            case 'primary':
                $this->createTermSystem($school);
                break;
            case 'mixed':
                // Mixed schools might use either system - let's use semester for this example
                $this->createSemesterSystem($school);
                break;
            default:
                $this->createSemesterSystem($school);
                break;
        }
    }

    private function createTermSystem(School $school): void
    {
        $currentYear = Carbon::now()->year;
        $academicYear = $currentYear.'/'.($currentYear + 1);

        // Three term system (common in Ghana primary/secondary)
        $terms = [
            [
                'name' => 'First Term',
                'type' => 'term',
                'sequence' => 1,
                'year_sequence' => 1,
                'start_date' => Carbon::create($currentYear, 9, 15), // Mid September
                'end_date' => Carbon::create($currentYear, 12, 15), // Mid December
                'status' => 'active',
                //                'is_current' => true,
                'registration_start' => Carbon::create($currentYear, 8, 1),
                'registration_end' => Carbon::create($currentYear, 9, 10),
                'exam_start' => Carbon::create($currentYear, 12, 5),
                'exam_end' => Carbon::create($currentYear, 12, 15),
            ],
            [
                'name' => 'Second Term',
                'type' => 'term',
                'sequence' => 2,
                'year_sequence' => 2,
                'start_date' => Carbon::create($currentYear + 1, 1, 10), // January
                'end_date' => Carbon::create($currentYear + 1, 4, 5), // Early April
                'status' => 'upcoming',
                //                'is_current' => false,
                'registration_start' => Carbon::create($currentYear, 12, 20),
                'registration_end' => Carbon::create($currentYear + 1, 1, 5),
                'exam_start' => Carbon::create($currentYear + 1, 3, 25),
                'exam_end' => Carbon::create($currentYear + 1, 4, 5),
            ],
            [
                'name' => 'Third Term',
                'type' => 'term',
                'sequence' => 3,
                'year_sequence' => 3,
                'start_date' => Carbon::create($currentYear + 1, 4, 25), // Late April
                'end_date' => Carbon::create($currentYear + 1, 7, 20), // Mid July
                'status' => 'upcoming',
                //                'is_current' => false,
                'registration_start' => Carbon::create($currentYear + 1, 4, 10),
                'registration_end' => Carbon::create($currentYear + 1, 4, 20),
                'exam_start' => Carbon::create($currentYear + 1, 7, 1),
                'exam_end' => Carbon::create($currentYear + 1, 7, 15),
            ],
        ];

        foreach ($terms as $termData) {
            $termData['school_id'] = $school->id;
            $termData['academic_year'] = $academicYear;
            $termData['description'] = "Academic term for {$school->name}";

            AcademicPeriod::create($termData);
        }
    }

    private function createSemesterSystem(School $school): void
    {
        $currentYear = Carbon::now()->year;
        $academicYear = $currentYear.'/'.($currentYear + 1);

        // Two semester system (common in tertiary institutions)
        $semesters = [
            [
                'name' => 'First Semester',
                'type' => 'semester',
                'sequence' => 1,
                'year_sequence' => 1,
                'start_date' => Carbon::create($currentYear, 9, 1), // September
                'end_date' => Carbon::create($currentYear + 1, 1, 15), // January
                'status' => 'active',
                //                'is_current' => true,
                'registration_start' => Carbon::create($currentYear, 7, 1),
                'registration_end' => Carbon::create($currentYear, 8, 25),
                'exam_start' => Carbon::create($currentYear, 12, 15),
                'exam_end' => Carbon::create($currentYear + 1, 1, 10),
            ],
            [
                'name' => 'Second Semester',
                'type' => 'semester',
                'sequence' => 2,
                'year_sequence' => 2,
                'start_date' => Carbon::create($currentYear + 1, 2, 1), // February
                'end_date' => Carbon::create($currentYear + 1, 6, 30), // June
                'status' => 'upcoming',
                //                'is_current' => false,
                'registration_start' => Carbon::create($currentYear + 1, 1, 20),
                'registration_end' => Carbon::create($currentYear + 1, 1, 28),
                'exam_start' => Carbon::create($currentYear + 1, 6, 1),
                'exam_end' => Carbon::create($currentYear + 1, 6, 25),
            ],
        ];

        // Some tertiary institutions also have summer sessions
        if ($school->ownership === 'private') {
            $semesters[] = [
                'name' => 'Summer Session',
                'type' => 'session',
                'sequence' => 1,
                'year_sequence' => 3,
                'start_date' => Carbon::create($currentYear + 1, 7, 5),
                'end_date' => Carbon::create($currentYear + 1, 8, 25),
                'status' => 'upcoming',
                //                'is_current' => false,
                'registration_start' => Carbon::create($currentYear + 1, 6, 15),
                'registration_end' => Carbon::create($currentYear + 1, 7, 1),
                'exam_start' => Carbon::create($currentYear + 1, 8, 15),
                'exam_end' => Carbon::create($currentYear + 1, 8, 25),
            ];
        }

        foreach ($semesters as $semesterData) {
            $semesterData['school_id'] = $school->id;
            $semesterData['academic_year'] = $academicYear;
            $semesterData['description'] = "Academic period for {$school->name}";

            AcademicPeriod::create($semesterData);
        }
    }

    private function createQuarterSystem(School $school): void
    {
        // Alternative system for some international schools
        $currentYear = Carbon::now()->year;
        $academicYear = $currentYear.'/'.($currentYear + 1);

        $quarters = [
            [
                'name' => 'Fall Quarter',
                'type' => 'quarter',
                'sequence' => 1,
                'year_sequence' => 1,
                'start_date' => Carbon::create($currentYear, 9, 1),
                'end_date' => Carbon::create($currentYear, 11, 30),
                'status' => 'active',
                //                'is_current' => true,
            ],
            [
                'name' => 'Winter Quarter',
                'type' => 'quarter',
                'sequence' => 2,
                'year_sequence' => 2,
                'start_date' => Carbon::create($currentYear + 1, 1, 5),
                'end_date' => Carbon::create($currentYear + 1, 3, 15),
                'status' => 'upcoming',
                //                'is_current' => false,
            ],
            [
                'name' => 'Spring Quarter',
                'type' => 'quarter',
                'sequence' => 3,
                'year_sequence' => 3,
                'start_date' => Carbon::create($currentYear + 1, 3, 25),
                'end_date' => Carbon::create($currentYear + 1, 6, 15),
                'status' => 'upcoming',
                //                'is_current' => false,
            ],
            [
                'name' => 'Summer Quarter',
                'type' => 'quarter',
                'sequence' => 4,
                'year_sequence' => 4,
                'start_date' => Carbon::create($currentYear + 1, 6, 25),
                'end_date' => Carbon::create($currentYear + 1, 8, 30),
                'status' => 'upcoming',
                //                'is_current' => false,
            ],
        ];

        foreach ($quarters as $quarterData) {
            $quarterData['school_id'] = $school->id;
            $quarterData['academic_year'] = $academicYear;
            $quarterData['description'] = "Academic quarter for {$school->name}";

            AcademicPeriod::create($quarterData);
        }
    }
}
