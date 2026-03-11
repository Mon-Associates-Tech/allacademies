<?php

namespace App\Livewire\Users;

use App\Models\Author;
use App\Models\BookBorrowing;
use App\Models\BookSubscription;
use App\Models\Chat\SubscriptionCycle;
use App\Models\Chat\UserTokenSubscription;
use App\Models\Librarian;
use App\Models\Note;
use App\Models\QuizSession;
use App\Models\Student;
use App\Models\StudentParent;
use App\Models\Subscription;
use App\Models\Teacher;
use App\Models\Team;
use App\Models\User;
use App\Models\UserPreference;
use App\Models\Worksheet;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\On;
use Livewire\Component;

class UserRelationshipManager extends Component
{
    public int $userId;

    public function mount(int $userId): void
    {
        $this->userId = $userId;
    }

    #[On('deleteRelationship')]
    public function deleteRelationship(int $userId, string $relation, int $itemId): void
    {
        // Verify the user exists
        $user = User::find($userId);
        if (! $user) {
            session()->flash('error', 'User not found.');

            return;
        }

        try {
            $deleted = $this->performDeletion($user, $relation, $itemId);

            if ($deleted) {
                session()->flash('message', 'Item deleted successfully.');
                $this->dispatch('relationship-deleted');
            } else {
                session()->flash('error', 'Failed to delete item. Item may not exist or cannot be deleted.');
            }
        } catch (\Exception $e) {
            Log::error('Failed to delete relationship item', [
                'user_id' => $userId,
                'relation' => $relation,
                'item_id' => $itemId,
                'error' => $e->getMessage(),
            ]);
            session()->flash('error', 'An error occurred while deleting the item.');
        }
    }

    protected function performDeletion(User $user, string $relation, int $itemId): bool
    {
        return match ($relation) {
            // Role-specific profiles (HasOne)
            'student' => $this->deleteProfile(Student::class, $user->id, $itemId),
            'teacher' => $this->deleteProfile(Teacher::class, $user->id, $itemId),
            'author' => $this->deleteProfile(Author::class, $user->id, $itemId),
            'librarian' => $this->deleteProfile(Librarian::class, $user->id, $itemId),
            'parent' => $this->deleteProfile(StudentParent::class, $user->id, $itemId),

            // Content relationships (HasMany)
            'notes' => $this->deleteHasMany(Note::class, $user->id, $itemId),
            'worksheets' => $this->deleteHasMany(Worksheet::class, $user->id, $itemId),
            'quizSessions' => $this->deleteHasMany(QuizSession::class, $user->id, $itemId),

            // Library relationships (HasMany)
            'borrowedBooks' => $this->deleteHasMany(BookBorrowing::class, $user->id, $itemId),
            'bookSubscriptions' => $this->deleteHasMany(BookSubscription::class, $user->id, $itemId),

            // Subscription relationships (HasMany)
            'subscriptions' => $this->deleteHasMany(Subscription::class, $user->id, $itemId, 'subscriber_id'),
            'tokenSubscriptions' => $this->deleteHasMany(UserTokenSubscription::class, $user->id, $itemId),
            'subscriptionCycles' => $this->deleteHasMany(SubscriptionCycle::class, $user->id, $itemId),

            // Team relationships
            'ownedTeams' => $this->deleteOwnedTeam($user->id, $itemId),
            'joinedTeams' => $this->detachFromTeam($user->id, $itemId),

            // Other relationships
            'preferences' => $this->deleteHasMany(UserPreference::class, $user->id, $itemId),
            'roles' => $this->detachRole($user, $itemId),

            default => false,
        };
    }

    protected function deleteProfile(string $modelClass, int $userId, int $itemId): bool
    {
        $item = $modelClass::where('user_id', $userId)->where('id', $itemId)->first();

        if ($item) {
            return $item->delete();
        }

        return false;
    }

    protected function deleteHasMany(string $modelClass, int $userId, int $itemId, string $userColumn = 'user_id'): bool
    {
        $item = $modelClass::where($userColumn, $userId)->where('id', $itemId)->first();

        if ($item) {
            return $item->delete();
        }

        return false;
    }

    protected function deleteOwnedTeam(int $userId, int $teamId): bool
    {
        $team = Team::where('owner_id', $userId)->where('id', $teamId)->first();

        if ($team) {
            // Remove all members first
            DB::table('team_user')->where('team_id', $teamId)->delete();

            return $team->delete();
        }

        return false;
    }

    protected function detachFromTeam(int $userId, int $teamId): bool
    {
        $deleted = DB::table('team_user')
            ->where('user_id', $userId)
            ->where('team_id', $teamId)
            ->delete();

        return $deleted > 0;
    }

    protected function detachRole(User $user, int $roleId): bool
    {
        if ($user->roles()->where('role_id', $roleId)->exists()) {
            $user->roles()->detach($roleId);

            return true;
        }

        return false;
    }

    public function render()
    {
        return view('livewire.users.user-relationship-manager');
    }
}
