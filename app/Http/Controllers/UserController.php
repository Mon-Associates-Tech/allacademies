<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    /**
     * Display a listing of the resource(user).
     *
     * @return Application|Factory|\Illuminate\View\View|object|View
     */
    public function index(Request $request)
    {
        $this->authorize('administrate');

        $users = User::query()
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->input('search');
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'LIKE', "%{$search}%")
                      ->orWhere('email', 'LIKE', "%{$search}%");
                });
            })
            ->when($request->filled('role'), function ($query) use ($request) {
                $query->where('role', $request->input('role'));
            })
            ->when($request->boolean('verified'), function ($query) {
                $query->whereNotNull('email_verified_at');
            })
            ->when($request->boolean('unverified'), function ($query) {
                $query->whereNull('email_verified_at');
            })
            ->when($request->boolean('online'), function ($query) {
                $query->where('is_online', true);
            })
            ->when($request->missing('all'), function ($query) {
                // Only show verified users by default unless 'all' parameter is present
                if (!request()->hasAny(['verified', 'unverified'])) {
                    $query->whereNotNull('email_verified_at');
                }
            })
            ->latest('id')
            ->paginate(15)
            ->withQueryString();

        return view('users.index', [
            'users' => $users,
        ]);
    }

    /**
     * Display the specified resource(user).
     *
     * @param User $user
     * @return Factory|View|Application|\Illuminate\View\View|object
     */
    public function show(User $user)
    {
        $this->authorize('administrate');

        // Load relationships and counts
        $user->loadCount([
            'subscriptions',
            'ownedTeams',
            'joinedTeams',
            'worksheets'
        ])->load([
            'primaryRole',
            'currentTeam',
            'student',
            'subscriptions' => function ($query) {
                $query->latest()->limit(5);
            },
            'ownedTeams' => function ($query) {
                $query->latest()->limit(3);
            },
            'joinedTeams' => function ($query) {
                $query->latest()->limit(3);
            }
        ]);

        return view('users.show', [
            'user' => $user,
        ]);
    }

    /**
     * Change the role of a user.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function changeRole(Request $request)
    {
        $this->authorize('own');

        $request->validate([
            'user_id' => 'required|exists:users,id',
            'email' => 'required|email|exists:users,email',
            'role' => 'required|in:subscriber,student,teacher,librarian,author,parent,moderator,admin'
        ]);

        $user = User::findOrFail($request->user_id);

        // Additional validation: make sure the email matches the user
        if ($user->email !== $request->email) {
            throw ValidationException::withMessages([
                'email' => 'The provided email does not match the selected user.'
            ]);
        }

        // Prevent changing owner role
        if (UserRole::OWNER === $user->role) {
            throw ValidationException::withMessages([
                'role' => "You cannot change this user's role."
            ]);
        }

        $oldRole = $user->role;
        $user->role = UserRole::from($request->role);
        $user->save();
        $user->assignRole($request->role);

        // Create student record if role is changed to student
        if ($request->role === 'student' && !$user->student) {
            \App\Models\Student::create([
                'user_id' => $user->id,
                'student_group_id' => null, // You might want to assign to a default group
            ]);
        }

        // Optionally, remove student record if role is changed away from student
        if ($oldRole === 'student' && $request->role !== 'student' && $user->student) {
            $user->student->delete();
        }

        return redirect()->route('users.index')->with('success',
            "Successfully changed {$user->name}'s role from {$oldRole} to {$user->role->value}."
        );
    }
}
