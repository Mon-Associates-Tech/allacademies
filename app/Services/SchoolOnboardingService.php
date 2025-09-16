<?php

namespace App\Services;

use App\Models\School;
use App\Models\User;
use App\Models\AcademicGroup;
use App\Models\AcademicLevel;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class SchoolOnboardingService
{
    public function createSchool(array $data, User $admin, array $academicGroups = [], array $academicLevels = []): School
    {
        return DB::transaction(function() use ($data, $admin, $academicGroups, $academicLevels) {
            // Create school
            $school = School::create([
                ...$data,
                'status' => 'inactive'
            ]);

            // Assign user to school and make them admin
            $admin->update(['school_id' => $school->id]);

            // Ensure user has admin role
            if (!$admin->hasRole('admin')) {
                $admin->assignRole('admin');
            }

            // Attach selected academic groups
            if (!empty($academicGroups)) {
                $school->academicGroups()->sync($academicGroups);
            }

            // Attach selected academic levels - CORRECTED CODE
            if (!empty($academicLevels)) {
                // Get the academic levels with their group information
                $levels = AcademicLevel::whereIn('id', $academicLevels)
                    ->get(['id', 'academic_group_id']);

                // Prepare the sync data with group IDs
                $syncData = [];
                foreach ($levels as $level) {
                    $syncData[$level->id] = ['academic_group_id' => $level->academic_group_id];
                }

                // Sync academic levels with their group associations
                $school->academicLevels()->sync($syncData);
            }

            return $school;
        });
    }

    public function completeOnboarding(School $school, User $admin): void
    {
        DB::transaction(function() use ($school, $admin) {
            // Activate the school
            $school->update(['status' => 'active']);

            // Set up default settings if not provided
            $this->setupDefaultSettings($school);

            // Create default academic structure if none selected
            if ($school->academicGroups()->count() === 0) {
                $this->attachDefaultAcademicStructure($school);
            }
        });
    }

    private function setupDefaultSettings(School $school): void
    {
        $defaultSettings = [
            'timezone' => 'Africa/Accra',
            'currency' => 'GHS',
            'academic_year_start' => now()->startOfYear()->format('Y-m-d'),
            'academic_year_end' => now()->endOfYear()->format('Y-m-d'),
            'features' => [
                'library_management' => true,
                'academic_management' => true,
                'user_management' => true,
            ]
        ];

        $school->update([
            'settings' => array_merge($school->settings ?? [], $defaultSettings)
        ]);
    }

    private function attachDefaultAcademicStructure(School $school): void
    {
        // Get or create default academic groups based on school type
        $defaultGroups = $this->getDefaultAcademicGroups($school->type);

        foreach ($defaultGroups as $groupData) {
            $group = AcademicGroup::firstOrCreate(
                ['name' => $groupData['name']],
                $groupData
            );

            // Attach to school if not already attached
            if (!$school->academicGroups()->where('academic_group_id', $group->id)->exists()) {
                $school->academicGroups()->attach($group->id);
            }
        }
    }

    private function getDefaultAcademicGroups(string $schoolType): array
    {
        return match ($schoolType) {
            'primary' => [
                ['name' => 'Nursery', 'description' => 'Nursery classes'],
                ['name' => 'Primary', 'description' => 'Primary 1-6'],
            ],
            'secondary' => [
                ['name' => 'Junior High', 'description' => 'JHS 1-3'],
                ['name' => 'Senior High', 'description' => 'SHS 1-3'],
            ],
            'tertiary' => [
                ['name' => 'Undergraduate', 'description' => 'Bachelor\'s programs'],
                ['name' => 'Postgraduate', 'description' => 'Master\'s and PhD programs'],
            ],
            'mixed' => [
                ['name' => 'Nursery', 'description' => 'Nursery classes'],
                ['name' => 'Primary', 'description' => 'Primary 1-6'],
                ['name' => 'Junior High', 'description' => 'JHS 1-3'],
                ['name' => 'Senior High', 'description' => 'SHS 1-3'],
            ],
            default => []
        };
    }
}
