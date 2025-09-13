<?php

namespace App\Livewire\Users;

use Livewire\Component;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class DeleteUserModal extends Component
{
    use AuthorizesRequests;

    public $userId;
    public $user;
    public $showModal = false;
    public $itemsToDelete = [];

    protected $listeners = ['openDeleteModal'];

    public function openDeleteModal($userId = null)
    {
        // Handle case where $userId might be passed as an array or object
        if (is_array($userId) && isset($userId[0])) {
            $userId = $userId[0];
        } elseif (is_object($userId) && property_exists($userId, 'id')) {
            $userId = $userId->id;
        }

        // Ensure $userId is an integer
        $userId = (int) $userId;

        if ($userId <= 0) {
            return;
        }

        $this->userId = $userId;
        $this->user = User::find($userId);

        if ($this->user) {
            $this->loadItemsToDelete();
            $this->showModal = true;
        }
    }

    public function loadItemsToDelete()
    {
        if (!$this->user) {
            $this->itemsToDelete = [];
            return;
        }

        // Manually create array without using collections
        $items = [];

        // Subscriptions
        $subscriptionCount = 0;
        if (method_exists($this->user, 'subscriptions')) {
            try {
                $subscriptionCount = $this->user->subscriptions()->count();
            } catch (\Exception $e) {
                $subscriptionCount = 0;
            }
        }
        $items[] = [
            'name' => 'Subscriptions',
            'count' => $subscriptionCount,
            'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
        ];

        // Owned Teams
        $ownedTeamsCount = 0;
        if (method_exists($this->user, 'ownedTeams')) {
            try {
                $ownedTeamsCount = $this->user->ownedTeams()->count();
            } catch (\Exception $e) {
                $ownedTeamsCount = 0;
            }
        }
        $items[] = [
            'name' => 'Owned Teams',
            'count' => $ownedTeamsCount,
            'icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z',
        ];

        // Team Memberships (joined teams)
        $teamMembershipsCount = 0;
        if (method_exists($this->user, 'joinedTeams')) {
            try {
                $teamMembershipsCount = $this->user->joinedTeams()->count();
            } catch (\Exception $e) {
                $teamMembershipsCount = 0;
            }
        }
        $items[] = [
            'name' => 'Team Memberships',
            'count' => $teamMembershipsCount,
            'icon' => 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z',
        ];

        // Worksheets
        $worksheetsCount = 0;
        if (method_exists($this->user, 'worksheets')) {
            try {
                $worksheetsCount = $this->user->worksheets()->count();
            } catch (\Exception $e) {
                $worksheetsCount = 0;
            }
        }
        $items[] = [
            'name' => 'Worksheets',
            'count' => $worksheetsCount,
            'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
        ];

        // Filter items with count > 0
        $filteredItems = [];
        foreach ($items as $item) {
            if (is_array($item) && isset($item['count']) && $item['count'] > 0) {
                $filteredItems[] = $item;
            }
        }

        $this->itemsToDelete = $filteredItems;
    }

    public function getTotalItemsCount()
    {
        $count = 0;
        if (is_array($this->itemsToDelete)) {
            foreach ($this->itemsToDelete as $item) {
                if (is_array($item) && isset($item['count'])) {
                    $count += $item['count'];
                }
            }
        }
        return $count;
    }

    public function deleteUser()
    {
        $user = User::find($this->userId);

        if (!$user) {
            $this->showModal = false;
            return;
        }

        // $this->authorize('delete', $user);

        // Delete all user-related data
        DB::transaction(function () use ($user) {
            // Delete related records first
            if (method_exists($user, 'subscriptions')) {
                $user->subscriptions()->delete();
            }

            if (method_exists($user, 'messages')) {
                $user->messages()->delete();
            }

            if (method_exists($user, 'notifications')) {
                $user->notifications()->delete();
            }

            if (method_exists($user, 'worksheets')) {
                $user->worksheets()->delete();
            }

            if (method_exists($user, 'assessmentResponses')) {
                $user->assessmentResponses()->delete();
            }

            if (method_exists($user, 'progressRecords')) {
                $user->progressRecords()->delete();
            }

            // Handle team relationships properly
            // Remove user from all teams they've joined
            if (method_exists($user, 'joinedTeams')) {
                try {
                    $user->joinedTeams()->detach();
                } catch (\Exception $e) {
                    // Fallback: manually delete from pivot table
                    \DB::table('team_user')->where('user_id', $user->id)->delete();
                }
            }

            // Delete teams owned by user
            if (method_exists($user, 'ownedTeams')) {
                $user->ownedTeams()->delete();
            }

            // Finally delete the user
            $user->delete();
        });

        $this->showModal = false;

        // Redirect to users index with success message
        session()->flash('message', 'User successfully deleted.');
        return redirect()->route('users.index');
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->userId = null;
        $this->user = null;
        $this->itemsToDelete = [];
    }

    public function render()
    {
        return view('livewire.users.delete-user-modal');
    }
}
