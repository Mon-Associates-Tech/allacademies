<?php

namespace App\Livewire\UserBooks;

use App\Jobs\NotifyUsersAboutBookShareJob;
use App\Models\AcademicGroup;
use App\Models\AcademicLevel;
use App\Models\StudentGroup;
use App\Models\User;
use App\Models\UserBook;
use App\Models\UserBookShare;
use App\Notifications\UserBookSharedNotification;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class ManageShares extends Component
{
    use WithPagination;

    public UserBook $userBook;

    // Share form properties
    public $shareType = 'academic_level';

    public $selectedAcademicGroupId = null;

    public $selectedAcademicLevelId = null;

    public $selectedStudentGroupId = null;

    public $individualEmail = '';

    public $expiresAt = null;

    public $notes = '';

    public $sendNotification = true; // New property for notification checkbox

    // UI state
    public $showShareModal = false;

    public $activeTab = 'shares';

    // Search and Filter for Access List
    public $searchTerm = '';

    public $filterShareType = '';

    // Available options
    public $academicGroups = [];

    public $academicLevels = [];

    public $studentGroups = [];

    protected $rules = [
        'shareType' => 'required|in:individual,academic_group,academic_level,student_group',
        'expiresAt' => 'nullable|date|after:now',
        'notes' => 'nullable|string|max:500',
        'sendNotification' => 'boolean',
    ];

    public function mount(UserBook $userBook)
    {
        $this->userBook = $userBook;
        $this->loadAvailableOptions();
    }

    protected function loadAvailableOptions()
    {
        $user = Auth::user();
        $teacher = $user->teacher;

        if ($teacher) {
            $this->academicGroups = $teacher->academicGroups()->get();
            $this->academicLevels = $teacher->academicLevels()->get();
            $this->studentGroups = $teacher->studentGroups()->get();
        } else {
            $this->academicGroups = AcademicGroup::forCurrentSchool()->get();
            $this->academicLevels = AcademicLevel::forCurrentSchool()->get();
            $this->studentGroups = StudentGroup::where('school_id', $user->school_id)->get();
        }
    }

    public function updatedShareType()
    {
        $this->selectedAcademicGroupId = null;
        $this->selectedAcademicLevelId = null;
        $this->selectedStudentGroupId = null;
        $this->individualEmail = '';
    }

    public function createShare()
    {
        $this->validate();

        // Additional validation based on share type
        $additionalRules = match ($this->shareType) {
            'academic_group' => ['selectedAcademicGroupId' => 'required|exists:academic_groups,id'],
            'academic_level' => ['selectedAcademicLevelId' => 'required|exists:academic_levels,id'],
            'student_group' => ['selectedStudentGroupId' => 'required|exists:student_groups,id'],
            'individual' => ['individualEmail' => 'required|email'],
            default => [],
        };

        $this->validate($additionalRules);

        if ($this->isDuplicateShare()) {
            $this->addError('shareType', 'This target is already shared with.');

            return;
        }

        try {
            $shareData = [
                'user_book_id' => $this->userBook->id,
                'shared_by_user_id' => Auth::id(),
                'share_type' => $this->shareType,
                'status' => $this->shareType === 'individual' ? 'pending' : 'accepted',
                'expires_at' => $this->expiresAt,
                'notes' => $this->notes,
                'shared_to_user_id' => null,
                'shared_to_email' => null,
            ];

            // Add type-specific data
            match ($this->shareType) {
                'academic_group' => $shareData['academic_group_id'] = $this->selectedAcademicGroupId,
                'academic_level' => $shareData['academic_level_id'] = $this->selectedAcademicLevelId,
                'student_group' => $shareData['student_group_id'] = $this->selectedStudentGroupId,
                'individual' => $this->addIndividualShareData($shareData),
            };

            if ($this->selectedAcademicLevelId) {
                $this->selectedAcademicGroupId = AcademicLevel::find($this->selectedAcademicLevelId)?->academic_group_id;
                $shareData['academic_group_id'] = $this->selectedAcademicGroupId;
            }

            $share = UserBookShare::create($shareData);

            // Send notifications if enabled
            if ($this->sendNotification) {
                $this->sendShareNotifications($share);
            }

            $this->dispatch('share-created');
            $this->reset(['shareType', 'selectedAcademicGroupId', 'selectedAcademicLevelId',
                'selectedStudentGroupId', 'individualEmail', 'expiresAt', 'notes']);
            $this->showShareModal = false;

            $affectedCount = $share->getAffectedUsersCount();
            $message = $this->sendNotification
                ? "Book shared successfully with {$affectedCount} user(s)! Notifications are being sent."
                : "Book shared successfully with {$affectedCount} user(s)!";

            session()->flash('message', $message);

        } catch (\Exception $e) {
            \Log::error('Failed to create share', [
                'user_book_id' => $this->userBook->id,
                'error' => $e->getMessage(),
            ]);

            $this->addError('general', 'Failed to share book. Please try again.');
        }
    }

    protected function sendShareNotifications(UserBookShare $share): void
    {
        if ($share->share_type === 'individual') {
            // For individual shares, send immediately
            if ($share->sharedTo) {
                $share->sharedTo->notify(new UserBookSharedNotification($share));
            }
        } else {
            // For group/level shares, dispatch job to handle bulk notifications
            NotifyUsersAboutBookShareJob::dispatch($share);
        }
    }

    protected function addIndividualShareData(array &$shareData): void
    {
        $user = User::where('email', $this->individualEmail)->first();

        if ($user) {
            $shareData['shared_to_user_id'] = $user->id;
        }

        $shareData['shared_to_email'] = $this->individualEmail;
    }

    protected function isDuplicateShare(): bool
    {
        $query = UserBookShare::where('user_book_id', $this->userBook->id)
            ->where('share_type', $this->shareType);

        match ($this->shareType) {
            'academic_group' => $query->where('academic_group_id', $this->selectedAcademicGroupId),
            'academic_level' => $query->where('academic_level_id', $this->selectedAcademicLevelId),
            'student_group' => $query->where('student_group_id', $this->selectedStudentGroupId),
            'individual' => $query->where('shared_to_email', $this->individualEmail),
        };

        return $query->exists();
    }

    public function revokeShare($shareId)
    {
        $share = UserBookShare::where('user_book_id', $this->userBook->id)
            ->findOrFail($shareId);

        if ($share->shared_by_user_id !== Auth::id() && $this->userBook->user_id !== Auth::id()) {
            abort(403);
        }

        $share->delete();

        session()->flash('message', 'Access revoked successfully!');
    }

    public function getSharesProperty()
    {
        return UserBookShare::where('user_book_id', $this->userBook->id)
            ->with(['academicGroup', 'academicLevel', 'studentGroup', 'sharedTo'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);
    }

    public function getAccessListProperty()
    {
        $shares = UserBookShare::where('user_book_id', $this->userBook->id)
            ->active()
            ->get();

        $usersWithAccess = collect();

        foreach ($shares as $share) {
            $affected = $share->getAffectedUsers();
            if ($affected) {
                $usersWithAccess = $usersWithAccess->merge($affected->map(function ($user) use ($share) {
                    return [
                        'user' => $user,
                        'share_type' => $share->share_type,
                        'share_target' => $share->getShareTargetName(),
                        'share_id' => $share->id,
                    ];
                }));
            }
        }

        $uniqueUsers = $usersWithAccess->unique('user.id');

        // Apply search filter
        if ($this->searchTerm) {
            $uniqueUsers = $uniqueUsers->filter(function ($item) {
                $searchLower = strtolower($this->searchTerm);

                return str_contains(strtolower($item['user']->name), $searchLower) ||
                    str_contains(strtolower($item['user']->email), $searchLower);
            });
        }

        // Apply share type filter
        if ($this->filterShareType) {
            $uniqueUsers = $uniqueUsers->filter(function ($item) {
                return $item['share_type'] === $this->filterShareType;
            });
        }

        return $uniqueUsers;
    }

    public function render()
    {
        return view('livewire.user-books.manage-shares', [
            'shares' => $this->shares,
            'accessList' => $this->activeTab === 'access_list' ? $this->accessList : collect(),
        ]);
    }
}
