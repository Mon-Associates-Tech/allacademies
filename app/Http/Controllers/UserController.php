<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class UserController extends Controller
{
    /**
     * Display a listing of the resource(user).
     *
     * @return Response
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
     * @return Response
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
            'role',
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
}
