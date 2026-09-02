<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Team;

class UserDeletionService
{
    /**
     * Delete a user and all their related data.
     *
     * @param  User|int  $user  The user instance or user ID to delete
     * @return bool True if deletion was successful, false otherwise
     */
    public function deleteUser(User|int $user): bool
    {
        if (is_int($user)) {
            $user = User::find($user);
        }

        if (! $user) {
            return false;
        }

        DB::transaction(function () use ($user) {
            // Delete HasMany relationships
            $this->safeDelete($user, 'subscriptions');
            $this->safeDelete($user, 'tokenSubscriptions');
            $this->safeDelete($user, 'subscriptionCycles');
            $this->safeDelete($user, 'tokenUsageLogs');
            $this->safeDelete($user, 'loginActivities');
            $this->safeDelete($user, 'worksheets');
            $this->safeDelete($user, 'quizSessions');
            $this->safeDelete($user, 'notes');
            $this->safeDelete($user, 'borrowedBooks');
            $this->safeDelete($user, 'bookSubscriptions');
            $this->safeDelete($user, 'preferences');

            // Delete messages and notifications (from Notifiable trait)
            $this->safeDelete($user, 'messages');
            $this->safeDelete($user, 'notifications');

            // Delete assessment responses and progress records if they exist
            $this->safeDelete($user, 'assessmentResponses');
            $this->safeDelete($user, 'progressRecords');

            // Handle uploaded media - nullify the uploaded_by field instead of deleting
            $this->nullifyUploadedMedia($user);

            // Detach many-to-many relationships (pivot tables)
            $this->safeDetach($user, 'sharedNotes');
            $this->safeDetach($user, 'roles');

            // Handle team relationships properly
            $this->detachJoinedTeams($user);

            // Delete teams owned by user
            //$this->safeDelete($user, 'ownedTeams');
          
            $user->current_team_id = null;
            $user->save();
            Team::where('owner_id', $user->id)->delete();

            // Delete role-specific profile records (HasOne relationships)
            $this->deleteRoleProfiles($user);

            // Finally delete the user
            $user->delete();
        });

        return true;
    }

    /**
     * Safely delete a relationship.
     */
    protected function safeDelete(User $user, string $relationName): void
    {
        if (method_exists($user, $relationName)) {
            try {
                $user->$relationName()->delete();
            } catch (\Exception $e) {
                Log::warning("Failed to delete {$relationName} for user {$user->id}: ".$e->getMessage());
            }
        }
    }

    /**
     * Safely detach a many-to-many relationship.
     */
    protected function safeDetach(User $user, string $relationName): void
    {
        if (method_exists($user, $relationName)) {
            try {
                $user->$relationName()->detach();
            } catch (\Exception $e) {
                Log::warning("Failed to detach {$relationName} for user {$user->id}: ".$e->getMessage());
            }
        }
    }

    /**
     * Nullify uploaded media instead of deleting.
     */
    protected function nullifyUploadedMedia(User $user): void
    {
        if (method_exists($user, 'uploadedMedia')) {
            try {
                $user->uploadedMedia()->update(['uploaded_by' => null]);
            } catch (\Exception $e) {
                Log::warning("Failed to nullify uploadedMedia for user {$user->id}: ".$e->getMessage());
            }
        }
    }

    /**
     * Detach user from all joined teams.
     */
    protected function detachJoinedTeams(User $user): void
    {
        if (method_exists($user, 'joinedTeams')) {
            try {
                $user->joinedTeams()->detach();
            } catch (\Exception $e) {
                // Fallback: manually delete from pivot table
                DB::table('team_user')->where('user_id', $user->id)->delete();
            }
        }
    }

    /**
     * Delete role-specific profile records.
     */
    protected function deleteRoleProfiles(User $user): void
    {
        $roleProfiles = ['student', 'teacher', 'author', 'librarian', 'parent'];

        foreach ($roleProfiles as $profile) {
            if ($user->$profile) {
                try {
                    $user->$profile()->delete();
                } catch (\Exception $e) {
                    Log::warning("Failed to delete {$profile} profile for user {$user->id}: ".$e->getMessage());
                }
            }
        }

        // Handle accountant separately (commented out in original code)
        // if ($user->accountant) {
        //     $user->accountant()->delete();
        // }
    }

