<?php

namespace App\Services;

use App\Models\School;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;

class SchoolContextService
{
    /**
     * Get the current school context
     */
    public static function getCurrentSchool(): ?School
    {
        if (app()->bound('current_school')) {
            return app('current_school');
        }

        $user = Auth::user();
        if (!$user) {
            return null;
        }

        // For cross-school users, check session
        if ($user->canAccessCrossSchool()) {
            $schoolId = session('current_school_id');
            if ($schoolId) {
                $school = School::find($schoolId);
                if ($school) {
                    app()->instance('current_school', $school);
                    return $school;
                }
            }
            return null; // Cross-school users might not have a current school
        }

        // For regular users, return their school
        if ($user->school_id) {
            app()->instance('current_school', $user->school);
            return $user->school;
        }

        return null;
    }

    /**
     * Set the current school context
     */
    public static function setCurrentSchool(?School $school): void
    {
        if ($school) {
            session(['current_school_id' => $school->id]);
            app()->instance('current_school', $school);
        } else {
            session()->forget('current_school_id');
            if (app()->bound('current_school')) {
                app()->forgetInstance('current_school');
            }
        }
    }

    /**
     * Check if user is in "all schools" view
     */
public static function isAllSchoolsView(): bool
{
    $user = Auth::user();

    if (!$user || !$user->canAccessCrossSchool()) {
        return false;
    }

    // Super admins and owners can see all schools by default
    if ($user->isSuperAdmin() || $user->hasRole('owner')) {
        // Unless they've specifically selected a school
        return !session()->has('current_school_id') || session('current_school_id') === null;
    }

    // For other cross-school users, check session
    return !session()->has('current_school_id') || session('current_school_id') === null;
}


    /**
     * Get schools accessible by current user
     */
    public static function getAccessibleSchools(): Collection
    {
        $user = Auth::user();

        if (!$user) {
            return new Collection([]);
        }

        if ($user->canAccessCrossSchool()) {
            return School::active()
                ->withValidSubscription()
                ->orderBy('name')
                ->get();
        }

        // Regular users only see their own school
        if ($user->school_id && $user->school) {
            return new Collection([$user->school]);
        }

        return new Collection([]);
    }

    /**
     * Get context info for current user
     */
    public static function getContextInfo(): array
    {
        $user = Auth::user();
        $currentSchool = self::getCurrentSchool();

        return [
            'user' => $user,
            'current_school' => $currentSchool,
            'can_switch_schools' => $user && $user->canAccessCrossSchool(),
            'is_all_schools_view' => self::isAllSchoolsView(),
            'accessible_schools' => self::getAccessibleSchools(),
            'has_school_context' => $currentSchool !== null,
        ];
    }

    /**
     * Switch to a specific school
     */
    public static function switchToSchool($schoolId): array
    {
        $user = Auth::user();

        if (!$user || !$user->canAccessCrossSchool()) {
            return [
                'success' => false,
                'message' => 'Unauthorized to switch schools',
            ];
        }

        if ($schoolId) {
            $school = School::find($schoolId);

            if (!$school) {
                return [
                    'success' => false,
                    'message' => 'School not found',
                ];
            }

            self::setCurrentSchool($school);

            return [
                'success' => true,
                'message' => "Switched to {$school->name}",
                'school' => $school,
            ];
        } else {
            // Switch to all schools view
            self::setCurrentSchool(null);

            return [
                'success' => true,
                'message' => 'Now viewing all schools',
                'school' => null,
            ];
        }
    }

    /**
     * Get aggregated stats based on current context
     */
    public static function getStats(): array
    {
        $user = Auth::user();

        if (!$user) {
            return [];
        }

        if (self::isAllSchoolsView() && $user->canAccessCrossSchool()) {
            // Global stats across all schools
            return [
                'total_schools' => School::active()->count(),
                'total_users' => User::active()->count(),
                'total_students' => \App\Models\Student::crossSchool()->active()->count(),
                'total_teachers' => \App\Models\Teacher::crossSchool()->active()->count(),
                'total_librarians' => \App\Models\Librarian::crossSchool()->count(),
                'total_parents' => \App\Models\StudentParent::crossSchool()->count(),
            ];
        }

        // School-specific stats
        $currentSchool = self::getCurrentSchool();
        if ($currentSchool) {
            return array_merge($currentSchool->getStats(), [
                'school_name' => $currentSchool->name,
                'school_code' => $currentSchool->code,
            ]);
        }

        return [];
    }

    /**
     * Apply school context to a query builder
     */
    public static function applySchoolContext($query)
    {
        $user = Auth::user();

        if (!$user) {
            return $query->whereRaw('0=1'); // No results for unauthenticated users
        }

        // Cross-school users in all schools view
        if (self::isAllSchoolsView() && $user->canAccessCrossSchool()) {
            return $query->crossSchool(); // No school filtering
        }

        // Apply current school context
        $currentSchool = self::getCurrentSchool();
        if ($currentSchool && $query->getModel()->getTable() !== 'schools') {
            // Only apply school filtering if the model has a school_id column
            if (in_array('school_id', $query->getModel()->getFillable()) ||
                \Schema::hasColumn($query->getModel()->getTable(), 'school_id')) {
                return $query->where('school_id', $currentSchool->id);
            }
        }

        return $query;
    }

    /**
     * Get breadcrumb for current context
     */
    public static function getBreadcrumb(): array
    {
        $breadcrumb = [];
        $currentSchool = self::getCurrentSchool();

        if (self::isAllSchoolsView()) {
            $breadcrumb[] = [
                'label' => 'All Schools',
                'icon' => 'building-office-2',
                'active' => true,
            ];
        } elseif ($currentSchool) {
            $breadcrumb[] = [
                'label' => $currentSchool->name,
                'icon' => 'building-office',
                'active' => true,
                'subtitle' => $currentSchool->code,
            ];
        }

        return $breadcrumb;
    }
}
