<?php

namespace Database\Seeders;

use App\Models\School;
use App\Models\AcademicYear;
use App\Models\AcademicPeriod;
use App\Models\GradeScale;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SchoolConfigurationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🏫 Starting School Configuration Seeding...');

        DB::beginTransaction();

        try {
            // Create multiple schools with different configurations
            $schools = $this->createSchools();

            foreach ($schools as $school) {
                $this->command->info("📚 Configuring {$school->name}...");

                // Create academic years for each school
                $academicYears = $this->createAcademicYears($school);

                // Create academic periods/terms for each academic year
                foreach ($academicYears as $academicYear) {
                    $this->createAcademicPeriods($school, $academicYear);
                }

                // Create grade scales for each school
                $this->createGradeScales($school);
            }

            DB::commit();

            $this->command->info('✅ School Configuration Seeding completed successfully!');
            $this->command->newLine();
            $this->command->table(
                ['Schools', 'Academic Years', 'Academic Periods', 'Grade Scales'],
                [[
                    School::count(),
                    AcademicYear::count(),
                    AcademicPeriod::count(),
                    GradeScale::count()
                ]]
            );

        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error('❌ Seeding failed: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Create sample schools with comprehensive configuration
     */
    private function createSchools(): array
    {
        $schoolsData = [
            [
                'name' => 'Springfield Academy',
                'code' => 'SA-001',
                'email' => 'info@springfieldacademy.edu',
                'phone' => '+1-555-0101',
                'website' => 'https://springfieldacademy.edu',
                'address' => '123 Education Boulevard',
                'city' => 'Springfield',
                'state' => 'Illinois',
                'country' => 'United States',
                'postal_code' => '62701',
                'type' => 'secondary', // Changed from 'high_school'
                'description' => 'A premier secondary institution committed to academic excellence and character development since 1985.',
                'student_capacity' => 1500,
                'timezone' => 'America/Chicago',
                'currency' => 'USD',
                'settings' => [
                    'school_motto' => 'Excellence in Education',
                    'school_colors' => [
                        'primary' => '#1E3A8A',
                        'secondary' => '#10B981'
                    ],
                    'report_header' => 'Springfield Academy - Where Future Leaders Learn',
                    'report_footer' => 'Committed to Excellence | Accredited by National Education Board',
                    'letterhead' => null,
                ],
            ],
            [
                'name' => 'Riverside International School',
                'code' => 'RIS-002',
                'email' => 'admin@riverside-intl.edu',
                'phone' => '+1-555-0202',
                'website' => 'https://riverside-intl.edu',
                'address' => '456 River Park Drive',
                'city' => 'Portland',
                'state' => 'Oregon',
                'country' => 'United States',
                'postal_code' => '97201',
                'type' => 'mixed', // Valid enum value
                'description' => 'An internationally recognized institution providing world-class education from K-12.',
                'student_capacity' => 2000,
                'timezone' => 'America/Los_Angeles',
                'currency' => 'USD',
                'settings' => [
                    'school_motto' => 'Global Citizens, Local Leaders',
                    'school_colors' => [
                        'primary' => '#7C3AED',
                        'secondary' => '#F59E0B'
                    ],
                    'report_header' => 'Riverside International School - Shaping Tomorrow\'s Leaders',
                    'report_footer' => 'IB World School | Recognized Worldwide',
                    'letterhead' => null,
                ],
            ],
            [
                'name' => 'Greenwood Technical Institute',
                'code' => 'GTI-003',
                'email' => 'contact@greenwood-tech.edu',
                'phone' => '+1-555-0303',
                'website' => 'https://greenwood-tech.edu',
                'address' => '789 Innovation Way',
                'city' => 'Austin',
                'state' => 'Texas',
                'country' => 'United States',
                'postal_code' => '73301',
                'type' => 'tertiary', // Changed from 'technical' to valid enum
                'description' => 'Specializing in technical education and vocational training for the modern workforce.',
                'student_capacity' => 800,
                'timezone' => 'America/Chicago',
                'currency' => 'USD',
                'settings' => [
                    'school_motto' => 'Skills for Tomorrow',
                    'school_colors' => [
                        'primary' => '#059669',
                        'secondary' => '#DC2626'
                    ],
                    'report_header' => 'Greenwood Technical Institute - Building Professional Excellence',
                    'report_footer' => 'Industry Certified Programs | Career Ready',
                    'letterhead' => null,
                ],
            ],
            [
                'name' => 'Maple Grove Primary School',
                'code' => 'MGPS-004',
                'email' => 'office@maplegrove.edu',
                'phone' => '+1-555-0404',
                'website' => 'https://maplegrove.edu',
                'address' => '321 Maple Street',
                'city' => 'Boston',
                'state' => 'Massachusetts',
                'country' => 'United States',
                'postal_code' => '02101',
                'type' => 'primary', // Valid enum value
                'description' => 'Nurturing young minds through innovative early education programs.',
                'student_capacity' => 600,
                'timezone' => 'America/New_York',
                'currency' => 'USD',
                'settings' => [
                    'school_motto' => 'Growing Together, Learning Forever',
                    'school_colors' => [
                        'primary' => '#EC4899',
                        'secondary' => '#8B5CF6'
                    ],
                    'report_header' => 'Maple Grove Primary School - Where Learning Begins',
                    'report_footer' => 'A Foundation for Lifelong Learning',
                    'letterhead' => null,
                ],
            ],
            [
                'name' => 'Summit University',
                'code' => 'SU-005',
                'email' => 'info@summit.edu',
                'phone' => '+1-555-0505',
                'website' => 'https://summit.edu',
                'address' => '1000 University Avenue',
                'city' => 'Denver',
                'state' => 'Colorado',
                'country' => 'United States',
                'postal_code' => '80201',
                'type' => 'tertiary', // Changed from 'university'
                'description' => 'A leading research university offering comprehensive undergraduate and graduate programs.',
                'student_capacity' => 5000,
                'timezone' => 'America/Denver',
                'currency' => 'USD',
                'settings' => [
                    'school_motto' => 'Reaching New Heights in Education',
                    'school_colors' => [
                        'primary' => '#0EA5E9',
                        'secondary' => '#F97316'
                    ],
                    'report_header' => 'Summit University - Excellence in Higher Education',
                    'report_footer' => 'Research | Innovation | Leadership',
                    'letterhead' => null,
                ],
            ],
            [
                'name' => 'Heritage Community School',
                'code' => 'HCS-006',
                'email' => 'info@heritagecommunity.edu',
                'phone' => '+1-555-0606',
                'website' => 'https://heritagecommunity.edu',
                'address' => '500 Community Drive',
                'city' => 'Seattle',
                'state' => 'Washington',
                'country' => 'United States',
                'postal_code' => '98101',
                'type' => 'other', // Using 'other' enum value
                'description' => 'A unique community-based learning institution focused on alternative education methods.',
                'student_capacity' => 400,
                'timezone' => 'America/Los_Angeles',
                'currency' => 'USD',
                'settings' => [
                    'school_motto' => 'Learning Beyond Boundaries',
                    'school_colors' => [
                        'primary' => '#6366F1',
                        'secondary' => '#14B8A6'
                    ],
                    'report_header' => 'Heritage Community School - Innovative Learning',
                    'report_footer' => 'Community-Centered Education',
                    'letterhead' => null,
                ],
            ],
        ];

        $schools = [];
        foreach ($schoolsData as $data) {
            $school = School::firstOrCreate(
                ['code' => $data['code']],
                $data
            );
            $schools[] = $school;
            $this->command->info("  ✓ Created school: {$data['name']} (Type: {$data['type']})");
        }

        return $schools;
    }

    /**
     * Create academic years for a school
     */
    private function createAcademicYears(School $school): array
    {
        $academicYears = [];
        $currentYear = Carbon::now()->year;

        $yearsData = [
            [
                'name' => ($currentYear - 1) . '-' . $currentYear,
                'start_date' => Carbon::create($currentYear - 1, 9, 1),
                'end_date' => Carbon::create($currentYear, 6, 30),
                'status' => 'completed',
                'is_current' => false,
            ],
            [
                'name' => $currentYear . '-' . ($currentYear + 1),
                'start_date' => Carbon::create($currentYear, 9, 1),
                'end_date' => Carbon::create($currentYear + 1, 6, 30),
                'status' => 'active',
                'is_current' => true,
            ],
            [
                'name' => ($currentYear + 1) . '-' . ($currentYear + 2),
                'start_date' => Carbon::create($currentYear + 1, 9, 1),
                'end_date' => Carbon::create($currentYear + 2, 6, 30),
                'status' => 'upcoming',
                'is_current' => false,
            ],
        ];

        foreach ($yearsData as $data) {
            $data['school_id'] = $school->id;
            $academicYear = AcademicYear::firstOrCreate(
                [
                    'school_id' => $school->id,
                    'name' => $data['name']
                ],
                $data
            );
            $academicYears[] = $academicYear;
            $this->command->info("    ✓ Created academic year: {$data['name']}");
        }

        return $academicYears;
    }

    /**
     * Create academic periods/terms for an academic year
     */
    private function createAcademicPeriods(School $school, AcademicYear $academicYear): void
    {
        $startDate = Carbon::parse($academicYear->start_date);
        $endDate = Carbon::parse($academicYear->end_date);

        // Determine period structure based on school type
        $periodStructure = $this->getPeriodStructure($school->type);

        foreach ($periodStructure as $index => $periodConfig) {
            $periodStart = $startDate->copy()->addMonths($periodConfig['start_offset']);
            $periodEnd = $startDate->copy()->addMonths($periodConfig['end_offset']);

            // Calculate status based on dates
            $status = $this->calculatePeriodStatus($periodStart, $periodEnd);

            $periodData = [
                'school_id' => $school->id,
                'academic_year_id' => $academicYear->id,
                'name' => $periodConfig['name'] . ' ' . $academicYear->name,
                'type' => $periodConfig['type'],
                'sequence' => $index + 1,
                'start_date' => $periodStart,
                'end_date' => $periodEnd,
                'status' => $status,
                'description' => $periodConfig['description'],
                'registration_start' => $periodStart->copy()->subWeeks(2),
                'registration_end' => $periodStart->copy()->addWeeks(1),
                'exam_start' => $periodEnd->copy()->subWeeks(2),
                'exam_end' => $periodEnd->copy()->subDays(3),
            ];

            AcademicPeriod::firstOrCreate(
                [
                    'school_id' => $school->id,
                    'academic_year_id' => $academicYear->id,
                    'sequence' => $index + 1
                ],
                $periodData
            );
            $this->command->info("      ✓ Created period: {$periodData['name']}");
        }
    }

    /**
     * Get period structure based on school type
     */
    private function getPeriodStructure(string $schoolType): array
    {
        switch ($schoolType) {
            case 'tertiary': // For universities, colleges, technical institutes
                return [
                    [
                        'name' => 'Fall Semester',
                        'type' => 'semester',
                        'start_offset' => 0,
                        'end_offset' => 4,
                        'description' => 'Fall semester with comprehensive course offerings',
                    ],
                    [
                        'name' => 'Spring Semester',
                        'type' => 'semester',
                        'start_offset' => 5,
                        'end_offset' => 9,
                        'description' => 'Spring semester concluding the academic year',
                    ],
                ];

            case 'secondary': // For high schools and secondary schools
                return [
                    [
                        'name' => 'First Semester',
                        'type' => 'semester',
                        'start_offset' => 0,
                        'end_offset' => 4,
                        'description' => 'First semester of the academic year',
                    ],
                    [
                        'name' => 'Second Semester',
                        'type' => 'semester',
                        'start_offset' => 5,
                        'end_offset' => 9,
                        'description' => 'Second semester of the academic year',
                    ],
                ];

            case 'primary': // For primary/elementary schools
                return [
                    [
                        'name' => 'Term 1',
                        'type' => 'term',
                        'start_offset' => 0,
                        'end_offset' => 3,
                        'description' => 'First term - Building foundations',
                    ],
                    [
                        'name' => 'Term 2',
                        'type' => 'term',
                        'start_offset' => 3,
                        'end_offset' => 6,
                        'description' => 'Second term - Strengthening skills',
                    ],
                    [
                        'name' => 'Term 3',
                        'type' => 'term',
                        'start_offset' => 6,
                        'end_offset' => 9,
                        'description' => 'Third term - Year-end assessment',
                    ],
                ];

            case 'other': // For alternative or special schools
                return [
                    [
                        'name' => 'Quarter 1',
                        'type' => 'quarter',
                        'start_offset' => 0,
                        'end_offset' => 2,
                        'description' => 'First quarter - Introduction and fundamentals',
                    ],
                    [
                        'name' => 'Quarter 2',
                        'type' => 'quarter',
                        'start_offset' => 2,
                        'end_offset' => 4,
                        'description' => 'Second quarter - Intermediate skills',
                    ],
                    [
                        'name' => 'Quarter 3',
                        'type' => 'quarter',
                        'start_offset' => 5,
                        'end_offset' => 7,
                        'description' => 'Third quarter - Advanced applications',
                    ],
                    [
                        'name' => 'Quarter 4',
                        'type' => 'quarter',
                        'start_offset' => 7,
                        'end_offset' => 9,
                        'description' => 'Fourth quarter - Comprehensive review',
                    ],
                ];

            case 'mixed': // For mixed/combined schools (K-12)
            default:
                return [
                    [
                        'name' => 'Trimester 1',
                        'type' => 'trimester',
                        'start_offset' => 0,
                        'end_offset' => 3,
                        'description' => 'First trimester of the academic year',
                    ],
                    [
                        'name' => 'Trimester 2',
                        'type' => 'trimester',
                        'start_offset' => 3,
                        'end_offset' => 6,
                        'description' => 'Second trimester of the academic year',
                    ],
                    [
                        'name' => 'Trimester 3',
                        'type' => 'trimester',
                        'start_offset' => 6,
                        'end_offset' => 9,
                        'description' => 'Third trimester of the academic year',
                    ],
                ];
        }
    }

    /**
     * Calculate period status based on dates
     */
    private function calculatePeriodStatus(Carbon $startDate, Carbon $endDate): string
    {
        $now = Carbon::now();

        if ($now->lt($startDate)) {
            return 'upcoming';
        } elseif ($now->between($startDate, $endDate)) {
            return 'active';
        } else {
            return 'completed';
        }
    }

    /**
     * Create grade scales for a school
     */
    private function createGradeScales(School $school): void
    {
        // Different grading systems based on school type
        $gradeScales = $this->getGradeScaleForSchoolType($school->type);

        foreach ($gradeScales as $gradeData) {
            $gradeData['school_id'] = $school->id;
            GradeScale::firstOrCreate(
                [
                    'school_id' => $school->id,
                    'letter_grade' => $gradeData['letter_grade']
                ],
                $gradeData
            );
        }

        $this->command->info("    ✓ Created " . count($gradeScales) . " grade scales");
    }

    /**
     * Get grade scale configuration based on school type
     */
    private function getGradeScaleForSchoolType(string $schoolType): array
    {
        switch ($schoolType) {
            case 'tertiary': // For universities and colleges
                return [
                    [
                        'name' => 'A Plus',
                        'letter_grade' => 'A+',
                        'min_score' => 97.00,
                        'max_score' => 100.00,
                        'grade_point' => 4.00,
                        'remarks' => 'Outstanding',
                        'is_active' => true,
                    ],
                    [
                        'name' => 'A',
                        'letter_grade' => 'A',
                        'min_score' => 93.00,
                        'max_score' => 96.99,
                        'grade_point' => 4.00,
                        'remarks' => 'Excellent',
                        'is_active' => true,
                    ],
                    [
                        'name' => 'A Minus',
                        'letter_grade' => 'A-',
                        'min_score' => 90.00,
                        'max_score' => 92.99,
                        'grade_point' => 3.70,
                        'remarks' => 'Very Good',
                        'is_active' => true,
                    ],
                    [
                        'name' => 'B Plus',
                        'letter_grade' => 'B+',
                        'min_score' => 87.00,
                        'max_score' => 89.99,
                        'grade_point' => 3.30,
                        'remarks' => 'Good',
                        'is_active' => true,
                    ],
                    [
                        'name' => 'B',
                        'letter_grade' => 'B',
                        'min_score' => 83.00,
                        'max_score' => 86.99,
                        'grade_point' => 3.00,
                        'remarks' => 'Above Average',
                        'is_active' => true,
                    ],
                    [
                        'name' => 'B Minus',
                        'letter_grade' => 'B-',
                        'min_score' => 80.00,
                        'max_score' => 82.99,
                        'grade_point' => 2.70,
                        'remarks' => 'Good',
                        'is_active' => true,
                    ],
                    [
                        'name' => 'C Plus',
                        'letter_grade' => 'C+',
                        'min_score' => 77.00,
                        'max_score' => 79.99,
                        'grade_point' => 2.30,
                        'remarks' => 'Average',
                        'is_active' => true,
                    ],
                    [
                        'name' => 'C',
                        'letter_grade' => 'C',
                        'min_score' => 73.00,
                        'max_score' => 76.99,
                        'grade_point' => 2.00,
                        'remarks' => 'Satisfactory',
                        'is_active' => true,
                    ],
                    [
                        'name' => 'C Minus',
                        'letter_grade' => 'C-',
                        'min_score' => 70.00,
                        'max_score' => 72.99,
                        'grade_point' => 1.70,
                        'remarks' => 'Pass',
                        'is_active' => true,
                    ],
                    [
                        'name' => 'D',
                        'letter_grade' => 'D',
                        'min_score' => 60.00,
                        'max_score' => 69.99,
                        'grade_point' => 1.00,
                        'remarks' => 'Below Average',
                        'is_active' => true,
                    ],
                    [
                        'name' => 'F',
                        'letter_grade' => 'F',
                        'min_score' => 0.00,
                        'max_score' => 59.99,
                        'grade_point' => 0.00,
                        'remarks' => 'Fail',
                        'is_active' => true,
                    ],
                ];

            case 'primary': // For primary/elementary schools
                return [
                    [
                        'name' => 'A - Excellent',
                        'letter_grade' => 'A',
                        'min_score' => 90.00,
                        'max_score' => 100.00,
                        'grade_point' => 4.00,
                        'remarks' => 'Excellent - Exceeds expectations',
                        'is_active' => true,
                    ],
                    [
                        'name' => 'B - Good',
                        'letter_grade' => 'B',
                        'min_score' => 80.00,
                        'max_score' => 89.99,
                        'grade_point' => 3.00,
                        'remarks' => 'Good - Meets all expectations',
                        'is_active' => true,
                    ],
                    [
                        'name' => 'C - Satisfactory',
                        'letter_grade' => 'C',
                        'min_score' => 70.00,
                        'max_score' => 79.99,
                        'grade_point' => 2.00,
                        'remarks' => 'Satisfactory - Meets most expectations',
                        'is_active' => true,
                    ],
                    [
                        'name' => 'D - Needs Improvement',
                        'letter_grade' => 'D',
                        'min_score' => 60.00,
                        'max_score' => 69.99,
                        'grade_point' => 1.00,
                        'remarks' => 'Needs Improvement',
                        'is_active' => true,
                    ],
                    [
                        'name' => 'F - Needs Support',
                        'letter_grade' => 'F',
                        'min_score' => 0.00,
                        'max_score' => 59.99,
                        'grade_point' => 0.00,
                        'remarks' => 'Needs Significant Support',
                        'is_active' => true,
                    ],
                ];

            case 'secondary': // For secondary/high schools
            case 'mixed': // For mixed K-12 schools
            case 'other': // For other types
            default:
                return [
                    [
                        'name' => 'A - Excellent',
                        'letter_grade' => 'A',
                        'min_score' => 90.00,
                        'max_score' => 100.00,
                        'grade_point' => 4.00,
                        'remarks' => 'Excellent',
                        'is_active' => true,
                    ],
                    [
                        'name' => 'B - Good',
                        'letter_grade' => 'B',
                        'min_score' => 80.00,
                        'max_score' => 89.99,
                        'grade_point' => 3.00,
                        'remarks' => 'Good',
                        'is_active' => true,
                    ],
                    [
                        'name' => 'C - Average',
                        'letter_grade' => 'C',
                        'min_score' => 70.00,
                        'max_score' => 79.99,
                        'grade_point' => 2.00,
                        'remarks' => 'Average',
                        'is_active' => true,
                    ],
                    [
                        'name' => 'D - Below Average',
                        'letter_grade' => 'D',
                        'min_score' => 60.00,
                        'max_score' => 69.99,
                        'grade_point' => 1.00,
                        'remarks' => 'Below Average',
                        'is_active' => true,
                    ],
                    [
                        'name' => 'F - Fail',
                        'letter_grade' => 'F',
                        'min_score' => 0.00,
                        'max_score' => 59.99,
                        'grade_point' => 0.00,
                        'remarks' => 'Fail',
                        'is_active' => true,
                    ],
                ];
        }
    }
}