    /**
     * Get all user relationship items with counts and icons.
     * This method lists all the various relationships of the user for display purposes.
     *
     * @param  User  $user  The user to get relationships for
     * @param  bool  $onlyWithData  If true, only return items with count > 0
     * @return array<int, array{name: string, count: int, icon: string}>
     */
    public function getUserRelationshipItems(User $user, bool $onlyWithData = true): array
    {
        $items = [];

        // Helper function to safely count relationship
        $safeCount = function (string $relationName) use ($user): int {
            if (method_exists($user, $relationName)) {
                try {
                    return $user->$relationName()->count();
                } catch (\Exception $e) {
                    return 0;
                }
            }

            return 0;
        };

        // Subscriptions
        $items[] = [
            'name' => 'Subscriptions',
            'count' => $safeCount('subscriptions'),
            'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
        ];

        // Owned Teams
        $items[] = [
            'name' => 'Owned Teams',
            'count' => $safeCount('ownedTeams'),
            'icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z',
        ];

        // Team Memberships (joined teams)
        $items[] = [
            'name' => 'Team Memberships',
            'count' => $safeCount('joinedTeams'),
            'icon' => 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z',
        ];

        // Worksheets
        $items[] = [
            'name' => 'Worksheets',
            'count' => $safeCount('worksheets'),
            'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
        ];

        // Notes
        $items[] = [
            'name' => 'Notes',
            'count' => $safeCount('notes'),
            'icon' => 'M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z',
        ];

        // Quiz Sessions
        $items[] = [
            'name' => 'Quiz Sessions',
            'count' => $safeCount('quizSessions'),
            'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4',
        ];

        // Login Activities
        $items[] = [
            'name' => 'Login Activities',
            'count' => $safeCount('loginActivities'),
            'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z',
        ];

        // Book Subscriptions
        $items[] = [
            'name' => 'Book Subscriptions',
            'count' => $safeCount('bookSubscriptions'),
            'icon' => 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253',
        ];

        // Borrowed Books
        $items[] = [
            'name' => 'Borrowed Books',
            'count' => $safeCount('borrowedBooks'),
            'icon' => 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253',
        ];

        // Token Subscriptions
        $items[] = [
            'name' => 'Token Subscriptions',
            'count' => $safeCount('tokenSubscriptions'),
            'icon' => 'M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z',
        ];

        // Subscription Cycles
        $items[] = [
            'name' => 'Subscription Cycles',
            'count' => $safeCount('subscriptionCycles'),
            'icon' => 'M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15',
        ];

        // Token Usage Logs
        $items[] = [
            'name' => 'Token Usage Logs',
            'count' => $safeCount('tokenUsageLogs'),
            'icon' => 'M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
        ];

        // User Preferences
        $items[] = [
            'name' => 'Preferences',
            'count' => $safeCount('preferences'),
            'icon' => 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z',
        ];

        // Uploaded Media
        $items[] = [
            'name' => 'Uploaded Media',
            'count' => $safeCount('uploadedMedia'),
            'icon' => 'M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z',
        ];

        // Role-specific profiles - Student
        $items[] = [
            'name' => 'Student Profile',
            'count' => $user->student ? 1 : 0,
            'icon' => 'M12 14l9-5-9-5-9 5 9 5z M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z',
        ];

        // Role-specific profiles - Teacher
        $items[] = [
            'name' => 'Teacher Profile',
            'count' => $user->teacher ? 1 : 0,
            'icon' => 'M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z',
        ];

        // Role-specific profiles - Author
        $items[] = [
            'name' => 'Author Profile',
            'count' => $user->author ? 1 : 0,
            'icon' => 'M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z',
        ];

        // Role-specific profiles - Librarian
        $items[] = [
            'name' => 'Librarian Profile',
            'count' => $user->librarian ? 1 : 0,
            'icon' => 'M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z',
        ];

        // Role-specific profiles - Parent
        $items[] = [
            'name' => 'Parent Profile',
            'count' => $user->parent ? 1 : 0,
            'icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z',
        ];

        // Roles (many-to-many)
        $items[] = [
            'name' => 'Role Assignments',
            'count' => $safeCount('roles'),
            'icon' => 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z',
        ];

        // Filter items with count > 0 if requested
        if ($onlyWithData) {
            $items = array_filter($items, fn ($item) => $item['count'] > 0);
            $items = array_values($items); // Re-index array
        }

        return $items;
    }

    /**
     * Get the total count of all items that will be deleted for a user.
     *
     * @param  User  $user  The user to count items for
     * @return int Total count of all relationship items
     */
    public function getTotalItemsCount(User $user): int
    {
        $items = $this->getUserRelationshipItems($user, false);

        return array_sum(array_column($items, 'count'));
    }
}
