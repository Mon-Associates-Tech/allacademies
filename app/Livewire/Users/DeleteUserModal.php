<?php

namespace App\Livewire\Users;

use App\Models\User;
use App\Services\UserDeletionService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;

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

    public function loadItemsToDelete(?UserDeletionService $deletionService = null)
    {
        if (! $this->user) {
            $this->itemsToDelete = [];

            return;
        }

        // Use the service to get relationship items (reusable across the application)
        $deletionService = $deletionService ?? app(UserDeletionService::class);
        $this->itemsToDelete = $deletionService->getUserRelationshipItems($this->user);
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

    public function deleteUser(UserDeletionService $deletionService)
    {
        $user = User::find($this->userId);

        if (! $user) {
            $this->showModal = false;

            return;
        }

        // $this->authorize('delete', $user);

        $deletionService->deleteUser($user);

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
