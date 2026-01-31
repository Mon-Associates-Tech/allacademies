<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateProfileRequest;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Arr;

class ProfileController extends Controller
{
    public function show(Authenticatable $user)
    {
        $user->load('preferredAcademicLevel.academicGroup');
        $profileCompletion = $this->calculateProfileCompletion($user);

        return view('profile.show', [
            'user' => $user,
            'profileCompletion' => $profileCompletion,
        ]);
    }

    /**
     * Calculate profile completion percentage and items.
     *
     * @param  \App\Models\User  $user
     * @return array{percentage: int, items: array}
     */
    protected function calculateProfileCompletion($user): array
    {
        $items = [
            [
                'label' => 'Basic info (name)',
                'completed' => ! empty($user->first_name) && ! empty($user->last_name),
            ],
            [
                'label' => 'Email verified',
                'completed' => $user->hasVerifiedEmail(),
            ],
            [
                'label' => 'Profile photo added',
                'completed' => ! empty($user->avatar),
            ],
            [
                'label' => 'Cover image added',
                'completed' => ! empty($user->cover_image),
            ],
            [
                'label' => 'Phone number added',
                'completed' => ! empty($user->phone),
            ],
            [
                'label' => 'Gender specified',
                'completed' => ! empty($user->gender),
            ],
            [
                'label' => 'Location added',
                'completed' => ! empty($user->country) || ! empty($user->city),
            ],
            [
                'label' => 'Academic level set',
                'completed' => ! empty($user->preferred_academic_level_id),
            ],
        ];

        $completedCount = collect($items)->where('completed', true)->count();
        $totalCount = count($items);
        $percentage = $totalCount > 0 ? round(($completedCount / $totalCount) * 100) : 0;

        return [
            'percentage' => $percentage,
            'items' => $items,
        ];
    }

    public function edit(Authenticatable $user)
    {
        return view('profile.edit', [
            'user' => $user,
        ]);
    }

    /** @param \App\Models\User $user */
    public function update(Authenticatable $user, UpdateProfileRequest $request)
    {
        $excludeFields = ['avatar', 'force_update_avatar', 'cover_image', 'force_update_cover_image'];
        $user->update(Arr::except($request->validated(), $excludeFields));

        if ($user->wasChanged('email')) {
            $user->forceFill(['email_verified_at' => null])->save();
            $user->sendEmailVerificationNotification();
        }

        $user->updateAvatar($request->file('avatar'), $request->post('force_update_avatar'));
        $user->updateCoverImage($request->file('cover_image'), $request->post('force_update_cover_image'));

        return redirect()->route('profile.show')->with('success', __('status.profile.updated'));
    }
}
