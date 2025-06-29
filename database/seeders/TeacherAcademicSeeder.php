<?php

namespace Database\Seeders;

use App\Models\Teacher;
use App\Models\AcademicLevel;
use App\Models\AcademicGroup;
use Illuminate\Database\Seeder;

class TeacherAcademicSeeder extends Seeder
{
    public function run(): void
    {
        // Example: Assign teachers to academic levels and groups
        $teachers = Teacher::all();
        $academicLevels = AcademicLevel::all();
        $academicGroups = AcademicGroup::all();

        foreach ($teachers as $teacher) {
            // Assign to some academic levels
            $levels = $academicLevels->random(rand(1, 3));
            foreach ($levels as $index => $level) {
                $teacher->academicLevels()->attach($level->id, [
                    'is_primary' => $index === 0, // First one is primary
                    'notes' => 'Assigned during seeding',
                ]);
            }

            // Assign to some academic groups
            $groups = $academicGroups->random(rand(1, 2));
            foreach ($groups as $index => $group) {
                $teacher->academicGroups()->attach($group->id, [
                    'is_primary' => $index === 0, // First one is primary
                    'notes' => 'Assigned during seeding',
                ]);
            }
        }
    }
}
